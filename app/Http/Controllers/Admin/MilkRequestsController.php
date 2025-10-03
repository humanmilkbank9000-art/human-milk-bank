<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MilkRequestsController extends Controller
{
    public function index(Request $request)
    {
        // Require admin session
        if (!session('admin_id')) {
            return redirect('/admin/pin');
        }

        $view = $request->query('view', 'pending'); // pending | accepted | declined | archived
        $q = trim((string)$request->query('q', ''));

        // Map UI view to DB status
        $statusMap = [
            'pending' => 'pending',
            'accepted' => 'approved', // UI label Accepted -> DB status approved
            'declined' => 'declined',
            'archived' => null // Special handling below
        ];

        // Counts per tab
        try {
            $counts = [
                'pending'  => DB::table('breastmilk_requests')->where('status', 'pending')->whereNull('archived_at')->count(),
                'accepted' => DB::table('breastmilk_requests')->where('status', 'approved')->whereNull('archived_at')->count(),
                'declined' => DB::table('breastmilk_requests')->where('status', 'declined')->whereNull('archived_at')->count(),
                'archived' => DB::table('breastmilk_requests')->whereNotNull('archived_at')->count(),
            ];
        } catch (\Throwable $e) {
            $counts = ['pending'=>0,'accepted'=>0,'declined'=>0,'archived'=>0];
        }

        // Base query
        $query = DB::table('breastmilk_requests as r')
            ->join('users as u', 'r.User_ID', '=', 'u.User_ID')
            ->select(
                'r.id',
                'u.Full_Name as user_full_name',
                'u.Contact_Number as contact',
                'r.recipient_name',
                'r.recipient_dob',
                'r.recipient_weight',
                'r.requested_volume',
                'r.needed_by_date',
                'r.scheduled_date',
                'r.scheduled_time',
                'r.decided_number_of_bags',
                'r.decided_total_volume',
                'r.approved_at',
                'r.dispensed_at',
                'r.status',
                'r.prescription_image_path',
                // 'r.medical_condition', // removed from user request
                'r.contact_number',
                'r.admin_notes',
                'r.archived_at',
                'r.created_at',
                'r.updated_at'
            )
            ->orderBy('r.created_at', 'desc');

        // Apply status filter
        $dbStatus = $statusMap[$view] ?? null;
        if ($view === 'archived') {
            $query->whereNotNull('r.archived_at');
        } else {
            $query->whereNull('r.archived_at');
            if ($dbStatus) {
                $query->where('r.status', $dbStatus);
            }
        }

        if ($q !== '') {
            $query->where(function($sub) use ($q) {
                $sub->where('u.Full_Name', 'like', "%$q%")
                    ->orWhere('u.Contact_Number', 'like', "%$q%")
                    ->orWhere('r.recipient_name', 'like', "%$q%");
            });
        }

    $requests = $query->get();

        return view('admin.milk-requests', [
            'view' => $view,
            'counts' => $counts,
            'q' => $q,
            'requests' => $requests,
        ]);
    }

    public function list(Request $request)
    {
        $status = $request->query('status');

        try {
            $query = DB::table('breastmilk_requests as r')
                ->join('users as u', 'r.User_ID', '=', 'u.User_ID')
                ->select(
                    'r.id',
                    'u.Full_Name as user_full_name',
                    'r.recipient_name',
                    'r.scheduled_date',
                    'r.scheduled_time',
                    'r.status',
                    'r.prescription_image_path',
                    'r.created_at'
                )
                ->orderBy('r.created_at', 'desc');

            if ($status) {
                $query->where('r.status', $status);
            }

            $rows = $query->get();
            return response()->json(['success' => true, 'data' => $rows]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to load requests'], 500);
        }
    }

    public function updateStatus(Request $request, int $id)
    {
        // Require admin session
        if (!session('admin_id')) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }
            return redirect('/admin/pin');
        }

        $request->validate([
            'status' => 'required|in:approved,declined',
            // Notes become required ONLY when declining
            'admin_notes' => [
                'nullable',
                'string',
                function($attr,$val,$fail) use ($request){
                    if($request->input('status')==='declined' && (!is_string($val) || trim($val)==='')){
                        $fail('Comments/Notes are required when declining a request.');
                    }
                }
            ],
            // When approving, these will be validated again as required below
            'decided_number_of_bags' => 'nullable|integer|min:1',
            'decided_total_volume' => 'nullable|integer|min:1',
            'selected_batch' => 'nullable|string',
        ]);

        try {
            // If approving, auto-dispense now (record the received time as acceptance time)
            if ($request->status === 'approved') {
                // Require final numbers on approve
                $request->validate([
                    'decided_number_of_bags' => 'required|integer|min:1',
                    'decided_total_volume' => 'required|integer|min:1',
                ]);

                $updated = DB::transaction(function() use ($request, $id) {
                    // Fetch request + guardian name for dispensed records
                    $reqRow = DB::table('breastmilk_requests as r')
                        ->join('users as u','r.User_ID','=','u.User_ID')
                        ->select('r.*','u.Full_Name as guardian_name','u.User_ID')
                        ->where('r.id', $id)
                        ->lockForUpdate()
                        ->first();
                    if (!$reqRow) {
                        return 0;
                    }

                    $neededVol = (int)$request->decided_total_volume; // ml
                    $remainingVol = $neededVol;

                    // If a specific batch was selected, constrain to that batch; otherwise FIFO across all
                    $selectedBatch = trim((string)$request->input('selected_batch','')) ?: null;
                    $piQuery = DB::table('pasteurized_inventory')
                        ->select('id','unpasteurized_id','batch_number','total_volume','date_pasteurized','time_pasteurized')
                        ->lockForUpdate();
                    if ($selectedBatch) {
                        $piQuery->where('batch_number', $selectedBatch);
                    }
                    $piRows = $piQuery
                        ->orderBy('date_pasteurized','asc')
                        ->orderBy('time_pasteurized','asc')
                        ->orderBy('id','asc')
                        ->get();

                    // If selected batch doesn't have enough, supplement from other batches FIFO
                    $supplementRows = collect();
                    if ($selectedBatch && $piRows->sum('total_volume') < $neededVol) {
                        $supplementRows = DB::table('pasteurized_inventory')
                            ->select('id','unpasteurized_id','batch_number','total_volume','date_pasteurized','time_pasteurized')
                            ->where('batch_number','!=',$selectedBatch)
                            ->orderBy('date_pasteurized','asc')
                            ->orderBy('time_pasteurized','asc')
                            ->orderBy('id','asc')
                            ->lockForUpdate()
                            ->get();
                    }

                    $allRows = $selectedBatch ? $piRows->concat($supplementRows) : $piRows;
                    $totalAvail = $allRows->sum('total_volume');
                    if ($totalAvail < $neededVol) {
                        $baseMsg = 'Insufficient pasteurized stock to dispense the requested volume. Needed: '.$neededVol.' ml, Available: '.$totalAvail.' ml';
                        if ($selectedBatch) { $baseMsg .= ' (Preferred batch '.$selectedBatch.' + supplements)'; }
                        throw new \RuntimeException($baseMsg);
                    }

                    $portions = [];
                    foreach ($allRows as $pi) {
                        if ($remainingVol <= 0) break;
                        $avail = max(0, (int)$pi->total_volume);
                        if ($avail <= 0) continue;
                        $take = min($remainingVol, $avail);
                        $newVol = $avail - $take;
                        if ($newVol <= 0) {
                            // mark depleted by setting to zero (keep row to preserve FK and batch linkage)
                            DB::table('pasteurized_inventory')->where('id', $pi->id)->update([
                                'total_volume' => 0,
                                'updated_at' => now(),
                            ]);
                        } else {
                            DB::table('pasteurized_inventory')->where('id', $pi->id)->update([
                                'total_volume' => $newVol,
                                'updated_at' => now(),
                            ]);
                        }
                        $remainingVol -= $take;
                        $portions[] = [
                            'pasteurized_id' => $pi->id,
                            'unpasteurized_id' => $pi->unpasteurized_id,
                            'batch_number' => $pi->batch_number,
                            'volume' => $take,
                        ];
                    }

                    if ($remainingVol > 0) {
                        // Should not happen due to pre-check; ensure rollback
                        throw new \RuntimeException('Failed to allocate pasteurized stock for the entire requested volume.');
                    }

                    $now = now();

                    // Update request as accepted (approved) and record dispensed timestamp as now
                    $updated = DB::table('breastmilk_requests')
                        ->where('id', $id)
                        ->update([
                            'status' => 'approved',
                            'admin_notes' => $request->admin_notes,
                            'approved_at' => $now,
                            'dispensed_at' => $now,
                            'decided_number_of_bags' => (int)$request->decided_number_of_bags,
                            'decided_total_volume' => (int)$request->decided_total_volume,
                            'updated_at' => $now,
                        ]);

                    // Create dispensed records (one per portion to preserve batch linkage)
                    $nowDate = $now->format('Y-m-d');
                    $nowTime = $now->format('H:i:s');
                    foreach ($portions as $p) {
                        DB::table('dispensed_records')->insert([
                            'guardian_name' => $reqRow->guardian_name,
                            'recipient_name' => $reqRow->recipient_name,
                            'volume' => $p['volume'],
                            'unpasteurized_id' => $p['unpasteurized_id'],
                            'pasteurized_id' => $p['pasteurized_id'],
                            'date_dispensed' => $nowDate,
                            'time_dispensed' => $nowTime,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                    }

                    // Notify user as dispensed immediately
                    DB::table('notifications')->insert([
                        'User_ID' => $reqRow->User_ID,
                        'type' => 'breastmilk_request_dispensed',
                        'title' => 'Breastmilk Request Dispensed',
                        'message' => 'Your breastmilk request has been accepted and dispensed.',
                        'is_read' => false,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    return $updated;
                });
            } else {
                // Decline path already validated that notes are present (closure above)
                $updated = DB::table('breastmilk_requests')
                    ->where('id', $id)
                    ->update([
                        'status' => $request->status,
                        'admin_notes' => $request->admin_notes,
                        'approved_at' => null,
                        'updated_at' => now(),
                    ]);
            }

            if (!$updated) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Request not found'], 404);
                }
                return redirect()->back()->with('error', 'Request not found');
            }

            // Notify the user
            $userId = DB::table('breastmilk_requests')->where('id', $id)->value('User_ID');
            if ($userId) {
                if ($request->status !== 'approved') {
                    DB::table('notifications')->insert([
                        'User_ID' => $userId,
                        'type' => 'breastmilk_request_result',
                        'title' => 'Breastmilk Request Update',
                        'message' => 'Your breastmilk request has been declined.' . ($request->admin_notes ? ' Notes: ' . $request->admin_notes : ''),
                        'is_read' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            if ($request->expectsJson()) {
                return response()->json(['success' => true]);
            }
            return redirect()->back()->with('success', $request->status === 'approved' ? 'Request accepted and dispensed. Received time recorded.' : 'Breastmilk request status updated successfully');
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $msg ?: 'Failed to update status'], 500);
            }
            return redirect()->back()->with('error', $msg ?: 'Failed to update status');
        }
    }

    /**
     * Show the details of a single breastmilk request.
     */
    public function show(Request $request, int $id)
    {
        if (!session('admin_id')) {
            return redirect('/admin/pin');
        }

        // Fetch joined record
        $row = DB::table('breastmilk_requests as r')
            ->join('users as u', 'r.User_ID', '=', 'u.User_ID')
            ->select(
                'r.id',
                'r.User_ID',
                'u.Full_Name as user_full_name',
                'u.Contact_Number as contact',
                'r.recipient_name',
                'r.recipient_dob',
                'r.recipient_weight',
                'r.requested_volume',
                'r.needed_by_date',
                'r.scheduled_date',
                'r.scheduled_time',
                'r.decided_number_of_bags',
                'r.decided_total_volume',
                'r.approved_at',
                'r.dispensed_at',
                'r.status',
                'r.prescription_image_path',
                // 'r.medical_condition', // removed from user request
                'r.contact_number',
                'r.admin_notes',
                'r.archived_at',
                'r.created_at',
                'r.updated_at'
            )
            ->where('r.id', $id)
            ->first();

        if (!$row) {
            return redirect()->route('admin.milk-requests')->with('error', 'Request not found');
        }

        // Build counts for tabs
        $counts = [
            'pending'  => DB::table('breastmilk_requests')->where('status', 'pending')->whereNull('archived_at')->count(),
            'accepted' => DB::table('breastmilk_requests')->where('status', 'approved')->whereNull('archived_at')->count(),
            'declined' => DB::table('breastmilk_requests')->where('status', 'declined')->whereNull('archived_at')->count(),
            'archived' => DB::table('breastmilk_requests')->whereNotNull('archived_at')->count(),
        ];

        // Resolve prescription URL and type (image/pdf/other)
        $prescriptionUrl = null;
        $prescriptionType = null;
        if (!empty($row->prescription_image_path)) {
            $path = trim((string) $row->prescription_image_path);
            $pathForType = strtolower($path);

            // Determine file type by extension
            $ext = strtolower(pathinfo(parse_url($pathForType, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
            $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
            if (in_array($ext, $imageExts, true)) {
                $prescriptionType = 'image';
            } elseif ($ext === 'pdf') {
                $prescriptionType = 'pdf';
            } else {
                $prescriptionType = 'other';
            }

            // Absolute URL
            if (preg_match('/^https?:\/\//i', $path)) {
                $prescriptionUrl = $path;
            } else {
                // Normalize to public disk relative path
                $normalized = ltrim(str_replace('\\', '/', $path), '/');
                if (str_starts_with($normalized, 'storage/')) {
                    // storage symlink path -> strip 'storage/' for disk path
                    $normalized = ltrim(substr($normalized, 8), '/');
                }
                if (str_starts_with($normalized, 'public/')) {
                    // Some apps persist 'public/...' -> strip for disk path
                    $normalized = ltrim(substr($normalized, 7), '/');
                }

                try {
                    $public = Storage::disk('public');
                    // If the file exists on public disk, generate URL respecting subfolder base
                    if ($public->exists($normalized)) {
                        $prescriptionUrl = url('storage/' . $normalized);
                    } else {
                        // Build URL assuming standard storage symlink; use url() to respect base path
                        $prescriptionUrl = url('storage/' . $normalized);
                    }
                } catch (\Throwable $e) {
                    // Fallback to url() using storage symlink pattern
                    $prescriptionUrl = url('storage/' . $normalized);
                }
            }
        }

        return view('admin.milk-requests', [
            'detailMode' => true,
            'view' => $request->query('view', 'pending'),
            'counts' => $counts,
            'requestRow' => $row,
            'prescriptionUrl' => $prescriptionUrl,
            'prescriptionType' => $prescriptionType,
        ]);
    }

    /**
     * Admin dispenses milk for a request and records final numbers.
     */
    public function dispense(Request $request, int $id)
    {
        if (!session('admin_id')) {
            return $request->expectsJson() ? response()->json(['success' => false, 'message' => 'Unauthorized'], 401)
                                           : redirect('/admin/pin');
        }

        $request->validate([
            'decided_number_of_bags' => 'required|integer|min:1',
            'decided_total_volume' => 'required|integer|min:1', // ml
            'selected_batch' => 'nullable|string',
            'dispense_date' => 'nullable|date',
            'dispense_time' => 'nullable|date_format:H:i',
        ]);

        try {
            $updated = DB::transaction(function() use ($request, $id) {
                $row = DB::table('breastmilk_requests as r')
                    ->join('users as u','r.User_ID','=','u.User_ID')
                    ->select('r.*','u.Full_Name as guardian_name','u.User_ID')
                    ->where('r.id', $id)
                    ->lockForUpdate()
                    ->first();
                if (!$row) {
                    throw new \RuntimeException('Request not found');
                }

                $neededVol = (int)$request->decided_total_volume; // ml
                $remainingVol = $neededVol;

                $selectedBatch = trim((string)$request->input('selected_batch','')) ?: null;
                $piQuery = DB::table('pasteurized_inventory')
                    ->select('id','unpasteurized_id','batch_number','total_volume','date_pasteurized','time_pasteurized')
                    ->lockForUpdate();
                if ($selectedBatch) {
                    $piQuery->where('batch_number', $selectedBatch);
                }
                $piRows = $piQuery
                    ->orderBy('date_pasteurized','asc')
                    ->orderBy('time_pasteurized','asc')
                    ->orderBy('id','asc')
                    ->get();

                $supplementRows = collect();
                if ($selectedBatch && $piRows->sum('total_volume') < $neededVol) {
                    $supplementRows = DB::table('pasteurized_inventory')
                        ->select('id','unpasteurized_id','batch_number','total_volume','date_pasteurized','time_pasteurized')
                        ->where('batch_number','!=',$selectedBatch)
                        ->orderBy('date_pasteurized','asc')
                        ->orderBy('time_pasteurized','asc')
                        ->orderBy('id','asc')
                        ->lockForUpdate()
                        ->get();
                }

                $allRows = $selectedBatch ? $piRows->concat($supplementRows) : $piRows;
                $totalAvail = $allRows->sum('total_volume');
                if ($totalAvail < $neededVol) {
                    $baseMsg = 'Insufficient pasteurized stock to dispense the requested volume. Needed: '.$neededVol.' ml, Available: '.$totalAvail.' ml';
                    if ($selectedBatch) { $baseMsg .= ' (Preferred batch '.$selectedBatch.' + supplements)'; }
                    throw new \RuntimeException($baseMsg);
                }

                $portions = [];
                foreach ($allRows as $pi) {
                    if ($remainingVol <= 0) break;
                    $avail = max(0, (int)$pi->total_volume);
                    if ($avail <= 0) continue;
                    $take = min($remainingVol, $avail);
                    $newVol = $avail - $take;
                    if ($newVol <= 0) {
                        DB::table('pasteurized_inventory')->where('id', $pi->id)->update([
                            'total_volume' => 0,
                            'updated_at' => now(),
                        ]);
                    } else {
                        DB::table('pasteurized_inventory')->where('id', $pi->id)->update([
                            'total_volume' => $newVol,
                            'updated_at' => now(),
                        ]);
                    }
                    $remainingVol -= $take;
                    $portions[] = [
                        'pasteurized_id' => $pi->id,
                        'unpasteurized_id' => $pi->unpasteurized_id,
                        'batch_number' => $pi->batch_number,
                        'volume' => $take,
                    ];
                }

                if ($remainingVol > 0) {
                    throw new \RuntimeException('Failed to allocate pasteurized stock for the entire requested volume.');
                }

                // Resolve dispensed_at from provided date/time or now
                $dispenseDate = trim((string)$request->input('dispense_date',''));
                $dispenseTime = trim((string)$request->input('dispense_time',''));
                if ($dispenseDate !== '') {
                    $dispensedAt = \Carbon\Carbon::parse($dispenseDate.' '.($dispenseTime !== '' ? $dispenseTime : '00:00:00'));
                } else {
                    $dispensedAt = now();
                }

                DB::table('breastmilk_requests')->where('id', $id)->update([
                    'decided_number_of_bags' => (int)$request->decided_number_of_bags,
                    'decided_total_volume' => (int)$request->decided_total_volume,
                    // Keep status as approved to remain in Accepted tab
                    'dispensed_at' => $dispensedAt,
                    'updated_at' => now(),
                ]);

                // Create dispensed records with provided date/time
                $dispDate = $dispensedAt->format('Y-m-d');
                $dispTime = $dispensedAt->format('H:i:s');
                foreach ($portions as $p) {
                    DB::table('dispensed_records')->insert([
                        'guardian_name' => $row->guardian_name,
                        'recipient_name' => $row->recipient_name,
                        'volume' => $p['volume'],
                        'unpasteurized_id' => $p['unpasteurized_id'],
                        'pasteurized_id' => $p['pasteurized_id'],
                        'date_dispensed' => $dispDate,
                        'time_dispensed' => $dispTime,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // notify user
                DB::table('notifications')->insert([
                    'User_ID' => $row->User_ID,
                    'type' => 'breastmilk_request_dispensed',
                    'title' => 'Breastmilk Request Dispensed',
                    'message' => 'Your breastmilk has been dispensed. Please coordinate with the unit for pickup details.',
                    'is_read' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                return 1;
            });

            return $request->expectsJson()
                ? response()->json(['success' => true])
                : redirect()->back()->with('success', 'Marked as dispensed.');
        } catch (\Exception $e) {
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'Failed to update: '.$e->getMessage()], 500)
                : redirect()->back()->with('error', 'Failed to update: '.$e->getMessage());
        }
    }

    /**
     * Update the recorded dispense date/time after the fact.
     */
    public function updateDispenseTime(Request $request, int $id)
    {
        if (!session('admin_id')) {
            return $request->expectsJson() ? response()->json(['success' => false, 'message' => 'Unauthorized'], 401)
                                           : redirect('/admin/pin');
        }

        $request->validate([
            'dispense_date' => 'required|date',
            'dispense_time' => 'required|date_format:H:i',
        ]);

        try {
            $row = DB::table('breastmilk_requests as r')
                ->join('users as u','r.User_ID','=','u.User_ID')
                ->select('r.*','u.Full_Name as guardian_name')
                ->where('r.id', $id)
                ->first();
            if (!$row) {
                return $request->expectsJson()
                    ? response()->json(['success' => false, 'message' => 'Request not found'], 404)
                    : redirect()->back()->with('error', 'Request not found');
            }

            $dt = \Carbon\Carbon::parse($request->dispense_date.' '.$request->dispense_time.':00');

            DB::transaction(function() use ($id, $dt, $row) {
                DB::table('breastmilk_requests')->where('id', $id)->update([
                    'dispensed_at' => $dt,
                    'updated_at' => now(),
                ]);
                // Best-effort update of dispensed_records by guardian/recipient and previous recorded datetime
                if (!empty($row->dispensed_at)) {
                    $old = \Carbon\Carbon::parse($row->dispensed_at);
                    DB::table('dispensed_records')
                        ->where('guardian_name', $row->guardian_name)
                        ->where('recipient_name', $row->recipient_name)
                        ->where('date_dispensed', $old->format('Y-m-d'))
                        ->where('time_dispensed', $old->format('H:i:s'))
                        ->update([
                            'date_dispensed' => $dt->format('Y-m-d'),
                            'time_dispensed' => $dt->format('H:i:s'),
                            'updated_at' => now(),
                        ]);
                }
            });

            return $request->expectsJson()
                ? response()->json(['success' => true])
                : redirect()->back()->with('success', 'Dispense time updated.');
        } catch (\Exception $e) {
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'Failed to update: '.$e->getMessage()], 500)
                : redirect()->back()->with('error', 'Failed to update: '.$e->getMessage());
        }
    }

    /**
     * Archive a breastmilk request (accepted or declined only).
     */
    public function archive(Request $request, int $id)
    {
        if (!session('admin_id')) {
            return $request->expectsJson() ? response()->json(['success' => false, 'message' => 'Unauthorized'], 401)
                                           : redirect('/admin/pin');
        }

        try {
            $row = DB::table('breastmilk_requests')->where('id', $id)->first();
            if (!$row) {
                return $request->expectsJson()
                    ? response()->json(['success' => false, 'message' => 'Request not found'], 404)
                    : redirect()->back()->with('error', 'Request not found');
            }
            if (!in_array($row->status, ['approved','declined'], true)) {
                return $request->expectsJson()
                    ? response()->json(['success' => false, 'message' => 'Only accepted or declined requests can be archived'], 422)
                    : redirect()->back()->with('error', 'Only accepted or declined requests can be archived');
            }
            if (!is_null($row->archived_at)) {
                return $request->expectsJson()
                    ? response()->json(['success' => false, 'message' => 'Request already archived'], 422)
                    : redirect()->back()->with('error', 'Request already archived');
            }

            DB::table('breastmilk_requests')->where('id', $id)->update([
                'archived_at' => now(),
            ]);

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Request archived successfully']);
            }
            return redirect()->back()->with('success', 'Request archived successfully');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to archive'], 500);
            }
            return redirect()->back()->with('error', 'Failed to archive request');
        }
    }

    /**
     * Unarchive a breastmilk request.
     */
    public function unarchive(Request $request, int $id)
    {
        if (!session('admin_id')) {
            return $request->expectsJson() ? response()->json(['success' => false, 'message' => 'Unauthorized'], 401)
                                           : redirect('/admin/pin');
        }

        try {
            $row = DB::table('breastmilk_requests')->where('id', $id)->first();
            if (!$row) {
                return $request->expectsJson()
                    ? response()->json(['success' => false, 'message' => 'Request not found'], 404)
                    : redirect()->back()->with('error', 'Request not found');
            }
            if (is_null($row->archived_at)) {
                return $request->expectsJson()
                    ? response()->json(['success' => false, 'message' => 'Request is not archived'], 422)
                    : redirect()->back()->with('error', 'Request is not archived');
            }

            DB::table('breastmilk_requests')->where('id', $id)->update([
                'archived_at' => null,
            ]);

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Request unarchived successfully']);
            }
            return redirect()->back()->with('success', 'Request unarchived successfully');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to unarchive'], 500);
            }
            return redirect()->back()->with('error', 'Failed to unarchive request');
        }
    }

    /**
     * Permanently delete an archived breastmilk request.
     */
    public function destroy(Request $request, int $id)
    {
        if (!session('admin_id')) {
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'Unauthorized'], 401)
                : redirect('/admin/pin');
        }

        try {
            $row = DB::table('breastmilk_requests')->where('id', $id)->first();
            if (!$row) {
                return $request->expectsJson()
                    ? response()->json(['success' => false, 'message' => 'Request not found'], 404)
                    : redirect()->back()->with('error', 'Request not found');
            }
            if (is_null($row->archived_at)) {
                return $request->expectsJson()
                    ? response()->json(['success' => false, 'message' => 'Only archived requests can be deleted'], 422)
                    : redirect()->back()->with('error', 'Only archived requests can be deleted');
            }

            DB::table('breastmilk_requests')->where('id', $id)->delete();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Request deleted successfully']);
            }
            return redirect()->back()->with('success', 'Request deleted successfully');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to delete request'], 500);
            }
            return redirect()->back()->with('error', 'Failed to delete request');
        }
    }
}
