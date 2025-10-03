<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminAvailabilityController extends Controller
{
    public function updateAvailability(Request $request)
    {
        $days = $request->input('days');
        $startDate = $request->input('start_date');

        foreach ($days as $day => $details) {
            $date = date('Y-m-d', strtotime($startDate . ' + ' . array_search($day, array_keys($days)) . ' days'));

            DB::table('admin_week_availability')->updateOrInsert(
                ['date' => $date],
                [
                    'is_available' => $details['available'],
                    'start_time' => $details['start_time'],
                    'end_time' => $details['end_time'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        return response()->json(['success' => true]);
    }

    public function updateDayAvailability(Request $request)
    {
        try {
            $date = $request->input('date');
            $isAvailable = $request->input('is_available');
            $timeSlots = $request->input('time_slots', []);

            // Update or create the day availability record
            DB::table('admin_week_availability')->updateOrInsert(
                ['date' => $date],
                [
                    'is_available' => $isAvailable,
                    'start_time' => $isAvailable && !empty($timeSlots) ? min($timeSlots) : null,
                    'end_time' => $isAvailable && !empty($timeSlots) ? max($timeSlots) : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // Clear existing time slots for this date
            DB::table('admin_time_slots')->where('date', $date)->delete();

            // Insert new time slots if day is available
            if ($isAvailable && !empty($timeSlots)) {
                $timeSlotData = [];
                foreach ($timeSlots as $timeSlot) {
                    $timeSlotData[] = [
                        'date' => $date,
                        'time_slot' => $timeSlot,
                        'is_available' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                DB::table('admin_time_slots')->insert($timeSlotData);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getDayAvailability(Request $request)
    {
        try {
            $date = $request->input('date');

            $dayAvailability = DB::table('admin_week_availability')
                ->where('date', $date)
                ->first();

            $timeSlots = DB::table('admin_time_slots')
                ->where('date', $date)
                ->where('is_available', true)
                ->pluck('time_slot')
                ->toArray();

            if ($dayAvailability) {
                return response()->json([
                    'success' => true,
                    'availability' => [
                        'is_available' => $dayAvailability->is_available,
                        'time_slots' => $timeSlots
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'availability' => [
                        'is_available' => false,
                        'time_slots' => []
                    ]
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function get()
    {
        try {
            $availability = DB::table('admin_week_availability')
                ->where('is_available', true)
                ->get();

            return response()->json($availability);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
