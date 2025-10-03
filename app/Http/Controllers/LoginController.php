<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * Login Controller
 *
 * Handles user authentication including login, logout,
 * and session management for the breast milk donation system.
 */
class LoginController extends Controller
{
    /**
     * Session keys.
     */
    private const SESSION_USER_ID = 'user_id';
    private const SESSION_USER_NAME = 'user_name';
    private const SESSION_USER_TYPE = 'user_type';

    /**
     * Route names.
     */
    private const ROUTE_DASHBOARD = '/dashboard';
    private const ROUTE_HOME = '/';

    /**
     * Validation rules.
     */
    private const VALIDATION_RULES = [
        'contact_number' => 'required|string|regex:/^\\d{11}$/',
        'password' => 'required|string',
    ];

    /**
     * Error messages.
     */
    private const ERROR_INVALID_CREDENTIALS = 'The provided credentials do not match our records.';

    /**
     * Success messages.
     */
    private const SUCCESS_LOGOUT = 'You have been successfully logged out.';

    /**
     * Display the login form.
     */
    public function login(): View
    {
        return view('user.user-login');
    }

    /**
     * Authenticate user login attempt.
     */
    public function authenticate(Request $request): RedirectResponse
    {
        // Normalize contact number to 11-digit local format (e.g., 09XXXXXXXXX)
        $rawInput = (string) $request->input('contact_number');
        $digits = preg_replace('/\D+/', '', $rawInput ?? '');

        // Handle common variants:
        // 1) +639XXXXXXXXX or 639XXXXXXXXX -> 09XXXXXXXXX
        if (str_starts_with($digits, '63') && strlen($digits) === 12) {
            $digits = '0' . substr($digits, 2); // 63 + 9XXXXXXXXX -> 0 + 9XXXXXXXXX
        }

        // 2) 9XXXXXXXXX (10 digits starting with 9) -> 09XXXXXXXXX
        if (strlen($digits) === 10 && str_starts_with($digits, '9')) {
            $digits = '0' . $digits;
        }

        $request->merge([
            'contact_number' => $digits,
        ]);

        $request->validate(
            self::VALIDATION_RULES,
            [
                'contact_number.regex' => 'Contact number must be exactly 11 digits.',
            ]
        );

        $user = $this->findUserByContactNumber($request->contact_number);

        if ($this->isValidUser($user, $request->password)) {
            $this->createUserSession($user);
            return $this->redirectToDashboard($user);
        }

        return $this->redirectBackWithError($request);
    }

    /**
     * Logout the authenticated user.
     */
    public function logout(): RedirectResponse
    {
        $this->clearUserSession();
        
        return redirect(self::ROUTE_HOME)
            ->with('success', self::SUCCESS_LOGOUT);
    }

    /**
     * Find user by contact number.
     */
    private function findUserByContactNumber(string $contactNumber): ?User
    {
        $variants = $this->buildContactNumberVariants($contactNumber);
        return User::whereIn('Contact_Number', $variants)->first();
    }

    /**
     * Build common contact number variants from normalized 11-digit format.
     * Examples from 09XXXXXXXXX → [09XXXXXXXXX, 9XXXXXXXXX, 639XXXXXXXXX, +639XXXXXXXXX]
     */
    private function buildContactNumberVariants(string $normalized): array
    {
        $digits = preg_replace('/\D+/', '', $normalized ?? '');
        $variants = [];

        // Ensure we have something; expect 11-digit 09XXXXXXXXX here
        if (strlen($digits) === 11 && str_starts_with($digits, '0')) {
            $rest = substr($digits, 1); // 9XXXXXXXXX
            $variants[] = $digits;                 // 09XXXXXXXXX
            $variants[] = $rest;                   // 9XXXXXXXXX
            $variants[] = '63' . $rest;            // 639XXXXXXXXX
            $variants[] = '+63' . $rest;           // +639XXXXXXXXX
        } elseif (strlen($digits) === 12 && str_starts_with($digits, '63')) {
            $rest = substr($digits, 2);            // 9XXXXXXXXX
            $variants[] = '0' . $rest;             // 09XXXXXXXXX
            $variants[] = $rest;                   // 9XXXXXXXXX
            $variants[] = $digits;                 // 639XXXXXXXXX
            $variants[] = '+63' . $rest;           // +639XXXXXXXXX
        } elseif (strlen($digits) === 10 && str_starts_with($digits, '9')) {
            $variants[] = '0' . $digits;           // 09XXXXXXXXX
            $variants[] = $digits;                 // 9XXXXXXXXX
            $variants[] = '63' . $digits;          // 639XXXXXXXXX
            $variants[] = '+63' . $digits;         // +639XXXXXXXXX
        } else {
            // Fallback: return as-is
            $variants[] = $digits;
        }

        return array_values(array_unique($variants));
    }

    /**
     * Check if user credentials are valid.
     */
    private function isValidUser(?User $user, string $password): bool
    {
        return $user !== null && Hash::check($password, $user->Password);
    }

    /**
     * Create user session with authentication data.
     */
    private function createUserSession(User $user): void
    {
        session([
            self::SESSION_USER_ID => $user->User_ID,
            self::SESSION_USER_NAME => $user->Full_Name,
            self::SESSION_USER_TYPE => $user->User_Type,
        ]);
    }

    /**
     * Redirect to dashboard with welcome message.
     */
    private function redirectToDashboard(User $user): RedirectResponse
    {
        $welcomeMessage = "Welcome back, {$user->Full_Name}!";
        
        return redirect(self::ROUTE_DASHBOARD)
            ->with('success', $welcomeMessage);
    }

    /**
     * Redirect back with authentication error.
     */
    private function redirectBackWithError(Request $request): RedirectResponse
    {
        return back()
            ->withErrors(['contact_number' => self::ERROR_INVALID_CREDENTIALS])
            ->withInput($request->only('contact_number'));
    }

    /**
     * Clear all user session data.
     */
    private function clearUserSession(): void
    {
        session()->flush();
    }
}
