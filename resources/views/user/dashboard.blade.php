<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.theme')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Breastmilk Donation System</title>

    <!-- Sidebar/top bar uses admin shared styles -->
    <link rel="stylesheet" href="{{ asset('css/admin.css?v=20250921') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Override Swal.fire to default to no backdrop (transparent) unless explicitly provided.
        (function () {
            if (!window.Swal) return;
            const _fire = Swal.fire.bind(Swal);
            Swal.fire = fu nc t io n(o pts) {
                if (typeof opts === 'string' || Array.isArray(opts)) {
                    return _fire(opts);
                }
                opts = opts || {};
                if (typeof opts.backdrop === 'undefined') {
                    // Set a fully transparent backdrop so the dark overlay is removed.
                    opts.backdrop = false; // SweetAlert2: false removes the overlay entirely
                }
                return _fire(opts);
            };
        })();
    </script>
    <style>
        /* Global SweetAlert2 button theme for user: purple confirm, gray cancel */
        .swal2-actions {
            gap: 8px !important;
        }

        .swal2-styled {
            box-shadow: none !important;
            border: none !important;
        }

        .swal2-styled:focus {
            outline: none !important;
            box-shadow: 0 0 0 3px rgba(124, 77, 255, 0.28) !important;
        }

        .swal2-styled.swal2-confirm {
            background-color: #7C4DFF !important;
            /* primary purple */
            color: #ffffff !important;
            border-radius: 10px !important;
            padding: 10px 18px !important;
            font-weight: 600 !important;
        }

        .swal2-styled.swal2-confirm:hover {
            background-color: #6A3EF3 !important;
        }

        .swal2-styled.swal2-cancel {
            background-color: #E5E7EB !important;
            /* neutral gray */
            color: #111827 !important;
            border-radius: 10px !important;
            padding: 10px 18px !important;
            font-weight: 600 !important;
        }

        .swal2-styled.swal2-cancel:hover {
            background-color: #D1D5DB !important;
        }
    </style>
    <script>
        window.__PENDING_REQUESTS__ = @json($pendingRequests ?? []);
    </script>
    <style>
        /* Ensure SweetAlert overlays cover the user top bar */
        .swal2-container {
            z-index: 100000 !important;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f8f9fa;
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        :root {
            --sidebar-accent: #ff69b4;
        }

        .sidebar {
            width: 280px;
            background-color: #ffd3e4;
            /* fallback (softened) */
            background: linear-gradient(135deg, #ffd3e4 0%, #ff9fc5 40%, #ff6fb1 100%);
            padding: 20px;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            transition: transform 0.3s ease;
            z-index: 2001;
            /* ensure sidebar is always above overlays */
            /* Allow scroll but hide the scrollbar */
            overflow-y: auto;
            -ms-overflow-style: none;
            /* IE and old Edge */
            scrollbar-width: none;
            /* Firefox */
        }

        /* Chrome/Safari/Edge */
        .sidebar::-webkit-scrollbar {
            width: 0;
            height: 0;
            display: none;
        }

        /* Hide scrollbars in modal content while keeping scroll functional */
        .modal-body,
        .modal-calendar-content {
            -ms-overflow-style: none;
            /* IE and old Edge */
            scrollbar-width: none;
            /* Firefox */
        }

        .modal-body::-webkit-scrollbar,
        .modal-calendar-content::-webkit-scrollbar {
            width: 0;
            height: 0;
            display: none;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: #fff;
            border: 2px solid #eee;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px auto;
            object-fit: contain;
        }

        .user-label {
            color: #333;
            font-size: 16px;
            font-weight: 600;
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, 'Roboto', sans-serif;
            text-align: center;
            margin-bottom: 30px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .nav-menu {
            flex: 1;
        }

        .nav-item {
            display: flex;
            align-items: center;
            padding: 14px 18px;
            margin-bottom: 8px;
            background: transparent;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color .2s ease, color .2s ease;
            text-decoration: none;
            color: #111;
            /* readable & uniform */
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, 'Roboto', sans-serif;
            box-shadow: none;
            position: relative;
        }

        .nav-item:hover {
            background: rgba(255, 255, 255, .22);
            color: #111;
        }

        .nav-item.active {
            background: rgba(255, 255, 255, .35);
            color: #111;
            font-weight: 600;
        }

        .nav-item.active::before {
            content: "";
            position: absolute;
            left: 0;
            top: 8px;
            bottom: 8px;
            width: 4px;
            border-radius: 3px;
            background: var(--sidebar-accent, #ff69b4);
        }

        .nav-item:focus-visible {
            outline: 2px solid var(--sidebar-accent, #ff69b4);
            outline-offset: 2px;
        }

        .nav-icon {
            width: 26px;
            height: 26px;
            margin-right: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #6b7280;
        }

        .nav-item:hover .nav-icon,
        .nav-item.active .nav-icon {
            color: #111;
        }

        .nav-text {
            font-size: 16px;
            font-weight: 600;
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, 'Roboto', sans-serif;
            letter-spacing: 0.3px;
            line-height: 1.5;
            flex: 1;
            min-width: 0;
        }

        /* Main Content Styles */
        .main-content {

            /* Walk-in calendar container */
            #walkInCalendar {
                max-width: 100%;
                margin: 0 auto;
            }

            /* Removed FullCalendar-specific styles */
            /* Ensure the admin-styled top bar stays above mobile sidebar overlay */
            .top-bar.admin-top-bar {
                z-index: 3000 !important;
            }

            @media (min-width: 769px) {

                /* Offset top bar to account for fixed sidebar on desktop */
                .admin-top-bar {
                    left: 280px;
                    right: 0;
                    width: auto;
                    position: fixed;
                    top: 0;
                }

                .main-content.expanded .admin-top-bar {
                    left: 0;
                    right: 0;
                    width: 100%;
                }
            }

            /* Reserve space below the fixed header */
            .top-bar-space {
                padding-top: var(--admin-topbar-height, 72px);
            }

            .top-bar-left {
                display: flex;
                align-items: center;
                gap: 14px;
            }

            .top-bar-logos {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .top-bar-logos img {
                height: 48px;
                width: auto;
            }

            .top-bar-title-wrap {
                display: flex;
                flex-direction: column;
                line-height: 1.2;
            }

            .top-bar-title-wrap h1 {
                margin: 0;
                font-size: 28px;
                font-weight: 700;
                color: #000;
            }

            @media (max-width: 480px) {
                .top-bar-title-wrap h1 {
                    font-size: 22px;
                    font-weight: 700;
                }
            }

            .top-bar-subtitle {
                color: #666;
                font-size: 14px;
                margin-top: 2px;
            }

            .hamburger-btn {
                background: none;
                border: none;
                font-size: 24px;
                cursor: pointer;
                color: #333;
                padding: 8px;
                border-radius: 4px;
                transition: background-color 0.3s ease;
                margin-right: 15px;
            }

            .hamburger-btn:hover {
                background-color: #ffd1df;
                color: #ff69b4;
            }

            .page-title {
                font-size: 22px;
                font-weight: 800;
                background: linear-gradient(135deg, #ff69b4, #d63384);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }

            .top-right {
                display: flex;
                align-items: center;
                gap: 20px;
            }

            .notification-btn {
                background: none;
                border: none;
                font-size: 20px;
                cursor: pointer;
                color: #333;
                position: relative;
                transition: color 0.3s ease;
            }

            .notification-btn:hover {
                color: #ff69b4;
            }

            .logout-btn {
                background-color: #ff69b4;
                position: relative;
                z-index: 3001;
                color: #fff;
                border: none;
                padding: 10px 20px;
                border-radius: 5px;
                cursor: pointer;
                font-size: 14px;
                font-weight: 500;
                transition: background-color 0.3s ease;
            }

            .logout-btn:hover {
                background-color: #e55a9e;
            }

            /* Dashboard Content */
            .dashboard-content {
                flex: 1;
                padding: 30px;
                background-color: #f8f9fa;
            }

            .content-box {
                background-color: #ffd1df;
                border-radius: 15px;
                padding: 30px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }

            /* Dashboard Content */
            .dashboard-content {
                padding: 30px;
            }

            /* Donation sections alignment */
            .donation-options {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                gap: 16px;
                align-items: stretch;
            }

            .donation-option {
                background: #fff;
                border: 1px solid #eee;
                border-radius: 12px;
                padding: 16px;
                cursor: pointer;
                transition: box-shadow .2s, transform .2s;
                text-align: left;
            }

            .donation-option:hover {
                box-shadow: 0 8px 18px rgba(255, 105, 180, .25);
                transform: translateY(-2px);
            }

            .donation-option .option-icon {
                font-size: 24px;
                margin-bottom: 8px;
            }

            .content-title {
                font-size: 24px;
                font-weight: bold;
                color: #333;
                margin-bottom: 25px;
                text-align: center;
            }

            /* Step-by-step guide only (no status card) */

            .step-guide {
                max-width: 1100px;
                margin: 0 auto;
            }

            /* Each step as a compact card: number | title + description */
            .step-item {
                margin-bottom: 20px;
                padding: 16px 18px;
                background-color: #fff;
                border-radius: 12px;
                border: 1px solid #f0d3df;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
                display: grid;
                grid-template-columns: 40px 1fr;
                grid-template-rows: auto auto;
                grid-template-areas:
                    "num title"
                    "num desc";
                column-gap: 12px;
                row-gap: 6px;
            }

            .step-number {
                grid-area: num;
                display: inline-block;
                width: 36px;
                height: 36px;
                line-height: 36px;
                background-color: #ff69b4;
                color: #fff;
                border-radius: 50%;
                text-align: center;
                font-weight: 800;
                font-size: 14px;
            }

            .step-title {
                grid-area: title;
                font-size: 17px;
                font-weight: 800;
                color: #1f2937;
                margin: 0;
            }

            .step-description {
                grid-area: desc;
                color: #4b5563;
                line-height: 1.6;
                font-size: 14px;
            }

            /* Compact steps grid layout */
            .compact-steps {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 14px;
                align-items: stretch;
            }

            .compact-steps .step-item {
                margin: 0;
            }

            .compact-steps .step-number {
                width: 32px;
                height: 32px;
                line-height: 32px;
            }

            .compact-steps .step-title {
                font-size: 16px;
            }

            .compact-steps .step-description {
                line-height: 1.5;
                font-size: 14px;
            }

            /* End carousel (reverted to cards) */
            @media (max-width: 480px) {
                .step-item {
                    padding: 14px 16px;
                    grid-template-columns: 34px 1fr;
                }

                .step-number {
                    width: 30px;
                    height: 30px;
                    line-height: 30px;
                    font-size: 13px;
                }

                .step-title {
                    font-size: 16px;
                }

                .step-description {
                    font-size: 13.5px;
                }
            }

            /* Donation Options Styles */
            .donation-options {
                display: flex;
                justify-content: space-around;
                gap: 20px;
                margin-top: 30px;
            }

            .donation-option {
                flex: 1;
                background-color: #fff;
                border-radius: 10px;
                padding: 25px;
                text-align: center;
                cursor: pointer;
                transition: transform 0.3s ease;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            .donation-option:hover {
                transform: translateY(-5px);
            }

            .option-icon {
                font-size: 48px;
                margin-bottom: 15px;
            }

            .donation-option h3 {
                font-size: 20px;
                font-weight: bold;
                color: #333;
                margin-bottom: 10px;
            }

            .donation-option p {
                font-size: 14px;
                color: #666;
                line-height: 1.5;
            }

            /* Form Styles */
            .form-section {
                margin-bottom: 25px;
            }

            .form-section h3 {
                font-size: 18px;
                font-weight: bold;
                color: #333;
                margin-bottom: 15px;
            }

            .form-row {
                display: flex;
                gap: 20px;
                margin-bottom: 15px;
            }

            .form-group {
                flex: 1;
            }

            .form-group label {
                display: block;
                font-size: 14px;
                font-weight: 500;
                color: #555;
                margin-bottom: 8px;
            }

            .form-group input[type="text"],
            .form-group input[type="date"],
            .form-group input[type="number"],
            .form-group select,
            .form-group textarea {
                width: 100%;
                padding: 10px;
                border: 1px solid #ccc;
                border-radius: 5px;
                font-size: 14px;
                box-sizing: border-box;
            }

            .form-group input[type="radio"],
            .form-group input[type="checkbox"] {
                margin-right: 5px;
            }

            .radio-group {
                display: flex;
                gap: 15px;
                margin-bottom: 15px;
            }

            .radio-group label {
                display: flex;
                align-items: center;
                cursor: pointer;
            }

            .checkbox-group {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                margin-top: 10px;
            }

            .checkbox-group label {
                display: flex;
                align-items: center;
                cursor: pointer;
            }

            .question-group {
                margin-bottom: 20px;
                padding: 15px;
                background-color: #f8f9fa;
                border-radius: 8px;
                border-left: 4px solid #ff69b4;
            }

            .question-group p {
                margin-bottom: 10px;
                color: #333;
            }

            .form-actions {
                display: flex;
                justify-content: space-between;
                margin-top: 25px;
            }

            .submit-btn,
            .cancel-btn {
                padding: 10px 20px;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-size: 14px;
                font-weight: 500;
                transition: background-color 0.3s ease;
            }

            .submit-btn {
                background-color: #ff69b4;
                color: #fff;
            }

            .submit-btn:hover {
                background-color: #e55a9e;
            }

            .cancel-btn {
                background-color: #ffb6ce;
                color: #333;
                border: 2px solid #ff69b4;
            }

            .cancel-btn:hover {
                background-color: #ffd1df;
                color: #ff69b4;
            }

            /* Donation History Styles */
            .donation-history {
                margin-top: 20px;
            }

            .history-table {
                width: 100%;
                border-collapse: collapse;
                background-color: #fff;
                border-radius: 8px;
                overflow: hidden;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            .history-table th,
            .history-table td {
                padding: 12px;
                text-align: left;
                border-bottom: 1px solid #eee;
            }

            .history-table th {
                background-color: #ffb6ce;
                color: #333;
                font-weight: bold;
            }

            .history-table tr:hover {
                background-color: #f8f9fa;
            }

            /* Enhanced styling for number of bags column */
            .history-table th:nth-child(2) {
                background-color: #ff69b4;
                color: white;
                text-align: center;
            }

            .history-table td:nth-child(2) {
                text-align: center;
                background-color: #fff0f5;
                font-weight: bold;
            }

            /* Success Message Animation Styles */
            #success-message {
                animation: slideInDown 0.5s ease-out;
            }

            @keyframes slideInDown {
                from {
                    opacity: 0;
                    transform: translateY(-20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            /* Success message close button hover effect */
            #success-message button:hover {
                background-color: rgba(21, 87, 36, 0.1);
                border-radius: 50%;
            }

            /* Modern Breastmilk Request Form Styles */
            .request-form-container {
                max-width: 900px;
                margin: 0 auto;
                padding: 24px;
            }

            .modern-form-card {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(20px);
                border-radius: 20px;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(255, 255, 255, 0.2);
                border: 1px solid rgba(255, 182, 206, 0.3);
                position: relative;
                z-index: 1;
                overflow: hidden;
                padding: 32px;
            }

            .modern-form-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 4px;
                background: linear-gradient(135deg, #ff69b4, #d63384, #ff69b4);
                background-size: 200% 100%;
                animation: gradient-shift 3s ease-in-out infinite;
            }

            @keyframes gradient-shift {

                0%,
                100% {
                    background-position: 0% 50%;
                }

                50% {
                    background-position: 100% 50%;
                }
            }

            .form-section {
                background: rgba(255, 255, 255, 0.8);
                border-radius: 16px;
                padding: 28px;
                margin-bottom: 24px;
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
                border: 1px solid rgba(255, 182, 206, 0.2);
                transition: all 0.3s ease;
                position: relative;
            }

            .form-section:hover {
                transform: translateY(-2px);
                box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
            }

            .section-title {
                display: flex;
                align-items: center;
                gap: 12px;
                margin: 0 0 24px 0;
                font-size: 20px;
                font-weight: 700;
                background: linear-gradient(135deg, #ff69b4, #d63384);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                padding-bottom: 12px;
                border-bottom: 2px solid rgba(255, 105, 180, 0.2);
                position: relative;
            }

            .section-title::before {
                content: '';
                position: absolute;
                bottom: -2px;
                left: 0;
                width: 50px;
                height: 2px;
                background: linear-gradient(135deg, #ff69b4, #d63384);
                border-radius: 1px;
            }

            .section-title i {
                font-size: 22px;
                color: #ff69b4;
                filter: drop-shadow(0 2px 4px rgba(255, 105, 180, 0.3));
            }

            .form-row {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 24px;
                margin-bottom: 24px;
            }

            .form-group {
                margin-bottom: 24px;
                position: relative;
            }

            .form-label {
                display: flex;
                align-items: center;
                gap: 10px;
                font-weight: 600;
                color: #333;
                margin-bottom: 10px;
                font-size: 14px;
                position: relative;
            }

            .form-label i {
                font-size: 16px;
                color: #ff69b4;
                filter: drop-shadow(0 1px 2px rgba(255, 105, 180, 0.3));
            }

            .form-control {
                width: 100%;
                padding: 14px 16px;
                border: 2px solid rgba(255, 105, 180, 0.2);
                border-radius: 12px;
                font-size: 14px;
                background: rgba(255, 255, 255, 0.8);
                transition: all 0.3s ease;
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            }

            /* Visual hint for read-only fields */
            .form-control.readonly,
            .form-control[readonly] {
                background: #f9f9f9;
                color: #666;
                cursor: not-allowed;
                border-color: rgba(255, 105, 180, 0.25);
            }

            .form-control:focus {
                outline: none;
                border-color: #ff69b4;
                box-shadow: 0 0 0 4px rgba(255, 105, 180, 0.15), 0 8px 25px rgba(255, 105, 180, 0.2);
                background: rgba(255, 255, 255, 0.95);
                transform: translateY(-1px);
            }

            .form-control:valid {
                border-color: #28a745;
                background: rgba(40, 167, 69, 0.05);
            }

            .form-control::placeholder {
                color: #999;
                font-style: italic;
            }

            .form-help {
                display: block;
                font-size: 12px;
                color: #666;
                margin-top: 6px;
                font-style: italic;
                opacity: 0.8;
            }

            /* Modern File Upload Styles */
            .file-upload-container {
                display: flex;
                align-items: center;
                gap: 12px;
                margin-top: 12px;
                padding: 16px;
                background: rgba(255, 255, 255, 0.6);
                border: 2px dashed rgba(255, 105, 180, 0.3);
                border-radius: 12px;
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
            }

            .file-upload-container:hover {
                background: rgba(255, 105, 180, 0.05);
                border-color: #ff69b4;
                transform: translateY(-1px);
            }

            .file-upload-container::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255, 105, 180, 0.1), transparent);
                transition: left 0.5s ease;
            }

            .file-upload-container:hover::before {
                left: 100%;
            }

            .file-input {
                display: none;
            }

            .file-input.error+.file-upload-label {
                background: linear-gradient(135deg, #dc3545, #c82333);
                box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
            }

            .file-input.success+.file-upload-label {
                background: linear-gradient(135deg, #28a745, #1e7e34);
                box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
            }

            .file-upload-label {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                padding: 12px 20px;
                background: linear-gradient(135deg, #ff69b4, #d63384);
                color: white;
                border: none;
                border-radius: 10px;
                cursor: pointer;
                transition: all 0.3s ease;
                font-size: 14px;
                font-weight: 600;
                box-shadow: 0 4px 15px rgba(255, 105, 180, 0.3);
            }

            .file-upload-label:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(255, 105, 180, 0.4);
            }

            .file-upload-label:active {
                transform: translateY(0);
            }

            .file-upload-label i {
                font-size: 16px;
            }

            .file-name {
                font-size: 14px;
                color: #666;
                font-style: italic;
                flex: 1;
            }

            .prescription-preview {
                margin-top: 15px;
                text-align: center;
            }

            /* Modern Button Styles */
            .form-actions {
                display: flex;
                gap: 16px;
                justify-content: flex-end;
                margin-top: 32px;
                padding-top: 24px;
                border-top: 1px solid rgba(255, 105, 180, 0.2);
            }

            .btn {
                padding: 14px 28px;
                border: none;
                border-radius: 12px;
                color: white;
                font-weight: 600;
                cursor: pointer;
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
                transition: all 0.3s ease;
                font-size: 14px;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                position: relative;
                overflow: hidden;
            }

            .btn:active {
                transform: translateY(0);
            }

            .btn-secondary {
                background: linear-gradient(135deg, #6c757d, #495057);
            }

            .btn-secondary:hover {
                background: linear-gradient(135deg, #5a6268, #343a40);
                transform: translateY(-2px);
                box-shadow: 0 12px 35px rgba(108, 117, 125, 0.3);
            }

            .btn-primary {
                background: linear-gradient(135deg, #ff69b4, #d63384);
            }

            .btn-primary:hover {
                background: linear-gradient(135deg, #e91e63, #c2185b);
                transform: translateY(-2px);
                box-shadow: 0 12px 35px rgba(255, 105, 180, 0.4);
            }

            .btn i {
                font-size: 16px;
            }

            .btn:disabled {
                opacity: 0.6;
                cursor: not-allowed;
                transform: none !important;
            }

            .btn.loading {
                position: relative;
                color: transparent;
            }

            .btn.loading::after {
                content: '';
                position: absolute;
                top: 50%;
                left: 50%;
                width: 20px;
                height: 20px;
                margin: -10px 0 0 -10px;
                border: 2px solid transparent;
                border-top: 2px solid #ffffff;
                border-radius: 50%;
                animation: spin 1s linear infinite;
            }

            @keyframes spin {
                0% {
                    transform: rotate(0deg);
                }

                100% {
                    transform: rotate(360deg);
                }
            }

            /* Modern Warning Section */
            .warning-section {
                margin: 24px 0;
            }

            .warning-box {
                background: linear-gradient(135deg, rgba(255, 193, 7, 0.1), rgba(255, 152, 0, 0.1));
                border: 1px solid rgba(255, 193, 7, 0.3);
                border-radius: 16px;
                padding: 20px;
                display: flex;
                align-items: flex-start;
                gap: 16px;
                position: relative;
                overflow: hidden;
            }

            .warning-box::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 3px;
                background: linear-gradient(135deg, #ffc107, #ff9800);
            }

            .warning-icon {
                font-size: 24px;
                color: #ff9800;
                margin-top: 2px;
                filter: drop-shadow(0 2px 4px rgba(255, 152, 0, 0.3));
            }

            .warning-content h4 {
                margin: 0 0 8px 0;
                font-size: 16px;
                font-weight: 700;
                color: #e65100;
            }

            .warning-content p {
                margin: 0;
                color: #bf360c;
                line-height: 1.5;
                font-size: 14px;
            }

            /* Error and Alert Styles */
            .error-text {
                color: #dc3545;
                font-size: 12px;
                margin-top: 4px;
                display: block;
            }

            .contact-alert {
                color: #dc3545;
                font-size: 12px;
                margin-top: 4px;
                display: none;
            }

            .form-control.error {
                border-color: #dc3545;
                box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
            }

            .form-control.success {
                border-color: #28a745;
                box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);
            }

            /* Warning Section */
            .warning-section {
                margin: 30px 0;
            }

            .warning-box {
                display: flex;
                align-items: flex-start;
                gap: 15px;
                background: linear-gradient(135deg, #fff3cd, #ffeaa7);
                border: 2px solid #ffc107;
                border-radius: 12px;
                padding: 20px;
                box-shadow: 0 4px 15px rgba(255, 193, 7, 0.2);
            }

            .warning-icon {
                font-size: 24px;
                color: #856404;
                flex-shrink: 0;
            }

            .warning-content h4 {
                margin: 0 0 10px 0;
                color: #856404;
                font-weight: 600;
            }

            .warning-content p {
                margin: 0;
                color: #856404;
                line-height: 1.5;
            }

            /* Form Actions */
            .form-actions {
                display: flex;
                justify-content: flex-end;
                gap: 15px;
                margin-top: 30px;
                padding-top: 20px;
                border-top: 2px solid #f0f0f0;
            }

            .btn {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 12px 24px;
                border: none;
                border-radius: 8px;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .btn-secondary {
                background: #ffb6ce;
                color: #333;
                border: 2px solid #ff69b4;
            }

            .btn-secondary:hover {
                background: #ffd1df;
                color: #ff69b4;
                transform: translateY(-2px);
                box-shadow: 0 4px 15px rgba(255, 105, 180, 0.3);
            }

            .btn-primary {
                background: linear-gradient(135deg, #ff69b4, #ff89c9);
                color: white;
            }

            .btn-primary:hover {
                background: linear-gradient(135deg, #e55a9e, #ff69b4);
                transform: translateY(-2px);
                box-shadow: 0 4px 15px rgba(255, 105, 180, 0.4);
            }

            .btn:disabled {
                opacity: 0.6;
                cursor: not-allowed;
                transform: none !important;
            }

            /* Modern Responsive Design */
            @media (max-width: 768px) {
                .request-form-container {
                    padding: 16px;
                }

                .modern-form-card {
                    border-radius: 16px;
                    margin: 0;
                }

                .form-section {
                    padding: 20px;
                    margin-bottom: 20px;
                }

                .section-title {
                    font-size: 18px;
                    margin-bottom: 20px;
                }

                .form-row {
                    grid-template-columns: 1fr;
                    gap: 20px;
                    margin-bottom: 20px;
                }

                .form-group {
                    margin-bottom: 20px;
                }

                .form-actions {
                    flex-direction: column;
                    gap: 12px;
                    margin-top: 24px;
                }

                .btn {
                    width: 100%;
                    padding: 16px 24px;
                    font-size: 16px;
                    justify-content: center;
                }

                .file-upload-container {
                    flex-direction: column;
                    align-items: stretch;
                    gap: 12px;
                }

                .file-upload-label {
                    justify-content: center;
                    padding: 16px 20px;
                }

                .warning-box {
                    flex-direction: column;
                    text-align: center;
                    padding: 16px;
                }

                .warning-icon {
                    align-self: center;
                }
            }

            @media (max-width: 480px) {
                .request-form-container {
                    padding: 12px;
                }

                .modern-form-card {
                    border-radius: 12px;
                }

                .form-section {
                    padding: 16px;
                    margin-bottom: 16px;
                }

                .section-title {
                    font-size: 16px;
                    margin-bottom: 16px;
                }

                .form-control {
                    padding: 12px 14px;
                    font-size: 16px;
                    /* Prevents zoom on iOS */
                }

                .btn {
                    padding: 14px 20px;
                    font-size: 15px;
                }
            }

            .status {
                padding: 4px 8px;
                border-radius: 4px;
                font-size: 12px;
                font-weight: bold;
            }

            .status.pending {
                background-color: #fff3cd;
                color: #856404;
            }

            .status.accepted {
                background-color: #d4edda;
                color: #155724;
            }

            .status.declined {
                background-color: #f8d7da;
                color: #721c24;
            }

            /* Responsive Design */
            @media (max-width: 768px) {
                .sidebar {
                    transform: translateX(-100%);
                    transition: transform 0.3s ease;
                    z-index: 1000;
                }

                .sidebar.open {
                    transform: translateX(0);
                }

                .main-content {
                    margin-left: 0;
                }

                .content-box {
                    padding: 20px;
                }

                .step-item {
                    padding: 15px;
                }

                .donation-options {
                    flex-direction: column;
                    gap: 15px;
                }

                .donation-option {
                    padding: 20px;
                }

                .form-row {
                    flex-direction: column;
                    gap: 10px;
                }
            }

            /* Icons use Font Awesome elements (match admin) */

            /* Improved Health Screening UI */
            .card {
                background: #fff;
                border-radius: 12px;
                box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
                border: 1px solid #f0f1f3;
                overflow: hidden;
            }

            .card-header {
                background: linear-gradient(135deg, #ffb6ce, #ff69b4);
                color: #000;
                /* Make header text black */
                padding: 18px 24px;
                display: flex;
                align-items: center;
                gap: 12px;
                font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, 'Roboto', sans-serif;
                font-weight: 600;
                /* Undo bold: semibold */
                font-size: 14px;
                /* Match compact look in screenshot */
                letter-spacing: 0.2px;
                text-shadow: none;
            }

            .card-header i {
                color: #000;
            }

            .card-body {
                padding: 22px 24px;
            }

            .subtext {
                color: #667085;
                font-size: 14px;
                margin-top: 6px;
            }

            .section {
                border: 1px solid #eee;
                border-radius: 10px;
                margin-bottom: 18px;
                overflow: hidden;
            }

            .section-title {
                background: #fafbfc;
                padding: 14px 18px;
                font-weight: 700;
                color: #38404b;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .section-content {
                padding: 16px 18px;
            }

            .section-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                gap: 16px;
            }

            .field label {
                display: block;
                font-size: 12px;
                font-weight: 700;
                color: #6b7280;
                margin-bottom: 6px;
                text-transform: uppercase;
            }

            .field input[type="text"],
            .field input[type="number"],
            .field input[type="date"],
            .field select,
            .field textarea {
                width: 100%;
                padding: 10px 12px;
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                font-size: 14px;
                outline: none;
                background: #fff;
            }

            .helper {
                font-size: 12px;
                color: #9aa1ab;
                margin-top: 6px;
            }

            .pill {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 6px 10px;
                border-radius: 999px;
                font-size: 12px;
                font-weight: 700;
            }

            .pill.pending {
                background: #fff3cd;
                color: #856404;
            }

            .pill.accepted {
                background: #d4edda;
                color: #155724;
            }

            .pill.declined {
                background: #f8d7da;
                color: #721c24;
            }

            .actions {
                display: flex;
                gap: 10px;
                justify-content: flex-end;
                margin-top: 10px;
            }

            .btn-primary-solid {
                background: #ff69b4;
                color: #fff;
                border: none;
                padding: 10px 16px;
                border-radius: 8px;
                cursor: pointer;
                font-weight: 700;
            }

            .btn-secondary-outline {
                background: #ffb6ce;
                color: #333;
                border: 2px solid #ff69b4;
                padding: 10px 16px;
                border-radius: 8px;
                cursor: pointer;
                transition: all 0.3s ease;
                font-weight: 700;
            }

            /* Next button visual aligned with app forward action */
            .btn-next {
                background: #667eea;
                color: #fff;
                border: none;
                padding: 10px 16px;
                border-radius: 8px;
                cursor: pointer;
                font-weight: 700;
            }

            /* Proper submit (success) button */
            .btn-success {
                background: #28a745;
                color: #fff;
                border: none;
                padding: 10px 16px;
                border-radius: 8px;
                cursor: pointer;
                font-weight: 700;
            }

            .btn-secondary-outline:hover {
                background: #ffd1df;
                color: #ff69b4;
            }

            details.section summary {
                list-style: none;
                cursor: pointer;
            }

            details.section summary::-webkit-details-marker {
                display: none;
            }

            .chevron {
                margin-left: auto;
                transition: transform .2s ease;
            }

            details[open] .chevron {
                transform: rotate(180deg);
            }

            /* Notification badge */
            .notification-container {
                position: relative;
                display: inline-block;
            }

            .notification-badge {
                position: absolute;
                top: -6px;
                right: -6px;
                background-color: #ff4444;
                color: #fff;
                border-radius: 50%;
                padding: 2px 6px;
                font-size: 11px;
                font-weight: 700;
                min-width: 18px;
                line-height: 1.2;
                text-align: center;
                display: none;
            }

            /* Notification modal */
            .modal {
                display: none;
                position: fixed;
                z-index: 100;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
            }

            .modal-content {
                background: #fff;
                margin: 8% auto;
                border-radius: 10px;
                width: 90%;
                max-width: 700px;
                overflow: hidden;
            }

            .modal-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 16px 20px;
                background: #ff69b4;
                color: #fff;
            }

            .modal-body {
                padding: 20px;
                max-height: 60vh;
                overflow-y: auto;
            }

            .close-btn {
                background: none;
                border: none;
                color: #fff;
                font-size: 24px;
                cursor: pointer;
            }

            .notification-item {
                padding: 12px 0;
                border-bottom: 1px solid #eee;
            }

            .notification-item:last-child {
                border-bottom: none;
            }

            .notification-title {
                font-weight: 700;
                color: #333;
                margin-bottom: 6px;
            }

            .notification-message {
                color: #555;
                margin-bottom: 6px;
            }

            .notification-time {
                color: #999;
                font-size: 12px;
            }

            /* Inline Notification Styles - Matching Login Success Style */
            .inline-notification {
                background-color: #d4edda;
                color: #155724;
                padding: 10px 15px;
                border-radius: 4px;
                margin-bottom: 15px;
                border: 1px solid #c3e6cb;
                font-size: 14px;
                position: relative;
                transform: translateX(100%);
                opacity: 0;
                transition: all 0.5s ease-out;
                width: 100%;
                /* span full content width */
                max-width: none;
                /* remove narrow cap (was 400px) */
                box-sizing: border-box;
                /* include padding in width calc */
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                pointer-events: auto;
                /* allow interaction despite parent disabling */
            }

            .inline-notification.show {
                transform: translateX(0);
                opacity: 1;
            }

            .inline-notification.hide {
                transform: translateX(100%);
                opacity: 0;
            }

            .inline-notification.success {
                background-color: #d4edda;
                color: #155724;
                border: 1px solid #c3e6cb;
            }

            .inline-notification.error {
                background-color: #f8d7da;
                color: #721c24;
                border: 1px solid #f5c6cb;
            }

            .inline-notification.warning {
                background-color: #fff3cd;
                color: #856404;
                border: 1px solid #ffeaa7;
            }

            .inline-notification.info {
                background-color: #d1ecf1;
                color: #0c5460;
                border: 1px solid #bee5eb;
            }

            .inline-notification-close {
                position: absolute;
                top: 5px;
                right: 10px;
                background: none;
                border: none;
                font-size: 16px;
                color: #155724;
                cursor: pointer;
                font-weight: bold;
                padding: 0;
                width: 20px;
                height: 20px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.2s ease;
                pointer-events: auto;
            }

            .inline-notification-close:hover {
                color: #0d4a0d;
                transform: scale(1.1);
            }

            .inline-notification-message {
                margin: 0;
                padding-right: 25px;
                line-height: 1.4;
            }

            /* Ensure titles inside collapsible bars are readable: black text on pink bar */
            details.section>summary.section-title {
                background: linear-gradient(90deg, #ffc1d8 0%, #ff69b4 60%, #ff5fb0 100%);
                color: #000000 !important;
                -webkit-text-fill-color: #000000 !important;
                -webkit-background-clip: border-box !important;
                background-clip: border-box !important;
                padding: 14px 18px;
                font-weight: 600;
                /* Undo bold: semibold */
                font-size: 16px;
                display: flex;
                align-items: center;
                gap: 8px;
                list-style: none;
                border-top-left-radius: 10px;
                border-top-right-radius: 10px;
                text-shadow: none;
                font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, 'Roboto', sans-serif;
            }

            /* Hide any decorative before element that could overlay the text */
            details.section>summary.section-title::before {
                display: none !important;
            }

            /* Match icons to readable black */
            details.section>summary.section-title i {
                color: #000 !important;
                filter: none;
            }

            /* Health Screening UX improvements */
            details.section {
                border: 1px solid #f0d3df;
                border-radius: 10px;
                background: #fff;
                margin-bottom: 14px;
                overflow: hidden;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.04);
            }

            /* Keep same layout but stronger bar handled above */
            details.section[open]>summary.section-title {
                border-bottom: 1px solid #f4c6d6;
            }

            details.section>summary.section-title:hover {
                filter: brightness(0.98);
                cursor: pointer;
            }

            details.section>summary.section-title::marker {
                display: none;
            }

            details.section>summary.section-title::-webkit-details-marker {
                display: none;
            }

            details.section>summary.section-title .chevron {
                margin-left: auto;
                transition: transform .2s ease;
            }

            details.section[open]>summary.section-title .chevron {
                transform: rotate(180deg);
            }

            .section-content {
                padding: 16px 18px;
                background: #fff;
            }

            .section-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                gap: 16px;
            }

            .field label {
                display: block;
                font-size: 12px;
                font-weight: 700;
                color: #6b7280;
                margin-bottom: 6px;
                text-transform: uppercase;
            }

            .field input[type="text"],
            .field input[type="number"],
            .field input[type="date"],
            .field select,
            .field textarea {
                width: 100%;
                padding: 10px 12px;
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                font-size: 14px;
                outline: none;
                background: #fff;
            }

            .field input:focus,
            .field select:focus,
            .field textarea:focus {
                border-color: #ff69b4;
                box-shadow: 0 0 0 3px rgba(255, 105, 180, 0.15);
            }

            .radio-group {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
            }

            .radio-group label {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 8px 12px;
                border: 1px solid #e5e7eb;
                border-radius: 999px;
                cursor: pointer;
                background: #fff;
            }

            .radio-group input[type="radio"] {
                accent-color: #ff69b4;
            }

            .radio-group label:hover {
                border-color: #ff69b4;
                background: #fff6fa;
            }

            .question-group {
                padding: 10px 0;
                border-bottom: 1px dashed #f0d3df;
            }

            .question-group:last-child {
                border-bottom: none;
            }

            .invalid-field {
                border-color: #dc3545 !important;
                box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1) !important;
            }

            .error-text {
                color: #dc3545;
                font-size: 12px;
                margin-top: 4px;
            }

            /* Strong error state like screenshot: red outline + subtle bg */
            .question-group.error,
            .field.error {
                border: 1.5px solid #dc3545 !important;
                border-radius: 12px !important;
                background: #fffafa;
                padding: 12px 12px !important;
            }

            /* Brief attention pulse */
            .error-flash {
                animation: errorFlash 1.1s ease-out;
            }

            @keyframes errorFlash {
                0% {
                    box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
                }

                30% {
                    box-shadow: 0 0 0 4px rgba(220, 53, 69, 0.18);
                }

                100% {
                    box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
                }
            }

            /* Mobile responsiveness */
            @media (max-width: 768px) {

                /* Make inline notifications stretch edge-to-edge on small screens */
                #inline-notification-container {
                    top: 10px;
                    right: 10px;
                    left: 10px;
                    max-width: none;
                }

                .inline-notification {
                    padding: 10px 15px;
                    font-size: 14px;
                }
            }

            .modal-actions {
                padding: 12px 20px;
                border-top: 1px solid #eee;
                text-align: right;
            }

            .btn-link {
                background: none;
                border: none;
                color: #007bff;
                cursor: pointer;
                font-size: 14px;
            }

            /* Custom Confirmation Modal Styles */
            .confirmation-modal {
                display: none;
                position: fixed;
                z-index: 10050;
                /* above top bar (3000) */
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                overflow: auto;
                background-color: rgba(0, 0, 0, 0.6);
            }

            .confirmation-modal-content {
                background-color: #fefefe;
                margin: 15% auto;
                padding: 0;
                border-radius: 12px;
                width: 90%;
                max-width: 400px;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
                animation: modalSlideIn 0.3s ease-out;
            }

            @keyframes modalSlideIn {
                from {
                    opacity: 0;
                    transform: translateY(-50px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .confirmation-modal-header {
                padding: 20px 25px 15px;
                border-bottom: 1px solid #eee;
                text-align: center;
            }

            .confirmation-modal-title {
                margin: 0;
                font-size: 18px;
                font-weight: 600;
                color: #333;
            }

            .confirmation-modal-body {
                padding: 20px 25px;
                text-align: center;
                color: #666;
                line-height: 1.5;
            }

            .confirmation-modal-footer {
                padding: 15px 25px 25px;
                text-align: center;
                display: flex;
                gap: 10px;
                justify-content: center;
            }

            .confirmation-btn {
                padding: 10px 20px;
                border: none;
                border-radius: 6px;
                cursor: pointer;
                font-size: 14px;
                font-weight: 500;
                min-width: 80px;
                transition: all 0.2s ease;
            }

            .confirmation-btn-cancel {
                background-color: #ffb6ce;
                color: #333;
                border: 2px solid #ff69b4;
            }

            .confirmation-btn-cancel:hover {
                background-color: #ffd1df;
                color: #ff69b4;
            }

            .confirmation-btn-confirm {
                background-color: #ff69b4;
                color: white;
            }

            .confirmation-btn-confirm:hover {
                background-color: #e55a9e;
            }

            /* Modal Styles */
            .modal-calendar {
                display: none;
                position: fixed;
                z-index: 10050;
                /* above top bar (3000) */
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                overflow: auto;
                background-color: rgba(0, 0, 0, 0.6);
            }

            /* Disable hover/clicks on app chrome when a modal is open */
            body.modal-open .top-bar,
            body.modal-open .sidebar {
                pointer-events: none !important;
            }

            body.modal-open .nav-item,
            body.modal-open .hamburger-btn,
            body.modal-open .notification-btn,
            body.modal-open .logout-btn {
                pointer-events: none !important;
            }

            .modal-calendar-content {
                background-color: #fefefe;
                margin: 2% auto;
                padding: 25px;
                border-radius: 12px;
                width: 95%;
                max-width: 1000px;
                max-height: 90vh;
                overflow-y: auto;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
                position: relative;
            }

            .close-calendar-modal {
                color: #aaa;
                position: absolute;
                top: 15px;
                right: 25px;
                font-size: 35px;
                font-weight: bold;
                cursor: pointer;
            }

            /* Vanilla calendar (walk-in) */
            .vc-calendar {
                margin-top: 10px;
            }

            .vc-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                margin-bottom: 10px;
            }

            .vc-title {
                font-weight: 700;
                font-size: 16px;
                color: #111;
            }

            .vc-nav-btn {
                background: #ffd1df;
                color: #ff69b4;
                border: 2px solid #ff69b4;
                border-radius: 8px;
                padding: 6px 10px;
                cursor: pointer;
                font-weight: 600;
            }

            .vc-nav-btn:hover {
                background: #ffb6c9;
            }

            .vc-grid {
                display: grid;
                grid-template-columns: repeat(7, 1fr);
                gap: 6px;
            }

            .vc-weekday {
                text-align: center;
                font-weight: 700;
                font-size: 12px;
                color: #6b7280;
                padding: 6px 0;
            }

            .vc-day {
                text-align: center;
                padding: 10px 0;
                border-radius: 8px;
                font-weight: 600;
                color: #9ca3af;
                background: #f3f4f6;
                cursor: not-allowed;
            }

            .vc-day.available {
                background: #ffc0cb;
                border: 2px solid #ff69b4;
                color: #111;
                cursor: pointer;
            }

            .vc-day.available:hover {
                background: #ffb6c1;
            }

            .vc-day.selected {
                outline: 3px solid #ff1493;
                outline-offset: 2px;
            }


            .fc-day-disabled {
                background-color: #f5f5f5;
                cursor: not-allowed;
            }

            .available-day {
                background-color: #d4edda !important;
            }

            .day-selected {
                background-color: #ffc107 !important;
            }

            .time-slot-buttons {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                margin-top: 10px;
            }

            .time-slot-btn {
                padding: 12px 16px;
                border: 2px solid #ff69b4;
                border-radius: 10px;
                background-color: #fff;
                color: #ff69b4;
                cursor: pointer;
                transition: all 0.3s ease;
                font-weight: 500;
                font-size: 14px;
                min-width: 120px;
                text-align: center;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            .time-slot-btn.selected,
            .time-slot-btn:hover {
                background-color: #ff69b4;
                color: #fff;
                transform: translateY(-2px);
                box-shadow: 0 6px 12px rgba(255, 105, 180, 0.4);
            }

            .time-slot-btn:active {
                transform: translateY(0);
                box-shadow: 0 2px 4px rgba(255, 105, 180, 0.3);
            }

            /* Disabled/unavailable time slot appearance (non-interactive) */
            .time-slot-btn.disabled,
            .time-slot-btn[disabled] {
                opacity: 0.6;
                cursor: not-allowed;
                background: #f9fafb;
                border-color: #eee;
                color: #9aa0a6;
                box-shadow: none;
                transform: none;
            }

            .time-slot-btn.disabled:hover,
            .time-slot-btn[disabled]:hover {
                background: #f9fafb;
                color: #9aa0a6;
                transform: none;
                box-shadow: none;
            }

            /* Time slot container styling */
            .time-slot-buttons-container {
                background: #f8f9fa;
                padding: 20px;
                border-radius: 12px;
                border: 1px solid #e9ecef;
                margin-top: 20px;
            }

            .selected-date-info {
                background: linear-gradient(135deg, #ffb6ce, #ff69b4);
                color: white;
                padding: 15px;
                border-radius: 8px;
                margin-bottom: 20px;
                text-align: center;
            }

            .selected-date-info h4 {
                color: white !important;
                margin: 0 0 5px 0;
                font-size: 18px;
            }

            .selected-date-info p {
                color: rgba(255, 255, 255, 0.9) !important;
                margin: 0;
                font-size: 14px;
            }

            .time-slots-grid {
                min-height: 60px;
            }

            /* Calendar modal specific styling */
            #walkInCalendar,
            #homeCollectionCalendar {
                max-width: 100%;
                margin: 0 auto;
            }

            #walkInCalendar .fc-view-harness,
            #homeCollectionCalendar .fc-view-harness {
                height: auto !important;
                min-height: 400px;
            }

            /* Walk-in calendar styling - same as admin */
            #walkInCalendar .fc-header-toolbar {
                margin-bottom: 1.5em !important;
            }

            #walkInCalendar .fc-daygrid-day.fc-day-today {
                background-color: #fff0f5 !important;
            }

            #walkInCalendar .fc-event {
                cursor: default;
            }

            #walkInCalendar .fc-daygrid-day-frame {
                cursor: pointer;
            }

            /* Ensure calendar table fits in modal */
            .modal-calendar .fc-daygrid-body {
                width: 100% !important;
            }

            .modal-calendar .fc-col-header-cell,
            .modal-calendar .fc-daygrid-day {
                min-height: 40px;
            }

            #walkInTimeModal {
                display: none;
            }

            /* Enhanced Home Collection Modal Styles */
            .home-collection-modal {
                max-width: 700px !important;
                max-height: 90vh;
                overflow-y: auto;
            }

            .home-collection-header {
                text-align: center;
                margin-bottom: 30px;
                padding: 0 20px;
            }

            .home-collection-header h2 {
                color: #333;
                font-size: 28px;
                font-weight: 700;
                margin-bottom: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 12px;
            }

            .home-collection-header h2 i {
                color: #ff69b4;
                font-size: 26px;
            }

            .home-collection-header .subtitle {
                color: #666;
                font-size: 15px;
                margin: 0;
                font-weight: 400;
            }

            .home-collection-form {
                padding: 0 20px 20px;
            }

            .form-section {
                background: rgba(255, 249, 252, 0.7);
                border: 1px solid rgba(255, 105, 180, 0.15);
                border-radius: 16px;
                padding: 24px;
                margin-bottom: 24px;
                position: relative;
                overflow: hidden;
            }

            .form-section::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 3px;
                background: linear-gradient(135deg, #ff69b4, #ff8fa3);
            }

            .section-title {
                font-size: 18px;
                font-weight: 700;
                color: #333;
                margin-bottom: 20px;
                display: flex;
                align-items: center;
                gap: 10px;
                padding-bottom: 8px;
                border-bottom: 1px solid rgba(255, 105, 180, 0.2);
            }

            .section-title i {
                color: #ff69b4;
                font-size: 16px;
            }

            .form-row {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
                margin-bottom: 20px;
            }

            .form-group {
                margin-bottom: 20px;
            }

            .form-group label {
                display: flex;
                align-items: center;
                gap: 8px;
                font-weight: 600;
                color: #333;
                margin-bottom: 8px;
                font-size: 14px;
            }

            .form-group label i {
                color: #ff69b4;
                font-size: 14px;
            }

            .required {
                color: #dc3545;
                font-weight: 700;
            }

            .form-input {
                width: 100%;
                padding: 12px 16px;
                border: 2px solid #e1e5e9;
                border-radius: 10px;
                font-size: 14px;
                background: #fff;
                transition: all 0.3s ease;
                box-sizing: border-box;
            }

            .form-input:focus {
                outline: none;
                border-color: #ff69b4;
                box-shadow: 0 0 0 3px rgba(255, 105, 180, 0.15);
                background: #fff;
            }

            .form-input:invalid {
                border-color: #dc3545;
            }

            .form-input.error {
                border-color: #dc3545;
                background: #fff5f5;
            }

            .help-text {
                font-size: 12px;
                color: #6b7280;
                margin-top: 4px;
                display: flex;
                align-items: center;
                gap: 4px;
            }

            .help-text i {
                font-size: 11px;
                color: #9ca3af;
            }

            .error-message {
                background: #fee2e2;
                color: #991b1b;
                padding: 8px 12px;
                border-radius: 8px;
                font-size: 12px;
                margin-top: 6px;
                display: flex;
                align-items: center;
                gap: 6px;
            }

            .error-message i {
                color: #dc2626;
            }

            .map-section {
                background: rgba(255, 255, 255, 0.8);
                border: 1px solid rgba(255, 105, 180, 0.1);
                border-radius: 12px;
                padding: 20px;
                margin-top: 10px;
            }

            .map-search {
                margin-bottom: 15px;
            }

            .pickup-map {
                width: 100%;
                height: 280px;
                border-radius: 12px;
                border: 1px solid #e5e7eb;
                background: #f9fafb;
                margin-bottom: 10px;
            }

            .form-actions {
                display: flex;
                justify-content: flex-end;
                gap: 12px;
                margin-top: 30px;
                padding: 20px 0 0;
                border-top: 1px solid rgba(255, 105, 180, 0.15);
            }

            .btn {
                padding: 12px 24px;
                border-radius: 10px;
                font-size: 14px;
                font-weight: 600;
                border: none;
                cursor: pointer;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                gap: 8px;
                min-width: 120px;
                justify-content: center;
            }

            .btn-secondary {
                background: #f3f4f6;
                color: #374151;
                border: 1px solid #d1d5db;
            }

            .btn-secondary:hover {
                background: #e5e7eb;
                transform: translateY(-1px);
            }

            .btn-primary {
                background: linear-gradient(135deg, #ff69b4, #e91e63);
                color: #fff;
                box-shadow: 0 4px 12px rgba(255, 105, 180, 0.3);
                position: relative;
                overflow: hidden;
            }

            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(255, 105, 180, 0.4);
            }

            .btn-primary:disabled {
                opacity: 0.7;
                cursor: not-allowed;
                transform: none;
            }

            .btn-loading {
                display: none;
            }

            .btn-primary.loading .btn-text {
                display: none;
            }

            .btn-primary.loading .btn-loading {
                display: flex;
                align-items: center;
                gap: 8px;
            }

            /* Responsive Design */
            @media (max-width: 768px) {
                .home-collection-modal {
                    max-width: 95% !important;
                    margin: 10px;
                }

                .form-row {
                    grid-template-columns: 1fr;
                    gap: 15px;
                }

                .form-section {
                    padding: 20px 16px;
                }

                .home-collection-header h2 {
                    font-size: 24px;
                }

                .form-actions {
                    flex-direction: column-reverse;
                }

                .btn {
                    width: 100%;
                }
            }

            @media (max-width: 480px) {
                .home-collection-form {
                    padding: 0 10px 10px;
                }

                .form-section {
                    padding: 16px 12px;
                    margin-bottom: 16px;
                }

                .section-title {
                    font-size: 16px;
                }

                .pickup-map {
                    height: 200px;
                }
            }
    </style>
</head>

<body>
    <!-- Sidebar (matches admin classes/palette) -->
    <div class="sidebar angle-135 brand-rose-dark" id="sidebar">
        <div class="logo-container">
            <img src="{{ asset('logo.png') }}" alt="Logo" class="logo">
            <div class="user-label">{{ session('user_name', 'USER') }}</div>
        </div>

        <nav class="nav-menu">
            <a href="#" class="nav-item active" onclick="showDashboard()">
                <span class="nav-icon"><i class="fas fa-home"></i></span>
                <span class="nav-text">DASHBOARD</span>
            </a>

            <a href="#" class="nav-item" onclick="showHealthScreening()">
                <span class="nav-icon"><i class="fas fa-stethoscope"></i></span>
                <span class="nav-text">Health Screening</span>
            </a>

            <a href="#" class="nav-item" onclick="showDonate()">
                <span class="nav-icon"><i class="fas fa-hand-holding-medical"></i></span>
                <span class="nav-text">Donate</span>
            </a>

            <a href="#" class="nav-item" onclick="showPending()">
                <span class="nav-icon"><i class="fas fa-clock"></i></span>
                <span class="nav-text">Pending Donations</span>
            </a>

            <a href="#" class="nav-item" onclick="showDonationHistory()">
                <span class="nav-icon"><i class="fas fa-file-medical"></i></span>
                <span class="nav-text">My Donation History</span>
            </a>

            <a href="#" class="nav-item" onclick="showBreastMilkRequest()">
                <span class="nav-icon"><i class="fas fa-baby"></i></span>
                <span class="nav-text">BreastMilk Request</span>
            </a>
            <a href="{{ route('user.settings') }}" class="nav-item" id="nav-settings-link"
                onclick="showSettings(event)">
                <span class="nav-icon"><i class="fas fa-user-cog"></i></span>
                <span class="nav-text">Settings</span>
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content top-bar-space">
        <!-- Top bar styled like admin -->
        <div class="top-bar admin-top-bar">
            <div class="top-bar-left">
                <button class="hamburger-btn" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
                <div class="top-bar-logos">
                    <img src="/hospital logo.png" alt="Hospital Logo" />
                    <img src="/logo.png" alt="HMBLSC Logo" />
                </div>
                <div class="top-bar-title-wrap">
                    <h1 id="pageTitle">Dashboard</h1>
                    <div class="top-bar-subtitle">Cagayan de Oro City - Human Milk Bank &amp; Lactation Support Center
                    </div>
                </div>
            </div>
            <div class="top-bar-right">
                <div class="notification-container">
                    <button class="notification-btn" onclick="showUserNotifications()"><i
                            class="fas fa-bell"></i></button>
                    <span id="user-notification-badge" class="notification-badge">0</span>
                </div>
                <button class="logout-btn" onclick="logout()">Logout</button>
            </div>
        </div>
        <script>
            // Measure and set CSS var for top-bar height on user pages as well
            (function () {
                function setTopBarHeightVar() {
                    try {
                        var tb = document.querySelector('.admin-top-bar');
                        if (!tb) return;
                        var h = tb.getBoundingClientRect().height;
                        document.documentElement.style.setProperty('--admin-topbar-height', h + 'px');
                    } catch (e) { }
                }
                window.addEventListener('load', setTopBarHeightVar);
                window.addEventListener('resize', function () {
                    clearTimeout(window.__tbHU);
                    window.__tbHU = setTimeout(setTopBarHeightVar, 100);
                });
                document.addEventListener('readystatechange', function () { if (document.readyState === 'complete') setTopBarHeightVar(); });
            })();
        </script>

        <!-- Inline Notification Container (doesn't block clicks on sidebar) -->
        <div id="inline-notification-container">
            <!-- Notifications dynamically inserted here -->
        </div>

        @if(session('success'))
            <script>
                (function () {
                    var msg = @json(session('success'));
                    // Delay until DOM ready to ensure container exists
                    document.addEventListener('DOMContentLoaded', function () {
                        try { if (typeof showInlineNotification === 'function') { showInlineNotification('success', '', String(msg || 'Success'), 5000); } }
                        catch (e) { console.log(msg); }
                    });
                })();
            </script>
        @endif
        @if(session('error'))
            <div class="error-message">
                {{ session('error') }}
            </div>
        @endif


        <!-- User Notifications Modal -->
        <div id="user-notification-modal" class="modal-calendar" style="display:none;">
            <div class="modal-calendar-content" style="max-width:520px;">
                <span class="close-calendar-modal" onclick="closeModal('user-notification-modal')">&times;</span>
                <h2 style="margin-top:0;">Notifications</h2>
                <div id="user-notifications-list"></div>
                <div style="display:flex;justify-content:space-between;margin-top:12px;gap:10px;">
                    <button class="submit-btn" type="button" onclick="deleteAllUserNotifications()"
                        style="background:linear-gradient(135deg, #dc3545, #c82333);">Delete All</button>
                    <button class="submit-btn" type="button" onclick="markAllUserNotificationsRead()">Mark all as
                        read</button>
                </div>
            </div>
        </div>

        <!-- Walk-in Donation Modal -->
        <div id="walkInModal" class="modal-calendar">
            <div class="modal-calendar-content">
                <span class="close-calendar-modal" onclick="closeModal('walkInModal')">&times;</span>
                <h2>Walk-in Donation Appointment</h2>
                <div
                    style="background: #e7f3ff; border: 1px solid #b3d9ff; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
                    <h4 style="color: #0066cc; margin: 0 0 8px 0; font-size: 16px;"><i class="fas fa-calendar-alt"></i>
                        How to Book Your Appointment:</h4>
                    <ol style="color: #333; margin: 0; padding-left: 20px; font-size: 14px;">
                        <li>Look for dates highlighted in <strong style="color: #ff69b4;">pink</strong> - these are
                            available for donations</li>
                        <li>Click on your preferred available date</li>
                        <li>Select a 1-hour time slot that works for you</li>
                        <li>Click "Book Appointment" to confirm</li>
                    </ol>
                </div>
                <div id="walkInCalendar"></div>

                <div class="time-slot-buttons-container" style="margin-top: 20px;">
                    <div class="selected-date-info" style="display: none; text-align: center; margin-bottom: 15px;">
                        <h4 style="color: #ff69b4; margin: 0;">Selected Date: <span id="selectedDateDisplay"></span>
                        </h4>
                        <p style="color: #666; margin: 5px 0;">Choose your preferred 1-hour time slot:</p>
                    </div>
                    <div class="time-slots-grid"></div>
                </div>
                <form id="walkInFormModal" action="{{ route('donation.walk-in') }}" method="POST"
                    style="margin-top: 20px;">
                    @csrf
                    <input type="hidden" name="donation_date" id="walkInDateModal">
                    <input type="hidden" name="donation_time" id="walkInTimeModal" required>
                    <button type="submit" class="submit-btn">Book Appointment</button>
                </form>
            </div>
        </div>

        <!-- Home Collection Modal -->
        <div id="homeCollectionModal" class="modal-calendar">
            <div class="modal-calendar-content home-collection-modal">
                <span class="close-calendar-modal" onclick="closeModal('homeCollectionModal')">&times;</span>
                <div class="home-collection-header">
                    <h2><i class="fas fa-home"></i> Home Collection Request</h2>
                    <p class="subtitle">Schedule a convenient pickup at your location</p>
                </div>

                <form id="homeCollectionFormModal" action="{{ route('donation.home-collection') }}" method="POST"
                    class="home-collection-form">
                    @csrf

                    <!-- Donation Details Section -->
                    <div class="form-section">
                        <h3 class="section-title"><i class="fas fa-box"></i> Donation Details</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="number_of_bags">
                                    <i class="fas fa-shopping-bag"></i>
                                    Number of Bags
                                    <span class="required">*</span>
                                </label>
                                <input type="number" name="number_of_bags" id="number_of_bags" min="1" max="50" required
                                    placeholder="e.g., 5" class="form-input">
                                <small class="help-text">How many sealed storage bags do you have?</small>
                            </div>
                            <div class="form-group">
                                <label for="total_volume">
                                    <i class="fas fa-tint"></i>
                                    Total Volume (ml)
                                    <span class="required">*</span>
                                </label>
                                <input type="number" name="total_volume" id="total_volume" min="1" max="5000" required
                                    placeholder="e.g., 500" class="form-input">
                                <small class="help-text">Total amount in milliliters</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="homeCollectionDateModal">
                                <i class="fas fa-calendar-alt"></i>
                                Date Milk Was Collected
                                <span class="required">*</span>
                            </label>
                            <input type="date" name="date_collected" id="homeCollectionDateModal"
                                max="{{ date('Y-m-d') }}" required class="form-input">
                            <small class="help-text">When did you extract/pump this milk? (Must be today or
                                earlier)</small>
                        </div>
                    </div>

                    <!-- Pickup Information Section -->
                    <div class="form-section">
                        <h3 class="section-title"><i class="fas fa-map-marker-alt"></i> Pickup Information</h3>
                        <div class="form-group">
                            <label for="pickup_address">
                                <i class="fas fa-home"></i>
                                Pickup Address
                                <span class="required">*</span>
                            </label>
                            <textarea name="pickup_address" id="pickup_address" rows="3" required maxlength="500"
                                pattern="^[a-zA-Z0-9\s,.\-#]+$"
                                title="Address can only contain letters, numbers, spaces, and common symbols (,.-#)"
                                oninput="validateAddress(this)" class="form-input"
                                oninvalid="this.setCustomValidity('Address can only contain letters, numbers, spaces, and common symbols (,.-#)')"
                                placeholder="Enter your complete pickup address...">{{ $userAddress }}</textarea>
                            <small class="help-text">
                                <i class="fas fa-info-circle"></i>
                                Must match your registered address. Include house number, street, barangay, city.
                            </small>
                            <div id="pickup_address_alert" class="error-message" style="display:none;">
                                <i class="fas fa-exclamation-triangle"></i>
                                Address can only contain letters, numbers, spaces, and common symbols (,.-#). Maximum
                                500 characters.
                            </div>
                        </div>

                        <!-- Map Section -->
                        <div class="form-group map-section">
                            <label for="pickup_search">
                                <i class="fas fa-search-location"></i>
                                Locate on Map (Optional)
                            </label>
                            <input type="text" id="pickup_search"
                                placeholder="Search for your address or nearby landmark" class="form-input map-search"
                                autocomplete="off">
                            <div id="pickup_map" class="pickup-map"></div>
                            <small class="help-text">
                                <i class="fas fa-map"></i>
                                Search your address or drag the pin to help us find your exact location.
                            </small>
                            <input type="hidden" name="pickup_lat" id="pickup_lat">
                            <input type="hidden" name="pickup_lng" id="pickup_lng">
                        </div>
                    </div>

                    <!-- Submit Section -->
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('homeCollectionModal')">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary submit-btn">
                            <i class="fas fa-paper-plane"></i>
                            <span class="btn-text">Submit Request</span>
                            <span class="btn-loading" style="display:none;">
                                <i class="fas fa-spinner fa-spin"></i> Submitting...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Confirmation Modal -->
        <div id="confirmationModal" class="confirmation-modal">
            <div class="confirmation-modal-content">
                <div class="confirmation-modal-header">
                    <h3 class="confirmation-modal-title" id="confirmationTitle">Confirm Action</h3>
                </div>
                <div class="confirmation-modal-body">
                    <p id="confirmationMessage">Are you sure you want to perform this action?</p>
                </div>
                <div class="confirmation-modal-footer">
                    <button class="confirmation-btn confirmation-btn-cancel"
                        onclick="closeConfirmationModal()">Cancel</button>
                    <button class="confirmation-btn confirmation-btn-confirm"
                        id="confirmationConfirmBtn">Confirm</button>
                </div>
            </div>
        </div>

        <!-- Dashboard Content -->
        <div id="dashboard-view" class="dashboard-content">
            <div class="content-box">
                <h1 class="content-title">Welcome, {{ session('user_name', 'User') }}!</h1>
                <h2 class="content-title">Step-by-step guide on how to donate breastmilk:</h2>

                <div class="step-guide compact-steps">
                    <div class="step-item">
                        <div class="step-number">1</div>
                        <div class="step-title">Undergo Health Screening</div>
                        <div class="step-description">
                            The milk bank or hospital will inquire about your medical history, lifestyle, medications,
                            and travel history. They may also require blood tests to ensure you are healthy and free
                            from diseases like HIV or hepatitis.
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="step-number">2</div>
                        <div class="step-title">Get Approved as a Donor</div>
                        <div class="step-description">
                            Once your health screening is successful, you will be approved as a donor and can begin the
                            donation process.
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="step-number">3</div>
                        <div class="step-title">Follow Safe Milk Expression and Storage</div>
                        <div class="step-description">
                            Use clean hands and sterilized equipment when expressing milk. Store the expressed milk in
                            clean, labeled containers with the date and time.
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="step-number">4</div>
                        <div class="step-title">Freeze and Store the Milk Properly</div>
                        <div class="step-description">
                            Freeze the milk immediately after pumping and store it in a deep freezer until collection,
                            as required by some milk banks.
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="step-number">5</div>
                        <div class="step-title">Coordinate Milk Pickup or Drop-off</div>
                        <div class="step-description">
                            Some milk banks offer pickup services, while others may require you to drop off the milk at
                            a hospital or milk bank location.
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="step-number">6</div>
                        <div class="step-title">Repeat as Needed</div>
                        <div class="step-description">
                            Continue donating as long as you are approved and producing excess milk. Regular donations
                            help maintain a steady supply for babies in need.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        // Sidebar toggle functionality (ported from admin)
        (function () {
            if (window.__userSidebarToggleInit) return;
            window.__userSidebarToggleInit = true;

            function isMobile() { return window.matchMedia('(max-width: 768px)').matches; }

            function ensureOverlay() {
                var overlay = document.getElementById('user-sidebar-overlay');
                if (!overlay) {
                    overlay = document.createElement('div');
                    overlay.id = 'user-sidebar-overlay';
                    overlay.className = 'sidebar-overlay';
                    document.body.appendChild(overlay);
                    overlay.addEventListener('click', function () { closeMobileSidebar(); });
                }
                return overlay;
            }

            function openMobileSidebar() {
                var sidebar = document.getElementById('sidebar');
                if (!sidebar) return;
                sidebar.classList.add('open');
                ensureOverlay().classList.add('active');
                document.body.classList.add('no-scroll');
                try {
                    var btn = document.querySelector('.hamburger-btn');
                    if (btn) {
                        var icon = btn.querySelector('i');
                        if (icon) { icon.classList.remove('fa-bars', 'fa-times'); icon.classList.add('fa-times'); }
                        btn.setAttribute('aria-expanded', 'true');
                        btn.setAttribute('aria-label', 'Close sidebar');
                    }
                } catch (e) { }
            }
            function closeMobileSidebar() {
                var sidebar = document.getElementById('sidebar');
                var overlay = document.getElementById('user-sidebar-overlay');
                if (sidebar) sidebar.classList.remove('open');
                if (overlay) overlay.classList.remove('active');
                document.body.classList.remove('no-scroll');
                try {
                    var btn = document.querySelector('.hamburger-btn');
                    if (btn) {
                        var icon = btn.querySelector('i');
                        if (icon) { icon.classList.remove('fa-bars', 'fa-times'); icon.classList.add('fa-bars'); }
                        btn.setAttribute('aria-expanded', 'false');
                        btn.setAttribute('aria-label', 'Open sidebar');
                    }
                } catch (e) { }
            }

            window.toggleSidebar = function () {
                var sidebar = document.getElementById('sidebar');
                var main = document.querySelector('.main-content');
                if (!sidebar) return;
                if (isMobile()) {
                    if (sidebar.classList.contains('open')) closeMobileSidebar(); else openMobileSidebar();
                } else {
                    sidebar.classList.toggle('collapsed');
                    if (main) main.classList.toggle('expanded');
                }
            };

            document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && isMobile()) closeMobileSidebar(); });
            document.addEventListener('click', function (e) {
                var link = e.target && e.target.closest ? e.target.closest('.nav-menu a') : null;
                if (!link) return;
                if (isMobile()) closeMobileSidebar();
            });
            var resizeTimer = null;
            window.addEventListener('resize', function () {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function () { if (!isMobile()) closeMobileSidebar(); }, 150);
            });
        })();

        // Close any open overlays/modals to prevent invisible blockers
        function closeAllOverlays() {
            try {
                // Hide known modal shells
                document.querySelectorAll('.modal, .modal-calendar, .confirmation-modal').forEach(el => {
                    el.style.display = 'none';
                });
                // Remove ad-hoc overlays we create dynamically
                const hsOverlay = document.getElementById('hs-incomplete-overlay');
                if (hsOverlay && hsOverlay.parentNode) hsOverlay.parentNode.removeChild(hsOverlay);
                const review = document.getElementById('health-review-modal');
                if (review && review.parentNode) review.parentNode.removeChild(review);
                // Deactivate any sidebar overlays and ensure sidebar is closed on mobile
                document.querySelectorAll('.sidebar-overlay').forEach(ov => ov.classList.remove('active'));
                const userSide = document.getElementById('sidebar');
                if (userSide && userSide.classList.contains('open')) userSide.classList.remove('open');
                document.body.style.overflow = 'auto';
                // Always re-enable pointer events for sidebar and top bar
                const sidebar = document.getElementById('sidebar');
                if (sidebar) sidebar.style.pointerEvents = 'auto';
                const topBar = document.querySelector('.top-bar');
                if (topBar) topBar.style.pointerEvents = 'auto';
                document.querySelectorAll('.logout-btn, .nav-item, .hamburger-btn, .notification-btn').forEach(btn => {
                    btn.style.pointerEvents = 'auto';
                });
            } catch (_) { }
        }

        // Ensure sidebar nav clicks always work
        function wireSidebarNav() {
            const menu = document.querySelector('.nav-menu');
            if (!menu) return;
            menu.querySelectorAll('.nav-item').forEach((a) => {
                a.addEventListener('click', function (e) {
                    const label = (this.querySelector('.nav-text')?.textContent || '').trim().toLowerCase();
                    try {
                        if (label === 'dashboard') { e.preventDefault(); e.stopPropagation(); closeAllOverlays(); return showDashboard(); }
                        if (label === 'health screening') { e.preventDefault(); e.stopPropagation(); closeAllOverlays(); return showHealthScreening(); }
                        if (label === 'donate') { e.preventDefault(); e.stopPropagation(); closeAllOverlays(); return showDonate(); }
                        if (label === 'pending donations') { e.preventDefault(); e.stopPropagation(); closeAllOverlays(); return showPending(); }
                        if (label === 'my donation history') { e.preventDefault(); e.stopPropagation(); closeAllOverlays(); return showDonationHistory(); }
                        if (label === 'breastmilk request') { e.preventDefault(); e.stopPropagation(); closeAllOverlays(); return showBreastMilkRequest(); }
                    } catch (err) {
                        console.warn('Nav handler error:', err);
                    }
                    // if not matched, allow inline onclick handler to run
                });
            });
        }
        // Wire once on load
        wireSidebarNav();

        // Safety: close any stray overlays on load (in case of back/forward cache)
        window.addEventListener('load', () => {
            closeAllOverlays();
            // run twice to catch late-mounted nodes
            setTimeout(closeAllOverlays, 50);
        });

        // UX: allow ESC to quickly dismiss any open overlay
        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeAllOverlays();
            }
        });

        // No carousel: steps are displayed as responsive cards

        // Guard health screening submission: block if required fields are missing
        document.addEventListener('click', function (e) {
            var t = e.target;
            if (t && t.id === 'health-next-btn') {
                var form = t.closest('form');
                if (form && !form.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (typeof e.stopImmediatePropagation === 'function') e.stopImmediatePropagation();
                    if (typeof form.reportValidity === 'function') form.reportValidity();
                    try {
                        var firstInvalid = form.querySelector(':invalid');
                        if (firstInvalid) {
                            var panel = firstInvalid.closest('.tab-panel');
                            if (panel && typeof window.activateHealthTab === 'function') { window.activateHealthTab(panel.id); }
                            const grp = firstInvalid.closest('.question-group') || firstInvalid.closest('.field') || firstInvalid;
                            if (grp) { grp.classList.add('error'); grp.classList.add('error-flash'); setTimeout(function () { grp.classList.remove('error-flash'); }, 1200); grp.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
                            if (typeof firstInvalid.focus === 'function') firstInvalid.focus({ preventScroll: true });
                            // Inline message like screenshot
                            (function () {
                                const gtxt = (function () { try { const g = firstInvalid.closest('.question-group'); if (g) { const p = g.querySelector('p'); if (p) return (p.textContent || '').trim(); } const lab = firstInvalid.closest('.field')?.querySelector('label'); if (lab) return (lab.textContent || '').trim(); } catch (_) { } return 'Please complete this question.'; })();
                                let box = (firstInvalid.closest('.question-group') || firstInvalid.closest('.field') || firstInvalid).querySelector('.inline-error');
                                if (!box) { box = document.createElement('div'); box.className = 'error-text inline-error'; (firstInvalid.closest('.question-group') || firstInvalid.closest('.field') || firstInvalid).appendChild(box); }
                                box.textContent = 'Please answer: ' + gtxt;
                            })();
                        }
                    } catch (_) { }
                }
            }
        }, true);

        function showIncompleteHealthModal(message) {
            var wrap = document.createElement('div');
            wrap.id = 'hs-incomplete-overlay';
            wrap.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,.5);display:flex;align-items:center;justify-content:center;z-index:1100;';
            wrap.innerHTML = `
            <div style="background:#fff;max-width:460px;width:92%;border-radius:14px;overflow:hidden;box-shadow:0 12px 30px rgba(0,0,0,.25);">
                <div style="background:linear-gradient(135deg,#ff69b4,#ff89c9);color:#fff;padding:14px 18px;font-weight:700;">Health Screening Incomplete</div>
                <div style="padding:18px;">
                    <p style="margin:0 0 14px 0;color:#374151;font-size:15px;">${message}</p>
                    <p style="margin:0;color:#6b7280;font-size:13px;">Tip: Fields highlighted in red are required.</p>
                </div>
                <div style="padding:12px 16px;border-top:1px solid #eee;display:flex;justify-content:flex-end;gap:8px;">
                    <button id="hs-modal-ok" style="background:#111827;color:#fff;border:none;padding:10px 16px;border-radius:10px;cursor:pointer;font-weight:700;">OK</button>
                </div>
            </div>`;
            document.body.appendChild(wrap);
            var ok = document.getElementById('hs-modal-ok');
            if (ok) ok.onclick = function () { wrap.remove(); };
        }
    </script>
    <script>
        // Responsive tables for user pages (stack vertically on mobile)
        (function () {
            if (window.__userTableStackInit) return; window.__userTableStackInit = true;
            function makeTablesStackable(root) {
                try {
                    var scope = root && root.querySelectorAll ? root : document;
                    scope.querySelectorAll('table').forEach(function (table) {
                        var thead = table.querySelector('thead');
                        if (!thead) return;
                        var headers = Array.from(thead.querySelectorAll('th')).map(function (th) { return (th.textContent || '').trim(); });
                        if (!headers.length) return;
                        table.querySelectorAll('tbody tr').forEach(function (tr) {
                            Array.from(tr.children).forEach(function (td, idx) {
                                if (td && td.nodeName === 'TD' && !td.hasAttribute('data-label')) {
                                    td.setAttribute('data-label', headers[idx] || '');
                                }
                            });
                        });
                    });
                } catch (e) { }
            }
            window.makeTablesStackable = makeTablesStackable;
            document.addEventListener('DOMContentLoaded', function () { makeTablesStackable(document); });
            try {
                var mo = new MutationObserver(function (mutations) {
                    mutations.forEach(function (m) {
                        m.addedNodes && m.addedNodes.forEach(function (n) {
                            if (n && n.nodeType === 1) {
                                if (n.matches && n.matches('table')) { makeTablesStackable(n.parentNode || n); }
                                else if (n.querySelectorAll) { var ts = n.querySelectorAll('table'); ts.forEach(function (t) { makeTablesStackable(t.parentNode || t); }); }
                            }
                        });
                    });
                });
                mo.observe(document.body, { subtree: true, childList: true });
            } catch (e) { }
        })();
    </script>
    <script>
        let walkInCalendar;
        let availableDates = {};

        // Simple modal helpers
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (!modal) return;
            modal.style.display = 'block';
            // prevent body scroll when modal is open
            try {
                document.body.style.overflow = 'hidden';
                document.body.classList.add('modal-open');
            } catch (_) { }
        }
        const NOTIF_STORAGE_KEY = 'user_notifications';
        const LAST_STATUS_KEY = 'hs_last_status';

        function getStoredNotifications() {
            try { return JSON.parse(localStorage.getItem(NOTIF_STORAGE_KEY) || '[]'); } catch (_) { return []; }
        }

        function setStoredNotifications(list) {
            localStorage.setItem(NOTIF_STORAGE_KEY, JSON.stringify(list));
        }

        function unreadCount() {
            return getStoredNotifications().filter(n => !n.is_read).length;
        }

        function updateNotificationBadge() {
            const badge = document.getElementById('user-notification-badge');
            const count = unreadCount();
            if (!badge) return;
            if (count > 0) {
                badge.textContent = String(count);
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
        }

        function addScreeningNotificationIfUpdated(status, adminNotes, createdAt) {
            // Only notify when status moves out of pending
            if (!status || status === 'pending') return;
            const last = localStorage.getItem(LAST_STATUS_KEY);
            if (last === status) return; // already notified for this status
            localStorage.setItem(LAST_STATUS_KEY, status);
            const list = getStoredNotifications();
            const title = 'Health Screening Result';
            const message = `Your health screening has been ${status}. ${adminNotes ? 'Notes: ' + adminNotes : ''}`;
            list.unshift({ id: Date.now(), title, message, created_at: createdAt || new Date().toISOString(), is_read: false });
            setStoredNotifications(list);
            updateNotificationBadge();
        }

        function loadUserNotificationsUI() {
            const container = document.getElementById('user-notifications-list');
            if (!container) return;

            // Fetch server-side notifications
            fetch('/user/notifications')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Combine server notifications with local notifications
                        const serverNotifications = data.data.map(n => ({
                            id: n.id,
                            title: n.title,
                            message: n.message,
                            created_at: n.created_at,
                            is_read: n.is_read
                        }));
                        const localNotifications = getStoredNotifications();

                        // Merge and deduplicate (server notifications take priority)
                        const allNotifications = [...serverNotifications, ...localNotifications];
                        const uniqueNotifications = allNotifications.filter((notification, index, self) =>
                            index === self.findIndex(n => n.id === notification.id)
                        );

                        container.innerHTML = '';
                        if (uniqueNotifications.length === 0) {
                            container.innerHTML = '<div class="notification-item"><div class="notification-message">No notifications.</div></div>';
                            return;
                        }

                        uniqueNotifications.forEach(n => {
                            const div = document.createElement('div');
                            div.className = 'notification-item';
                            div.innerHTML = `
                                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                    <div style="flex: 1;">
                                        <div class="notification-title">${n.title}</div>
                                        <div class="notification-message">${n.message}</div>
                                        <div class="notification-time">${new Date(n.created_at).toLocaleString()}</div>
                                    </div>
                                    <button onclick="deleteSingleNotification(${n.id})" style="background: #dc3545; color: white; border: none; padding: 4px 8px; border-radius: 4px; cursor: pointer; font-size: 12px; margin-left: 10px;">Delete</button>
                                </div>
                            `;
                            container.appendChild(div);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error loading notifications:', error);
                    // Fallback to local notifications
                    const list = getStoredNotifications();
                    container.innerHTML = '';
                    if (list.length === 0) {
                        container.innerHTML = '<div class="notification-item"><div class="notification-message">No notifications.</div></div>';
                        return;
                    }
                    list.forEach(n => {
                        const div = document.createElement('div');
                        div.className = 'notification-item';
                        div.innerHTML = `
                            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                <div style="flex: 1;">
                                    <div class="notification-title">${n.title}</div>
                                    <div class="notification-message">${n.message}</div>
                                    <div class="notification-time">${new Date(n.created_at).toLocaleString()}</div>
                                </div>
                                <button onclick="deleteSingleNotification(${n.id})" style="background: #dc3545; color: white; border: none; padding: 4px 8px; border-radius: 4px; cursor: pointer; font-size: 12px; margin-left: 10px;">Delete</button>
                            </div>
                        `;

                        // (health screening customization moved; see initHealthScreeningEnhancements())
                        container.appendChild(div);
                    });
                });
        }

        function showUserNotifications() {
            loadUserNotificationsUI();
            openModal('user-notification-modal');
        }

        // Close a specific modal by id
        function closeModal(modalId) {
            try {
                const el = document.getElementById(modalId);
                if (el) el.style.display = 'none';
                document.body.style.overflow = 'auto';
                document.body.classList.remove('modal-open');
            } catch (_) { }
        }

        function markAllUserNotificationsRead() {
            // Mark server notifications as read
            fetch('/user/notifications', { method: 'GET' })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Mark each unread notification as read
                        data.data.filter(n => !n.is_read).forEach(notification => {
                            fetch(`/user/notifications/${notification.id}/mark-read`, { method: 'POST' })
                                .catch(error => console.error('Error marking notification as read:', error));
                        });
                    }
                })
                .catch(error => console.error('Error fetching notifications:', error));

            // Mark local notifications as read
            const list = getStoredNotifications().map(n => ({ ...n, is_read: true }));
            setStoredNotifications(list);
            updateNotificationBadge();
            loadUserNotificationsUI();
        }

        // Custom confirmation modal functions
        function showConfirmationModal(title, message, onConfirm) {
            document.getElementById('confirmationTitle').textContent = title;
            document.getElementById('confirmationMessage').textContent = message;
            document.getElementById('confirmationModal').style.display = 'block';
            try { document.body.classList.add('modal-open'); document.body.style.overflow = 'hidden'; } catch (_) { }

            // Remove any existing event listeners
            const confirmBtn = document.getElementById('confirmationConfirmBtn');
            const newConfirmBtn = confirmBtn.cloneNode(true);
            confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);

            // Add new event listener
            newConfirmBtn.addEventListener('click', function () {
                closeConfirmationModal();
                onConfirm();
            });
        }

        function closeConfirmationModal() {
            document.getElementById('confirmationModal').style.display = 'none';
            try { document.body.classList.remove('modal-open'); document.body.style.overflow = 'auto'; } catch (_) { }
        }

        // Close modal when clicking outside
        window.addEventListener('click', function (event) {
            const cm = document.getElementById('confirmationModal');
            if (event.target === cm) {
                closeConfirmationModal();
                return;
            }
            // Close calendar-style modals if clicking on backdrop
            document.querySelectorAll('.modal-calendar').forEach((m) => {
                if (event.target === m) {
                    m.style.display = 'none';
                    document.body.style.overflow = 'auto';
                    document.body.classList.remove('modal-open');
                }
            });
        });

        function deleteAllUserNotifications() {
            // Show custom confirmation modal
            showConfirmationModal(
                'Delete All Notifications',
                'Are you sure you want to delete all notifications? This action cannot be undone.',
                function () {
                    performDeleteAllNotifications();
                }
            );
        }

        function performDeleteAllNotifications() {

            // Delete server notifications
            fetch('/user/notifications', { method: 'GET' })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Delete each notification
                        const deletePromises = data.data.map(notification =>
                            fetch(`/user/notifications/${notification.id}/delete`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                }
                            })
                        );

                        Promise.all(deletePromises)
                            .then(() => {
                                // Clear local notifications
                                setStoredNotifications([]);
                                updateNotificationBadge();
                                loadUserNotificationsUI();

                                // Show success message
                                showInlineNotification('success', '', 'All notifications have been deleted successfully!', 3000);
                            })
                            .catch(error => {
                                console.error('Error deleting notifications:', error);
                                showInlineNotification('error', '', 'Some notifications could not be deleted. Please try again.', 5000);
                            });
                    }
                })
                .catch(error => {
                    console.error('Error fetching notifications:', error);
                    showInlineNotification('error', '', 'Unable to fetch notifications. Please try again.', 5000);
                });
        }

        function deleteSingleNotification(notificationId) {
            // Show custom confirmation modal
            showConfirmationModal(
                'Delete Notification',
                'Are you sure you want to delete this notification?',
                function () {
                    performDeleteSingleNotification(notificationId);
                }
            );
        }

        function performDeleteSingleNotification(notificationId) {

            // Delete server notification
            fetch(`/user/notifications/${notificationId}/delete`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove from local notifications as well
                        const list = getStoredNotifications().filter(n => n.id !== notificationId);
                        setStoredNotifications(list);
                        updateNotificationBadge();
                        loadUserNotificationsUI();

                        // Show success message
                        showInlineNotification('success', '', 'Notification deleted successfully!', 3000);
                    } else {
                        showInlineNotification('error', '', 'Failed to delete notification. Please try again.', 5000);
                    }
                })
                .catch(error => {
                    console.error('Error deleting notification:', error);
                    showInlineNotification('error', '', 'Unable to delete notification. Please try again.', 5000);
                });
        }

        // Load available time slots for donation
        function loadAvailableSlots(dateStr, formType) {
            const timeSlotsContainer = document.querySelector('#' + formType + 'Modal .time-slot-buttons-container');
            const timeSlotsGrid = document.querySelector('#' + formType + 'Modal .time-slots-grid');
            const selectedDateInfo = document.querySelector('#' + formType + 'Modal .selected-date-info');
            const selectedDateDisplay = document.getElementById('selectedDateDisplay');
            const dateInput = document.getElementById(formType + 'DateModal');

            dateInput.value = dateStr;

            // Show selected date info
            if (selectedDateInfo && selectedDateDisplay) {
                selectedDateDisplay.textContent = new Date(dateStr).toLocaleDateString('en-US', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                selectedDateInfo.style.display = 'block';
            }

            // Show loading state
            if (timeSlotsGrid) {
                timeSlotsGrid.innerHTML = '<p style="text-align: center; color: #666;">Loading available time slots...</p>';
            }

            fetch(`/donation/available-slots?date=${dateStr}`)
                .then(response => response.json())
                .then(data => {
                    if (timeSlotsGrid) {
                        timeSlotsGrid.innerHTML = '';

                        if (data.success && data.slots.length > 0) {
                            const buttonGroup = document.createElement('div');
                            buttonGroup.className = 'time-slot-buttons';
                            buttonGroup.style.cssText = 'display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 10px; margin-top: 10px;';

                            data.slots.forEach(slot => {
                                const btn = document.createElement('button');
                                btn.type = 'button';
                                btn.className = 'time-slot-btn';

                                // Format time display
                                const timeDisplay = new Date(`1970-01-01T${slot}`).toLocaleTimeString('en-US', {
                                    hour: 'numeric',
                                    minute: '2-digit',
                                    hour12: true
                                });

                                btn.innerHTML = `
                                    <div style="font-weight: bold;">${timeDisplay}</div>
                                    <div style="font-size: 12px; color: #666;">1 hour slot</div>
                                `;
                                btn.dataset.slot = slot;

                                btn.onclick = () => {
                                    const timeInput = document.getElementById(formType + 'TimeModal');
                                    timeInput.value = slot;
                                    document.querySelectorAll('#' + formType + 'Modal .time-slot-btn').forEach(b => b.classList.remove('selected'));
                                    btn.classList.add('selected');
                                };

                                buttonGroup.appendChild(btn);
                            });

                            timeSlotsGrid.appendChild(buttonGroup);
                        } else {
                            timeSlotsGrid.innerHTML = `
                                <div style="text-align: center; padding: 20px; color: #666;">
                                    <p>No available time slots for this date.</p>
                                    <p style="font-size: 14px;">Please select a different date highlighted in pink.</p>
                                </div>
                            `;
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading slots:', error);
                    if (timeSlotsGrid) {
                        timeSlotsGrid.innerHTML = `
                            <div style="text-align: center; padding: 20px; color: #dc3545;">
                                <p>Error loading time slots.</p>
                                <p style="font-size: 14px;">Please try again or select a different date.</p>
                            </div>
                        `;
                    }
                });
        }

        function pollHealthScreeningStatus() {
            fetch('/health-screening/check-existing')
                .then(r => r.json())
                .then(data => {
                    if (data && data.hasExisting) {
                        addScreeningNotificationIfUpdated(data.status, data.admin_notes, data.updated_at || data.created_at);
                    }
                })
                .catch(() => { });
        }

        // Show server messages (success/error)
        function showServerMessages() {
            // Check for success message
            const urlParams = new URLSearchParams(window.location.search);
            const success = urlParams.get('success');
            const error = urlParams.get('error');

            if (success) {
                showInlineNotification('success', '', decodeURIComponent(success), 5000);
                // Clean up URL
                const newUrl = window.location.pathname;
                window.history.replaceState({}, document.title, newUrl);
            }

            if (error) {
                showInlineNotification('error', '', decodeURIComponent(error), 5000);
                // Clean up URL
                const newUrl = window.location.pathname;
                window.history.replaceState({}, document.title, newUrl);
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            updateNotificationBadge();
            pollHealthScreeningStatus();
            setInterval(pollHealthScreeningStatus, 8000); // ~8s for near real-time
            showServerMessages(); // Show any server messages
            setupFormSubmission(); // Setup form submission handlers
            // Ensure Facebook widget is shown on initial dashboard load
            toggleFacebookWidget(true);
            // Prefill donor details for the screening form date etc when page loads
            const setVal = (id, val) => { const el = document.getElementById(id); if (el) el.value = val ?? '—'; };
            const today = new Date();
            setVal('form_date', today.toLocaleDateString());

            // Load user profile information
            fetch('/profile/current')
                .then(r => r.json())
                .then(info => {
                    if (info && info.success) {
                        setVal('donor_full_name', info.user.full_name);
                        setVal('donor_dob', new Date(info.user.date_of_birth).toLocaleDateString());
                        setVal('donor_age', info.user.age);
                        setVal('donor_sex', info.user.sex);
                        setVal('donor_address', info.user.address);
                        setVal('donor_contact', info.user.contact_number);
                    }
                })
                .catch(() => { });

            // Load infant information
            fetch('/infant-information/current')
                .then(r => r.json())
                .then(data => {
                    if (data && data.success && data.hasInfant) {
                        setVal('infant_full_name', data.infant.full_name);
                        setVal('infant_sex', data.infant.sex);
                        setVal('infant_dob', new Date(data.infant.date_of_birth).toLocaleDateString());
                        (function () {
                            const total = parseInt(data.infant.age, 10);
                            let text = '';
                            if (!isNaN(total)) {
                                const years = Math.floor(total / 12);
                                const months = total % 12;
                                if (years === 0) {
                                    text = months === 1 ? '1 month' : months + ' months';
                                } else if (months === 0) {
                                    text = years === 1 ? '1 year' : years + ' years';
                                } else {
                                    text = (years === 1 ? '1 year' : years + ' years') + ' ' + (months === 1 ? '1 month' : months + ' months');
                                }
                            }
                            setVal('infant_age', text || (data.infant.age + ' months'));
                        })();
                        setVal('infant_birthweight', (data.infant.birthweight ?? '') + (data.infant.birthweight != null ? ' kg' : ''));
                    }
                })
                .catch(() => { });

            // Inline server success message is now handled via showInlineNotification() above.
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

        // Inline Notification System - Matching Login Success Style
        function showInlineNotification(type, title, message, duration = 5000) {
            const container = document.getElementById('inline-notification-container');
            if (!container) return;

            const notification = document.createElement('div');
            notification.className = `inline-notification ${type}`;

            // Simple structure like login success message
            notification.innerHTML = `
                <button class="inline-notification-close" onclick="hideInlineNotification(this)">&times;</button>
                <div class="inline-notification-message">${message}</div>
            `;

            container.appendChild(notification);

            // Trigger animation
            setTimeout(() => {
                notification.classList.add('show');
            }, 100);

            // Auto-hide after duration
            if (duration > 0) {
                setTimeout(() => {
                    hideInlineNotification(notification.querySelector('.inline-notification-close'));
                }, duration);
            }

            return notification;
        }

        function hideInlineNotification(closeButton) {
            const notification = closeButton.closest('.inline-notification');
            if (!notification) return;

            notification.classList.remove('show');
            notification.classList.add('hide');

            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 400); // Wait for animation to complete
        }

        // Enhanced success modal that shows inline notification
        function showSuccessModalWithInline(message, onClose) {
            showInlineNotification('success', '', message, 5000);
            if (onClose) {
                setTimeout(onClose, 1000); // Small delay to let user see the notification
            }
        }

        // Function to show/hide Facebook widget based on current section
        function toggleFacebookWidget(show) {
            const fbWidget = document.querySelector('.floating-fb-follow');
            if (fbWidget) {
                fbWidget.style.display = show ? 'flex' : 'none';
            }
        }




        // Navigation functions
        function showDashboard() {
            document.getElementById('pageTitle').textContent = 'DASHBOARD';
            // Show Facebook widget only on main dashboard
            toggleFacebookWidget(true);
            // If URL still shows settings, normalize it back to /dashboard without adding a new history entry
            if (window.location.pathname === '/dashboard/settings') {
                history.replaceState({ view: 'dashboard' }, '', '/dashboard');
            }
            document.getElementById('dashboard-view').innerHTML = `
                <div class="content-box">
                    <h1 class="content-title">Welcome, {{ session('user_name', 'User') }}!</h1>
                    <h2 class="content-title">Step-by-step guide on how to donate breastmilk:</h2>
                    <div class=\"step-guide compact-steps\">
                        <div class=\"step-item\">
                            <div class=\"step-number\">1</div>
                            <div class=\"step-title\">Undergo Health Screening</div>
                            <div class=\"step-description\">The milk bank or hospital will inquire about your medical history, lifestyle, medications, and travel history. They may also require blood tests to ensure you are healthy and free from diseases like HIV or hepatitis.</div>
                        </div>
                        <div class=\"step-item\">
                            <div class=\"step-number\">2</div>
                            <div class=\"step-title\">Get Approved as a Donor</div>
                            <div class=\"step-description\">Once your health screening is successful, you will be approved as a donor and can begin the donation process.</div>
                        </div>
                        <div class=\"step-item\">
                            <div class=\"step-number\">3</div>
                            <div class=\"step-title\">Follow Safe Milk Expression and Storage</div>
                            <div class=\"step-description\">Use clean hands and sterilized equipment when expressing milk. Store the expressed milk in clean, labeled containers with the date and time.</div>
                        </div>
                        <div class=\"step-item\">
                            <div class=\"step-number\">4</div>
                            <div class=\"step-title\">Freeze and Store the Milk Properly</div>
                            <div class=\"step-description\">Freeze the milk immediately after pumping and store it in a deep freezer until collection, as required by some milk banks.</div>
                        </div>
                        <div class=\"step-item\">
                            <div class=\"step-number\">5</div>
                            <div class=\"step-title\">Coordinate Milk Pickup or Drop-off</div>
                            <div class=\"step-description\">Some milk banks offer pickup services, while others may require you to drop off the milk at a hospital or milk bank location.</div>
                        </div>
                        <div class=\"step-item\">
                            <div class=\"step-number\">6</div>
                            <div class=\"step-title\">Repeat as Needed</div>
                            <div class=\"step-description\">Continue donating as long as you are approved and producing excess milk. Regular donations help maintain a steady supply for babies in need.</div>
                        </div>
                    </div>
                </div>
            `;
            updateActiveNav('dashboard');

        }



        function showHealthScreening() {
            console.log('showHealthScreening function called');
            // Hide Facebook widget when in health screening
            toggleFacebookWidget(false);
            if (window.location.pathname === '/dashboard/settings') {
                history.replaceState({ view: 'health' }, '', '/dashboard');
            }

            // Check if user already has a health screening
            fetch('/health-screening/check-existing')
                .then(response => response.json())
                .then(data => {
                    if (data.hasExisting) {
                        document.getElementById('pageTitle').textContent = 'HEALTH SCREENING';
                        document.getElementById('dashboard-view').innerHTML = `
                            <div class="card">
                                <div class="card-header"><i class="fas fa-clipboard-list"></i> Health Screening Status</div>
                                <div class="card-body">
                                    <details class="section" open>
                                        <summary class="section-title">Status Overview <span class="chevron">▾</span></summary>
                                        <div class="section-content">
                                            <div class="section-grid">
                                                <div>
                                                    <div class="field"><label>Status</label>
                                                        <div><span class="pill ${data.status}">${data.status.toUpperCase()}</span></div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="field"><label>Submitted</label>
                                                        <div>${new Date(data.created_at_iso || data.created_at).toLocaleString()}</div>
                                                    </div>
                                                </div>
                                                ${data.status !== 'pending' ? `
                                                <div>
                                                    <div class="field"><label>${data.status === 'accepted' ? 'Date Accepted' : 'Date Declined'}</label>
                                                        <div>${new Date((data.updated_at_iso || data.updated_at || data.created_at_iso || data.created_at)).toLocaleString()}</div>
                                                    </div>
                                                </div>` : ''}
                                            </div>
                                            ${data.admin_notes ? `<div class="field"><label>Admin Notes</label><div class="helper">${data.admin_notes}</div></div>` : ''}
                                            <div class="helper">Only one health screening is allowed per user.</div>
                                        </div>
                                    </details>
                                </div>
                            </div>
                        `;
                    } else {
                        // Show the health screening form
                        document.getElementById('pageTitle').textContent = 'HEALTH SCREENING';
                        console.log('Page title updated');
                        document.getElementById('dashboard-view').innerHTML = `
                            <div class=\"card\">
                                <div class=\"card-header\"><i class=\"fas fa-heart\"></i> Health Screening Form</div>
                                <div class=\"card-body\">
                                    <p class=\"subtext\">Please complete this health screening form to be eligible for breastmilk donation.</p>
                                    <style>
                                        /* Tabs styling scoped to health screening card */
                                        .card-body .tabs { 
                                            position: sticky; top: 0; z-index: 2;
                                            display: flex; gap: 8px; flex-wrap: nowrap; 
                                            border-bottom: 1px solid #f4c6d6; 
                                            margin: 8px 0 12px; 
                                            background: #fff; 
                                            overflow-x: auto; -webkit-overflow-scrolling: touch; 
                                            padding-bottom: 2px; 
                                        }
                                        .card-body .tabs::-webkit-scrollbar { height: 8px; }
                                        .card-body .tabs::-webkit-scrollbar-thumb { background: #f4c6d6; border-radius: 999px; }
                                        .card-body .tab { appearance: none; border: 1px solid #f4c6d6; border-bottom: none; background: #fff6fa; color: #000; padding: 8px 12px; border-top-left-radius: 10px; border-top-right-radius: 10px; font-weight: 600; cursor: pointer; white-space: nowrap; position: relative; }
                                        .card-body .tab[aria-selected="true"], .card-body .tab.active { background: #ffd9e7; border-color: #f4c6d6; }
                                        .card-body .tab:hover { filter: brightness(0.98); }
                                        .card-body .tab:focus-visible { outline: 2px solid #ff69b4; outline-offset: 2px; }
                                        .card-body .tab.complete::after { content: '✓'; position: absolute; top: -6px; right: -6px; width: 18px; height: 18px; background: #28a745; color: #fff; border-radius: 50%; font-size: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(0,0,0,0.15); }
                                        .card-body .tabs .tab-underline { position: absolute; left: 0; bottom: 0; height: 2px; background: #ff69b4; transition: transform .25s ease, width .25s ease; border-radius: 2px; }
                                        .card-body .tab-panel { display: none; padding-top: 6px; }
                                        .card-body .tab-panel.active { display: block; }
                                        /* per-panel Prev/Next removed as requested */

                                        /* Question containers styling */
                                        .card-body .question-group {
                                            background: #ffffff;
                                            border: 1px solid #f0d3df;
                                            border-radius: 12px;
                                            padding: 14px 16px;
                                            margin: 10px 0;
                                            box-shadow: 0 4px 10px rgba(0,0,0,0.04);
                                            display: flex;
                                            flex-direction: column;
                                            gap: 10px;
                                        }
                                        .card-body .question-group:hover {
                                            border-color: #ffb6ce;
                                            box-shadow: 0 8px 18px rgba(255,105,180,0.12);
                                        }
                                        .card-body .question-group p {
                                            margin: 0 0 6px 0;
                                            color: #111827;
                                            font-weight: 700;
                                            font-size: 15px;
                                        }
                                        .card-body .radio-group,
                                        .card-body .checkbox-group {
                                            display: flex;
                                            flex-wrap: wrap;
                                            gap: 10px;
                                        }
                                        .card-body .radio-group label,
                                        .card-body .checkbox-group label {
                                            display: inline-flex;
                                            align-items: center;
                                            gap: 8px;
                                            padding: 8px 12px;
                                            border: 1px solid #e5e7eb;
                                            border-radius: 999px;
                                            cursor: pointer;
                                            background: #fff;
                                            color: #111827;
                                            transition: border-color .2s, background-color .2s, box-shadow .2s;
                                        }
                                        .card-body .radio-group label:hover,
                                        .card-body .checkbox-group label:hover { border-color: #ff69b4; background: #fff6fa; }
                                        .card-body input[type="radio"],
                                        .card-body input[type="checkbox"] { accent-color: #ff69b4; }

                                        /* Field styling inside questions */
                                        .card-body .field { display: flex; flex-direction: column; gap: 6px; }
                                        .card-body .field label { font-weight: 600; color: #374151; font-size: 14px; }
                                        .card-body .field input,
                                        .card-body .field select,
                                        .card-body .field textarea {
                                            width: 100%;
                                            padding: 10px 12px;
                                            border: 1px solid #e5e7eb;
                                            border-radius: 10px;
                                            font-size: 14px;
                                            background: #fff;
                                            transition: all .2s ease;
                                        }
                                        .card-body .field input:focus,
                                        .card-body .field select:focus,
                                        .card-body .field textarea:focus {
                                            outline: none;
                                            border-color: #ff69b4;
                                            box-shadow: 0 0 0 4px rgba(255,105,180,0.15);
                                            background: #fff;
                                        }
                                        .card-body .helper { color: #6b7280; font-size: 12px; }
                                        .card-body .invalid-field,
                                        .card-body [aria-invalid="true"] {
                                            border-color: #dc3545 !important;
                                            box-shadow: 0 0 0 3px rgba(220,53,69,0.1);
                                        }

                                        /* Small icon on each question header */
                                        .card-body .question-group > p {
                                            display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
                                            font-size:15px; /* improved readability */
                                            line-height:1.35;
                                            font-weight:600;
                                            color:#1f2937; /* slate-800 */
                                            margin:0 0 6px;
                                            -webkit-font-smoothing:antialiased;
                                            text-rendering:optimizeLegibility;
                                            font-feature-settings:"kern","liga";
                                        }
                                        /* Prevent <br> from interfering with flex layout */
                                        .card-body .question-group > p br { display: none; }
                                        .card-body .question-group > p strong {
                                            font-weight:600; /* keep consistent weight to avoid jump */
                                        }
                                        .card-body .question-group > p::before {
                                            content: "\f059"; /* fa-solid circle-question */
                                            font-family: "Font Awesome 6 Free";
                                            font-weight: 900;
                                            font-size: 14px; color: #ff69b4;
                                            width: 16px; display: inline-block; text-align: center;
                                        }
                                        /* Translation line (Bisaya) */
                                        .card-body .question-group .q-trans {
                                            display:block; flex: 1 0 100%;
                                            font-weight:500;
                                            font-size:12px;
                                            line-height:1.35;
                                            color:#000; /* set to black per request */
                                            margin-top:2px; margin-left:24px; /* indent to line up under text (icon 16px + 8px gap) */
                                            font-style:italic; /* retained unless removal requested */
                                        }

                                        /* Subsection headers and dividers */
                                        .card-body .subsection-header {
                                            display: flex; align-items: center; gap: 8px;
                                            margin: 14px 0 6px; padding-top: 8px;
                                            font-weight: 800; font-size: 12px; color: #6b7280;
                                            text-transform: uppercase; letter-spacing: .04em;
                                            border-top: 1px dashed #f0d3df;
                                        }
                                        .card-body .subsection-header .q-icon { color: #d63384; font-size: 14px; }
                                    </style>
                                    <form id=\"healthScreeningForm\" method=\"POST\" action=\"{{ route('health-screening.submit') }}\">
                                        @csrf
                                        <div class=\"tabs\" role=\"tablist\" aria-label=\"Health Screening Sections\">
                                            <button type=\"button\" class=\"tab active\" role=\"tab\" aria-selected=\"true\" aria-controls=\"tab-personal\" id=\"tabbtn-personal\">Personal Information</button>
                                            <button type=\"button\" class=\"tab\" role=\"tab\" aria-selected=\"false\" aria-controls=\"tab-infant\" id=\"tabbtn-infant\">Infant Information</button>
                                            <button type=\"button\" class=\"tab\" role=\"tab\" aria-selected=\"false\" aria-controls=\"tab-medical\" id=\"tabbtn-medical\">Medical History</button>
                                            <button type=\"button\" class=\"tab\" role=\"tab\" aria-selected=\"false\" aria-controls=\"tab-sexual\" id=\"tabbtn-sexual\">Sexual History</button>
                                            <button type=\"button\" class=\"tab\" role=\"tab\" aria-selected=\"false\" aria-controls=\"tab-donorinfant\" id=\"tabbtn-donorinfant\">Donor's Infant</button>
                                            <div class=\"tab-underline\" aria-hidden=\"true\"></div>
                                        </div>

                                        <div id=\"tab-personal\" class=\"tab-panel active\" role=\"tabpanel\" aria-labelledby=\"tabbtn-personal\">
                                            <div class=\"section-grid\">
                                                    <div class="field">
                                                        <label>Civil Status</label>
                                                        <select name="civil_status" required>
                                                            <option value="">Select Civil Status</option>
                                                            <option value="Single">Single</option>
                                                            <option value="Married">Married</option>
                                                            <option value="Divorced">Divorced</option>
                                                            <option value="Widowed">Widowed</option>
                                                        </select>
                                                    </div>
                                                    <div class="field">
                                                        <label>Occupation</label>
                                                        <input type="text" name="occupation" required maxlength="100" pattern="[A-Za-z\s]+" title="Letters and spaces only" oninput="(function(el){var v=el.value;var f=v.replace(/[^A-Za-z\s]/g,'');var warn=el.nextElementSibling; if(v!==f){el.value=f; if(warn){warn.style.display='block'; clearTimeout(el._occTo); el._occTo=setTimeout(function(){warn.style.display='none';},2000);} } else { if(warn){warn.style.display='none';}}})(this)">
                                                        <div style="display:none;font-size:12px;color:#dc3545;margin-top:4px;">Only letters and spaces are allowed.</div>
                                                    </div>
                                                    <div class="field">
                                                        <label>Type of Donor</label>
                                                        <select name="type_of_donor" required>
                                                            <option value="">Select Type</option>
                                                            <option value="community">Community</option>
                                                            <option value="private">Private</option>
                                                            <option value="employee">Employee</option>
                                                            <option value="network_office_agency">Network Office/Agency</option>
                                                        </select>
                                                    </div>
                                                    <div class="field">
                                                        <label>Full Name</label>
                                                        <input id="donor_full_name" type="text" readonly placeholder="Loading...">
                                                    </div>
                                                    <div class="field">
                                                        <label>Date of Birth</label>
                                                        <input id="donor_dob" type="text" readonly placeholder="Loading...">
                                                    </div>
                                                    <div class="field">
                                                        <label>Age</label>
                                                        <input id="donor_age" type="text" readonly placeholder="Loading...">
                                                    </div>
                                                    <div class="field">
                                                        <label>Sex</label>
                                                        <input id="donor_sex" type="text" readonly placeholder="Loading...">
                                                    </div>
                                                    <div class="field" style="grid-column: 1 / -1;">
                                                        <label>Home Address</label>
                                                        <input id="donor_address" type="text" readonly placeholder="Loading...">
                                                    </div>
                                                    <div class="field">
                                                        <label>Cell Phone Number</label>
                                                        <input id="donor_contact" type="text" readonly placeholder="Loading...">
                                                    </div>
                                                    <div class="field">
                                                        <label>Form Date</label>
                                                        <input id="form_date" type="text" readonly>
                                                    </div>
                                                </div>
                                            </div>

                                        <div id=\"tab-infant\" class=\"tab-panel\" role=\"tabpanel\" aria-labelledby=\"tabbtn-infant\">
                                            <div class=\"section-grid\">
                                                    <div class="field">
                                                        <label>Infant Name</label>
                                                        <input id="infant_full_name" type="text" readonly placeholder="Loading...">
                                                        <div class="helper">Auto-filled from your registration</div>
                                                    </div>
                                                    <div class="field">
                                                        <label>Sex</label>
                                                        <input id="infant_sex" type="text" readonly placeholder="Loading...">
                                                    </div>
                                                    <div class="field">
                                                        <label>Date of Birth</label>
                                                        <input id="infant_dob" type="text" readonly placeholder="Loading...">
                                                    </div>
                                                    <div class="field">
                                                        <label>Age</label>
                                                        <input id="infant_age" type="text" readonly placeholder="Loading...">
                                                    </div>
                                                    <div class="field">
                                                        <label>Birthweight</label>
                                                        <input id="infant_birthweight" type="text" readonly placeholder="Loading...">
                                                    </div>
                                                </div>
                                            </div>

                                        <div id=\"tab-medical\" class=\"tab-panel\" role=\"tabpanel\" aria-labelledby=\"tabbtn-medical\">
                                            <div class=\"subsection-header\"><i class=\"fa-solid fa-stethoscope q-icon\"></i> General medical background</div>
                                            <div class=\"question-group\">
                                                    <p><strong>1. Have you donated breastmilk before?</strong><br><span class=\"q-trans\">Nakahatag ka na ba ug gatas sa inahan kaniadto?</span></p>
                                                    <div class="radio-group">
                                                        <label><input type="radio" name="mhq_1" value="yes" required> Yes</label>
                                                        <label><input type="radio" name="mhq_1" value="no" required> No</label>
                                                    </div>
                                                </div>
                                                <div class="question-group">
                                                    <p><strong>2. Have you for any reason been deferred as a breastmilk donor?</strong><br><span class=\"q-trans\">Aduna ka bay rason nga gidili ka isip naghatag ug gatas sa inahan?</span></p>
                                                    <div class="radio-group">
                                                        <label><input type="radio" name="mhq_2" value="yes" required> Yes</label>
                                                        <label><input type="radio" name="mhq_2" value="no" required> No</label>
                                                    </div>
                                                    <div class="field">
                                                        <label>If yes, for what reason?</label>
                                                        <textarea name="mhq_2_reason" rows="2"></textarea>
                                                    </div>
                                                </div>
                                                <div class="question-group">
                                                    <p><strong>3. Did you have a normal pregnancy and delivery for your most recent pregnancy?</strong><br><span class=\"q-trans\">Aduna ka bay normal nga pagmabdos ug pagpanganak sa imong pinakabag-o nga pagmabdos?</span></p>
                                                    <div class="radio-group">
                                                        <label><input type="radio" name="mhq_3" value="yes" required> Yes</label>
                                                        <label><input type="radio" name="mhq_3" value="no" required> No</label>
                                                    </div>
                                                </div>
                                                <div class="question-group">
                                                    <p><strong>4. Do you have any acute or chronic infection such as but not limited to: tuberculosis, hepatitis, systemic disorders?</strong><br><span class=\"q-trans\">Aduna ka bay grabe o dugay nga impeksyon sama sa: tuberculosis, hepatitis, mga sakit sa lawas?</span></p>
                                                    <div class="radio-group">
                                                        <label><input type="radio" name="mhq_4" value="yes" required> Yes</label>
                                                        <label><input type="radio" name="mhq_4" value="no" required> No</label>
                                                    </div>
                                                    <div class="field">
                                                        <label>If yes, what specific disease(s)?</label>
                                                        <textarea name="mhq_4_reason" rows="2"></textarea>
                                                    </div>
                                                </div>
                                                <div class="question-group">
                                                    <p><strong>5. Have you been diagnosed with a chronic non-infectious illness such as but not limited to: diabetes, hypertension, heart disease?</strong><br><span class=\"q-trans\">Na-diagnose ka na ba ug dugay nga sakit nga dili makatakod sama sa: diabetes, hypertension, sakit sa kasingkasing?</span></p>
                                                    <div class="radio-group">
                                                        <label><input type="radio" name="mhq_5" value="yes" required> Yes</label>
                                                        <label><input type="radio" name="mhq_5" value="no" required> No</label>
                                                    </div>
                                                    <div class="field">
                                                        <label>If yes, what specific disease(s)?</label>
                                                        <textarea name="mhq_5_reason" rows="2"></textarea>
                                                    </div>
                                                </div>
                                                <div class="question-group">
                                                    <p><strong>6. Have you received any blood transfusion or any blood products within the last twelve (12) months?</strong><br><span class=\"q-trans\">Nakadawat ka ba ug dugo o mga produkto sa dugo sulod sa miaging dose ka (12) bulan?</span></p>
                                                    <div class="radio-group">
                                                        <label><input type="radio" name="mhq_6" value="yes" required> Yes</label>
                                                        <label><input type="radio" name="mhq_6" value="no" required> No</label>
                                                    </div>
                                                </div>
                                                <div class="question-group">
                                                    <p><strong>7. Have you received any organ or tissue transplant within the last twelve (12) months?</strong><br><span class=\"q-trans\">Nakadawat ka ba ug organ o tissue transplant sulod sa miaging dose ka (12) bulan?</span></p>
                                                    <div class="radio-group">
                                                        <label><input type="radio" name="mhq_7" value="yes" required> Yes</label>
                                                        <label><input type="radio" name="mhq_7" value="no" required> No</label>
                                                    </div>
                                                </div>
                                                <div class="question-group">
                                                    <p><strong>8. Have you had any intake of any alcohol or hard liquor within the last twenty four (24) hours?</strong><br><span class=\"q-trans\">Nag-inom ka ba ug alkohol o lig-on nga ilimnon sulod sa miaging kawhaan ug upat (24) ka oras?</span></p>
                                                    <div class="radio-group">
                                                        <label><input type="radio" name="mhq_8" value="yes" required> Yes</label>
                                                        <label><input type="radio" name="mhq_8" value="no" required> No</label>
                                                    </div>
                                                    <div class="field">
                                                        <label>If yes, how much (in cc or ml)?</label>
                                                        <input type="text" name="mhq_8_amount">
                                                    </div>
                                                </div>
                                                <div class="subsection-header"><i class="fa-solid fa-capsules q-icon"></i> Medications, vitamins, and substances</div>
                                                <div class="question-group">
                                                    <p><strong>9. Do you use megadose vitamins or pharmacologically active herbal preparations?</strong><br><span class=\"q-trans\">Naggamit ka ba ug daghan kaayo nga bitamina o mga herbal nga tambal?</span></p>
                                                    <div class="radio-group">
                                                        <label><input type="radio" name="mhq_9" value="yes" required> Yes</label>
                                                        <label><input type="radio" name="mhq_9" value="no" required> No</label>
                                                    </div>
                                                </div>
                                                <div class="question-group">
                                                    <p><strong>10. Do you regularly use over-the-counter medications or systemic preparations such as replacement hormones, birth control hormones, anti-hypoglycemics, blood thinners?</strong><br><span class=\"q-trans\">Kanunay ka bang naggamit ug mga tambal nga walay reseta o mga sistemang tambal sama sa replacement hormones, birth control hormones, anti-hypoglycemics, blood thinners?</span></p>
                                                    <div class="radio-group">
                                                        <label><input type="radio" name="mhq_10" value="yes" required> Yes</label>
                                                        <label><input type="radio" name="mhq_10" value="no" required> No</label>
                                                    </div>
                                                    <div class="field">
                                                        <label>If yes, what specific medication(s)?</label>
                                                        <textarea name="mhq_10_reason" rows="2"></textarea>
                                                    </div>
                                                </div>
                                                <div class="question-group">
                                                    <p><strong>11. Are you a total vegetarian/vegan?</strong><br><span class=\"q-trans\">Vegetarian/vegan ka ba nga kompleto?</span></p>
                                                    <div class="radio-group">
                                                        <label><input type="radio" name="mhq_11" value="yes" required> Yes</label>
                                                        <label><input type="radio" name="mhq_11" value="no" required> No</label>
                                                    </div>
                                                    <div class="field">
                                                        <label>If yes, do you supplement your diet with vitamins?</label>
                                                        <div class="radio-group">
                                                            <label><input type="radio" name="mhq_11_supplement" value="yes"> Yes</label>
                                                            <label><input type="radio" name="mhq_11_supplement" value="no"> No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="subsection-header"><i class="fa-solid fa-smoking q-icon"></i> Lifestyle and exposures</div>
                                                <div class="question-group">
                                                    <p><strong>12. Do you use illicit drugs?</strong><br><span class=\"q-trans\">Naggamit ka ba ug mga dili legal nga droga?</span></p>
                                                    <div class="radio-group">
                                                        <label><input type="radio" name="mhq_12" value="yes" required> Yes</label>
                                                        <label><input type="radio" name="mhq_12" value="no" required> No</label>
                                                    </div>
                                                </div>
                                                <div class="question-group">
                                                    <p><strong>13. Do you smoke?</strong><br><span class=\"q-trans\">Nagsigarilyo ka ba?</span></p>
                                                    <div class="radio-group">
                                                        <label><input type="radio" name="mhq_13" value="yes" required> Yes</label>
                                                        <label><input type="radio" name="mhq_13" value="no" required> No</label>
                                                    </div>
                                                    <div class="field">
                                                        <label>If yes, how many sticks or packs per day?</label>
                                                        <input type="text" name="mhq_13_amount">
                                                    </div>
                                                </div>
                                                <div class="question-group">
                                                    <p><strong>14. Are you around people who smoke (passive smoking)?</strong><br><span class=\"q-trans\">Naa ka ba sa palibot sa mga tawo nga nagsigarilyo (passive smoking)?</span></p>
                                                    <div class="radio-group">
                                                        <label><input type="radio" name="mhq_14" value="yes" required> Yes</label>
                                                        <label><input type="radio" name="mhq_14" value="no" required> No</label>
                                                    </div>
                                                </div>
                                                <div class="question-group">
                                                    <p><strong>15. Have you had breast augmentation surgery, using silicone breast implants?</strong><br><span class=\"q-trans\">Nakaoperasyon ka na ba sa dughan gamit ang silicone breast implants?</span></p>
                                                    <div class="radio-group">
                                                        <label><input type="radio" name="mhq_15" value="yes" required> Yes</label>
                                                        <label><input type="radio" name="mhq_15" value="no" required> No</label>
                                                    </div>
                                                </div>
                                        </div>

                                        <div id=\"tab-sexual\" class=\"tab-panel\" role=\"tabpanel\" aria-labelledby=\"tabbtn-sexual\">
                                            <div class=\"subsection-header\"><i class=\"fa-solid fa-ribbon q-icon\"></i> Sexual health history</div>
                                            <div class=\"question-group\">
                                                    <p><strong>1. Have you ever had syphilis, HIV, herpes or any sexually transmitted disease (STD)?</strong><br><span class=\"q-trans\">Nakaangkon ka na ba ug syphilis, HIV, herpes o bisan unsang sakit nga makuha pinaagi sa pakighilawas (STD)?</span></p>
                                                    <div class="radio-group">
                                                        <label><input type="radio" name="shq_1" value="yes" required> Yes</label>
                                                        <label><input type="radio" name="shq_1" value="no" required> No</label>
                                                    </div>
                                                </div>
                                                <div class="question-group">
                                                    <p><strong>2. Do you have multiple sexual partners?</strong><br><span class=\"q-trans\">Aduna ka bay daghang kauban sa pakighilawas?</span></p>
                                                    <div class="radio-group">
                                                        <label><input type="radio" name="shq_2" value="yes" required> Yes</label>
                                                        <label><input type="radio" name="shq_2" value="no" required> No</label>
                                                    </div>
                                                </div>
                                                <div class="question-group">
                                                    <p><strong>3. Have you had a sexual partner who is:</strong><br><span class=\"q-trans\">Aduna ka bay kauban sa pakighilawas nga:</span></p>
                                                    <div class="checkbox-group">
                                                        <label><input type="checkbox" name="shq_3_bisexual"> Bisexual</label>
                                                        <label><input type="checkbox" name="shq_3_promiscuous"> Promiscuous</label>
                                                        <label><input type="checkbox" name="shq_3_std"> Has had an STD, AIDS/HIV</label>
                                                        <label><input type="checkbox" name="shq_3_blood"> Received blood for a long period of time for a bleeding problem</label>
                                                        <label><input type="checkbox" name="shq_3_drugs"> Is an intravenous drug user</label>
                                                    </div>
                                                </div>
                                                <div class="question-group">
                                                    <p><strong>4. Have you had a tattoo applied or had an accidental needlestick injury or contact with someone else's blood?</strong><br><span class=\"q-trans\">Nakapatattoo ka na ba o nakaangkon ug aksidenteng pagkatusok sa injection o nakahikap sa dugo sa uban?</span></p>
                                                    <div class="radio-group">
                                                        <label><input type="radio" name="shq_4" value="yes" required> Yes</label>
                                                        <label><input type="radio" name="shq_4" value="no" required> No</label>
                                                    </div>
                                                </div>
                                        </div>

                                        <div id=\"tab-donorinfant\" class=\"tab-panel\" role=\"tabpanel\" aria-labelledby=\"tabbtn-donorinfant\">
                                            <div class=\"subsection-header\"><i class=\"fa-solid fa-baby q-icon\"></i> Donor’s infant wellness</div>
                                            <div class=\"question-group\">
                                                    <p><strong>1. Is your child healthy?</strong><br><span class=\"q-trans\">Himsog ba ang imong anak?</span></p>
                                                    <div class="radio-group">
                                                        <label><input type="radio" name="diq_1" value="yes" required> Yes</label>
                                                        <label><input type="radio" name="diq_1" value="no" required> No</label>
                                                    </div>
                                                </div>
                                                <div class="question-group">
                                                    <p><strong>2. Was your child delivered full term?</strong><br><span class=\"q-trans\">Natawo ba ang imong anak sa hustong panahon (full term)?</span></p>
                                                    <div class="radio-group">
                                                        <label><input type="radio" name="diq_2" value="yes" required> Yes</label>
                                                        <label><input type="radio" name="diq_2" value="no" required> No</label>
                                                    </div>
                                                </div>
                                                <div class="question-group">
                                                    <p><strong>3. Are you exclusively breastfeeding your child?</strong><br><span class=\"q-trans\">Gatas sa inahan ra ba ang imong gihatag sa imong anak (exclusively breastfeeding)?</span></p>
                                                    <div class="radio-group">
                                                        <label><input type="radio" name="diq_3" value="yes" required> Yes</label>
                                                        <label><input type="radio" name="diq_3" value="no" required> No</label>
                                                    </div>
                                                </div>
                                                <div class="question-group">
                                                    <p><strong>4. Is/was your youngest child jaundiced?</strong><br><span class=\"q-trans\">Nangitag ba o nangitag na ba ang imong pinakagamay nga anak?</span></p>
                                                    <div class="radio-group">
                                                        <label><input type="radio" name="diq_4" value="yes" required> Yes</label>
                                                        <label><input type="radio" name="diq_4" value="no" required> No</label>
                                                    </div>
                                                    <div class="field">
                                                        <label>If yes, at what age and how long did it last?</label>
                                                        <textarea name="diq_4_reason" rows="2"></textarea>
                                                    </div>
                                                </div>
                                                <div class="question-group">
                                                    <p><strong>5. Has your child ever received milk from another mother?</strong><br><span class=\"q-trans\">Nakadawat na ba ang imong anak ug gatas gikan sa laing inahan?</span></p>
                                                    <div class="radio-group">
                                                        <label><input type="radio" name="diq_5" value="yes" required> Yes</label>
                                                        <label><input type="radio" name="diq_5" value="no" required> No</label>
                                                    </div>
                                                    <div class="field">
                                                        <label>If yes, when did this happen?</label>
                                                        <textarea name="diq_5_reason" rows="2"></textarea>
                                                    </div>
                                                </div>
                                        </div>

                                        <div class="actions">
                                            <button type="button" class="btn-secondary-outline" onclick="showDashboard()">Back to Dashboard</button>
                                            <button type="button" id="hs-next-global" class="btn-next" style="display:none;">Next</button>
                                            <button type="button" id="health-next-btn" class="btn-success">Submit Health Screening</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        `;

                        // Wire Next -> save + review
                        // Initialize question text replacements & conditional logic AFTER form is in DOM
                        queueMicrotask(() => { if (typeof initHealthScreeningEnhancements === 'function') initHealthScreeningEnhancements(); });
                        const nextBtn = document.getElementById('health-next-btn');
                        if (nextBtn) {
                            nextBtn.addEventListener('click', async function () {
                                // Directly save and open review modal (no SweetAlert here)
                                const formEl = nextBtn.closest('form');
                                if (!formEl) return;
                                const fd = new FormData(formEl);
                                try {
                                    const res = await fetch('{{ route("health-screening.submit") }}', {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Accept': 'application/json'
                                        },
                                        body: fd
                                    });
                                    const data = await res.json();
                                    if (data.success && data.show_review_modal) {
                                        await openHealthReviewModal();
                                    } else {
                                        // On validation error, try to switch to the first invalid field's tab
                                        const firstInvalid = formEl.querySelector(':invalid');
                                        if (firstInvalid) {
                                            const panel = firstInvalid.closest('.tab-panel');
                                            if (panel && typeof window.activateHealthTab === 'function') {
                                                window.activateHealthTab(panel.id);
                                            }
                                            firstInvalid.classList.add('invalid-field');
                                            firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                            try { firstInvalid.focus({ preventScroll: true }); } catch (e) { }
                                        }
                                        // Inline/custom notification for errors
                                        if (typeof showInlineNotification === 'function') {
                                            showInlineNotification('error', '', data.message || 'Unable to save form. Please check required fields.', 5000);
                                        } else {
                                            alert(data.message || 'Unable to save form. Please check required fields.');
                                        }
                                    }
                                } catch (_) {
                                    if (typeof showInlineNotification === 'function') {
                                        showInlineNotification('error', '', 'Network error while saving. Please try again.', 5000);
                                    } else {
                                        alert('Network error while saving. Please try again.');
                                    }
                                }
                            });
                        }

                        // Initialize tabs behavior
                        (function initHealthTabs() {
                            const tabsContainer = document.querySelector('.card-body .tabs');
                            const underline = document.querySelector('.card-body .tabs .tab-underline');
                            const tabs = Array.from(document.querySelectorAll('.card-body .tabs .tab'));
                            const panels = Array.from(document.querySelectorAll('.card-body .tab-panel'));
                            const form = document.getElementById('healthScreeningForm');
                            const byId = id => document.getElementById(id);
                            const nextGlobal = byId('hs-next-global');

                            function updateUnderline() {
                                const active = tabs.find(b => b.classList.contains('active')) || tabs[0];
                                if (!active || !underline || !tabsContainer) return;
                                const aRect = active.getBoundingClientRect();
                                const cRect = tabsContainer.getBoundingClientRect();
                                const left = aRect.left - cRect.left + tabsContainer.scrollLeft + 8; // 8px padding compensation
                                underline.style.width = Math.max(24, aRect.width - 16) + 'px';
                                underline.style.transform = 'translateX(' + left + 'px)';
                            }

                            function markCompletion() {
                                tabs.forEach(btn => btn.classList.remove('complete'));
                                panels.forEach(panel => {
                                    if (isPanelComplete(panel)) {
                                        const btn = tabs.find(b => b.getAttribute('aria-controls') === panel.id);
                                        if (btn) btn.classList.add('complete');
                                    }
                                });
                            }

                            function isPanelComplete(panel) {
                                if (!panel) return false;
                                // Gather required fields
                                const required = Array.from(panel.querySelectorAll('[required]'));
                                if (required.length === 0) return true; // no required fields

                                // Group radio names within this panel
                                const radioReq = required.filter(el => el.type === 'radio');
                                const radioNames = Array.from(new Set(radioReq.map(r => r.name)));
                                // Validate radios: at least one checked per name within this panel
                                for (const name of radioNames) {
                                    const checked = panel.querySelector('input[type="radio"][name="' + name.replace(/"/g, '\\"') + '"]:checked');
                                    if (!checked) return false;
                                }
                                // Validate other required inputs/selects/textareas
                                const others = required.filter(el => el.type !== 'radio');
                                for (const el of others) {
                                    if (el.tagName === 'SELECT') { if (!el.value) return false; }
                                    else if ((el.type === 'text' || el.type === 'number' || el.type === 'date' || el.tagName === 'TEXTAREA')) { if (!String(el.value || '').trim()) return false; }
                                }
                                return true;
                            }

                            function validatePanel(panel) {
                                // Return first invalid element in panel or null
                                const required = Array.from(panel.querySelectorAll('[required]'));
                                // Check radios by group
                                const radioReq = required.filter(el => el.type === 'radio');
                                const radioNames = Array.from(new Set(radioReq.map(r => r.name)));
                                for (const name of radioNames) {
                                    const any = panel.querySelector('input[type="radio"][name="' + name.replace(/"/g, '\\"') + '"]:checked');
                                    if (!any) {
                                        return panel.querySelector('input[type="radio"][name="' + name.replace(/"/g, '\\"') + '"]');
                                    }
                                }
                                // Others
                                for (const el of required.filter(el => el.type !== 'radio')) {
                                    const val = (el.value || '').trim();
                                    if (!val) return el;
                                }
                                return null;
                            }

                            const activate = (panelId) => {
                                tabs.forEach(btn => {
                                    const isActive = btn.getAttribute('aria-controls') === panelId;
                                    btn.classList.toggle('active', isActive);
                                    btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
                                    btn.tabIndex = isActive ? 0 : -1;
                                });
                                panels.forEach(p => p.classList.toggle('active', p.id === panelId));
                                try { localStorage.setItem('hs_active_tab', panelId); } catch (e) { }
                                try { history.replaceState(null, '', '#' + panelId); } catch (e) { }
                                markCompletion();
                                updateUnderline();

                                // Show/hide Next button depending on active panel
                                if (nextGlobal) {
                                    if (panelId === 'tab-medical') {
                                        nextGlobal.style.display = '';
                                        nextGlobal.textContent = 'Next: Sexual History';
                                        nextGlobal.dataset.target = 'tab-sexual';
                                    } else if (panelId === 'tab-sexual') {
                                        nextGlobal.style.display = '';
                                        nextGlobal.textContent = "Next: Donor's Infant";
                                        nextGlobal.dataset.target = 'tab-donorinfant';
                                    } else {
                                        nextGlobal.style.display = 'none';
                                        nextGlobal.removeAttribute('data-target');
                                    }
                                }
                            };

                            // Expose for external use (e.g., validation handlers)
                            window.activateHealthTab = activate;

                            tabs.forEach(btn => btn.addEventListener('click', () => activate(btn.getAttribute('aria-controls'))));
                            // Keyboard support: Left/Right/Home/End to switch
                            tabs.forEach((btn, idx) => btn.addEventListener('keydown', (e) => {
                                if (e.key === 'ArrowRight' || e.key === 'ArrowLeft' || e.key === 'Home' || e.key === 'End') {
                                    e.preventDefault();
                                    let ni = idx;
                                    if (e.key === 'Home') ni = 0; else if (e.key === 'End') ni = tabs.length - 1; else { const dir = e.key === 'ArrowRight' ? 1 : -1; ni = (idx + dir + tabs.length) % tabs.length; }
                                    tabs[ni].focus();
                                    tabs[ni].click();
                                }
                            }));

                            // Helpers for inline errors
                            function getQuestionTextFor(el) {
                                try {
                                    const grp = el.closest('.question-group');
                                    if (grp) { const p = grp.querySelector('p'); if (p) return (p.textContent || '').trim(); }
                                    const lab = el.closest('.field')?.querySelector('label');
                                    if (lab) return (lab.textContent || '').trim();
                                } catch (_) { }
                                return 'Please complete this question.';
                            }
                            function showFieldInlineError(el, message) {
                                const grp = el.closest('.question-group') || el.closest('.field') || el;
                                if (!grp) return;
                                grp.classList.add('error');
                                grp.classList.add('error-flash');
                                let box = grp.querySelector('.inline-error');
                                if (!box) { box = document.createElement('div'); box.className = 'error-text inline-error'; grp.appendChild(box); }
                                box.textContent = message;
                                setTimeout(() => grp.classList.remove('error-flash'), 1200);
                            }

                            // Global Next button behavior with validation
                            if (nextGlobal) {
                                nextGlobal.addEventListener('click', function () {
                                    const target = nextGlobal.dataset.target;
                                    // Determine current active panel
                                    const activePanel = panels.find(p => p.classList.contains('active'));
                                    if (activePanel) {
                                        const invalid = validatePanel(activePanel);
                                        if (invalid) {
                                            invalid.classList.add('invalid-field');
                                            const grp = invalid.closest('.question-group') || invalid.closest('.field') || invalid;
                                            if (grp) { grp.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
                                            try {
                                                if (invalid.type === 'radio') {
                                                    const name = invalid.name;
                                                    const r0 = activePanel.querySelector('input[type="radio"][name="' + name.replace(/"/g, '\\"') + '"]');
                                                    (r0 || invalid).focus({ preventScroll: true });
                                                } else {
                                                    invalid.focus({ preventScroll: true });
                                                }
                                            } catch (e) { }
                                            showFieldInlineError(invalid, 'Please answer: ' + getQuestionTextFor(invalid));
                                            return;
                                        }
                                    }
                                    if (target) {
                                        activate(target);
                                        // After switching, direct to the first question in the new panel
                                        setTimeout(function () {
                                            const newPanel = byId(target);
                                            if (!newPanel) return;
                                            const q1 = newPanel.querySelector('.question-group') || newPanel.querySelector('.field');
                                            if (q1) {
                                                q1.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                                const firstInput = q1.querySelector('input, select, textarea');
                                                try { if (firstInput) firstInput.focus({ preventScroll: true }); } catch (_) { }
                                            }
                                        }, 0);
                                    }
                                });
                            }

                            function handleInputChange(e) {
                                const t = e.target;
                                if (!t || !form) return;
                                // Clear invalid highlight when user changes value
                                if (t.classList) t.classList.remove('invalid-field');
                                const grp = t.closest('.question-group') || t.closest('.field');
                                if (grp) { grp.classList.remove('error'); const box = grp.querySelector('.inline-error'); if (box) box.remove(); }
                                markCompletion();
                                updateUnderline();
                            }

                            if (form) form.addEventListener('input', handleInputChange, true);
                            if (form) form.addEventListener('change', handleInputChange, true);

                            // Always start on Personal Information tab (ignore URL hash/localStorage for initial load)
                            try { localStorage.removeItem('hs_active_tab'); } catch (e) { }
                            activate('tab-personal');

                            // Update underline on container scroll/resize
                            tabsContainer && tabsContainer.addEventListener('scroll', updateUnderline);
                            window.addEventListener('resize', updateUnderline);
                            // Ensure underline is positioned on first paint
                            setTimeout(updateUnderline, 0);
                        })();

                        // Fetch infant info and populate the auto-filled fields
                        fetch('/infant-information/current')
                            .then(r => r.json())
                            .then(info => {
                                const setVal = (id, val) => { const el = document.getElementById(id); if (el) el.value = val ?? '—'; };
                                if (info && info.success && info.hasInfant) {
                                    setVal('infant_full_name', info.infant.full_name);
                                    setVal('infant_sex', info.infant.sex);
                                    setVal('infant_dob', new Date(info.infant.date_of_birth).toLocaleDateString());
                                    // Format age in months/years
                                    (function () {
                                        const total = parseInt(info.infant.age, 10);
                                        let text = '';
                                        if (!isNaN(total)) {
                                            const years = Math.floor(total / 12);
                                            const months = total % 12;
                                            if (years === 0) {
                                                text = months === 1 ? '1 month' : months + ' months';
                                            } else if (months === 0) {
                                                text = years === 1 ? '1 year' : years + ' years';
                                            } else {
                                                text = (years === 1 ? '1 year' : years + ' years') + ' ' + (months === 1 ? '1 month' : months + ' months');
                                            }
                                        }
                                        setVal('infant_age', text || (info.infant.age + ' months'));
                                    })();
                                    // Append kg to birthweight for clarity
                                    setVal('infant_birthweight', (info.infant.birthweight ?? '') + (info.infant.birthweight != null ? ' kg' : ''));
                                } else {
                                    setVal('infant_full_name', 'Not found');
                                    setVal('infant_sex', 'Not found');
                                    setVal('infant_dob', 'Not found');
                                    setVal('infant_age', 'Not found');
                                    setVal('infant_birthweight', 'Not found');
                                }
                            })
                            .catch(() => { });

                        // Fetch donor profile AFTER form render so fields exist
                        (function populateDonorProfile() {
                            const setVal = (id, val) => { const el = document.getElementById(id); if (el) el.value = val ?? '—'; };
                            setVal('form_date', new Date().toLocaleDateString());
                            fetch('/profile/current')
                                .then(r => r.json())
                                .then(p => {
                                    if (p && p.success) {
                                        setVal('donor_full_name', p.user.full_name);
                                        setVal('donor_dob', new Date(p.user.date_of_birth).toLocaleDateString());
                                        setVal('donor_age', p.user.age);
                                        setVal('donor_sex', p.user.sex);
                                        setVal('donor_address', p.user.address);
                                        setVal('donor_contact', p.user.contact_number);
                                    }
                                })
                                .catch(() => { });
                        })();
                    }
                    console.log('Health screening form loaded');
                    updateActiveNav('health');
                    console.log('Navigation updated');
                })
                .catch(error => {
                    console.error('Error checking health screening status:', error);
                    // Avoid calling undefined fallback; show a gentle inline notice instead
                    if (typeof showInlineNotification === 'function') {
                        showInlineNotification('error', '', 'Unable to load health screening status. Please refresh the page.', 5000);
                    }
                });
        }

        // Open review modal: fetch answers and render with Back/Submit
        async function openHealthReviewModal() {
            try {
                const res = await fetch('{{ route("health-screening.review-data") }}');
                const data = await res.json();
                if (!data.success) { alert('Unable to load review data.'); return; }

                // Build modal HTML if not exists
                let modal = document.getElementById('health-review-modal');
                if (!modal) {
                    modal = document.createElement('div');
                    modal.id = 'health-review-modal';
                    // Use high z-index so overlay sits above top bar/sidebar
                    modal.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,.5);display:flex;align-items:center;justify-content:center;z-index:10050;';
                    modal.innerHTML = `
                        <div style="background:#fff;max-width:900px;width:95%;border-radius:16px;padding:0;max-height:85vh;overflow:hidden;box-shadow:0 25px 50px rgba(0,0,0,0.25);">
                            <div style="display:flex;justify-content:space-between;align-items:center;padding:24px 24px 16px 24px;border-bottom:1px solid #f1f5f9;">
                                <h2 style="margin:0;font-size:24px;font-weight:700;color:#1f2937;background:linear-gradient(135deg,#ff69b4,#d63384);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Review your Health Screening</h2>
                                <button id="review-close" style="background:#f8fafc;border:2px solid #e2e8f0;border-radius:12px;font-size:18px;cursor:pointer;width:44px;height:44px;display:flex;align-items:center;justify-content:center;line-height:1;color:#64748b;transition:all 0.2s ease;font-weight:bold;">&times;</button>
                            </div>
                            <div id="review-body" style="padding:20px 24px;max-height:60vh;overflow-y:auto;"></div>
                            <div style="display:flex;justify-content:space-between;padding:20px 24px;background:#f8fafc;border-top:1px solid #e2e8f0;gap:12px;">
                                <button id="review-back" style="background:transparent;color:#6b7280;border:2px solid #d1d5db;padding:14px 24px;border-radius:12px;cursor:pointer;font-size:16px;font-weight:600;transition:all 0.3s ease;display:flex;align-items:center;gap:8px;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M19 12H5"></path>
                                        <path d="M12 19l-7-7 7-7"></path>
                                    </svg>
                                    Back
                                </button>
                                <button id="review-submit" style="background:linear-gradient(135deg,#10b981,#059669);color:#fff;border:none;padding:14px 28px;border-radius:12px;cursor:pointer;font-size:16px;font-weight:600;transition:all 0.3s ease;box-shadow:0 4px 14px rgba(16,185,129,0.25);display:flex;align-items:center;gap:8px;">
                                    Submit
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M5 12h14"></path>
                                        <path d="M12 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>`;
                    document.body.appendChild(modal);
                    try { document.body.classList.add('modal-open'); document.body.style.overflow = 'hidden'; } catch (_) { }

                    // Enhanced close button functionality
                    const closeBtn = document.getElementById('review-close');
                    closeBtn.onclick = () => {
                        try { document.body.classList.remove('modal-open'); document.body.style.overflow = 'auto'; } catch (_) { };
                        modal.remove();
                    };

                    // Add hover effects to close button
                    closeBtn.addEventListener('mouseenter', () => {
                        closeBtn.style.background = '#ef4444';
                        closeBtn.style.borderColor = '#dc2626';
                        closeBtn.style.color = '#fff';
                        closeBtn.style.transform = 'scale(1.05)';
                    });
                    closeBtn.addEventListener('mouseleave', () => {
                        closeBtn.style.background = '#f8fafc';
                        closeBtn.style.borderColor = '#e2e8f0';
                        closeBtn.style.color = '#64748b';
                        closeBtn.style.transform = 'scale(1)';
                    });

                    // Add hover effects to back button
                    const backBtn = document.getElementById('review-back');
                    backBtn.addEventListener('mouseenter', () => {
                        backBtn.style.background = '#f3f4f6';
                        backBtn.style.borderColor = '#9ca3af';
                        backBtn.style.color = '#374151';
                        backBtn.style.transform = 'translateY(-1px)';
                    });
                    backBtn.addEventListener('mouseleave', () => {
                        backBtn.style.background = 'transparent';
                        backBtn.style.borderColor = '#d1d5db';
                        backBtn.style.color = '#6b7280';
                        backBtn.style.transform = 'translateY(0)';
                    });

                    // Add hover effects to submit button
                    const submitBtn = document.getElementById('review-submit');
                    submitBtn.addEventListener('mouseenter', () => {
                        submitBtn.style.background = 'linear-gradient(135deg,#059669,#047857)';
                        submitBtn.style.transform = 'translateY(-2px)';
                        submitBtn.style.boxShadow = '0 8px 25px rgba(16,185,129,0.4)';
                    });
                    submitBtn.addEventListener('mouseleave', () => {
                        submitBtn.style.background = 'linear-gradient(135deg,#10b981,#059669)';
                        submitBtn.style.transform = 'translateY(0)';
                        submitBtn.style.boxShadow = '0 4px 14px rgba(16,185,129,0.25)';
                    });
                }

                // Render answers by sections
                const rb = modal.querySelector('#review-body');
                const sec = (title, items) => `
                    <div style="margin-bottom:16px;background:#f8f9fa;border-left:4px solid #ff69b4;padding:12px;border-radius:6px;">
                        <h3 style="margin:0 0 8px 0;">${title}</h3>
                        <div>${items.join('')}</div>
                    </div>`;
                const row = (q, a, qb = null) => `<div style=\"padding:6px 0;border-bottom:1px dashed #ddd;\"><strong>${q}</strong>${qb ? `<div style=\\"font-size:12px;color:#374151;margin-top:2px;\\">${qb}</div>` : ''}<div style=\"color:#444;\">${a ?? '—'}</div></div>`;

                // The controller returns { success: true, data: { ... } }
                const s = data.data || {};
                const html = [];

                // Basic info
                html.push(sec('Basic Information', [
                    row('Civil Status', s.civil_status),
                    row('Occupation', s.occupation),
                    row('Type of Donor', s.type_of_donor)
                ]));

                // Medical history mapping
                const mh = s.medical_history || {};
                const mhLabels = {
                    1: ['Have you donated breastmilk before?', 'Nakahatag/naka-donar ka na ba sa imung gatas kaniadto?'],
                    2: ['Have you for any reason been deferred as a breastmilk donor?', 'Naballbaran na ba ka nga mag-donar sa imung gatas kaniadto?'],
                    3: ['Did you have a normal pregnancy and delivery for your most recent pregnancy?', 'Wala ka bay naaging mnga kalisod og komplikasyon sa pinakaulahi nimung pagburos og pagpanganak?'],
                    4: ['Do you have any acute or chronic infection such as but not limited to: tuberculosis, hepatitis, systemic disorders?', 'Aduna ka bay gibating mga sakit sama sa Tuberculosis, sakit sa atay or sakit sa dugo?'],
                    5: ['Have you been diagnosed with a chronic non-infectious illness such as but not limited to: diabetes, hypertension, heart disease?', 'Nadayagnos ka ba nga adunay laygay nga dll makatakod nga sakit sama sa apas dill limitado sa: altapresyon, sakit sa kasingkasing'],
                    6: ['Have you received any blood transfusion or any blood products within the last twelve (12) months?', 'Naabunohan ka ba ug dugo sulod sa niaging 12 ka buwan?'],
                    7: ['Have you received any organ or tissue transplant within the last twelve (12) months?', 'Niagi ka ba ug operasyon din nidawat ka ug bahin/parte sa lawas sulod sa nlilabay nga 12 ka bulan?'],
                    8: ['Have you had any intake of alcohol or hard liquor within the last twenty four (24) hours?', 'Sulod sa 24 oras, naka inum ka ba og bisan unsang ilimnong makahubog?'],
                    9: ['Do you use megadose vitamins or pharmacologically active herbal preparations?', 'Gainum ka ba og sobra sa gitakda na mga bitamina og mga produktong adunay sagol na herbal?'],
                    10: ['Do you regularly use over-the-counter medications or systemic preparations such as replacement hormones, birth control hormones, anti-hypoglycemics, blood thinners?', 'Kanunay ba ka gagamit o gainum sa mga tambal kung lain ang paminaw sa lawas? Og gainum ka ba sa mha tambal pampugong sa pagburos?'],
                    11: ['Are you a total vegetarian/vegan?', 'Ikaw ba dili gakaon sa lain pagkaon kundi utan lang?'],
                    12: ['Do you use illicit drugs?', 'Gagamit ka ba sa ginadilina mga droga?'],
                    13: ['Do you smoke?', 'Gapanigarilyo ka ba?'],
                    14: ['Are you around people who smoke (passive smoking)?', 'doul ba ka permi sa mga tao nga gapanigarilyo?'],
                    15: ['Have you had breast augmentation surgery, using silicone breast implants?', 'kaw ba niagi ug operasyon sa imung suso din nagpabutang ug "silicone" O artipisyal na suso?']
                };
                const mhHtml = [];
                for (let i = 1; i <= 15; i++) {
                    const ans = mh[`mhq_${i}`];
                    if (ans != null) {
                        const lbl = mhLabels[i];
                        if (Array.isArray(lbl)) mhHtml.push(row(lbl[0], ans, lbl[1])); else mhHtml.push(row(lbl || `Question ${i}`, ans));
                    }
                    // Additional fields
                    if (i === 2 && mh.mhq_2_reason) mhHtml.push(row('Reason (for #2)', mh.mhq_2_reason));
                    if (i === 4 && mh.mhq_4_reason) mhHtml.push(row('Specific disease(s) (for #4)', mh.mhq_4_reason));
                    if (i === 5 && mh.mhq_5_reason) mhHtml.push(row('Specific disease(s) (for #5)', mh.mhq_5_reason));
                    if (i === 8 && mh.mhq_8_amount) mhHtml.push(row('Amount/frequency (for #8)', mh.mhq_8_amount));
                    if (i === 10 && mh.mhq_10_reason) mhHtml.push(row('Medication(s) (for #10)', mh.mhq_10_reason));
                    if (i === 11 && mh.mhq_11_supplement != null) mhHtml.push(row('Supplement with vitamins? (for #11)', mh.mhq_11_supplement));
                    if (i === 13 && mh.mhq_13_amount) mhHtml.push(row('Sticks/packs per day (for #13)', mh.mhq_13_amount));
                }
                if (mhHtml.length) html.push(sec('Medical History', mhHtml));

                // Sexual history mapping
                const sh = s.sexual_history || {};
                const shLabels = {
                    1: ['Have you ever had Syphilis, HIV, herpes or any sexually transmitted disease (STD)?', 'Niagi ka ba og bisan unsang sakit sa kinatawo? ○ sakit na makuha pinaagi sa pakighilawas?'],
                    2: ['Do you have multiple sexual partners?', 'aduna ka bay lain pares sa pakighilawas gawas sa imu bana/kapikas?'],
                    4: ['Have you had a tattoo applied or had an accidental needlestick injury or contact with someone else\'s blood?', 'Niagi ka ba og papatik sukad? Niagi ka ba og katusok sa bisan unsang dagom?']
                };
                const shHtml = [];
                if (sh.shq_1 != null) { const l = shLabels[1]; shHtml.push(row(l[0], sh.shq_1, l[1])); }
                if (sh.shq_2 != null) { const l = shLabels[2]; shHtml.push(row(l[0], sh.shq_2, l[1])); }
                // Question 3 checkboxes as a list
                const q3Map = {
                    shq_3_bisexual: 'Bisexual (silahis)',
                    shq_3_promiscuous: 'Promiscuous (bisan kinsa ang pares)',
                    shq_3_std: 'Has had an STD, AIDS/HIV (adunay sakit sa kinatawo)',
                    shq_3_blood: 'Received blood for a long period of time for a bleeding problem (niagi og abuno sa dugo)',
                    shq_3_drugs: 'Is an intravenous drug user (gagamit og bisan unsang druga pinaagi sa pagtusok sa dagum sa ugat)'
                };
                const q3List = Object.keys(q3Map).filter(k => !!sh[k]).map(k => q3Map[k]);
                if (q3List.length) shHtml.push(row('Have you had a sexual partner who is:', q3List.join(', '), 'Niagi ka ba og pakighilawas ning mga mosunod?'));
                if (sh.shq_4 != null) { const l = shLabels[4]; shHtml.push(row(l[0], sh.shq_4, l[1])); }
                if (shHtml.length) html.push(sec('Sexual History', shHtml));

                // Donor infant mapping
                const di = s.donor_infant || {};
                const diLabels = {
                    1: ['Is your child healthy?', 'Himsog ba ang imung anak?'],
                    2: ['Was your child delivered full term?', 'Gipanganak ba siya sa saktong buwan?'],
                    3: ['Are you exclusively breastfeeding your child?', 'Kaugalingong gatas lang ba nimu ang gipalnum sa bata?'],
                    4: ['Is/was your youngest child jaundiced?', 'imung kinamanghuran na bata ba niagi og pagdalag sa pamanit?'],
                    5: ['Have you ever received breastmilk from another mother?', 'Nakadawat ba ang imung anak og gatas sa laing inahan?']
                };
                const diHtml = [];
                for (let i = 1; i <= 5; i++) {
                    const ans = di[`diq_${i}`];
                    if (ans != null) { const l = diLabels[i]; diHtml.push(row(l[0], ans, l[1])); }
                    if (i === 4 && di.diq_4_reason) diHtml.push(row('Details (for #4)', di.diq_4_reason));
                    if (i === 5 && di.diq_5_reason) diHtml.push(row('Details (for #5)', di.diq_5_reason));
                }
                if (diHtml.length) html.push(sec("Donor's Infant", diHtml));

                rb.innerHTML = html.join('');

                // Wire buttons
                modal.style.display = 'flex';
                modal.querySelector('#review-back').onclick = () => { try { document.body.classList.remove('modal-open'); document.body.style.overflow = 'auto'; } catch (_) { }; modal.remove(); };
                modal.querySelector('#review-submit').onclick = async () => {
                    try {
                        if (window.Swal) {
                            const confirm = await Swal.fire({
                                title: 'Final submit?',
                                text: 'You cannot edit after final submission. Proceed?',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Submit',
                                cancelButtonText: 'Cancel',
                                reverseButtons: true
                            });
                            if (!confirm.isConfirmed) return;
                        }
                        const r = await fetch('{{ route("health-screening.final-submit") }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } });
                        const j = await r.json();
                        if (j.success) {
                            if (window.Swal) {
                                Swal.fire('Submitted', 'Health screening submitted successfully! Please wait for admin review.', 'success').then(() => {
                                    try { document.body.classList.remove('modal-open'); document.body.style.overflow = 'auto'; } catch (_) { }
                                    modal.remove();
                                    showHealthScreening();
                                });
                            } else {
                                showSuccessModalWithInline('Health screening submitted successfully! Please wait for admin review.', () => {
                                    try { document.body.classList.remove('modal-open'); document.body.style.overflow = 'auto'; } catch (_) { }
                                    modal.remove();
                                    showHealthScreening();
                                });
                            }
                        } else {
                            if (window.Swal) Swal.fire('Submission failed', j.message || 'Submission failed.', 'error'); else showInlineNotification('error', '', j.message || 'Submission failed.', 5000);
                        }
                    } catch (_) {
                        if (window.Swal) Swal.fire('Network error', 'Unable to submit health screening. Please check your connection and try again.', 'error'); else showInlineNotification('error', '', 'Unable to submit health screening. Please check your connection and try again.', 5000);
                    }
                };
            } catch (_) {
                showInlineNotification('error', '', 'Unable to open health screening review. Please try again.', 5000);
            }
        }

        async function showPending() {
            document.getElementById('pageTitle').textContent = 'PENDING DONATIONS';
            // Hide Facebook widget when in pending donations section
            toggleFacebookWidget(false);
            if (window.location.pathname === '/dashboard/settings') {
                history.replaceState({ view: 'pending' }, '', '/dashboard');
            }
            document.getElementById('dashboard-view').innerHTML = `
                <div class="content-box">
                    <h1 class="content-title">Pending Donations</h1>
                    <div class="table-container" style="overflow-x:auto;">
                        <table class="history-table" id="pending-table">
                            <thead>
                                <tr>
                                    <th>Donation Method</th>
                                    <th>Number of Bags</th>
                                    <th>Total Volume (ml)</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="pendingRequestsList"><tr><td colspan="6" style="text-align:center;color:#666;">Loading...</td></tr></tbody>
                        </table>
                    </div>
                </div>
            `;
            updateActiveNav('pending');
            await loadPendingRequests();
        }

        async function loadPendingRequests({ autoRedirectIfEmpty = false } = {}) {
            const listEl = document.getElementById('pendingRequestsList');
            if (!listEl) return;

            const render = (items) => {
                const tbody = listEl; // tbody element
                tbody.innerHTML = '';
                if (!items || items.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;color:#666;padding:12px;">No pending donation requests.</td></tr>`;
                    return;
                }
                const formatDate = (d) => d ? new Date(d).toLocaleDateString() : '—';
                const formatTime12h = (t) => {
                    if (!t || t === '00:00:00') return '—';
                    const m = String(t).match(/^(\d{1,2}):(\d{2})(?::(\d{2}))?/);
                    if (!m) return t;
                    let h = parseInt(m[1], 10); const min = m[2]; const ampm = h >= 12 ? 'PM' : 'AM'; h = h % 12; if (h === 0) h = 12; return `${h}:${min} ${ampm}`;
                };
                items.forEach(it => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                                    <td>${it.donation_type === 'walk_in' ? 'Walk-in' : 'Home Collection'}</td>
                                    <td>${it.number_of_bags ?? '—'}</td>
                                    <td>${it.total_volume ?? '—'}</td>
                                    <td>${formatDate(it.donation_date)}</td>
                                    <td>${formatTime12h(it.donation_time)}</td>
                                    <td><span class="pill pending">PENDING</span></td>
                                `;
                    tbody.appendChild(tr);
                });
            };

            // 1) Instant render from preloaded data
            if (Array.isArray(window.__PENDING_REQUESTS__)) {
                render(window.__PENDING_REQUESTS__);
            }

            // 2) Silent background refresh to keep fresh data (no spinner)
            try {
                const res = await fetch('/donation/pending-requests', { headers: { 'Accept': 'application/json', 'Cache-Control': 'no-store' } });
                if (!res.ok) throw new Error('Failed to load pending requests');
                const json = await res.json();
                if (!json.success) throw new Error(json.message || 'Failed');
                const items = json.data || [];

                // Only re-render if different length or first item changed (basic diff)
                const current = Array.isArray(window.__PENDING_REQUESTS__) ? window.__PENDING_REQUESTS__ : [];
                const changed = current.length !== items.length || JSON.stringify(current[0] || {}) !== JSON.stringify(items[0] || {});
                if (changed) {
                    window.__PENDING_REQUESTS__ = items;
                    render(items);
                }

                if (items.length === 0 && autoRedirectIfEmpty) {
                    // Optional: showDonationHistory();
                }
            } catch (err) {
                console.warn('Background refresh of pending requests failed:', err);
                // Keep preloaded content; don't show an error box to avoid UX flicker
            }
        }

        function showDonate() {
            document.getElementById('pageTitle').textContent = 'DONATE';
            // Hide Facebook widget when in donate section
            toggleFacebookWidget(false);
            if (window.location.pathname === '/dashboard/settings') {
                history.replaceState({ view: 'donate' }, '', '/dashboard');
            }

            // First check health screening status
            fetch('/health-screening/check-existing')
                .then(response => response.json())
                .then(data => {
                    if (!data.hasExisting) {
                        // No health screening submitted yet
                        document.getElementById('dashboard-view').innerHTML = `
                            <div class="content-box">
                                <h1 class="content-title">Donate Breastmilk</h1>
                                <div style="text-align: center; padding: 40px 20px;">
                                    <div style="font-size: 48px; margin-bottom: 20px;"><i class="fas fa-clipboard-list"></i></div>
                                    <h2 style="color: #666; margin-bottom: 20px;">Health Screening Required</h2>
                                    <p style="color: #666; margin-bottom: 30px; line-height: 1.6;">
                                        You must complete the health screening process before you can donate breastmilk.<br>
                                        This ensures the safety and quality of donated milk for babies in need.
                                    </p>
                                    <button onclick="showHealthScreening()" class="submit-btn" style="padding: 12px 30px; font-size: 16px;">
                                        Complete Health Screening
                                    </button>
                                </div>
                            </div>
                        `;
                    } else if (data.status === 'pending') {
                        // Health screening is pending
                        document.getElementById('dashboard-view').innerHTML = `
                            <div class="content-box">
                                <h1 class="content-title">Donate Breastmilk</h1>
                                <div style="text-align: center; padding: 40px 20px;">
                                    <div style="font-size: 48px; margin-bottom: 20px;">⏳</div>
                                    <h2 style="color: #666; margin-bottom: 20px;">Health Screening Under Review</h2>
                                    <p style="color: #666; margin-bottom: 30px; line-height: 1.6;">
                                        Your health screening is currently being reviewed by our medical team.<br>
                                        You will be notified once the review is complete.
                                    </p>
                                    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-top: 20px;">
                                        <strong>Status:</strong> <span class="pill pending">PENDING</span><br>
                                        <strong>Submitted:</strong> ${new Date(data.created_at_iso || data.created_at).toLocaleString()}
                                    </div>
                                </div>
                            </div>
                        `;
                    } else if (data.status === 'declined') {
                        // Health screening was declined
                        document.getElementById('dashboard-view').innerHTML = `
                            <div class="content-box">
                                <h1 class="content-title">Donate Breastmilk</h1>
                                <div style="text-align: center; padding: 40px 20px;">
                                    <div style="font-size: 48px; margin-bottom: 20px;"><i class="fas fa-times-circle"></i></div>
                                    <h2 style="color: #666; margin-bottom: 20px;">Health Screening Not Approved</h2>
                                    <p style="color: #666; margin-bottom: 30px; line-height: 1.6;">
                                        Unfortunately, your health screening was not approved at this time.<br>
                                        Please contact our medical team for more information.
                                    </p>
                                    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-top: 20px;">
                                        <strong>Status:</strong> <span class="pill declined">DECLINED</span><br>
                                        <strong>Date Declined:</strong> ${new Date(data.updated_at_iso || data.updated_at).toLocaleString()}<br>
                                        ${data.admin_notes ? `<strong>Notes:</strong> ${data.admin_notes}` : ''}
                                    </div>
                                </div>
                            </div>
                        `;
                    } else if (data.status === 'accepted') {
                        // Health screening accepted - show donation options
                        document.getElementById('dashboard-view').innerHTML = `
                            <div class="content-box">
                                <h1 class="content-title">Donate Breastmilk</h1>
                                <p style="text-align: center; color: #666; margin-bottom: 30px;">
                                    Choose your preferred donation method below.
                                </p>
                                
                                <div class="donation-options">
                                    <div class="donation-option" onclick="showWalkInForm()">
                                        <div class="option-icon"><i class="fas fa-person-walking" aria-hidden="true"></i></div>
                                        <h3>Walk-in Donation</h3>
                                        <p>Visit our facility to donate breastmilk in person</p>
                                    </div>
                                    
                                    <div class="donation-option" onclick="showHomeCollectionForm()">
                                        <div class="option-icon"><i class="fas fa-house" aria-hidden="true"></i></div>
                                        <h3>Home Collection</h3>
                                        <p>Schedule a pickup from your home</p>
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                    updateActiveNav('donate');
                })
                .catch(error => {
                    console.error('Error checking health screening status:', error);
                });
        }

        function showWalkInForm() {
            openModal('walkInModal');

            const calendarEl = document.getElementById('walkInCalendar');
            calendarEl.innerHTML = '';

            // Build vanilla calendar container
            const header = document.createElement('div');
            header.className = 'vc-header';
            const prevBtn = document.createElement('button'); prevBtn.className = 'vc-nav-btn'; prevBtn.textContent = 'Prev';
            const titleEl = document.createElement('div'); titleEl.className = 'vc-title';
            const nextBtn = document.createElement('button'); nextBtn.className = 'vc-nav-btn'; nextBtn.textContent = 'Next';
            header.appendChild(prevBtn); header.appendChild(titleEl); header.appendChild(nextBtn);

            const weekdays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            const grid = document.createElement('div'); grid.className = 'vc-grid';
            weekdays.forEach(w => {
                const wd = document.createElement('div'); wd.className = 'vc-weekday'; wd.textContent = w; grid.appendChild(wd);
            });

            const daysContainer = document.createElement('div'); daysContainer.className = 'vc-grid';

            const wrapper = document.createElement('div'); wrapper.className = 'vc-calendar';
            wrapper.appendChild(header); wrapper.appendChild(grid); wrapper.appendChild(daysContainer);
            calendarEl.appendChild(wrapper);

            let current = new Date();
            current.setDate(1);
            let avail = {};

            async function fetchAvailability() {
                try {
                    const resp = await fetch('{{ route("admin.availability.get") }}');
                    const data = await resp.json();
                    avail = {};
                    data.forEach(item => { avail[item.date] = true; });
                } catch (_) {
                    avail = {};
                }
            }

            function fmtYmd(d) {
                const y = d.getFullYear();
                const m = String(d.getMonth() + 1).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');
                return `${y}-${m}-${day}`;
            }

            async function render() {
                titleEl.textContent = current.toLocaleString('en-US', { month: 'long', year: 'numeric' });
                daysContainer.innerHTML = '';
                const firstDay = new Date(current.getFullYear(), current.getMonth(), 1);
                const startWeekday = firstDay.getDay();
                const daysInMonth = new Date(current.getFullYear(), current.getMonth() + 1, 0).getDate();

                // leading blanks for previous month
                for (let i = 0; i < startWeekday; i++) {
                    const blank = document.createElement('div'); blank.className = 'vc-day'; blank.textContent = ''; daysContainer.appendChild(blank);
                }

                // ensure availability is loaded
                if (Object.keys(avail).length === 0) { await fetchAvailability(); }

                for (let d = 1; d <= daysInMonth; d++) {
                    const cell = document.createElement('div');
                    cell.className = 'vc-day';
                    cell.textContent = String(d);
                    const dateStr = fmtYmd(new Date(current.getFullYear(), current.getMonth(), d));
                    if (avail[dateStr]) {
                        cell.classList.add('available');
                        cell.addEventListener('click', () => {
                            daysContainer.querySelectorAll('.vc-day.available').forEach(el => el.classList.remove('selected'));
                            cell.classList.add('selected');
                            loadAvailableSlots(dateStr, 'walkIn');
                        });
                    }
                    daysContainer.appendChild(cell);
                }
            }

            prevBtn.onclick = async () => { current.setMonth(current.getMonth() - 1); await render(); };
            nextBtn.onclick = async () => { current.setMonth(current.getMonth() + 1); await render(); };

            render();
        }


        function showHomeCollectionForm() {
            openModal('homeCollectionModal');
            // Lazy-load map only when needed
            try { loadGoogleMapsIfNeeded().then(initPickupMap).catch(() => { }); } catch (_) { }
        }

        function showDonationHistory() {
            document.getElementById('pageTitle').textContent = 'MY DONATION HISTORY';
            // Hide Facebook widget when in donation history section
            toggleFacebookWidget(false);
            if (window.location.pathname === '/dashboard/settings') {
                history.replaceState({ view: 'history' }, '', '/dashboard');
            }
            document.getElementById('dashboard-view').innerHTML = `
                <div class="content-box">
                    <h1 class="content-title">My Donation History</h1>
                    <div class="donation-history">
                        <table class="history-table">
                            <thead>
                                <tr>
                                    <th>Donation Method</th>
                                    <th>Number of bags</th>
                                    <th>Total Volume</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody id="donationHistoryBody"></tbody>
                        </table>
                    </div>
                </div>
            `;

            fetch('/donation/history')
                .then(response => response.json())
                .then(data => {
                    const historyBody = document.getElementById('donationHistoryBody');
                    if (data.success && data.data.length > 0) {
                        data.data.forEach(donation => {
                            const row = document.createElement('tr');
                            const donationMethod = donation.donation_type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
                            const numberOfBags = donation.number_of_bags || 'N/A';
                            const totalVolume = donation.total_volume ? `${donation.total_volume} ml` : 'N/A';
                            const date = new Date(donation.donation_date).toLocaleDateString();
                            const time = new Date(`2000-01-01T${donation.donation_time}`).toLocaleTimeString('en-US', {
                                hour: 'numeric',
                                minute: '2-digit',
                                hour12: true
                            });

                            row.innerHTML = `
                                <td>${donationMethod}</td>
                                <td><strong style="color: #ff69b4;">${numberOfBags}</strong></td>
                                <td>${totalVolume}</td>
                                <td>${date}</td>
                                <td>${time}</td>
                            `;
                            historyBody.appendChild(row);
                        });
                    } else {
                        historyBody.innerHTML = '<tr><td colspan="5">No donation history found.</td></tr>';
                    }
                })
                .catch(error => {
                    console.error('Error fetching donation history:', error);
                    document.getElementById('donationHistoryBody').innerHTML = '<tr><td colspan="5">Error loading history.</td></tr>';
                });

            updateActiveNav('history');
        }

        function showBreastMilkRequest() {
            document.getElementById('pageTitle').textContent = 'BREASTMILK REQUEST';
            // Hide Facebook widget when in breastmilk request section
            toggleFacebookWidget(false);
            document.getElementById('dashboard-view').innerHTML = `
                <div class="content-box">
                    <h1 class="content-title">Breastmilk Request</h1>

                    <div class="request-form-container">
                        <div class="modern-form-card">
                            <form id="breastmilk-request-form" method="POST" action="{{ route('breastmilk-request.store') }}" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="medical_condition" value="None" />

                                <style>
                                    .bm-tabs { display:flex; gap:8px; border-bottom:1px solid #f4c6d6; margin:8px 0 14px; position:relative; flex-wrap:wrap; }
                                    .bm-tab { appearance:none; background:#fff6fa; border:1px solid #f4c6d6; border-bottom:none; padding:8px 14px; font-weight:600; cursor:pointer; border-top-left-radius:10px; border-top-right-radius:10px; position:relative; }
                                    .bm-tab[aria-selected="true"], .bm-tab.active { background:#ffd9e7; }
                                    .bm-tab:focus-visible { outline:2px solid #ff69b4; outline-offset:2px; }
                                    .bm-tab-panel { display:none; }
                                    .bm-tab-panel.active { display:block; animation:fadeIn .25s ease; }
                                    @keyframes fadeIn { from { opacity:0; transform:translateY(4px);} to { opacity:1; transform:translateY(0);} }
                                    .bm-actions { margin-top:18px; display:flex; gap:10px; }
                                    .bm-next { background:#ff69b4; color:#fff; border:none; padding:8px 14px; border-radius:8px; font-weight:600; cursor:pointer; }
                                    .bm-prev { background:#e5e7eb; color:#1f2937; border:none; padding:8px 14px; border-radius:8px; font-weight:600; cursor:pointer; }
                                    .bm-next:disabled { opacity:.5; cursor:not-allowed; }
                                </style>
                                <div class="bm-inline-alert" role="alert" aria-live="polite" style="background:#fff4e5; border:1px solid #f6c88f; padding:10px 14px; border-radius:10px; display:flex; gap:10px; align-items:flex-start; line-height:1.4; margin:4px 0 14px;">
                                    <span style="color:#d18400; font-size:18px; line-height:1; padding-top:2px;" aria-hidden="true"><i class="fas fa-exclamation-triangle"></i></span>
                                    <span style="flex:1; font-size:14px; color:#6b4b16;">
                                        <strong style="display:block; font-size:14px; margin-bottom:2px;">Important Notice</strong>
                                        There might be a possibility that your request is declined by the human milk bank staff upon arriving in the unit due to strict policy and rules in dispensing breast milk.
                                    </span>
                                </div>
                                <div class="bm-tabs" role="tablist" aria-label="Breastmilk Request Sections">
                                    <button type="button" class="bm-tab active" role="tab" aria-selected="true" aria-controls="bm-panel-recipient" id="bm-tab-recipient">Recipient</button>
                                    <button type="button" class="bm-tab" role="tab" aria-selected="false" aria-controls="bm-panel-appointment" id="bm-tab-appointment">Appointment</button>
                                    <button type="button" class="bm-tab" role="tab" aria-selected="false" aria-controls="bm-panel-prescription" id="bm-tab-prescription">Prescription</button>
                                </div>
                                <div id="bm-panel-recipient" class="bm-tab-panel active" role="tabpanel" aria-labelledby="bm-tab-recipient">
                                    <h3 class="section-title"><i class="fas fa-baby"></i> Recipient Information</h3>
                                    <small id="bm_infant_info_note" class="form-help" style="display:none;margin-top:6px;margin-bottom:10px;">These fields are auto-filled from your registered infant information and cannot be edited here.</small>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label class="form-label"><i class="fas fa-user"></i> Recipient Full Name *</label>
                                            <input id="bm_recipient_name" type="text" name="recipient_name" class="form-control" required placeholder="Enter recipient's full name" onblur="validateField(this)" oninput="validateNameField(this)" pattern="[a-zA-Z\s]+" title="Only letters and spaces are allowed">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label"><i class="fas fa-birthday-cake"></i> Date of Birth *</label>
                                            <input id="bm_recipient_dob" type="date" name="recipient_dob" class="form-control" required onblur="validateField(this)">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label class="form-label"><i class="fas fa-weight"></i> Weight (kg) *</label>
                                            <input id="bm_recipient_weight" type="number" name="recipient_weight" class="form-control" step="0.01" min="0" required placeholder="Enter weight in kg" onblur="validateField(this)">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label"><i class="fas fa-phone"></i> Contact Number *</label>
                                            <input id="bm_contact_number" type="tel" name="contact_number" class="form-control" required placeholder="Enter 11-digit number" inputmode="numeric" pattern="^\\d{11}$" title="Enter exactly 11 digits" oninput=" this.value=this.value.replace(/[^0-9]/g,''); const alertEl=this.parentElement.querySelector('.contact-alert'); if (this.value.length>11 || (this.value.length>0 && this.value.length<11)) { this.setCustomValidity('Contact number must be exactly 11 digits'); if(alertEl){alertEl.style.display='block';} this.classList.remove('success'); this.classList.add('error'); } else { this.setCustomValidity(''); if(alertEl){alertEl.style.display='none';} this.classList.remove('error'); this.classList.add('success'); } " oninvalid="this.setCustomValidity('Contact number must be exactly 11 digits')">
                                            <div class="contact-alert error-text" style="display:none;">Contact number must be exactly 11 digits.</div>
                                        </div>
                                    </div>
                                </div>
                                <div id="bm-panel-appointment" class="bm-tab-panel" role="tabpanel" aria-labelledby="bm-tab-appointment">
                                    <h3 class="section-title"><i class="fas fa-hospital"></i> Appointment</h3>
                                    <div class="form-group" style="grid-column:1/-1;">
                                        <label class="form-label"><i class="fas fa-clock"></i> Choose Appointment</label>
                                        <input type="hidden" name="scheduled_date" id="bmScheduledDate" required />
                                        <input type="hidden" name="scheduled_time" id="bmScheduledTime" required />
                                        <div id="bmSelectedAppt" style="margin:6px 0 10px; padding:8px 10px; border:1px dashed #ccc; border-radius:6px; color:#666;">No appointment selected</div>
                                        <div id="bmInlineSchedule" class="vc" style="border:1px solid #eee; border-radius:10px; padding:12px; background:#fff;">
                                            <div class="vc-header" style="display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:10px;">
                                                <button class="vc-prev" type="button" style="padding:6px 10px; background:#ffd1e6; border:1px solid #ff69b4; color:#7a2944; border-radius:6px;">Prev</button>
                                                <div class="vc-title" style="font-weight:600;">&nbsp;</div>
                                                <button class="vc-next" type="button" style="padding:6px 10px; background:#ffd1e6; border:1px solid #ff69b4; color:#7a2944; border-radius:6px;">Next</button>
                                            </div>
                                            <div class="vc-dow" style="display:grid;grid-template-columns:repeat(7,1fr);gap:6px; margin-bottom:6px; color:#777; font-size:12px;">
                                                <div style="text-align:center;">Sun</div><div style="text-align:center;">Mon</div><div style="text-align:center;">Tue</div><div style="text-align:center;">Wed</div><div style="text-align:center;">Thu</div><div style="text-align:center;">Fri</div><div style="text-align:center;">Sat</div>
                                            </div>
                                            <div class="vc-days" style="display:grid;grid-template-columns:repeat(7,1fr);gap:6px;"></div>
                                            <div class="selected-date-info" style="display:none;margin-top:12px;">
                                                <strong class="selected-date"></strong>
                                            </div>
                                            <div class="bm-slots-panel" style="margin-top:12px; background:#fff; border:1px solid #e9ecef; border-radius:10px; min-height:90px; padding:10px;">
                                                <div class="time-slots-grid"></div>
                                            </div>
                                        </div>
                                        <small class="form-help">Pick any available slot. Admin will finalize the volume and bags when dispensing.</small>
                                    </div>
                                </div>
                                <div id="bm-panel-prescription" class="bm-tab-panel" role="tabpanel" aria-labelledby="bm-tab-prescription">
                                    <h3 class="section-title"><i class="fas fa-clipboard-list"></i> Doctor's Prescription</h3>
                                    <div class="form-group">
                                        <label class="form-label"><i class="fas fa-file-medical"></i> Upload Scanned Doctor's Prescription *</label>
                                        <div class="file-upload-container">
                                            <input type="file" name="prescription_image" id="prescription-upload" class="file-input" accept="image/*,.pdf" required>
                                            <label for="prescription-upload" class="file-upload-label"><i class="fas fa-paperclip"></i> Choose File</label>
                                            <span class="file-name" id="file-name">No file selected</span>
                                        </div>
                                        <small class="form-help">Accepted formats: JPG, PNG, PDF (Max size: 5MB)</small>
                                    </div>
                                    <div class="prescription-preview" id="prescription-preview" style="display: none;">
                                        <img id="preview-image" src="" alt="Prescription Preview" style="max-width: 100%; height: auto; border-radius: 8px; border: 2px solid #ddd;">
                                    </div>
                                </div>
                                <div class="form-actions" style="margin-top:12px; display:flex; gap:10px;">
                                    <button type="button" class="btn btn-secondary" onclick="showDashboard()"><i class="fas fa-times"></i> Cancel</button>
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Submit Request</button>
                                </div>

                            
                        </form>
                        </div>
                    </div>
                    <div class="content-box" style="margin-top:16px;">
                        <h2 class="content-title" style="font-size:18px;">My Recent Requests</h2>
                        <div id="my-breastmilk-requests" style="padding:10px 0;">Loading...</div>
                    </div>
                </div>
            `;

            // After injecting the form, populate and lock recipient fields from infant/user profile
            (async function fillAndLockRecipient() {
                try {
                    const [infantRes, profileRes] = await Promise.all([
                        fetch('/infant-information/current').then(r => r.json()).catch(() => null),
                        fetch('/profile/current').then(r => r.json()).catch(() => null)
                    ]);

                    const byId = (id) => document.getElementById(id);
                    const nameEl = byId('bm_recipient_name');
                    const dobEl = byId('bm_recipient_dob');
                    const weightEl = byId('bm_recipient_weight');
                    const contactEl = byId('bm_contact_number');
                    const noteEl = byId('bm_infant_info_note');

                    // If infant exists, use infant data; otherwise, fallback to profile for contact only
                    if (infantRes && infantRes.success && infantRes.hasInfant) {
                        if (nameEl) { nameEl.value = infantRes.infant.full_name || ''; nameEl.readOnly = true; nameEl.classList.add('readonly'); }
                        if (dobEl) { dobEl.value = (infantRes.infant.date_of_birth || '').substring(0, 10); dobEl.readOnly = true; dobEl.classList.add('readonly'); }
                        if (weightEl) { weightEl.value = infantRes.infant.birthweight ?? ''; weightEl.readOnly = true; weightEl.classList.add('readonly'); }
                        if (noteEl) { noteEl.style.display = 'block'; }
                    }

                    if (profileRes && profileRes.success && profileRes.user) {
                        if (contactEl) { contactEl.value = profileRes.user.contact_number || ''; contactEl.readOnly = true; contactEl.classList.add('readonly'); }
                    }
                } catch (e) {
                    // Non-fatal; user can still type if fetch fails
                }
            })();

            // Add event listeners for file upload
            setupFileUpload();
            setupFormSubmission();
            initBmInlineSchedule();
            loadBreastmilkRequests();
            // Initialize Breastmilk Request tabs
            (function initBmTabs() {
                const tabs = document.querySelectorAll('.bm-tab');
                const panels = document.querySelectorAll('.bm-tab-panel');
                if (!tabs.length) return;
                function activate(tab) {
                    tabs.forEach(t => { t.classList.remove('active'); t.setAttribute('aria-selected', 'false'); });
                    panels.forEach(p => p.classList.remove('active'));
                    tab.classList.add('active');
                    tab.setAttribute('aria-selected', 'true');
                    const panel = document.getElementById(tab.getAttribute('aria-controls'));
                    if (panel) panel.classList.add('active');
                }
                tabs.forEach(t => {
                    t.addEventListener('click', () => activate(t));
                    t.addEventListener('keydown', (e) => {
                        if (['ArrowRight', 'ArrowLeft'].includes(e.key)) {
                            e.preventDefault();
                            const list = [...tabs];
                            const idx = list.indexOf(t);
                            const nextIdx = e.key === 'ArrowRight' ? (idx + 1) % list.length : (idx - 1 + list.length) % list.length;
                            list[nextIdx].focus(); activate(list[nextIdx]);
                        }
                    });
                });
            })();
            updateActiveNav('request');
        }

        function showSettings(e) {
            // If invoked via click, prevent full page load and manage SPA state
            if (e && e.preventDefault) e.preventDefault();
            // Push canonical settings URL only if not already there
            if (window.location.pathname !== '/dashboard/settings') {
                history.pushState({ view: 'settings' }, '', '/dashboard/settings');
            } else {
                // Replace state to ensure we mark it as settings view for our logic
                history.replaceState({ view: 'settings' }, '', '/dashboard/settings');
            }
            document.getElementById('pageTitle').textContent = 'ACCOUNT SETTINGS';
            toggleFacebookWidget(false);
            document.getElementById('dashboard-view').innerHTML = `
                <style>
                    .settings-grid { display:grid; gap:26px; max-width:760px; }
                    @media (min-width:720px){ .settings-grid { grid-template-columns: 1fr; } }
                    .settings-card { background:#fff; border:1px solid #f0d6e2; border-radius:14px; padding:26px 28px 30px; box-shadow:0 4px 14px rgba(255,105,180,0.12); position:relative; overflow:hidden; }
                    .settings-card:before { content:""; position:absolute; inset:0; background:linear-gradient(135deg,rgba(255,105,180,0.08),rgba(255,182,206,0.08)); pointer-events:none; }
                    .settings-heading { margin:0 0 6px; font-size:20px; letter-spacing:.5px; }
                    .settings-sub { margin:0 0 22px; font-size:13px; color:#555; line-height:1.5; }
                    .field-row { display:flex; flex-direction:column; gap:8px; position:relative; }
                    .pw-wrapper { position:relative; }
                    .pw-toggle { position:absolute; top:50%; right:10px; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:#888; padding:4px; }
                    .pw-toggle:focus-visible { outline:2px solid #ff69b4; border-radius:4px; }
                    .inline-error { font-size:12px; color:#c92a2a; font-weight:600; display:none; }
                    .strength-bar { height:8px; background:#eee; border-radius:6px; overflow:hidden; position:relative; }
                    .strength-bar span { position:absolute; inset:0; width:0%; background:#ff6fae; transition:width .3s, background .3s; }
                    .strength-label { font-size:11px; font-weight:600; letter-spacing:.5px; color:#666; margin-top:6px; text-transform:uppercase; }
                    .status-inline { font-size:13px; font-weight:600; min-height:20px; }
                    .status-inline.success { color:#1b7f32; }
                    .status-inline.error { color:#c92a2a; }
                    .divider-soft { height:1px; background:linear-gradient(90deg,#ffe1ef,#ffd2e8,#ffe1ef); margin:28px 0; border:none; }
                    .accent-badge { display:inline-block; background:#ffe8f2; color:#8a2d55; padding:4px 10px; font-size:11px; font-weight:700; border-radius:999px; letter-spacing:.5px; margin-left:8px; }
                </style>
                <div class="settings-grid">
                  <div class="settings-card" aria-labelledby="pw-heading">
                    <h2 id="pw-heading" class="settings-heading">Change Password <span class="accent-badge">SECURITY</span></h2>
                    <p class="settings-sub">Update your account password. Choose a strong, unique password you are not using elsewhere.</p>
                    <form id="password-update-form" novalidate>
                        <div class="field-row">
                            <label for="current_password">Current Password</label>
                            <div class="pw-wrapper">
                                <input type="password" id="current_password" name="current_password" required autocomplete="current-password" class="modern-input" />
                                <button type="button" class="pw-toggle" data-target="current_password" aria-label="Toggle current password visibility"><i class="fas fa-eye"></i></button>
                            </div>
                            <div class="inline-error" id="err-current_password"></div>
                        </div>
                        <div class="field-row">
                            <label for="new_password">New Password</label>
                            <div class="pw-wrapper">
                                <input type="password" id="new_password" name="new_password" minlength="8" required autocomplete="new-password" class="modern-input" />
                                <button type="button" class="pw-toggle" data-target="new_password" aria-label="Toggle new password visibility"><i class="fas fa-eye"></i></button>
                            </div>
                            <div class="strength-bar" aria-hidden="true"><span id="pw-strength-fill"></span></div>
                            <div class="strength-label" id="pw-strength-label">Strength: —</div>
                            <small style="color:#666;">Minimum 8 characters. Add numbers & symbols for stronger security.</small>
                            <div class="inline-error" id="err-new_password"></div>
                        </div>
                        <div class="field-row">
                            <label for="new_password_confirmation">Confirm New Password</label>
                            <div class="pw-wrapper">
                                <input type="password" id="new_password_confirmation" name="new_password_confirmation" required autocomplete="new-password" class="modern-input" />
                                <button type="button" class="pw-toggle" data-target="new_password_confirmation" aria-label="Toggle confirm password visibility"><i class="fas fa-eye"></i></button>
                            </div>
                            <div class="inline-error" id="err-new_password_confirmation"></div>
                        </div>
                        <hr class="divider-soft" />
                        <div style="display:flex; gap:14px; align-items:center; flex-wrap:wrap;">
                            <button type="submit" class="submit-btn" style="padding:12px 30px; min-width:170px;">Update Password</button>
                            <div id="pwd-status" class="status-inline" role="status" aria-live="polite"></div>
                        </div>
                    </form>
                  </div>
                </div>`;
            updateActiveNav('settings');
            const form = document.getElementById('password-update-form');
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const statusEl = document.getElementById('pwd-status');
                statusEl.classList.remove('error', 'success');
                statusEl.style.color = '#555';
                statusEl.textContent = 'Updating...';
                const fd = new FormData(form);
                try {
                    const res = await fetch('{{ route('user.settings.password') }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: fd
                    });
                    const ct = res.headers.get('Content-Type') || '';
                    if (res.ok && ct.includes('text/html')) {
                        statusEl.classList.add('success');
                        statusEl.style.color = '#1b7f32';
                        statusEl.textContent = 'Password updated successfully. Please use the new password next login.';
                        form.reset();
                        return;
                    }
                    if (ct.includes('application/json')) {
                        const data = await res.json();
                        if (data.success) {
                            statusEl.classList.add('success');
                            statusEl.style.color = '#1b7f32';
                            statusEl.textContent = data.message || 'Password updated successfully.';
                            form.reset();
                        } else { throw new Error(data.message || 'Update failed.'); }
                    } else {
                        if (!res.ok) throw new Error('Failed to update password.');
                        statusEl.classList.add('success');
                        statusEl.style.color = '#1b7f32';
                        statusEl.textContent = 'Password updated (fallback).';
                        form.reset();
                    }
                } catch (err) {
                    statusEl.classList.add('error');
                    statusEl.style.color = '#c92a2a';
                    statusEl.textContent = err.message || 'Error updating password.';
                }
            });
            document.querySelectorAll('.pw-toggle').forEach(btn => {
                btn.addEventListener('click', () => {
                    const targetId = btn.getAttribute('data-target');
                    const input = document.getElementById(targetId);
                    if (!input) return;
                    if (input.type === 'password') { input.type = 'text'; btn.innerHTML = '<i class="fas fa-eye-slash"></i>'; }
                    else { input.type = 'password'; btn.innerHTML = '<i class="fas fa-eye"></i>'; }
                });
            });
            const np = document.getElementById('new_password');
            const fill = document.getElementById('pw-strength-fill');
            const label = document.getElementById('pw-strength-label');
            const calcStrength = (val) => { let score = 0; if (!val) return 0; if (val.length >= 8) score++; if (/[A-Z]/.test(val)) score++; if (/[0-9]/.test(val)) score++; if (/[^A-Za-z0-9]/.test(val)) score++; if (val.length >= 12) score++; return Math.min(score, 5); };
            const strengthLabels = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong', 'Excellent'];
            np.addEventListener('input', () => { const s = calcStrength(np.value); const pct = (s / 5) * 100; fill.style.width = pct + '%'; let color = '#ff6fae'; if (s >= 2) color = '#ff9ac9'; if (s >= 3) color = '#ffb6ce'; if (s >= 4) color = '#8ddc8c'; if (s >= 5) color = '#28a745'; fill.style.background = color; label.textContent = 'Strength: ' + strengthLabels[s]; });
            const npc = document.getElementById('new_password_confirmation');
            const errConfirm = document.getElementById('err-new_password_confirmation');
            function checkMatch() { if (npc.value && np.value && npc.value !== np.value) { errConfirm.style.display = 'block'; errConfirm.textContent = 'Passwords do not match'; } else { errConfirm.style.display = 'none'; errConfirm.textContent = ''; } }
            np.addEventListener('input', checkMatch); npc.addEventListener('input', checkMatch);
        }

        // Handle browser back/forward navigation for settings route
        window.addEventListener('popstate', function () {
            if (window.location.pathname === '/dashboard/settings') {
                // Re-render settings if navigating back to it
                showSettings();
            } else if (window.location.pathname === '/dashboard') {
                // Basic fallback to dashboard when returning
                showDashboard();
            }
        });

        // If page loaded while already at /dashboard/settings inside dashboard context, render settings view
        if (window.location && window.location.pathname === '/dashboard/settings') {
            // Delay to ensure DOM elements like #dashboard-view exist
            setTimeout(() => { try { showSettings(); } catch (_) { } }, 50);
        }

        // Auto-open settings when controller passed flag (server-rendered variable)
        @if(!empty($autoShowSettings))
            setTimeout(() => { try { showSettings(); } catch (e) { console.warn('Auto show settings failed', e); } }, 30);
        @endif

            // Field validation function
            function validateField(field) {
                if (field.hasAttribute('required')) {
                    if (!field.value.trim()) {
                        field.classList.remove('success');
                        field.classList.add('error');
                    } else {
                        field.classList.remove('error');
                        field.classList.add('success');
                    }
                }
            }

        // Name field validation function
        function validateNameField(field) {
            // Remove special characters except letters, spaces, hyphens, and apostrophes
            const sanitizedValue = field.value.replace(/[<>='"]/g, '');
            if (field.value !== sanitizedValue) {
                field.value = sanitizedValue;
                showInlineNotification('warning', '', 'Special characters like <, >, =, \', " are not allowed in names.', 3000);
            }

            // Validate the field
            validateField(field);
        }

        // Address validation function
        function validateAddress(input) {
            const address = input.value;
            const alertId = input.id + '_alert';
            const alertEl = document.getElementById(alertId);

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

        // Enhanced Home Collection Form Validation
        function validateHomeCollectionField(field) {
            const fieldGroup = field.closest('.form-group');
            let isValid = true;
            let errorMessage = '';

            // Remove existing error states
            field.classList.remove('error');
            const existingError = fieldGroup.querySelector('.error-message');
            if (existingError) {
                existingError.remove();
            }

            // Required field validation
            if (field.hasAttribute('required') && !field.value.trim()) {
                isValid = false;
                errorMessage = 'This field is required.';
            }

            // Specific field validations
            switch (field.id) {
                case 'number_of_bags':
                    const bags = parseInt(field.value);
                    if (field.value && (bags < 1 || bags > 50)) {
                        isValid = false;
                        errorMessage = 'Number of bags must be between 1 and 50.';
                    }
                    break;

                case 'total_volume':
                    const volume = parseInt(field.value);
                    if (field.value && (volume < 1 || volume > 5000)) {
                        isValid = false;
                        errorMessage = 'Total volume must be between 1 and 5000 ml.';
                    }
                    break;

                case 'homeCollectionDateModal':
                    if (field.value) {
                        const selectedDate = new Date(field.value);
                        const today = new Date();
                        today.setHours(23, 59, 59, 999); // End of today

                        if (selectedDate > today) {
                            isValid = false;
                            errorMessage = 'Collection date cannot be in the future.';
                        }

                        // Check if date is too old (e.g., more than 7 days ago)
                        const weekAgo = new Date();
                        weekAgo.setDate(weekAgo.getDate() - 7);
                        weekAgo.setHours(0, 0, 0, 0);

                        if (selectedDate < weekAgo) {
                            isValid = false;
                            errorMessage = 'Collection date cannot be more than 7 days ago.';
                        }
                    }
                    break;

                case 'pickup_address':
                    if (field.value) {
                        if (field.value.length > 500) {
                            isValid = false;
                            errorMessage = 'Address must not exceed 500 characters.';
                        } else if (!/^[a-zA-Z0-9\s,.\-#]*$/.test(field.value)) {
                            isValid = false;
                            errorMessage = 'Address can only contain letters, numbers, spaces, and symbols (,.-#).';
                        } else if (field.value.length < 10) {
                            isValid = false;
                            errorMessage = 'Please provide a complete address with at least 10 characters.';
                        }
                    }
                    break;
            }

            // Apply validation result
            if (!isValid) {
                field.classList.add('error');
                if (errorMessage) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'error-message';
                    errorDiv.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${errorMessage}`;
                    fieldGroup.appendChild(errorDiv);
                }
            }

            return isValid;
        }

        function validateHomeCollectionForm() {
            const form = document.getElementById('homeCollectionFormModal');
            if (!form) return false;

            const fields = form.querySelectorAll('.form-input[required], .form-input[data-validate]');
            let isValid = true;

            fields.forEach(field => {
                if (!validateHomeCollectionField(field)) {
                    isValid = false;
                }
            });

            // Cross-field validation
            const bags = parseInt(document.getElementById('number_of_bags').value);
            const volume = parseInt(document.getElementById('total_volume').value);

            // Check if volume is reasonable for number of bags (rough estimate: 50-200ml per bag)
            if (bags && volume) {
                const avgPerBag = volume / bags;
                if (avgPerBag < 10 || avgPerBag > 500) {
                    const volumeField = document.getElementById('total_volume');
                    volumeField.classList.add('error');

                    const fieldGroup = volumeField.closest('.form-group');
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'error-message';
                    errorDiv.innerHTML = `<i class="fas fa-exclamation-triangle"></i> Volume seems unusual for ${bags} bags. Expected range: ${bags * 10}-${bags * 500}ml.`;
                    fieldGroup.appendChild(errorDiv);

                    isValid = false;
                }
            }

            return isValid;
        }

        // Setup file upload functionality
        function setupFileUpload() {
            const fileInput = document.getElementById('prescription-upload');
            const fileName = document.getElementById('file-name');
            const previewContainer = document.getElementById('prescription-preview');
            const previewImage = document.getElementById('preview-image');

            if (fileInput) {
                fileInput.addEventListener('change', function (e) {
                    const file = e.target.files[0];
                    if (file) {
                        // Update file name display
                        fileName.textContent = file.name;

                        // Validate file size (5MB max)
                        if (file.size > 5 * 1024 * 1024) {
                            showInlineNotification('error', '', 'File size must be less than 5MB', 5000);
                            fileInput.value = '';
                            fileName.textContent = 'No file selected';
                            previewContainer.style.display = 'none';
                            fileInput.classList.remove('success');
                            fileInput.classList.add('error');
                            return;
                        }

                        // Mark file input as valid
                        fileInput.classList.remove('error');
                        fileInput.classList.add('success');

                        // Show preview for images
                        if (file.type.startsWith('image/')) {
                            const reader = new FileReader();
                            reader.onload = function (e) {
                                previewImage.src = e.target.result;
                                previewContainer.style.display = 'block';
                            };
                            reader.readAsDataURL(file);
                        } else {
                            previewContainer.style.display = 'none';
                        }
                    } else {
                        fileName.textContent = 'No file selected';
                        previewContainer.style.display = 'none';
                    }
                });
            }
        }

        // Setup form submission handlers
        function setupFormSubmission() {
            console.log('Setting up form submission handlers...');

            // Breastmilk request form
            const breastmilkForm = document.getElementById('breastmilk-request-form');
            if (breastmilkForm) {
                console.log('Breastmilk form found and handler attached');
                breastmilkForm.addEventListener('submit', function (e) {
                    // If already confirmed, allow native submit to proceed
                    if (breastmilkForm.dataset.confirmed === 'true') {
                        breastmilkForm.dataset.confirmed = '';
                        return;
                    }
                    e.preventDefault();

                    // Validate required fields
                    const requiredFields = breastmilkForm.querySelectorAll('[required]');
                    let isValid = true;

                    requiredFields.forEach(field => {
                        if (!field.value.trim()) {
                            isValid = false;
                            field.classList.remove('success');
                            field.classList.add('error');
                        } else {
                            field.classList.remove('error');
                            field.classList.add('success');
                        }
                    });

                    if (!isValid) {
                        showInlineNotification('error', '', 'Please fill in all required fields.', 5000);
                        return;
                    }

                    // SweetAlert2 confirmation dialog (native submit on confirm)
                    if (window.Swal) {
                        Swal.fire({
                            title: 'Confirm Breastmilk Request Submission',
                            text: 'Are you sure you want to submit this breastmilk request? This action cannot be undone.',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Confirm',
                            cancelButtonText: 'Cancel',
                            reverseButtons: true,
                            backdrop: 'rgba(0,0,0,0.45)' // match logout dim background
                        }).then(function (result) {
                            if (!result.isConfirmed) return;
                            const submitBtn = breastmilkForm.querySelector('button[type="submit"]');
                            if (submitBtn) { submitBtn.textContent = 'Submitting...'; submitBtn.disabled = true; }
                            breastmilkForm.dataset.confirmed = 'true';
                            breastmilkForm.submit();
                        });
                    } else {
                        // Fallback to native confirm
                        if (!confirm('Are you sure you want to submit this breastmilk request? This action cannot be undone.')) return;
                        const submitBtn = breastmilkForm.querySelector('button[type="submit"]');
                        if (submitBtn) { submitBtn.textContent = 'Submitting...'; submitBtn.disabled = true; }
                        breastmilkForm.dataset.confirmed = 'true';
                        breastmilkForm.submit();
                    }
                });
            } else {
                console.log('Breastmilk request form not found');
            }

            // Walk-in form submission
            const walkInForm = document.getElementById('walkInFormModal');
            if (walkInForm) {
                console.log('Walk-in form found and handler attached');
                walkInForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    console.log('Walk-in form submitted');

                    // Validate required fields before confirmation
                    const donationDate = document.getElementById('walkInDateModal')?.value;
                    const donationTime = document.getElementById('walkInTimeModal')?.value;
                    console.log('Walk-in form data (pre-confirm):', { donationDate, donationTime });

                    if (!donationDate || !donationTime) {
                        if (typeof showInlineNotification === 'function') {
                            showInlineNotification('error', '', 'Please select a date and time for your appointment.', 5000);
                        } else {
                            // fallback
                            alert('Please select a date and time for your appointment.');
                        }
                        return;
                    }

                    // Show SweetAlert confirmation dialog for walk-in appointment
                    Swal.fire({
                        title: 'Confirm Walk-in Appointment',
                        text: 'Are you sure you want to book this walk-in appointment? This action cannot be undone.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Book Appointment',
                        cancelButtonText: 'Cancel',
                        reverseButtons: false, // Ensure Cancel is on left, Confirm on right
                        focusConfirm: true,
                        customClass: {
                            popup: 'user-swal'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loading state
                            const submitBtn = walkInForm.querySelector('button[type="submit"]');
                            if (submitBtn) {
                                submitBtn.textContent = 'Booking...';
                                submitBtn.disabled = true;
                            }

                            // Read current values from hidden inputs populated by the picker
                            const currentDate = document.getElementById('walkInDateModal')?.value;
                            const currentTime = document.getElementById('walkInTimeModal')?.value;

                            // Revalidate availability just before final submit to avoid race conditions
                            fetch(`/donation/available-slots?date=${encodeURIComponent(currentDate)}`)
                                .then(r => r.json())
                                .then(data => {
                                    const stillAvailable = data && data.success && Array.isArray(data.slots) && data.slots.includes(currentTime);
                                    if (!stillAvailable) {
                                        // Restore button state
                                        if (submitBtn) {
                                            submitBtn.textContent = 'Book Appointment';
                                            submitBtn.disabled = false;
                                        }
                                        showInlineNotification('warning', '', 'Sorry, this time slot was just taken. Please pick another slot.', 5000);
                                        // Keep modal open for reselection
                                        return;
                                    }

                                    // Create and submit a temporary form with necessary payload
                                    const tempForm = document.createElement('form');
                                    tempForm.method = 'POST';
                                    tempForm.action = walkInForm.action;
                                    tempForm.style.display = 'none';

                                    // CSRF token
                                    const csrfToken = document.createElement('input');
                                    csrfToken.type = 'hidden';
                                    csrfToken.name = '_token';
                                    csrfToken.value = '{{ csrf_token() }}';
                                    tempForm.appendChild(csrfToken);

                                    // Append form data
                                    const dateInput = document.createElement('input');
                                    dateInput.type = 'hidden';
                                    dateInput.name = 'donation_date';
                                    dateInput.value = currentDate;
                                    tempForm.appendChild(dateInput);

                                    const timeInput = document.createElement('input');
                                    timeInput.type = 'hidden';
                                    timeInput.name = 'donation_time';
                                    timeInput.value = currentTime;
                                    tempForm.appendChild(timeInput);

                                    console.log('Submitting walk-in form to:', walkInForm.action);
                                    console.log('Form data:', { donation_date: currentDate, donation_time: currentTime });

                                    document.body.appendChild(tempForm);
                                    tempForm.submit();
                                })
                                .catch(() => {
                                    if (submitBtn) {
                                        submitBtn.textContent = 'Book Appointment';
                                        submitBtn.disabled = false;
                                    }
                                    showInlineNotification('error', '', 'Could not validate availability. Please try again.', 5000);
                                });
                        }
                    });
                });
            } else {
                console.log('Walk-in form not found');
            }

            // Home collection form submission
            const homeCollectionForm = document.getElementById('homeCollectionFormModal');
            if (homeCollectionForm) {
                console.log('Home collection form found and handler attached');

                // Add real-time validation to form inputs
                const formInputs = homeCollectionForm.querySelectorAll('.form-input');
                formInputs.forEach(input => {
                    // Add focus/blur effects
                    input.addEventListener('focus', function () {
                        this.parentElement.classList.add('focused');
                    });

                    input.addEventListener('blur', function () {
                        this.parentElement.classList.remove('focused');
                        validateHomeCollectionField(this);
                    });

                    input.addEventListener('input', function () {
                        if (this.classList.contains('error')) {
                            validateHomeCollectionField(this);
                        }
                    });
                });

                homeCollectionForm.addEventListener('submit', function (e) {
                    // If already confirmed, allow native submit to proceed
                    if (homeCollectionForm.dataset.confirmed === 'true') {
                        homeCollectionForm.dataset.confirmed = '';
                        return;
                    }
                    e.preventDefault();
                    console.log('Home collection form submitted');

                    // Enhanced form validation
                    const isValid = validateHomeCollectionForm();

                    if (!isValid) {
                        // Scroll to first error field
                        const firstError = homeCollectionForm.querySelector('.form-input.error');
                        if (firstError) {
                            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            firstError.focus();
                        }

                        if (typeof showInlineNotification === 'function') {
                            showInlineNotification('error', 'Validation Error', 'Please fix the highlighted fields and try again.', 5000);
                        }
                        return;
                    }

                    // Show enhanced loading state
                    const submitBtn = homeCollectionForm.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.classList.add('loading');
                        submitBtn.disabled = true;
                    }

                    // SweetAlert2 confirmation dialog
                    if (window.Swal) {
                        Swal.fire({
                            title: 'Confirm Home Collection Request',
                            html: `
                                <div style="text-align: left; padding: 10px 0;">
                                    <p style="margin-bottom: 15px; color: #666;">Please review your request details:</p>
                                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 10px 0;">
                                        <p><strong>Bags:</strong> ${document.getElementById('number_of_bags').value}</p>
                                        <p><strong>Volume:</strong> ${document.getElementById('total_volume').value} ml</p>
                                        <p><strong>Date Collected:</strong> ${new Date(document.getElementById('homeCollectionDateModal').value).toLocaleDateString()}</p>
                                    </div>
                                    <p style="color: #dc3545; font-size: 14px; margin-top: 15px;">
                                        <i class="fas fa-exclamation-triangle"></i> 
                                        This action cannot be undone.
                                    </p>
                                </div>
                            `,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: '<i class="fas fa-paper-plane"></i> Submit Request',
                            cancelButtonText: '<i class="fas fa-times"></i> Cancel',
                            reverseButtons: false,
                            focusConfirm: true,
                            customClass: {
                                popup: 'user-swal',
                            },
                            didOpen: () => {
                                // Reset button state if user cancels
                                if (submitBtn) {
                                    submitBtn.classList.remove('loading');
                                    submitBtn.disabled = false;
                                }
                            }
                        }).then(function (result) {
                            if (result.isConfirmed) {
                                // Show final loading state
                                if (submitBtn) {
                                    submitBtn.classList.add('loading');
                                    submitBtn.disabled = true;
                                }
                                // Submit original form (includes @csrf hidden field)
                                homeCollectionForm.dataset.confirmed = 'true';
                                homeCollectionForm.submit();
                            } else {
                                // Reset loading state if cancelled
                                if (submitBtn) {
                                    submitBtn.classList.remove('loading');
                                    submitBtn.disabled = false;
                                }
                            }
                        });
                    } else {
                        // Fallback to native confirm if SweetAlert is unavailable
                        if (confirm('Are you sure you want to submit this home collection request? This action cannot be undone.')) {
                            homeCollectionForm.dataset.confirmed = 'true';
                            homeCollectionForm.submit();
                        } else {
                            if (submitBtn) {
                                submitBtn.classList.remove('loading');
                                submitBtn.disabled = false;
                            }
                        }
                    }
                });
            } else {
                console.log('Home collection form not found');
            }

            console.log('Form submission setup completed');
        }

        // Breastmilk appointment picker (inline vanilla calendar like walk-in)
        function initBmInlineSchedule() {
            const container = document.getElementById('bmInlineSchedule');
            if (!container) return;
            const daysContainer = container.querySelector('.vc-days');
            const prevBtn = container.querySelector('.vc-prev');
            const nextBtn = container.querySelector('.vc-next');
            const titleEl = container.querySelector('.vc-title');
            const slotsGrid = container.querySelector('.time-slots-grid');
            const selectedDateInfo = container.querySelector('.selected-date-info');
            const selectedDateDisplay = container.querySelector('.selected-date');

            let current = new Date();
            const avail = {};
            const fmtYmd = (d) => `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;

            async function fetchAvailability() {
                const month = String(current.getMonth() + 1).padStart(2, '0');
                const year = String(current.getFullYear());
                const r = await fetch(`/donation/availability?month=${month}&year=${year}`);
                const j = await r.json();
                (j.availability || []).forEach(a => { avail[a.date] = true; });
            }

            async function render() {
                titleEl.textContent = current.toLocaleString('en-US', { month: 'long', year: 'numeric' });
                daysContainer.innerHTML = '';
                slotsGrid.innerHTML = '';
                const firstDay = new Date(current.getFullYear(), current.getMonth(), 1);
                const startWeekday = firstDay.getDay();
                const daysInMonth = new Date(current.getFullYear(), current.getMonth() + 1, 0).getDate();

                if (Object.keys(avail).length === 0) { await fetchAvailability(); }

                for (let i = 0; i < startWeekday; i++) {
                    const blank = document.createElement('div');
                    blank.style.minHeight = '42px';
                    daysContainer.appendChild(blank);
                }
                for (let d = 1; d <= daysInMonth; d++) {
                    const cell = document.createElement('button');
                    cell.type = 'button';
                    const dateObj = new Date(current.getFullYear(), current.getMonth(), d);
                    const dateStr = fmtYmd(dateObj);
                    const isAvail = !!avail[dateStr];
                    cell.className = 'vc-day' + (isAvail ? ' available' : '');
                    cell.textContent = String(d);
                    cell.style.cssText = 'min-height:42px; border:1px solid #eee; border-radius:10px; background:' + (isAvail ? '#ffd1e6' : '#f3f4f6') + '; color:' + (isAvail ? '#7a2944' : '#999') + ';';
                    if (isAvail) {
                        const dot = document.createElement('div');
                        dot.style.cssText = 'width:6px;height:6px;background:#ff69b4;border-radius:999px;position:absolute;bottom:6px;right:8px;';
                        cell.style.position = 'relative';
                        cell.appendChild(dot);
                    }
                    if (isAvail) {
                        cell.addEventListener('click', () => {
                            daysContainer.querySelectorAll('.vc-day.available').forEach(el => {
                                el.classList.remove('selected');
                                el.style.outline = '';
                            });
                            cell.classList.add('selected');
                            cell.style.outline = '2px solid #ff69b4';
                            selectedDateInfo.style.display = 'block';
                            selectedDateDisplay.textContent = new Date(dateStr).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
                            // Load slots for this date
                            slotsGrid.innerHTML = '<p style="text-align:center;color:#666;">Loading available time slots...</p>';
                            fetch(`/donation/available-slots?date=${encodeURIComponent(dateStr)}`)
                                .then(r => r.json())
                                .then(data => {
                                    slotsGrid.innerHTML = '';
                                    if (data && data.success) {
                                        const all = Array.isArray(data.all) ? data.all : (Array.isArray(data.slots) ? data.slots : []);
                                        const unavailable = new Set(Array.isArray(data.unavailable) ? data.unavailable : []);
                                        if (!all.length) {
                                            slotsGrid.innerHTML = '<div style="text-align:center;color:#666;padding:12px;">No time slots available for this date.</div>';
                                            return;
                                        }
                                        const group = document.createElement('div');
                                        group.style.cssText = 'display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:10px;';
                                        all.forEach(slot => {
                                            const b = document.createElement('button');
                                            b.type = 'button';
                                            b.className = 'time-slot-btn';
                                            const timeDisplay = new Date(`1970-01-01T${slot}`).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
                                            const isUnavailable = unavailable.has(slot);
                                            b.innerHTML = `<div style=\"font-weight:600;\">${timeDisplay}</div><div style=\"font-size:12px;color:${isUnavailable ? '#a00' : '#666'};\">${isUnavailable ? 'Unavailable' : '1 hour slot'}</div>`;
                                            if (!isUnavailable) {
                                                b.onclick = () => {
                                                    // Mark selection
                                                    container.querySelectorAll('.time-slot-btn').forEach(x => x.classList.remove('selected'));
                                                    b.classList.add('selected');
                                                    document.getElementById('bmScheduledDate').value = dateStr;
                                                    document.getElementById('bmScheduledTime').value = slot;
                                                    document.getElementById('bmSelectedAppt').textContent = `${selectedDateDisplay.textContent} • ${timeDisplay}`;
                                                    // Hide other time options
                                                    const others = group.querySelectorAll('.time-slot-btn:not(.selected)');
                                                    others.forEach(o => o.style.display = 'none');
                                                    // Add change link if not present
                                                    if (!group.querySelector('.change-time-slot')) {
                                                        const change = document.createElement('button');
                                                        change.type = 'button';
                                                        change.textContent = 'Change time';
                                                        change.className = 'change-time-slot';
                                                        change.style.cssText = 'grid-column:1/-1;justify-self:start;background:none;border:none;color:#ff69b4;cursor:pointer;font-size:13px;padding:4px 2px;text-decoration:underline;';
                                                        change.addEventListener('click', () => {
                                                            // Reveal hidden buttons
                                                            group.querySelectorAll('.time-slot-btn').forEach(btn => { btn.style.display = ''; });
                                                            // Remove selected state to allow new selection
                                                            b.classList.remove('selected');
                                                            change.remove();
                                                            document.getElementById('bmScheduledTime').value = '';
                                                            document.getElementById('bmSelectedAppt').textContent = selectedDateDisplay.textContent;
                                                        });
                                                        group.appendChild(change);
                                                    }
                                                };
                                            } else {
                                                b.classList.add('disabled');
                                                b.setAttribute('disabled', 'disabled');
                                            }
                                            group.appendChild(b);
                                        });
                                        slotsGrid.appendChild(group);
                                    } else {
                                        slotsGrid.innerHTML = '<div style="text-align:center;color:#666;padding:12px;">No time slots available for this date.</div>';
                                    }
                                })
                                .catch(() => { slotsGrid.innerHTML = '<div style="color:#a00; text-align:center; padding:12px;">Error loading slots.</div>'; });
                        });
                    }
                    daysContainer.appendChild(cell);
                }
            }

            prevBtn.onclick = async () => { current.setMonth(current.getMonth() - 1); await render(); };
            nextBtn.onclick = async () => { current.setMonth(current.getMonth() + 1); await render(); };
            render();
        }

        // Load user's recent breastmilk requests
        function loadBreastmilkRequests() {
            const container = document.getElementById('my-breastmilk-requests');
            if (!container) return;
            container.textContent = 'Loading...';
            fetch('{{ route("breastmilk-request.history") }}')
                .then(r => r.json())
                .then(data => {
                    if (!data.success) {
                        container.textContent = data.message || 'Failed to load requests.';
                        return;
                    }
                    const list = Array.isArray(data.data) ? data.data : [];
                    if (list.length === 0) {
                        container.innerHTML = '<div style="color:#666;">No requests yet.</div>';
                        return;
                    }
                    const rows = list.slice(0, 5).map(item => {
                        const created = new Date(item.created_at).toLocaleString();
                        const apptDate = item.scheduled_date ? new Date(item.scheduled_date).toLocaleDateString() : '—';
                        const apptTime = item.scheduled_time && item.scheduled_time !== '00:00:00'
                            ? new Date(`2000-01-01T${item.scheduled_time}`).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true })
                            : '—';
                        return `
                            <div style="display:flex;align-items:center;justify-content:space-between;border:1px solid #eee;border-radius:8px;padding:10px;margin-bottom:8px;">
                                <div>
                                    <div style="font-weight:600;">${item.recipient_name}</div>
                                    <div style="font-size:12px;color:#666;">Appointment: ${apptDate} ${apptTime !== '—' ? '• ' + apptTime : ''}</div>
                                </div>
                                <div style="text-align:right;">
                                    <div style="font-size:12px;color:#666;">${created}</div>
                                    <span class="pill ${item.status === 'approved' ? 'approved' : item.status === 'declined' ? 'declined' : 'pending'}">${item.status}</span>
                                </div>
                            </div>`;
                    }).join('');
                    container.innerHTML = rows;
                })
                .catch(() => {
                    container.textContent = 'Error loading requests.';
                });
        }

        function updateActiveNav(activeItem) {
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('active');
            });
            const selector = {
                'dashboard': 'DASHBOARD',
                'health': 'Health Screening',
                'donate': 'Donate',
                'pending': 'Pending Donations',
                'history': 'My Donation History',
                'request': 'BreastMilk Request',
                'settings': 'Settings'
            }[activeItem];

            if (selector) {
                const navItem = Array.from(document.querySelectorAll('.nav-text')).find(el => el.textContent === selector);
                if (navItem) {
                    navItem.closest('.nav-item').classList.add('active');
                }
            }
            if (activeItem === 'settings') {
                document.getElementById('nav-settings-link')?.classList.add('active');
            }
        }

        function logout() {
            // proactively close overlays and sidebar so nothing blocks clicks
            try { closeAllOverlays(); } catch (_) { }
            if (typeof Swal === 'undefined') {
                const ok = window.confirm('Logout?');
                if (!ok) return;
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("logout") }}';
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);
                document.body.appendChild(form);
                form.submit();
                return;
            }
            Swal.fire({
                title: 'Confirm Logout',
                text: 'You will be signed out of your account.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Logout',
                cancelButtonText: 'Cancel',
                backdrop: 'rgba(0,0,0,0.45)',
                focusConfirm: false,
                didOpen: (el) => {
                    // Add distinctive class for styling if needed
                    el.querySelector('.swal2-confirm')?.classList.add('swal2-confirm-logout');
                    // Move focus to cancel for safety (optional)
                    setTimeout(() => { el.querySelector('.swal2-cancel')?.focus(); }, 10);
                }
            }).then(function (result) {
                if (result.isConfirmed) {
                    try { closeAllOverlays(); } catch (_) { }
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("logout") }}';
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>

    <script>
        // --- Google Maps integration for Home Collection ---
        let __mapsLoaded = false;
        let __pickupMap, __pickupMarker, __geocoder, __autocomplete;
        let __autocompleteListenerAdded = false;
        let __markerDragListenerAdded = false;

        function loadGoogleMapsIfNeeded() {
            return new Promise((resolve, reject) => {
                if (__mapsLoaded || (window.google && window.google.maps)) {
                    __mapsLoaded = true;
                    return resolve();
                }
                const existing = document.getElementById('google-maps-js');
                if (existing) {
                    existing.addEventListener('load', () => { __mapsLoaded = true; resolve(); });
                    existing.addEventListener('error', reject);
                    return;
                }
                const key = @json(config('services.google.maps_api_key'));
                if (!key) {
                    console.warn('Google Maps API key missing. Set GOOGLE_MAPS_API_KEY in .env');
                    const mapEl = document.getElementById('pickup_map');
                    if (mapEl) {
                        mapEl.innerHTML = '<div style="padding:12px;border:1px solid #f5c2c7;background:#f8d7da;color:#842029;border-radius:8px;">Map unavailable: Missing Google Maps API key. Ask admin to set GOOGLE_MAPS_API_KEY in .env and run config:clear.</div>';
                    }
                    return reject(new Error('Missing Google Maps API key'));
                }
                const script = document.createElement('script');
                script.id = 'google-maps-js';
                script.src = `https://maps.googleapis.com/maps/api/js?key=${key}&libraries=places`;
                script.async = true;
                script.defer = true;
                script.onload = () => { __mapsLoaded = true; resolve(); };
                script.onerror = () => {
                    const mapEl = document.getElementById('pickup_map');
                    if (mapEl) {
                        mapEl.innerHTML = '<div style="padding:12px;border:1px solid #f5c2c7;background:#f8d7da;color:#842029;border-radius:8px;">Unable to load Google Maps. Check network or API key restrictions.</div>';
                    }
                    reject(new Error('Google Maps script load error'));
                };
                document.head.appendChild(script);
            });
        }

        // Initialize / enhance health screening form (question text rewrite + conditional follow-ups)
        function initHealthScreeningEnhancements() {
            try {
                const form = document.getElementById('healthScreeningForm');
                if (!form) return; // not on form yet
                if (form.dataset.enhanced === '1') return; // already processed

                const medicalQs = [
                    { name: 'mhq_1', en: 'Have you donated breastmilk before?', trans: 'Nakahatag/naka-donar ka na ba sa imung gatas kaniadto?' },
                    { name: 'mhq_2', en: 'Have you for any reason been deferred as a breastmilk donor?', trans: 'Naballbaran na ba ka nga mag-donar sa imung gatas kaniadto?' },
                    { name: 'mhq_3', en: 'Did you have a normal pregnancy and delivery for your most recent pregnancy?', trans: 'Wala ka bay naaging mnga kalisod og komplikasyon sa pinakaulahi nimung pagburos og pagpanganak?' },
                    { name: 'mhq_4', en: 'Do you have any acute or chronic infection such as but not limited to: tuberculosis, hepatitis, systemic disorders?', trans: 'Aduna ka bay gibating mga sakit sama sa Tuberculosis, sakit sa atay or sakit sa dugo?' },
                    { name: 'mhq_5', en: 'Have you been diagnosed with a chronic non-infectious illness such as but not limited to: diabetes, hypertension, heart disease?', trans: 'Nadayagnos ka ba nga adunay laygay nga dll makatakod nga sakit sama sa apas dill limitado sa: altapresyon, sakit sa kasingkasing' },
                    { name: 'mhq_6', en: 'Have you received any blood transfusion or any blood products within the last twelve (12) months?', trans: 'Naabunohan ka ba ug dugo sulod sa niaging 12 ka buwan?' },
                    { name: 'mhq_7', en: 'Have you received any organ or tissue transplant within the last twelve (12) months?', trans: 'Niagi ka ba ug operasyon din nidawat ka ug bahin/parte sa lawas sulod sa nlilabay nga 12 ka bulan?' },
                    { name: 'mhq_8', en: 'Have you had any intake of alcohol or hard liquor within the last twenty four (24) hours?', trans: 'Sulod sa 24 oras, naka inum ka ba og bisan unsang ilimnong makahubog?' },
                    { name: 'mhq_9', en: 'Do you use megadose vitamins or pharmacologically active herbal preparations?', trans: 'Gainum ka ba og sobra sa gitakda na mga bitamina og mga produktong adunay sagol na herbal?' },
                    { name: 'mhq_10', en: 'Do you regularly use over-the-counter medications or systemic preparations such as replacement hormones, birth control hormones, anti-hypoglycemics, blood thinners?', trans: 'Kanunay ba ka gagamit o gainum sa mga tambal kung lain ang paminaw sa lawas? Og gainum ka ba sa mha tambal pampugong sa pagburos?' },
                    { name: 'mhq_11', en: 'Are you a total vegetarian/vegan?', trans: 'Ikaw ba dili gakaon sa lain pagkaon kundi utan lang?' },
                    { name: 'mhq_12', en: 'Do you use illicit drugs?', trans: 'Gagamit ka ba sa ginadilina mga droga?' },
                    { name: 'mhq_13', en: 'Do you smoke?', trans: 'Gapanigarilyo ka ba?' },
                    { name: 'mhq_14', en: 'Are you around people who smoke (passive smoking)?', trans: 'doul ba ka permi sa mga tao nga gapanigarilyo?' },
                    { name: 'mhq_15', en: 'Have you had breast augmentation surgery, using silicone breast implants?', trans: 'kaw ba niagi ug operasyon sa imung suso din nagpabutang ug "silicone" O artipisyal na suso?' }
                ];
                const sexualQs = [
                    { name: 'shq_1', en: 'Have you ever had Syphilis, HIV, herpes or any sexually transmitted disease (STD)?', trans: 'Niagi ka ba og bisan unsang sakit sa kinatawo? ○ sakit na makuha pinaagi sa pakighilawas?' },
                    { name: 'shq_2', en: 'Do you have multiple sexual partners?', trans: 'aduna ka bay lain pares sa pakighilawas gawas sa imu bana/kapikas?' },
                    { name: 'shq_3', en: 'Have you had a sexual partner who is:', trans: 'Niagi ka ba og pakighilawas ning mga mosunod?' },
                    { name: 'shq_4', en: 'Have you had a tattoo applied or had an accidental needlestick injury or contact with someone else\'s blood?', trans: 'Niagi ka ba og papatik sukad? Niagi ka ba og katusok sa bisan unsang dagom?' }
                ];
                const infantQs = [
                    { name: 'diq_1', en: 'Is your child healthy?', trans: 'Himsog ba ang imung anak?' },
                    { name: 'diq_2', en: 'Was your child delivered full term?', trans: 'Gipanganak ba siya sa saktong buwan?' },
                    { name: 'diq_3', en: 'Are you exclusively breastfeeding your child?', trans: 'Kaugalingong gatas lang ba nimu ang gipalnum sa bata?' },
                    { name: 'diq_4', en: 'Is/was your youngest child jaundiced?', trans: 'imung kinamanghuran na bata ba niagi og pagdalag sa pamanit?' },
                    { name: 'diq_5', en: 'Have you ever received breastmilk from another mother?', trans: 'Nakadawat ba ang imung anak og gatas sa laing inahan?' }
                ];

                function rewrite(groups) {
                    groups.forEach(q => {
                        const input = form.querySelector(`input[name="${q.name}"]`);
                        if (!input) return;
                        const group = input.closest('.question-group');
                        if (!group) return;
                        const p = group.querySelector('p');
                        if (!p) return;
                        let prefix = '';
                        const strong = p.querySelector('strong');
                        if (strong) {
                            const m = strong.textContent.trim().match(/^(\d+)\./);
                            if (m) prefix = m[1] + '. ';
                        }
                        p.innerHTML = `<strong>${prefix}${q.en}</strong><br><span class=\"q-trans\">${q.trans}</span>`;
                    });
                }
                rewrite(medicalQs); rewrite(sexualQs); rewrite(infantQs);

                const labelMap = {
                    mhq_2_reason: 'If yes, for what reason? (Kung oo, unsay hinungdan?)',
                    mhq_4_reason: 'If yes, what specific disease(s)? (Kung naa, unsa man kini?)',
                    mhq_5_reason: 'If yes, what specific disease(s)? (Kung naa, unsa man kini?)',
                    mhq_8_amount: 'If yes, how much? (Kung oo, unsa ka daghan? in cc or ml)',
                    mhq_10_reason: 'If yes, what specific medication(s)? (Kung oo, unsa nga tambal?)',
                    mhq_11_supplement: 'If yes, do you supplement your diet with vitamins? (Kung oo, nag-inom ka ba ug mga bitamina?)',
                    mhq_13_amount: 'If yes, how many sticks or packs per day? (Kung oo, pila ka stick o pack kada adlaw?)',
                    diq_4_reason: 'If yes, at what age and how long did it last? (Kung oo, sa unsang edad ug pila ka dugay?)',
                    diq_5_reason: 'If yes, when did this happen? (Kung oo, kanus-a kini nahitabo?)'
                };
                Object.entries(labelMap).forEach(([field, text]) => {
                    const el = form.querySelector(`[name="${field}"]`);
                    if (!el) return;
                    const wrap = el.closest('.field');
                    if (wrap) {
                        const lab = wrap.querySelector('label');
                        if (lab) lab.textContent = text;
                    }
                });

                const conditionalParents = {
                    mhq_2: 'mhq_2_reason',
                    mhq_4: 'mhq_4_reason',
                    mhq_5: 'mhq_5_reason',
                    mhq_8: 'mhq_8_amount',
                    mhq_10: 'mhq_10_reason',
                    mhq_11: 'mhq_11_supplement',
                    mhq_13: 'mhq_13_amount',
                    diq_4: 'diq_4_reason',
                    diq_5: 'diq_5_reason'
                };

                Object.entries(conditionalParents).forEach(([radioName, fieldName]) => {
                    let target = form.querySelector(`[name="${fieldName}"]`);
                    if (!target) {
                        const rg = form.querySelector(`input[name="${fieldName}"]`);
                        if (rg) target = rg.closest('.field') || rg.closest('.radio-group') || rg.parentElement;
                    }
                    if (target) {
                        const container = target.closest('.field') || target.closest('.radio-group') || target;
                        container.setAttribute('data-cond-parent', radioName);
                        container.style.display = 'none';
                    }
                });

                function toggleConditional(radioName) {
                    const checked = form.querySelector(`input[type=radio][name="${radioName}"]:checked`);
                    const show = !!checked && checked.value === 'yes';
                    const blocks = form.querySelectorAll(`[data-cond-parent="${radioName}"]`);
                    blocks.forEach(b => {
                        b.style.display = show ? '' : 'none';
                        if (!show) {
                            b.querySelectorAll('input:not([type=radio]):not([type=checkbox]), textarea').forEach(i => i.value = '');
                            b.querySelectorAll('input[type=radio], input[type=checkbox]').forEach(i => i.checked = false);
                        }
                    });
                }
                Object.keys(conditionalParents).forEach(radioName => {
                    form.querySelectorAll(`input[type=radio][name="${radioName}"]`).forEach(r => {
                        r.addEventListener('change', () => toggleConditional(radioName));
                    });
                    toggleConditional(radioName); // initial hide unless YES
                });

                form.dataset.enhanced = '1';
            } catch (e) { console.warn('initHealthScreeningEnhancements failed', e); }
        }

        function initPickupMap() {
            if (!window.google || !google.maps) return;
            const mapEl = document.getElementById('pickup_map');
            if (!mapEl) return;

            // Default center: Cagayan de Oro City Hall
            const defaultCenter = { lat: 8.482, lng: 124.647 };
            const latInput = document.getElementById('pickup_lat');
            const lngInput = document.getElementById('pickup_lng');

            let center = defaultCenter;
            if (latInput && lngInput && latInput.value && lngInput.value) {
                const lat = parseFloat(latInput.value);
                const lng = parseFloat(lngInput.value);
                if (!Number.isNaN(lat) && !Number.isNaN(lng)) center = { lat, lng };
            }

            __geocoder = __geocoder || new google.maps.Geocoder();
            __pickupMap = new google.maps.Map(mapEl, {
                center,
                zoom: 14,
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: false,
            });

            __pickupMarker = __pickupMarker || new google.maps.Marker({
                map: __pickupMap,
                position: center,
                draggable: true,
                animation: google.maps.Animation.DROP,
            });
            __pickupMarker.setMap(__pickupMap);
            __pickupMarker.setPosition(center);
            __pickupMap.setCenter(center);

            function updateLatLngFields(pos) {
                if (latInput) latInput.value = pos.lat();
                if (lngInput) lngInput.value = pos.lng();
            }

            function reverseGeocode(pos) {
                const ta = document.getElementById('pickup_address');
                if (!__geocoder || !ta) return;
                __geocoder.geocode({ location: pos }, (results, status) => {
                    if (status === 'OK' && results && results[0]) {
                        ta.value = results[0].formatted_address;
                    }
                });
            }

            // Marker drag handlers
            if (!__markerDragListenerAdded) {
                __pickupMarker.addListener('dragend', () => {
                    const pos = __pickupMarker.getPosition();
                    updateLatLngFields(pos);
                    reverseGeocode(pos);
                });
                __markerDragListenerAdded = true;
            }

            // Initialize Places Autocomplete on the search input
            const searchInput = document.getElementById('pickup_search');
            if (searchInput) {
                if (!__autocomplete) {
                    __autocomplete = new google.maps.places.Autocomplete(searchInput, {
                        fields: ['geometry', 'formatted_address', 'name']
                    });
                }
                __autocomplete.bindTo('bounds', __pickupMap);
                if (!__autocompleteListenerAdded) {
                    __autocomplete.addListener('place_changed', () => {
                        const place = __autocomplete.getPlace();
                        if (!place || !place.geometry) return;
                        const pos = place.geometry.location;
                        __pickupMap.panTo(pos);
                        __pickupMap.setZoom(16);
                        __pickupMarker.setPosition(pos);
                        updateLatLngFields(pos);
                        const ta = document.getElementById('pickup_address');
                        if (ta) ta.value = place.formatted_address || place.name;
                    });
                    __autocompleteListenerAdded = true;
                }
            }

            // If we have a prefilled address (from $userAddress), try geocoding it once
            const ta = document.getElementById('pickup_address');
            if (ta && ta.value && (!latInput.value || !lngInput.value)) {
                __geocoder.geocode({ address: ta.value }, (results, status) => {
                    if (status === 'OK' && results && results[0] && results[0].geometry) {
                        const loc = results[0].geometry.location;
                        __pickupMap.setCenter(loc);
                        __pickupMarker.setPosition(loc);
                        updateLatLngFields(loc);
                    }
                });
            }
        }
    </script>
</body>

</html>

<!-- Floating Facebook Follow (User Dashboard Only) -->
<style>
    .floating-fb-follow {
        position: fixed;
        bottom: 18px;
        right: 18px;
        z-index: 6000;
        font-family: 'Segoe UI', Arial, sans-serif;
    }

    .floating-fb-follow {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 14px;
        font-weight: 600;
        color: #1f2937;
    }

    .floating-fb-follow .fb-follow-text {
        white-space: nowrap;
        font-weight: 600;
    }

    .floating-fb-follow .fb-follow-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #1877f2;
        color: #fff;
        text-decoration: none;
        font-size: 18px;
        transition: background .25s, transform .25s, box-shadow .25s;
        box-shadow: 0 4px 12px rgba(24, 119, 242, 0.35);
    }

    .floating-fb-follow .fb-follow-link:hover {
        background: #1362c7;
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(24, 119, 242, 0.45);
    }

    .floating-fb-follow .fb-follow-link:focus-visible {
        outline: 2px solid #1d4ed8;
        outline-offset: 2px;
    }

    @media (max-width:700px) {
        .floating-fb-follow .fb-follow-text {
            display: none;
        }
    }

    /* Avoid obstructing bottom UI elements if any sticky footer appears later */
    body.has-sticky-footer .floating-fb-follow {
        bottom: 70px;
    }
</style>
<div class="floating-fb-follow" aria-hidden="false" role="complementary">
    <span class="fb-follow-text">Please Follow and Support us on Facebook:</span>
    <a class="fb-follow-link" href="https://www.facebook.com/CDOHMBLSC" target="_blank" rel="noopener noreferrer"
        aria-label="Follow us on Facebook (opens in new tab)">
        <i class="fab fa-facebook-f" aria-hidden="true"></i>
    </a>
</div>