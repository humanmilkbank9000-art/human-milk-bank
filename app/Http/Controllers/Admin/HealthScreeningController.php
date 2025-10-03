<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\HealthScreening;

class HealthScreeningController extends Controller
{
    public function index(Request $request)
    {
        // Check if admin is logged in
        if (!session('admin_id')) {
            return redirect('/admin/pin');
        }

        // Read view/filter params
        $view = $request->query('view', 'pending'); // pending | accepted | declined | archived
        if (!in_array($view, ['pending','accepted','declined','archived'], true)) {
            $view = 'pending';
        }
        $q = trim((string) $request->query('q', ''));
    $year = $request->query('year');
    $month = $request->query('month');
    $archivedStatus = $request->query('archived_status', 'all'); // 'all' | 'accepted' | 'declined' (for archived view)

        // Base query with user info
        $query = \DB::table('health_screenings')
            ->join('users', 'health_screenings.User_ID', '=', 'users.User_ID')
            ->select(
                'health_screenings.Health_Screening_ID',
                'health_screenings.status',
                'health_screenings.created_at',
                'health_screenings.updated_at',
                'health_screenings.archived_at',
                'users.Full_Name',
                'users.Contact_Number'
            );

        // Apply view filters
        if ($view === 'archived') {
            $query->whereNotNull('health_screenings.archived_at');
            // Optional filter archived by status (accepted/declined)
            if (in_array($archivedStatus, ['accepted','declined'], true)) {
                $query->where('health_screenings.status', $archivedStatus);
            }
            // Optional year/month filters on updated_at (date accepted/declined)
            if ($year && $year !== 'all') {
                $query->whereYear('health_screenings.updated_at', (int) $year);
            }
            if ($month && $month !== 'all') {
                $query->whereMonth('health_screenings.updated_at', (int) $month);
            }
        } else {
            $query->whereNull('health_screenings.archived_at');
            if (in_array($view, ['pending', 'accepted', 'declined'], true)) {
                $query->where('health_screenings.status', $view);
            }
        }

        // Search by name or contact
        if ($q !== '') {
            $query->where(function($sub) use ($q) {
                $sub->where('users.Full_Name', 'like', "%{$q}%")
                    ->orWhere('users.Contact_Number', 'like', "%{$q}%");
            });
        }

        // Order: pending by created_at desc; others by updated_at desc then created_at desc
        if ($view === 'pending') {
            $query->orderBy('health_screenings.created_at', 'desc');
        } else {
            $query->orderBy('health_screenings.updated_at', 'desc')
                  ->orderBy('health_screenings.created_at', 'desc');
        }

    $screenings = $query->get();

        // Sidebar indicator: count of pending (non-archived)
        $pendingScreenings = \DB::table('health_screenings')
            ->where('status', 'pending')
            ->whereNull('archived_at')
            ->count();

        // Counts for summary cards
        $counts = [
            'pending'  => \DB::table('health_screenings')->where('status', 'pending')->whereNull('archived_at')->count(),
            'accepted' => \DB::table('health_screenings')->where('status', 'accepted')->whereNull('archived_at')->count(),
            'declined' => \DB::table('health_screenings')->where('status', 'declined')->whereNull('archived_at')->count(),
            'archived' => \DB::table('health_screenings')->whereNotNull('archived_at')->count(),
        ];

        return view('admin.health-screening', [
            'view' => $view,
            'q' => $q,
            'year' => $year,
            'month' => $month,
            'archived_status' => $archivedStatus,
            'screenings' => $screenings,
            'pendingScreenings' => $pendingScreenings,
            'counts' => $counts,
        ]);
    }

    public function show(Request $request, $screeningId)
    {
        // Check if admin is logged in
        if (!session('admin_id')) {
            return redirect('/admin/pin');
        }

        try {
            // Use the model with relationships to get all data efficiently
            $screening = HealthScreening::with([
                'user',
                'medicalHistoryAnswers',
                'sexualHistoryAnswers',
                'donorInfantAnswers'
            ])->find($screeningId);

            if (!$screening) {
                return redirect('/admin/health-screening')->with('error', 'Health screening not found');
            }

            // Extract the related data for the view
            $medicalHistory = $screening->medicalHistoryAnswers;
            $sexualHistory = $screening->sexualHistoryAnswers;
            $donorInfant = $screening->donorInfantAnswers;
            $infants = $screening->user->infants;

            // For sidebar sync: current section view and pending count
            $currentView = $request->query('view', 'pending');
            $pendingScreenings = \DB::table('health_screenings')
                ->where('status', 'pending')
                ->whereNull('archived_at')
                ->count();

            // Also provide counts for tabs so unified blade can still render nav
            $counts = [
                'pending'  => \DB::table('health_screenings')->where('status', 'pending')->whereNull('archived_at')->count(),
                'accepted' => \DB::table('health_screenings')->where('status', 'accepted')->whereNull('archived_at')->count(),
                'declined' => \DB::table('health_screenings')->where('status', 'declined')->whereNull('archived_at')->count(),
                'archived' => \DB::table('health_screenings')->whereNotNull('archived_at')->count(),
            ];

            return view('admin.health-screening', [
                'detailMode' => true,
                'screening' => $screening,
                'medicalHistory' => $medicalHistory,
                'sexualHistory' => $sexualHistory,
                'donorInfant' => $donorInfant,
                'infants' => $infants,
                'view' => $currentView,
                'counts' => $counts,
                'pendingScreenings' => $pendingScreenings,
            ]);

        } catch (\Exception $e) {
            return redirect('/admin/health-screening')->with('error', 'An error occurred while loading the health screening');
        }
    }

