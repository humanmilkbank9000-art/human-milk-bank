<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.theme')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #ffd6e1 0%, #ffb6ce 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: auto;
            padding: 24px;
        }

        body::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0) rotate(0);
            }

            50% {
                transform: translateY(-20px) rotate(180deg);
            }
        }

        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(255, 255, 255, 0.2);
            width: 760px;
            max-width: 92vw;
            border: 1px solid rgba(255, 182, 206, 0.3);
            position: relative;
            z-index: 1;
        }

        .card-header {
            display: flex;
            gap: 16px;
            align-items: center;
            padding: 28px 32px 0 32px;
        }

        .logo {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: linear-gradient(135deg, #fff 0%, #ffeef2 100%);
            border: 3px solid rgba(255, 105, 180, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(255, 105, 180, 0.2);
        }

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 50%;
        }

        .title {
            font-size: 26px;
            font-weight: 700;
            background: linear-gradient(135deg, #ff69b4, #d63384);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .card-body {
            padding: 24px 32px 32px 32px;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .full {
            grid-column: 1 / -1;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-label {
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }

        .input {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid rgba(255, 105, 180, 0.2);
            border-radius: 12px;
            font-size: 14px;
            background: rgba(255, 255, 255, 0.8);
            transition: all .2s ease;
        }

        .input:focus {
            outline: none;
            border-color: #ff69b4;
            box-shadow: 0 0 0 4px rgba(255, 105, 180, 0.15), 0 8px 25px rgba(255, 105, 180, 0.2);
            background: rgba(255, 255, 255, 0.95);
            transform: translateY(-1px);
        }

        .radio-row {
            display: flex;
            gap: 18px;
            align-items: center;
        }

        .error {
            color: #dc3545;
            font-size: 12px;
        }

        .alert {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            border: 1px solid #f5c6cb;
            font-size: 14px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            border: 1px solid #c3e6cb;
            font-size: 14px;
            position: relative;
        }

        .input-wrap {
            position: relative;
        }

        .toggle-eye {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #6c757d;
            padding: 2px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .toggle-eye:hover {
            color: #343a40;
        }

        .toggle-eye svg {
            width: 20px;
            height: 20px;
        }

        .icon-hidden {
            display: none;
        }

        .btn-row {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 8px;
        }

        .btn {
            padding: 12px 18px;
            border: none;
            border-radius: 12px;
            color: #fff;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            transition: all .2s;
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6c757d, #495057);
        }

        .btn-primary {
            background: linear-gradient(135deg, #ff69b4, #d63384);
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        @media (max-width: 860px) {
            .grid {
                grid-template-columns: 1fr;
            }

            .card {
                width: 100%;
            }

            .card-header {
                padding: 20px 20px 0 20px;
            }

            .card-body {
                padding: 20px;
            }
        }
    </style>
</head>

<body class="theme-gradient">
    <div class="card">
        <div class="card-header">
            <div class="logo">
                <img src="{{ asset('logo.png') }}" alt="Cagayan de Oro City Human Milk Bank Logo">
            </div>
            <div class="title">Create Account</div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div id="success-message" class="success">
                    <button onclick="hideSuccessMessage()"
                        style="position:absolute; top: 5px; right: 10px; background:none; border:none; font-size:16px; color:#155724; cursor:pointer; font-weight:bold;">&times;</button>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert">{{ session('error') }}</div>
            @endif

            <form id="createAccountForm" method="POST" action="{{ route('create-account.store') }}"
                onsubmit="submitForm()">
                @csrf
                <div class="grid">
                    <div class="form-group">
                        <label class="form-label">First Name</label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" class="input" required
                            oninput="validateAndCapitalizeName(this)" pattern="[a-zA-Z\s]+"
                            title="Only letters and spaces are allowed">
                        @error('first_name')<span class="error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" class="input" required
                            oninput="validateAndCapitalizeName(this)" pattern="[a-zA-Z\s]+"
                            title="Only letters and spaces are allowed">
                        @error('last_name')<span class="error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Middle Name (optional)</label>
                        <input type="text" name="middle_name" value="{{ old('middle_name') }}" class="input"
                            oninput="validateAndCapitalizeName(this)" pattern="[a-zA-Z\s]+"
                            title="Only letters and spaces are allowed">
                        @error('middle_name')<span class="error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Gender</label>
                        <div class="radio-row">
                            <label><input type="radio" name="gender" value="female" {{ old('gender') == 'female' ? 'checked' : '' }} required> Female</label>
                            <label><input type="radio" name="gender" value="male" {{ old('gender') == 'male' ? 'checked' : '' }}> Male</label>
                        </div>
                        @error('gender')<span class="error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Birthday</label>
                        <input type="date" id="birthday" name="birthday" value="{{ old('birthday') }}" class="input"
                            required>
                        @error('birthday')<span class="error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Age</label>
                        <input type="number" id="age" name="age" value="{{ old('age') }}" class="input" readonly min="0"
                            max="120" step="1" oninput="validateAge(this)"
                            oninvalid="this.setCustomValidity('Age must be between 0 and 120 years')">
                        <div id="age_alert" class="error" style="display:none;">Age must be between 0 and 120 years.
                        </div>
                        @error('age')<span class="error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group full">
                        <label class="form-label">Contact Number</label>
                        <input type="tel" class="input" name="contact_number" value="{{ old('contact_number') }}"
                            required inputmode="numeric" pattern="^\d{11}$" title="Enter exactly 11 digits"
                            oninput="this.value=this.value.replace(/[^0-9]/g,''); if (this.value.length>11 || (this.value.length>0 && this.value.length<11)) { this.setCustomValidity('Contact number must be exactly 11 digits'); this.nextElementSibling.style.display='block'; } else { this.setCustomValidity(''); this.nextElementSibling.style.display='none'; }"
                            oninvalid="this.setCustomValidity('Contact number must be exactly 11 digits')">
                        <div class="error" style="display:none;">Contact number must be exactly 11 digits.</div>
                        @error('contact_number')<span class="error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group full">
                        <label class="form-label">Address</label>
                        <input type="text" class="input" name="address" value="{{ old('address') }}" required
                            maxlength="500" pattern="^[a-zA-Z0-9\s,.\-#]+$"
                            title="Address can only contain letters, numbers, spaces, and common symbols (,.-#)"
                            oninput="validateAddress(this)"
                            oninvalid="this.setCustomValidity('Address can only contain letters, numbers, spaces, and common symbols (,.-#)')">
                        <div id="address_alert" class="error" style="display:none;">Address can only contain letters,
                            numbers, spaces, and common symbols (,.-#). Maximum 500 characters.</div>
                        @error('address')<span class="error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <div class="input-wrap">
                            <input type="password" class="input" name="password" id="password" required
                                pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,64}"
                                title="8-64 chars, include upper, lower, number, special" oninput="
                                 const el=this; const alertEl=document.getElementById('password_alert');
                                 const ok=/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,64}$/.test(el.value);
                                 el.setCustomValidity(ok ? '' : 'Password must be 8-64 chars and include upper, lower, number, and special character.');
                                 if(alertEl){alertEl.style.display = ok ? 'none' : 'block';}
                                 const c=document.getElementById('confirm_password');
                                 if(c){ const cm=document.getElementById('confirm_password_alert'); const mismatch=(c.value && c.value!==el.value); c.setCustomValidity(mismatch ? 'Confirm password must match' : ''); if(cm){ cm.style.display = mismatch ? 'block' : 'none'; } }
                               "
                                oninvalid="this.setCustomValidity('Password must be 8-64 chars and include upper, lower, number, and special character.')">
                            <button type="button" class="toggle-eye" aria-label="Toggle password visibility"
                                onclick="togglePassword('password', this)">
                                <svg class="icon-eye" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <svg class="icon-eye-off icon-hidden" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" aria-hidden="true">
                                    <path
                                        d="M17.94 17.94A10.94 10.94 0 0112 20c-7 0-11-8-11-8a21.77 21.77 0 015.06-6.94">
                                    </path>
                                    <path d="M9.9 4.24A10.94 10.94 0 0112 4c7 0 11 8 11 8a21.82 21.82 0 01-3.87 5.14">
                                    </path>
                                    <line x1="1" y1="1" x2="23" y2="23"></line>
                                </svg>
                            </button>
                        </div>
                        <div id="password_alert" class="error" style="display:none;">Password must be 8-64 chars and
                            include upper, lower, number, and special character.</div>
                        @error('password')<span class="error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirm Password</label>
                        <div class="input-wrap">
                            <input type="password" class="input" name="confirm_password" id="confirm_password" required
                                oninput="const p=document.getElementById('password'); const cm=document.getElementById('confirm_password_alert'); const mismatch=(this.value && p && this.value!==p.value); this.setCustomValidity(mismatch ? 'Confirm password must match' : ''); if(cm){ cm.style.display = mismatch ? 'block' : 'none'; }"
                                oninvalid="this.setCustomValidity('Confirm password must match')">
                            <button type="button" class="toggle-eye" aria-label="Toggle password visibility"
                                onclick="togglePassword('confirm_password', this)">
                                <svg class="icon-eye" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <svg class="icon-eye-off icon-hidden" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" aria-hidden="true">
                                    <path
                                        d="M17.94 17.94A10.94 10.94 0 0112 20c-7 0-11-8-11-8a21.77 21.77 0 015.06-6.94">
                                    </path>
                                    <path d="M9.9 4.24A10.94 10.94 0 0112 4c7 0 11 8 11 8a21.82 21.82 0 01-3.87 5.14">
                                    </path>
                                    <line x1="1" y1="1" x2="23" y2="23"></line>
                                </svg>
                            </button>
                        </div>
                        <div id="confirm_password_alert" class="error" style="display:none;">Confirm password must
                            match.</div>
                        @error('confirm_password')<span class="error">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="btn-row">
                    <button type="button" class="btn btn-secondary" onclick="goBack()">Back</button>
                    <button type="submit" class="btn btn-primary">Next</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let formHasData = false;

        // Function to calculate age from birthday
        function calculateAge(birthDate) {
            const today = new Date();
            const birth = new Date(birthDate);
            let age = today.getFullYear() - birth.getFullYear();
            const monthDiff = today.getMonth() - birth.getMonth();

            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
                age--;
            }

            // Ensure age is within valid range (0-120)
            return Math.max(0, Math.min(120, age));
        }

        // Function to validate age input
        function validateAge(input) {
            const age = parseInt(input.value);
            const alertEl = document.getElementById('age_alert');

            if (isNaN(age) || age < 0 || age > 120) {
                input.setCustomValidity('Age must be between 0 and 120 years');
                if (alertEl) alertEl.style.display = 'block';
                return false;
            } else {
                input.setCustomValidity('');
                if (alertEl) alertEl.style.display = 'none';
                return true;
            }
        }

        // Function to validate address input
        function validateAddress(input) {
            const address = input.value;
            const alertEl = document.getElementById('address_alert');

            // Check length
            if (address.length > 500) {
                input.setCustomValidity('Address must not exceed 500 characters');
                if (alertEl) alertEl.style.display = 'block';
                return false;
            }

            // Check pattern - allow alphanumeric, spaces, and common symbols (,.-#)
            const addressPattern = /^[a-zA-Z0-9\s,.\-#]*$/;
            if (address && !addressPattern.test(address)) {
                input.setCustomValidity('Address can only contain letters, numbers, spaces, and common symbols (,.-#)');
                if (alertEl) alertEl.style.display = 'block';
                return false;
            }

            // Clear validation if valid
            input.setCustomValidity('');
            if (alertEl) alertEl.style.display = 'none';
            return true;
        }

        // Add event listener to birthday field
        document.getElementById('birthday').addEventListener('change', function () {
            const birthday = this.value;
            const ageField = document.getElementById('age');

            if (birthday) {
                const age = calculateAge(birthday);
                ageField.value = age;
                // Validate the calculated age
                validateAge(ageField);
            } else {
                ageField.value = '';
                // Clear validation when no birthday
                ageField.setCustomValidity('');
                const alertEl = document.getElementById('age_alert');
                if (alertEl) alertEl.style.display = 'none';
            }
        });

        // Check if form has data
        function checkFormData() {
            const form = document.getElementById('createAccountForm');
            const inputs = form.querySelectorAll('input[type="text"], input[type="password"], input[type="date"]');
            const radios = form.querySelectorAll('input[type="radio"]:checked');

            for (let input of inputs) {
                if (input.value.trim() !== '') {
                    return true;
                }
            }

            return radios.length > 0;
        }

        // Add event listeners to all form inputs
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('createAccountForm');
            const inputs = form.querySelectorAll('input[type="text"], input[type="password"], input[type="date"]');
            const radios = form.querySelectorAll('input[type="radio"]');

            // Monitor text inputs
            inputs.forEach(input => {
                input.addEventListener('input', function () {
                    formHasData = checkFormData();
                });
            });

            // Monitor radio buttons
            radios.forEach(radio => {
                radio.addEventListener('change', function () {
                    formHasData = checkFormData();
                });
            });

            // Auto-hide success message after 5 seconds
            const successMessage = document.getElementById('success-message');
            if (successMessage) {
                setTimeout(function () {
                    hideSuccessMessage();
                }, 5000); // 5 seconds
            }
        });

        // Function to hide success message with animation
        function hideSuccessMessage() {
            const successMessage = document.getElementById('success-message');
            if (successMessage) {
                successMessage.style.opacity = '0';
                successMessage.style.transform = 'translateY(-20px)';
                setTimeout(function () {
                    successMessage.style.display = 'none';
                }, 500); // Wait for animation to complete
            }
        }

        // Add flag to track intentional navigation
        let isIntentionalNavigation = false;

        // Function to go back to login page with warning
        function goBack() {
            if (formHasData) {
                const confirmed = confirm('You have entered data in the form. Are you sure you want to go back? Your data will be lost.');
                if (confirmed) {
                    isIntentionalNavigation = true;
                    window.location.href = '/';
                }
            } else {
                isIntentionalNavigation = true;
                window.location.href = '/';
            }
        }

        // Function to handle form submission
        function submitForm() {
            isIntentionalNavigation = true;
            return true; // Allow form to submit
        }

        // Warn user when trying to leave the page with data
        window.addEventListener('beforeunload', function (e) {
            if (formHasData && !isIntentionalNavigation) {
                e.preventDefault();
                e.returnValue = 'You have entered data in the form. Are you sure you want to leave?';
                return 'You have entered data in the form. Are you sure you want to leave?';
            }
        });

        // Toggle password visibility
        function togglePassword(id, btn) {
            const input = document.getElementById(id);
            if (!input) return;
            const isPwd = input.type === 'password';
            input.type = isPwd ? 'text' : 'password';
            if (btn) {
                const eye = btn.querySelector('.icon-eye');
                const eyeOff = btn.querySelector('.icon-eye-off');
                if (eye && eyeOff) {
                    if (isPwd) { eye.classList.add('icon-hidden'); eyeOff.classList.remove('icon-hidden'); }
                    else { eye.classList.remove('icon-hidden'); eyeOff.classList.add('icon-hidden'); }
                }
            }
        }

        // Name field validation and capitalization function
        function validateAndCapitalizeName(field) {
            // Remove special characters except letters, spaces, hyphens, and apostrophes
            let sanitizedValue = field.value.replace(/[<>='"]/g, '');

            // Capitalize first letter of each word
            sanitizedValue = sanitizedValue.replace(/\b\w/g, letter => letter.toUpperCase());

            if (field.value !== sanitizedValue) {
                const cursorPosition = field.selectionStart;
                field.value = sanitizedValue;

                // Restore cursor position
                field.setSelectionRange(cursorPosition, cursorPosition);

                // Show warning message for special characters
                if (field.value.replace(/[<>='"]/g, '') !== field.value) {
                    const warningDiv = document.createElement('div');
                    warningDiv.className = 'error';
                    warningDiv.textContent = 'Special characters like <, >, =, \', " are not allowed in names.';
                    warningDiv.style.marginTop = '5px';
                    warningDiv.style.fontSize = '12px';
                    warningDiv.style.color = '#dc3545';

                    // Remove existing warning if any
                    const existingWarning = field.parentElement.querySelector('.name-warning');
                    if (existingWarning) {
                        existingWarning.remove();
                    }

                    warningDiv.className = 'name-warning error';
                    field.parentElement.appendChild(warningDiv);

                    // Remove warning after 3 seconds
                    setTimeout(() => {
                        if (warningDiv.parentElement) {
                            warningDiv.remove();
                        }
                    }, 3000);
                }
            }
        }

        // Keep the old function for compatibility
        function validateNameField(field) {
            validateAndCapitalizeName(field);
        }
    </script>
</body>

</html>