<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserSettingsController extends Controller
{
    public function index(Request $request)
    {
        if (!session('user_id')) {
            return redirect()->route('user-login')->with('error', 'Please login to access settings.');
        }

        $userId = (int) session('user_id');
        // If this is an AJAX/partial request in future we could return only fragment; current SPA builds HTML itself
        if ($request->ajax() || $request->wantsJson() || $request->query('partial')) {
            // Lightweight confirmation response so caller knows user is authorized
            return response()->json(['success' => true]);
        }

        // Replicate minimal data preload used by dashboard so shell renders consistently
        $userAddress = DB::table('users')->where('User_ID', $userId)->value('Address') ?? '';

        $pendingHistory = DB::table('donation_history')
            ->where('User_ID', $userId)
            ->where('status', 'pending')
            ->select(
                'donation_type',
                'number_of_bags',
                'total_volume',
                'donation_date',
                'donation_time',
                'pickup_address',
                'created_at'
            )
            ->get();

        $pendingWalkIns = DB::table('walk_in_requests')
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->select(
                DB::raw("'walk_in' as donation_type"),
                DB::raw('NULL as number_of_bags'),
                DB::raw('NULL as total_volume'),
                'donation_date',
                'donation_time',
                DB::raw('NULL as pickup_address'),
                'created_at'
            )
            ->get();

        $pendingRequests = $pendingHistory->concat($pendingWalkIns)->sortByDesc('created_at')->values();

        // Return the dashboard shell with a flag to auto-open settings via JS
        return view('user.dashboard', [
            'userAddress' => $userAddress,
            'pendingRequests' => $pendingRequests,
            'autoShowSettings' => true,
        ]);
    }

    public function updatePassword(Request $request)
    {
        if (!session('user_id')) {
            return redirect()->route('user-login')->with('error', 'Unauthorized');
        }

        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $user = DB::table('users')->where('User_ID', session('user_id'))->first();
            if (!$user) {
                return redirect()->back()->with('error', 'User not found.');
            }

            // Password field is stored hashed (assuming bcrypt). If not hashed yet, treat as plain fallback.
            $storedHash = $user->Password;
            $currentPasswordInput = $request->current_password;

            $matches = false;
            if (Hash::needsRehash($storedHash)) {
                // If the stored value is not a valid bcrypt hash (legacy plain), compare directly
                $matches = hash_equals($storedHash, $currentPasswordInput);
            } else {
                $matches = Hash::check($currentPasswordInput, $storedHash);
            }

            if (!$matches) {
                return redirect()->back()->with('error', 'Current password is incorrect.');
            }

            $newHash = Hash::make($request->new_password);
            DB::table('users')->where('User_ID', $user->User_ID)->update([
                'Password' => $newHash,
                'updated_at' => now(),
            ]);

            return redirect()->route('user.settings')->with('success', 'Password updated successfully.');
        } catch (\Exception $e) {
            Log::error('Password update failed', ['user_id' => session('user_id'), 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to update password. Please try again.');
        }
    }
}
