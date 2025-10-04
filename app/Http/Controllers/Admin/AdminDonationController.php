<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\WalkInRequest;

class AdminDonationController extends Controller
{
    public function index()
    {
        // Show the dedicated donations management page
        // Precompute tab counts to avoid zero flicker on initial render.
        // Definitions (must match the AJAX panel loaders in the Blade/JS):
        //  Pending:  walk_in_requests.status = 'pending'  (ONLY pending)  PLUS
        //            donation_history (home_collection, status='pending', NOT archived)
        //  Scheduled Pickups: donation_history (home_collection, status='scheduled', NOT archived, has scheduled_date & scheduled_time)
        //  Completed Walk-in: donation_history (walk_in, status='completed', NOT archived)
        //  Completed Home Collection: donation_history (home_collection, status='completed', NOT archived)
        //  Archived: donation_history (archived_at NOT NULL) – both types
        $tabCounts = [
            'pending' => 0,
            'scheduled' => 0,
            'completed_walkin' => 0,
            'completed_pickup' => 0,
            'archived' => 0,
        ];
        try {
            // Pending walk-in requests (exclude confirmed / validated to match UI list)
            $pendingWalkIns = DB::table('walk_in_requests')
                ->where('status', 'pending')
                ->count();

            // Pending home collection (still just a request, not scheduled yet)
            $pendingHome = DB::table('donation_history')
                ->whereNull('archived_at')
                ->where('donation_type', 'home_collection')
                ->where('status', 'pending')
                ->count();

            $tabCounts['pending'] = $pendingWalkIns + $pendingHome;

            // Scheduled (awaiting validation) home collections
            $tabCounts['scheduled'] = DB::table('donation_history')
                ->whereNull('archived_at')
                ->where('donation_type', 'home_collection')
                ->where('status', 'scheduled')
                ->whereNotNull('scheduled_date')
                ->whereNotNull('scheduled_time')
                ->count();

            // Completed walk-in donations
            $tabCounts['completed_walkin'] = DB::table('donation_history')
                ->whereNull('archived_at')
                ->where('donation_type', 'walk_in')
                ->where('status', 'completed')
                ->count();

            // Completed home collection donations
            $tabCounts['completed_pickup'] = DB::table('donation_history')
                ->whereNull('archived_at')
                ->where('donation_type', 'home_collection')
                ->where('status', 'completed')
                ->count();

            // Archived (any type)
            $tabCounts['archived'] = DB::table('donation_history')
                ->whereNotNull('archived_at')
                ->count();
        } catch (\Throwable $e) {
            // Silently fail; counts will self-correct via JS refresh calls after page load.
        }

        return view('admin.donations.index', compact('tabCounts'));
    }

    // Get all donations
    public function getAllDonations()
    {
        try {
            $donations = DB::table('donation_history')
                ->join('users', 'donation_history.User_ID', '=', 'users.User_ID')
                ->select(
                    'donation_history.*',
                    'users.Full_Name',
                    'users.Contact_Number'
                )
                ->orderBy('donation_history.created_at', 'desc')
                ->get();

            return response()->json(['success' => true, 'data' => $donations]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while fetching donations'], 500);
        }
    }

    // Get donations by type
    public function getDonationsByType($type)
    {
        try {
            $donations = DB::table('donation_history')
                ->join('users', 'donation_history.User_ID', '=', 'users.User_ID')
                ->where('donation_history.donation_type', $type)
                ->select(
                    'donation_history.*',
                    'users.Full_Name',
                    'users.Contact_Number'
                )
                ->orderBy('donation_history.created_at', 'desc')
                ->get();

            return response()->json(['success' => true, 'data' => $donations]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while fetching donations'], 500);
        }
    }

    // Update donation status
    public function updateDonationStatus(Request $request, $donationId)
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,approved,completed,rejected',
                'admin_notes' => 'nullable|string',
                'number_of_bags' => 'nullable|integer|min:1',
                'total_volume' => 'nullable|numeric|min:1',
            ]);

            $donation = DB::table('donation_history')
                ->where('id', $donationId)
                ->first();

            if (!$donation) {
                return response()->json(['success' => false, 'message' => 'Donation not found'], 404);
            }

            $updateData = [
                'status' => $request->status,
                'admin_notes' => $request->admin_notes,
                'updated_at' => now(),
            ];