    public function updateStatus(Request $request, $screeningId)
    {
        // Check if admin is logged in
        if (!session('admin_id')) {
            return redirect('/admin/pin');
        }

        try {
            $request->validate([
                'status' => 'required|in:accepted,declined',
                'admin_notes' => [
                    'nullable',
                    'string',
                    function($attribute, $value, $fail) use ($request) {
                        if ($request->status === 'declined') {
                            if (is_null($value) || trim($value) === '') {
                                $fail('Notes are required when declining a health screening.');
                            }
                        }
                    }
                ],
            ]);

            // Update health screening status using the model
            $screening = HealthScreening::find($screeningId);
            if (!$screening) {
                return redirect()->back()->with('error', 'Health screening not found');
            }

            $screening->update([
                'status' => $request->status,
                'admin_notes' => $request->admin_notes,
            ]);
            
            if ($screening) {
                // Create notification for user
                $statusText = $request->status == 'accepted' ? 'accepted' : 'declined';
                DB::table('notifications')->insert([
                    'User_ID' => $screening->User_ID,
                    'type' => 'health_screening_result',
                    'title' => 'Health Screening Result',
                    'message' => "Your health screening has been {$statusText}. " . ($request->admin_notes ? "Notes: {$request->admin_notes}" : ''),
                    'is_read' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Return specific success message based on action
            $successMessage = $request->status === 'accepted' 
                ? 'Health screening has been accepted successfully'
                : 'Health screening has been declined successfully';

            return redirect()->back()->with('success', $successMessage);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while updating the status');
        }
    }

    /**
     * Archive a health screening (soft archive via archived_at timestamp).
     */
    public function archive(Request $request, $screeningId)
    {
        if (!session('admin_id')) {
            return $request->expectsJson() ? response()->json(['success' => false, 'message' => 'Unauthorized'], 401)
                                           : redirect('/admin/pin');
        }

        try {
            $screening = HealthScreening::find($screeningId);
            if (!$screening) {
                return $request->expectsJson()
                    ? response()->json(['success' => false, 'message' => 'Health screening not found'], 404)
                    : redirect()->back()->with('error', 'Health screening not found');
            }

        // Allow archiving accepted or declined screenings
        if (!in_array($screening->status, ['accepted', 'declined'])) {
                return $request->expectsJson()
            ? response()->json(['success' => false, 'message' => 'Only accepted or declined screenings can be archived'], 422)
            : redirect()->back()->with('error', 'Only accepted or declined screenings can be archived');
            }

            $screening->archived_at = now();
            // Preserve updated_at (date accepted)
            $screening->timestamps = false;
            $screening->save();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Screening archived successfully']);
            }
            return redirect()->back()->with('success', 'Screening archived successfully');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'An error occurred while archiving'], 500);
            }
            return redirect()->back()->with('error', 'An error occurred while archiving the screening');
        }
    }

    /**
     * Unarchive a health screening by clearing archived_at.
     */
    public function unarchive(Request $request, $screeningId)
    {
        if (!session('admin_id')) {
            return $request->expectsJson() ? response()->json(['success' => false, 'message' => 'Unauthorized'], 401)
                                           : redirect('/admin/pin');
        }

        try {
            $screening = HealthScreening::find($screeningId);
            if (!$screening) {
                return $request->expectsJson()
                    ? response()->json(['success' => false, 'message' => 'Health screening not found'], 404)
                    : redirect()->back()->with('error', 'Health screening not found');
            }

            // Only unarchive if currently archived
            if (is_null($screening->archived_at)) {
                return $request->expectsJson()
                    ? response()->json(['success' => false, 'message' => 'Screening is not archived'], 422)
                    : redirect()->back()->with('error', 'Screening is not archived');
            }

            // Clear archived_at; preserve updated_at
            $screening->archived_at = null;
            $screening->timestamps = false;
            $screening->save();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Screening unarchived successfully']);
            }
            return redirect()->back()->with('success', 'Screening unarchived successfully');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'An error occurred while unarchiving'], 500);
            }
            return redirect()->back()->with('error', 'An error occurred while unarchiving the screening');
        }
    }

    /**
     * Permanently delete a health screening. Only allowed if archived.
     */
    public function destroy(Request $request, $screeningId)
    {
        if (!session('admin_id')) {
            return $request->expectsJson() ? response()->json(['success' => false, 'message' => 'Unauthorized'], 401)
                                           : redirect('/admin/pin');
        }

        try {
            $screening = HealthScreening::find($screeningId);
            if (!$screening) {
                return $request->expectsJson()
                    ? response()->json(['success' => false, 'message' => 'Health screening not found'], 404)
                    : redirect()->back()->with('error', 'Health screening not found');
            }

            if (is_null($screening->archived_at)) {
                return $request->expectsJson()
                    ? response()->json(['success' => false, 'message' => 'Only archived screenings can be deleted'], 422)
                    : redirect()->back()->with('error', 'Only archived screenings can be deleted');
            }

            // Delete related answers to avoid orphaned rows (if no cascade constraints)
            try {
                $screening->medicalHistoryAnswers()->delete();
            } catch (\Throwable $e) { /* ignore */ }
            try {
                $screening->sexualHistoryAnswers()->delete();
            } catch (\Throwable $e) { /* ignore */ }
            try {
                $screening->donorInfantAnswers()->delete();
            } catch (\Throwable $e) { /* ignore */ }
            try {
                if (method_exists($screening, 'notifications')) {
                    $screening->notifications()->delete();
                }
            } catch (\Throwable $e) { /* ignore */ }

            $screening->delete();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Health screening deleted successfully']);
            }
            return redirect()->back()->with('success', 'Health screening deleted successfully');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'An error occurred while deleting'], 500);
            }
            return redirect()->back()->with('error', 'An error occurred while deleting the screening');
        }
    }
}
