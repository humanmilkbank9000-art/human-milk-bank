<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Infant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Helpers\AddressValidation;
use Illuminate\Validation\Rules\Password;

class CreateAccountController extends Controller
{
    public function create_account(){
        return view('user.create-account');
    }

    public function store(Request $request)
    {
        // Sanitize contact number to digits only
        $request->merge([
            'contact_number' => preg_replace('/\D+/', '', (string) $request->input('contact_number')),
        ]);

        // Sanitize names and address to remove special characters
        $request->merge([
            'first_name' => preg_replace('/[<>=\'"]/', '', $request->input('first_name')),
            'last_name' => preg_replace('/[<>=\'"]/', '', $request->input('last_name')),
            'middle_name' => preg_replace('/[<>=\'"]/', '', $request->input('middle_name')),
            'address' => AddressValidation::sanitize($request->input('address')),
        ]);

        // Strong password policy and full validation
        $commonPasswords = [
            'password','password1','password123','123456','123456789','qwerty','abc123','111111','123123','letmein','welcome','admin','iloveyou','monkey','dragon'
        ];

        $rules = [
            'first_name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'last_name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'middle_name' => 'nullable|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'gender' => 'required|in:male,female',
            'birthday' => 'required|date',
            'age' => 'required|integer|min:0|max:120',
            'contact_number' => 'required|string|regex:/^\\d{11}$/|unique:users,Contact_Number',
            'address' => AddressValidation::getValidationRules(),
            'password' => [
                'required', 'string', Password::min(8)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols()
                    // Uncomment to also check against known data breaches if internet is available
                    //->uncompromised()
                    ,
                'max:64'
            ],
            'confirm_password' => 'required|same:password',
        ];

        $messages = [
            'first_name.regex' => 'First name can only contain letters and spaces. Special characters like <, >, =, \', " are not allowed.',
            'last_name.regex' => 'Last name can only contain letters and spaces. Special characters like <, >, =, \', " are not allowed.',
            'middle_name.regex' => 'Middle name can only contain letters and spaces. Special characters like <, >, =, \', " are not allowed.',
            'age.min' => 'Age must be at least 0 years.',
            'age.max' => 'Age must not exceed 120 years.',
            'age.integer' => 'Age must be a valid number.',
            'address.max' => AddressValidation::getValidationMessages()['max'],
            'address.regex' => AddressValidation::getValidationMessages()['regex'],
            'contact_number.regex' => 'Contact number must be exactly 11 digits.',
            'contact_number.unique' => 'This phone number is already registered. Please use a different phone number or login with your existing account.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.max' => 'Password must be at most 64 characters.',
            'password.letters' => 'Password must include letters.',
            'password.mixed' => 'Password must include uppercase and lowercase letters.',
            'password.numbers' => 'Password must include numbers.',
            'password.symbols' => 'Password must include at least one special character.',
            'confirm_password.same' => 'Confirm password must match the password.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        $validator->after(function ($validator) use ($request, $commonPasswords) {
            $pwd = strtolower((string) $request->input('password'));
            if (in_array($pwd, $commonPasswords, true)) {
                $validator->errors()->add('password', 'This password is too common. Please choose a more secure password.');
            }
        });

        $validator->validate();

        // Store user data in session for later processing (hash the password to avoid storing plaintext)
        session([
            'user_data' => [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'middle_name' => $request->middle_name,
                'gender' => $request->gender,
                'birthday' => $request->birthday,
                'age' => $request->age,
                'contact_number' => $request->contact_number,
                'address' => $request->address,
                'password' => Hash::make($request->password),
            ]
        ]);

        return redirect('/infant-information')->with('success', 'Account information saved. Please complete infant information.');
    }
}


