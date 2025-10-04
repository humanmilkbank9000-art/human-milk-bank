<!DOCTYPE html>
<html lang="en">
<head>
  @include('partials.theme')
  <meta charset="UTF-8">
  <title>User Login - Breastmilk Donation System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    /* Two-panel auth card layout */
  .auth-card { display: flex; width: min(760px, 92vw); border-radius: 20px; overflow: hidden; }
  .auth-left { flex: 1 1 40%; background: linear-gradient(135deg, var(--pink-100), var(--pink-200)); position: relative; padding: 32px; display: flex; flex-direction: column; justify-content: center; gap: 12px; }
    .auth-left::before { content: ''; position: absolute; inset: 0; background: radial-gradient(circle, rgba(255,255,255,0.25) 1px, transparent 1px); background-size: 36px 36px; pointer-events: none; }
    .brand-logo { width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, #fff 0%, #ffeef2 100%); border: 3px solid rgba(255,105,180,0.25); display: flex; align-items: center; justify-content: center; overflow: hidden; box-shadow: 0 10px 30px rgba(255,105,180,0.25); margin-bottom: 6px; }
    .brand-logo img { width: 100%; height: 100%; object-fit: contain; border-radius: 50%; }
    .welcome-eyebrow { font-weight: 700; letter-spacing: 1px; font-size: 14px; color: #7a295d; text-transform: uppercase; }
    .welcome-name { font-size: 22px; line-height: 1.35; font-weight: 800; background: linear-gradient(135deg, var(--accent), var(--accent-dark)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
    .welcome-note { color: #333; opacity: .85; font-size: 14px; margin-top: 4px; }

  .auth-right { flex: 1 1 60%; background: rgba(255,255,255,0.95); padding: 28px 32px; }
    .user-title { font-size: 26px; font-weight: 700; margin-bottom: 18px; background: linear-gradient(135deg, var(--accent), var(--accent-dark)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; text-transform: uppercase; letter-spacing: 1px; text-align: left; }

    .form-group { margin-bottom: 16px; text-align: left; }
    .form-group label { display: block; margin-bottom: 8px; color: var(--text); font-weight: 600; font-size: 14px; }
    .form-control { width: 100%; padding: 14px 16px; border: 2px solid rgba(255,105,180,0.2); border-radius: 12px; font-size: 16px; transition: all .2s ease; background: rgba(255,255,255,0.85); backdrop-filter: blur(10px); }
    .form-control:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 4px rgba(255,105,180,0.15), 0 8px 25px rgba(255,105,180,0.2); background: rgba(255,255,255,0.95); transform: translateY(-1px); }

    .password-field { position: relative; }
    .toggle-password { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; padding: 4px; cursor: pointer; color: #777; }
    .toggle-password:hover { color: var(--accent); }
    .toggle-password svg { width: 22px; height: 22px; display: block; }

    .login-btn { width: 100%; padding: 14px 24px; background: linear-gradient(135deg, var(--accent), var(--accent-dark)); color: #fff; border: none; border-radius: 12px; font-size: 16px; font-weight: 700; cursor: pointer; transition: all .2s ease; margin-top: 6px; box-shadow: 0 8px 25px rgba(255,105,180,0.25); text-transform: uppercase; letter-spacing: 1px; }
    .login-btn:hover { transform: translateY(-1px); box-shadow: 0 12px 35px rgba(255,105,180,0.35); }
    .login-btn:active { transform: translateY(0); }

    .forgot-link { color: var(--accent); text-decoration: none; font-size: 14px; margin: 10px 0; display: inline-block; cursor: pointer; transition: all .2s ease; padding: 6px 12px; border-radius: 8px; }
    .forgot-link:hover { color: var(--accent-dark); background: rgba(255,105,180,0.1); }

    .create-btn { width: 100%; padding: 14px 24px; background: linear-gradient(135deg, #28a745, #20c997); color: #fff; font-size: 16px; border: none; border-radius: 12px; cursor: pointer; margin-top: 12px; transition: all .2s ease; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; box-shadow: 0 8px 25px rgba(40, 167, 69, 0.25); }
    .create-btn:hover { transform: translateY(-1px); box-shadow: 0 12px 35px rgba(40, 167, 69, 0.35); }

    .error-message { background-color: #f8d7da; color: #721c24; padding: 10px 15px; border-radius: 8px; margin-bottom: 12px; border: 1px solid #f5c6cb; font-size: 14px; }
    .success-message { background-color: #d4edda; color: #155724; padding: 10px 15px; border-radius: 8px; margin-bottom: 12px; border: 1px solid #c3e6cb; font-size: 14px; position: relative; }
    .error-text { color: #dc3545; font-size: 12px; margin-top: 5px; display: block; }

    .floating-admin-btn { position: fixed; bottom: 25px; right: 25px; width: 60px; height: 60px; background: linear-gradient(135deg, var(--accent), var(--accent-dark)); border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; color: white; font-size: 20px; box-shadow: 0 8px 25px rgba(255, 105, 180, 0.4); transition: all 0.3s ease; z-index: 1000; }
    .floating-admin-btn:hover { transform: translateY(-3px) scale(1.05); box-shadow: 0 12px 35px rgba(255, 105, 180, 0.5); color: white; }

  @media (max-width: 900px) { .auth-card { flex-direction: column; width: min(600px, 96vw); } .auth-left { padding: 24px; } .auth-right { padding: 22px; } }
    @media (max-width: 480px) { .brand-logo { width: 96px; height: 96px; } .welcome-name { font-size: 18px; } .user-title { font-size: 22px; } }
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
      <div class="welcome-note">Log in to access the dashboard.</div>
      <div class="welcome-note"  style="color: red;"><h3>Version 1.1.11</h3></div>
    </div>

    <div class="auth-right">
      <div class="user-title">Login</div>

      @if(session('success'))
        <div id="success-message" class="success-message" style="transition: opacity 0.5s ease-out, transform 0.5s ease-out;">
          <button onclick="hideSuccessMessage()" style="position: absolute; top: 5px; right: 10px; background: none; border: none; font-size: 16px; color: #155724; cursor: pointer; font-weight: bold;">&times;</button>
          {{ session('success') }}
        </div>
      @endif

      @if(session('error'))
        <div class="error-message">
          {{ session('error') }}
        </div>
      @endif

      <form method="POST" action="{{ route('login.authenticate') }}">
        @csrf
        <div class="form-group">
          <label for="contact_number">Contact Number</label>
          <input type="tel" id="contact_number" name="contact_number" class="form-control" value="{{ old('contact_number') }}" placeholder="Enter 11-digit number" required inputmode="numeric">
          <div id="contact_number_alert" class="error-text" style="display:none;">Contact number must be exactly 11 digits.</div>
          @error('contact_number')
            <span class="error-text">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <div class="password-field">
            <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
            <button type="button" id="togglePassword" class="toggle-password" aria-label="Show password" aria-pressed="false">
              <svg id="icon-eye" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7Zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10Z"/><circle cx="12" cy="12" r="3"/></svg>
              <svg id="icon-eye-slash" style="display:none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M3.7 2.3 2.3 3.7 6.1 7.5C3.5 9.2 2 12 2 12s3 7 10 7c2.1 0 3.9-.6 5.3-1.4l2.7 2.7 1.4-1.4L3.7 2.3ZM12 17c-2.8 0-5-2.2-5-5 0-.6.1-1.2.3-1.7l1.6 1.6c-.1.3-.2.7-.2 1.1 0 1.7 1.4 3 3 3 .4 0 .8-.1 1.1-.2l1.6 1.6c-.5.2-1.1.3-1.7.3Zm7.8-3.8c.7-1 .2-1.9-.3-2.6C18.4 8.1 15.6 7 12 7c-.6 0-1.1 0-1.6.1l-1.7-1.7C9.8 5.1 10.9 5 12 5c7 0 10 7 10 7s-.9 2.1-2.2 3.2l-2-2Z"/></svg>
            </button>
          </div>
          @error('password')
            <span class="error-text">{{ $message }}</span>
          @enderror
        </div>

        <button type="submit" class="login-btn">Login</button>
      </form>

      <a href="#" class="forgot-link" onclick="alert('Contact system administrator for password reset.')">Forgot password?</a>
      <button class="create-btn" onclick="window.location.href='{{ route('create-account') }}'">Create Account</button>
    </div>
  </div>

  <!-- Floating Admin Button -->
  <a href="{{ route('admin.pin') }}" class="floating-admin-btn" title="Admin Login">🔐</a>

  <script>
    // Toggle password visibility
    document.addEventListener('DOMContentLoaded', function() {
      var toggle = document.getElementById('togglePassword');
      var pwd = document.getElementById('password');
      if (toggle && pwd) {
        toggle.addEventListener('click', function() {
          var isHidden = pwd.getAttribute('type') === 'password';
          pwd.setAttribute('type', isHidden ? 'text' : 'password');
          this.setAttribute('aria-pressed', isHidden ? 'true' : 'false');
          var eye = document.getElementById('icon-eye');
          var slash = document.getElementById('icon-eye-slash');
          if (eye && slash) {
            if (isHidden) { eye.style.display = 'none'; slash.style.display = 'block'; }
            else { eye.style.display = 'block'; slash.style.display = 'none'; }
          }
        });
      }
    });

    // Normalize and validate contact number on submit to avoid false pattern errors
    document.addEventListener('DOMContentLoaded', function() {
      var loginForm = document.querySelector('form[action="{{ route('login.authenticate') }}"]');
      if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
          var input = document.getElementById('contact_number');
          if (!input) return;

          // Normalize to digits only and trim to 11
          var digits = (input.value || '').replace(/\D+/g, '');

          // Accept common variants, convert to 11-digit starting with 0 when possible
          if (digits.length === 12 && digits.startsWith('63')) {
            digits = '0' + digits.slice(2);
          } else if (digits.length === 10 && digits.startsWith('9')) {
            digits = '0' + digits;
          }

          // Update field value with normalized digits
          input.value = digits;

          // Validate exactly 11 digits
          if (!/^\d{11}$/.test(digits)) {
            input.setCustomValidity('Contact number must be exactly 11 digits');
            input.reportValidity();
            e.preventDefault();
            return false;
          } else {
            input.setCustomValidity('');
          }
        });
      }
    });

    // Auto-hide success message after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
      const successMessage = document.getElementById('success-message');
      if (successMessage) {
        setTimeout(function() { hideSuccessMessage(); }, 5000);
      }
    });

    // Function to hide success message with animation
    function hideSuccessMessage() {
      const successMessage = document.getElementById('success-message');
      if (successMessage) {
        successMessage.style.opacity = '0';
        successMessage.style.transform = 'translateY(-12px)';
        setTimeout(function() { successMessage.style.display = 'none'; }, 500);
      }
    }
  </script>

</body>
</html>
