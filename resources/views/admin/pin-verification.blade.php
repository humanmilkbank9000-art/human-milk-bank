<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin PIN Verification - Breastmilk Donation System</title>
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
            overflow: hidden;
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
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        .pin-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(255, 255, 255, 0.2);
            padding: 35px 40px;
            width: 420px;
            max-width: 90vw;
            text-align: center;
            position: relative;
            z-index: 1;
            border: 1px solid rgba(255, 182, 206, 0.3);
        }

        .logo-container {
            margin-bottom: 30px;
        }

        .logo {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            background: linear-gradient(135deg, #fff 0%, #ffeef2 100%);
            border: 3px solid rgba(255, 105, 180, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px auto;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(255, 105, 180, 0.2);
            transition: transform 0.3s ease;
        }

        .logo:hover {
            transform: scale(1.05);
        }

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 50%;
        }

        .admin-title {
            font-size: 26px;
            font-weight: 700;
            background: linear-gradient(135deg, #ff69b4, #d63384);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .pin-subtitle {
            font-size: 16px;
            color: #666;
            margin-bottom: 35px;
            font-weight: 400;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 16px 20px;
            border: 2px solid rgba(255, 105, 180, 0.2);
            border-radius: 12px;
            font-size: 24px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
            text-align: center;
            letter-spacing: 12px;
            font-weight: 600;
            backdrop-filter: blur(10px);
        }

        .form-control:focus {
            outline: none;
            border-color: #ff69b4;
            box-shadow: 0 0 0 4px rgba(255, 105, 180, 0.15), 0 8px 25px rgba(255, 105, 180, 0.2);
            background: rgba(255, 255, 255, 0.95);
            transform: translateY(-2px);
        }

        .verify-btn {
            width: 100%;
            padding: 14px 24px;
            background: linear-gradient(135deg, #ff69b4, #d63384);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 15px;
            box-shadow: 0 8px 25px rgba(255, 105, 180, 0.3);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .verify-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(255, 105, 180, 0.4);
        }

        .verify-btn:active {
            transform: translateY(0);
        }

        .back-link {
            color: #ff69b4;
            text-decoration: underline;
            font-size: 14px;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .back-link:hover {
            color: #d63384;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            border: 1px solid #f5c6cb;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            border: 1px solid #c3e6cb;
        }

        @media (max-width: 480px) {
            .pin-container {
                padding: 30px 20px;
                width: 95vw;
            }

            .logo {
                width: 100px;
                height: 100px;
            }

            .admin-title {
                font-size: 20px;
            }
        }
    </style>
</head>
<body class="theme-gradient">
    <div class="pin-container">
        <div class="logo-container">
            <div class="logo">
                <img src="{{ asset('logo.png') }}" alt="Cagayan de Oro City Human Milk Bank Logo">
            </div>
            <div class="admin-title">Verify PIN</div>
            <div class="pin-subtitle">Credentials accepted. Enter your security PIN to finish login.</div>
        </div>

        @if(session('success'))
            <div class="success-message">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="error-message">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.pin.verify') }}">
            @csrf
            <div class="form-group">
                <label for="pin">PIN Code</label>
                <input type="password" id="pin" name="pin" class="form-control" 
                       placeholder="••••" maxlength="4" required>
                @error('pin')
                    <span style="color: red; font-size: 0.8rem;">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="verify-btn">Verify PIN</button>
        </form>

    <div class="back-link" onclick="window.location.href='{{ route('admin.login') }}'">← Back to Login</div>
    </div>

    <script>
        // Auto-focus on PIN input
        document.getElementById('pin').focus();
        
        // Only allow numbers
        document.getElementById('pin').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
</body>
</html>
