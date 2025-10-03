<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Settings</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body {
            font-family: system-ui, Arial, sans-serif;
            background: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .settings-wrapper {
            max-width: 720px;
            margin: 40px auto;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 32px 36px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, .05);
        }

        h1 {
            font-size: 24px;
            margin: 0 0 24px;
            letter-spacing: .5px;
        }

        .flash {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 18px;
            font-size: 14px;
            font-weight: 600;
        }

        .flash.success {
            background: #e6ffed;
            border: 1px solid #8ce0a1;
            color: #116329;
        }

        .flash.error {
            background: #ffecec;
            border: 1px solid #ffb2b2;
            color: #9d2222;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        label {
            font-size: 13px;
            font-weight: 600;
            color: #444;
            letter-spacing: .5px;
        }

        input[type=password] {
            padding: 12px 14px;
            border: 1px solid #d0d7de;
            border-radius: 8px;
            font-size: 14px;
            background: #fff;
            transition: border-color .2s, box-shadow .2s;
        }

        input[type=password]:focus {
            outline: none;
            border-color: #ff69b4;
            box-shadow: 0 0 0 3px rgba(255, 105, 180, .25);
        }

        .form-note {
            font-size: 12px;
            color: #666;
        }

        button {
            cursor: pointer;
            padding: 12px 20px;
            background: linear-gradient(135deg, #ff9ac9, #ff5fae);
            border: none;
            color: #fff;
            font-weight: 600;
            font-size: 14px;
            border-radius: 8px;
            letter-spacing: .5px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 10px rgba(255, 105, 180, .35);
            transition: transform .15s, box-shadow .2s;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(255, 105, 180, .45);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            text-decoration: none;
            font-weight: 600;
            color: #ff4d91;
            margin-bottom: 24px;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .invalid-feedback {
            color: #c92a2a;
            font-size: 12px;
            margin-top: 4px;
        }

        @media (max-width:640px) {
            .settings-wrapper {
                margin: 20px 12px;
                padding: 24px 22px;
            }
        }
    </style>
</head>

<body>
    <div class="settings-wrapper">
        <a href="{{ route('dashboard') }}" class="back-link">&larr; Back to Dashboard</a>
        <h1>Account Settings</h1>
        @if(session('success'))
            <div class="flash success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="flash error">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="flash error" style="display:block;">
                <ul style="margin:0; padding-left:20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('user.settings.password') }}">
            @csrf
            <div class="field">
                <label for="current_password">Current Password</label>
                <input type="password" name="current_password" id="current_password" required
                    autocomplete="current-password">
            </div>
            <div class="field">
                <label for="new_password">New Password</label>
                <input type="password" name="new_password" id="new_password" required minlength="8"
                    autocomplete="new-password">
                <p class="form-note">Minimum 8 characters. Use a mix of letters, numbers, and symbols for a stronger
                    password.</p>
            </div>
            <div class="field">
                <label for="new_password_confirmation">Confirm New Password</label>
                <input type="password" name="new_password_confirmation" id="new_password_confirmation" required
                    autocomplete="new-password">
            </div>
            <div style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
                <button type="submit">Update Password</button>
            </div>
        </form>
    </div>
</body>

</html>