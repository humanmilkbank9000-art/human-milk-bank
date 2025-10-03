<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BreastmilkRequestController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Sanitize contact number to digits only
            $request->merge([
                'contact_number' => preg_replace('/\D+/', '', (string) $request->input('contact_number')),
            ]);

            // Sanitize recipient name to remove special characters
            $sanitizedName = preg_replace('/[<>=\'"]/', '', $request->input('recipient_name'));
            $request->merge(['recipient_name' => $sanitizedName]);

            // Validate the request (appointment-based; admin decides dispensing values later)
            $request->validate([
                'recipient_name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
                'recipient_dob' => 'required|date|before:today',
                'recipient_weight' => 'required|numeric|min:0',
                'contact_number' => 'required|string|regex:/^\\d{11}$/',
                'scheduled_date' => 'required|date|after_or_equal:today',
                'scheduled_time' => 'required|string',
                'prescription_image' => 'required|file|mimes:jpeg,jpg,png,pdf|max:5120', // 5MB max
                // Optional field; some schemas require NOT NULL, so we'll default below if missing
                'medical_condition' => 'nullable|string|max:1000',
            ], [
                'contact_number.regex' => 'Contact number must be exactly 11 digits.',
                'recipient_name.regex' => 'Recipient name can only contain letters and spaces. Special characters like <, >, =, \', " are not allowed.',
            ]);

            // Get the authenticated user from session
            $userId = session('user_id');
            if (!$userId) {
                return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
            }

            // Handle file upload
            $prescriptionPath = null;
            if ($request->hasFile('prescription_image')) {
                $file = $request->file('prescription_image');
                $fileName = time() . '_' . $userId . '_' . $file->getClientOriginalName();
                $prescriptionPath = $file->storeAs('prescriptions', $fileName, 'public');
            }

            $selectedDate = $request->scheduled_date;
            $selectedTime = $request->scheduled_time;

            // Book inside a transaction to prevent double booking
            $requestId = DB::transaction(function () use ($userId, $request, $prescriptionPath, $selectedDate, $selectedTime) {
                // Lock the specific admin slot row to serialize bookings
                DB::table('admin_time_slots')
                    ->where('date', $selectedDate)
                    ->where('time_slot', $selectedTime)
                    ->lockForUpdate()
                    ->get();

                // Ensure the slot is actually defined as available
                $timeSlotAvailable = DB::table('admin_time_slots')
                    ->where('date', $selectedDate)
                    ->where('time_slot', $selectedTime)
                    ->where('is_available', true)
                    ->exists();
                if (!$timeSlotAvailable) {
                    throw new \RuntimeException('Selected appointment time is no longer available. Please choose another.');
                }

                // Check conflicts across flows
                $conflictWalkIn = DB::table('walk_in_requests')
                    ->where('donation_date', $selectedDate)
                    ->where('donation_time', $selectedTime)
                    ->whereIn('status', ['pending', 'confirmed', 'validated'])
                    ->exists();
                $conflictHistory = DB::table('donation_history')
                    ->where('donation_date', $selectedDate)
                    ->where('donation_time', $selectedTime)
                    ->where('donation_type', 'walk_in')
                    ->whereIn('status', ['pending', 'approved'])
                    ->exists();
                $conflictBm = DB::table('breastmilk_requests')
                    ->where('scheduled_date', $selectedDate)
                    ->where('scheduled_time', $selectedTime)
                    ->whereIn('status', ['pending', 'approved', 'dispensed'])
                    ->exists();
                if ($conflictWalkIn || $conflictHistory || $conflictBm) {
                    throw new \RuntimeException('Sorry, that time was just booked. Please pick another.');
                }

                return DB::table('breastmilk_requests')->insertGetId([
                    'User_ID' => $userId,
                    'recipient_name' => $request->recipient_name,
                    'recipient_dob' => $request->recipient_dob,
                    'recipient_weight' => $request->recipient_weight,
                    'contact_number' => $request->contact_number,
                    // Provide a safe default to satisfy NOT NULL schemas
                    'medical_condition' => $request->input('medical_condition', 'None'),
                    'requested_volume' => null, // Admin decides later
                    'needed_by_date' => null,    // Admin decides later
                    'scheduled_date' => $selectedDate,
                    'scheduled_time' => $selectedTime,
                    'prescription_image_path' => $prescriptionPath,
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });

            // Create notification for admin
            DB::table('notifications')->insert([
                'title' => 'New Breastmilk Request',
                'message' => 'A new breastmilk request has been submitted by ' . session('user_name') . ' for ' . $request->recipient_name,
                'type' => 'breastmilk_request',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Return JSON for AJAX; otherwise redirect back with flash
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Breastmilk request submitted successfully',
                    'request_id' => $requestId,
                ]);
            }
            return redirect()->back()->with('status', 'Breastmilk request submitted successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\RuntimeException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 409);
            }
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while submitting the request: ' . $e->getMessage(),
                ], 500);
            }
            return redirect()->back()->with('error', 'An error occurred while submitting the request.')->withInput();
        }
    }

    // Get user's breastmilk requests
    public function getUserRequests()
    {
        if (!session('user_id')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        try {
            $requests = DB::table('breastmilk_requests')
                ->where('User_ID', session('user_id'))
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json(['success' => true, 'data' => $requests]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while fetching requests'], 500);
        }
    }
}
