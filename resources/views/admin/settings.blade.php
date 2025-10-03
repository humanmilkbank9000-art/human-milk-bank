<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Settings - Breastmilk Donation System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin.css?v=20250921') }}">
    <style>
        .settings-container {

            // Additional event listeners as backup
            document.addEventListener('DOMContentLoaded', function () {
                    const passwordForm=document.getElementById('password-change-form');

                    if (passwordForm) {
                        passwordForm.addEventListener('submit', function(e) {
                                if ( !e.defaultPrevented) {
                                    handleSubmit(e);
                                }
                            });
                    }

                    const usernameForm=document.getElementById('username-change-form');

                    if (usernameForm) {
                        usernameForm.addEventListener('submit', function(e) {
                                if ( !e.defaultPrevented) {
                                    handleUsernameSubmit(e);
                                }
                            });
                    }
                });
            ng: 40px;
            max-width: 900px;
            margin: 0 auto;
        }

        .settings-header {
            text-align: center;
            margin-bottom: 50px;
            padding: 40px 30px;
            background: linear-gradient(135deg, #ffb6ce, #ff69b4);
            border-radius: 16px;
            color: white;
            box-shadow: 0 8px 25px rgba(255, 105, 180, 0.3);
        }

        .settings-header h1 {
            color: white;
            margin-bottom: 12px;
            font-size: 2.5rem;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .settings-header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.2rem;
            margin: 0;
            font-weight: 300;
        }

        .settings-cards {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .settings-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            border: 1px solid rgba(255, 105, 180, 0.1);
            transition: all 0.3s ease;
        }

        .settings-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
        }

        .card-header {
            background: linear-gradient(135deg, #ffb6ce, #ff69b4);
            color: #fff;
            padding: 15px 20px;
            border-bottom: none;
            position: relative;
        }

        .card-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), transparent);
            pointer-events: none;
        }

        .card-header h2 {
            margin: 0;
            font-size: 1.2rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 1;
        }

        .card-body {
            padding: 20px;
            background: linear-gradient(to bottom, #ffffff, #fefeff);
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #2d3748;
            font-size: 0.9rem;
            position: relative;
        }

        .form-group label::after {
            content: '*';
            color: #ff69b4;
            margin-left: 3px;
            font-weight: bold;
        }

        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            background: #ffffff;
            box-sizing: border-box;
            font-family: inherit;
        }

        .form-control:focus {
            outline: none;
            border-color: #ff69b4;
            box-shadow: 0 0 0 4px rgba(255, 105, 180, 0.15);
            background: #fff;
        }

        .form-control:hover {
            border-color: #cbd5e0;
        }

        .input-group {
            position: relative;
        }

        .input-group-append {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #718096;
            z-index: 5;
            font-size: 1.1rem;
            padding: 5px;
            border-radius: 5px;
            transition: all 0.2s ease;
        }

        .input-group-append:hover {
            color: #ff69b4;
            background: rgba(255, 105, 180, 0.1);
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            text-decoration: none;
            min-width: 130px;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, #ff69b4, #ff1493, #ff69b4);
            background-size: 200% 200%;
            color: #fff;
            box-shadow: 0 8px 20px rgba(255, 105, 180, 0.4);
            border: 2px solid transparent;
            position: relative;
            z-index: 1;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(255, 105, 180, 0.5);
            background-position: right center;
        }

        .btn-primary:active {
            transform: translateY(-1px);
            box-shadow: 0 6px 15px rgba(255, 105, 180, 0.3);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
            box-shadow: 0 4px 10px rgba(255, 105, 180, 0.2);
        }

        .btn-loading {
            display: none;
        }

        .alert {
            padding: 15px 20px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 8px;
            font-weight: 500;
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }

        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }

        .submit-section {
            text-align: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(255, 105, 180, 0.1);
        }

        @media (max-width: 768px) {
            .settings-container {
                padding: 15px;
            }

            .settings-cards {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .settings-header {
                padding: 15px;
                margin-bottom: 20px;
            }

            .settings-header h1 {
                font-size: 1.5rem;
            }

            .card-body {
                padding: 15px;
            }

            .btn {
                min-width: 120px;
                padding: 8px 16px;
            }
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    @include('admin.partials.sidebar')

    <!-- Main Content -->
    <div class="main-content top-bar-space">
        @include('admin.partials.top-bar', ['pageTitle' => 'Settings', 'pageTitleId' => 'pageTitle'])

        <div class="settings-container">
            <div class="settings-header">
                <h1><i class="fas fa-cog"></i> Admin Settings</h1>
                <p>Manage your account settings and preferences</p>
            </div>

            <div class="settings-cards">
                <!-- Username Change Card -->
                <div class="settings-card">
                    <div class="card-header">
                        <h2><i class="fas fa-user"></i> Change Username</h2>
                    </div>
                    <div class="card-body">
                        <!-- Display validation errors for username -->
                        @if($errors->has('username'))
                            <div class="alert alert-danger">
                                <ul style="margin: 0; padding-left: 20px;">
                                    <li>{{ $errors->first('username') }}</li>
                                </ul>
                            </div>
                        @endif

                        <form id="username-change-form" method="POST"
                            action="{{ route('admin.settings.update-username') }}"
                            onsubmit="return handleUsernameSubmit(event)">
                            @csrf

                            <div class="form-group">
                                <label for="current_username">Current Username</label>
                                <input type="text" class="form-control" id="current_username"
                                    value="{{ $admin->username }}" readonly
                                    style="background-color: #f8f9fa; color: #6c757d;">
                            </div>

                            <div class="form-group">
                                <label for="new_username">New Username</label>
                                <input type="text" class="form-control" id="new_username" name="username" required
                                    minlength="3" maxlength="50" pattern="[a-zA-Z0-9_]+"
                                    title="Username can only contain letters, numbers, and underscores"
                                    value="{{ old('username') }}">
                                <small class="form-text text-muted"
                                    style="color: #6c757d; font-size: 0.75rem; margin-top: 3px;">
                                    3-50 characters, letters, numbers, underscores only
                                </small>
                            </div>

                            <div class="submit-section">
                                <button type="submit" id="username-submit-btn" class="btn btn-primary">
                                    <span class="btn-text">
                                        <i class="fas fa-edit"></i>
                                        Update Username
                                    </span>
                                    <span class="btn-loading">
                                        <i class="fas fa-spinner fa-spin"></i>
                                        Updating...
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Password Change Card -->
                <div class="settings-card">
                    <div class="card-header">
                        <h2><i class="fas fa-lock"></i> Change Password</h2>
                    </div>
                    <div class="card-body">
                        <!-- Display validation errors -->
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul style="margin: 0; padding-left: 20px;">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Display success message -->
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <!-- Display error message -->
                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form id="password-change-form" method="POST"
                            action="{{ route('admin.settings.update-password') }}"
                            onsubmit="return handleSubmit(event)">
                            @csrf

                            <div class="form-group">
                                <label for="current_password">Current Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="current_password"
                                        name="current_password" required>
                                    <span class="input-group-append" onclick="togglePassword('current_password')">
                                        <i class="fas fa-eye" id="current_password_icon"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="new_password">New Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="new_password"
                                            name="new_password" required minlength="8">
                                        <span class="input-group-append" onclick="togglePassword('new_password')">
                                            <i class="fas fa-eye" id="new_password_icon"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="new_password_confirmation">Confirm New Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="new_password_confirmation"
                                            name="new_password_confirmation" required minlength="8">
                                        <span class="input-group-append"
                                            onclick="togglePassword('new_password_confirmation')">
                                            <i class="fas fa-eye" id="new_password_confirmation_icon"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="submit-section">
                                <button type="submit" id="submit-btn" class="btn btn-primary">
                                    <span class="btn-text">
                                        <i class="fas fa-save"></i>
                                        Update Password
                                    </span>
                                    <span class="btn-loading">
                                        <i class="fas fa-spinner fa-spin"></i>
                                        Updating...
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Show SweetAlert for success message
            @if(session('success'))
                Swal.fire({
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    confirmButtonColor: '#7C4DFF',
                    confirmButtonText: 'OK'
                });
            @endif

            // Show SweetAlert for error message
            @if(session('error'))
                Swal.fire({
                    title: 'Error!',
                    text: '{{ session('error') }}',
                    icon: 'error',
                    confirmButtonColor: '#7C4DFF',
                    confirmButtonText: 'OK'
                });
            @endif

                function handleUsernameSubmit(event) {
                    event.preventDefault();

                    const form = document.getElementById('username-change-form');
                    const submitBtn = document.getElementById('username-submit-btn');
                    const btnText = submitBtn.querySelector('.btn-text');
                    const btnLoading = submitBtn.querySelector('.btn-loading');

                    // Get form values
                    const currentUsername = document.getElementById('current_username').value.trim();
                    const newUsername = document.getElementById('new_username').value.trim();

                    // Validation
                    if (!newUsername) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Please enter a new username.',
                            icon: 'error',
                            confirmButtonColor: '#7C4DFF'
                        });
                        return false;
                    }

                    if (newUsername.length < 3) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Username must be at least 3 characters long.',
                            icon: 'error',
                            confirmButtonColor: '#7C4DFF'
                        });
                        return false;
                    }

                    if (newUsername.length > 50) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Username cannot exceed 50 characters.',
                            icon: 'error',
                            confirmButtonColor: '#7C4DFF'
                        });
                        return false;
                    }

                    if (!/^[a-zA-Z0-9_]+$/.test(newUsername)) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Username can only contain letters, numbers, and underscores.',
                            icon: 'error',
                            confirmButtonColor: '#7C4DFF'
                        });
                        return false;
                    }

                    if (newUsername.toLowerCase() === currentUsername.toLowerCase()) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'The new username must be different from your current username.',
                            icon: 'error',
                            confirmButtonColor: '#7C4DFF'
                        });
                        return false;
                    }

                    // Show loading state
                    submitBtn.disabled = true;
                    btnText.style.display = 'none';
                    btnLoading.style.display = 'inline-flex';

                    // Submit form after a short delay
                    setTimeout(function () {
                        form.submit();
                    }, 300);

                    return false;
                }

            function handleSubmit(event) {
                event.preventDefault();

                const form = document.getElementById('password-change-form');
                const submitBtn = document.getElementById('submit-btn');
                const btnText = submitBtn.querySelector('.btn-text');
                const btnLoading = submitBtn.querySelector('.btn-loading');

                // Get form values
                const currentPassword = document.getElementById('current_password').value.trim();
                const newPassword = document.getElementById('new_password').value.trim();
                const confirmPassword = document.getElementById('new_password_confirmation').value.trim();

                // Validation
                if (!currentPassword) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Please enter your current password.',
                        icon: 'error',
                        confirmButtonColor: '#7C4DFF'
                    });
                    return false;
                }

                if (!newPassword) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Please enter a new password.',
                        icon: 'error',
                        confirmButtonColor: '#7C4DFF'
                    });
                    return false;
                }

                if (newPassword.length < 8) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'New password must be at least 8 characters long.',
                        icon: 'error',
                        confirmButtonColor: '#7C4DFF'
                    });
                    return false;
                }

                if (!confirmPassword) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Please confirm your new password.',
                        icon: 'error',
                        confirmButtonColor: '#7C4DFF'
                    });
                    return false;
                }

                if (newPassword !== confirmPassword) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'New password and confirmation do not match.',
                        icon: 'error',
                        confirmButtonColor: '#7C4DFF'
                    });
                    return false;
                }

                // Show loading state
                submitBtn.disabled = true;
                btnText.style.display = 'none';
                btnLoading.style.display = 'inline-flex';

                // Submit form after a short delay
                setTimeout(function () {
                    form.submit();
                }, 300);

                return false;
            }

            function togglePassword(fieldId) {
                const field = document.getElementById(fieldId);
                const icon = document.getElementById(fieldId + '_icon');

                if (!field || !icon) return;

                if (field.type === 'password') {
                    field.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    field.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }

            // Additional event listener as backup
            document.addEventListener('DOMContentLoaded', function () {
                const form = document.getElementById('password-change-form');
                if (form) {
                    form.addEventListener('submit', function (e) {
                        if (!e.defaultPrevented) {
                            handleSubmit(e);
                        }
                    });
                }
            });
        </script>
</body>

</html>