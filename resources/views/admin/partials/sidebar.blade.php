<div class="sidebar angle-135 brand-rose-dark" id="sidebar">
    <div class="logo-container">
        <img src="{{ asset('logo.png') }}" alt="Logo" class="logo">
        <div class="admin-label" style="color:#000; font-weight:700;">ADMIN</div>
    </div>

    <nav class="nav-menu">
        <a href="{{ route('admin.dashboard') }}"
            class="nav-item {{ request()->routeIs('admin.dashboard*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-home"></i></span>
            <span class="nav-text">Dashboard</span>
        </a>
        <a href="{{ route('admin.health-screening', ['view' => 'pending']) }}"
            class="nav-item {{ request()->routeIs('admin.health-screening*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-stethoscope"></i></span>
            <span class="nav-text">Health Screening</span>
            @if(($adminSidebarCounts['hs_pending'] ?? 0) > 0)
                <span id="badge-hs-pending" class="nav-badge"
                    aria-label="{{ $adminSidebarCounts['hs_pending'] }} pending health screenings">{{ $adminSidebarCounts['hs_pending'] }}</span>
            @endif
        </a>

        <!-- Other Admin Links -->
        <a href="{{ route('admin.milk-requests', ['view' => 'pending']) }}"
            class="nav-item {{ request()->routeIs('admin.milk-requests*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-baby"></i></span>
            <span class="nav-text">Breastmilk Request</span>
            @if(($adminSidebarCounts['bm_requests_pending'] ?? 0) > 0)
                <span id="badge-requests-pending" class="nav-badge"
                    aria-label="{{ $adminSidebarCounts['bm_requests_pending'] }} pending breastmilk requests">{{ $adminSidebarCounts['bm_requests_pending'] }}</span>
            @endif
        </a>
        <a href="{{ route('admin.donations') }}"
            class="nav-item {{ request()->routeIs('admin.donations*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-file-medical"></i></span>
            <span class="nav-text">Breastmilk Donation</span>
            @if(($adminSidebarCounts['donations_pending'] ?? 0) > 0)
                <span id="badge-donations-pending" class="nav-badge"
                    aria-label="{{ $adminSidebarCounts['donations_pending'] }} pending donations">{{ $adminSidebarCounts['donations_pending'] }}</span>
            @endif
        </a>
        <a href="{{ route('admin.milk-inventory') }}"
            class="nav-item {{ request()->routeIs('admin.milk-inventory*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-warehouse"></i></span>
            <span class="nav-text">Inventory</span>
        </a>
        <a href="{{ route('admin.reports', ['tab' => 'monthly']) }}"
            class="nav-item {{ request()->routeIs('admin.reports*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-chart-bar"></i></span>
            <span class="nav-text">Monthly Reports</span>
        </a>
        <a href="{{ route('admin.settings') }}"
            class="nav-item {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-cog"></i></span>
            <span class="nav-text">Settings</span>
        </a>
    </nav>
</div>