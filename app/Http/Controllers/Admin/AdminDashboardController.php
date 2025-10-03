<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function dashboard()
    {
        // Check if admin is logged in
        if (!session('admin_id')) {
            return redirect('/admin/pin');
        }

        // Get dashboard statistics
        $totalUsers = DB::table('users')->count();
        $totalHealthScreenings = DB::table('health_screenings')->count();
        $pendingScreenings = DB::table('health_screenings')->where('status', 'pending')->count();
        $approvedScreenings = DB::table('health_screenings')->where('status', 'approved')->count();
        $rejectedScreenings = DB::table('health_screenings')->where('status', 'rejected')->count();
    // Use authoritative sources for analytics: completed donations from donation_history and APPROVED breastmilk requests only
    $totalDonations = DB::table('donation_history')->where('status', 'completed')->count();
    $totalRequests = DB::table('breastmilk_requests')->where('status', 'approved')->count();

        // Pending indicators for cards
        $pendingWalkInCount = DB::table('walk_in_requests')
            ->where('status', 'pending')
            ->count();
        $pendingHomeCollectionCount = DB::table('donation_history')
            ->where('donation_type', 'home_collection')
            ->where('status', 'pending')
            ->count();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalHealthScreenings',
            'pendingScreenings',
            'approvedScreenings',
            'rejectedScreenings',
            'totalDonations',
            'totalRequests',
            'pendingWalkInCount',
            'pendingHomeCollectionCount'
        ));
    }

    public function getHealthScreeningData($status = null)
    {
        try {
            $query = DB::table('health_screenings')
                ->join('users', 'health_screenings.User_ID', '=', 'users.User_ID')
                ->select(
                    'health_screenings.Health_Screening_ID',
                    'health_screenings.civil_status',
                    'health_screenings.occupation',
                    'health_screenings.type_of_donor',
                    'health_screenings.status',
                    'health_screenings.created_at',
                    'health_screenings.updated_at',
                    'health_screenings.admin_notes',
                    'health_screenings.archived_at',
                    'users.Full_Name',
                    'users.Contact_Number'
                );

            if ($status) {
                $query->where('health_screenings.status', $status);
            }

            // Archived filter: include only archived when ?archived=1, otherwise show non-archived by default
            $includeArchived = request()->boolean('archived', false);
            if ($includeArchived) {
                $query->whereNotNull('health_screenings.archived_at');
            } else {
                $query->whereNull('health_screenings.archived_at');
            }

            $screenings = $query->orderBy('health_screenings.created_at', 'desc')->get();

            return response()->json(['success' => true, 'data' => $screenings]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while fetching data'], 500);
        }
    }

    public function getNotifications()
    {
        try {
            $notifications = DB::table('notifications')
                ->where('is_read', false)
                ->whereNull('User_ID') // Only fetch admin notifications (those without User_ID)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json(['success' => true, 'data' => $notifications]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while fetching notifications'], 500);
        }
    }

    public function markNotificationAsRead($notificationId)
    {
        try {
            DB::table('notifications')
                ->where('id', $notificationId)
                ->update(['is_read' => true]);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while updating notification'], 500);
        }
    }

    public function getDonationData($type = null)
    {
        try {
            $query = DB::table('donation_history')
                ->join('users', 'donation_history.User_ID', '=', 'users.User_ID')
                ->select(
                    'donation_history.*',
                    'users.First_Name',
                    'users.Last_Name',
                    'users.Email'
                );

            if ($type) {
                $query->where('donation_history.donation_type', $type);
            }

            $donations = $query->orderBy('donation_history.created_at', 'desc')->get();

            return response()->json(['success' => true, 'data' => $donations]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while fetching donation data'], 500);
        }
    }

    /**
     * Lightweight analytics totals for live dashboard updates.
     * Returns total completed donations and total requests.
     */
    public function getAnalyticsTotals()
    {
        try {
            $totals = [
                'donations' => DB::table('donation_history')->where('status', 'completed')->count(),
                'requests'  => DB::table('breastmilk_requests')->where('status', 'approved')->count(),
                'screenings'=> DB::table('health_screenings')->count(),
            ];

            return response()->json(['success' => true, 'data' => $totals]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while fetching totals'], 500);
        }
    }

    /**
     * Monthly breakdown for donations (completed) and requests (approved) by month for a given year.
     * Returns labels Jan..Dec and two arrays: donations, requests.
     */
    public function getMonthlyBreakdown(Request $request)
    {
        try {
            $year = (int) ($request->query('year', now()->year));

            // Build base arrays
            $donations = array_fill(1, 12, 0);
            $requests  = array_fill(1, 12, 0);

            // Donations: count completed by month of updated_at (completion time)
            $donationRows = DB::table('donation_history')
                ->selectRaw('MONTH(updated_at) as m, COUNT(*) as c')
                ->where('status', 'completed')
                ->whereYear('updated_at', $year)
                ->groupBy('m')
                ->pluck('c', 'm');
            foreach ($donationRows as $m => $c) { $donations[(int)$m] = (int)$c; }

            // Requests: count approved by month of updated_at (approval time)
            $requestRows = DB::table('breastmilk_requests')
                ->selectRaw('MONTH(updated_at) as m, COUNT(*) as c')
                ->where('status', 'approved')
                ->whereYear('updated_at', $year)
                ->groupBy('m')
                ->pluck('c', 'm');
            foreach ($requestRows as $m => $c) { $requests[(int)$m] = (int)$c; }

            $labels = [];
            for ($i = 1; $i <= 12; $i++) { $labels[] = date('M', mktime(0,0,0,$i,1)); }

            return response()->json([
                'success' => true,
                'data' => [
                    'year' => $year,
                    'labels' => $labels,
                    'donations' => array_values($donations),
                    'requests' => array_values($requests),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to fetch monthly breakdown'], 500);
        }
    }
}
