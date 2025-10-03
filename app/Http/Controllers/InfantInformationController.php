<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Infant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;

class InfantInformationController extends Controller
{
    public function infant_information(){
        // Check if user data exists in session
        if (!session('user_data')) {
            return redirect('/create-account')->with('error', 'Please complete account information first.');
        }
        return view('user.infant-information');
    }

    public function store(Request $request)
    {
        // Sanitize names to remove special characters
        $request->merge([
            'first_name' => preg_replace('/[<>=\'"]/', '', $request->input('first_name')),
            'last_name' => preg_replace('/[<>=\'"]/', '', $request->input('last_name')),
            'middle_name' => preg_replace('/[<>=\'"]/', '', $request->input('middle_name')),
        ]);

        // Validate the request
        $request->validate([
            'first_name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'last_name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'middle_name' => 'nullable|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'gender' => 'required|in:male,female',
            'birthday' => 'required|date',
            'age' => 'required|integer|min:0|max:1440', // Age in months (0-1440 months = 0-120 years)
            'birth_weight' => 'required|numeric|min:0.5|max:7', // Birth weight in kilograms
        ], [
            'first_name.regex' => 'First name can only contain letters and spaces. Special characters like <, >, =, \', " are not allowed.',
            'last_name.regex' => 'Last name can only contain letters and spaces. Special characters like <, >, =, \', " are not allowed.',
            'middle_name.regex' => 'Middle name can only contain letters and spaces. Special characters like <, >, =, \', " are not allowed.',
            'age.min' => 'Age must be at least 0 months.',
            'age.max' => 'Age must not exceed 1440 months (120 years).',
            'age.integer' => 'Age must be a valid number in months.',
        ]);

        // Get user data from session
        $userData = session('user_data');
        if (!$userData) {
            return redirect('/create-account')->with('error', 'Please complete account information first.');
        }

        // Create full name for user
        $userFullName = $userData['first_name'] . ' ' . $userData['last_name'];
        if ($userData['middle_name']) {
            $userFullName = $userData['first_name'] . ' ' . $userData['middle_name'] . ' ' . $userData['last_name'];
        }

        // Create full name for infant
        $infantFullName = $request->first_name . ' ' . $request->last_name;
        if ($request->middle_name) {
            $infantFullName = $request->first_name . ' ' . $request->middle_name . ' ' . $request->last_name;
        }

        // Create user in database (Password mutator will hash automatically)
        $user = User::create([
            'Contact_Number' => $userData['contact_number'],
            'Full_Name' => $userFullName,
            'Age' => $userData['age'],
            'Address' => $userData['address'],
            'User_Type' => 'donor', // Default to donor
            'Password' => $userData['password'],
            'Date_Of_Birth' => $userData['birthday'],
            'Sex' => ucfirst($userData['gender']),
        ]);

        // Extract numeric age from "X months" format
        $ageInMonths = (int) filter_var($request->age, FILTER_SANITIZE_NUMBER_INT);

    // Normalize birth weight (kg, 2 decimal places)
    $birthWeightKg = round((float) str_replace(',', '.', (string) $request->birth_weight), 2);

        // Create infant in database
        $infant = Infant::create([
            'Full_Name' => $infantFullName,
            'Sex' => ucfirst($request->gender),
            'Date_Of_Birth' => $request->birthday,
            'Age' => $ageInMonths,
            'Birthweight' => $birthWeightKg,
            'User_ID' => $user->User_ID,
        ]);

        // Store user info in session for dashboard
        session([
            'user_id' => $user->User_ID,
            'user_name' => $user->Full_Name,
            'user_type' => $user->User_Type
        ]);

        // Clear the temporary user data
        session()->forget('user_data');

        return redirect('/dashboard')->with('success', 'Registration completed successfully! Welcome to the dashboard.');
    }

    /**
     * Return the current authenticated user's infant info as JSON
     */
    public function getCurrentUserInfant()
    {
        $userId = session('user_id');
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $infant = Infant::where('User_ID', $userId)->first();
        if (!$infant) {
            return response()->json(['success' => true, 'hasInfant' => false]);
        }

        return response()->json([
            'success' => true,
            'hasInfant' => true,
            'infant' => [
                'full_name' => $infant->Full_Name,
                'sex' => $infant->Sex,
                'date_of_birth' => $infant->Date_Of_Birth,
                'age' => $infant->Age,
                'birthweight' => $infant->Birthweight,
            ],
        ]);
    }
}
