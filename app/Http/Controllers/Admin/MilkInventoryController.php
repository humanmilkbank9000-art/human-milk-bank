<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MilkInventoryController extends Controller
{
    public function index()
    {
        // Note: Auto-backfill removed to prevent reappearing items after pasteurization.

        // Preload Unpasteurized
        $unpasteurized = DB::table('unpasteurized_inventory as ui')
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

        // Preload Pasteurized
        $pasteurized = DB::table('pasteurized_inventory as pi')
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

        // Preload Dispensed
        $dispensed = DB::table('dispensed_records as dr')
            ->leftJoin('unpasteurized_inventory as ui','dr.unpasteurized_id','=','ui.id')
            ->leftJoin('donation_history as dh','ui.donation_id','=','dh.id')
            ->leftJoin('users as u','dh.User_ID','=','u.User_ID')
            ->leftJoin('pasteurized_inventory as pi','dr.pasteurized_id','=','pi.id')
            ->select([
                'dr.id as Dispense_ID',
                'dr.guardian_name as Guardian_Name',
                'dr.recipient_name as Recipient_Name',
                'dr.volume as Volume',
                'u.Full_Name as Donor_Name',
                'pi.batch_number as Batch_Number',
                'dr.date_dispensed as Date',
                'dr.time_dispensed as Time'
            ])
            ->orderBy('dr.date_dispensed','desc')
            ->orderBy('dr.time_dispensed','desc')
            ->get();

        return view('admin.milk-inventory', [
            'unpasteurized' => $unpasteurized,
            'pasteurized' => $pasteurized,
            'dispensed' => $dispensed,
        ]);
    }

    /**
     * Convert an unpasteurized inventory entry into a pasteurized batch.
     */
    public function pasteurize(Request $request)
    {
        if (!session('admin_id')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $data = $request->validate([
            'unpasteurized_id' => 'required|integer|exists:unpasteurized_inventory,id',
            'batch_number' => 'required|string|max:100',
        ]);

        try {
            $ui = DB::table('unpasteurized_inventory')->where('id', $data['unpasteurized_id'])->first();
            if (!$ui) return response()->json(['success'=>false,'message'=>'Unpasteurized record not found'],404);

            // If batch exists, accumulate numbers; otherwise create it
            $existing = DB::table('pasteurized_inventory')->where('batch_number', $data['batch_number'])->first();
            if ($existing) {
                DB::table('pasteurized_inventory')->where('id', $existing->id)->update([
                    'number_of_bags' => (int)($existing->number_of_bags ?? 0) + (int)($ui->number_of_bags ?? 0),
                    'total_volume'   => (float)($existing->total_volume ?? 0) + (float)($ui->total_volume ?? 0),
                    'updated_at'     => now(),
                ]);
            } else {
                DB::table('pasteurized_inventory')->insert([
                    'unpasteurized_id' => $ui->id,
                    'batch_number' => $data['batch_number'],
                    'number_of_bags' => $ui->number_of_bags,
                    'total_volume' => $ui->total_volume,
                    'date_pasteurized' => now()->toDateString(),
                    'time_pasteurized' => now()->format('H:i:s'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Remove the source unpasteurized row so it disappears from list
            DB::table('unpasteurized_inventory')->where('id', $ui->id)->delete();

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Failed to pasteurize: '.$e->getMessage()], 500);
        }
    }

    // --- Batch Manager APIs ---
    public function listBatches()
    {
        if (!session('admin_id')) return response()->json(['success'=>false,'message'=>'Unauthorized'],401);
        $rows = DB::table('pasteurized_inventory')
            ->select('batch_number', DB::raw('SUM(total_volume) as total_volume'), DB::raw('COUNT(*) as items_count'))
            ->groupBy('batch_number')
            ->havingRaw('SUM(total_volume) > 0')
            ->orderBy('batch_number','desc')
            ->get();
        return response()->json(['success'=>true,'data'=>$rows]);
    }
    public function getBatchItems(Request $request)
    {
        if (!session('admin_id')) return response()->json(['success'=>false,'message'=>'Unauthorized'],401);
        $batch = trim((string)$request->query('batch'));
        if($batch==='') return response()->json(['success'=>true,'data'=>[]]);
        $rows = DB::table('pasteurized_inventory as pi')
            ->leftJoin('unpasteurized_inventory as ui','pi.unpasteurized_id','=','ui.id')
            ->leftJoin('donation_history as dh','ui.donation_id','=','dh.id')
            ->leftJoin('users as u','dh.User_ID','=','u.User_ID')
            ->select([
                'pi.id as Pasteurized_ID',
                'u.Full_Name as Donor_Name',
                'pi.number_of_bags as Number_of_Bags',
                'pi.total_volume as Total_Volume',
                'pi.date_pasteurized as Date_Pasteurized',
                'pi.time_pasteurized as Time_Pasteurized',
            ])
            ->where('pi.batch_number',$batch)
            ->orderBy('pi.created_at','desc')
            ->get();
        return response()->json(['success'=>true,'data'=>$rows]);
    }
    public function addItemsToBatch(Request $request)
    {
        if (!session('admin_id')) return response()->json(['success'=>false,'message'=>'Unauthorized'],401);
        $data = $request->validate([
            'batch_number' => 'required|string|max:100',
            'unpasteurized_ids' => 'required|array',
            'unpasteurized_ids.*' => 'integer|exists:unpasteurized_inventory,id'
        ]);
        try {
            $batch = $data['batch_number'];
            $ids = array_unique(array_map('intval', $data['unpasteurized_ids']));
            $rows = DB::table('unpasteurized_inventory')->whereIn('id', $ids)->get();
            foreach ($rows as $ui) {
                $existing = DB::table('pasteurized_inventory')->where('batch_number', $batch)->first();
                if ($existing) {
                    DB::table('pasteurized_inventory')->where('id', $existing->id)->update([
                        'number_of_bags' => (int)($existing->number_of_bags ?? 0) + (int)($ui->number_of_bags ?? 0),
                        'total_volume'   => (float)($existing->total_volume ?? 0) + (float)($ui->total_volume ?? 0),
                        'updated_at'     => now(),
                    ]);
                } else {
                    DB::table('pasteurized_inventory')->insert([
                        'unpasteurized_id' => $ui->id,
                        'batch_number' => $batch,
                        'number_of_bags' => $ui->number_of_bags,
                        'total_volume' => $ui->total_volume,
                        'date_pasteurized' => now()->toDateString(),
                        'time_pasteurized' => now()->format('H:i:s'),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                // Remove each consumed unpasteurized source row
                DB::table('unpasteurized_inventory')->where('id', $ui->id)->delete();
            }
            return response()->json(['success'=>true]);
        } catch (\Throwable $e) {
            return response()->json(['success'=>false,'message'=>'Failed to add items: '.$e->getMessage()],500);
        }
    }
    public function removeBatchItem(int $id)
    {
        if (!session('admin_id')) return response()->json(['success'=>false,'message'=>'Unauthorized'],401);
        try {
            $row = DB::table('pasteurized_inventory')->where('id',$id)->first();
            if(!$row) return response()->json(['success'=>false,'message'=>'Item not found'],404);
            DB::table('pasteurized_inventory')->where('id',$id)->delete();
            return response()->json(['success'=>true]);
        } catch (\Throwable $e) {
            return response()->json(['success'=>false,'message'=>'Failed to remove: '.$e->getMessage()],500);
        }
    }
}
