<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\DonorInfantAnswer;
use App\Models\HealthScreening;
use App\Models\MedicalHistoryAnswer;
use App\Models\SexualHistoryAnswer;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Health Screening Controller
 *
 * Handles all health screening related operations including submission,
 * review, status updates, and data retrieval for both users and admins.
 */
class HealthScreeningController extends Controller
{
    /**
     * Session keys.
     */
    private const SESSION_USER_ID = 'user_id';
    private const SESSION_HEALTH_SCREENING_DATA = 'health_screening_data';

    /**
     * Civil status options.
     */
    private const CIVIL_STATUS_OPTIONS = ['Single', 'Married', 'Divorced', 'Widowed'];

    /**
     * Answer options.
     */
    private const ANSWER_OPTIONS = ['yes', 'no'];

    /**
     * Medical history question count.
     */
    private const MEDICAL_HISTORY_QUESTIONS = 15;

    /**
     * Sexual history question count.
     */
    private const SEXUAL_HISTORY_QUESTIONS = 4;

    /**
     * Donor infant question count.
     */
    private const DONOR_INFANT_QUESTIONS = 5;

    /**
     * Medical history additional fields mapping.
     */
    private const MEDICAL_HISTORY_ADDITIONAL_FIELDS = [
        2 => 'mhq_2_reason',
        4 => 'mhq_4_reason',
        5 => 'mhq_5_reason',
        8 => 'mhq_8_amount',
        10 => 'mhq_10_reason',
        11 => 'mhq_11_supplement',
        13 => 'mhq_13_amount',
    ];

    /**
     * Sexual history question 3 checkboxes.
     */
    private const SEXUAL_HISTORY_Q3_CHECKBOXES = [
        'shq_3_bisexual',
        'shq_3_promiscuous',
        'shq_3_std',
        'shq_3_blood',
        'shq_3_drugs',
    ];

    /**
     * Notification types.
     */
    private const NOTIFICATION_TYPE_SUBMITTED = 'health_screening_submitted';
    private const NOTIFICATION_TYPE_RESULT = 'health_screening_result';

    /**
     * Submit health screening form data for review.
     */
    public function submit(Request $request): JsonResponse|RedirectResponse
    {
        if (!$this->isUserAuthenticated()) {
            return redirect()->route('user-login')
                ->with('error', 'Please login to submit health screening.');
        }

        try {
            // Sanitize occupation to allow only letters and spaces; remove special characters
            if ($request->has('occupation')) {
                $rawOccupation = (string) $request->input('occupation');
                $cleanOccupation = preg_replace('/[^A-Za-z\s]/u', '', $rawOccupation);
                $cleanOccupation = trim($cleanOccupation);
                $request->merge(['occupation' => $cleanOccupation]);
            }

            $this->validateHealthScreeningRequest($request);
            $healthScreeningData = $this->buildHealthScreeningData($request);
            
            session([self::SESSION_HEALTH_SCREENING_DATA => $healthScreeningData]);

            return response()->json([
                'success' => true,
                'message' => 'Form data saved. Please review your answers.',
                'show_review_modal' => true,
            ]);

        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while processing your health screening. Please try again.');
        }
    }

    /**
     * Check if user is authenticated.
     */
    private function isUserAuthenticated(): bool
    {
        return (bool) session(self::SESSION_USER_ID);
    }

    /**
     * Validate health screening request.
     */
    private function validateHealthScreeningRequest(Request $request): void
    {
        $rules = [
            'civil_status' => 'required|in:' . implode(',', self::CIVIL_STATUS_OPTIONS),
            'occupation' => 'required|string|min:2|max:100|regex:/^[A-Za-z\s]+$/',
            'type_of_donor' => 'required|in:' . implode(',', HealthScreening::getDonorTypeOptions()),
        ];

        // Add medical history validation rules
        for ($i = 1; $i <= self::MEDICAL_HISTORY_QUESTIONS; $i++) {
            $rules["mhq_{$i}"] = 'required|in:' . implode(',', self::ANSWER_OPTIONS);
        }

        // Add sexual history validation rules (skip question 3 as it's checkboxes)
        for ($i = 1; $i <= self::SEXUAL_HISTORY_QUESTIONS; $i++) {
            if ($i !== 3) {
                $rules["shq_{$i}"] = 'required|in:' . implode(',', self::ANSWER_OPTIONS);
            }
        }

        // Add donor infant validation rules
        for ($i = 1; $i <= self::DONOR_INFANT_QUESTIONS; $i++) {
            $rules["diq_{$i}"] = 'required|in:' . implode(',', self::ANSWER_OPTIONS);
        }

        $messages = [
            'occupation.regex' => 'Occupation must contain letters and spaces only.',
            'occupation.min' => 'Occupation must be at least 2 characters.',
            'occupation.max' => 'Occupation must not exceed 100 characters.',
        ];

        $request->validate($rules, $messages);
    }

