<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Breastmilk Donation System</title>
    @include('partials.theme')
    <style>
        /* Layout shell inherits theme gradient from partial */
        .auth-card {
            display: flex;
            width: min(760px, 92vw);
            border-radius: 20px;
            overflow: hidden;
        }

        .auth-left {
            flex: 1 1 40%;
            background: linear-gradient(135deg, var(--pink-100), var(--pink-200));
            position: relative;
            padding: 32px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 16px;
        }

        /* subtle grid dots like the theme */
        .auth-left::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.25) 1px, transparent 1px);
            background-size: 36px 36px;
            pointer-events: none;
        }

        .brand-logo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #fff 0%, #ffeef2 100%);
            border: 3px solid rgba(255, 105, 180, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(255, 105, 180, 0.25);
            margin-bottom: 8px;
        }

        .brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 50%;
        }

        .welcome-eyebrow {
            font-weight: 700;
            letter-spacing: 1px;
            font-size: 14px;
            color: #7a295d;
            text-transform: uppercase;
        }

        .welcome-title {
            font-size: 18px;
            color: #4a4a4a;
            font-weight: 700;
        }

        .welcome-name {
            font-size: 22px;
            line-height: 1.35;
            font-weight: 800;
            margin-top: 4px;
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .welcome-note {
            color: #333;
            opacity: .85;
            font-size: 14px;
            margin-top: 8px;
        }

        .auth-right {
            flex: 1 1 60%;
            background: rgba(255, 255, 255, 0.95);
            padding: 28px 32px;
        }

        .admin-title {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 22px;
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-align: left;
        }

        .form-group {
            margin-bottom: 16px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--text);
            font-weight: 600;
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid rgba(255, 105, 180, 0.2);
            border-radius: 12px;
            font-size: 16px;
            transition: all .2s ease;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(255, 105, 180, 0.15), 0 8px 25px rgba(255, 105, 180, 0.2);
            background: rgba(255, 255, 255, 0.95);
            transform: translateY(-1px);
        }

        .login-btn {
            width: 100%;
            padding: 14px 24px;
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all .2s ease;
            margin-top: 6px;
            box-shadow: 0 8px 25px rgba(255, 105, 180, 0.25);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .login-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 35px rgba(255, 105, 180, 0.35);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .forgot-password {
            color: var(--accent);
            text-decoration: none;
            font-size: 14px;
            margin: 10px 0;
            display: inline-block;
            cursor: pointer;
            transition: all .2s ease;
            padding: 6px 12px;
            border-radius: 8px;
        }

        .forgot-password:hover {
            color: var(--accent-dark);
            background: rgba(255, 105, 180, 0.1);
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 12px;
            border: 1px solid #f5c6cb;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 12px;
            border: 1px solid #c3e6cb;
            position: relative;
        }

        @media (max-width: 900px) {
            .auth-card {
                flex-direction: column;
                width: min(600px, 96vw);
            }

            .auth-left {
                padding: 24px;
            }

            .auth-right {
                padding: 22px;
            }
        }

        @media (max-width: 480px) {
            .brand-logo {
                width: 96px;
                height: 96px;
            }

            .welcome-name {
                font-size: 18px;
            }

            .admin-title {
                font-size: 22px;
            }
        }
    </style>
</head>

<body class="theme-gradient">
    <div class="auth-card card-ui">
        <div class="auth-left">
            <div class="brand-logo">
                <img src="{{ asset('logo.png') }}" alt="Cagayan de Oro City Human Milk Bank Logo">
            </div>
            <div class="welcome-eyebrow">Welcome to</div>
            <div class="welcome-name">Cagayan de Oro City - Human Milk Bank &amp; Lactation Support Center</div>
            <div class="welcome-note">Please sign in to continue to the administrator dashboard.</div>
        </div>

        <div class="auth-right">
            <div class="admin-title">Admin Login</div>

            @if(session('success'))
                <div id="success-message" class="success-message"
                    style="transition: opacity 0.5s ease-out, transform 0.5s ease-out;">
                    <button onclick="hideSuccessMessage()"
                        style="position: absolute; top: 5px; right: 10px; background: none; border: none; font-size: 16px; color: #155724; cursor: pointer; font-weight: bold;">&times;</button>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="error-message">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.authenticate') }}">
                @csrf
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" value="{{ old('username') }}"
                        required>
                    @error('username')
                        <span style="color: red; font-size: 0.8rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Password"
                        required>
                    @error('password')
                        <span style="color: red; font-size: 0.8rem;">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="login-btn">Log-In</button>
            </form>

            <div style="text-align: left; margin-top: 6px;">
                <a href="{{ route('admin.clear-session') }}"
                    style="color: #ff69b4; text-decoration: underline; font-size: 14px;">← Back to User Login</a>
            </div>
        </div>
    </div>

    <script>
        // Auto-hide success message after 5 seconds
        document.addEventListener('DOMContentLoaded', function () {
            const successMessage = document.getElementById('success-message');
            if (successMessage) {
                setTimeout(function () { hideSuccessMessage(); }, 5000);
            }
        });

        function hideSuccessMessage() {
            const successMessage = document.getElementById('success-message');
            if (successMessage) {
                successMessage.style.opacity = '0';
                successMessage.style.transform = 'translateY(-12px)';
                setTimeout(function () { successMessage.style.display = 'none'; }, 500);
            }
        }
    </script>
</body>

</html>