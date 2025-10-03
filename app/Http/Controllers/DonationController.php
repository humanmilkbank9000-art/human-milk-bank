<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Helpers\AddressValidation;
use App\Models\WalkInRequest;

class DonationController extends Controller
{
    public function walkIn(Request $request)
    {
        // Check if user is authenticated using session
        if (!session('user_id')) {
            return redirect()->route('user-login')->with('error', 'Please login to submit donation requests.');
        }

        try {
            // Validate the request
            $request->validate([
                'donation_date' => 'required|date|after_or_equal:today',
                'donation_time' => 'required|string',
            ]);

            // Get the authenticated user from session
            $userId = session('user_id');
            if (!$userId) {
                return redirect()->back()->with('error', 'User not authenticated.');
            }

            // Check if user has completed health screening and been accepted
            $healthScreening = DB::table('health_screenings')
                ->where('User_ID', $userId)
                ->where('status', 'accepted')
                ->first();

            if (!$healthScreening) {
                return redirect()->back()->with('error', 'You must complete and be accepted for health screening before donating.');
            }

            // Validate selected date and time
            $selectedDate = $request->donation_date;
            $selectedTime = $request->donation_time;

            // Check if the date is available
            $weekDay = DB::table('admin_week_availability')
                ->where('date', $selectedDate)
                ->where('is_available', true)
                ->first();

            if (!$weekDay) {
                return redirect()->back()->with('error', 'Selected date is not available for donations.');
            }

            // Get user's full name once
            $user = DB::table('users')->where('User_ID', $userId)->first();
            $userName = $user ? $user->Full_Name : 'User';

            // Perform booking inside a transaction to reduce race conditions
            DB::transaction(function () use ($selectedDate, $selectedTime, $userId, $userName) {
                // Lock the specific slot row if available (supported in MySQL/Postgres)
                // This helps serialize concurrent bookings for the same slot
                DB::table('admin_time_slots')
                    ->where('date', $selectedDate)
                    ->where('time_slot', $selectedTime)
                    ->lockForUpdate()
                    ->get();

                // Re-check that the specific time slot is defined as available by admin
                $timeSlotAvailable = DB::table('admin_time_slots')
                    ->where('date', $selectedDate)
                    ->where('time_slot', $selectedTime)
                    ->where('is_available', true)
                    ->exists();

                if (!$timeSlotAvailable) {
                    throw new \RuntimeException('Selected time slot is not available. Please choose a different time.');
                }

                // Reject if any existing non-cancelled booking exists for this slot
                $hasExistingWalkIn = WalkInRequest::where('donation_date', $selectedDate)
                    ->where('donation_time', $selectedTime)
                    ->whereIn('status', ['pending', 'confirmed', 'validated'])
                    ->exists();

                $hasHistoryBooking = DB::table('donation_history')
                    ->where('donation_date', $selectedDate)
                    ->where('donation_time', $selectedTime)
                    ->where('donation_type', 'walk_in')
                    ->whereIn('status', ['pending', 'approved'])
                    ->exists();

                if ($hasExistingWalkIn || $hasHistoryBooking) {
                    throw new \RuntimeException('This time slot has just been booked. Please select a different time.');
                }

                // Create walk-in request
                WalkInRequest::create([
                    'user_id' => $userId,
                    'donor_name' => $userName,
                    'donation_date' => $selectedDate,
                    'donation_time' => $selectedTime,
                    'status' => 'pending'
                ]);
            });

            // Create notification for admin
            DB::table('notifications')->insert([
                'title' => 'New Walk-in Donation Request',
                'message' => 'A new walk-in donation request has been submitted by ' . $userName . ' for ' . date('M d, Y', strtotime($selectedDate)) . ' at ' . $selectedTime,
                'type' => 'walk_in_request',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->route('dashboard')->with('success', 'Walk-in donation request submitted successfully! We will contact you to confirm your appointment.');

        } catch (ValidationException $e) {
            // Return validation errors back to the form with old input
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\RuntimeException $e) {
            // Friendly business-rule error (e.g., slot just taken)
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Walk-in donation error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => session('user_id'),
                'input' => $request->all(),
            ]);
            return redirect()->back()->with('error', 'An error occurred while submitting the donation request. Please try again.');
        }
    }