    /**
     * Build health screening data from request.
     */
    private function buildHealthScreeningData(Request $request): array
    {
        return [
            'civil_status' => $request->civil_status,
            'occupation' => $request->occupation,
            'type_of_donor' => $request->type_of_donor,
            'medical_history' => $this->buildMedicalHistoryData($request),
            'sexual_history' => $this->buildSexualHistoryData($request),
            'donor_infant' => $this->buildDonorInfantData($request),
        ];
    }

    /**
     * Build medical history data from request.
     */
    private function buildMedicalHistoryData(Request $request): array
    {
        $medicalHistory = [];

        for ($i = 1; $i <= self::MEDICAL_HISTORY_QUESTIONS; $i++) {
            $medicalHistory["mhq_{$i}"] = $request->input("mhq_{$i}");

            if (isset(self::MEDICAL_HISTORY_ADDITIONAL_FIELDS[$i])) {
                $fieldName = self::MEDICAL_HISTORY_ADDITIONAL_FIELDS[$i];
                if ($request->has($fieldName)) {
                    $medicalHistory[$fieldName] = $request->input($fieldName);
                }
            }
        }

        return $medicalHistory;
    }

    /**
     * Build sexual history data from request.
     */
    private function buildSexualHistoryData(Request $request): array
    {
        $sexualHistory = [];

        for ($i = 1; $i <= self::SEXUAL_HISTORY_QUESTIONS; $i++) {
            if ($i === 3) {
                // Handle checkbox group for question 3
                foreach (self::SEXUAL_HISTORY_Q3_CHECKBOXES as $checkbox) {
                    $sexualHistory[$checkbox] = $request->has($checkbox);
                }
            } else {
                $sexualHistory["shq_{$i}"] = $request->input("shq_{$i}");
            }
        }

        return $sexualHistory;
    }

    /**
     * Build donor infant data from request.
     */
    private function buildDonorInfantData(Request $request): array
    {
        $donorInfant = [];

        for ($i = 1; $i <= self::DONOR_INFANT_QUESTIONS; $i++) {
            $donorInfant["diq_{$i}"] = $request->input("diq_{$i}");

            // Handle additional info fields
            if ($i === 4 && $request->has('diq_4_reason')) {
                $donorInfant['diq_4_reason'] = $request->input('diq_4_reason');
            }
            if ($i === 5 && $request->has('diq_5_reason')) {
                $donorInfant['diq_5_reason'] = $request->input('diq_5_reason');
            }
        }

        return $donorInfant;
    }

    /**
     * Show the health screening review page.
     */
    public function showReview(): View|RedirectResponse
    {
        if (!$this->isUserAuthenticated()) {
            return redirect()->route('user-login')
                ->with('error', 'Please login to access this page.');
        }

        if (!$this->hasHealthScreeningData()) {
            return redirect()->route('dashboard')
                ->with('error', 'No health screening data found. Please fill out the form first.');
        }

        return view('user.health-screening-review');
    }

