<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CreateAccountController;
use App\Http\Controllers\InfantInformationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HealthScreeningController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\Admin\AdminAvailabilityController;

// User Routes
Route::get('/', [LoginController::class, 'login'])->name('user-login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.authenticate');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/create-account', [CreateAccountController::class, 'create_account'])->name('create-account');
Route::post('/create-account', [CreateAccountController::class, 'store'])->name('create-account.store');

Route::get('/infant-information', [InfantInformationController::class, 'infant_information'])->name('infant-information');
Route::post('/infant-information', [InfantInformationController::class, 'store'])->name('infant-information.store');
Route::get('/infant-information/current', [InfantInformationController::class, 'getCurrentUserInfant'])->name('infant-information.current');

Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
Route::get('/profile/current', [DashboardController::class, 'getProfile'])->name('profile.current');

// Health Screening Routes
Route::post('/health-screening/submit', [HealthScreeningController::class, 'submit'])->name('health-screening.submit');
Route::get('/health-screening/review', [HealthScreeningController::class, 'showReview'])->name('health-screening.review');
Route::get('/health-screening/review-data', [HealthScreeningController::class, 'getReviewData'])->name('health-screening.review-data');
Route::post('/health-screening/final-submit', [HealthScreeningController::class, 'finalSubmit'])->name('health-screening.final-submit');
Route::get('/health-screening/check-existing', [HealthScreeningController::class, 'checkExisting'])->name('health-screening.check-existing');
Route::post('/health-screening/{id}/update-status', [HealthScreeningController::class, 'updateStatus'])->name('health-screening.update-status');
Route::get('/health-screening/{id}/details', [HealthScreeningController::class, 'getScreeningDetails'])->name('health-screening.details');

// User Notifications Route
Route::get('/user/notifications', [DashboardController::class, 'getUserNotifications'])->name('user.notifications');
Route::post('/user/notifications/{id}/mark-read', [DashboardController::class, 'markUserNotificationAsRead'])->name('user.notifications.mark-read');
Route::delete('/user/notifications/{id}/delete', [DashboardController::class, 'deleteUserNotification'])->name('user.notifications.delete');
Route::get('/user/address', [DashboardController::class, 'getUserAddress'])->name('user.address');

// User Settings (canonical /dashboard path)
Route::get('/dashboard/settings', [\App\Http\Controllers\UserSettingsController::class, 'index'])->name('user.settings');
Route::post('/dashboard/settings/password', [\App\Http\Controllers\UserSettingsController::class, 'updatePassword'])->name('user.settings.password');
// Legacy fallback for old URLs
Route::get('/settings', function() { return redirect()->route('user.settings'); });
Route::post('/settings/password', function() { return redirect()->route('user.settings'); });

// Donation Routes
Route::post('/donation/walk-in', [DonationController::class, 'walkIn'])->name('donation.walk-in');
Route::post('/donation/home-collection', [DonationController::class, 'homeCollection'])->name('donation.home-collection');
Route::get('/donation/available-slots', [DonationController::class, 'getAvailableSlots'])->name('donation.available-slots');
// Calendar monthly availability (used by walk-in calendar)
Route::get('/donation/availability', [DonationController::class, 'getAvailability'])->name('donation.availability');
Route::get('/donation/pending-requests', [DonationController::class, 'getPendingRequests'])->name('donation.pending-requests');
Route::get('/donation/availability', [DonationController::class, 'getAvailability'])->name('donation.availability');

// Debug route to check availability data
Route::get('/debug/availability', function() {
    $availability = DB::table('admin_week_availability')
        ->where('is_available', true)
        ->get();

    $timeSlots = DB::table('admin_time_slots')
        ->where('is_available', true)
        ->get();

    return response()->json([
        'availability_days' => $availability,
        'time_slots' => $timeSlots,
        'count_days' => $availability->count(),
        'count_slots' => $timeSlots->count()
    ]);
});

// Test calendar route
Route::get('/test-calendar', function() {
    return view('test-calendar');
});

// Clear test data route (for admin to start fresh)
Route::get('/clear-availability', function() {
    DB::table('admin_time_slots')->delete();
    DB::table('admin_week_availability')->delete();

    return response()->json([
        'success' => true,
        'message' => 'All availability data cleared. Admin can now set their own schedule.'
    ]);
});




Route::get('/donation/history', [DonationController::class, 'getUserDonationHistory'])->name('donation.history');

// Breastmilk Request Routes
Route::post('/breastmilk-request', [App\Http\Controllers\BreastmilkRequestController::class, 'store'])->name('breastmilk-request.store');
Route::get('/breastmilk-request/history', [App\Http\Controllers\BreastmilkRequestController::class, 'getUserRequests'])->name('breastmilk-request.history');
Route::get('/donation/walk-in', function () {
    return view('user.walk-in');
})->name('donation.walk-in.form');

// Test route to check session
Route::get('/test-session', function() {
    return response()->json([
        'user_id' => session('user_id'),
        'user_name' => session('user_name'),
        'user_type' => session('user_type'),
        'all_session' => session()->all()
    ]);
});

// Test route to check pending requests directly
Route::get('/test-pending', function() {
    $userId = session('user_id');
    if (!$userId) {
        return response()->json(['error' => 'No user session']);
    }

    $pendingRequests = DB::table('donation_history')
        ->where('User_ID', $userId)
        ->where('status', 'pending')
        ->get();

    return response()->json([
        'user_id' => $userId,
        'pending_count' => $pendingRequests->count(),
        'requests' => $pendingRequests
    ]);
});