    public function homeCollection(Request $request)
    {
        // Check if user is authenticated using session
        if (!session('user_id')) {
            return redirect()->route('user-login')->with('error', 'Please login to submit donation requests.');
        }

        try {
            // Sanitize pickup address to remove special characters
            $request->merge([
                'pickup_address' => AddressValidation::sanitize($request->input('pickup_address')),
            ]);

            // Validate the request
            $request->validate([
                'number_of_bags' => 'required|integer|min:1',
                'total_volume' => 'required|numeric|min:1',
                'date_collected' => 'required|date|before_or_equal:today', // Milk was extracted in the past
                'pickup_address' => AddressValidation::getValidationRules(),
            ], [
                'pickup_address.max' => AddressValidation::getValidationMessages()['max'],
                'pickup_address.regex' => AddressValidation::getValidationMessages()['regex'],
            ]);

            // Get the authenticated user from session
            $userId = session('user_id');
            if (!$userId) {
                return redirect()->back()->with('error', 'User not authenticated.');
            }

            // Check if user has completed health screening and been accepted
            $healthScreening = DB::table('health_screenings')
                ->where('User_ID', $userId)
                ->where('status', 'accepted')
                ->first();

            if (!$healthScreening) {
                return redirect()->back()->with('error', 'You must complete and be accepted for health screening before donating.');
            }

            // Get user's full name and address
            $user = DB::table('users')->where('User_ID', $userId)->first();

            // Verify the pickup address matches the user's registered address
            if (trim($request->pickup_address) !== trim($user->Address)) {
                return redirect()->back()->with('error', 'Pickup address must match your registered address. Please contact support if you need to update your address.');
            }

            // Create home collection request (admin will schedule pickup time)
            DB::table('donation_history')->insert([
                'User_ID' => $userId,
                'donation_type' => 'home_collection',
                'number_of_bags' => $request->number_of_bags,
                'total_volume' => $request->total_volume,
                'donation_date' => $request->date_collected, // When milk was extracted
                'donation_time' => '00:00:00', // Placeholder - admin will schedule actual pickup time
                'pickup_address' => $request->pickup_address,
                'status' => 'pending', // Admin will schedule pickup time
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create notification for admin
            DB::table('notifications')->insert([
                'title' => 'New Home Collection Request',
                'message' => 'A new home collection request has been submitted by ' . $user->Full_Name . ' for ' . date('M d, Y', strtotime($request->date_collected)) . '. Please schedule pickup time.',
                'type' => 'home_collection_request',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Redirect to pending requests page
            return redirect()->route('dashboard', ['show' => 'pending'])->with('success', 'Home collection request submitted successfully! Our admin team will contact you to schedule the pickup time.');

        } catch (ValidationException $e) {
            // Return validation errors back to the form with old input
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Home collection request error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => session('user_id'),
                'input' => $request->all(),
            ]);
            return redirect()->back()->with('error', 'An error occurred while submitting the donation request. Please try again.');
        }
    }

    // Get availability for the calendar
    public function getAvailability(Request $request)
    {
        try {
            $month = $request->input('month', date('m'));
            $year = $request->input('year', date('Y'));

            $startDate = new \DateTime("{$year}-{$month}-01");
            $endDate = new \DateTime(date('Y-m-t', $startDate->getTimestamp()));

            // Get all available days from admin_week_availability table
            $weeklyAvailability = DB::table('admin_week_availability')
                ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->where('is_available', true)
                ->get();

            $availability = [];

            foreach ($weeklyAvailability as $day) {
                // Check if there are any available time slots for this day
                $hasTimeSlots = DB::table('admin_time_slots')
                    ->where('date', $day->date)
                    ->where('is_available', true)
                    ->exists();

                if ($hasTimeSlots) {
                    $availability[] = [
                        'date' => $day->date,
                        'is_available' => true
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'availability' => $availability
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while fetching availability: ' . $e->getMessage()]);
        }
    }

    // Get available time slots for a specific date
    public function getAvailableSlots(Request $request)
    {
        try {
            $date = $request->input('date');

            // Check if the date is available
            $weekDay = DB::table('admin_week_availability')
                ->where('date', $date)
                ->where('is_available', true)
                ->first();

            if (!$weekDay) {
                return response()->json(['success' => false, 'message' => 'No availability for this date']);
            }

            // Get admin-defined available time slots for this date
            $availableTimeSlots = DB::table('admin_time_slots')
                ->where('date', $date)
                ->where('is_available', true)
                ->pluck('time_slot')
                ->toArray();

            if (empty($availableTimeSlots)) {
                return response()->json(['success' => false, 'message' => 'No time slots available for this date']);
            }

            // Filter out already booked slots across flows
            $bookedSlotsHistory = DB::table('donation_history')
                ->where('donation_date', $date)
                ->where('donation_type', 'walk_in')
                ->whereIn('status', ['pending', 'approved'])
                ->pluck('donation_time')
                ->toArray();

            // Also exclude slots already taken in walk_in_requests (non-cancelled)
            $bookedSlotsWalkIn = DB::table('walk_in_requests')
                ->where('donation_date', $date)
                ->whereIn('status', ['pending', 'confirmed', 'validated'])
                ->pluck('donation_time')
                ->toArray();

            // Exclude scheduled breastmilk request appointments
            $bookedSlotsBm = DB::table('breastmilk_requests')
                ->where('scheduled_date', $date)
                ->whereNotNull('scheduled_time')
                ->whereIn('status', ['pending', 'approved', 'dispensed'])
                ->pluck('scheduled_time')
                ->toArray();

            $bookedSlots = array_unique(array_merge($bookedSlotsHistory, $bookedSlotsWalkIn, $bookedSlotsBm));
            $availableSlots = array_values(array_diff($availableTimeSlots, $bookedSlots));

            return response()->json([
                'success' => true,
                'slots' => $availableSlots,
                'all' => array_values($availableTimeSlots),
                'unavailable' => array_values($bookedSlots),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while fetching available slots: ' . $e->getMessage()]);
        }
    }

    // Get user's donation history
    public function getUserDonationHistory()
    {
        if (!session('user_id')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        try {
            $donations = DB::table('donation_history')
                ->where('User_ID', session('user_id'))
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json(['success' => true, 'data' => $donations]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while fetching donation history'], 500);
        }
    }

    // Get user's pending donation requests
    public function getPendingRequests()
    {
        if (!session('user_id')) {
            Log::error('getPendingRequests: No user_id in session');
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        try {
            $userId = session('user_id');
            Log::info('getPendingRequests: Fetching for user_id: ' . $userId);

            // Get pending donation requests from donation_history table
            $pendingRequests = DB::table('donation_history')
                ->where('User_ID', $userId)
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->get();

            Log::info('getPendingRequests: Found ' . $pendingRequests->count() . ' donation history requests');

            // Also get pending walk-in requests from walk_in_requests table
            $walkInRequests = DB::table('walk_in_requests')
                ->where('user_id', $userId)
                ->where('status', 'pending')
                ->select('user_id as User_ID', 'donation_date', 'donation_time', 'status', 'created_at', 'updated_at')
                ->selectRaw("'walk_in' as donation_type, null as number_of_bags, null as total_volume, null as pickup_address")
                ->orderBy('created_at', 'desc')
                ->get();

            Log::info('getPendingRequests: Found ' . $walkInRequests->count() . ' walk-in requests');

            // Combine both collections
            $allRequests = $pendingRequests->concat($walkInRequests)->sortByDesc('created_at');

            Log::info('getPendingRequests: Total combined requests: ' . $allRequests->count());

            return response()->json(['success' => true, 'data' => $allRequests->values()]);
        } catch (\Exception $e) {
            Log::error('Error fetching pending requests: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error loading pending requests'], 500);
        }
    }
}