    /**
     * Get health screening review data.
     */
    public function getReviewData(): JsonResponse
    {
        if (!$this->isUserAuthenticated()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to access this page.',
            ], 401);
        }

        if (!$this->hasHealthScreeningData()) {
            return response()->json([
                'success' => false,
                'message' => 'No health screening data found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => session(self::SESSION_HEALTH_SCREENING_DATA),
        ]);
    }

    /**
     * Check if health screening data exists in session.
     */
    private function hasHealthScreeningData(): bool
    {
        return (bool) session(self::SESSION_HEALTH_SCREENING_DATA);
    }

    /**
     * Final submission of health screening after review.
     */
    public function finalSubmit(Request $request): JsonResponse|RedirectResponse
    {
        if (!$this->isUserAuthenticated()) {
            return redirect()->route('user-login')
                ->with('error', 'Please login to submit health screening.');
        }

        if (!$this->hasHealthScreeningData()) {
            return redirect()->route('dashboard')
                ->with('error', 'No health screening data found. Please fill out the form first.');
        }

        try {
            $userId = (int) session(self::SESSION_USER_ID);
            $healthScreeningData = session(self::SESSION_HEALTH_SCREENING_DATA);

            if ($this->userHasExistingScreening($userId)) {
                return redirect()->back()
                    ->with('error', 'You have already submitted a health screening. Only one screening is allowed per user.');
            }

            $healthScreening = $this->createHealthScreening($userId, $healthScreeningData);
            $this->saveHealthScreeningAnswers($healthScreening->Health_Screening_ID, $healthScreeningData);
            $this->createAdminNotification();
            
            session()->forget(self::SESSION_HEALTH_SCREENING_DATA);

            $successMessage = 'Health screening submitted successfully! Please wait for admin review.';

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                ]);
            }

            return redirect()->route('dashboard')->with('success', $successMessage);

        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while submitting your health screening. Please try again.');
        }
    }

    /**
     * Check if user already has an existing health screening.
     */
    private function userHasExistingScreening(int $userId): bool
    {
        return DB::table('health_screenings')
            ->where('User_ID', $userId)
            ->exists();
    }

    /**
     * Create health screening record.
     */
    private function createHealthScreening(int $userId, array $healthScreeningData): HealthScreening
    {
        return HealthScreening::create([
            'User_ID' => $userId,
            'civil_status' => $healthScreeningData['civil_status'],
            'occupation' => $healthScreeningData['occupation'],
            'type_of_donor' => $healthScreeningData['type_of_donor'],
            'status' => HealthScreening::STATUS_PENDING,
        ]);
    }

    /**
     * Save all health screening answers.
     */
    private function saveHealthScreeningAnswers(int $screeningId, array $healthScreeningData): void
    {
        $this->saveMedicalHistoryAnswers($screeningId, $healthScreeningData['medical_history']);
        $this->saveSexualHistoryAnswers($screeningId, $healthScreeningData['sexual_history']);
        $this->saveDonorInfantAnswers($screeningId, $healthScreeningData['donor_infant']);
    }

    /**
     * Save medical history answers.
     */
    private function saveMedicalHistoryAnswers(int $screeningId, array $medicalHistory): void
    {
        for ($i = 1; $i <= self::MEDICAL_HISTORY_QUESTIONS; $i++) {
            $answer = $medicalHistory["mhq_{$i}"] ?? 'no';
            $additionalInfo = null;

            if (isset(self::MEDICAL_HISTORY_ADDITIONAL_FIELDS[$i])) {
                $fieldName = self::MEDICAL_HISTORY_ADDITIONAL_FIELDS[$i];
                $additionalInfo = $medicalHistory[$fieldName] ?? null;
            }

            MedicalHistoryAnswer::create([
                'health_screening_id' => $screeningId,
                'question_number' => $i,
                'answer' => $answer,
                'additional_info' => $additionalInfo,
            ]);
        }
    }

    /**
     * Save sexual history answers.
     */
    private function saveSexualHistoryAnswers(int $screeningId, array $sexualHistory): void
    {
        for ($i = 1; $i <= self::SEXUAL_HISTORY_QUESTIONS; $i++) {
            if ($i === 3) {
                [$answer, $additionalInfo] = $this->processSexualHistoryQuestion3($sexualHistory);
            } else {
                $answer = $sexualHistory["shq_{$i}"] ?? 'no';
                $additionalInfo = null;
            }

            SexualHistoryAnswer::create([
                'health_screening_id' => $screeningId,
                'question_number' => $i,
                'answer' => $answer,
                'additional_info' => $additionalInfo,
            ]);
        }
    }

    /**
     * Process sexual history question 3 checkboxes.
     */
    private function processSexualHistoryQuestion3(array $sexualHistory): array
    {
        $selectedOptions = [];

        foreach (self::SEXUAL_HISTORY_Q3_CHECKBOXES as $checkbox) {
            if (isset($sexualHistory[$checkbox]) && $sexualHistory[$checkbox]) {
                $selectedOptions[] = str_replace('shq_3_', '', $checkbox);
            }
        }

        $answer = empty($selectedOptions) ? 'no' : 'yes';
        $additionalInfo = empty($selectedOptions) ? null : implode(', ', $selectedOptions);

        return [$answer, $additionalInfo];
    }

    /**
     * Save donor infant answers.
     */
    private function saveDonorInfantAnswers(int $screeningId, array $donorInfant): void
    {
        for ($i = 1; $i <= self::DONOR_INFANT_QUESTIONS; $i++) {
            $answer = $donorInfant["diq_{$i}"] ?? 'no';
            $additionalInfo = null;

            if ($i === 4 && isset($donorInfant['diq_4_reason'])) {
                $additionalInfo = $donorInfant['diq_4_reason'];
            } elseif ($i === 5 && isset($donorInfant['diq_5_reason'])) {
                $additionalInfo = $donorInfant['diq_5_reason'];
            }

            DonorInfantAnswer::create([
                'health_screening_id' => $screeningId,
                'question_number' => $i,
                'answer' => $answer,
                'additional_info' => $additionalInfo,
            ]);
        }
    }

    /**
     * Create notification for admin about new submission.
     */
    private function createAdminNotification(): void
    {
        DB::table('notifications')->insert([
            'type' => self::NOTIFICATION_TYPE_SUBMITTED,
            'title' => 'New Health Screening Submitted',
            'message' => 'A new health screening has been submitted and requires review.',
            'is_read' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Update health screening status (admin function).
     */
    public function updateStatus(Request $request, int $screeningId): JsonResponse
    {
        try {
            $request->validate([
                'status' => 'required|in:' . implode(',', [HealthScreening::STATUS_ACCEPTED, HealthScreening::STATUS_DECLINED]),
                'admin_notes' => 'nullable|string',
            ]);

            $this->updateScreeningStatus($screeningId, $request->status, $request->admin_notes);
            $this->createUserNotification($screeningId, $request->status, $request->admin_notes);

            return response()->json([
                'success' => true,
                'message' => 'Health screening status updated successfully',
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the status',
            ], 500);
        }
    }

    /**
     * Update screening status in database.
     */
    private function updateScreeningStatus(int $screeningId, string $status, ?string $adminNotes): void
    {
        DB::table('health_screenings')
            ->where('Health_Screening_ID', $screeningId)
            ->update([
                'status' => $status,
                'admin_notes' => $adminNotes,
                'updated_at' => now(),
            ]);
    }

    /**
     * Create notification for user about status update.
     */
    private function createUserNotification(int $screeningId, string $status, ?string $adminNotes): void
    {
        $screening = DB::table('health_screenings')
            ->where('Health_Screening_ID', $screeningId)
            ->first();

        if (!$screening) {
            return;
        }

        $message = "Your health screening has been {$status}.";
        if ($adminNotes) {
            $message .= " Notes: {$adminNotes}";
        }

        DB::table('notifications')->insert([
            'User_ID' => $screening->User_ID,
            'type' => self::NOTIFICATION_TYPE_RESULT,
            'title' => 'Health Screening Result',
            'message' => $message,
            'is_read' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Get detailed health screening information.
     */
    public function getScreeningDetails(int $screeningId): JsonResponse
    {
        try {
            $screening = HealthScreening::with([
                'user',
                'medicalHistoryAnswers',
                'sexualHistoryAnswers',
                'donorInfantAnswers',
            ])->find($screeningId);

            if (!$screening) {
                return response()->json([
                    'success' => false,
                    'message' => 'Health screening not found',
                ], 404);
            }

            $screeningData = $this->formatScreeningData($screening);

            return response()->json([
                'success' => true,
                'screening' => (object) $screeningData,
                'medical_history' => $screening->medicalHistoryAnswers,
                'sexual_history' => $screening->sexualHistoryAnswers,
                'donor_infant' => $screening->donorInfantAnswers,
                'infants' => $screening->user->infants,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching screening details',
            ], 500);
        }
    }

    /**
     * Format screening data for response.
     */
    private function formatScreeningData(HealthScreening $screening): array
    {
        $screeningData = $screening->toArray();
        $screeningData['Full_Name'] = $screening->user->Full_Name;
        $screeningData['Contact_Number'] = $screening->user->Contact_Number;

        return $screeningData;
    }

    /**
     * Check if user has existing health screening.
     */
    public function checkExisting(): JsonResponse
    {
        try {
            $userId = session(self::SESSION_USER_ID);
            
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                ], 401);
            }

            $screening = DB::table('health_screenings')
                ->where('User_ID', $userId)
                ->first();

            if ($screening) {
                return response()->json([
                    'success' => true,
                    'hasExisting' => true,
                    'status' => $screening->status,
                    'admin_notes' => $screening->admin_notes,
                    'created_at' => $screening->created_at,
                    'updated_at' => $screening->updated_at,
                    'created_at_iso' => $screening->created_at 
                        ? Carbon::parse($screening->created_at)->toIso8601String() 
                        : null,
                    'updated_at_iso' => $screening->updated_at 
                        ? Carbon::parse($screening->updated_at)->toIso8601String() 
                        : null,
                ]);
            }

            return response()->json([
                'success' => true,
                'hasExisting' => false,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while checking health screening status',
            ], 500);
        }
    }
}