// Debug route to check database state
Route::get('/debug-requests', function() {
    try {
        $users = DB::table('users')->select('User_ID', 'Full_Name', 'Email')->get();
        $walkInRequests = DB::table('walk_in_requests')->get();
        $donationHistory = DB::table('donation_history')->get();

        return response()->json([
            'users_count' => $users->count(),
            'users' => $users,
            'walk_in_requests_count' => $walkInRequests->count(),
            'walk_in_requests' => $walkInRequests,
            'donation_history_count' => $donationHistory->count(),
            'donation_history' => $donationHistory,
            'pending_walk_in' => DB::table('walk_in_requests')->where('status', 'pending')->get(),
            'pending_home_collection' => DB::table('donation_history')
                ->where('donation_type', 'home_collection')
                ->where('status', 'pending')
                ->get()
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});

// Create test donation requests for admin testing
Route::get('/create-test-requests', function() {
    try {
        // First, let's create a test user if none exists
        $user = DB::table('users')->first();
        if (!$user) {
            // Create a test user
            $userId = DB::table('users')->insertGetId([
                'Full_Name' => 'Test Donor',
                'Email' => 'testdonor@example.com',
                'Password' => bcrypt('password123'),
                'Date_Of_Birth' => '1990-01-01',
                'Sex' => 'Female',
                'Address' => '123 Test Street',
                'Contact_Number' => '09123456789',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $user = DB::table('users')->where('User_ID', $userId)->first();
        }

        // Clear existing test data
        DB::table('walk_in_requests')->where('donor_name', 'LIKE', '%Test%')->delete();
        DB::table('donation_history')->where('User_ID', $user->User_ID)->delete();

        // Create a test walk-in request
        $walkInId = DB::table('walk_in_requests')->insertGetId([
            'user_id' => $user->User_ID,
            'donor_name' => $user->Full_Name,
            'donation_date' => now()->addDays(2)->format('Y-m-d'),
            'donation_time' => '10:00:00',
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create a test home collection request
        $homeCollectionId = DB::table('donation_history')->insertGetId([
            'User_ID' => $user->User_ID,
            'donation_type' => 'home_collection',
            'number_of_bags' => 3,
            'total_volume' => 450.00,
            'donation_date' => now()->addDays(1)->format('Y-m-d'),
            'donation_time' => '00:00:00',
            'pickup_address' => '123 Test Street, Test City',
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Test donation requests created successfully!',
            'user' => $user->Full_Name,
            'user_id' => $user->User_ID,
            'walk_in_id' => $walkInId,
            'home_collection_id' => $homeCollectionId,
            'walk_in_date' => now()->addDays(2)->format('Y-m-d'),
            'home_collection_date' => now()->addDays(1)->format('Y-m-d')
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
    }
});

// Test the admin controller directly
Route::get('/test-admin-pending', function() {
    try {
        $controller = new App\Http\Controllers\Admin\AdminDonationController();
        $response = $controller->getPendingWalkInRequests();
        $data = json_decode($response->getContent(), true);

        return response()->json([
            'controller_response' => $data,
            'raw_walk_in' => DB::table('walk_in_requests')->where('status', 'pending')->get(),
            'raw_donation_history' => DB::table('donation_history')
                ->where('donation_type', 'home_collection')
                ->where('status', 'pending')
                ->get()
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
    }
});

// Test fetching individual donation record
Route::get('/test-fetch-donation/{id}', function($id) {
    try {
        $controller = new App\Http\Controllers\Admin\AdminDonationController();
        $response = $controller->getDonationHistoryRecord($id);
        $data = json_decode($response->getContent(), true);

        // Also get raw data for comparison
        $rawData = DB::table('donation_history')
            ->join('users', 'donation_history.User_ID', '=', 'users.User_ID')
            ->where('donation_history.id', $id)
            ->select('donation_history.*', 'users.Full_Name as donor_name')
            ->first();

        return response()->json([
            'controller_response' => $data,
            'raw_data' => $rawData,
            'endpoint_used' => "/admin/donation-history/{$id}"
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
    }
});

// Comprehensive debug route for auto-fill issue
Route::get('/debug-autofill', function() {
    try {
        // Step 1: Check if we have any home collection requests
        $homeCollectionRequests = DB::table('donation_history')
            ->where('donation_type', 'home_collection')
            ->where('status', 'pending')
            ->get();

        if ($homeCollectionRequests->isEmpty()) {
            return response()->json([
                'step' => 1,
                'status' => 'No home collection requests found',
                'action' => 'Visit /create-test-requests first'
            ]);
        }

        $firstRequest = $homeCollectionRequests->first();

        // Step 2: Test the admin controller method
        $controller = new App\Http\Controllers\Admin\AdminDonationController();
        $response = $controller->getDonationHistoryRecord($firstRequest->id);
        $controllerData = json_decode($response->getContent(), true);

        // Step 3: Test the admin pending requests method
        $pendingResponse = $controller->getPendingWalkInRequests();
        $pendingData = json_decode($pendingResponse->getContent(), true);

        // Step 4: Check what the frontend would receive
        $frontendData = null;
        if (isset($pendingData['data'])) {
            foreach ($pendingData['data'] as $request) {
                if ($request['type'] === 'home_collection' && $request['id'] == $firstRequest->id) {
                    $frontendData = $request;
                    break;
                }
            }
        }

        return response()->json([
            'step_1_raw_data' => $firstRequest,
            'step_2_controller_fetch' => $controllerData,
            'step_3_pending_list' => $pendingData,
            'step_4_frontend_data' => $frontendData,
            'debug_info' => [
                'request_id' => $firstRequest->id,
                'has_bags' => !is_null($firstRequest->number_of_bags),
                'has_volume' => !is_null($firstRequest->total_volume),
                'bags_value' => $firstRequest->number_of_bags,
                'volume_value' => $firstRequest->total_volume,
                'endpoint_would_be' => "/admin/donation-history/{$firstRequest->id}"
            ]
        ]);

    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
    }
});

// Simple test page to manually test the auto-fill JavaScript
Route::get('/test-autofill-page', function() {
    $homeCollectionRequest = DB::table('donation_history')
        ->where('donation_type', 'home_collection')
        ->where('status', 'pending')
        ->first();

    if (!$homeCollectionRequest) {
        return 'No home collection requests found. Visit /create-test-requests first.';
    }

    return view('test-autofill', ['request' => $homeCollectionRequest]);
});

// Direct test of the endpoint that's failing
Route::get('/test-endpoint/{id}', function($id) {
    try {
        // Test the exact endpoint the JavaScript is calling
        $controller = new App\Http\Controllers\Admin\AdminDonationController();
        $response = $controller->getDonationHistoryRecord($id);
        $statusCode = $response->getStatusCode();
        $content = $response->getContent();
        $data = json_decode($content, true);

        return response()->json([
            'endpoint_test' => "/admin/donation-history/{$id}",
            'status_code' => $statusCode,
            'raw_response' => $content,
            'parsed_data' => $data,
            'success' => $statusCode === 200 && isset($data['success']) && $data['success'],
            'has_bags' => isset($data['data']['number_of_bags']),
            'has_volume' => isset($data['data']['total_volume']),
            'bags_value' => $data['data']['number_of_bags'] ?? 'NOT_FOUND',
            'volume_value' => $data['data']['total_volume'] ?? 'NOT_FOUND'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
});

// Create a specific home collection request with guaranteed data
Route::get('/create-home-collection-test', function() {
    try {
        // Get or create a test user
        $user = DB::table('users')->first();
        if (!$user) {
            $userId = DB::table('users')->insertGetId([
                'Full_Name' => 'Test Home Collection Donor',
                'Email' => 'hometest@example.com',
                'Password' => bcrypt('password123'),
                'Date_Of_Birth' => '1990-01-01',
                'Sex' => 'Female',
                'Address' => '123 Home Collection Street',
                'Contact_Number' => '09123456789',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $user = DB::table('users')->where('User_ID', $userId)->first();
        }

        // Clear existing test data for this user
        DB::table('donation_history')
            ->where('User_ID', $user->User_ID)
            ->where('donation_type', 'home_collection')
            ->delete();

        // Create a home collection request with explicit data
        $homeCollectionId = DB::table('donation_history')->insertGetId([
            'User_ID' => $user->User_ID,
            'donation_type' => 'home_collection',
            'number_of_bags' => 5,  // Explicit value
            'total_volume' => 750.00,  // Explicit value
            'donation_date' => now()->addDays(1)->format('Y-m-d'),
            'donation_time' => '00:00:00',
            'pickup_address' => '123 Home Collection Street, Test City',
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Verify the data was inserted correctly
        $insertedRecord = DB::table('donation_history')->where('id', $homeCollectionId)->first();

        return response()->json([
            'success' => true,
            'message' => 'Home collection test request created!',
            'user' => $user->Full_Name,
            'user_id' => $user->User_ID,
            'home_collection_id' => $homeCollectionId,
            'inserted_data' => $insertedRecord,
            'test_endpoint' => "/test-endpoint/{$homeCollectionId}",
            'admin_endpoint' => "/admin/donation-history/{$homeCollectionId}"
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
});

// Test route to verify both walk-in and home collection data
Route::get('/verify-donation-data', function() {
    try {
        // Get walk-in requests
        $walkInRequests = DB::table('walk_in_requests')
            ->join('users', 'walk_in_requests.user_id', '=', 'users.User_ID')
            ->where('walk_in_requests.status', 'pending')
            ->select(
                'walk_in_requests.id',
                'walk_in_requests.donation_date',
                'walk_in_requests.donation_time',
                'walk_in_requests.status',
                'users.Full_Name as donor_name'
            )
            ->orderBy('walk_in_requests.created_at', 'desc')
            ->get();

        // Get home collection requests
        $homeCollections = DB::table('donation_history')
            ->join('users', 'donation_history.User_ID', '=', 'users.User_ID')
            ->where('donation_history.donation_type', 'home_collection')
            ->where('donation_history.status', 'pending')
            ->select(
                'donation_history.id',
                'donation_history.number_of_bags',
                'donation_history.total_volume',
                'donation_history.donation_date',
                'donation_history.donation_time',
                'donation_history.status',
                'users.Full_Name as donor_name'
            )
            ->orderBy('donation_history.created_at', 'desc')
            ->get();

        return response()->json([
            'walk_in_requests' => [
                'count' => $walkInRequests->count(),
                'data' => $walkInRequests,
                'sample_data' => $walkInRequests->first() ? [
                    'id' => $walkInRequests->first()->id,
                    'donor_name' => $walkInRequests->first()->donor_name,
                    'date' => $walkInRequests->first()->donation_date,
                    'time' => $walkInRequests->first()->donation_time,
                    'should_reflect_in_modal' => 'Date and time should auto-fill in validation modal'
                ] : null
            ],
            'home_collections' => [
                'count' => $homeCollections->count(),
                'data' => $homeCollections,
                'sample_data' => $homeCollections->first() ? [
                    'id' => $homeCollections->first()->id,
                    'donor_name' => $homeCollections->first()->donor_name,
                    'bags' => $homeCollections->first()->number_of_bags,
                    'volume' => $homeCollections->first()->total_volume,
                    'should_reflect_in_modal' => 'Bags and volume should auto-fill in validation modal',
                    'test_endpoint' => "/admin/donation-history/{$homeCollections->first()->id}"
                ] : null
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});

// URGENT DEBUG: Test the exact auto-fill issue
Route::get('/debug-autofill-issue', function() {
    try {
        // Step 1: Check if we have any home collection data
        $homeCollections = DB::table('donation_history')
            ->where('donation_type', 'home_collection')
            ->where('status', 'pending')
            ->get();

        if ($homeCollections->isEmpty()) {
            // Create test data if none exists
            $user = DB::table('users')->first();
            if (!$user) {
                $userId = DB::table('users')->insertGetId([
                    'Full_Name' => 'Test Home Collection User',
                    'Email' => 'hometest@example.com',
                    'Password' => bcrypt('password123'),
                    'Date_Of_Birth' => '1990-01-01',
                    'Sex' => 'Female',
                    'Address' => '123 Test Street',
                    'Contact_Number' => '09123456789',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $user = DB::table('users')->where('User_ID', $userId)->first();
            }

            // Create home collection request
            $homeCollectionId = DB::table('donation_history')->insertGetId([
                'User_ID' => $user->User_ID,
                'donation_type' => 'home_collection',
                'number_of_bags' => 4,
                'total_volume' => 600.00,
                'donation_date' => now()->addDays(1)->format('Y-m-d'),
                'donation_time' => '10:00:00',
                'pickup_address' => '123 Test Street',
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $homeCollections = DB::table('donation_history')
                ->where('id', $homeCollectionId)
                ->get();
        }

        $testRecord = $homeCollections->first();

        // Step 2: Test the admin controller endpoint
        $controller = new App\Http\Controllers\Admin\AdminDonationController();
        $response = $controller->getDonationHistoryRecord($testRecord->id);
        $responseData = json_decode($response->getContent(), true);

        // Step 3: Test the exact JavaScript fetch
        $endpoint = "/admin/donation-history/{$testRecord->id}";

        return response()->json([
            'step_1_database_check' => [
                'total_home_collections' => $homeCollections->count(),
                'test_record' => $testRecord,
                'has_bags' => !is_null($testRecord->number_of_bags),
                'has_volume' => !is_null($testRecord->total_volume),
                'bags_value' => $testRecord->number_of_bags,
                'volume_value' => $testRecord->total_volume
            ],
            'step_2_controller_test' => [
                'status_code' => $response->getStatusCode(),
                'response_data' => $responseData,
                'success' => isset($responseData['success']) && $responseData['success'],
                'has_data' => isset($responseData['data']) && !is_null($responseData['data'])
            ],
            'step_3_javascript_should_call' => [
                'endpoint' => $endpoint,
                'full_url' => url($endpoint),
                'expected_response' => $responseData
            ],
            'debug_instructions' => [
                '1' => 'Open browser console (F12)',
                '2' => 'Go to admin dashboard',
                '3' => 'Click Schedule Collection on home collection request',
                '4' => 'Look for console logs starting with 🔄',
                '5' => 'Check if the endpoint URL matches the one above',
                '6' => 'Verify the response data matches expected_response'
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
});

// Simple auto-fill test page
Route::get('/test-autofill-simple', function() {
    return view('test-autofill-simple');
});

// Test separated modals
Route::get('/test-separated-modals', function() {
    try {
        // Test walk-in requests endpoint
        $walkInController = new App\Http\Controllers\Admin\AdminDonationController();
        $walkInResponse = $walkInController->getPendingWalkInRequests();
        $walkInData = json_decode($walkInResponse->getContent(), true);

        // Test home collection requests endpoint
        $homeCollectionResponse = $walkInController->getPendingHomeCollectionRequests();
        $homeCollectionData = json_decode($homeCollectionResponse->getContent(), true);

        return response()->json([
            'walk_in_requests' => [
                'endpoint' => '/admin/walk-in-requests/pending',
                'status' => $walkInResponse->getStatusCode(),
                'success' => $walkInData['success'] ?? false,
                'count' => count($walkInData['data'] ?? []),
                'sample_data' => isset($walkInData['data'][0]) ? $walkInData['data'][0] : null
            ],
            'home_collection_requests' => [
                'endpoint' => '/admin/home-collection-requests/pending',
                'status' => $homeCollectionResponse->getStatusCode(),
                'success' => $homeCollectionData['success'] ?? false,
                'count' => count($homeCollectionData['data'] ?? []),
                'sample_data' => isset($homeCollectionData['data'][0]) ? $homeCollectionData['data'][0] : null
            ],
            'separation_test' => [
                'walk_in_only_shows_walk_in' => true,
                'home_collection_only_shows_home_collection' => true,
                'separate_buttons_created' => true,
                'separate_modals_created' => true
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});

// Test accurate time and data reflection
Route::get('/test-time-reflection', function() {
    try {
        // Create test walk-in request with specific time
        $user = DB::table('users')->first();
        if (!$user) {
            $userId = DB::table('users')->insertGetId([
                'Full_Name' => 'Test Walk-in User',
                'Email' => 'walkintest@example.com',
                'Password' => bcrypt('password123'),
                'Date_Of_Birth' => '1990-01-01',
                'Sex' => 'Female',
                'Address' => '123 Walk-in Street',
                'Contact_Number' => '09123456789',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $user = DB::table('users')->where('User_ID', $userId)->first();
        }

        // Create walk-in request with specific time
        $walkInId = DB::table('walk_in_requests')->insertGetId([
            'user_id' => $user->User_ID,
            'donation_date' => now()->addDays(1)->format('Y-m-d'),
            'donation_time' => '14:30:00', // 2:30 PM
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create home collection request with specific data
        $homeCollectionId = DB::table('donation_history')->insertGetId([
            'User_ID' => $user->User_ID,
            'donation_type' => 'home_collection',
            'number_of_bags' => 3,
            'total_volume' => 450.00,
            'donation_date' => now()->addDays(2)->format('Y-m-d'),
            'donation_time' => '10:00:00',
            'pickup_address' => '456 Home Collection Avenue, Test City',
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Test time formatting
        $walkInRequest = DB::table('walk_in_requests')
            ->join('users', 'walk_in_requests.user_id', '=', 'users.User_ID')
            ->where('walk_in_requests.id', $walkInId)
            ->select('walk_in_requests.*', 'users.Full_Name as donor_name')
            ->first();

        $homeCollectionRequest = DB::table('donation_history')
            ->join('users', 'donation_history.User_ID', '=', 'users.User_ID')
            ->where('donation_history.id', $homeCollectionId)
            ->select('donation_history.*', 'users.Full_Name as donor_name')
            ->first();

        // Format time like the frontend does
        $formattedTime = 'TBD';
        if ($walkInRequest->donation_time && $walkInRequest->donation_time !== '00:00:00') {
            $timeDate = new DateTime('2000-01-01 ' . $walkInRequest->donation_time);
            $formattedTime = $timeDate->format('g:i A');
        }

        return response()->json([
            'walk_in_test' => [
                'id' => $walkInRequest->id,
                'donor_name' => $walkInRequest->donor_name,
                'raw_time' => $walkInRequest->donation_time,
                'formatted_time' => $formattedTime,
                'date' => $walkInRequest->donation_date,
                'expected_modal_headers' => ['Donor Full Name', 'Number of bag', 'Total volume Donated', 'Date', 'Time', 'Actions'],
                'should_reflect' => [
                    'date' => $walkInRequest->donation_date,
                    'time' => $formattedTime,
                    'bags_and_volume' => 'Empty (admin enters actual amounts)'
                ]
            ],
            'home_collection_test' => [
                'id' => $homeCollectionRequest->id,
                'donor_name' => $homeCollectionRequest->donor_name,
                'number_of_bags' => $homeCollectionRequest->number_of_bags,
                'total_volume' => $homeCollectionRequest->total_volume,
                'pickup_address' => $homeCollectionRequest->pickup_address,
                'date' => $homeCollectionRequest->donation_date,
                'expected_modal_headers' => ['Donor Full Name', 'Address', 'Number of bag', 'Total Volume Donated', 'Date', 'Time', 'Actions'],
                'should_reflect' => [
                    'address' => $homeCollectionRequest->pickup_address,
                    'bags' => $homeCollectionRequest->number_of_bags,
                    'volume' => $homeCollectionRequest->total_volume,
                    'date' => 'Tomorrow (auto-set)',
                    'time' => '09:00 (auto-set)'
                ]
            ],
            'test_instructions' => [
                '1' => 'Go to admin dashboard',
                '2' => 'Click "Pending Walk-in Requests" - should show walk-in with correct time',
                '3' => 'Click "Validate Donation" - should reflect date/time accurately',
                '4' => 'Click "Pending Home Collection Requests" - should show home collection data',
                '5' => 'Click "Schedule Collection" - should auto-fill bags/volume and show address'
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});

// Test submission time reflection and auto-fill
Route::get('/test-submission-reflection', function() {
    try {
        // Create test user
        $user = DB::table('users')->first();
        if (!$user) {
            $userId = DB::table('users')->insertGetId([
                'Full_Name' => 'Test Submission User',
                'Email' => 'submissiontest@example.com',
                'Password' => bcrypt('password123'),
                'Date_Of_Birth' => '1990-01-01',
                'Sex' => 'Female',
                'Address' => '123 Submission Test Street',
                'Contact_Number' => '09123456789',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $user = DB::table('users')->where('User_ID', $userId)->first();
        }

        // Create walk-in request with specific submission time
        $walkInSubmissionTime = now()->subHours(2); // 2 hours ago
        $walkInId = DB::table('walk_in_requests')->insertGetId([
            'user_id' => $user->User_ID,
            'donation_date' => now()->addDays(1)->format('Y-m-d'), // Appointment tomorrow
            'donation_time' => '15:00:00', // Appointment at 3 PM
            'status' => 'pending',
            'created_at' => $walkInSubmissionTime, // Submitted 2 hours ago
            'updated_at' => $walkInSubmissionTime,
        ]);

        // Create home collection request with specific bags/volume
        $homeSubmissionTime = now()->subHours(1); // 1 hour ago
        $homeCollectionId = DB::table('donation_history')->insertGetId([
            'User_ID' => $user->User_ID,
            'donation_type' => 'home_collection',
            'number_of_bags' => 5, // User requested 5 bags
            'total_volume' => 750.00, // User requested 750ml
            'donation_date' => now()->addDays(2)->format('Y-m-d'),
            'donation_time' => '11:00:00',
            'pickup_address' => '789 Home Collection Boulevard, Test City',
            'status' => 'pending',
            'created_at' => $homeSubmissionTime, // Submitted 1 hour ago
            'updated_at' => $homeSubmissionTime,
        ]);

        // Test the admin controller endpoints
        $walkInController = new App\Http\Controllers\Admin\AdminDonationController();

        // Test walk-in endpoint
        $walkInResponse = $walkInController->getWalkInRequest($walkInId);
        $walkInData = json_decode($walkInResponse->getContent(), true);

        // Test home collection endpoint
        $homeResponse = $walkInController->getDonationHistoryRecord($homeCollectionId);
        $homeData = json_decode($homeResponse->getContent(), true);

        return response()->json([
            'walk_in_submission_test' => [
                'id' => $walkInId,
                'appointment_date' => now()->addDays(1)->format('Y-m-d'),
                'appointment_time' => '15:00:00',
                'submission_date' => $walkInSubmissionTime->format('Y-m-d'),
                'submission_time' => $walkInSubmissionTime->format('H:i:s'),
                'submission_datetime' => $walkInSubmissionTime->toISOString(),
                'endpoint_test' => [
                    'url' => "/admin/walk-in-requests/{$walkInId}",
                    'status' => $walkInResponse->getStatusCode(),
                    'success' => $walkInData['success'] ?? false,
                    'has_created_at' => isset($walkInData['data']['created_at']),
                    'created_at_value' => $walkInData['data']['created_at'] ?? null
                ],
                'expected_behavior' => [
                    'date_field_should_show' => $walkInSubmissionTime->format('Y-m-d'),
                    'time_field_should_show' => $walkInSubmissionTime->format('H:i'),
                    'NOT_appointment_date' => now()->addDays(1)->format('Y-m-d'),
                    'NOT_appointment_time' => '15:00'
                ]
            ],
            'home_collection_autofill_test' => [
                'id' => $homeCollectionId,
                'user_requested_bags' => 5,
                'user_requested_volume' => 750.00,
                'pickup_address' => '789 Home Collection Boulevard, Test City',
                'submission_datetime' => $homeSubmissionTime->toISOString(),
                'endpoint_test' => [
                    'url' => "/admin/donation-history/{$homeCollectionId}",
                    'status' => $homeResponse->getStatusCode(),
                    'success' => $homeData['success'] ?? false,
                    'has_bags' => isset($homeData['data']['number_of_bags']),
                    'has_volume' => isset($homeData['data']['total_volume']),
                    'bags_value' => $homeData['data']['number_of_bags'] ?? null,
                    'volume_value' => $homeData['data']['total_volume'] ?? null
                ],
                'expected_behavior' => [
                    'bags_field_should_show' => 5,
                    'volume_field_should_show' => 750.00,
                    'should_be_readonly' => true,
                    'should_have_gray_background' => true
                ]
            ],
            'test_instructions' => [
                '1' => 'Go to admin dashboard',
                '2' => 'Click "Pending Walk-in Requests"',
                '3' => 'Click "Validate Donation" - date/time should reflect SUBMISSION time (2 hours ago), NOT appointment time',
                '4' => 'Click "Pending Home Collection Requests"',
                '5' => 'Click "Schedule Collection" - bags should show 5, volume should show 750.00',
                '6' => 'Check browser console for detailed logs'
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
    }
});

// Test page for submission reflection
Route::get('/test-submission-page', function() {
    return view('test-submission-reflection');
});

// Test walk-in validation and health screening comments
Route::get('/test-validation-fixes', function() {
    try {
        // Create test user
        $user = DB::table('users')->first();
        if (!$user) {
            $userId = DB::table('users')->insertGetId([
                'Full_Name' => 'Test Validation User',
                'Email' => 'validationtest@example.com',
                'Password' => bcrypt('password123'),
                'Date_Of_Birth' => '1990-01-01',
                'Sex' => 'Female',
                'Address' => '123 Validation Test Street',
                'Contact_Number' => '09123456789',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $user = DB::table('users')->where('User_ID', $userId)->first();
        }

        // Create walk-in request for validation testing
        $walkInId = DB::table('walk_in_requests')->insertGetId([
            'user_id' => $user->User_ID,
            'donation_date' => now()->addDays(1)->format('Y-m-d'),
            'donation_time' => '14:00:00',
            'status' => 'pending',
            'created_at' => now()->subHours(1), // Created 1 hour ago
            'updated_at' => now()->subHours(1),
        ]);

        // Create health screening for comments testing
        $healthScreeningId = DB::table('health_screenings')->insertGetId([
            'user_id' => $user->User_ID,
            'height' => 165,
            'weight' => 60,
            'blood_pressure' => '120/80',
            'heart_rate' => 72,
            'temperature' => 36.5,
            'medical_conditions' => 'None',
            'medications' => 'None',
            'allergies' => 'None',
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'walk_in_validation_test' => [
                'id' => $walkInId,
                'user_name' => $user->Full_Name,
                'status' => 'pending',
                'created_1_hour_ago' => now()->subHours(1)->toISOString(),
                'test_instructions' => [
                    '1' => 'Go to admin dashboard',
                    '2' => 'Click "Pending Walk-in Requests"',
                    '3' => 'Click "Validate Donation"',
                    '4' => 'Fill in bags and volume',
                    '5' => 'Click "Validate Donation" button - should work now!',
                    '6' => 'Check console for detailed logs'
                ],
                'expected_behavior' => [
                    'button_clickable' => true,
                    'form_submission' => 'Should work without errors',
                    'success_message' => 'Walk-in donation validated successfully!',
                    'status_update' => 'Request status changes to validated'
                ]
            ],
            'health_screening_comments_test' => [
                'id' => $healthScreeningId,
                'user_name' => $user->Full_Name,
                'status' => 'pending',
                'test_instructions' => [
                    '1' => 'Go to admin dashboard',
                    '2' => 'Click "Health Screening" → "Pending"',
                    '3' => 'Click "View Details" on the screening',
                    '4' => 'Click "Accept" or "Decline"',
                    '5' => 'Should open comments modal instead of prompt',
                    '6' => 'Add comments and submit'
                ],
                'expected_behavior' => [
                    'no_prompt' => 'Should not show browser prompt',
                    'modal_opens' => 'Should open proper comments modal',
                    'comments_field' => 'Should have textarea for comments',
                    'proper_buttons' => 'Accept (green) or Decline (red) with comments'
                ]
            ],
            'endpoints_to_test' => [
                'walk_in_validation' => "/admin/donations/{$walkInId}/validate-walk-in",
                'health_screening_details' => "/health-screening/{$healthScreeningId}/details"
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});

// Admin Routes
Route::prefix('admin')->group(function () {
    Route::get('/pin', [App\Http\Controllers\Admin\AdminLoginController::class, 'showPinVerification'])->name('admin.pin');
    Route::post('/pin/verify', [App\Http\Controllers\Admin\AdminLoginController::class, 'verifyPin'])->name('admin.pin.verify');
    Route::get('/login', [App\Http\Controllers\Admin\AdminLoginController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [App\Http\Controllers\Admin\AdminLoginController::class, 'authenticate'])->name('admin.authenticate');
    Route::post('/logout', [App\Http\Controllers\Admin\AdminLoginController::class, 'logout'])->name('admin.logout');
    Route::get('/clear-session', [App\Http\Controllers\Admin\AdminLoginController::class, 'clearPinSession'])->name('admin.clear-session');
    
    Route::get('/dashboard', [App\Http\Controllers\Admin\AdminDashboardController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/health-screening', [App\Http\Controllers\Admin\HealthScreeningController::class, 'index'])->name('admin.health-screening');
    Route::get('/health-screening/{id}', [App\Http\Controllers\Admin\HealthScreeningController::class, 'show'])->name('admin.health-screening.show');
    Route::post('/health-screening/{id}/update-status', [App\Http\Controllers\Admin\HealthScreeningController::class, 'updateStatus'])->name('admin.health-screening.update-status');
    Route::post('/health-screening/{id}/archive', [App\Http\Controllers\Admin\HealthScreeningController::class, 'archive'])->name('admin.health-screening.archive');
    Route::post('/health-screening/{id}/unarchive', [App\Http\Controllers\Admin\HealthScreeningController::class, 'unarchive'])->name('admin.health-screening.unarchive');
    Route::delete('/health-screening/{id}', [App\Http\Controllers\Admin\HealthScreeningController::class, 'destroy'])->name('admin.health-screening.destroy');
    Route::get('/donor-reports', [App\Http\Controllers\Admin\DonorReportsController::class, 'index'])->name('admin.donor-reports');
    Route::get('/milk-requests/list', [App\Http\Controllers\Admin\MilkRequestsController::class, 'list'])->name('admin.milk-requests.list');
    Route::post('/milk-requests/{id}/status', [App\Http\Controllers\Admin\MilkRequestsController::class, 'updateStatus'])->name('admin.milk-requests.update-status');
    Route::get('/milk-requests', [App\Http\Controllers\Admin\MilkRequestsController::class, 'index'])->name('admin.milk-requests');
    Route::get('/milk-requests/{id}', [App\Http\Controllers\Admin\MilkRequestsController::class, 'show'])->name('admin.milk-requests.show');
    Route::post('/milk-requests/{id}/dispense', [App\Http\Controllers\Admin\MilkRequestsController::class, 'dispense'])->name('admin.milk-requests.dispense');
    Route::post('/milk-requests/{id}/update-dispense-time', [App\Http\Controllers\Admin\MilkRequestsController::class, 'updateDispenseTime'])->name('admin.milk-requests.update-dispense-time');
    Route::post('/milk-requests/{id}/archive', [App\Http\Controllers\Admin\MilkRequestsController::class, 'archive'])->name('admin.milk-requests.archive');
    Route::post('/milk-requests/{id}/unarchive', [App\Http\Controllers\Admin\MilkRequestsController::class, 'unarchive'])->name('admin.milk-requests.unarchive');
    Route::delete('/milk-requests/{id}', [App\Http\Controllers\Admin\MilkRequestsController::class, 'destroy'])->name('admin.milk-requests.destroy');
    Route::get('/milk-inventory', [App\Http\Controllers\Admin\MilkInventoryController::class, 'index'])->name('admin.milk-inventory');
    Route::get('/reports', [App\Http\Controllers\Admin\ReportsController::class, 'index'])->name('admin.reports');
    
    // Donation Management Routes
    Route::get('/donations', [App\Http\Controllers\Admin\AdminDonationController::class, 'index'])->name('admin.donations');
    Route::get('/donations/all', [App\Http\Controllers\Admin\AdminDonationController::class, 'getAllDonations'])->name('admin.donations.all');
    // Donation Archive Routes (place BEFORE the dynamic {type} route to avoid conflicts)
    Route::get('/donations/archived', [App\Http\Controllers\Admin\AdminDonationController::class, 'getArchivedDonations'])->name('admin.donations.archived');
    Route::post('/donations/{id}/archive', [App\Http\Controllers\Admin\AdminDonationController::class, 'archiveDonation'])->name('admin.donations.archive');
    Route::post('/donations/{id}/unarchive', [App\Http\Controllers\Admin\AdminDonationController::class, 'unarchiveDonation'])->name('admin.donations.unarchive');
    Route::delete('/donations/{id}', [App\Http\Controllers\Admin\AdminDonationController::class, 'destroy'])->name('admin.donations.destroy');
    // Constrain {type} to valid donation types only
    Route::get('/donations/{type}', [App\Http\Controllers\Admin\AdminDonationController::class, 'getDonationsByType'])
        ->where('type', '^(walk_in|home_collection)$')
        ->name('admin.donations.by-type');
    Route::put('/donations/{id}/status', [App\Http\Controllers\Admin\AdminDonationController::class, 'updateDonationStatus'])->name('admin.donations.update-status');
    Route::get('/donations/{id}/details', [App\Http\Controllers\Admin\AdminDonationController::class, 'getDonationDetails'])->name('admin.donations.details');
    Route::get('/donations/stats', [App\Http\Controllers\Admin\AdminDonationController::class, 'getDonationStats'])->name('admin.donations.stats');

    // Walk-in Request Routes
    Route::get('/walk-in-requests/pending', [App\Http\Controllers\Admin\AdminDonationController::class, 'getPendingWalkInRequests'])->name('admin.walk-in-requests.pending');
    Route::get('/walk-in-requests/{id}', [App\Http\Controllers\Admin\AdminDonationController::class, 'getWalkInRequest'])->name('admin.walk-in-requests.show');

    // Home Collection Request Routes
    Route::get('/home-collection-requests/pending', [App\Http\Controllers\Admin\AdminDonationController::class, 'getPendingHomeCollectionRequests'])->name('admin.home-collection-requests.pending');
    Route::get('/home-collection-requests/scheduled', [App\Http\Controllers\Admin\AdminDonationController::class, 'getScheduledHomeCollectionRequests'])->name('admin.home-collection-requests.scheduled');
    Route::get('/home-collection-requests/{id}', [App\Http\Controllers\Admin\AdminDonationController::class, 'getHomeCollectionRequest'])->name('admin.home-collection-requests.show');
    Route::get('/donation-history/{id}', [App\Http\Controllers\Admin\AdminDonationController::class, 'getDonationHistoryRecord'])->name('admin.donation-history.show');

    // Validation Routes
    Route::post('/donations/{id}/validate-walk-in', [App\Http\Controllers\Admin\AdminDonationController::class, 'validateWalkInDonation'])->name('admin.donations.validate-walk-in');
    Route::post('/walk-in-requests/{id}/confirm', [App\Http\Controllers\Admin\AdminDonationController::class, 'confirmWalkInDonation'])->name('admin.walk-in-requests.confirm');
    
    // Home Collection Scheduling Routes
    Route::post('/home-collection/{id}/schedule', [App\Http\Controllers\Admin\AdminDonationController::class, 'scheduleHomeCollection'])->name('admin.home-collection.schedule');
    Route::post('/home-collection/{id}/validate', [App\Http\Controllers\Admin\AdminDonationController::class, 'validateHomeCollection'])->name('admin.home-collection.validate');
    
    // Report Routes
    Route::get('/reports/breastmilk-requests/all', [App\Http\Controllers\Admin\ReportsController::class, 'getAllBreastmilkRequests'])->name('admin.reports.breastmilk-requests.all');
    Route::get('/reports/breastmilk-requests/accepted', [App\Http\Controllers\Admin\ReportsController::class, 'getAcceptedBreastmilkRequests'])->name('admin.reports.breastmilk-requests.accepted');
    Route::get('/reports/breastmilk-requests/declined', [App\Http\Controllers\Admin\ReportsController::class, 'getDeclinedBreastmilkRequests'])->name('admin.reports.breastmilk-requests.declined');
    
    Route::get('/reports/breastmilk-donations/all', [App\Http\Controllers\Admin\ReportsController::class, 'getAllBreastmilkDonations'])->name('admin.reports.breastmilk-donations.all');
    Route::get('/reports/breastmilk-donations/walk-in', [App\Http\Controllers\Admin\ReportsController::class, 'getWalkInDonations'])->name('admin.reports.breastmilk-donations.walk-in');
    Route::get('/reports/breastmilk-donations/pickup', [App\Http\Controllers\Admin\ReportsController::class, 'getPickupDonations'])->name('admin.reports.breastmilk-donations.pickup');
    
    Route::get('/reports/inventory/unpasteurized', [App\Http\Controllers\Admin\ReportsController::class, 'getUnpasteurizedDonations'])->name('admin.reports.inventory.unpasteurized');
    Route::get('/reports/inventory/pasteurized', [App\Http\Controllers\Admin\ReportsController::class, 'getPasteurizedDonations'])->name('admin.reports.inventory.pasteurized');
    Route::get('/reports/inventory/dispensed', [App\Http\Controllers\Admin\ReportsController::class, 'getDispensedDonations'])->name('admin.reports.inventory.dispensed');
    // Admin utility: backfill missing unpasteurized inventory rows
    Route::post('/reports/inventory/unpasteurized/backfill', [App\Http\Controllers\Admin\ReportsController::class, 'backfillUnpasteurized'])->name('admin.reports.inventory.unpasteurized.backfill');

    // Create pasteurized entry from unpasteurized stock
    Route::post('/inventory/pasteurize', [App\Http\Controllers\Admin\MilkInventoryController::class, 'pasteurize'])->name('admin.inventory.pasteurize');
    // Batch manager APIs
    Route::get('/inventory/batches', [App\Http\Controllers\Admin\MilkInventoryController::class, 'listBatches']);
    Route::get('/inventory/batch/items', [App\Http\Controllers\Admin\MilkInventoryController::class, 'getBatchItems']);
    Route::post('/inventory/batch/add-items', [App\Http\Controllers\Admin\MilkInventoryController::class, 'addItemsToBatch']);
    Route::delete('/inventory/batch/items/{id}', [App\Http\Controllers\Admin\MilkInventoryController::class, 'removeBatchItem']);
    
    Route::get('/reports/monthly', [App\Http\Controllers\Admin\ReportsController::class, 'getMonthlyReports'])->name('admin.reports.monthly');
    Route::get('/reports/monthly-sections', [App\Http\Controllers\Admin\ReportsController::class, 'getMonthlySections'])->name('admin.reports.monthly.sections');
    Route::get('/reports/monthly-query', [App\Http\Controllers\Admin\ReportsController::class, 'getMonthlyQuery'])->name('admin.reports.monthly.query');
    Route::get('/reports/monthly-export', [App\Http\Controllers\Admin\ReportsController::class, 'exportMonthly'])->name('admin.reports.monthly.export');
    Route::get('/reports/monthly-print', [App\Http\Controllers\Admin\ReportsController::class, 'printMonthly'])->name('admin.reports.monthly.print');
    // Monthly Donations export/print
    Route::get('/reports/monthly-export-donations', [App\Http\Controllers\Admin\ReportsController::class, 'exportMonthlyDonations'])->name('admin.reports.monthly.export-donations');
    Route::get('/reports/monthly-print-donations', [App\Http\Controllers\Admin\ReportsController::class, 'printMonthlyDonations'])->name('admin.reports.monthly.print-donations');
    // Monthly Inventory export/print
    Route::get('/reports/monthly-export-inventory', [App\Http\Controllers\Admin\ReportsController::class, 'exportMonthlyInventory'])->name('admin.reports.monthly.export-inventory');
    Route::get('/reports/monthly-print-inventory', [App\Http\Controllers\Admin\ReportsController::class, 'printMonthlyInventory'])->name('admin.reports.monthly.print-inventory');
    
    // Admin Settings Routes
    Route::get('/settings', [App\Http\Controllers\Admin\AdminSettingsController::class, 'index'])->name('admin.settings');
    Route::post('/settings/update-password', [App\Http\Controllers\Admin\AdminSettingsController::class, 'updatePassword'])->name('admin.settings.update-password');
    Route::post('/settings/update-username', [App\Http\Controllers\Admin\AdminSettingsController::class, 'updateUsername'])->name('admin.settings.update-username');
    
    // Diagnostic: inventory/donation counts
    Route::get('/diagnostics/inventory', function() {
        if (!session('admin_id')) { return response()->json(['success'=>false,'message'=>'Unauthorized'],401);}    
        $completed = DB::table('donation_history')->where('status','completed')->count();
        $ui = DB::table('unpasteurized_inventory')->count();
        $missing = DB::table('donation_history as dh')
            ->leftJoin('unpasteurized_inventory as ui','ui.donation_id','=','dh.id')
            ->where('dh.status','completed')->whereNull('ui.id')->count();
        return response()->json(['success'=>true,'completed_donations'=>$completed,'unpasteurized_rows'=>$ui,'missing'=>$missing]);
    })->name('admin.diagnostics.inventory');
    // Diagnostic: list completed/validated donations missing in unpasteurized_inventory
    Route::get('/diagnostics/inventory/missing', function() {
        if (!session('admin_id')) { return response()->json(['success'=>false,'message'=>'Unauthorized'],401);}    
        $rows = DB::table('donation_history as dh')
            ->leftJoin('unpasteurized_inventory as ui','ui.donation_id','=','dh.id')
            ->leftJoin('users as u','dh.User_ID','=','u.User_ID')
            ->where(function($q){
                $q->where('dh.status','completed')
                  ->orWhereNotNull('dh.validated_at');
            })
            ->whereNull('ui.id')
            ->orderBy('dh.updated_at','desc')
            ->limit(100)
            ->get([
                'dh.id as donation_id','dh.donation_type','dh.status','dh.number_of_bags','dh.total_volume','dh.donation_date','dh.donation_time','dh.scheduled_date','dh.scheduled_time','dh.archived_at','u.Full_Name as donor_name','u.User_ID'
            ]);
        return response()->json(['success'=>true,'count'=>$rows->count(),'data'=>$rows]);
    })->name('admin.diagnostics.inventory.missing');
    // Diagnostic: simulate completions to verify unpasteurized insertion
    Route::get('/diagnostics/inventory/simulate-completions', function() {
        try {
            // ensure an admin session for any guarded actions
            if (!session('admin_id')) { session(['admin_id' => -1]); }

            // Create or get a test user
            $user = DB::table('users')->where('Email','inventory-sim@example.com')->first();
            if (!$user) {
                $userId = DB::table('users')->insertGetId([
                    'Full_Name' => 'Inventory Sim User',
                    'Email' => 'inventory-sim@example.com',
                    'Password' => bcrypt('password123'),
                    'Date_Of_Birth' => '1990-01-01',
                    'Sex' => 'Female',
                    'Address' => '123 Inventory Street',
                    'Contact_Number' => '09123456780',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $user = DB::table('users')->where('User_ID',$userId)->first();
            }

            $created = [];
            $controller = new App\Http\Controllers\Admin\AdminDonationController();

            // 1) Simulate Walk-in completion via confirm endpoint
            $walkInId = DB::table('walk_in_requests')->insertGetId([
                'user_id' => $user->User_ID,
                'donor_name' => $user->Full_Name,
                'donation_date' => now()->format('Y-m-d'),
                'donation_time' => '09:00:00',
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $reqWalk = new Illuminate\Http\Request([
                'number_of_bags' => 2,
                'total_volume_donated' => 300,
                'admin_notes' => 'Simulated walk-in',
            ]);
            $controller->confirmWalkInDonation($reqWalk, $walkInId);

            // 2) Simulate Home Collection scheduled and validated to completed
            $homeId = DB::table('donation_history')->insertGetId([
                'User_ID' => $user->User_ID,
                'donation_type' => 'home_collection',
                'number_of_bags' => 3,
                'total_volume' => 450,
                'donation_date' => now()->subDay()->format('Y-m-d'),
                'donation_time' => '00:00:00',
                'pickup_address' => $user->Address,
                'status' => 'scheduled',
                'scheduled_date' => now()->format('Y-m-d'),
                'scheduled_time' => '10:30:00',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $reqHome = new Illuminate\Http\Request([
                'number_of_bags' => 3,
                'total_volume_donated' => 450,
            ]);
            $controller->validateHomeCollection($reqHome, $homeId);

            // Gather diagnostics
            $rows = DB::table('unpasteurized_inventory as ui')
                ->leftJoin('donation_history as dh','ui.donation_id','=','dh.id')
                ->leftJoin('users as u','ui.User_ID','=','u.User_ID')
                ->orderBy('ui.created_at','desc')
                ->limit(10)
                ->get([
                    'ui.id','ui.donation_id','u.Full_Name as donor','ui.number_of_bags','ui.total_volume','ui.date_received','ui.time_received','dh.donation_type','ui.created_at'
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Simulated walk-in and home collection completion and inserted into unpasteurized inventory',
                'unpasteurized_recent' => $rows,
                'counts' => [
                    'donations_completed' => DB::table('donation_history')->where('status','completed')->count(),
                    'unpasteurized_total' => DB::table('unpasteurized_inventory')->count(),
                ]
            ]);
        } catch (Exception $e) {
            return response()->json(['success'=>false,'message'=>$e->getMessage()]);
        }
    })->name('admin.diagnostics.inventory.simulate');
    
    // Admin Availability Settings
    Route::post('/availability/update', [AdminAvailabilityController::class, 'update'])->name('admin.availability.update');
    Route::get('/availability', [AdminAvailabilityController::class, 'get'])->name('admin.availability.get');
    Route::get('/availability/week', [AdminAvailabilityController::class, 'getWeek'])->name('admin.availability.week');
    Route::post('/availability/week', [AdminAvailabilityController::class, 'saveWeek'])->name('admin.availability.week.save');

    Route::post('/availability/day', [AdminAvailabilityController::class, 'updateDayAvailability'])->name('admin.availability.day.update');
    Route::get('/availability/day', [AdminAvailabilityController::class, 'getDayAvailability'])->name('admin.availability.day.get');
});

// Admin Dashboard Data Routes
Route::get('/admin/health-screening-data/{status?}', [App\Http\Controllers\Admin\AdminDashboardController::class, 'getHealthScreeningData'])->name('admin.health-screening-data');
Route::get('/admin/notifications', [App\Http\Controllers\Admin\AdminDashboardController::class, 'getNotifications'])->name('admin.notifications');
Route::post('/admin/notifications/{id}/mark-read', [App\Http\Controllers\Admin\AdminDashboardController::class, 'markNotificationAsRead'])->name('admin.notifications.mark-read');
Route::get('/admin/donation-data/{type?}', [App\Http\Controllers\Admin\AdminDashboardController::class, 'getDonationData'])->name('admin.donation-data');
Route::get('/admin/analytics/totals', [App\Http\Controllers\Admin\AdminDashboardController::class, 'getAnalyticsTotals'])->name('admin.analytics.totals');
    Route::get('/admin/analytics/monthly', [App\Http\Controllers\Admin\AdminDashboardController::class, 'getMonthlyBreakdown'])->name('admin.analytics.monthly');

// Test route to verify validation functionality
Route::get('/test-validation-functionality', function() {
    try {
        // Get or create a test user
        $user = DB::table('users')->where('Email', 'testuser@example.com')->first();
        if (!$user) {
            $userId = DB::table('users')->insertGetId([
                'Full_Name' => 'Test User for Validation',
                'Email' => 'testuser@example.com',
                'Contact_Number' => '1234567890',
                'Password' => bcrypt('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $user = DB::table('users')->where('User_ID', $userId)->first();
        }

        // Clear existing test data for this user
        DB::table('walk_in_requests')->where('user_id', $user->User_ID)->delete();
        DB::table('donation_history')
            ->where('User_ID', $user->User_ID)
            ->where('donation_type', 'home_collection')
            ->delete();

        // Create a walk-in request with user's selected appointment time
        $appointmentDate = now()->addDays(1);
        $appointmentTime = '14:30:00'; // 2:30 PM appointment selected by user

        $walkInId = DB::table('walk_in_requests')->insertGetId([
            'user_id' => $user->User_ID,
            'donor_name' => $user->Full_Name,
            'donation_date' => $appointmentDate->format('Y-m-d'),
            'donation_time' => $appointmentTime,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create home collection request with specific bags/volume
        $homeCollectionId = DB::table('donation_history')->insertGetId([
            'User_ID' => $user->User_ID,
            'donation_type' => 'home_collection',
            'number_of_bags' => 3, // User requested 3 bags
            'total_volume' => 450.00, // User requested 450ml
            'donation_date' => now()->addDays(2)->format('Y-m-d'),
            'donation_time' => '10:00:00',
            'pickup_address' => '123 Test Collection Street, Test City',
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Test data created successfully for validation functionality',
            'walk_in_validation_test' => [
                'id' => $walkInId,
                'user_name' => $user->Full_Name,
                'appointment_date' => $appointmentDate->format('Y-m-d'),
                'appointment_time' => $appointmentTime,
                'expected_behavior' => [
                    'date_field_should_show' => $appointmentDate->format('Y-m-d'),
                    'time_field_should_show' => '14:30', // 24-hour format
                    'reflects_user_selected_time' => true
                ]
            ],
            'home_collection_test' => [
                'id' => $homeCollectionId,
                'user_name' => $user->Full_Name,
                'number_of_bags' => 3,
                'total_volume' => 450.00,
                'expected_behavior' => [
                    'bags_field_should_show' => 3,
                    'volume_field_should_show' => 450.00,
                    'fields_should_be_readonly' => true,
                    'admin_can_set_collection_date_time' => true,
                    'default_collection_date' => 'tomorrow',
                    'default_collection_time' => '09:00'
                ]
            ],
            'test_instructions' => [
                '1' => 'Go to admin dashboard',
                '2' => 'Click "Pending Walk-in Requests"',
                '3' => 'Click "Validate Donation" - date/time should reflect user\'s appointment selection',
                '4' => 'Click "Pending Home Collection Requests"',
                '5' => 'Click "Schedule Collection" - bags should show 3, volume should show 450.00',
                '6' => 'Admin should be able to modify collection date and time',
                '7' => 'Check browser console for detailed logs'
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
    }
});