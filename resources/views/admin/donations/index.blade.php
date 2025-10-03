<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Breastmilk Donation Management</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css?v=20250921') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        window.__DONATION_TAB_COUNTS__ = @json($tabCounts);
    </script>
    <style>
        body {
            background: #f8f9fa;
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .content {
            padding: 22px;
        }

        /* Tab styling (match admin patterns) */
        .tabs {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin: 0 0 18px;
            position: relative;
        }

        .tab {
            --tab-bg: #ffffff;
            --tab-border: #e5e7eb;
            --tab-color: #000;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 999px;
            background: var(--tab-bg);
            border: 1px solid var(--tab-border);
            color: var(--tab-color);
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
            line-height: 1;
            position: relative;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: background .35s, color .25s, box-shadow .35s, transform .35s, border-color .35s;
        }

        .tab .count {
            background: #f1f3f5;
            color: #444;
            padding: 3px 8px;
            border-radius: 999px;
            font-weight: 600;
            font-size: 12px;
            line-height: 1;
            transition: background .35s, color .35s;
        }

        .tab:hover {
            background: #fff7fb;
            border-color: #ffc1d8;
            box-shadow: 0 4px 10px rgba(255, 105, 180, 0.18);
            transform: translateY(-2px);
        }

        .tab.active {
            background: linear-gradient(135deg, #ffb6ce, #ff69b4);
            color: #000 !important;
            border-color: #ff69b4;
            font-weight: 700;
            box-shadow: 0 6px 16px rgba(255, 105, 180, 0.35);
        }

        .tab.active .count {
            background: #f1f3f5;
            color: #000 !important;
        }

        .tabs::after {
            content: "";
            position: absolute;
            inset: auto 0 -6px 0;
            height: 2px;
            background: linear-gradient(90deg, #ffd1e6, #ff69b4, #ffd1e6);
            border-radius: 2px;
            opacity: .4;
            pointer-events: none;
        }

        /* Cards and table */
        .section-card {
            background: #fff;
            border: 1px solid #eee;
            border-radius: 12px;
            padding: 18px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .04);
        }

        .card {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            overflow: hidden;
        }

        .card-header {
            padding: 12px 14px;
            border-bottom: 1px solid #e9ecef;
            font-weight: 600;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            text-align: left;
            padding: 10px 12px;
            border-bottom: 1px solid #f1f3f5;
            font-size: 14px;
        }

        /* Pink theme reused */
        .pink-card {
            background: #ffe3ef;
            border: 1px solid #ffc9dd;
            border-radius: 16px;
            padding: 14px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .data-table.theme-pink {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
        }

        .data-table.theme-pink thead th {
            background: #ffc0d4;
            color: #333;
            padding: 12px;
            font-weight: 700;
            border-bottom: 1px solid #f1a9c1;
        }

        .data-table.theme-pink thead th:nth-child(2) {
            background: #ff9fc1;
            color: #fff;
        }

        .data-table.theme-pink tbody td {
            padding: 12px;
            border-bottom: 1px solid #f6c8d7;
        }

        .data-table.theme-pink tbody tr:last-child td {
            border-bottom: none;
        }

        .data-table.theme-pink tbody td:nth-child(2) {
            color: #ff4d6d;
            font-weight: 700;
        }

        .empty {
            padding: 18px;
            text-align: center;
            color: #666;
        }

        .badge {
            display: inline-block;
            padding: 3px 7px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 999px;
        }

        .badge.walk_in {
            background: #e7f5ff;
            color: #1c7ed6;
            border: 1px solid #d0ebff;
        }

        .badge.home_collection {
            background: #fff8e1;
            color: #ad6800;
            border: 1px solid #ffe7a3;
        }

        .btn-sm {
            padding: 6px 10px;
            font-size: 12px;
            border-radius: 6px;
            border: 1px solid #e9ecef;
            background: #fff;
            cursor: pointer;
        }

        .btn-success {
            border-color: #28a745;
            background-color: #28a745;
            color: #fff;
        }

        .btn-success:hover {
            filter: brightness(0.95);
        }

        .btn-danger {
            border-color: #dc3545;
            background-color: #dc3545;
            color: #fff;
        }

        .btn-danger:hover {
            filter: brightness(0.95);
        }

        .controls-inline {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .controls-inline input[type="date"],
        .controls-inline input[type="time"] {
            padding: 6px 8px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 12px;
        }

        .assign-pickup-wrapper {
            font-size: 13px;
        }

        .assign-pickup-wrapper .ap-donor {
            margin-bottom: 4px;
        }

        .assign-pickup-wrapper .ap-grid {
            display: flex;
            gap: 16px;
            align-items: flex-end;
        }

        .assign-pickup-wrapper .ap-field {
            display: flex;
            flex-direction: column;
            gap: 4px;
            flex: 1;
        }

        .assign-pickup-wrapper .ap-label {
            font-size: 12px;
            font-weight: 600;
        }

        .assign-pickup-wrapper input[type=date],
        .assign-pickup-wrapper input[type=time] {
            padding: 6px 8px;
            font-size: 13px;
            border: 1px solid #ccc;
            border-radius: 4px;
            height: 34px;
        }

        .assign-pickup-wrapper #ap-inline-msg {
            margin-top: 6px;
            font-size: 12px;
            font-weight: 600;
            color: #d9534f;
            display: none;
        }
    </style>
</head>

<body>
    @include('admin.partials.sidebar')
    <div class="main-content top-bar-space">
        @include('admin.partials.top-bar', ['pageTitle' => 'Breastmilk Donation Management', 'pageTitleId' => 'pageTitle'])

        <div class="content">
            <nav class="tabs" aria-label="Breastmilk Donation views" role="tablist">
                <a class="tab" id="tab-pending" href="{{ route('admin.donations') }}?view=pending">Pending <span
                        class="count" id="count-tab-pending">{{ $tabCounts['pending'] ?? 0 }}</span></a>
                <a class="tab" id="tab-scheduled" href="{{ route('admin.donations') }}?view=scheduled">Scheduled Pickups
                    <span class="count" id="count-tab-scheduled">{{ $tabCounts['scheduled'] ?? 0 }}</span></a>
                <a class="tab" id="tab-completed-walkin"
                    href="{{ route('admin.donations') }}?view=completed-walkin">Completed Walk-in <span class="count"
                        id="count-tab-walkin">{{ $tabCounts['completed_walkin'] ?? 0 }}</span></a>
                <a class="tab" id="tab-completed-pickup"
                    href="{{ route('admin.donations') }}?view=completed-pickup">Completed Home Collection <span
                        class="count" id="count-tab-pickup">{{ $tabCounts['completed_pickup'] ?? 0 }}</span></a>
                <a class="tab" id="tab-archived" href="{{ route('admin.donations') }}?view=archived">Archived <span
                        class="count" id="count-tab-archived">{{ $tabCounts['archived'] ?? 0 }}</span></a>
            </nav>

            <!-- Pending -->
            <div class="card pink-card" id="panel-pending" style="display:none;">
                <div class="card-header">Pending Requests</div>
                <div style="overflow-x:auto;">
                    <table class="data-table theme-pink">
                        <thead>
                            <tr>
                                <th>Donor</th>
                                <th>Type</th>
                                <th>Bags</th>
                                <th>Total Volume (ml)</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Address</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="donations-pending-tbody"></tbody>
                    </table>
                </div>
            </div>

            <!-- Scheduled -->
            <div class="card pink-card" id="panel-scheduled" style="display:none;">
                <div class="card-header">Scheduled Home Collection Pickup</div>
                <div style="overflow-x:auto;">
                    <table class="data-table theme-pink">
                        <thead>
                            <tr>
                                <th>Donor</th>
                                <th>Bags</th>
                                <th>Total Volume (ml)</th>
                                <th>Pickup Date</th>
                                <th>Pickup Time</th>
                                <th>Address</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="donations-scheduled-tbody"></tbody>
                    </table>
                </div>
            </div>

            <!-- Completed Walk-in -->
            <div class="card pink-card" id="panel-completed-walkin" style="display:none;">
                <div class="card-header">Completed Walk-in Donations</div>
                <div style="overflow-x:auto;">
                    <table class="data-table theme-pink">
                        <thead>
                            <tr>
                                <th>Donor</th>
                                <th>Bags</th>
                                <th>Total Volume (ml)</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="donations-walkin-completed-tbody"></tbody>
                    </table>
                </div>
            </div>

            <!-- Completed Pickup -->
            <div class="card pink-card" id="panel-completed-pickup" style="display:none;">
                <div class="card-header">Completed Home Collection Donation</div>
                <div style="overflow-x:auto;">
                    <table class="data-table theme-pink">
                        <thead>
                            <tr>
                                <th>Donor</th>
                                <th>Address</th>
                                <th>Bags</th>
                                <th>Total Volume (ml)</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="donations-pickup-completed-tbody"></tbody>
                    </table>
                </div>
            </div>

            <!-- Archived -->
            <div class="card pink-card" id="panel-archived" style="display:none;">
                <div class="card-header">Archived Donations</div>
                <div
                    style="padding:10px 12px; border-bottom:1px solid #e9ecef; display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                    <input id="archived-q" type="text" placeholder="Search"
                        style="padding:8px 10px; border:1px solid #ddd; border-radius:6px; min-width:200px;">
                    <select id="archived-type" style="padding:8px 10px; border:1px solid #ddd; border-radius:6px;">
                        <option value="">All</option>
                        <option value="walk_in">Walk-in</option>
                        <option value="home_collection">Home collection</option>
                    </select>
                    <select id="archived-year" style="padding:8px 10px; border:1px solid #ddd; border-radius:6px;">
                        <option value="">All Years</option>
                    </select>
                    <select id="archived-month" style="padding:8px 10px; border:1px solid #ddd; border-radius:6px;">
                        <option value="">All Months</option>
                        <option value="1">January</option>
                        <option value="2">February</option>
                        <option value="3">March</option>
                        <option value="4">April</option>
                        <option value="5">May</option>
                        <option value="6">June</option>
                        <option value="7">July</option>
                        <option value="8">August</option>
                        <option value="9">September</option>
                        <option value="10">October</option>
                        <option value="11">November</option>
                        <option value="12">December</option>
                    </select>
                    <button id="archived-apply" class="btn-sm" style="padding:8px 14px;">Apply</button>
                </div>
                <div style="overflow-x:auto;">
                    <table class="data-table theme-pink">
                        <thead>
                            <tr>
                                <th>Donor</th>
                                <th>Type</th>
                                <th>Bags</th>
                                <th>Total Volume (ml)</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Address</th>
                                <th>Archived At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="donations-archived-tbody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('admin.pending-walk-in-requests')
    @include('admin.validate-walk-in')
    @include('admin.pending-home-collection-requests')
    @include('admin.scheduled-home-collection-pickup')
    @include('admin.donations')

    <!-- Success Modal (reused) -->
    <div id="successModal" class="modal">
        <div class="modal-content" style="max-width: 420px; text-align: center;">
            <div class="modal-header">
                <h3 class="modal-title" id="successModalTitle">Success</h3>
                <button class="close-btn" onclick="closeModal('successModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div style="font-size: 48px; margin-bottom: 20px; color: #28a745;"><i class="fas fa-check-circle"></i>
                </div>
                <p id="successModalMessage" style="font-size: 16px; color: #333; margin-bottom: 20px;"></p>
                <button onclick="closeModal('successModal')" class="btn btn-primary"
                    style="padding: 10px 30px;">OK</button>
            </div>
        </div>
    </div>

    <script>
        // Shared layout helpers
        function toggleSidebar() { if (typeof window.toggleSidebar === 'function') { return window.toggleSidebar(); } }
        function openModal(id) { var el = document.getElementById(id); if (el) el.style.display = 'flex'; }
        function closeModal(id) { var el = document.getElementById(id); if (el) el.style.display = 'none'; }
        function logout() { openModal('logoutModal'); setTimeout(function () { var c = document.getElementById('logout-cancel-btn'); if (c) c.focus(); }, 0); }
        function confirmLogout() {
            var btn = document.getElementById('logout-confirm-btn');
            var spinner = document.getElementById('logout-spinner');
            var text = document.getElementById('logout-confirm-text');
            if (btn) btn.disabled = true;
            if (spinner) spinner.style.display = 'inline-block';
            if (text) text.textContent = 'Logging out...';
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('admin.logout') }}';
            form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}">';
            document.body.appendChild(form);
            form.submit();
        }
        function showSuccessModal(titleOrMessage, maybeMessage) {
            var titleEl = document.getElementById('successModalTitle');
            var msgEl = document.getElementById('successModalMessage');
            var modalEl = document.getElementById('successModal');
            var title = (maybeMessage !== undefined) ? String(titleOrMessage) : 'Success';
            var message = (maybeMessage !== undefined) ? String(maybeMessage) : String(titleOrMessage);
            if (titleEl) titleEl.textContent = title; if (msgEl) msgEl.textContent = message; if (modalEl) modalEl.style.display = 'flex';
        }

        function getParam(name) { const url = new URL(window.location.href); return url.searchParams.get(name); }
        function setActiveTab(tab) {
            const tabs = ['pending', 'scheduled', 'completed-walkin', 'completed-pickup', 'archived'];
            tabs.forEach(t => {
                const a = document.getElementById('tab-' + t.replace(/_/g, '-')) || document.getElementById('tab-' + t);
                const p = document.getElementById('panel-' + t.replace(/_/g, '-')) || document.getElementById('panel-' + t);
                if (a) a.classList.toggle('active', t === tab);
                if (p) p.style.display = t === tab ? 'block' : 'none';
            });
        }

        function formatDateStr(s) { try { return s ? new Date(s).toLocaleDateString() : 'N/A'; } catch (e) { return s || 'N/A'; } }
        function formatTimeStr(t) { if (!t) return 'TBD'; try { const s = t.length === 5 ? t + ':00' : t; const d = new Date('2000-01-01T' + s); if (!isNaN(d.getTime())) { return d.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true }); } } catch (e) { } return t; }

        function setBadge(id, value) { const el = document.getElementById(id); if (el) el.textContent = String(value); }

        function deleteArchivedDonation(id) {
            if (window.Swal && window.saConfirm) {
                saConfirm({ title: 'Delete this donation permanently?', icon: 'warning', confirmButtonText: 'Delete', confirmButtonColor: '#dc3545' }).then(r => {
                    if (r.isConfirmed) {
                        fetch(`/admin/donations/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } })
                            .then(r => r.json()).then(d => {
                                if (d && d.success) {
                                    // Prefer SweetAlert modal if available
                                    if (window.Swal) {
                                        Swal.fire({ icon: 'success', title: 'Deleted', text: 'Donation deleted successfully', confirmButtonText: 'OK' }).then(() => { loadArchived(); refreshCounts(); });
                                    } else {
                                        showSuccessModal('Success', 'Donation deleted successfully');
                                        loadArchived(); refreshCounts();
                                    }
                                } else {
                                    const msg = (d && d.message) ? d.message : 'Failed to delete donation';
                                    if (window.Swal) Swal.fire({ icon: 'error', title: 'Error', text: msg });
                                }
                            }).catch(() => {
                                if (window.Swal) Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to delete donation' });
                            });
                    }
                });
                return;
            }
            if (!confirm('Delete this archived donation permanently?')) return;
            fetch(`/admin/donations/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } })
                .then(() => { loadArchived(); refreshCounts(); });
        }

        function loadPending() {
            const tbody = document.getElementById('donations-pending-tbody'); if (!tbody) return; tbody.innerHTML = '';
            // Fetch combined pending requests (walk-in + home collection)
            fetch('/admin/walk-in-requests/pending', { cache: 'no-store' }).then(r => r.json()).then(d => {
                const rows = (d && d.success && Array.isArray(d.data)) ? d.data : [];
                if (!rows.length) { tbody.innerHTML = '<tr><td colspan="8" class="empty">No pending requests.</td></tr>'; setBadge('count-tab-pending', 0); return; }
                rows.forEach(x => {
                    const tr = document.createElement('tr');
                    const date = formatDateStr(x.donation_date);
                    const time24 = (x.donation_time && x.donation_time.length > 5) ? x.donation_time.substring(0, 5) : (x.donation_time || '');
                    const timeDisp = formatTimeStr(x.donation_time);
                    const displayDate = x.type === 'home_collection' ? '—' : date;
                    const displayTime = x.type === 'home_collection' ? '—' : timeDisp;
                    const typeBadge = `<span class="badge ${x.type}">${x.type === 'walk_in' ? 'Walk-in' : 'Home collection'}</span>`;
                    const addr = (x.pickup_address || x.address || '—');
                    const bags = x.type === 'home_collection' && x.number_of_bags ? x.number_of_bags : '—';
                    const vol = x.type === 'home_collection' && x.total_volume ? x.total_volume : '—';
                    let actions = '';
                    if (x.type === 'walk_in') {
                        actions = `<div class="controls-inline" style="justify-content:flex-end;">
                            <button class="btn-sm btn-success" onclick="openWalkInValidationModal(${x.id}, '${(x.donor_name || '').replace(/'/g, "\\'")}', '${x.donation_date || ''}', '${time24 || ''}')">Validate</button>
                        </div>`;
                    } else {
                        actions = `<div class=\"controls-inline\" style=\"justify-content:flex-end;\">
                            <button class=\"btn-sm btn-success\" onclick=\"openAssignPickupModal(${x.id}, '${(x.donor_name || '').replace(/'/g, "\\'")}')\">Assign</button>
                        </div>`;
                    }
                    tr.innerHTML = `<td>${x.donor_name || 'N/A'}</td>
                                    <td>${typeBadge}</td>
                                    <td>${bags}</td>
                                    <td>${vol !== '—' ? vol + 'ml' : '—'}</td>
                                    <td>${displayDate}</td>
                                    <td>${displayTime}</td>
                                    <td title="${addr}" style="max-width:220px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">${addr}</td>
                                    <td>${actions}</td>`;
                    tbody.appendChild(tr);
                });
                setBadge('count-tab-pending', rows.length);
            }).catch(() => { tbody.innerHTML = '<tr><td colspan="8" class="empty">Failed to load.</td></tr>'; });
        }

        // Enhanced Assign Pickup Modal (SweetAlert)
        function openAssignPickupModal(requestId, donorName) {
            const now = new Date();
            const today = now.toISOString().split('T')[0];
            if (!window.Swal) {
                const d = prompt('Enter pickup date (YYYY-MM-DD):'); if (!d) return;
                const t = prompt('Enter pickup time (HH:MM 24h):'); if (!t) return;
                return submitAssignPickup(requestId, donorName, d, t);
            }
            const html = `
                <div class="assign-pickup-wrapper">
                    <div class="ap-donor"><strong>${donorName}</strong></div>
                    <div class="ap-grid">
                        <label class="ap-field">
                            <span class="ap-label">Pickup Date <span class="req" title="Required">*</span></span>
                            <input type="date" id="swal-pickup-date" min="${today}" required />
                        </label>
                        <label class="ap-field">
                            <span class="ap-label">Pickup Time <span class="req" title="Required">*</span></span>
                            <input type="time" id="swal-pickup-time" required />
                        </label>
                    </div>
                    <div id="ap-inline-msg" class="ap-inline-msg" role="alert"></div>
                </div>`;
            Swal.fire({
                title: 'Assign Pickup',
                html: html,
                width: 460,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Assign',
                didOpen: () => {
                    const dateEl = document.getElementById('swal-pickup-date');
                    const timeEl = document.getElementById('swal-pickup-time');
                    if (dateEl) dateEl.value = today;
                    const setMinTime = () => {
                        if (!dateEl || !timeEl) return;
                        if (dateEl.value === today) {
                            const min = new Date(Date.now() + 30 * 60000);
                            const hh = String(min.getHours()).padStart(2, '0');
                            const mm = String(min.getMinutes()).padStart(2, '0');
                            timeEl.min = hh + ':' + mm;
                        } else { timeEl.removeAttribute('min'); }
                    };
                    dateEl && dateEl.addEventListener('change', () => { setMinTime(); validateAssignForm(); });
                    timeEl && timeEl.addEventListener('input', validateAssignForm);
                    setMinTime();
                },
                preConfirm: () => {
                    const d = (document.getElementById('swal-pickup-date') || {}).value;
                    const t = (document.getElementById('swal-pickup-time') || {}).value;
                    const valid = validateAssignForm();
                    if (!valid) return false; return { d, t };
                }
            }).then(res => { if (res.isConfirmed && res.value) { submitAssignPickup(requestId, donorName, res.value.d, res.value.t); } });
        }

        function validateAssignForm() {
            const msgEl = document.getElementById('ap-inline-msg');
            const dEl = document.getElementById('swal-pickup-date');
            const tEl = document.getElementById('swal-pickup-time');
            if (!dEl || !tEl) return true;
            const show = (m) => { if (msgEl) { msgEl.textContent = m; msgEl.style.display = 'block'; } };
            const hide = () => { if (msgEl) { msgEl.textContent = ''; msgEl.style.display = 'none'; } };
            const d = dEl.value, t = tEl.value; if (!d || !t) { show('Please select both date and time.'); return false; }
            try { const now = new Date(); const sel = new Date(d + 'T' + t + ':00'); if (isNaN(sel.getTime())) { show('Invalid date/time.'); return false; } const today = now.toISOString().split('T')[0]; if (d === today) { const min = new Date(Date.now() + 30 * 60000); if (sel < min) { show('Time must be at least 30 minutes from now.'); return false; } } } catch (e) { show('Invalid date/time.'); return false; }
            hide(); return true;
        }

        function submitAssignPickup(requestId, donorName, pickupDate, pickupTime) {
            const fmt12h = (t) => { try { const [h, m] = String(t).split(':'); let H = parseInt(h, 10) || 0; const ampm = H >= 12 ? 'PM' : 'AM'; H = H % 12; if (H === 0) H = 12; return `${H}:${m} ${ampm}`; } catch (_) { return t; } };
            fetch(`/admin/home-collection/${requestId}/schedule`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content'), 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, body: JSON.stringify({ collection_date: pickupDate, collection_time: pickupTime }) })
                .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
                .then(data => { if (data && data.success) { if (window.Swal) { Swal.fire({ icon: 'success', title: 'Assigned', text: `Pickup scheduled on ${new Date(pickupDate).toLocaleDateString()} at ${fmt12h(pickupTime)}.` }); } else { alert('Pickup assigned successfully.'); } loadPending(); refreshCounts(); } else { const msg = (data && data.message) ? data.message : 'Failed to assign'; if (window.Swal) Swal.fire({ icon: 'error', title: 'Error', text: msg }); else alert(msg); } })
                .catch(err => { if (window.Swal) Swal.fire({ icon: 'error', title: 'Error assigning pickup', text: err.message }); else alert('Error: ' + err.message); });
        }

        function loadScheduled() {
            const tbody = document.getElementById('donations-scheduled-tbody'); if (!tbody) return; tbody.innerHTML = '';
            fetch('/admin/home-collection-requests/scheduled', { cache: 'no-store' }).then(r => r.json()).then(d => {
                const rows = (d.success && Array.isArray(d.data)) ? d.data : [];
                if (!rows.length) { tbody.innerHTML = '<tr><td colspan="7" class="empty">No scheduled pickups.</td></tr>'; return; }
                rows.forEach(x => {
                    const tr = document.createElement('tr');
                    const date = formatDateStr(x.scheduled_date || x.collection_date);
                    const time = formatTimeStr(x.scheduled_time || x.collection_time);
                    const addr = x.pickup_address || x.address || '—';
                    const bagsId = 'bags-' + x.id; const volId = 'volume-' + x.id;
                    const donor = (x.donor_name || x.Full_Name || 'N/A').replace(/'/g, "\\'");
                    const bagsVal = x.number_of_bags || x.Number_of_Bags || '';
                    const volVal = x.total_volume || x.Total_Volume || x.total_volume_donated || '';
                    tr.innerHTML = `<td>${donor}</td>
                                    <td><input type="number" id="${bagsId}" value="${bagsVal}" min="1" style="width:70px; padding:4px; border:1px solid #ddd; border-radius:4px; text-align:center;"></td>
                                    <td><input type="number" id="${volId}" value="${volVal}" min="1" style="width:90px; padding:4px; border:1px solid #ddd; border-radius:4px; text-align:center;"> <small style="color:#666; font-size:10px;">ml</small></td>
                                    <td>${date}</td>
                                    <td>${time}</td>
                                    <td title="${addr}" style="max-width:220px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">${addr}</td>
                                    <td><button class="btn-sm btn-success" onclick="confirmPickupDirectly(${x.id}, '${donor}', '${x.scheduled_date || x.collection_date || ''}', '${x.scheduled_time || x.collection_time || ''}')">Confirm Pickup</button></td>`;
                    tbody.appendChild(tr);
                });
                setBadge('count-tab-scheduled', rows.length);
            }).catch(() => { tbody.innerHTML = '<tr><td colspan="7" class="empty">Failed to load.</td></tr>'; });
        }

        function archiveDonation(id, sourceTab) {
            fetch(`/admin/donations/${id}/archive`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } })
                .then(r => r.json()).then(d => {
                    if (d.success) {
                        // Always take the admin to the Archived tab so they can see the item move
                        setActiveTab('archived');
                        loadArchived();
                        // Optionally also refresh the source tab list in the background
                        if (sourceTab === 'walkin') { loadCompletedWalkIn(); }
                        else if (sourceTab === 'pickup') { loadCompletedPickup(); }
                        refreshCounts();
                    }
                });
        }

        function unarchiveDonation(id) {
            fetch(`/admin/donations/${id}/unarchive`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } })
                .then(r => r.json()).then(d => { if (d.success) { loadArchived(); refreshCounts(); } });
        }

        function loadCompletedWalkIn() {
            const tbody = document.getElementById('donations-walkin-completed-tbody'); if (!tbody) return; tbody.innerHTML = '';
            fetch('/admin/reports/breastmilk-donations/walk-in', { cache: 'no-store' }).then(r => r.json()).then(d => {
                const rows = (d.success && Array.isArray(d.data)) ? d.data : [];
                if (!rows.length) { tbody.innerHTML = '<tr><td colspan="6" class="empty">No walk-in donations found.</td></tr>'; return; }
                rows.forEach(x => {
                    const tr = document.createElement('tr');
                    const date = formatDateStr(x.donation_date || x.Date);
                    const time = formatTimeStr(x.donation_time || x.Time);
                    const name = x.Full_Name || x.donor_name || 'N/A';
                    const bags = x.number_of_bags || x.Number_of_Bags || '—';
                    const vol = x.total_volume || x.Total_Volume_Donated || '—';
                    tr.innerHTML = `<td>${name}</td><td>${bags}</td><td>${vol}ml</td><td>${date}</td><td>${time}</td><td><button class="btn-sm btn-success" onclick="archiveDonation(${x.id || x.Breastmilk_Donation_ID || ''}, 'walkin')">Archive</button></td>`;
                    tbody.appendChild(tr);
                });
                setBadge('count-tab-walkin', rows.length);
            }).catch(() => { tbody.innerHTML = '<tr><td colspan="6" class="empty">Failed to load.</td></tr>'; });
        }

        function loadCompletedPickup() {
            const tbody = document.getElementById('donations-pickup-completed-tbody'); if (!tbody) return; tbody.innerHTML = '';
            fetch('/admin/reports/breastmilk-donations/pickup', { cache: 'no-store' }).then(r => r.json()).then(d => {
                const rows = (d.success && Array.isArray(d.data)) ? d.data : [];
                if (!rows.length) { tbody.innerHTML = '<tr><td colspan="7" class="empty">No pickup donations found.</td></tr>'; return; }
                rows.forEach(x => {
                    const tr = document.createElement('tr');
                    const date = formatDateStr(x.donation_date || x.scheduled_date || x.Date);
                    const time = formatTimeStr(x.donation_time || x.scheduled_time || x.Time);
                    const name = x.Full_Name || x.donor_name || 'N/A';
                    const addr = x.pickup_address || x.address || 'N/A';
                    const bags = x.number_of_bags || x.Number_of_Bags || '—';
                    const vol = x.total_volume || x.Total_Volume || x.Total_Volume_Donated || '—';
                    tr.innerHTML = `<td>${name}</td><td title="${addr}" style="max-width:220px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">${addr}</td><td>${bags}</td><td>${vol}ml</td><td>${date}</td><td>${time}</td><td><button class="btn-sm btn-success" onclick="archiveDonation(${x.id || ''}, 'pickup')">Archive</button></td>`;
                    tbody.appendChild(tr);
                });
                setBadge('count-tab-pickup', rows.length);
            }).catch(() => { tbody.innerHTML = '<tr><td colspan="7" class="empty">Failed to load.</td></tr>'; });
        }

        function loadArchived() {
            const tbody = document.getElementById('donations-archived-tbody'); if (!tbody) return; tbody.innerHTML = '';
            fetch('/admin/donations/archived', { cache: 'no-store' }).then(r => r.json()).then(d => {
                const rows = (d.success && Array.isArray(d.data)) ? d.data : [];
                if (!rows.length) { tbody.innerHTML = '<tr><td colspan="9" class="empty">No archived donations.</td></tr>'; return; }
                rows.forEach(x => {
                    const tr = document.createElement('tr');
                    const date = formatDateStr(x.donation_date);
                    const time = formatTimeStr(x.donation_time);
                    const name = x.Full_Name || 'N/A';
                    const typeBadge = `<span class=\"badge ${x.donation_type}\">${x.donation_type === 'walk_in' ? 'Walk-in' : 'Home collection'}</span>`;
                    const addr = x.pickup_address || '—';
                    const bags = x.number_of_bags || '—';
                    const vol = x.total_volume || '—';
                    const archivedAt = x.archived_at ? new Date(x.archived_at).toLocaleString() : '—';
                    tr.innerHTML = `<td>${name}</td><td>${typeBadge}</td><td>${bags}</td><td>${vol !== '—' ? vol + 'ml' : '—'}</td><td>${date}</td><td>${time}</td><td title="${addr}">${addr}</td><td>${archivedAt}</td><td><div style=\"display:flex; gap:6px; flex-wrap:wrap;\"><button class=\"btn-sm btn-success\" onclick=\"unarchiveDonation(${x.id})\">Unarchive</button><button class=\"btn-sm btn-danger\" onclick=\"deleteArchivedDonation(${x.id})\">Delete</button></div></td>`;
                    tbody.appendChild(tr);
                });
                setBadge('count-tab-archived', rows.length);
            }).catch(() => { tbody.innerHTML = '<tr><td colspan="9" class="empty">Failed to load.</td></tr>'; });
        }

        // Initialize archived filters UI and events
        function initArchivedFilters() {
            const yEl = document.getElementById('archived-year');
            if (yEl && yEl.children.length <= 1) {
                const current = new Date().getFullYear();
                for (let y = current; y >= current - 5; y--) {
                    const opt = document.createElement('option'); opt.value = String(y); opt.textContent = String(y); yEl.appendChild(opt);
                }
            }
            const applyBtn = document.getElementById('archived-apply');
            if (applyBtn) {
                applyBtn.addEventListener('click', () => {
                    // Persist current params in URL for shareability
                    const qEl = document.getElementById('archived-q');
                    const tEl = document.getElementById('archived-type');
                    const yEl2 = document.getElementById('archived-year');
                    const mEl = document.getElementById('archived-month');
                    const params = new URLSearchParams(window.location.search);
                    params.set('view', 'archived');
                    if (qEl && qEl.value.trim()) params.set('q', qEl.value.trim()); else params.delete('q');
                    if (tEl && tEl.value) params.set('type', tEl.value); else params.delete('type');
                    if (yEl2 && yEl2.value) params.set('year', yEl2.value); else params.delete('year');
                    if (mEl && mEl.value) params.set('month', mEl.value); else params.delete('month');
                    history.replaceState(null, '', `${location.pathname}?${params.toString()}`);
                    loadArchived();
                });
            }
            const qEl2 = document.getElementById('archived-q');
            if (qEl2) { qEl2.addEventListener('keydown', (e) => { if (e.key === 'Enter') { e.preventDefault(); document.getElementById('archived-apply')?.click(); } }); }
        }

        function refreshCounts() {
            Promise.all([
                fetch('/admin/walk-in-requests/pending', { cache: 'no-store' }).then(r => r.json()).catch(() => null),
                fetch('/admin/home-collection-requests/scheduled', { cache: 'no-store' }).then(r => r.json()).catch(() => null),
                fetch('/admin/reports/breastmilk-donations/walk-in', { cache: 'no-store' }).then(r => r.json()).catch(() => null),
                fetch('/admin/reports/breastmilk-donations/pickup', { cache: 'no-store' }).then(r => r.json()).catch(() => null),
                fetch('/admin/donations/archived', { cache: 'no-store' }).then(r => r.json()).catch(() => null)
            ]).then((results) => {
                const [pending, scheduled, walkin, pickup, archived] = results;
                const trySet = (id, payload) => {
                    if (payload && payload.success && Array.isArray(payload.data)) setBadge(id, payload.data.length);
                    // On failure, keep whatever the UI already computed from panel load
                };
                trySet('count-tab-pending', pending);
                trySet('count-tab-scheduled', scheduled);
                trySet('count-tab-walkin', walkin);
                trySet('count-tab-pickup', pickup);
                trySet('count-tab-archived', archived);

                // Also update sidebar donation badge (pending only) without full page reload.
                // Definition matches backend: pending walk-in requests + pending home collection requests.
                try {
                    const walkInPendingCount = (pending && pending.success && Array.isArray(pending.data)) ? pending.data.filter(x => x.type === 'walk_in').length : 0;
                    const homeCollectionPendingCount = (pending && pending.success && Array.isArray(pending.data)) ? pending.data.filter(x => x.type === 'home_collection').length : 0;
                    const totalPendingDonations = walkInPendingCount + homeCollectionPendingCount;
                    const badgeEl = document.getElementById('badge-donations-pending');
                    if (badgeEl) {
                        if (totalPendingDonations > 0) {
                            badgeEl.textContent = String(totalPendingDonations);
                            badgeEl.setAttribute('aria-label', totalPendingDonations + ' pending donations');
                        } else {
                            // Remove badge if zero to mirror initial blade conditional
                            badgeEl.parentElement && badgeEl.parentElement.removeChild(badgeEl);
                        }
                    } else if (totalPendingDonations > 0) {
                        // Badge not present (was zero at load); create it dynamically for better UX.
                        const donationsLink = document.querySelector('a.nav-item[href*="/admin/donations"]');
                        if (donationsLink) {
                            const span = document.createElement('span');
                            span.id = 'badge-donations-pending';
                            span.className = 'nav-badge';
                            span.textContent = String(totalPendingDonations);
                            span.setAttribute('aria-label', totalPendingDonations + ' pending donations');
                            donationsLink.appendChild(span);
                        }
                    }
                } catch (e) { /* silent */ }
            });
        }

        function loadActiveTab() {
            const view = (getParam('view') || 'pending').toLowerCase();
            const normalized = ['pending', 'scheduled', 'completed-walkin', 'completed-pickup', 'archived'].includes(view) ? view : 'pending';
            setActiveTab(normalized);
            if (normalized === 'pending') loadPending();
            if (normalized === 'scheduled') loadScheduled();
            if (normalized === 'completed-walkin') loadCompletedWalkIn();
            if (normalized === 'completed-pickup') loadCompletedPickup();
            if (normalized === 'archived') loadArchived();
        }

        // Proxy used by included partials to refresh panels after actions
        function loadReportData(reportType) {
            switch (reportType) {
                case 'pending-walk-in-requests':
                case 'pending-home-collection-requests':
                    loadPending(); break;
                case 'scheduled-home-collection-pickup':
                    loadScheduled(); break;
                case 'walk-in-donations':
                    loadCompletedWalkIn(); break;
                case 'pickup-donations':
                    loadCompletedPickup(); break;
                case 'archived-donations':
                    loadArchived(); break;
                default:
                    // no-op
                    break;
            }
            refreshCounts();
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Initialize counts from server-preloaded values to avoid zero flicker
            if (window.__DONATION_TAB_COUNTS__) {
                setBadge('count-tab-pending', window.__DONATION_TAB_COUNTS__.pending ?? 0);
                setBadge('count-tab-scheduled', window.__DONATION_TAB_COUNTS__.scheduled ?? 0);
                setBadge('count-tab-walkin', window.__DONATION_TAB_COUNTS__.completed_walkin ?? 0);
                setBadge('count-tab-pickup', window.__DONATION_TAB_COUNTS__.completed_pickup ?? 0);
                setBadge('count-tab-archived', window.__DONATION_TAB_COUNTS__.archived ?? 0);
            }
            // Mark active tab based on query param
            const view = (getParam('view') || 'pending').toLowerCase();
            const normalized = ['pending', 'scheduled', 'completed-walkin', 'completed-pickup', 'archived'].includes(view) ? view : 'pending';
            setActiveTab(normalized);
            // Load data
            loadActiveTab();
            // Load counts
            refreshCounts();
            // Init archived filters (years and handlers)
            initArchivedFilters();
        });
    </script>

    <!-- Logout Confirmation Modal -->
    <div id="logoutModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="logout-modal-title"
        aria-describedby="logout-modal-desc"
        style="display:none; position: fixed; z-index: 9999; inset: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); justify-content: center; align-items: center; padding: 20px;">
        <div class="modal-content"
            style="background-color: #fff; padding: 0; border-radius: 12px; width: 90%; max-width: 440px; display: flex; flex-direction: column; box-shadow: 0 10px 25px rgba(0,0,0,0.3); text-align:center;">
            <div class="modal-header"
                style="background: linear-gradient(135deg, #ffb6ce, #ff69b4); color: #fff; padding: 18px 22px; border-radius: 12px 12px 0 0; display:flex; align-items:center; justify-content: space-between;">
                <h3 class="modal-title" id="logout-modal-title" style="margin:0; font-size:18px;">Confirm Logout</h3>
                <button class="close-btn" aria-label="Close" onclick="closeModal('logoutModal')" type="button"
                    style="background:none; border:none; color:#fff; font-size:24px; cursor:pointer;">&times;</button>
            </div>
            <div class="modal-body" id="logout-modal-desc" style="padding:22px 24px 26px;">
                <div style="font-size: 44px; margin-bottom: 14px; color: #ff4d6d;">
                    <i class="fas fa-right-from-bracket"></i>
                </div>
                <p style="font-size: 15px; color: #333; margin: 0 12px 22px;">You’re about to log out of the admin
                    dashboard.</p>
                <div
                    style="background:#fff7fb; border:1px solid #ffd1e6; color:#7a2944; padding:10px 12px; border-radius:8px; font-size:13px; margin:0 16px 18px;">
                    Make sure any unsaved changes are saved.</div>
                <div style="display: flex; gap: 10px; justify-content: center;">
                    <button id="logout-cancel-btn" onclick="closeModal('logoutModal')" class="btn" type="button"
                        style="padding: 10px 20px; min-width: 110px; background:#6c757d; color:#fff; border-radius:6px;">Cancel</button>
                    <button id="logout-confirm-btn" onclick="confirmLogout()" class="btn" type="button"
                        style="padding: 10px 20px; min-width: 110px; display:inline-flex; align-items:center; justify-content:center; gap:8px; background:#ff69b4; color:#fff; border-radius:6px;">
                        <i class="fas fa-circle-notch fa-spin" id="logout-spinner" style="display:none;"></i>
                        <span id="logout-confirm-text">Logout</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>