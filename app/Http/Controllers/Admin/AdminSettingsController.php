<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

class AdminSettingsController extends Controller
{
    /**
     * Display the admin settings page.
     */
    public function index()
    {
        // Check if admin is authenticated
        if (!session('admin_id')) {
            return redirect()->route('admin.login');
        }

        // Get current admin data
        $admin = DB::table('admins')->where('Admin_ID', session('admin_id'))->first();
        
        if (!$admin) {
            return redirect()->route('admin.login');
        }

        return view('admin.settings', compact('admin'));
    }

    /**
     * Update the admin password.
     */
    public function updatePassword(Request $request)
    {
        // Check if admin is authenticated
        if (!session('admin_id')) {
            return redirect()->route('admin.login');
        }

        // Validate the request
        $request->validate([
            'current_password' => 'required',
            'new_password' => [
                'required',
                'confirmed',
                Password::min(8)
            ],
        ], [
            'new_password.confirmed' => 'The new password confirmation does not match.',
            'new_password.min' => 'The new password must be at least 8 characters.',
        ]);

        try {
            // Get the current admin
            $adminId = session('admin_id');
            $admin = DB::table('admins')->where('Admin_ID', $adminId)->first();

            if (!$admin) {
                return back()->with('error', 'Admin not found.');
            }

            // Verify current password
            if (!Hash::check($request->current_password, $admin->Password)) {
                return back()
                    ->withErrors(['current_password' => 'The current password is incorrect.'])
                    ->withInput();
            }

            // Check if new password is the same as current password
            if (Hash::check($request->new_password, $admin->Password)) {
                return back()
                    ->withErrors(['new_password' => 'The new password must be different from your current password.'])
                    ->withInput();
            }

            // Update the password
            $updated = DB::table('admins')
                ->where('Admin_ID', $adminId)
                ->update([
                    'Password' => Hash::make($request->new_password),
                    'updated_at' => now(),
                ]);

            if ($updated) {
                // Log the password change
                $this->logPasswordChange($adminId);

                return back()->with('success', 'Password updated successfully!');
            } else {
                return back()->with('error', 'Failed to update password. Please try again.');
            }

        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Admin password update failed: ' . $e->getMessage(), [
                'admin_id' => session('admin_id'),
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'An error occurred while updating your password. Please try again.');
        }
    }

    /**
     * Update the admin username.
     */
    public function updateUsername(Request $request)
    {
        // Check if admin is authenticated
        if (!session('admin_id')) {
            return redirect()->route('admin.login');
        }

        // Validate the request
        $request->validate([
            'username' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'regex:/^[a-zA-Z0-9_]+$/',
                'unique:admins,username,' . session('admin_id') . ',Admin_ID'
            ],
        ], [
            'username.required' => 'Username is required.',
            'username.min' => 'Username must be at least 3 characters.',
            'username.max' => 'Username cannot exceed 50 characters.',
            'username.regex' => 'Username can only contain letters, numbers, and underscores.',
            'username.unique' => 'This username is already taken.',
        ]);

        try {
            // Get the current admin
            $adminId = session('admin_id');
            $admin = DB::table('admins')->where('Admin_ID', $adminId)->first();

            if (!$admin) {
                return back()->with('error', 'Admin not found.');
            }

            // Check if new username is the same as current username
            if (strtolower($request->username) === strtolower($admin->username)) {
                return back()
                    ->withErrors(['username' => 'The new username must be different from your current username.'])
                    ->withInput();
            }

            // Update the username
            $updated = DB::table('admins')
                ->where('Admin_ID', $adminId)
                ->update([
                    'username' => $request->username,
                    'updated_at' => now(),
                ]);

            if ($updated) {
                // Log the username change
                $this->logUsernameChange($adminId, $admin->username, $request->username);

                return back()->with('success', 'Username updated successfully!');
            } else {
                return back()->with('error', 'Failed to update username. Please try again.');
            }

        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Admin username update failed: ' . $e->getMessage(), [
                'admin_id' => session('admin_id'),
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'An error occurred while updating your username. Please try again.');
        }
    }

    /**
     * Log password change activity for security purposes.
     */
    private function logPasswordChange($adminId)
    {
        try {
            // Log to Laravel's log files
            Log::info('Admin password changed', [
                'admin_id' => $adminId,
                'timestamp' => now()->toDateTimeString(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

        } catch (\Exception $e) {
            // Fail silently for logging - don't break the main functionality
            Log::error('Failed to log password change: ' . $e->getMessage());
        }
    }

    /**
     * Log username change activity for security purposes.
     */
    private function logUsernameChange($adminId, $oldUsername, $newUsername)
    {
        try {
            // Log to Laravel's log files
            Log::info('Admin username changed', [
                'admin_id' => $adminId,
                'old_username' => $oldUsername,
                'new_username' => $newUsername,
                'timestamp' => now()->toDateTimeString(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

        } catch (\Exception $e) {
            // Fail silently for logging - don't break the main functionality
            Log::error('Failed to log username change: ' . $e->getMessage());
        }
    }
}