            // For walk-in donations, admin can add the actual data after collection
            if ($request->status === 'completed' && $donation->donation_type === 'walk_in') {
                if ($request->number_of_bags && $request->total_volume) {
                    $updateData['number_of_bags'] = $request->number_of_bags;
                    $updateData['total_volume'] = $request->total_volume;
                }
            }

            // For home collection, admin can verify and update the data
            if ($request->status === 'completed' && $donation->donation_type === 'home_collection') {
                if ($request->number_of_bags) {
                    $updateData['number_of_bags'] = $request->number_of_bags;
                }
                if ($request->total_volume) {
                    $updateData['total_volume'] = $request->total_volume;
                }
            }

            DB::table('donation_history')
                ->where('id', $donationId)
                ->update($updateData);

            // If marked completed now, insert into unpasteurized inventory (idempotent)
            if ($request->status === 'completed') {
                $completed = DB::table('donation_history')->where('id', $donationId)->first();
                if ($completed) {
                    DB::table('unpasteurized_inventory')->insertOrIgnore([
                        'donation_id'   => $completed->id,
                        'User_ID'       => $completed->User_ID,
                        'number_of_bags'=> $completed->number_of_bags,
                        'total_volume'  => $completed->total_volume,
                        'date_received' => now()->toDateString(),
                        'time_received' => now()->format('H:i:s'),
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);
                }
            }

            // Create notification for user
            $userName = DB::table('users')->where('User_ID', $donation->User_ID)->value('Full_Name');
            
            DB::table('notifications')->insert([
                'title' => 'Donation Status Updated',
                'message' => 'Your ' . $donation->donation_type . ' donation has been ' . $request->status . '.',
                'type' => 'donation_status',
                'User_ID' => $donation->User_ID,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json(['success' => true, 'message' => 'Donation status updated successfully']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while updating donation status'], 500);
        }
    }

    // Get donation details
    public function getDonationDetails($donationId)
    {
        try {
            $donation = DB::table('donation_history')
                ->join('users', 'donation_history.User_ID', '=', 'users.User_ID')
                ->where('donation_history.id', $donationId)
                ->select(
                    'donation_history.*',
                    'users.Full_Name',
                    'users.Contact_Number',
                    'users.Address'
                )
                ->first();

            if (!$donation) {
                return response()->json(['success' => false, 'message' => 'Donation not found'], 404);
            }

            return response()->json(['success' => true, 'data' => $donation]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while fetching donation details'], 500);
        }
    }

    // Get donation statistics
    public function getDonationStats()
    {
        try {
            $stats = [
                'total_donations' => DB::table('donation_history')->whereNull('archived_at')->count(),
                'pending_donations' => DB::table('donation_history')->whereNull('archived_at')->where('status', 'pending')->count(),
                'approved_donations' => DB::table('donation_history')->whereNull('archived_at')->where('status', 'approved')->count(),
                'completed_donations' => DB::table('donation_history')->whereNull('archived_at')->where('status', 'completed')->count(),
                'walk_in_donations' => DB::table('donation_history')->whereNull('archived_at')->where('donation_type', 'walk_in')->count(),
                'home_collection_donations' => DB::table('donation_history')->whereNull('archived_at')->where('donation_type', 'home_collection')->count(),
                'total_volume' => DB::table('donation_history')->whereNull('archived_at')->whereNotNull('total_volume')->sum('total_volume'),
                'total_bags' => DB::table('donation_history')->whereNull('archived_at')->whereNotNull('number_of_bags')->sum('number_of_bags'),
            ];

            return response()->json(['success' => true, 'data' => $stats]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while fetching statistics'], 500);
        }
    }

    // Archive a donation (completed or otherwise)
    public function archiveDonation($id)
    {
        try {
            $updated = DB::table('donation_history')->where('id', $id)->update([
                'archived_at' => now(),
                'updated_at' => now(),
            ]);
            if(!$updated){
                return response()->json(['success'=>false,'message'=>'Donation not found'],404);
            }
            return response()->json(['success'=>true]);
        } catch(\Exception $e){
            return response()->json(['success'=>false,'message'=>'Failed to archive donation'],500);
        }
    }

    // Unarchive a donation
    public function unarchiveDonation($id)
    {
        try {
            $updated = DB::table('donation_history')->where('id', $id)->update([
                'archived_at' => null,
                'updated_at' => now(),
            ]);
            if(!$updated){
                return response()->json(['success'=>false,'message'=>'Donation not found'],404);
            }
            return response()->json(['success'=>true]);
        } catch(\Exception $e){
            return response()->json(['success'=>false,'message'=>'Failed to unarchive donation'],500);
        }
    }

    // Permanently delete an archived donation
    public function destroy($id)
    {
        try {
            $row = DB::table('donation_history')->where('id', $id)->first();
            if (!$row) {
                return response()->json(['success'=>false,'message'=>'Donation not found'],404);
            }
            if (is_null($row->archived_at)) {
                return response()->json(['success'=>false,'message'=>'Only archived donations can be deleted'],422);
            }
            DB::table('donation_history')->where('id', $id)->delete();
            return response()->json(['success'=>true]);
        } catch(\Exception $e){
            return response()->json(['success'=>false,'message'=>'Failed to delete donation'],500);
        }
    }

    // List archived donations (both types)
    public function getArchivedDonations(Request $request)
    {
        try {
            $q = trim((string) $request->query('q', ''));
            $type = trim((string) $request->query('type', ''));
            $year = $request->query('year');
            $month = $request->query('month');

            $query = DB::table('donation_history as dh')
                ->join('users as u','dh.User_ID','=','u.User_ID')
                ->whereNotNull('dh.archived_at');

            if ($q !== '') {
                $query->where(function($w) use ($q){
                    $w->where('u.Full_Name','like','%'.$q.'%')
                      ->orWhere('u.Contact_Number','like','%'.$q.'%');
                });
            }

            if (in_array($type, ['walk_in','home_collection'], true)) {
                $query->where('dh.donation_type', $type);
            }

            // Filter by year/month using donation_date (fallback to scheduled_date for home_collection if needed)
            if ($year) {
                $query->whereYear('dh.donation_date', $year)
                      ->orWhere(function($w) use ($year){
                          $w->where('dh.donation_type','home_collection')->whereYear('dh.scheduled_date', $year);
                      });
            }
            if ($month) {
                $query->whereMonth('dh.donation_date', $month)
                      ->orWhere(function($w) use ($month){
                          $w->where('dh.donation_type','home_collection')->whereMonth('dh.scheduled_date', $month);
                      });
            }

            $rows = $query->select([
                    'dh.id','u.Full_Name','u.Contact_Number','dh.donation_type','dh.number_of_bags','dh.total_volume','dh.donation_date','dh.donation_time','dh.scheduled_date','dh.scheduled_time','dh.pickup_address','dh.archived_at'
                ])
                ->orderBy('dh.archived_at','desc')
                ->get();

            return response()->json(['success'=>true,'data'=>$rows]);
        } catch(\Exception $e){
            return response()->json(['success'=>false,'message'=>'Failed to fetch archived donations'],500);
        }
    }

    // Get pending donation requests (both walk-in and home collection)
    public function getPendingWalkInRequests()
    {
        try {
            // Get walk-in requests
            $walkInRequests = WalkInRequest::with('user')
                ->where('status', 'pending')
                ->orderBy('donation_date', 'asc')
                ->orderBy('donation_time', 'asc')
                ->get()
                ->map(function ($request) {
                    return [
                        'id' => $request->id,
                        'donor_name' => $request->donor_name,
                        'donation_date' => $request->donation_date,
                        'donation_time' => $request->donation_time,
                        'type' => 'walk_in',
                        'number_of_bags' => null, // Walk-in requests don't have this until donation
                        'total_volume' => null,   // Walk-in requests don't have this until donation
                        'pickup_address' => null,
                        // Provide donor's profile address for walk-in display fallback
                        'address' => optional($request->user)->Address,
                    ];
                });

            // Get home collection requests from donation_history table (where they're actually stored)
            $homeCollectionRequests = DB::table('donation_history')
                ->join('users', 'donation_history.User_ID', '=', 'users.User_ID')
                ->where('donation_history.donation_type', 'home_collection')
                ->where('donation_history.status', 'pending')
                ->whereNull('donation_history.archived_at')
                ->select(
                    'donation_history.id',
                    'users.Full_Name as donor_name',
                    'donation_history.donation_date',
                    'donation_history.donation_time',
                    'donation_history.number_of_bags',
                    'donation_history.total_volume',
                    'donation_history.pickup_address',
                    'users.Address as address'
                )
                ->orderBy('donation_history.created_at', 'desc')
                ->get()
                ->map(function ($request) {
                    return [
                        'id' => $request->id,
                        'donor_name' => $request->donor_name,
                        'donation_date' => $request->donation_date,
                        'donation_time' => $request->donation_time,
                        'type' => 'home_collection',
                        'number_of_bags' => $request->number_of_bags,
                        'total_volume' => $request->total_volume,
                        'pickup_address' => $request->pickup_address,
                        'address' => $request->address,
                    ];
                });

            // Combine both types
            $allRequests = $walkInRequests->concat($homeCollectionRequests)
                ->sortBy('donation_date')
                ->values();

            return response()->json(['success' => true, 'data' => $allRequests]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while fetching donation requests'], 500);
        }
    }

    // Confirm walk-in donation
    public function confirmWalkInDonation(Request $request, $requestId)
    {
        try {
            $request->validate([
                'number_of_bags' => 'required|integer|min:1',
                'total_volume_donated' => 'required|numeric|min:1',
                'admin_notes' => 'nullable|string|max:500',
            ]);

            $walkInRequest = WalkInRequest::find($requestId);
            if (!$walkInRequest) {
                return response()->json(['success' => false, 'message' => 'Walk-in request not found'], 404);
            }

            // Create donation and inventory atomically
            $donationId = null;
            DB::transaction(function () use ($walkInRequest, $request, &$donationId) {
                // Create donation record in donation_history
                $donationId = DB::table('donation_history')->insertGetId([
                    'User_ID' => $walkInRequest->user_id,
                    'donation_type' => 'walk_in',
                    'donation_date' => $walkInRequest->donation_date,
                    'donation_time' => $walkInRequest->donation_time,
                    'number_of_bags' => $request->number_of_bags,
                    'total_volume' => $request->total_volume_donated,
                    'status' => 'completed',
                    'admin_notes' => $request->admin_notes ?: 'Walk-in donation confirmed by admin',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Idempotent insert into unpasteurized inventory (use current time when confirming)
                DB::table('unpasteurized_inventory')->insertOrIgnore([
                    'donation_id'   => $donationId,
                    'User_ID'       => $walkInRequest->user_id,
                    'number_of_bags'=> $request->number_of_bags,
                    'total_volume'  => $request->total_volume_donated,
                    'date_received' => now()->toDateString(),
                    'time_received' => now()->format('H:i:s'),
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            });

            // Update walk-in request status
            $walkInRequest->update(['status' => 'confirmed']);

            // Create notification for user
            DB::table('notifications')->insert([
                'title' => 'Walk-in Donation Confirmed',
                'message' => 'Your walk-in donation has been successfully recorded. Thank you for your contribution!',
                'type' => 'donation_confirmed',
                'User_ID' => $walkInRequest->user_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json(['success' => true, 'message' => 'Walk-in donation confirmed successfully']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while confirming donation: ' . $e->getMessage()], 500);
        }
    }

    // Get individual walk-in request details
    public function getWalkInRequest($id)
    {
        try {
            $request = WalkInRequest::with('user')->find($id);

            if (!$request) {
                return response()->json(['success' => false, 'message' => 'Walk-in request not found'], 404);
            }

            // Ensure proper time format
            $requestData = $request->toArray();
            if (isset($requestData['donation_time'])) {
                // Ensure time is in HH:MM format
                $time = $requestData['donation_time'];
                if (strlen($time) > 5) {
                    $requestData['donation_time'] = substr($time, 0, 5);
                }
            }

            return response()->json(['success' => true, 'data' => $requestData]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while fetching walk-in request'], 500);
        }
    }

    // Get individual home collection request details
    public function getHomeCollectionRequest($id)
    {
        try {
            $request = DB::table('home_collection_requests')
                ->join('users', 'home_collection_requests.User_ID', '=', 'users.User_ID')
                ->where('home_collection_requests.id', $id)
                ->select(
                    'home_collection_requests.*',
                    'users.Full_Name as donor_name',
                    'users.Email as donor_email',
                    'users.Contact_Number as donor_contact'
                )
                ->first();

            if (!$request) {
                return response()->json(['success' => false, 'message' => 'Home collection request not found'], 404);
            }

            return response()->json(['success' => true, 'data' => $request]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while fetching home collection request'], 500);
        }
    }

    // Get individual home collection request record
    public function getDonationHistoryRecord($id)
    {
        try {
            // Get from donation_history table (where home collection requests are actually stored)
            $donation = DB::table('donation_history')
                ->join('users', 'donation_history.User_ID', '=', 'users.User_ID')
                ->where('donation_history.id', $id)
                ->select(
                    'donation_history.*',
                    'users.Full_Name as donor_name',
                    'users.Email as donor_email',
                    'users.Contact_Number as donor_contact'
                )
                ->first();

            if (!$donation) {
                return response()->json(['success' => false, 'message' => 'Donation record not found'], 404);
            }

            return response()->json(['success' => true, 'data' => $donation]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while fetching donation record'], 500);
        }
    }

    // Get pending home collection requests
    public function getPendingHomeCollectionRequests()
    {
        try {
            // Get home collection requests from donation_history table (where they're actually stored)
            $homeCollectionRequests = DB::table('donation_history')
                ->join('users', 'donation_history.User_ID', '=', 'users.User_ID')
                ->where('donation_history.donation_type', 'home_collection')
                ->where('donation_history.status', 'pending')
                ->select(
                    'donation_history.id',
                    'users.Full_Name as donor_name',
                    'donation_history.donation_date',
                    'donation_history.donation_time',
                    'donation_history.number_of_bags',
                    'donation_history.total_volume',
                    'donation_history.pickup_address'
                )
                ->orderBy('donation_history.created_at', 'desc')
                ->get()
                ->map(function ($request) {
                    return [
                        'id' => $request->id,
                        'donor_name' => $request->donor_name,
                        'donation_date' => $request->donation_date,
                        'donation_time' => $request->donation_time,
                        'type' => 'home_collection',
                        'number_of_bags' => $request->number_of_bags,
                        'total_volume' => $request->total_volume,
                        'pickup_address' => $request->pickup_address
                    ];
                });

            return response()->json(['success' => true, 'data' => $homeCollectionRequests]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while fetching home collection requests'], 500);
        }
    }

    // Get scheduled home collection requests
    public function getScheduledHomeCollectionRequests()
    {
        try {
            $requests = DB::table('donation_history')
                ->join('users', 'donation_history.User_ID', '=', 'users.User_ID')
                ->where('donation_history.donation_type', 'home_collection')
                ->where('donation_history.status', 'scheduled')
                ->whereNull('donation_history.validated_at') // Ensure not already validated
                ->whereNotNull('donation_history.scheduled_date') // Must have scheduled date
                ->whereNotNull('donation_history.scheduled_time') // Must have scheduled time
                ->whereNull('donation_history.archived_at')
                ->select(
                    'donation_history.id',
                    'users.Full_Name as donor_name',
                    'donation_history.number_of_bags',
                    'donation_history.total_volume',
                    'donation_history.scheduled_date',
                    'donation_history.scheduled_time',
                    'donation_history.pickup_address'
                )
                ->orderBy('donation_history.scheduled_date', 'asc')
                ->get();

            return response()->json(['success' => true, 'data' => $requests]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while fetching scheduled home collection requests'], 500);
        }
    }

    // Validate walk-in donation
    public function validateWalkInDonation(Request $request, $id)
    {
        try {
            $request->validate([
                'number_of_bags' => 'required|integer|min:1',
                'total_volume_donated' => 'required|numeric|min:1'
            ]);

            // Find the walk-in request
            $walkInRequest = DB::table('walk_in_requests')->where('id', $id)->first();

            if (!$walkInRequest) {
                return response()->json(['success' => false, 'message' => 'Walk-in request not found'], 404);
            }

            // Update the walk-in request status to validated
            DB::table('walk_in_requests')
                ->where('id', $id)
                ->update([
                    'status' => 'validated',
                    'validated_at' => now(),
                    'updated_at' => now()
                ]);

            // Create a donation record and inventory atomically (using user's selected time)
            $donationId = null;
            DB::transaction(function () use ($walkInRequest, $request, &$donationId) {
                $donationId = DB::table('donation_history')->insertGetId([
                    'User_ID' => $walkInRequest->user_id,
                    'donation_type' => 'walk_in',
                    'number_of_bags' => $request->number_of_bags,
                    'total_volume' => $request->total_volume_donated,
                    'donation_date' => $walkInRequest->donation_date,
                    'donation_time' => $walkInRequest->donation_time,
                    'status' => 'completed',
                    'validated_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                DB::table('unpasteurized_inventory')->insertOrIgnore([
                    'donation_id'   => $donationId,
                    'User_ID'       => $walkInRequest->user_id,
                    'number_of_bags'=> $request->number_of_bags,
                    'total_volume'  => $request->total_volume_donated,
                    'date_received' => now()->toDateString(),
                    'time_received' => now()->format('H:i:s'),
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            });

            // Create notification for user
            $user = DB::table('users')->where('User_ID', $walkInRequest->user_id)->first();
            
            DB::table('notifications')->insert([
                'title' => 'Walk-in Donation Validated',
                'message' => 'Your walk-in donation has been successfully validated and recorded. Thank you for your contribution!',
                'type' => 'donation_validated',
                'User_ID' => $walkInRequest->user_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Walk-in donation validated successfully',
                'donation_id' => $donationId
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while validating the donation'
            ], 500);
        }
    }

    // Schedule home collection
    public function scheduleHomeCollection(Request $request, $id)
    {
        try {
            $request->validate([
                'collection_date' => 'required|date|after_or_equal:today',
                'collection_time' => 'required|string',
            ]);

            // Find the home collection request in donation_history
            $homeCollection = DB::table('donation_history')
                ->where('id', $id)
                ->where('donation_type', 'home_collection')
                ->where('status', 'pending')
                ->first();

            if (!$homeCollection) {
                return response()->json(['success' => false, 'message' => 'Home collection request not found'], 404);
            }

            // Update the home collection with scheduled date and time
            DB::table('donation_history')
                ->where('id', $id)
                ->update([
                    'scheduled_date' => $request->collection_date,
                    'scheduled_time' => $request->collection_time,
                    'status' => 'scheduled',
                    'validated_at' => null, // Clear any previous validation
                    'updated_at' => now()
                ]);

            // Create notification for user
            $user = DB::table('users')->where('User_ID', $homeCollection->User_ID)->first();
            
            DB::table('notifications')->insert([
                'title' => 'Home Collection Scheduled',
                'message' => 'Your home collection has been scheduled for ' . date('M d, Y', strtotime($request->collection_date)) . ' at ' . $request->collection_time . '. Please be available at the scheduled time.',
                'type' => 'collection_scheduled',
                'User_ID' => $homeCollection->User_ID,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Home collection scheduled successfully',
                'scheduled_date' => $request->collection_date,
                'scheduled_time' => $request->collection_time
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while scheduling the collection'
            ], 500);
        }
    }

    // Validate home collection after scheduling
    public function validateHomeCollection(Request $request, $id)
    {
        try {
            $request->validate([
                'number_of_bags' => 'required|integer|min:1',
                'total_volume_donated' => 'required|numeric|min:1'
            ]);

            // Find the scheduled home collection request
            $homeCollection = DB::table('donation_history')
                ->where('id', $id)
                ->where('donation_type', 'home_collection')
                ->where('status', 'scheduled')
                ->first();

            if (!$homeCollection) {
                return response()->json(['success' => false, 'message' => 'Scheduled home collection request not found'], 404);
            }

            // Update the home collection with validation data (keep scheduled date/time)
            DB::table('donation_history')
                ->where('id', $id)
                ->update([
                    'number_of_bags' => $request->number_of_bags,
                    'total_volume' => $request->total_volume_donated,
                    'status' => 'completed',
                    'validated_at' => now(),
                    'updated_at' => now()
                ]);

            // Insert into unpasteurized inventory (use current time when admin confirms)
            $completed = DB::table('donation_history')->where('id', $id)->first();
            if ($completed) {
                DB::table('unpasteurized_inventory')->insertOrIgnore([
                    'donation_id'   => $completed->id,
                    'User_ID'       => $completed->User_ID,
                    'number_of_bags'=> $completed->number_of_bags,
                    'total_volume'  => $completed->total_volume,
                    'date_received' => now()->toDateString(),
                    'time_received' => now()->format('H:i:s'),
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }

            // Create notification for user
            DB::table('notifications')->insert([
                'title' => 'Home Collection Completed',
                'message' => 'Your home collection has been successfully completed and validated. Thank you for your donation!',
                'type' => 'collection_completed',
                'User_ID' => $homeCollection->User_ID,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Home collection validated successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while validating the collection'
            ], 500);
        }
    }
}
