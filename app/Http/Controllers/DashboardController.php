<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Dashboard Controller
 *
 * Handles user dashboard operations including profile management,
 * notifications, and user data retrieval.
 */
class DashboardController extends Controller
{
    /**
     * Session key for user ID.
     */
    private const SESSION_USER_ID = 'user_id';

    /**
     * Database table names.
     */
    private const USERS_TABLE = 'users';
    private const NOTIFICATIONS_TABLE = 'notifications';

    /**
     * Error messages.
     */
    private const ERROR_UNAUTHORIZED = 'Unauthorized';
    private const ERROR_USER_NOT_FOUND = 'User not found';
    private const ERROR_LOGIN_REQUIRED = 'Please login to access the dashboard.';
    private const ERROR_FETCH_NOTIFICATIONS = 'An error occurred while fetching notifications';
    private const ERROR_UPDATE_NOTIFICATION = 'An error occurred while updating notification';

    /**
     * Display the user dashboard.
     */
    public function dashboard(): View|RedirectResponse
    {
        if (!$this->isUserAuthenticated()) {
            return redirect()->route('user-login')
                ->with('error', self::ERROR_LOGIN_REQUIRED);
        }

        $userAddress = $this->getUserAddress();

        // Preload user's pending donation requests (for instant render without client fetch)
        $userId = $this->getCurrentUserId();
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

        return view('user.dashboard', compact('userAddress', 'pendingRequests'));
    }

    /**
     * Get authenticated user's profile details for auto-fill.
     */
    public function getProfile(): JsonResponse
    {
        if (!$this->isUserAuthenticated()) {
            return $this->unauthorizedResponse();
        }

        $user = $this->findUserById($this->getCurrentUserId());
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => self::ERROR_USER_NOT_FOUND,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'user' => [
                'full_name' => $user->Full_Name,
                'date_of_birth' => $user->Date_Of_Birth,
                'age' => $user->Age,
                'sex' => $user->Sex,
                'address' => $user->Address,
                'contact_number' => $user->Contact_Number,
            ],
        ]);
    }

    /**
     * Get user notifications.
     */
    public function getUserNotifications(): JsonResponse
    {
        if (!$this->isUserAuthenticated()) {
            return $this->unauthorizedResponse();
        }

        try {
            $notifications = $this->fetchUserNotifications($this->getCurrentUserId());

            return response()->json([
                'success' => true,
                'data' => $notifications,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => self::ERROR_FETCH_NOTIFICATIONS,
            ], 500);
        }
    }

    /**
     * Mark user notification as read.
     */
    public function markUserNotificationAsRead(int $notificationId): JsonResponse
    {
        if (!$this->isUserAuthenticated()) {
            return $this->unauthorizedResponse();
        }

        try {
            $this->updateNotificationReadStatus($notificationId, $this->getCurrentUserId());

            return response()->json(['success' => true]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => self::ERROR_UPDATE_NOTIFICATION,
            ], 500);
        }
    }

    /**
     * Delete user notification.
     */
    public function deleteUserNotification(int $notificationId): JsonResponse
    {
        if (!$this->isUserAuthenticated()) {
            return $this->unauthorizedResponse();
        }

        try {
            $this->deleteNotification($notificationId, $this->getCurrentUserId());

            return response()->json(['success' => true]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete notification',
            ], 500);
        }
    }

    /**
     * Get user address.
     */
    public function getUserAddress(): JsonResponse|string
    {
        if (!$this->isUserAuthenticated()) {
            // For dashboard method, return empty string
            if (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'] === 'dashboard') {
                return '';
            }
            return $this->unauthorizedResponse();
        }

        $user = $this->findUserById($this->getCurrentUserId());
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => self::ERROR_USER_NOT_FOUND,
            ], 404);
        }

        // For dashboard method, return address string
        if (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'] === 'dashboard') {
            return $user->Address ?? '';
        }

        return response()->json([
            'success' => true,
            'address' => $user->Address,
        ]);
    }

    /**
     * Check if user is authenticated.
     */
    private function isUserAuthenticated(): bool
    {
        return (bool) session(self::SESSION_USER_ID);
    }

    /**
     * Get current user ID from session.
     */
    private function getCurrentUserId(): int
    {
        return (int) session(self::SESSION_USER_ID);
    }

    /**
     * Find user by ID.
     */
    private function findUserById(int $userId): ?object
    {
        return DB::table(self::USERS_TABLE)
            ->where('User_ID', $userId)
            ->first();
    }

    /**
     * Fetch user notifications.
     */
    private function fetchUserNotifications(int $userId): object
    {
        return DB::table(self::NOTIFICATIONS_TABLE)
            ->where('User_ID', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Update notification read status.
     */
    private function updateNotificationReadStatus(int $notificationId, int $userId): void
    {
        DB::table(self::NOTIFICATIONS_TABLE)
            ->where('id', $notificationId)
            ->where('User_ID', $userId) // Ensure user can only mark their own notifications
            ->update(['is_read' => true]);
    }

    /**
     * Delete notification.
     */
    private function deleteNotification(int $notificationId, int $userId): void
    {
        DB::table(self::NOTIFICATIONS_TABLE)
            ->where('id', $notificationId)
            ->where('User_ID', $userId) // Ensure user can only delete their own notifications
            ->delete();
    }

    /**
     * Return unauthorized response.
     */
    private function unauthorizedResponse(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => self::ERROR_UNAUTHORIZED,
        ], 401);
    }
}
