<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminLoginController extends Controller
{
    /**
     * NEW FLOW: Credentials first, then PIN.
     * showLogin is always accessible (no prior PIN requirement).
     */
    public function showLogin()
    {
        // If fully logged in already, go dashboard
        if (session('admin_id')) {
            return redirect('/admin/dashboard');
        }
        return view('admin.login');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $admin = Admin::where('username', $request->username)->first();

        if ($admin && Hash::check($request->password, $admin->Password)) {
            // Store a pre-auth session flag; final login awaits PIN verification.
            session([
                'admin_pre_id' => $admin->Admin_ID,
                'admin_pre_name' => $admin->Full_Name,
                'admin_pre_started' => now()->toISOString(),
            ]);

            // Clear any previous finalized session data (safety)
            session()->forget(['admin_id','admin_name','admin_type']);

            return redirect()->route('admin.pin')->with('success', 'Credentials accepted. Enter PIN to finish login.');
        }

        return back()->withErrors(['username' => 'The provided credentials do not match our records.'])->withInput($request->only('username'));
    }

    /**
     * Show PIN screen AFTER successful credentials.
     */
    public function showPinVerification()
    {
        if (!session('admin_pre_id')) {
            return redirect()->route('admin.login')->with('error','Please login first.');
        }
        return view('admin.pin-verification');
    }

    /**
     * Verify PIN and finalize login (promote pre-auth to full session).
     */
    public function verifyPin(Request $request)
    {
        if (!session('admin_pre_id')) {
            return redirect()->route('admin.login')->with('error','Session expired. Please login again.');
        }

        $request->validate([
            'pin' => 'required|string|size:4',
        ]);

        // Simple static PIN for now (original logic retained).
        $validPin = '8080';

        // Basic attempt limiter (session based)
        $attempts = session('admin_pin_attempts', 0);
        if ($attempts >= 5) {
            return back()->withErrors(['pin' => 'Too many attempts. Please login again.']);
        }

        if ($request->pin === $validPin) {
            $adminId = session('admin_pre_id');
            $adminName = session('admin_pre_name');
            // Finalize
            session([
                'admin_id' => $adminId,
                'admin_name' => $adminName,
                'admin_type' => 'admin',
                'admin_verified' => true,
            ]);
            // Cleanup
            session()->forget(['admin_pre_id','admin_pre_name','admin_pin_attempts','admin_pre_started']);
            return redirect('/admin/dashboard')->with('success', 'Welcome back, ' . $adminName . '!');
        }

        session(['admin_pin_attempts' => $attempts + 1]);
        return back()->withErrors(['pin' => 'Invalid PIN code.'])->withInput($request->only('pin'));
    }

    public function logout()
    {
        session()->flush();
        return redirect('/')->with('success', 'You have been successfully logged out.');
    }

    public function clearPinSession()
    {
        session()->flush();
        return redirect('/')->with('success', 'Admin session cleared.');
    }
}
