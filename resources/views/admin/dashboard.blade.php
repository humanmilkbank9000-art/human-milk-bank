<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard - Breastmilk Donation System</title>
    <!-- Removed FullCalendar; using lightweight vanilla calendar -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin.css?v=20250921') }}">
    <!-- Chart.js (moved into head for proper loading) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" integrity="sha384-jyWcMX7Hri2JrRscYfCN6MDYJ6xPQxwA7Lkp+QlHGywqs3tHxudkVlAK/5C9Q2nX" crossorigin="anonymous"></script>
    <style>
        /* Retained page-specific styles only (submenus, dashboard content, modals, etc.) */
        body { background:#f8f9fa; display:flex; min-height:100vh; }
        .nav-item.nav-item-collapsible { justify-content: space-between; gap:10px; }
        .nav-item .nav-caret { margin-left:auto; display:inline-flex; align-items:center; transition:transform .2s ease; color:#333; }
        .nav-item.open .nav-caret { transform:rotate(180deg); }
        .submenu { display:none; padding-left:0; margin:8px 0 10px 0; }
        .submenu .submenu-item { display:flex; align-items:center; gap:12px; padding:12px 14px; margin:8px 0 0 0; background:#fff; color:#333; text-decoration:none; border:1px solid rgba(255,105,180,.18); border-radius:12px; transition:all .2s ease; box-shadow:0 2px 4px rgba(0,0,0,.04); }
        .submenu .submenu-item:hover { background:#fff5f9; color:#ff3e98; transform:translateX(4px); }
        .submenu .submenu-item .icon { width:18px; display:inline-flex; justify-content:center; }

        /* Dashboard Content */
        .dashboard-content {
            padding: 30px;
        }

        /* Welcome banner removed */

        #calendar {
            max-width: 900px;
            margin: 0 auto;
        }

        /* Calendar styling (for availability modal) */
        .fc-daygrid-day.fc-day-available { background-color: #ffc0cb !important; border-color: #ff69b4 !important; }
        .fc-daygrid-day:hover { background-color: #ffe4e1 !important; cursor: pointer; }

        /* Category Options Styles */
        .category-view {
            padding: 30px;
        }

        .category-options-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
        }

        .option-card {
            background: #fff;
            border-radius: 12px; /* Smooth edges */
            padding: 25px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            border: 1px solid #e9ecef;
            position: relative; /* allow floating badge */
        }

        .option-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
            color: #ff69b4;
        }

        /* Circle icon to match Requests look */
        .option-card .icon-circle {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            background: #ff69b4;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
            box-shadow: 0 6px 12px rgba(255, 105, 180, 0.25);
        }
        .option-card .icon-circle i { font-size: 22px; }

        .option-card .title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }
        .badge-count {
            display: inline-block;
            background: #ff69b4;
            color: #fff;
            border-radius: 999px;
            padding: 2px 8px;
            font-size: 12px;
            line-height: 1;
            font-weight: 700;
            vertical-align: middle;
        }
        .badge-floating { position: absolute; top: 10px; right: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.15); }
        
        /* Modal Styles */
        .modal {
            display: none; /* toggled to flex */
            position: fixed;
            z-index: 9999;
            inset: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
            padding: 20px; /* small gutter for small screens */
        }

        .modal-content {
            background-color: #fff;
            padding: 0;
            border-radius: 12px;
            width: 90%;
            max-width: 1000px;
            max-height: 80vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        }

        .modal-header {
            background: linear-gradient(135deg, #ffb6ce, #ff69b4);
            color: #fff;
            padding: 20px 30px;
            border-radius: 12px 12px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-size: 20px;
            font-weight: bold;
        }

        .close-btn {
            background: none;
            border: none;
            color: #fff;
            font-size: 28px;
            cursor: pointer;
        }

        .modal-body {
            padding: 30px;
            overflow-y: auto;
        }

        .modal-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .modal-table th,
        .modal-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .modal-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        
        .status, .answer {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }

        .status.pending { background-color: #fff3cd; color: #856404; }
        .status.accepted { background-color: #d4edda; color: #155724; }
        .status.declined { background-color: #f8d7da; color: #721c24; }
        .answer.yes { background-color: #d4edda; color: #155724; }
        .answer.no { background-color: #f8d7da; color: #721c24; }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin: 2px;
        }
    /* Use global .btn-primary styles */
    .btn-primary { }
        .btn-success { background-color: #28a745; color: white; }
        .btn-danger { background-color: #dc3545; color: white; }
        .btn-secondary { background-color: #6c757d; color: white; }
        .btn-sm { padding: 4px 8px; font-size: 12px; }
        .modal-footer { padding: 15px; border-top: 1px solid #eee; text-align: right; }

        /* Health Screening Details Styles */
        .screening-details {
            max-height: 70vh;
            overflow-y: auto;
            padding: 10px;
        }

        .screening-details h3 {
            color: #ff69b4;
            margin: 20px 0 15px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid #ff69b4;
            font-size: 18px;
        }

        .screening-details h4 {
            color: #666;
            margin: 15px 0 10px 0;
            font-size: 16px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 10px;
            margin-bottom: 15px;
        }

        .info-grid p {
            margin: 5px 0;
            padding: 8px;
            background: #f8f9fa;
            border-radius: 4px;
            border-left: 3px solid #ff69b4;
        }

        .infant-info {
            background: #f0f8ff;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            border: 1px solid #e0e0e0;
        }

        .questions-section {
            background: #fafafa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .question-answer {
            background: white;
            padding: 12px;
            margin-bottom: 10px;
            border-radius: 6px;
            border-left: 4px solid #ddd;
        }

        .question-answer:last-child {
            margin-bottom: 0;
        }

        .question {
            color: #333;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .answer {
            margin-bottom: 5px;
        }

        .answer-yes {
            color: #28a745;
            font-weight: bold;
            background: #d4edda;
            padding: 2px 6px;
            border-radius: 3px;
        }

        .answer-no {
            color: #dc3545;
            font-weight: bold;
            background: #f8d7da;
            padding: 2px 6px;
            border-radius: 3px;
        }

        .additional-info {
            color: #666;
            font-style: italic;
            background: #fff3cd;
            padding: 8px;
            border-radius: 4px;
            margin-top: 5px;
            border-left: 3px solid #ffc107;
        }

        .status {
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
        }

        .status.pending {
            background: #fff3cd;
            color: #856404;
        }

        .status.accepted {
            background: #d4edda;
            color: #155724;
        }

        .status.declined {
            background: #f8d7da;
            color: #721c24;
        }

        /* Walk-in Donations Table Enhancements */
        #walk-in-donations-modal .modal-table th:nth-child(2) {
            background: linear-gradient(135deg, #ff69b4, #d63384);
            color: white;
            text-align: center;
        }

        #walk-in-donations-modal .modal-table td:nth-child(2) {
            text-align: center;
            background: #fff0f5;
            font-weight: bold;
        }

        /* Pill styles for request types */
        .pill {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        .pill.pending { background:#fff3cd; color:#856404; }
        .pill.accepted { background:#d4edda; color:#155724; }
        .pill.declined { background:#f8d7da; color:#721c24; }
        .pill.walk-in { background:#e3f2fd; color:#1976d2; }
        .pill.home-collection { background:#f3e5f5; color:#7b1fa2; }

        /* Inline alert styles */
    .inline-alert { display:flex; align-items:center; gap:10px; padding:12px 16px; border-radius:10px; border:1px solid; margin: 0 0 16px 0; }
        .inline-alert .icon { font-size:18px; }
        .inline-alert-success { background:#d1fae5; border-color:#10b981; color:#065f46; }
        .inline-alert-info { background:#e0f2fe; border-color:#3b82f6; color:#1e3a8a; }
        .inline-alert-error { background:#fee2e2; border-color:#ef4444; color:#7f1d1d; }
    .inline-alert.fade-out { opacity: 0; transition: opacity 400ms ease; }
    /* Clean up: removed donation-specific card grid; using same option-card as Requests */

        /* Health screening pie percentages legend */
        .hs-pct-legend { display:flex; gap:14px; flex-wrap:wrap; justify-content:center; align-items:center; margin-top:10px; }
        .hs-pct-item { display:flex; align-items:center; gap:6px; font-weight:600; color:#374151; }
        .hs-dot { width:10px; height:10px; border-radius:50%; display:inline-block; }
        .hs-dot.pending { background:#F59E0B; }
        .hs-dot.accepted { background:#10B981; }
        .hs-dot.declined { background:#EF4444; }
        .hs-pct-value { min-width:32px; text-align:right; font-variant-numeric: tabular-nums; }

    /* Square tile layout for consistent sizing */
    .square-tile { position: relative; width: 100%; aspect-ratio: 1 / 1; border: 1px solid #e5e7eb; border-radius: 12px; background: #fff; box-shadow: 0 6px 16px rgba(0,0,0,0.08); overflow: hidden; transition: box-shadow .2s ease, transform .2s ease; }
    .square-tile:hover { box-shadow: 0 10px 24px rgba(0,0,0,0.12); transform: translateY(-2px); }
    .square-content { position: relative; width: 100%; height: 100%; display:flex; align-items:center; justify-content:center; }
    .square-canvas { width: 100% !important; height: 100% !important; display: block; }
    .square-caption { position:absolute; bottom:8px; left:8px; right:8px; font-size:11px; color:#6b7280; text-align:center; pointer-events:none; }
    .square-overlay { position:absolute; top:8px; left:8px; right:8px; display:flex; align-items:center; justify-content:center; }
    /* Stacked layout so legend sits below the chart */
    /* Reserve room for the caption under the pie by not using full height */
    .square-stack { width:100%; height:calc(100% - 34px); display:flex; flex-direction:column; align-items:center; justify-content:center; padding:8px; box-sizing:border-box; }
    .square-legend { position: static; display:flex; gap:10px; flex-wrap:wrap; justify-content:center; margin-top:8px; }
    .square-stack .square-canvas { height: 72% !important; }
    /* Flow/caption helpers for HS tile */
    .square-content-flow { display:flex; flex-direction: column; align-items: center; justify-content: center; }
    .square-caption-static { position: static; margin-top: 6px; pointer-events: auto; }
    /* Smaller legend text and dots below the HS pie */
    .square-legend .hs-pct-item { font-size: 12px; }
    .square-legend .hs-dot { width: 8px; height: 8px; }
    .square-legend .hs-pct-value { min-width: 28px; }
    /* Tweak legend size when used as overlay */
    .square-overlay.hs-pct-legend { gap:10px; }
    .square-overlay .hs-pct-item { font-size:11px; }

        /* Table controls (search + pagination) */
        .table-controls { display:flex; justify-content: space-between; align-items:center; gap:12px; flex-wrap: wrap; }
        .table-controls .search-box { flex: 1 1 260px; }
        .table-controls .search-box input { width:100%; padding:8px 10px; border:1px solid #ddd; border-radius:6px; }
        .pagination { display:flex; gap:8px; align-items:center; }
        .pagination button { padding:6px 10px; border:1px solid #ddd; background:#fff; cursor:pointer; border-radius:6px; }
        .pagination button:disabled { opacity: .5; cursor: not-allowed; }
        .pagination .page-info { color:#666; font-size: 12px; }
    </style>
</head>
<body>
    
    <!-- Sidebar -->
    @include('admin.partials.sidebar')

    <!-- Main Content -->
    <div class="main-content top-bar-space">
        @include('admin.partials.top-bar', ['pageTitle' => 'Dashboard', 'pageTitleId' => 'pageTitle'])
        @if(session('success'))
        <div class="inline-alert inline-alert-success" role="status" aria-live="polite" style="margin: 0 30px 16px 30px;">
            <span>{{ session('success') }}</span>
        </div>
        @endif
        <div id="admin-toast-container" style="display:none"></div>
        <script>
            (function(){
                var alertEl = document.querySelector('.inline-alert');
                if (!alertEl) return;
                // Auto dismiss after 3 seconds
                setTimeout(function(){
                    // Trigger fade out
                    alertEl.classList.add('fade-out');
                    // Remove from DOM after transition
                    alertEl.addEventListener('transitionend', function(){
                        if (alertEl && alertEl.parentNode) {
                            alertEl.parentNode.removeChild(alertEl);
                        }
                    }, { once: true });
                }, 3000);
            })();
        </script>

        @php
            // Aggregate counts for chart (safe fallback)
            try {
                // Cards
                $totalDonations = \Illuminate\Support\Facades\DB::table('donation_history')->whereNull('archived_at')->count();
                $totalRequests  = \Illuminate\Support\Facades\DB::table('breastmilk_requests')->where('status','approved')->count();
                $totalScreenings = \Illuminate\Support\Facades\DB::table('health_screenings')->count();

                // Chart: show only Walk-in vs Pickup (home collection) donations
                $walkInTotal = \Illuminate\Support\Facades\DB::table('donation_history')
                    ->whereNull('archived_at')
                    ->where('donation_type','walk_in')
                    ->count();
                $pickupTotal = \Illuminate\Support\Facades\DB::table('donation_history')
                    ->whereNull('archived_at')
                    ->where('donation_type','home_collection')
                    ->count();

                // Pie: active health screenings by status (exclude archived)
                $hsPendingActive = \Illuminate\Support\Facades\DB::table('health_screenings')
                    ->where('status','pending')
                    ->whereNull('archived_at')
                    ->count();
                $hsAcceptedActive = \Illuminate\Support\Facades\DB::table('health_screenings')
                    ->where('status','accepted')
                    ->whereNull('archived_at')
                    ->count();
                $hsDeclinedActive = \Illuminate\Support\Facades\DB::table('health_screenings')
                    ->where('status','declined')
                    ->whereNull('archived_at')
                    ->count();
            } catch (Throwable $e) {
                $totalDonations = 0;
                $totalRequests = 0;
                $totalScreenings = 0;
                $walkInTotal = 0;
                $pickupTotal = 0;
                $hsPendingActive = 0; $hsAcceptedActive = 0; $hsDeclinedActive = 0;
            }
        @endphp

    <!-- Analytics (moved inside body) -->
    <div id="analytics-section" class="admin-analytics" style="margin:30px 30px 0 30px;">
            <h2 style="font-size:18px;font-weight:700;margin:0 0 16px;display:flex;align-items:center;gap:8px;">
                <span style="color:#d63384;">&#9685;</span> Breastmilk Overview
            </h2>
            <!-- Top KPI row -->
            <div class="kpi-row">
                <div class="kpi-card kpi-donation" aria-label="Total Donations">
                    <div>
                        <div class="kpi-title">Total Donations</div>
                        <div id="total-donations-count" class="kpi-value">{{ number_format($totalDonations) }}</div>
                        <div class="kpi-meta">All-time donations</div>
                    </div>
                    <span class="kpi-icon" aria-hidden="true"><i class="fas fa-hand-holding-medical"></i></span>
                </div>
                <div class="kpi-card kpi-request" aria-label="Approved Requests">
                    <div>
                        <div class="kpi-title">Approved Requests</div>
                        <div id="total-requests-count" class="kpi-value">{{ number_format($totalRequests) }}</div>
                        <div class="kpi-meta">Approved Request</div>
                    </div>
                    <span class="kpi-icon" aria-hidden="true"><i class="fas fa-file-medical"></i></span>
                </div>
                <div class="kpi-card kpi-screening" aria-label="Health Screenings">
                    <div>
                        <div class="kpi-title">Health Screenings</div>
                        <div id="total-screenings-count" class="kpi-value">{{ number_format($totalScreenings ?? 0) }}</div>
                        <div class="kpi-meta">Total Health Screenings</div>
                    </div>
                    <span class="kpi-icon" aria-hidden="true"><i class="fas fa-stethoscope"></i></span>
                </div>
            </div>

            <!-- Charts grid -->
            <div style="display:flex;flex-wrap:wrap;gap:24px;align-items:flex-start;">
                <div class="chart-card" style="flex:1 1 100%;min-width:280px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                        <div style="font-weight:700; color:#374151;">Donations vs Requests (Monthly)</div>
                        <div>
                            <select id="monthly-year-select" style="padding:6px 8px; border:1px solid #e5e7eb; border-radius:6px;">
                                @for($y = now()->year; $y >= now()->year - 4; $y--)
                                    <option value="{{ $y }}" {{ $y===now()->year ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <canvas id="donationsRequestsLineChart" aria-label="Line chart of monthly donations and requests" role="img"></canvas>
                </div>
                <div class="chart-card" style="flex:1 1 320px;max-width:520px;min-width:280px;">
                    <div class="square-tile">
                        <div class="square-content square-content-flow">
                            <canvas id="breastmilkTotalsChart" class="square-canvas" aria-label="Bar chart of walk-in donations versus pickup (home collection) donations" role="img"></canvas>
                            <div class="square-caption">Showing walk-in vs pickup donations currently in the system.</div>
                        </div>
                    </div>
                </div>
                <div class="chart-card" style="flex:1 1 320px;max-width:520px;min-width:280px;">
                    <div class="square-tile">
                        <div class="square-content square-content-flow">
                            <div class="square-stack">
                                <canvas id="hsStatusPieChart" class="square-canvas" aria-label="Pie chart of health screenings by status" role="img"></canvas>
                                @php
                        $hsTotalActive = max(0, ($hsPendingActive ?? 0) + ($hsAcceptedActive ?? 0) + ($hsDeclinedActive ?? 0));
                        $hsPendingPct  = $hsTotalActive > 0 ? round(($hsPendingActive ?? 0) / $hsTotalActive * 100) : 0;
                        $hsAcceptedPct = $hsTotalActive > 0 ? round(($hsAcceptedActive ?? 0) / $hsTotalActive * 100) : 0;
                        $hsDeclinedPct = $hsTotalActive > 0 ? round(($hsDeclinedActive ?? 0) / $hsTotalActive * 100) : 0;
                    @endphp
                                <div id="hsStatusPercentages" class="hs-pct-legend square-legend" aria-live="polite">
                                    <div class="hs-pct-item"><span class="hs-dot pending" aria-hidden="true"></span><span>Pending</span><span id="hs-pending-pct" class="hs-pct-value">{{ $hsPendingPct }}%</span></div>
                                    <div class="hs-pct-item"><span class="hs-dot accepted" aria-hidden="true"></span><span>Accepted</span><span id="hs-accepted-pct" class="hs-pct-value">{{ $hsAcceptedPct }}%</span></div>
                                    <div class="hs-pct-item"><span class="hs-dot declined" aria-hidden="true"></span><span>Declined</span><span id="hs-declined-pct" class="hs-pct-value">{{ $hsDeclinedPct }}%</span></div>
                                </div>
                            </div>
                            <div class="square-caption square-caption-static">Active health screenings by status (pending, accepted, declined).</div>
                        </div>
                    </div>
                </div>

                <!-- Availability action -->
                <div style="flex:1 1 320px; min-width:280px;">
                    <div class="square-tile" role="button" tabindex="0" onclick="openAvailabilityCalendarModal()" onkeypress="if(event.key==='Enter'){openAvailabilityCalendarModal()}" aria-label="Open availability calendar">
                        <div class="square-content">
                            <div class="option-card" style="box-shadow:none; border:none; background:transparent; padding:0;">
                                <div class="icon-circle" style="width:44px; height:44px; margin-bottom:8px;"><i class="fas fa-calendar-check" style="font-size:20px;"></i></div>
                                <div class="title" style="font-size:16px;">Set Admin Availability</div>
                                <div style="font-size:12px; color:#6b7280; margin-top:4px;">Open the calendar to mark available days and time slots.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard View -->
        <div id="dashboard-view" class="dashboard-content">
            <!-- Quick Actions moved under analytics -->
        </div>

        <!-- Category View -->
        <div id="category-view" class="category-view" style="display: none;">

            <!-- Breastmilk Request Options -->
            <div id="breastmilk-request-options" class="category-options-grid" style="display: none;">
                <div class="option-card" onclick="window.location.href='{{ route('admin.milk-requests') }}?view=pending'">
                    <div class="icon"><i class="fas fa-clock"></i></div>
                    <div class="title">Pending Requests</div>
                </div>
                <div class="option-card" onclick="window.location.href='{{ route('admin.milk-requests') }}?view=accepted'">
                    <div class="icon"><i class="fas fa-check-circle"></i></div>
                    <div class="title">Approved Requests</div>
                </div>
                <div class="option-card" onclick="window.location.href='{{ route('admin.milk-requests') }}?view=declined'">
                    <div class="icon"><i class="fas fa-times-circle"></i></div>
                    <div class="title">Declined Requests</div>
                </div>
                <div class="option-card" onclick="window.location.href='{{ route('admin.milk-requests') }}?view=pending'">
                    <div class="icon"><i class="fas fa-clipboard-list"></i></div>
                    <div class="title">All Requests</div>
                </div>
            </div>

            <!-- Breastmilk Donation Options -->
            <div id="breastmilk-donation-options" class="category-options-grid" style="display: none;">
                <div class="option-card" onclick="showReport('pending-walk-in-requests')">
                    @if(isset($pendingWalkInCount) && $pendingWalkInCount > 0)
                        <span class="badge-count badge-floating" aria-label="Pending walk-in requests">{{ $pendingWalkInCount }}</span>
                    @endif
                    <div class="icon-circle"><i class="fas fa-walking"></i></div>
                    <div class="title">Pending Walk-in Requests</div>
                </div>
                <div class="option-card" onclick="showReport('pending-home-collection-requests')">
                    @if(isset($pendingHomeCollectionCount) && $pendingHomeCollectionCount > 0)
                        <span class="badge-count badge-floating" aria-label="Pending home collection requests">{{ $pendingHomeCollectionCount }}</span>
                    @endif
                    <div class="icon-circle"><i class="fas fa-home"></i></div>
                    <div class="title">Pending Home Collections</div>
                </div>
                <div class="option-card" onclick="showReport('scheduled-home-collection-pickup')">
                    <div class="icon-circle"><i class="fas fa-calendar-alt"></i></div>
                    <div class="title">Scheduled Pickups</div>
                </div>
                <div class="option-card" onclick="showReport('walk-in-donations')">
                    <div class="icon-circle"><i class="fas fa-check"></i></div>
                    <div class="title">Completed Walk-ins</div>
                </div>
                <div class="option-card" onclick="showReport('pickup-donations')">
                    <div class="icon-circle"><i class="fas fa-truck"></i></div>
                    <div class="title">Completed Pickups</div>
                </div>
            </div>

            
            
            <!-- Monthly Reports Options -->
            <div id="monthly-reports-options" class="category-options-grid" style="display: none;">
                <div class="option-card" onclick="showReport('monthly-summary')">
                    <div class="icon"><i class="fas fa-chart-line"></i></div>
                    <div class="title">Monthly Summary</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    @include('admin.donations')
    @include('admin.donor-reports')
    @include('admin.availability')
    @include('admin.success-modal')
    @include('admin.validate-walk-in')
    @include('admin.pending-walk-in-requests')
    @include('admin.pending-home-collection-requests')
    @include('admin.scheduled-home-collection-pickup')

    <!-- Success Modal -->
    <div id="successModal" class="modal">
        <div class="modal-content" style="max-width: 400px; text-align: center;">
            <div class="modal-header">
                <h3 class="modal-title" id="successModalTitle">Success</h3>
                <button class="close-btn" onclick="closeModal('successModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div style="font-size: 48px; margin-bottom: 20px; color: #28a745;"><i class="fas fa-check-circle"></i></div>
                <p id="successModalMessage" style="font-size: 16px; color: #333; margin-bottom: 20px;"></p>
                <button onclick="closeModal('successModal')" class="btn btn-primary" style="padding: 10px 30px;">OK</button>
            </div>
        </div>
    </div>

    <!-- Logout Confirmation Modal -->
    <div id="logoutModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="logout-modal-title" aria-describedby="logout-modal-desc" data-trap-focus="true">
        <div class="modal-content" style="max-width: 440px; text-align: center;">
            <div class="modal-header" style="align-items: center;">
                <h3 class="modal-title" id="logout-modal-title">Confirm Logout</h3>
                <button class="close-btn" aria-label="Close" onclick="closeModal('logoutModal')" type="button">&times;</button>
            </div>
            <div class="modal-body">
                <div style="font-size: 44px; margin-bottom: 14px; color: #ff4d6d;">
                    <i class="fas fa-right-from-bracket"></i>
                </div>
                <p id="logout-modal-desc" style="font-size: 15px; color: #333; margin: 0 12px 22px;">You’re about to log out of the admin dashboard.</p>
                <div style="background:#fff7fb; border:1px solid #ffd1e6; color:#7a2944; padding:10px 12px; border-radius:8px; font-size:13px; margin:0 16px 18px;">
                    Make sure any unsaved changes are saved.
                </div>
                <div style="display: flex; gap: 10px; justify-content: center;">
                    <button id="logout-cancel-btn" onclick="closeModal('logoutModal')" class="btn btn-secondary" type="button" style="padding: 10px 20px; min-width: 110px;">Cancel</button>
                    <button id="logout-confirm-btn" onclick="confirmLogout()" class="btn btn-primary" type="button" style="padding: 10px 20px; min-width: 110px; display:inline-flex; align-items:center; justify-content:center; gap:8px;">
                        <i class="fas fa-circle-notch fa-spin" id="logout-spinner" style="display:none;"></i>
                        <span id="logout-confirm-text">Logout</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- No FullCalendar script; vanilla calendar implemented below -->
    <script>
        // Sidebar submenu init (Health Screening)
        (function(){
            const toggle = document.getElementById('hsNavToggle');
            const menu = document.getElementById('hsSubmenu');
            if (!toggle || !menu) return;

            const key = 'admin.hsSubmenu.open';
            const setOpen = (open) => {
                menu.style.display = open ? 'block' : 'none';
                toggle.classList.toggle('open', open);
                toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                try { localStorage.setItem(key, open ? '1' : '0'); } catch(e) {}
            };

            // init from storage
            let initial = false;
            try { initial = localStorage.getItem(key) === '1'; } catch(e) {}
            setOpen(initial);

            toggle.addEventListener('click', function(){
                const isOpen = menu.style.display === 'block';
                setOpen(!isOpen);
            });

            // Close when clicking outside
            document.addEventListener('click', function(e){
                if (!toggle.contains(e.target) && !menu.contains(e.target)) {
                    if (menu.style.display === 'block') setOpen(false);
                }
            });
        })();

        const pageTitles = {
            'dashboard': 'Dashboard',
            'health-screening': 'Health Screening',
            'breastmilk-request': 'Breastmilk Request',
            'breastmilk-donation': 'Breastmilk Donation',
            'monthly-reports': 'Monthly Reports'
        };

        function showDashboard() {
            document.getElementById('dashboard-view').style.display = 'block';
            document.getElementById('category-view').style.display = 'none';
            document.getElementById('pageTitle').innerHTML = pageTitles['dashboard'];
            // Show analytics on dashboard view
            const analytics = document.getElementById('analytics-section');
            if (analytics) analytics.style.display = 'block';
            
            document.querySelectorAll('.nav-item').forEach(item => item.classList.remove('active'));
            // first nav-item is dashboard
            const first = document.querySelector('.nav-menu .nav-item');
            if (first) first.classList.add('active');
        }

        function showCategory(categoryId) {
            // Hide dashboard and show category container
            document.getElementById('dashboard-view').style.display = 'none';
            document.getElementById('category-view').style.display = 'block';
            // Hide analytics when viewing any category to avoid duplication with dashboard
            const analytics = document.getElementById('analytics-section');
            if (analytics) analytics.style.display = 'none';

            // Hide all category option grids
            document.querySelectorAll('.category-options-grid').forEach(grid => {
                grid.style.display = 'none';
            });

            // Show the selected one
            const gridEl = document.getElementById(categoryId + '-options');
            if (gridEl) {
                gridEl.style.display = 'grid';
            }
            
            // Update page title
            document.getElementById('pageTitle').innerHTML = pageTitles[categoryId];

            // Update active nav item
            document.querySelectorAll('.nav-item').forEach(item => item.classList.remove('active'));
            document.querySelector(`[onclick="showCategory('${categoryId}')"]`).classList.add('active');
        }

        function showReport(reportType) {
            // For health screening, redirect to dedicated non-modal page with proper view
            const mapping = {
                'users-undergo-screening': 'pending',
                'accepted-screening': 'accepted',
                'declined-screening': 'declined',
                'archived-screenings': 'archived'
            };
            if (mapping[reportType]) {
                window.location.href = `{{ route('admin.health-screening') }}?view=${mapping[reportType]}`;
                return;
            }
            // Fallback: keep legacy modal behavior for other categories if any
            const modalId = reportType + '-modal';
            const modal = document.getElementById(modalId);
            if (modal) {
                loadReportData(reportType);
                openModal(modalId);
            } else {
                console.error('Modal not found for report type:', reportType);
            }
        }
        
        // Client-side cache/state for health screening lists
    const reportData = {}; // { reportType: { status: 'accepted', all: [...] } }
    const reportState = {}; // { reportType: { page:1, pageSize:10, search:'', archivedView:'accepted', year:'all', month:'all' } }

        function loadReportData(reportType) {
            let endpoint = '';
            let status = '';
            let archivedFilter = false;

            switch (reportType) {
                // Health Screening
                case 'users-undergo-screening': status = 'pending'; break;
                case 'accepted-screening': status = 'accepted'; break;
                case 'accepted-archived': status = 'accepted'; archivedFilter = true; break;
                case 'declined-screening': status = 'declined'; break;
                case 'declined-archived': status = 'declined'; archivedFilter = true; break;
                case 'archived-screenings':
                    status = (reportState['archived-screenings']?.archivedView) || 'accepted';
                    archivedFilter = true;
                    break;

                // Walk-in Requests
                case 'pending-walk-in-requests':
                    endpoint = '/admin/walk-in-requests/pending';
                    break;

                // Home Collection Requests
                case 'pending-home-collection-requests':
                    endpoint = '/admin/home-collection-requests/pending';
                    break;

                // Scheduled Home Collection Pickup
                case 'scheduled-home-collection-pickup':
                    endpoint = '/admin/home-collection-requests/scheduled';
                    break;

                // Walk-in Donations
                case 'walk-in-donations':
                    endpoint = '/admin/reports/breastmilk-donations/walk-in';
                    break;

                // Pickup Donations
                case 'pickup-donations':
                    endpoint = '/admin/reports/breastmilk-donations/pickup';
                    break;

                // Other reports...
            }

            if (status) {
                endpoint = `/admin/health-screening-data/${status}`;
            } else if (endpoint) {
                // Use the specific endpoint
            } else {
                // Handle other report endpoints if necessary
                console.log("Endpoint for " + reportType + " not defined yet.");
                return;
            }

            const url = archivedFilter ? `${endpoint}?archived=1` : endpoint;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (reportType === 'pending-walk-in-requests') {
                            populateWalkInRequestsModal(data.data);
                        } else if (reportType === 'pending-home-collection-requests') {
                            populatePendingHomeCollectionRequestsModal(data.data);
                        } else if (reportType === 'scheduled-home-collection-pickup') {
                            populateScheduledHomeCollectionPickupModal(data.data);
                        } else if (reportType === 'walk-in-donations') {
                            populateWalkInDonationsModal(data.data);
                        } else if (reportType === 'pickup-donations') {
                            populatePickupDonationsModal(data.data);
                        } else {
                            // Cache and render with search/pagination
                            reportData[reportType] = { status, all: data.data || [] };
                            if (!reportState[reportType]) reportState[reportType] = { page: 1, pageSize: 10, search: '', archivedView: status === 'declined' ? 'declined' : 'accepted', year: 'all', month: 'all' };
                            if (reportType === 'archived-screenings') {
                                reportState[reportType].archivedView = status;
                            }
                            renderHealthScreeningReport(reportType);
                        }
                    }
                })
                .catch(console.error);
        }

        function getHealthScreeningControls(reportType) {
            // Map combined archived to shared control ids
            const base = reportType === 'archived-screenings' ? 'archived-screenings' : reportType;
            return {
                searchInput: document.getElementById(base + '-search'),
                pagination: document.getElementById(base + '-pagination'),
                yearSelect: document.getElementById(base + '-year'),
                monthSelect: document.getElementById(base + '-month')
            };
        }

        function attachSearchHandler(reportType) {
            const { searchInput, yearSelect, monthSelect } = getHealthScreeningControls(reportType);
            if (searchInput && !searchInput.dataset.bound) {
                searchInput.addEventListener('input', function() {
                    reportState[reportType].search = this.value || '';
                    reportState[reportType].page = 1;
                    renderHealthScreeningReport(reportType);
                });
                searchInput.dataset.bound = '1';
            }
            if (searchInput) {
                searchInput.value = reportState[reportType]?.search || '';
            }

            // Attach year/month filter handlers when present (archived-screenings)
            if (yearSelect && !yearSelect.dataset.bound) {
                yearSelect.addEventListener('change', function() {
                    reportState[reportType].year = this.value || 'all';
                    reportState[reportType].page = 1;
                    renderHealthScreeningReport(reportType);
                });
                yearSelect.dataset.bound = '1';
            }
            if (monthSelect && !monthSelect.dataset.bound) {
                monthSelect.addEventListener('change', function() {
                    reportState[reportType].month = this.value || 'all';
                    reportState[reportType].page = 1;
                    renderHealthScreeningReport(reportType);
                });
                monthSelect.dataset.bound = '1';
            }
            // Populate year options based on current data and view
            if (reportType === 'archived-screenings' && yearSelect) {
                populateArchivedYears(yearSelect);
                // Restore selections
                yearSelect.value = reportState[reportType].year || 'all';
                if (monthSelect) monthSelect.value = reportState[reportType].month || 'all';
            }
        }

        function populateArchivedYears(selectEl) {
            const rd = reportData['archived-screenings'];
            if (!rd || !rd.all) return;
            const currentView = reportState['archived-screenings']?.archivedView || rd.status || 'accepted';
            const items = rd.all.filter(x => x.status === currentView);
            const years = new Set();
            items.forEach(item => {
                const d = new Date(item.updated_at || item.created_at);
                if (!isNaN(d.getTime())) years.add(d.getFullYear());
            });
            const current = selectEl.value;
            selectEl.innerHTML = '<option value="all">All Years</option>' +
                Array.from(years).sort((a,b)=>b-a).map(y => `<option value="${y}">${y}</option>`).join('');
            if (current && (current === 'all' || years.has(parseInt(current)))) {
                selectEl.value = current;
            }
        }

        function filterHealthScreenings(reportType) {
            const state = reportState[reportType] || { search: '' };
            let data = reportData[reportType]?.all || [];
            // For combined archived modal, ensure correct status filter
            if (reportType === 'archived-screenings' && state.archivedView) {
                data = data.filter(item => item.status === state.archivedView);
                // Apply Year/Month filtering
                const y = state.year || 'all';
                const m = state.month || 'all';
                if (y !== 'all' || m !== 'all') {
                    data = data.filter(item => {
                        const d = new Date(item.updated_at || item.created_at);
                        if (isNaN(d.getTime())) return false;
                        const yearOk = y === 'all' ? true : d.getFullYear() === parseInt(y);
                        const monthOk = m === 'all' ? true : (d.getMonth() + 1) === parseInt(m);
                        return yearOk && monthOk;
                    });
                }
            }
            const term = (state.search || '').toLowerCase().trim();
            if (!term) return data;
            return data.filter(item => {
                const name = (item.Full_Name || '').toLowerCase();
                const contact = (item.Contact_Number || '').toLowerCase();
                return name.includes(term) || contact.includes(term);
            });
        }

        function renderPagination(reportType, totalItems) {
            const { pagination } = getHealthScreeningControls(reportType);
            if (!pagination) return;

            const state = reportState[reportType];
            const totalPages = Math.max(1, Math.ceil(totalItems / state.pageSize));
            state.page = Math.min(state.page, totalPages);

            pagination.innerHTML = '';
            const prevBtn = document.createElement('button');
            prevBtn.textContent = 'Prev';
            prevBtn.disabled = state.page <= 1;
            prevBtn.onclick = () => { state.page = Math.max(1, state.page - 1); renderHealthScreeningReport(reportType); };

            const nextBtn = document.createElement('button');
            nextBtn.textContent = 'Next';
            nextBtn.disabled = state.page >= totalPages;
            nextBtn.onclick = () => { state.page = Math.min(totalPages, state.page + 1); renderHealthScreeningReport(reportType); };

            const info = document.createElement('span');
            info.className = 'page-info';
            info.textContent = `Page ${state.page} of ${totalPages}`;

            pagination.appendChild(prevBtn);
            pagination.appendChild(info);
            pagination.appendChild(nextBtn);
        }

        function renderHealthScreeningReport(reportType) {
            const status = reportData[reportType]?.status;
            if (reportType === 'archived-screenings') {
                const hdr = document.getElementById('archived-date-header');
                if (hdr) hdr.textContent = (status === 'declined') ? 'Date Declined' : 'Date Accepted';
                updateArchivedToggleButtons();
            }
            const filtered = filterHealthScreenings(reportType);
            const state = reportState[reportType];
            const start = (state.page - 1) * state.pageSize;
            const pageItems = filtered.slice(start, start + state.pageSize);

            // Render table
            populateHealthScreeningModal(reportType, status, pageItems);

            // Ensure search handler is attached and render pagination
            attachSearchHandler(reportType);
            renderPagination(reportType, filtered.length);
        }

        function updateArchivedToggleButtons() {
            const view = reportState['archived-screenings']?.archivedView || reportData['archived-screenings']?.status || 'accepted';
            const a = document.getElementById('archived-toggle-accepted');
            const d = document.getElementById('archived-toggle-declined');
            if (a) a.className = 'btn ' + (view === 'accepted' ? 'btn-primary' : 'btn-secondary');
            if (d) d.className = 'btn ' + (view === 'declined' ? 'btn-primary' : 'btn-secondary');
        }

        function switchArchivedView(newView) {
            if (!reportState['archived-screenings']) {
                reportState['archived-screenings'] = { page: 1, pageSize: 10, search: '', archivedView: 'accepted' };
            }
            reportState['archived-screenings'].archivedView = (newView === 'declined') ? 'declined' : 'accepted';
            reportState['archived-screenings'].page = 1;
            loadReportData('archived-screenings');
        }

        // Helper function to format dates safely
        function formatDate(dateString) {
            if (!dateString) return 'N/A';

            try {
                const date = new Date(dateString);
                if (isNaN(date.getTime())) return 'N/A';

                return date.toLocaleString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                });
            } catch (error) {
                console.error('Error formatting date:', error);
                return 'N/A';
            }
        }

        function populateHealthScreeningModal(reportType, status, screenings) {
            const tbodyId = (reportType === 'archived-screenings') ? 'archived-screenings-tbody' : (reportType + '-tbody');
            const tbody = document.getElementById(tbodyId);
            if (!tbody) return;

            tbody.innerHTML = '';
            if (screenings.length === 0) {
                let message = 'No current health screening submission.';
                if (status === 'declined') {
                    message = (reportType === 'declined-archived' || reportType === 'archived-screenings') ? 'No archived declined screening.' : 'No current rejected health screening.';
                } else if (status === 'accepted') {
                    message = (reportType === 'accepted-archived' || reportType === 'archived-screenings') ? 'No archived accepted screening.' : 'No current approved health screening.';
                }
                tbody.innerHTML = `<tr><td colspan="100%">${message}</td></tr>`;
                return;
            }

            screenings.forEach(screening => {
                const row = document.createElement('tr');
                if (status === 'pending') {
                    // For pending screenings, show submission date
                    const submittedDate = formatDate(screening.created_at);
                    row.innerHTML = `
                        <td>${screening.Full_Name}</td>
                        <td>${screening.Contact_Number}</td>
                        <td>${submittedDate}</td>
                        <td><span class="status pending">Pending</span></td>
                        <td><button class="btn btn-primary btn-sm" onclick="viewScreeningDetails(${screening.Health_Screening_ID})">View</button></td>
                    `;
                } else {
                    // For accepted/declined screenings, show the date when status was updated
                    const statusDate = formatDate(screening.updated_at || screening.created_at);
                    const actions = [];
                    actions.push(`<button class="btn btn-primary btn-sm" onclick="viewScreeningDetails(${screening.Health_Screening_ID}, '${status}')">View</button>`);
                    const isArchivedView = (reportType === 'accepted-archived' || reportType === 'declined-archived' || reportType === 'archived-screenings');
                    if (!isArchivedView) {
                        // Show Archive button on active lists for both accepted and declined
                        actions.push(`<button class="btn btn-secondary btn-sm" onclick="archiveScreening(${screening.Health_Screening_ID}, '${status}')">Archive</button>`);
                    } else {
                        // Show Unarchive button on archived lists
                        actions.push(`<button class=\"btn btn-success btn-sm\" onclick=\"unarchiveScreening(${screening.Health_Screening_ID}, '${status}')\">Unarchive</button>`);
                        // Also allow permanent delete in archived view
                        actions.push(`<button class=\"btn btn-danger btn-sm\" onclick=\"deleteScreening(${screening.Health_Screening_ID})\">Delete</button>`);
                    }
                    row.innerHTML = `
                        <td>${screening.Full_Name}</td>
                        <td>${screening.Contact_Number}</td>
                        <td>${statusDate}</td>
                        <td><span class="status ${screening.status}">${screening.status}</span></td>
                        <td>${actions.join(' ')}</td>
                    `;
                }
                tbody.appendChild(row);
            });
        }

        function archiveScreening(screeningId, status) {
            if (!screeningId) return;
            if (window.saConfirm) {
                return saConfirm({ title:'Archive this screening?', text:'You can restore it from Archived later.', icon:'warning', confirmButtonText:'Archive' })
                    .then(function(r){ if(r.isConfirmed){ doArchive(screeningId, status); } });
            }
            if (!confirm('Archive this screening?')) return;
            doArchive(screeningId, status);
        }

        function doArchive(screeningId, status){

            fetch(`/admin/health-screening/${screeningId}/archive`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({})
            })
            .then(res => res.json())
            .then(data => {
                if (data && data.success) {
                    if (window.saSuccess) saSuccess('Screening archived successfully'); else showSuccessModal('Success','Screening archived successfully');
                    // Close any open health screening modal and reopen archived list if possible
                    closeModal('accepted-screening-modal');
                    closeModal('declined-screening-modal');
                    // Open combined archived modal with the selected view
                    if (!reportState['archived-screenings']) reportState['archived-screenings'] = { page: 1, pageSize: 10, search: '', archivedView: status, year: 'all', month: 'all' };
                    reportState['archived-screenings'].archivedView = status;
                    loadReportData('archived-screenings');
                    openModal('archived-screenings-modal');
                } else {
                    if (window.saError) saError((data && data.message) ? data.message : 'Failed to archive screening'); else alert((data && data.message) ? data.message : 'Failed to archive screening');
                }
            })
            .catch(err => {
                console.error('Archive error:', err);
                if (window.saError) saError('An error occurred while archiving'); else alert('An error occurred while archiving');
            });
        }

        function unarchiveScreening(screeningId, status) {
            if (!screeningId) return;
            if (window.saConfirm) {
                return saConfirm({ title:'Unarchive this screening?', confirmButtonText:'Unarchive' })
                    .then(function(r){ if(r.isConfirmed){ doUnarchive(screeningId, status); } });
            }
            if (!confirm('Unarchive this screening?')) return;
            doUnarchive(screeningId, status);
        }

        function doUnarchive(screeningId, status){

            fetch(`/admin/health-screening/${screeningId}/unarchive`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({})
            })
            .then(res => res.json())
            .then(data => {
                if (data && data.success) {
                    if (window.saSuccess) saSuccess('Screening unarchived successfully'); else showSuccessModal('Success','Screening unarchived successfully');
                    // Close combined archived modal
                    closeModal('archived-screenings-modal');
                    // Reload active list corresponding to status and open it
                    if (status === 'accepted') {
                        loadReportData('accepted-screening');
                        openModal('accepted-screening-modal');
                    } else if (status === 'declined') {
                        loadReportData('declined-screening');
                        openModal('declined-screening-modal');
                    }
                } else {
                    if (window.saError) saError((data && data.message) ? data.message : 'Failed to unarchive screening'); else alert((data && data.message) ? data.message : 'Failed to unarchive screening');
                }
            })
            .catch(err => {
                console.error('Unarchive error:', err);
                if (window.saError) saError('An error occurred while unarchiving'); else alert('An error occurred while unarchiving');
            });
        }

        function deleteScreening(screeningId) {
            if (!screeningId) return;
            if (window.saConfirm) {
                return saConfirm({ title:'Delete screening permanently?', text:'This action cannot be undone.', icon:'error', confirmButtonText:'Delete' })
                    .then(function(r){ if(r.isConfirmed){ doDelete(screeningId); } });
            }
            if (!confirm('This will permanently delete the screening. Continue?')) return;
            doDelete(screeningId);
        }

        function doDelete(screeningId){

            fetch(`/admin/health-screening/${screeningId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data && data.success) {
                    if (window.saSuccess) saSuccess('Health screening deleted successfully'); else showSuccessModal('Success','Health screening deleted successfully');
                    // Refresh archived list in current view
                    loadReportData('archived-screenings');
                } else {
                    if (window.saError) saError((data && data.message) ? data.message : 'Failed to delete screening'); else alert((data && data.message) ? data.message : 'Failed to delete screening');
                }
            })
            .catch(err => {
                console.error('Delete error:', err);
                if (window.saError) saError('An error occurred while deleting'); else alert('An error occurred while deleting');
            });
        }
        
        let currentScreeningId = null;
        function viewScreeningDetails(screeningId, statusFromList = null) {
            currentScreeningId = screeningId;
            fetch(`/health-screening/${screeningId}/details`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const content = document.getElementById('health-screening-details-content');
                        content.innerHTML = generateScreeningDetailsHTML(data);
                        
                        const footer = document.getElementById('health-screening-actions');
                        const status = data.screening?.status || statusFromList;

                        if (status === 'pending') {
                            footer.innerHTML = `
                                <div style="display: flex; flex-direction: column; gap: 15px; width: 100%;">
                                    <div class="form-group" style="margin: 0;">
                                        <label for="screening-comments" style="display: block; margin-bottom: 5px; font-weight: 500;">Comments:</label>
                                        <textarea id="screening-comments" class="form-control" rows="3" placeholder="Add your comments here..." style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; resize: vertical;"></textarea>
                                        <small class="form-text text-muted" style="color: #666; font-size: 12px;">Add any relevant comments or notes about this decision.</small>
                                    </div>
                                    <div style="display: flex; gap: 10px; justify-content: flex-end;">
                                        <button class="btn btn-success" onclick="submitScreeningAction('accept')" style="padding: 8px 16px;">✅ Accept</button>
                                        <button class="btn btn-danger" onclick="submitScreeningAction('decline')" style="padding: 8px 16px;">❌ Decline</button>
                                        <button class="btn btn-secondary" onclick="closeModal('health-screening-details-modal')" style="padding: 8px 16px;">Cancel</button>
                                    </div>
                                </div>
                            `;
                        } else {
                            footer.innerHTML = `<button class="btn btn-secondary" onclick="closeModal('health-screening-details-modal')">Close</button>`;
                        }
                        openModal('health-screening-details-modal');
                    }
                });
        }

        function generateScreeningDetailsHTML(data) {
            const s = data.screening;
            let html = `
                <div class="screening-details">
                    <h3>Personal Information</h3>
                    <div class="info-grid">
                        <p><strong>Name:</strong> ${s.Full_Name}</p>
                        <p><strong>Contact:</strong> ${s.Contact_Number}</p>
                        <p><strong>Civil Status:</strong> ${s.civil_status}</p>
                        <p><strong>Occupation:</strong> ${s.occupation}</p>
                        <p><strong>Type of Donor:</strong> ${s.type_of_donor.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}</p>
                        <p><strong>Submitted:</strong> ${formatDate(s.created_at)}</p>
                        <p><strong>Status:</strong> <span class="status ${s.status}">${s.status}</span></p>
                        ${s.updated_at && s.status !== 'pending' ? `<p><strong>Status Updated:</strong> ${formatDate(s.updated_at)}</p>` : ''}
                        ${s.admin_notes ? `<p><strong>Admin Notes:</strong> ${s.admin_notes}</p>` : ''}
                    </div>
            `;

            // Add infant information if available
            if (data.infants && data.infants.length > 0) {
                html += `<h3>Infant Information</h3>`;
                data.infants.forEach((infant, index) => {
                    html += `
                        <div class="infant-info">
                            <h4>Infant ${index + 1}</h4>
                            <div class="info-grid">
                                <p><strong>Name:</strong> ${infant.Full_Name}</p>
                                <p><strong>Sex:</strong> ${infant.Sex}</p>
                                <p><strong>Date of Birth:</strong> ${new Date(infant.Date_Of_Birth).toLocaleDateString()}</p>
                                <p><strong>Age:</strong> ${infant.Age} months</p>
                                <p><strong>Birth Weight:</strong> ${infant.Birthweight} kg</p>
                            </div>
                        </div>
                    `;
                });
            }

            // Medical History Questions - Exact from form
            if (data.medical_history && data.medical_history.length > 0) {
                html += `<h3>Medical History</h3><div class="questions-section">`;
                const medicalQuestions = [
                    "Have you donated breastmilk before?",
                    "Have you for any reason been deferred as a breastmilk donor?",
                    "Did you have a normal pregnancy and delivery for your most recent pregnancy?",
                    "Do you have any acute or chronic infection such as but not limited to: tuberculosis, hepatitis, systemic disorders?",
                    "Have you been diagnosed with a chronic non-infectious illness such as but not limited to: diabetes, hypertension, heart disease?",
                    "Have you received any blood transfusion or any blood products within the last twelve (12) months?",
                    "Have you received any organ or tissue transplant within the last twelve (12) months?",
                    "Have you had any intake of any alcohol or hard liquor within the last twenty four (24) hours?",
                    "Do you use megadose vitamins or pharmacologically active herbal preparations?",
                    "Do you regularly use over-the-counter medications or systemic preparations such as replacement hormones, birth control hormones, anti-hypoglycemics, blood thinners?",
                    "Are you a total vegetarian/vegan?",
                    "Do you use illicit drugs?",
                    "Do you smoke?",
                    "Are you around people who smoke (passive smoking)?",
                    "Have you had breast augmentation surgery, using silicone breast implants?"
                ];

                const medicalQuestionsBisaya = [
                    "<i>Nakahatag ka na ba ug gatas sa inahan kaniadto?</i>",
                    "<i>Aduna ka bay rason nga gidili ka isip naghatag ug gatas sa inahan?</i>",
                    "<i>Aduna ka bay normal nga pagmabdos ug pagpanganak sa imong pinakabag-o nga pagmabdos?</i>",
                    "<i>Aduna ka bay grabe o dugay nga impeksyon sama sa: tuberculosis, hepatitis, mga sakit sa lawas?</i>",
                    "<i>Na-diagnose ka na ba ug dugay nga sakit nga dili makatakod sama sa: diabetes, hypertension, sakit sa kasingkasing?</i>",
                    "<i>Nakadawat ka ba ug dugo o mga produkto sa dugo sulod sa miaging dose ka (12) bulan?</i>",
                    "<i>Nakadawat ka ba ug organ o tissue transplant sulod sa miaging dose ka (12) bulan?</i>",
                    "<i>Nag-inom ka ba ug alkohol o lig-on nga ilimnon sulod sa miaging kawhaan ug upat (24) ka oras?</i>",
                    "<i>Naggamit ka ba ug daghan kaayo nga bitamina o mga herbal nga tambal?</i>",
                    "<i>Kanunay ka bang naggamit ug mga tambal nga walay reseta o mga sistemang tambal sama sa replacement hormones, birth control hormones, anti-hypoglycemics, blood thinners?</i>",
                    "<i>Vegetarian/vegan ka ba nga kompleto?</i>",
                    "<i>Naggamit ka ba ug mga dili legal nga droga?</i>",
                    "<i>Nagsigarilyo ka ba?</i>",
                    "<i>Naa ka ba sa palibot sa mga tawo nga nagsigarilyo (passive smoking)?</i>",
                    "<i>Nakaoperasyon ka na ba sa dughan gamit ang silicone breast implants?</i>"
                ];

                data.medical_history.forEach((answer, index) => {
                    if (index < medicalQuestions.length) {
                        html += `
                            <div class="question-answer">
                                <p class="question"><strong>Q${index + 1}:</strong> ${medicalQuestions[index]}<br>${medicalQuestionsBisaya[index]}</p>
                                <p class="answer"><strong>Answer:</strong> <span class="answer-${answer.answer}">${answer.answer.toUpperCase()}</span></p>
                                ${answer.additional_info ? `<p class="additional-info"><strong>Additional Info:</strong> ${answer.additional_info}</p>` : ''}
                            </div>
                        `;
                    }
                });
                html += `</div>`;
            }

            // Sexual History Questions - Exact from form
            if (data.sexual_history && data.sexual_history.length > 0) {
                html += `<h3>Sexual History</h3><div class="questions-section">`;
                const sexualQuestions = [
                    "Have you ever had syphilis, HIV, herpes or any sexually transmitted disease (STD)?",
                    "Do you have multiple sexual partners?",
                    "Have you had a sexual partner who is: Bisexual, Promiscuous, Has had an STD/AIDS/HIV, Received blood for bleeding problems, or Is an intravenous drug user?",
                    "Have you had a tattoo applied or had an accidental needlestick injury or contact with someone else's blood?"
                ];

                const sexualQuestionsBisaya = [
                    "<i>Nakaangkon ka na ba ug syphilis, HIV, herpes o bisan unsang sakit nga makuha pinaagi sa pakighilawas (STD)?</i>",
                    "<i>Aduna ka bay daghang kauban sa pakighilawas?</i>",
                    "<i>Aduna ka bay kauban sa pakighilawas nga: Bisexual, Promiscuous, Adunay STD/AIDS/HIV, Nakadawat ug dugo tungod sa pagdugo, o Naggamit ug droga pinaagi sa injection?</i>",
                    "<i>Nakapatattoo ka na ba o nakaangkon ug aksidenteng pagkatusok sa injection o nakahikap sa dugo sa uban?</i>"
                ];

                data.sexual_history.forEach((answer, index) => {
                    const questionIndex = answer.question_number - 1;
                    if (questionIndex < sexualQuestions.length && sexualQuestions[questionIndex]) {
                        html += `
                            <div class="question-answer">
                                <p class="question"><strong>Q${answer.question_number}:</strong> ${sexualQuestions[questionIndex]}<br>${sexualQuestionsBisaya[questionIndex]}</p>
                                <p class="answer"><strong>Answer:</strong> <span class="answer-${answer.answer}">${answer.answer.toUpperCase()}</span></p>
                                ${answer.additional_info ? `<p class="additional-info"><strong>Additional Info:</strong> ${answer.additional_info}</p>` : ''}
                            </div>
                        `;
                    }
                });
                html += `</div>`;
            }

            // Donor's Infant Questions - Exact from form
            if (data.donor_infant && data.donor_infant.length > 0) {
                html += `<h3>Donor's Infant</h3><div class="questions-section">`;
                const infantQuestions = [
                    "Is your child healthy?",
                    "Was your child delivered full term?",
                    "Are you exclusively breastfeeding your child?",
                    "Is/was your youngest child jaundiced?",
                    "Has your child ever received milk from another mother?"
                ];

                const infantQuestionsBisaya = [
                    "<i>Himsog ba ang imong anak?</i>",
                    "<i>Natawo ba ang imong anak sa hustong panahon (full term)?</i>",
                    "<i>Gatas sa inahan ra ba ang imong gihatag sa imong anak (exclusively breastfeeding)?</i>",
                    "<i>Nangitag ba o nangitag na ba ang imong pinakagamay nga anak?</i>",
                    "<i>Nakadawat na ba ang imong anak ug gatas gikan sa laing inahan?</i>"
                ];

                data.donor_infant.forEach((answer, index) => {
                    if (index < infantQuestions.length) {
                        html += `
                            <div class="question-answer">
                                <p class="question"><strong>Q${index + 1}:</strong> ${infantQuestions[index]}<br>${infantQuestionsBisaya[index]}</p>
                                <p class="answer"><strong>Answer:</strong> <span class="answer-${answer.answer}">${answer.answer.toUpperCase()}</span></p>
                                ${answer.additional_info ? `<p class="additional-info"><strong>Additional Info:</strong> ${answer.additional_info}</p>` : ''}
                            </div>
                        `;
                    }
                });
                html += `</div>`;
            }

            html += `</div>`;
            return html;
        }

        // Keep a copy for filtering
        let __pendingWalkInAll = [];

        function populateWalkInRequestsModal(requests) {
            const tbody = document.getElementById('pending-walk-in-requests-tbody');
            if (!tbody) return;

            // Save all requests for later filtering
            __pendingWalkInAll = Array.isArray(requests) ? requests.slice() : [];
            tbody.innerHTML = '';

            // Filter only walk-in requests
            const walkInRequests = requests.filter(request => request.type === 'walk_in');

            if (walkInRequests.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6">No pending walk-in requests.</td></tr>';
                return;
            }

            walkInRequests.forEach(request => {
                const row = document.createElement('tr');
                const date = new Date(request.donation_date).toLocaleDateString();

                // Fix time formatting - handle both HH:MM:SS and HH:MM formats
                let time = 'TBD';
                if (request.donation_time && request.donation_time !== '00:00:00' && request.donation_time !== '00:00') {
                    try {
                        // Handle both HH:MM:SS and HH:MM formats
                        const timeStr = request.donation_time.length === 5 ? request.donation_time + ':00' : request.donation_time;
                        const timeDate = new Date(`2000-01-01T${timeStr}`);
                        if (!isNaN(timeDate.getTime())) {
                            time = timeDate.toLocaleTimeString('en-US', {
                                hour: 'numeric',
                                minute: '2-digit',
                                hour12: true
                            });
                        }
                    } catch (e) {
                        console.error('Error formatting time:', request.donation_time, e);
                        time = request.donation_time; // Fallback to original value
                    }
                }

                row.innerHTML = `
                    <td>${request.donor_name}</td>
                    <td><span class="pill walk-in">Walk-in</span></td>
                    <td>${date}</td>
                    <td>${time}</td>
                    <td>Visit facility</td>
                    <td>
                        <button class="btn btn-primary btn-sm" onclick="openWalkInConfirmationModal(${request.id}, '${request.donor_name}', '${date}', '${time}')">
                            Validate Donation
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        // Wire filter controls when modal content is present
        (function wireWalkInFilter(){
            const applyBtn = document.getElementById('walkin-filter-apply');
            const clearBtn = document.getElementById('walkin-filter-clear');
            const fromInp = document.getElementById('walkin-filter-from');
            const toInp = document.getElementById('walkin-filter-to');
            if(!applyBtn || !clearBtn || !fromInp || !toInp) return; // modal not loaded yet

            // Convert Date -> yyyy-mm-dd
            const toYmd = (d)=>{
                const yy=d.getFullYear(); const mm=String(d.getMonth()+1).padStart(2,'0'); const dd=String(d.getDate()).padStart(2,'0');
                return `${yy}-${mm}-${dd}`;
            };

            // Defaults: current month
            try {
                const now = new Date();
                const start = new Date(now.getFullYear(), now.getMonth(), 1);
                const end = new Date(now.getFullYear(), now.getMonth()+1, 0);
                if(!fromInp.value) fromInp.value = toYmd(start);
                if(!toInp.value) toInp.value = toYmd(end);
            } catch(_){}

            const renderFiltered = ()=>{
                const tbody = document.getElementById('pending-walk-in-requests-tbody');
                if (!tbody) return;
                tbody.innerHTML = '';
                const from = fromInp.value ? new Date(fromInp.value+'T00:00:00') : null;
                const to = toInp.value ? new Date(toInp.value+'T23:59:59') : null;
                const src = (__pendingWalkInAll||[]).filter(r=>r.type==='walk_in');
                const list = src.filter(r=>{
                    try {
                        const d = new Date(r.donation_date+'T00:00:00');
                        if (from && d < from) return false;
                        if (to && d > to) return false;
                        return true;
                    } catch(_) { return true; }
                });
                if (!list.length) { tbody.innerHTML = '<tr><td colspan="6">No pending walk-in requests.</td></tr>'; return; }

                list.forEach(request=>{
                    const row = document.createElement('tr');
                    const date = new Date(request.donation_date).toLocaleDateString();
                    let time = 'TBD';
                    if (request.donation_time && request.donation_time !== '00:00:00' && request.donation_time !== '00:00') {
                        try {
                            const timeStr = request.donation_time.length === 5 ? request.donation_time + ':00' : request.donation_time;
                            const timeDate = new Date(`2000-01-01T${timeStr}`);
                            if (!isNaN(timeDate.getTime())) {
                                time = timeDate.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
                            }
                        } catch (e) { time = request.donation_time; }
                    }
                    row.innerHTML = `
                        <td>${request.donor_name}</td>
                        <td><span class="pill walk-in">Walk-in</span></td>
                        <td>${date}</td>
                        <td>${time}</td>
                        <td>Visit facility</td>
                        <td>
                            <button class="btn btn-primary btn-sm" onclick="openWalkInConfirmationModal(${request.id}, '${request.donor_name}', '${date}', '${time}')">Validate Donation</button>
                        </td>`;
                    tbody.appendChild(row);
                });
            };

            applyBtn.addEventListener('click', renderFiltered);
            clearBtn.addEventListener('click', function(){ fromInp.value=''; toInp.value=''; renderFiltered(); });

            // Expose for external refresh after data reload
            window.__refreshWalkInFilter = renderFiltered;
        })();

        // openWalkInConfirmationModal is defined in the pending-walk-in-requests partial.

        // scheduleHomeCollection and fetchHomeCollectionData functions are now in pending-home-collection-requests.blade.php

        function populateWalkInDonationsModal(donations) {
            const tbody = document.getElementById('walk-in-donations-tbody');
            if (!tbody) return;

            tbody.innerHTML = '';
            if (donations.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5">No walk-in donations recorded yet.</td></tr>';
                return;
            }

            donations.forEach(donation => {
                const row = document.createElement('tr');
                const date = new Date(donation.donation_date).toLocaleDateString();
                const time = new Date(`2000-01-01T${donation.donation_time}`).toLocaleTimeString('en-US', {
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true
                });

                row.innerHTML = `
                    <td>${donation.Full_Name || 'N/A'}</td>
                    <td><strong style="color: #ff69b4; font-size: 16px;">${donation.number_of_bags || 'N/A'}</strong> bags</td>
                    <td>${donation.total_volume || 'N/A'} ml</td>
                    <td>${date}</td>
                    <td>${time}</td>
                `;
                tbody.appendChild(row);
            });
        }

        function submitScreeningAction(action) {
            const comments = document.getElementById('screening-comments').value.trim();
            
            if (currentScreeningId) {
                const status = action === 'accept' ? 'accepted' : 'declined';
                updateScreeningStatus(status, comments);
                closeModal('health-screening-details-modal');
            }
        }



        function updateScreeningStatus(status, adminNotes) {
            fetch(`/health-screening/${currentScreeningId}/update-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status, admin_notes: adminNotes })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccessModal(`Health screening ${status} successfully!`);
                    closeModal('health-screening-details-modal');
                    // Refresh relevant views
                    if(status === 'accepted') loadReportData('accepted-screening');
                    if(status === 'declined') loadReportData('declined-screening');
                    loadReportData('users-undergo-screening');
                } else {
                    alert('Error updating status: ' + data.message);
                }
            });
        }

        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if(modal) modal.style.display = 'flex';
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if(modal) modal.style.display = 'none';
        }

        // Availability Calendar Modal logic
        // Vanilla month calendar (no external libs)
        var availabilityCalendar = null;
        function openAvailabilityCalendarModal() {
            const modalId = 'availability-calendar-modal';
            const modal = document.getElementById(modalId);
            if (!modal) return;
            openModal(modalId);
            // init calendar once modal is visible
            setTimeout(function(){
                try {
                    const container = document.getElementById('availability-calendar');
                    if (!container) return;
                    buildVanillaCalendar(container);
                } catch(e) { console.error('Failed to init availability calendar:', e); }
            }, 0);
        }
        function closeAvailabilityCalendarModal(){
            const modalId = 'availability-calendar-modal';
            closeModal(modalId);
            availabilityCalendar = null; // nothing to destroy in vanilla version
        }

        function showSuccessModal(titleOrMessage, maybeMessage) {
            var titleEl = document.getElementById('successModalTitle');
            var msgEl = document.getElementById('successModalMessage');
            var modalEl = document.getElementById('successModal');
            // Support both signatures: (message) or (title, message)
            var title = (maybeMessage !== undefined) ? String(titleOrMessage) : 'Success';
            var message = (maybeMessage !== undefined) ? String(maybeMessage) : String(titleOrMessage);
            if (titleEl) titleEl.textContent = title;
            if (msgEl) msgEl.textContent = message;
            if (modalEl) modalEl.style.display = 'block';
        }

        // Removed toast on login; success now shows inline alert above.

        function logout() {
            openModal('logoutModal');
            // Focus management: move focus to cancel by default
            setTimeout(function(){
                var cancelBtn = document.getElementById('logout-cancel-btn');
                if (cancelBtn) cancelBtn.focus();
            }, 0);
        }

        // Simple focus trap for the logout modal
        (function(){
            const modal = document.getElementById('logoutModal');
            const trapFocus = function(e){
                if (modal.style.display !== 'block') return;
                const focusable = modal.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
                const first = focusable[0];
                const last = focusable[focusable.length - 1];
                if (e.key === 'Tab') {
                    if (e.shiftKey && document.activeElement === first) { last.focus(); e.preventDefault(); }
                    else if (!e.shiftKey && document.activeElement === last) { first.focus(); e.preventDefault(); }
                }
                if (e.key === 'Escape') { closeModal('logoutModal'); }
            };
            document.addEventListener('keydown', trapFocus);
        })();

        function confirmLogout() {
            // Loading state
            var btn = document.getElementById('logout-confirm-btn');
            var spinner = document.getElementById('logout-spinner');
            var text = document.getElementById('logout-confirm-text');
            if (btn) btn.disabled = true;
            if (spinner) spinner.style.display = 'inline-block';
            if (text) text.textContent = 'Logging out...';

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.logout") }}';
            form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}">';
            document.body.appendChild(form);
            form.submit();
        }
        
        // Close modal if clicking outside of it
        window.onclick = function(event) {
            document.querySelectorAll('.modal').forEach(modal => {
                if (event.target == modal) {
                    // Allow overlay click to close only for logoutModal and non-critical modals
                    if (modal.id === 'logoutModal') {
                        closeModal(modal.id);
                    }
                }
            });
        }
    </script>
        
<script>
// Initialize bar chart after DOM ready, with fallback if Chart.js CDN fails
document.addEventListener('DOMContentLoaded', function() {
    // Support category query param (moved from stray top script)
    const urlParams = new URLSearchParams(window.location.search);
    const category = urlParams.get('category');
    if (category && typeof window.showCategory === 'function') {
        showCategory(category);
    }
    const ctx = document.getElementById('breastmilkTotalsChart');
    if (!ctx) return;

    function buildTotalsChart() {
        if (typeof Chart === 'undefined') return; // guard
        const walkIn  = {{ $walkInTotal }};
        const pickup  = {{ $pickupTotal }};
        const dataValues = (walkIn + pickup) === 0 ? [0,0] : [walkIn, pickup];
        window.breastmilkTotalsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Walk-in','Pickup'],
                datasets: [{
                    label: 'Donations',
                    data: dataValues,
                    backgroundColor: ['#37353E','#44444E'],
                    borderColor: ['#37353E','#44444E'],
                    borderWidth: 2,
                    borderRadius: 6,
                    maxBarThickness: 60
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: function(ctx){ const v=ctx.raw||0; return `${ctx.label}: ${v.toLocaleString()}`; } } }
                }
            }
        });

        // Health Screening Status Pie
        const pieEl = document.getElementById('hsStatusPieChart');
        if (pieEl) {
            const hsPending  = {{ $hsPendingActive }};
            const hsAccepted = {{ $hsAcceptedActive }};
            const hsDeclined = {{ $hsDeclinedActive }};
            const pieData = [hsPending, hsAccepted, hsDeclined];
            const pieColors = ['#F59E0B', '#10B981', '#EF4444']; // amber (pending), green (accepted), red (declined)
            window.hsStatusPieChart = new Chart(pieEl, {
                type: 'pie',
                data: {
                    labels: ['Pending','Accepted','Declined'],
                    datasets: [{
                        data: pieData,
                        backgroundColor: pieColors,
                        borderColor: 'transparent',
                        borderWidth: 0,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    aspectRatio: 1,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(ctx){
                                    const v = ctx.raw || 0; const total = (pieData.reduce((a,b)=>a+b,0) || 1);
                                    const pct = Math.round((v/total)*100);
                                    return `${ctx.label}: ${v.toLocaleString()} (${pct}%)`;
                                }
                            }
                        }
                    }
                }
            });
            // Initialize legend percentages
            updateHsPctLegend(pieData);
        }

        // Expose a refresher to update totals and chart after actions
        window.refreshAnalyticsTotals = function() {
            // Update cards
            fetch('/admin/analytics/totals', { headers: { 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(json => {
                    if (!json || json.success !== true) return;
                    const d = json.data || { donations: 0, requests: 0, screenings: 0 };
                    const dCountEl = document.getElementById('total-donations-count');
                    const rCountEl = document.getElementById('total-requests-count');
                    const sCountEl = document.getElementById('total-screenings-count');
                    if (dCountEl) dCountEl.textContent = (d.donations ?? 0).toLocaleString();
                    if (rCountEl) rCountEl.textContent = (d.requests ?? 0).toLocaleString();
                    if (sCountEl) sCountEl.textContent = (d.screenings ?? 0).toLocaleString();
                })
                .catch(console.error);

            // Update bar chart with walk-in vs pickup
            fetch('/admin/donations/stats', { headers: { 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(json => {
                    if (!json || json.success !== true) return;
                    const stats = json.data || {};
                    const chart = window.breastmilkTotalsChart;
                    if (chart) {
                        const values = [Number(stats.walk_in_donations || 0), Number(stats.home_collection_donations || 0)];
                        chart.data.datasets[0].data = values;
                        chart.data.labels = ['Walk-in','Pickup'];
                        chart.update();
                    }
                })
                .catch(console.error);

            // Update pie chart with live counts per status (exclude archived by default)
            const statusEndpoints = ['pending','accepted','declined'].map(function(st){ return `/admin/health-screening-data/${st}`; });
            Promise.all(statusEndpoints.map(url => fetch(url, { headers: { 'Accept': 'application/json' } }).then(r=>r.json()).catch(()=>null)))
                .then(results => {
                    if (!results || results.length !== 3) return;
                    const counts = results.map(r => (r && r.success && Array.isArray(r.data)) ? r.data.length : 0);
                    const pie = window.hsStatusPieChart;
                    if (pie) { pie.data.datasets[0].data = counts; pie.update(); }
                    // Update percentages legend as well
                    updateHsPctLegend(counts);
                })
                .catch(console.error);
        };
    }

    // If Chart.js (from CDN) is missing, load a fallback from unpkg
    if (typeof Chart === 'undefined') {
        const fallback = document.createElement('script');
        fallback.src = 'https://unpkg.com/chart.js@4.4.1/dist/chart.umd.js';
        fallback.onload = buildTotalsChart;
        fallback.onerror = function(){ console.error('Chart.js failed to load from both primary CDN and fallback.'); };
        document.head.appendChild(fallback);
    } else {
        buildTotalsChart();
    }

    // Initialize Monthly Line Chart
    (function initMonthlyLine(){
        const lineEl = document.getElementById('donationsRequestsLineChart');
        if (!lineEl) return;
        const yearSelect = document.getElementById('monthly-year-select');
        const fetchAndRender = function(year){
            fetch(`/admin/analytics/monthly?year=${encodeURIComponent(year)}`, { headers: { 'Accept':'application/json' }})
                .then(r=>r.json())
                .then(json=>{
                    if (!json || json.success !== true) return;
                    const d = json.data || {};
                    const labels = d.labels || [];
                    const dsDon = d.donations || [];
                    const dsReq = d.requests || [];

                    if (!window.donReqLineChart) {
                        window.donReqLineChart = new Chart(lineEl, {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [
                                    {
                                        label: 'Donations',
                                        data: dsDon,
                                        borderColor: '#2563EB',
                                        backgroundColor: 'rgba(37, 99, 235, 0.15)',
                                        tension: 0.3,
                                        fill: true,
                                        pointRadius: 3
                                    },
                                    {
                                        label: 'Requests',
                                        data: dsReq,
                                        borderColor: '#DC2626',
                                        backgroundColor: 'rgba(220, 38, 38, 0.15)',
                                        tension: 0.3,
                                        fill: true,
                                        pointRadius: 3
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    y: { beginAtZero: true, ticks: { precision: 0 } }
                                },
                                plugins: { legend: { position: 'bottom' } }
                            }
                        });
                    } else {
                        const chart = window.donReqLineChart;
                        chart.data.labels = labels;
                        chart.data.datasets[0].data = dsDon;
                        chart.data.datasets[1].data = dsReq;
                        chart.update();
                    }
                })
                .catch(console.error);
        };
        const initialYear = yearSelect ? yearSelect.value : (new Date()).getFullYear();
        fetchAndRender(initialYear);
        if (yearSelect) {
            yearSelect.addEventListener('change', function(){ fetchAndRender(this.value); });
        }
    })();
});
</script>
<script>
// Helper to update health screening percentage legend under the pie chart
function updateHsPctLegend(arr){
    try {
        if (!Array.isArray(arr) || arr.length < 3) return;
        var total = (arr[0]||0)+(arr[1]||0)+(arr[2]||0);
        var p = total>0 ? Math.round((arr[0]/total)*100) : 0;
        var a = total>0 ? Math.round((arr[1]/total)*100) : 0;
        var d = total>0 ? Math.round((arr[2]/total)*100) : 0;
        var pe = document.getElementById('hs-pending-pct');
        var ae = document.getElementById('hs-accepted-pct');
        var de = document.getElementById('hs-declined-pct');
        if (pe) pe.textContent = p + '%';
        if (ae) ae.textContent = a + '%';
        if (de) de.textContent = d + '%';
    } catch(_) {}
}
</script>
    <!-- Availability Calendar Modal -->
    <div id="availability-calendar-modal" class="modal" role="dialog" aria-modal="true" aria-labelledby="availability-calendar-title">
        <div class="modal-content" style="max-width: 800px;">
            <div class="modal-header">
                <h3 class="modal-title" id="availability-calendar-title">Admin Availability Calendar</h3>
                <button class="close-btn" aria-label="Close" onclick="closeAvailabilityCalendarModal()" type="button">&times;</button>
            </div>
            <div class="modal-body">
                <p style="margin:0 0 10px; color:#6b7280;">Click a date to set availability and time slots.</p>
                <div id="availability-calendar" class="vc"></div>
            </div>
        </div>
    </div>

    <script>
    // Vanilla Calendar Implementation
    function buildVanillaCalendar(root){
        // Clear root
        root.innerHTML = '';
        const header = document.createElement('div');
        header.className = 'vc-header';
        const title = document.createElement('div');
        title.className = 'vc-title';
        const nav = document.createElement('div');
        nav.className = 'vc-nav';
        const btnPrev = document.createElement('button'); btnPrev.className='vc-btn'; btnPrev.textContent = 'Prev';
        const btnToday = document.createElement('button'); btnToday.className='vc-btn'; btnToday.textContent = 'Today';
        const btnNext = document.createElement('button'); btnNext.className='vc-btn'; btnNext.textContent = 'Next';
        nav.appendChild(btnPrev); nav.appendChild(btnToday); nav.appendChild(btnNext);
        header.appendChild(title); header.appendChild(nav);
        root.appendChild(header);

        const grid = document.createElement('div');
        grid.className = 'vc-grid';
        root.appendChild(grid);

        const weekdays = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
        weekdays.forEach(function(w){ const el=document.createElement('div'); el.className='vc-weekday'; el.textContent=w; grid.appendChild(el); });

        let current = new Date();

        function render(){
            // Remove previous day cells
            grid.querySelectorAll('.vc-day').forEach(el=>el.remove());
            // Title
            title.textContent = current.toLocaleString('en-US', { month:'long', year:'numeric' });
            // First day of month
            const first = new Date(current.getFullYear(), current.getMonth(), 1);
            const startDay = first.getDay();
            const daysInMonth = new Date(current.getFullYear(), current.getMonth()+1, 0).getDate();
            const prevMonthDays = new Date(current.getFullYear(), current.getMonth(), 0).getDate();

            // Fill leading days (previous month)
            for (let i=0; i<startDay; i++){
                const d = document.createElement('div'); d.className='vc-day out';
                const dateNum = prevMonthDays - startDay + 1 + i;
                d.innerHTML = '<div class="vc-date">'+dateNum+'</div>';
                grid.appendChild(d);
            }
            // Month days
            const dayCells = [];
            for (let day=1; day<=daysInMonth; day++){
                const cell = document.createElement('div'); cell.className='vc-day';
                const dateStr = toYmd(new Date(current.getFullYear(), current.getMonth(), day));
                cell.setAttribute('data-date', dateStr);
                const dateEl = document.createElement('div'); dateEl.className='vc-date'; dateEl.textContent = String(day);
                cell.appendChild(dateEl);
                if (isToday(current.getFullYear(), current.getMonth(), day)) cell.classList.add('today');
                cell.addEventListener('click', function(){ openAvailabilityModal(dateStr); });
                grid.appendChild(cell); dayCells.push(cell);
            }
            // Trailing days (next month) to complete weeks
            const totalCells = startDay + daysInMonth;
            const trailing = (7 - (totalCells % 7)) % 7;
            for (let i=1; i<=trailing; i++){
                const d = document.createElement('div'); d.className='vc-day out';
                d.innerHTML = '<div class="vc-date">'+i+'</div>';
                grid.appendChild(d);
            }

            // Fetch availability to mark days
            fetch('{{ route("admin.availability.get") }}')
                .then(r=>r.json())
                .then(list=>{
                    const items = Array.isArray(list) ? list : [];
                    const available = new Set(items.filter(x=>x && x.date).map(x=>x.date));
                    dayCells.forEach(function(c){ if (available.has(c.getAttribute('data-date'))) c.classList.add('available'); });
                })
                .catch(()=>{});
        }

        btnPrev.addEventListener('click', function(){ current.setMonth(current.getMonth()-1); render(); });
        btnNext.addEventListener('click', function(){ current.setMonth(current.getMonth()+1); render(); });
        btnToday.addEventListener('click', function(){ current = new Date(); render(); });

        render();
    }

    function toYmd(d){ const m = String(d.getMonth()+1).padStart(2,'0'); const day = String(d.getDate()).padStart(2,'0'); return d.getFullYear()+ '-' + m + '-' + day; }
    function isToday(y,m,d){ const t=new Date(); return t.getFullYear()===y && t.getMonth()===m && t.getDate()===d; }
    </script>
</body>
</html>
