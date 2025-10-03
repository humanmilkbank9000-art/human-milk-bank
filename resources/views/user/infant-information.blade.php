<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.theme')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Infant's Information</title>
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
            width: 900px;
            max-width: 95vw;
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
            <div class="title">Infant's Information</div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div id="success-message"
                    style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 15px; border: 1px solid #c3e6cb; transition: opacity 0.5s ease-out, transform 0.5s ease-out; position: relative;">
                    <button onclick="hideSuccessMessage()"
                        style="position: absolute; top: 5px; right: 10px; background: none; border: none; font-size: 16px; color: #155724; cursor: pointer; font-weight: bold;">&times;</button>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div
                    style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px; border: 1px solid #f5c6cb;">
                    {{ session('error') }}
                </div>
            @endif
            <form id="infantForm" method="POST" action="{{ route('infant-information.store') }}"
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
                            max="1440" step="1" oninput="validateInfantAge(this)"
                            oninvalid="this.setCustomValidity('Age must be between 0 and 1440 months (0-120 years)')">
                        <div id="age_human" style="font-size:12px;color:#555;margin-top:4px;">&nbsp;</div>
                        <div id="age_alert" class="error" style="display:none;">Age must be between 0 and 1440 months
                            (0-120 years).</div>
                        @error('age')<span class="error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group full">
                        <label class="form-label">Birth weight (kg)</label>
                        <input type="number" id="birth_weight" class="input" name="birth_weight"
                            value="{{ old('birth_weight') }}" step="0.01" min="0.5" max="7" inputmode="decimal"
                            placeholder="e.g., 3.2" required oninput="validateBirthWeight(this)"
                            onkeydown="return preventInvalidNumberKeys(event)">
                        <div id="birth_weight_alert" class="error" style="display:none;">Birth weight must be between
                            0.5 and 7.0 kilograms.</div>
                        @error('birth_weight')<span class="error">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="btn-row">
                    <button type="button" class="btn btn-secondary" onclick="goBack()">Back</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let formHasData = false;
        let isIntentionalNavigation = false;

        // Function to calculate age in months from birthday
        function calculateAge(birthDate) {
            const today = new Date();
            const birth = new Date(birthDate);

            let months = (today.getFullYear() - birth.getFullYear()) * 12;
            months += today.getMonth() - birth.getMonth();

            // Adjust if the day hasn't occurred yet this month
            if (today.getDate() < birth.getDate()) {
                months--;
            }

            // Ensure we don't return negative months and cap at 1440 months (120 years)
            return Math.max(0, Math.min(1440, months));
        }

        // Function to validate infant age input (in months)
        function validateInfantAge(input) {
            const age = parseInt(input.value);
            const alertEl = document.getElementById('age_alert');

            if (isNaN(age) || age < 0 || age > 1440) {
                input.setCustomValidity('Age must be between 0 and 1440 months (0-120 years)');
                if (alertEl) alertEl.style.display = 'block';
                return false;
            } else {
                input.setCustomValidity('');
                if (alertEl) alertEl.style.display = 'none';
                return true;
            }
        }

        function formatAgeFromMonths(totalMonths) {
            if (isNaN(totalMonths) || totalMonths < 0) return '';
            const years = Math.floor(totalMonths / 12);
            const months = totalMonths % 12;
            if (years === 0) {
                return months === 1 ? '1 month' : months + ' months';
            }
            if (months === 0) {
                return years === 1 ? '1 year' : years + ' years';
            }
            const yearPart = years === 1 ? '1 year' : years + ' years';
            const monthPart = months === 1 ? '1 month' : months + ' months';
            return yearPart + ' ' + monthPart;
        }

        // Add event listener to birthday field
        document.getElementById('birthday').addEventListener('change', function () {
            const birthday = this.value;
            const ageField = document.getElementById('age');
            const ageHuman = document.getElementById('age_human');

            if (birthday) {
                const ageInMonths = calculateAge(birthday);
                ageField.value = ageInMonths;
                // Validate the calculated age
                validateInfantAge(ageField);
                if (ageHuman) ageHuman.textContent = formatAgeFromMonths(ageInMonths);
            } else {
                ageField.value = '';
                // Clear validation when no birthday
                ageField.setCustomValidity('');
                const alertEl = document.getElementById('age_alert');
                if (alertEl) alertEl.style.display = 'none';
                if (ageHuman) ageHuman.textContent = '\u00A0';
            }
        });

        // Check if form has data
        function checkFormData() {
            const form = document.getElementById('infantForm');
            const inputs = form.querySelectorAll('input[type="text"], input[type="date"]');
            const radios = form.querySelectorAll('input[type="radio"]:checked');

            for (let input of inputs) {
                if (input.value.trim() !== '') {
                    return true;
                }
            }

            return radios.length > 0;
        }

        // Prevent invalid characters in number field
        function preventInvalidNumberKeys(e) {
            const invalidKeys = ['e', 'E', '+', '-'];
            if (invalidKeys.includes(e.key)) {
                e.preventDefault();
                return false;
            }
            return true;
        }

        // Validate birth weight (kilograms) between 0.5 and 7.0 with decimals
        function validateBirthWeight(input) {
            const alertEl = document.getElementById('birth_weight_alert');
            let value = input.value.replace(',', '.');
            const num = parseFloat(value);
            if (isNaN(num)) {
                input.setCustomValidity('Please enter a valid number.');
                if (alertEl) alertEl.style.display = 'block';
                return false;
            }
            if (num < 0.5 || num > 7.0) {
                input.setCustomValidity('Birth weight must be between 0.5 and 7.0 kilograms.');
                if (alertEl) alertEl.style.display = 'block';
                return false;
            }
            // Normalize to one decimal place in UI (optional)
            input.setCustomValidity('');
            if (alertEl) alertEl.style.display = 'none';
            return true;
        }

        // Add event listeners to all form inputs
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('infantForm');
            const inputs = form.querySelectorAll('input[type="text"], input[type="date"]');
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

            // Initialize human-readable age if prefilled
            const ageField = document.getElementById('age');
            const ageHuman = document.getElementById('age_human');
            if (ageField && ageField.value && ageHuman) {
                ageHuman.textContent = formatAgeFromMonths(parseInt(ageField.value, 10));
            }

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

        // Function to go back to create account page with warning
        function goBack() {
            if (formHasData) {
                const confirmed = confirm('You have entered data in the form. Are you sure you want to go back? Your data will be lost.');
                if (confirmed) {
                    isIntentionalNavigation = true;
                    window.location.href = '/create-account';
                }
            } else {
                isIntentionalNavigation = true;
                window.location.href = '/create-account';
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