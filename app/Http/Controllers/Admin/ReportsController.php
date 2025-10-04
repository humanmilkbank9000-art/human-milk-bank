<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;

class ReportsController extends Controller
{
    public function index()
    {
        return view('admin.reports');
    }

    public function getAllBreastmilkRequests()
    {
        try {
            $rows = DB::table('breastmilk_requests as r')
                ->join('users as u', 'r.User_ID', '=', 'u.User_ID')
                ->select(
                    'r.id as Breastmilk_Request_ID',
                    'u.Full_Name',
                    'r.contact_number as Contact_Number',
                    'r.prescription_image_path',
                    'r.status',
                    'r.created_at'
                )
                ->orderBy('r.created_at', 'desc')
                ->get();

            // Map to the structure expected by the admin reports modal
            $requests = $rows->map(function ($row) {
                return [
                    'Breastmilk_Request_ID' => $row->Breastmilk_Request_ID,
                    'Full_Name' => $row->Full_Name,
                    'Contact_Number' => $row->Contact_Number,
                    'Date' => optional($row->created_at)->format('Y-m-d'),
                    'Time' => optional($row->created_at)->format('H:i:s'),
                    'Prescription' => $row->prescription_image_path ? url('storage/' . $row->prescription_image_path) : null,
                    'status' => $row->status ?? 'pending',
                ];
            });

            return response()->json(['success' => true, 'data' => $requests]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while fetching data'], 500);
        }
    }

    public function getAcceptedBreastmilkRequests()
    {
        try {
            $rows = DB::table('breastmilk_requests as r')
                ->join('users as u', 'r.User_ID', '=', 'u.User_ID')
                ->where('r.status', 'approved')
                ->select(
                    'r.id as Breastmilk_Request_ID',
                    'u.Full_Name as Guardian_Name',
                    'u.Address',
                    'r.contact_number as Phone_Number',
                    'r.decided_total_volume as Dispensed_Volume',
                    'r.dispensed_at'
                )
                ->orderBy('r.dispensed_at', 'desc')
                ->get();

            $requests = $rows->map(function ($row) {
                // Safely format dispensed_at in app timezone
                $tz = config('app.timezone', 'UTC');
                $dateStr = null; $timeStr = null;
                if (!empty($row->dispensed_at)) {
                    try {
                        $c = \Carbon\Carbon::parse($row->dispensed_at)->timezone($tz);
                        $dateStr = $c->format('Y-m-d');
                        $timeStr = $c->format('H:i');
                    } catch (\Throwable $e) { /* noop */ }
                }
                return [
                    'Breastmilk_Request_ID' => $row->Breastmilk_Request_ID,
                    'Guardian_Name' => $row->Guardian_Name,
                    'Address' => $row->Address,
                    'Phone_Number' => $row->Phone_Number,
                    'Infant_Name' => null,
                    'Age' => null,
                    'Dispensed_Volume' => $row->Dispensed_Volume,
                    'Date' => $dateStr,
                    'Time' => $timeStr,
                ];
            });

            return response()->json(['success' => true, 'data' => $requests]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while fetching data'], 500);
        }
    }

    public function getDeclinedBreastmilkRequests()
    {
        try {
            $rows = DB::table('breastmilk_requests as r')
                ->join('users as u', 'r.User_ID', '=', 'u.User_ID')
                ->where('r.status', 'declined')
                ->select(
                    'r.id as Breastmilk_Request_ID',
                    'u.Full_Name as Guardian_Name',
                    'u.Address',
                    'r.contact_number as Phone_Number',
                    'r.admin_notes as Feedback',
                    'r.updated_at as declined_at'
                )
                ->orderBy('r.updated_at', 'desc')
                ->get();

            $requests = $rows->map(function ($row) {
                return [
                    'Breastmilk_Request_ID' => $row->Breastmilk_Request_ID,
                    'Guardian_Name' => $row->Guardian_Name,
                    'Address' => $row->Address,
                    'Phone_Number' => $row->Phone_Number,
                    'Date' => optional($row->declined_at)->format('Y-m-d'),
                    'Feedback' => $row->Feedback,
                ];
            });

            return response()->json(['success' => true, 'data' => $requests]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while fetching data'], 500);
        }
    }

    public function getAllBreastmilkDonations()
    {
        try {
            // Return sample data for all donations
            $donations = [
                [
                    'Breastmilk_Donation_ID' => 1,
                    'Full_Name' => 'Sarah Johnson',
                    'Donation_Method' => 'Walk-in',
                    'Number_of_Bags' => 5,
                    'Total_Volume' => '1000ml',
                    'Date' => '2025-01-15',
                    'Time' => '09:00:00'
                ],
                [
                    'Breastmilk_Donation_ID' => 2,
                    'Full_Name' => 'Lisa Chen',
                    'Donation_Method' => 'Pickup',
                    'Number_of_Bags' => 3,
                    'Total_Volume' => '600ml',
                    'Date' => '2025-01-16',
                    'Time' => '14:30:00'
                ],
                [
                    'Breastmilk_Donation_ID' => 3,
                    'Full_Name' => 'Anna Rodriguez',
                    'Donation_Method' => 'Walk-in',
                    'Number_of_Bags' => 4,
                    'Total_Volume' => '800ml',
                    'Date' => '2025-01-17',
                    'Time' => '11:15:00'
                ]
            ];
            return response()->json(['success' => true, 'data' => $donations]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while fetching data'], 500);
        }
    }

    public function getWalkInDonations()
    {
        try {
            // Fetch actual walk-in donations from donation_history table
            $donations = DB::table('donation_history')
                ->join('users', 'donation_history.User_ID', '=', 'users.User_ID')
                ->where('donation_history.donation_type', 'walk_in')
                ->where('donation_history.status', 'completed')
                ->whereNull('donation_history.archived_at')
                ->select(
                    'donation_history.id as Breastmilk_Donation_ID',
                    'users.Full_Name as Donor_Full_Name',
                    'donation_history.number_of_bags as Number_of_Bags',
                    'donation_history.total_volume as Total_Volume_Donated',
                    'donation_history.donation_date as Date',
                    'donation_history.donation_time as Time',
                    'donation_history.created_at'
                )
                ->orderBy('donation_history.donation_date', 'desc')
                ->orderBy('donation_history.donation_time', 'desc')
                ->get();

            // Format the data for display
            $formattedDonations = $donations->map(function ($donation) {
                return [
                    'Breastmilk_Donation_ID' => $donation->Breastmilk_Donation_ID,
                    'Full_Name' => $donation->Donor_Full_Name,
                    'number_of_bags' => $donation->Number_of_Bags,
                    'total_volume' => $donation->Total_Volume_Donated,
                    'donation_date' => $donation->Date,
                    'donation_time' => $donation->Time,
                    'created_at' => $donation->created_at
                ];
            });

            return response()->json(['success' => true, 'data' => $formattedDonations]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while fetching walk-in donations: ' . $e->getMessage()], 500);
        }
    }

    public function getPickupDonations()
    {
        try {
            // Debug: Check what's in donation_history table
            $allDonationHistory = DB::table('donation_history')->get();
            \Log::info('All Donation History Records:', [
                'count' => $allDonationHistory->count(),
                'sample' => $allDonationHistory->take(3)->toArray()
            ]);

            // Fetch confirmed home collection pickups from the donation_history table
            $confirmedPickups = DB::table('donation_history as dh')
                ->join('users as u', 'dh.User_ID', '=', 'u.User_ID')
                ->where('dh.donation_type', 'home_collection')
                ->where('dh.status', 'completed') // Only get completed/validated pickups
                ->whereNull('dh.archived_at')
                ->select([
                    'dh.id',
                    'u.Full_Name',
                    'dh.pickup_address',
                    'dh.number_of_bags',
                    'dh.total_volume',
                    'dh.scheduled_date',
                    'dh.scheduled_time',
                    'dh.validated_at',
                    'dh.updated_at'
                ])
                ->orderBy('dh.validated_at', 'desc')
                ->get();

            // Debug: Log the query and results
            \Log::info('Pickup Donations Query Results:', [
                'count' => $confirmedPickups->count(),
                'data' => $confirmedPickups->toArray()
            ]);

            // Format the data to match the expected structure
            $formattedDonations = $confirmedPickups->map(function ($pickup) {
                return [
                    'id' => $pickup->id,
                    'Full_Name' => $pickup->Full_Name,
                    'pickup_address' => $pickup->pickup_address,
                    'number_of_bags' => $pickup->number_of_bags,
                    'total_volume' => $pickup->total_volume,
                    'donation_date' => $pickup->scheduled_date, // Use the scheduled date as the donation date
                    'donation_time' => $pickup->scheduled_time, // Use the scheduled time as the donation time
                    'validated_at' => $pickup->validated_at,
                    'updated_at' => $pickup->updated_at
                ];
            });

            return response()->json(['success' => true, 'data' => $formattedDonations]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while fetching pickup donations: ' . $e->getMessage()], 500);
        }
    }

    public function getUnpasteurizedDonations()
    {
        try {
            $rows = DB::table('unpasteurized_inventory as ui')
                ->leftJoin('donation_history as dh','ui.donation_id','=','dh.id')
                ->leftJoin('users as u','ui.User_ID','=','u.User_ID')
                ->select([
                    'ui.id',
                    'u.Full_Name as Donor_Name',
                    'ui.number_of_bags as Number_of_Bags',
                    'ui.total_volume as Total_Volume',
                    'ui.date_received as Date',
                    'ui.time_received as Time',
                    'ui.created_at'
                ])
                ->orderBy('ui.date_received','desc')
                ->orderBy('ui.time_received','desc')
                ->get();

            // Format to match frontend expectations
            $data = $rows->map(function($r){
                return [
                    'Breastmilk_Donation_ID' => $r->id,
                    'Donor_Name' => $r->Donor_Name ?? 'Unknown Donor',
                    'Number_of_Bags' => $r->Number_of_Bags,
                    'Total_Volume' => $r->Total_Volume,
                    'Date' => $r->Date,
                    'Time' => $r->Time,
                    'created_at' => $r->created_at,
                ];
            });
            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while fetching data'], 500);
        }
    }

    public function getPasteurizedDonations()
    {
        try {
            // Select pasteurized inventory only (source unpasteurized rows may be deleted)
            $rows = DB::table('pasteurized_inventory as pi')
                ->select([
                    'pi.id as Breastmilk_Donation_ID',
                    'pi.batch_number as Batch_Number',
                    'pi.number_of_bags as Number_of_Bags',
                    'pi.total_volume as Total_Volume',
                    'pi.date_pasteurized as Date_Pasteurized',
                    'pi.time_pasteurized as Time_Pasteurized',
                    'pi.created_at'
                ])
                ->where('pi.total_volume', '>', 0)
                ->orderBy('pi.date_pasteurized','desc')
                ->orderBy('pi.time_pasteurized','desc')
                ->get();

            return response()->json(['success' => true, 'data' => $rows]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while fetching data'], 500);
        }
    }

    public function getDispensedDonations()
    {
        try {
            // Return dispensed records with batch linkage only; donor info omitted intentionally
            $rows = DB::table('dispensed_records as dr')
                ->leftJoin('pasteurized_inventory as pi','dr.pasteurized_id','=','pi.id')
                ->select([
                    'dr.id as Dispense_ID',
                    'dr.guardian_name as Guardian_Name',
                    'dr.recipient_name as Recipient_Name',
                    'dr.volume as Volume',
                    'pi.batch_number as Batch_Number',
                    'dr.date_dispensed as Date',
                    'dr.time_dispensed as Time'
                ])
                ->orderBy('dr.date_dispensed','desc')
                ->orderBy('dr.time_dispensed','desc')
                ->get();

            return response()->json(['success' => true, 'data' => $rows]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while fetching data'], 500);
        }
    }

    public function getMonthlyReports()
    {
        try {
            // Return sample data for monthly reports
            $reports = [
                [
                    'Month' => 'January 2025',
                    'Total_Donation' => 15,
                    'Total_Volume' => '3000ml',
                    'Total_Requests' => 8,
                    'Total_Volume_Requested' => '2400ml'
                ],
                [
                    'Month' => 'February 2025',
                    'Total_Donation' => 12,
                    'Total_Volume' => '2400ml',
                    'Total_Requests' => 6,
                    'Total_Volume_Requested' => '1800ml'
                ],
                [
                    'Month' => 'March 2025',
                    'Total_Donation' => 18,
                    'Total_Volume' => '3600ml',
                    'Total_Requests' => 10,
                    'Total_Volume_Requested' => '3000ml'
                ]
            ];
            return response()->json(['success' => true, 'data' => $reports]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while fetching data'], 500);
        }
    }

    /**
     * Monthly aggregates for Requests, Donations, and Inventory (last 12 months).
     */
    public function getMonthlySections()
    {
        try {
            $now = now()->startOfMonth();
            $months = [];
            for ($i=11; $i>=0; $i--) {
                $m = (clone $now)->subMonths($i);
                $key = $m->format('Y-m');
                $label = $m->format('F Y');
                $months[] = ['key'=>$key, 'label'=>$label];
            }

            $startDate = $months[0]['key'].'-01';
            $endDate = (clone $now)->endOfMonth()->format('Y-m-d');

            // Requests by created_at month
            $reqRows = DB::table('breastmilk_requests as r')
                ->selectRaw("DATE_FORMAT(r.created_at, '%Y-%m') as ym")
                ->selectRaw('COUNT(*) as total')
                ->selectRaw("SUM(CASE WHEN r.status='approved' THEN 1 ELSE 0 END) as approved")
                ->selectRaw("SUM(CASE WHEN r.status='declined' THEN 1 ELSE 0 END) as declined")
                ->selectRaw("SUM(CASE WHEN r.status IS NULL OR r.status='pending' THEN 1 ELSE 0 END) as pending")
                ->whereBetween('r.created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59'])
                ->groupBy('ym')
                ->pluck('total','ym')
                ->toArray();

            $reqApproved = DB::table('breastmilk_requests as r')
                ->selectRaw("DATE_FORMAT(r.created_at, '%Y-%m') as ym")
                ->selectRaw("SUM(CASE WHEN r.status='approved' THEN 1 ELSE 0 END) as val")
                ->whereBetween('r.created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59'])
                ->groupBy('ym')->pluck('val','ym')->toArray();
            $reqDeclined = DB::table('breastmilk_requests as r')
                ->selectRaw("DATE_FORMAT(r.created_at, '%Y-%m') as ym")
                ->selectRaw("SUM(CASE WHEN r.status='declined' THEN 1 ELSE 0 END) as val")
                ->whereBetween('r.created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59'])
                ->groupBy('ym')->pluck('val','ym')->toArray();
            $reqPending = DB::table('breastmilk_requests as r')
                ->selectRaw("DATE_FORMAT(r.created_at, '%Y-%m') as ym")
                ->selectRaw("SUM(CASE WHEN r.status IS NULL OR r.status='pending' THEN 1 ELSE 0 END) as val")
                ->whereBetween('r.created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59'])
                ->groupBy('ym')->pluck('val','ym')->toArray();

            $requests = [];
            foreach ($months as $m) {
                $ym = $m['key'];
                $requests[] = [
                    'Month' => $m['label'],
                    'total' => (int)($reqRows[$ym] ?? 0),
                    'approved' => (int)($reqApproved[$ym] ?? 0),
                    'declined' => (int)($reqDeclined[$ym] ?? 0),
                    'pending' => (int)($reqPending[$ym] ?? 0),
                ];
            }

            // Donations by month using donation_date or scheduled_date
            $donRows = DB::table('donation_history as dh')
                ->selectRaw("DATE_FORMAT(COALESCE(dh.donation_date, dh.scheduled_date), '%Y-%m') as ym")
                ->selectRaw('COUNT(*) as total')
                ->selectRaw("SUM(CASE WHEN dh.donation_type='walk_in' THEN 1 ELSE 0 END) as walk_in")
                ->selectRaw("SUM(CASE WHEN dh.donation_type='home_collection' THEN 1 ELSE 0 END) as pickup")
                ->selectRaw('SUM(COALESCE(dh.total_volume,0)) as total_volume')
                ->where('dh.status','completed')
                ->whereBetween(DB::raw('COALESCE(dh.donation_date, dh.scheduled_date)'), [$startDate, $endDate])
                ->groupBy('ym')
                ->get();
            $donMap = [];
            foreach ($donRows as $r) { $donMap[$r->ym] = $r; }
            $donations = [];
            foreach ($months as $m) {
                $ym = $m['key']; $r = $donMap[$ym] ?? null;
                $donations[] = [
                    'Month' => $m['label'],
                    'total' => (int)($r->total ?? 0),
                    'walk_in' => (int)($r->walk_in ?? 0),
                    'pickup' => (int)($r->pickup ?? 0),
                    'total_volume' => (float)($r->total_volume ?? 0),
                ];
            }

            // Inventory: pasteurized added (by date_pasteurized) and dispensed (by date_dispensed)
            $pastRows = DB::table('pasteurized_inventory as pi')
                ->selectRaw("DATE_FORMAT(pi.date_pasteurized, '%Y-%m') as ym")
                ->selectRaw('SUM(COALESCE(pi.total_volume,0)) as volume')
                ->whereBetween('pi.date_pasteurized', [$startDate, $endDate])
                ->groupBy('ym')->pluck('volume','ym')->toArray();
            $dispRows = DB::table('dispensed_records as dr')
                ->selectRaw("DATE_FORMAT(dr.date_dispensed, '%Y-%m') as ym")
                ->selectRaw('SUM(COALESCE(dr.volume,0)) as volume')
                ->whereBetween('dr.date_dispensed', [$startDate, $endDate])
                ->groupBy('ym')->pluck('volume','ym')->toArray();
            $inventory = [];
            foreach ($months as $m) {
                $ym = $m['key'];
                $inventory[] = [
                    'Month' => $m['label'],
                    'pasteurized_added' => (float)($pastRows[$ym] ?? 0),
                    'dispensed' => (float)($dispRows[$ym] ?? 0),
                ];
            }

            $monthsMeta = array_map(function($m){
                $dt = \Carbon\Carbon::createFromFormat('Y-m', $m['key']);
                return ['key'=>$m['key'], 'label'=>$m['label'], 'year'=>$dt->year, 'month'=>$dt->month];
            }, $months);

            return response()->json(['success'=>true,'data'=>[
                'months' => array_column($months,'label'),
                'months_meta' => $monthsMeta,
                'requests' => $requests,
                'donations' => $donations,
                'inventory' => $inventory,
            ]]);
        } catch (\Throwable $e) {
            return response()->json(['success'=>false,'message'=>'An error occurred while fetching monthly sections'],500);
        }
    }

    /**
     * On-demand monthly query for dependent dropdowns.
     * Query params: category=req|don|inv, sub=varies, year=YYYY, month=1-12
     */
    public function getMonthlyQuery(Request $request)
    {
        $cat = strtolower((string)$request->query('category','req'));
        $sub = strtolower((string)$request->query('sub','all'));
        $year = (int) $request->query('year', now()->year);
        $month = (int) $request->query('month', now()->month);
        if ($year < 2000 || $year > 2100) $year = now()->year;
        if ($month < 1 || $month > 12) $month = now()->month;

        $start = sprintf('%04d-%02d-01', $year, $month);
        $end = (new \DateTime($start))->modify('last day of this month')->format('Y-m-d');
        $label = date('F Y', strtotime($start));

        try {
            if ($cat === 'req') {
                // Aggregates by created_at for cards (unchanged)
                $baseCreated = DB::table('breastmilk_requests as r')
                    ->whereBetween('r.created_at', ["$start 00:00:00", "$end 23:59:59"]);
                $total = (clone $baseCreated)->count();
                $approved = (clone $baseCreated)->where('r.status','approved')->count();
                $declined = (clone $baseCreated)->where('r.status','declined')->count();
                $pending = $total - $approved - $declined;
                $focus = match($sub){
                    'accepted','approved' => $approved,
                    'declined' => $declined,
                    'all' => $total,
                    default => $total,
                };

                // Rows: only successfully dispensed requests within the selected month
                $rowsQuery = DB::table('breastmilk_requests as r')
                    ->join('users as u','r.User_ID','=','u.User_ID')
                    ->whereNotNull('r.dispensed_at')
                    ->whereBetween('r.dispensed_at', ["$start 00:00:00", "$end 23:59:59"])
                    ->whereNull('r.archived_at');

                $rows = $rowsQuery
                    ->orderBy('r.dispensed_at','desc')
                    ->get([
                        'u.Full_Name',
                        'r.decided_total_volume',
                        'r.dispensed_at',
                    ])
                    ->map(function($r){
                        $dateStr = null; $timeStr = null; $dtStr = null;
                        if (!empty($r->dispensed_at)) {
                            try {
                                $c = Carbon::parse($r->dispensed_at);
                                $dateStr = $c->format('Y-m-d');
                                $timeStr = $c->format('H:i');
                                $dtStr = $c->format('Y-m-d H:i');
                            } catch (\Throwable $e) {
                                $dateStr = null; $timeStr = null; $dtStr = null;
                            }
                        }
                        return [
                            'Full_Name' => $r->Full_Name,
                            'total_volume' => is_null($r->decided_total_volume) ? null : (int)$r->decided_total_volume,
                            'date' => $dateStr,
                            'date_received' => $dateStr,
                            'time_received' => $timeStr,
                            'datetime_received' => $dtStr,
                        ];
                    });

                return response()->json(['success'=>true,'data'=>[
                    'category'=>'req','label'=>$label,
                    'cards'=>[
                        ['label'=>'Total Requests','val'=>$total],
                        ['label'=>'Approved','val'=>$approved],
                        ['label'=>'Declined','val'=>$declined],
                        ['label'=>'Pending','val'=>$pending],
                    ],
                    'focus'=>['label'=>ucfirst($sub === 'accepted' ? 'Approved' : $sub),'val'=>$focus],
                    'rows'=>$rows,
                ]]);
            }
            if ($cat === 'don') {
                // donation_history by donation_date/scheduled_date
                $base = DB::table('donation_history as dh')
                    ->where('dh.status','completed')
                    ->whereBetween(DB::raw('COALESCE(dh.donation_date, dh.scheduled_date)'), [$start, $end]);
                $total = (clone $base)->count();
                $walkIn = (clone $base)->where('dh.donation_type','walk_in')->count();
                $pickup = (clone $base)->where('dh.donation_type','home_collection')->count();
                $totalVol = (clone $base)->sum(DB::raw('COALESCE(dh.total_volume,0)'));
                $focus = match($sub){
                    'walk_in' => $walkIn,
                    'pickup' => $pickup,
                    'all' => $total,
                    default => $total,
                };
                return response()->json(['success'=>true,'data'=>[
                    'category'=>'don','label'=>$label,
                    'cards'=>[
                        ['label'=>'Total Donations','val'=>$total],
                        ['label'=>'Walk-in','val'=>$walkIn],
                        ['label'=>'Pickup','val'=>$pickup],
                        ['label'=>'Total Volume','val'=>(float)$totalVol,'suffix'=>' ml'],
                    ],
                    'focus'=>['label'=>ucfirst(str_replace('_',' ', $sub)),'val'=>$focus]
                ]]);
            }
            // inv
            $added = DB::table('pasteurized_inventory as pi')
                ->whereBetween('pi.date_pasteurized', [$start, $end])
                ->sum(DB::raw('COALESCE(pi.total_volume,0)'));
            $disp = DB::table('dispensed_records as dr')
                ->whereBetween('dr.date_dispensed', [$start, $end])
                ->sum(DB::raw('COALESCE(dr.volume,0)'));
            $unp = DB::table('unpasteurized_inventory as ui')
                ->whereBetween('ui.date_received', [$start, $end])
                ->sum(DB::raw('COALESCE(ui.total_volume,0)'));
            $focusMap = [
                'pasteurized' => $added,
                'dispensed' => $disp,
                'unpasteurized' => $unp,
            ];
            $focus = $focusMap[$sub] ?? $added;
            return response()->json(['success'=>true,'data'=>[
                'category'=>'inv','label'=>$label,
                'cards'=>[
                    ['label'=>'Unpasteurized Received','val'=>(float)$unp,'suffix'=>' ml'],
                    ['label'=>'Pasteurized Added','val'=>(float)$added,'suffix'=>' ml'],
                    ['label'=>'Dispensed','val'=>(float)$disp,'suffix'=>' ml'],
                ],
                'focus'=>['label'=>ucfirst($sub),'val'=>$focus,'suffix'=>' ml']
            ]]);
        } catch (\Throwable $e) {
            return response()->json(['success'=>false,'message'=>'Monthly query failed'],500);
        }
    }

    /**
     * Export Monthly Requests report (dispensed requests) to PDF.
     * Query: year, month
     */
    public function exportMonthly(Request $request)
    {
        $year = (int) $request->query('year', now()->year);
        $month = (int) $request->query('month', now()->month);
        $start = sprintf('%04d-%02d-01', $year, $month);
        $end = (new \DateTime($start))->modify('last day of this month')->format('Y-m-d');

        // Data: dispensed requests in month
        $rows = DB::table('breastmilk_requests as r')
            ->join('users as u','r.User_ID','=','u.User_ID')
            ->whereNotNull('r.dispensed_at')
            ->whereBetween('r.dispensed_at', ["$start 00:00:00", "$end 23:59:59"]) 
            ->orderBy('r.dispensed_at')
            ->get(['u.Full_Name','r.decided_total_volume','r.dispensed_at']);
        $items = [];
        foreach ($rows as $i => $r) {
            $dt = Carbon::parse($r->dispensed_at)->timezone(config('app.timezone','UTC'));
            $items[] = [
                'no' => $i+1,
                'requestor' => $r->Full_Name,
                'volume' => (int)($r->decided_total_volume ?? 0),
                'date' => $dt->format('M d, Y'),
                'time' => $dt->format('h:i A'),
            ];
        }
        $title = 'Breastmilk Requests Report';
        $range = Carbon::parse($start)->format('M d, Y') . ' to ' . Carbon::parse($end)->format('M d, Y');
        // Prepare logo images as data URIs (only when GD is available). If GD is missing,
        // Dompdf will throw for PNGs; skip images so export still works.
        $logoLeftSrc = null; $logoRightSrc = null;
        $gdOk = extension_loaded('gd') || function_exists('imagecreatefrompng');
        if ($gdOk) {
            try {
                $leftPath = public_path('logo.png');
                if (is_file($leftPath)) {
                    $mime = function_exists('mime_content_type') ? mime_content_type($leftPath) : 'image/png';
                    $logoLeftSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($leftPath));
                }
            } catch (\Throwable $e) {}
            try {
                $rightPath = public_path('hospital logo.png');
                if (is_file($rightPath)) {
                    $mime = function_exists('mime_content_type') ? mime_content_type($rightPath) : 'image/png';
                    $logoRightSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($rightPath));
                }
            } catch (\Throwable $e) {}
        }

        $html = view('admin.reports.monthly-pdf', [
            'title' => $title,
            'range' => $range,
            'sortedBy' => 'dispensed_at',
            'items' => $items,
            'grandTotal' => array_sum(array_column($items,'volume')),
            'month' => $month,
            'year' => $year,
            'logoLeftSrc' => $logoLeftSrc,
            'logoRightSrc' => $logoRightSrc,
        ])->render();

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $fileName = sprintf('monthly-requests-%04d-%02d.pdf', $year, $month);
        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
        ]);
    }

    /**
     * Render a print-friendly Monthly Requests report.
     */
    public function printMonthly(Request $request)
    {
        $year = (int) $request->query('year', now()->year);
        $month = (int) $request->query('month', now()->month);
        $start = sprintf('%04d-%02d-01', $year, $month);
        $end = (new \DateTime($start))->modify('last day of this month')->format('Y-m-d');

        $rows = DB::table('breastmilk_requests as r')
            ->join('users as u','r.User_ID','=','u.User_ID')
            ->whereNotNull('r.dispensed_at')
            ->whereBetween('r.dispensed_at', ["$start 00:00:00", "$end 23:59:59"]) 
            ->orderBy('r.dispensed_at')
            ->get(['u.Full_Name','r.decided_total_volume','r.dispensed_at']);
        $items = [];
        foreach ($rows as $i => $r) {
            $dt = Carbon::parse($r->dispensed_at)->timezone(config('app.timezone','UTC'));
            $items[] = [
                'no' => $i+1,
                'requestor' => $r->Full_Name,
                'volume' => (int)($r->decided_total_volume ?? 0),
                'date' => $dt->format('M d, Y'),
                'time' => $dt->format('h:i A'),
            ];
        }
        return view('admin.reports.monthly-print', [
            'title' => 'Breastmilk Requests Report',
            'range' => Carbon::parse($start)->format('M d, Y') . ' to ' . Carbon::parse($end)->format('M d, Y'),
            'sortedBy' => 'dispensed_at',
            'items' => $items,
            'grandTotal' => array_sum(array_column($items,'volume')),
            'month' => $month,
            'year' => $year,
        ]);
    }

    /**
     * Export Monthly Donations (completed walk-in + home collection) to PDF.
     * Query: year, month
     */
    public function exportMonthlyDonations(Request $request)
    {
        $year = (int) $request->query('year', now()->year);
        $month = (int) $request->query('month', now()->month);
        $start = sprintf('%04d-%02d-01', $year, $month);
        $end = (new \DateTime($start))->modify('last day of this month')->format('Y-m-d');

        $rows = DB::table('donation_history as dh')
            ->join('users as u','dh.User_ID','=','u.User_ID')
            ->where('dh.status','completed')
            ->whereBetween(DB::raw('COALESCE(dh.donation_date, dh.scheduled_date)'), [$start, $end])
            ->orderBy(DB::raw('COALESCE(dh.donation_date, dh.scheduled_date)'))
            ->orderBy(DB::raw('COALESCE(dh.donation_time, dh.scheduled_time)'))
            ->get([
                'u.Full_Name as donor',
                'dh.donation_type as method',
                'dh.number_of_bags as bags',
                'dh.total_volume as volume',
                DB::raw('COALESCE(dh.donation_date, dh.scheduled_date) as ddate'),
                DB::raw('COALESCE(dh.donation_time, dh.scheduled_time) as dtime'),
            ]);
        $items = [];
        foreach ($rows as $i => $r) {
            $date = $r->ddate ? Carbon::parse($r->ddate)->format('M d, Y') : '';
            $time = $r->dtime ? Carbon::createFromFormat('H:i:s', $r->dtime, config('app.timezone'))->format('h:i A') : '';
            $items[] = [
                'no' => $i+1,
                'donor' => $r->donor,
                'method' => $r->method === 'home_collection' ? 'Pickup' : 'Walk-in',
                'bags' => (int)($r->bags ?? 0),
                'volume' => (float)($r->volume ?? 0),
                'date' => $date,
                'time' => $time,
            ];
        }
        $title = 'Breastmilk Donations Report';
        $range = Carbon::parse($start)->format('M d, Y') . ' to ' . Carbon::parse($end)->format('M d, Y');
        // Logos to base64 (only if GD is available)
        $logoLeftSrc = null; $logoRightSrc = null;
        $gdOk = extension_loaded('gd') || function_exists('imagecreatefrompng');
        if ($gdOk) {
            foreach ([["logo.png", 'logoLeftSrc'], ['hospital logo.png','logoRightSrc']] as $def) {
                $path = public_path($def[0]);
                if (is_file($path)) {
                    $mime = function_exists('mime_content_type') ? mime_content_type($path) : 'image/png';
                    ${$def[1]} = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
                }
            }
        }
        $html = view('admin.reports.monthly-donations-pdf', [
            'title' => $title,
            'range' => $range,
            'items' => $items,
            'grandTotalBags' => array_sum(array_column($items,'bags')),
            'grandTotalVolume' => array_sum(array_column($items,'volume')),
            'month' => $month,
            'year' => $year,
            'logoLeftSrc' => $logoLeftSrc,
            'logoRightSrc' => $logoRightSrc,
        ])->render();

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');
        $pdf = new Dompdf($options);
        $pdf->loadHtml($html);
        $pdf->setPaper('A4','portrait');
        $pdf->render();
        $fileName = sprintf('monthly-donations-%04d-%02d.pdf', $year, $month);
        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
        ]);
    }

    /**
     * Print-friendly Monthly Donations report.
     */
    public function printMonthlyDonations(Request $request)
    {
        $year = (int) $request->query('year', now()->year);
        $month = (int) $request->query('month', now()->month);
        $start = sprintf('%04d-%02d-01', $year, $month);
        $end = (new \DateTime($start))->modify('last day of this month')->format('Y-m-d');

        $rows = DB::table('donation_history as dh')
            ->join('users as u','dh.User_ID','=','u.User_ID')
            ->where('dh.status','completed')
            ->whereBetween(DB::raw('COALESCE(dh.donation_date, dh.scheduled_date)'), [$start, $end])
            ->orderBy(DB::raw('COALESCE(dh.donation_date, dh.scheduled_date)'))
            ->orderBy(DB::raw('COALESCE(dh.donation_time, dh.scheduled_time)'))
            ->get([
                'u.Full_Name as donor',
                'dh.donation_type as method',
                'dh.number_of_bags as bags',
                'dh.total_volume as volume',
                DB::raw('COALESCE(dh.donation_date, dh.scheduled_date) as ddate'),
                DB::raw('COALESCE(dh.donation_time, dh.scheduled_time) as dtime'),
            ]);
        $items = [];
        foreach ($rows as $i => $r) {
            $date = $r->ddate ? Carbon::parse($r->ddate)->format('M d, Y') : '';
            $time = $r->dtime ? Carbon::createFromFormat('H:i:s', $r->dtime, config('app.timezone'))->format('h:i A') : '';
            $items[] = [
                'no' => $i+1,
                'donor' => $r->donor,
                'method' => $r->method === 'home_collection' ? 'Pickup' : 'Walk-in',
                'bags' => (int)($r->bags ?? 0),
                'volume' => (float)($r->volume ?? 0),
                'date' => $date,
                'time' => $time,
            ];
        }
        return view('admin.reports.monthly-donations-print', [
            'title' => 'Breastmilk Donations Report',
            'range' => Carbon::parse($start)->format('M d, Y') . ' to ' . Carbon::parse($end)->format('M d, Y'),
            'items' => $items,
            'grandTotalBags' => array_sum(array_column($items,'bags')),
            'grandTotalVolume' => array_sum(array_column($items,'volume')),
            'month' => $month,
            'year' => $year,
        ]);
    }

    /**
     * Export Monthly Inventory to PDF with three sections: Unpasteurized, Pasteurized, Dispensed.
     */
    public function exportMonthlyInventory(Request $request)
    {
        $year = (int) $request->query('year', now()->year);
        $month = (int) $request->query('month', now()->month);
        $start = sprintf('%04d-%02d-01', $year, $month);
        $end = (new \DateTime($start))->modify('last day of this month')->format('Y-m-d');

        // Unpasteurized
        $unpRows = DB::table('unpasteurized_inventory as ui')
            ->leftJoin('users as u','ui.User_ID','=','u.User_ID')
            ->whereBetween('ui.date_received', [$start,$end])
            ->orderBy('ui.date_received')->orderBy('ui.time_received')
            ->get(['ui.id','u.Full_Name as donor','ui.number_of_bags','ui.total_volume','ui.date_received','ui.time_received']);
        $unpasteurized = [];
        foreach ($unpRows as $i=>$r){
            $unpasteurized[] = [
                'no'=>$i+1,
                'donor'=>$r->donor ?? 'Unknown',
                'bags'=>(int)($r->number_of_bags ?? 0),
                'volume'=>(float)($r->total_volume ?? 0),
                'date'=>$r->date_received ? Carbon::parse($r->date_received)->format('M d, Y') : '',
                'time'=>$r->time_received ? Carbon::createFromFormat('H:i:s', $r->time_received, config('app.timezone'))->format('h:i A') : '',
            ];
        }

        // Pasteurized
        $pasRows = DB::table('pasteurized_inventory as pi')
            ->whereBetween('pi.date_pasteurized', [$start,$end])
            ->where('pi.total_volume','>',0)
            ->orderBy('pi.date_pasteurized')->orderBy('pi.time_pasteurized')
            ->get(['pi.id','pi.batch_number','pi.number_of_bags','pi.total_volume','pi.date_pasteurized','pi.time_pasteurized']);
        $pasteurized = [];
        foreach ($pasRows as $i=>$r){
            $pasteurized[] = [
                'no'=>$i+1,
                'batch'=>$r->batch_number,
                'bags'=>(int)($r->number_of_bags ?? 0),
                'volume'=>(float)($r->total_volume ?? 0),
                'date'=>$r->date_pasteurized ? Carbon::parse($r->date_pasteurized)->format('M d, Y') : '',
                'time'=>$r->time_pasteurized ? Carbon::createFromFormat('H:i:s', $r->time_pasteurized, config('app.timezone'))->format('h:i A') : '',
            ];
        }

        // Dispensed
        $disRows = DB::table('dispensed_records as dr')
            ->leftJoin('pasteurized_inventory as pi','dr.pasteurized_id','=','pi.id')
            ->whereBetween('dr.date_dispensed', [$start,$end])
            ->orderBy('dr.date_dispensed')->orderBy('dr.time_dispensed')
            ->get(['dr.id','dr.guardian_name','dr.recipient_name','dr.volume','pi.batch_number','dr.date_dispensed','dr.time_dispensed']);
        $dispensed = [];
        foreach ($disRows as $i=>$r){
            $dispensed[] = [
                'no'=>$i+1,
                'guardian'=>$r->guardian_name,
                'recipient'=>$r->recipient_name,
                'batch'=>$r->batch_number,
                'volume'=>(float)($r->volume ?? 0),
                'date'=>$r->date_dispensed ? Carbon::parse($r->date_dispensed)->format('M d, Y') : '',
                'time'=>$r->time_dispensed ? Carbon::createFromFormat('H:i:s', $r->time_dispensed, config('app.timezone'))->format('h:i A') : '',
            ];
        }

        $title = 'Breastmilk Inventory Report';
        $range = Carbon::parse($start)->format('M d, Y') . ' to ' . Carbon::parse($end)->format('M d, Y');
        // Logos (only if GD is available)
        $logoLeftSrc = null; $logoRightSrc = null;
        $gdOk = extension_loaded('gd') || function_exists('imagecreatefrompng');
        if ($gdOk) {
            foreach ([["logo.png", 'logoLeftSrc'], ['hospital logo.png','logoRightSrc']] as $def) {
                $path = public_path($def[0]);
                if (is_file($path)) {
                    $mime = function_exists('mime_content_type') ? mime_content_type($path) : 'image/png';
                    ${$def[1]} = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
                }
            }
        }
        $html = view('admin.reports.monthly-inventory-pdf', [
            'title'=>$title,
            'range'=>$range,
            'unpasteurized'=>$unpasteurized,
            'pasteurized'=>$pasteurized,
            'dispensed'=>$dispensed,
            'totals'=>[
                'unpasteurized'=>array_sum(array_column($unpasteurized,'volume')),
                'pasteurized'=>array_sum(array_column($pasteurized,'volume')),
                'dispensed'=>array_sum(array_column($dispensed,'volume')),
            ],
            'logoLeftSrc'=>$logoLeftSrc,
            'logoRightSrc'=>$logoRightSrc,
            'month'=>$month,
            'year'=>$year,
        ])->render();

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');
        $pdf = new Dompdf($options);
        $pdf->loadHtml($html);
        $pdf->setPaper('A4','portrait');
        $pdf->render();
        $fileName = sprintf('monthly-inventory-%04d-%02d.pdf', $year, $month);
        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
        ]);
    }

    /**
     * Print-friendly Monthly Inventory with three sections.
     */
    public function printMonthlyInventory(Request $request)
    {
        $year = (int) $request->query('year', now()->year);
        $month = (int) $request->query('month', now()->month);
        $start = sprintf('%04d-%02d-01', $year, $month);
        $end = (new \DateTime($start))->format('Y-m-t');

        // Reuse export query logic
        $unpRows = DB::table('unpasteurized_inventory as ui')
            ->leftJoin('users as u','ui.User_ID','=','u.User_ID')
            ->whereBetween('ui.date_received', [$start,$end])
            ->orderBy('ui.date_received')->orderBy('ui.time_received')
            ->get(['ui.id','u.Full_Name as donor','ui.number_of_bags','ui.total_volume','ui.date_received','ui.time_received']);
        $unpasteurized = [];
        foreach ($unpRows as $i=>$r){
            $unpasteurized[] = [
                'no'=>$i+1,
                'donor'=>$r->donor ?? 'Unknown',
                'bags'=>(int)($r->number_of_bags ?? 0),
                'volume'=>(float)($r->total_volume ?? 0),
                'date'=>$r->date_received ? Carbon::parse($r->date_received)->format('M d, Y') : '',
                'time'=>$r->time_received ? Carbon::createFromFormat('H:i:s', $r->time_received, config('app.timezone'))->format('h:i A') : '',
            ];
        }
        $pasRows = DB::table('pasteurized_inventory as pi')
            ->whereBetween('pi.date_pasteurized', [$start,$end])
            ->where('pi.total_volume','>',0)
            ->orderBy('pi.date_pasteurized')->orderBy('pi.time_pasteurized')
            ->get(['pi.id','pi.batch_number','pi.number_of_bags','pi.total_volume','pi.date_pasteurized','pi.time_pasteurized']);
        $pasteurized = [];
        foreach ($pasRows as $i=>$r){
            $pasteurized[] = [
                'no'=>$i+1,
                'batch'=>$r->batch_number,
                'bags'=>(int)($r->number_of_bags ?? 0),
                'volume'=>(float)($r->total_volume ?? 0),
                'date'=>$r->date_pasteurized ? Carbon::parse($r->date_pasteurized)->format('M d, Y') : '',
                'time'=>$r->time_pasteurized ? Carbon::createFromFormat('H:i:s', $r->time_pasteurized, config('app.timezone'))->format('h:i A') : '',
            ];
        }
        $disRows = DB::table('dispensed_records as dr')
            ->leftJoin('pasteurized_inventory as pi','dr.pasteurized_id','=','pi.id')
            ->whereBetween('dr.date_dispensed', [$start,$end])
            ->orderBy('dr.date_dispensed')->orderBy('dr.time_dispensed')
            ->get(['dr.id','dr.guardian_name','dr.recipient_name','dr.volume','pi.batch_number','dr.date_dispensed','dr.time_dispensed']);
        $dispensed = [];
        foreach ($disRows as $i=>$r){
            $dispensed[] = [
                'no'=>$i+1,
                'guardian'=>$r->guardian_name,
                'recipient'=>$r->recipient_name,
                'batch'=>$r->batch_number,
                'volume'=>(float)($r->volume ?? 0),
                'date'=>$r->date_dispensed ? Carbon::parse($r->date_dispensed)->format('M d, Y') : '',
                'time'=>$r->time_dispensed ? Carbon::createFromFormat('H:i:s', $r->time_dispensed, config('app.timezone'))->format('h:i A') : '',
            ];
        }
        return view('admin.reports.monthly-inventory-print', [
            'title'=>'Breastmilk Inventory Report',
            'range'=>Carbon::parse($start)->format('M d, Y') . ' to ' . Carbon::parse($end)->format('M d, Y'),
            'unpasteurized'=>$unpasteurized,
            'pasteurized'=>$pasteurized,
            'dispensed'=>$dispensed,
            'totals'=>[
                'unpasteurized'=>array_sum(array_column($unpasteurized,'volume')),
                'pasteurized'=>array_sum(array_column($pasteurized,'volume')),
                'dispensed'=>array_sum(array_column($dispensed,'volume')),
            ],
            'month'=>$month,
            'year'=>$year,
        ]);
    }

    // Admin utility: Backfill missing unpasteurized inventory rows for completed donations
    public function backfillUnpasteurized()
    {
        try {
            // Require admin session
            if (!session('admin_id')) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }
            // Find completed donations with no corresponding unpasteurized record
            $missing = DB::table('donation_history as dh')
                ->leftJoin('unpasteurized_inventory as ui', 'ui.donation_id', '=', 'dh.id')
                ->where(function($q){
                    $q->where('dh.status', 'completed')
                      ->orWhereNotNull('dh.validated_at');
                })
                ->whereNull('ui.id')
                ->select('dh.id','dh.User_ID','dh.number_of_bags','dh.total_volume','dh.donation_date','dh.donation_time','dh.scheduled_date','dh.scheduled_time')
                ->get();

            $inserted = 0;
            foreach ($missing as $row) {
                // Use insertOrIgnore for safety if unique constraint exists
                $res = DB::table('unpasteurized_inventory')->insertOrIgnore([
                    'donation_id'   => $row->id,
                    'User_ID'       => $row->User_ID,
                    'number_of_bags'=> $row->number_of_bags,
                    'total_volume'  => $row->total_volume,
                    'date_received' => $row->donation_date ?: $row->scheduled_date,
                    'time_received' => $row->donation_time ?: $row->scheduled_time,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
                if ($res) { $inserted++; }
            }

            return response()->json(['success' => true, 'inserted' => $inserted, 'checked' => $missing->count()]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Backfill failed: '.$e->getMessage()], 500);
        }
    }
}
