<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reports - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin.css?v=20250921') }}">
    <style>
        /* Page-specific styles for Reports only. Global layout (sidebar, top bar, buttons) come from admin.css */
        .top-actions { display: flex; gap: 10px; }
        .btn-outline { padding: 8px 16px; border: 1px solid #ff69b4; background: transparent; color: #ff69b4; border-radius: 6px; cursor: pointer; transition: all 0.3s ease; }
        .btn-outline:hover { background: #ff69b4; color: #fff; }

        .reports-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 30px; }

        .report-category {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .report-category:hover {
            transform: translateY(-5px);
        }

        .category-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .category-icon {
            font-size: 24px;
            margin-right: 10px;
        }

        .category-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }

        .report-list {
            list-style: none;
        }

        .report-item {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .report-item:last-child {
            border-bottom: none;
        }

        .report-item:hover {
            color: #ff69b4;
        }

        .report-item-icon {
            margin-right: 8px;
        }

        /* Modal shell comes from admin.css; only tweak inner table + spacing here */
        .modal-body { padding: 20px; max-height: 60vh; overflow-y: auto; }

        .table-container {
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .data-table th,
        .data-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .data-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
        }

        .data-table tr:hover {
            background-color: #f5f5f5;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-accepted {
            background-color: #d4edda;
            color: #155724;
        }

        .status-declined {
            background-color: #f8d7da;
            color: #721c24;
        }

        .loading {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
        }

        .alert {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

    /* No availability styles needed on this page */

    /* Top-level tabs for report categories */
    .reports-tabs {
        display:flex;
        gap:8px;
        /* Add comfortable spacing from the fixed header */
        margin: 12px 20px 16px 20px;
        flex-wrap:wrap;
        /* When navigating via hash/anchors, keep tabs visible below fixed header */
        scroll-margin-top: calc(var(--admin-topbar-height, 72px) + 10px);
    }
    .reports-tab { padding:10px 14px; border:1px solid #e5e7eb; border-radius:999px; background:#fff; cursor:pointer; font-weight:600; font-size:14px; }
    .reports-tab.active { background:linear-gradient(135deg,#ffb6ce,#ff69b4); color:#fff; border-color:#ff69b4; }
    .reports-panel { display:none; }
    .reports-panel.active { display:block; }

    /* Monthly tabs */
    .tabs-bar {
        display:flex;
        gap:8px;
        flex-wrap:wrap;
        border-bottom:1px solid #e9ecef;
        padding-bottom:8px;
        margin-top: 8px; /* space from fixed header */
        margin-bottom:12px;
        scroll-margin-top: calc(var(--admin-topbar-height, 72px) + 8px);
    }
    .tab-btn { padding:8px 12px; border:1px solid #dee2e6; border-radius:6px; background:#f8f9fa; cursor:pointer; font-size:13px; color:#333; }
    .tab-btn:hover { background:#e9ecef; }
    .tab-btn.active { background:#0d6efd; color:#fff; border-color:#0d6efd; }
        .month-summary { display:grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap:10px; }
        .summary-card { background:#fff; border:1px solid #eee; border-radius:8px; padding:12px; }
        .summary-title { font-size:12px; color:#666; margin-bottom:6px; }
        .summary-value { font-size:18px; font-weight:700; color:#333; }

        /* Monthly inline controls */
        .inline-controls { display:flex; gap:10px; flex-wrap:wrap; margin: 8px 0 14px; }
        .inline-controls select { padding:8px 10px; border:1px solid #dee2e6; border-radius:6px; background:#fff; font-size:14px; color:#333; }
    .btn-primary { padding:8px 14px; background:#0d6efd; color:#fff; border:1px solid #0d6efd; border-radius:6px; cursor:pointer; font-weight:600; }
    .btn-primary:hover { background:#0b5ed7; border-color:#0a58ca; }
    .report-action-btn { display:inline-flex; align-items:center; gap:6px; }
        /* Ensure sidebar overlay never blocks clicks unless explicitly active */
        .sidebar-overlay { pointer-events: none !important; }
        .sidebar-overlay.active { pointer-events: auto !important; }
        /* Raise tabs above nearby elements just in case */
        .reports-tabs { position: relative; z-index: 2; }
        /* Tidy up focus rings on tabs (avoid thick black outlines) */
        .reports-tab { -webkit-tap-highlight-color: transparent; }
        .reports-tab:focus, .reports-tab:focus-visible { outline: none !important; box-shadow: none !important; }

        /* Pink themed report card + table (matches Donation History look) */
        .pink-card {
            background: #ffe3ef; /* light pink */
            border: 1px solid #ffc9dd;
            border-radius: 16px;
            padding: 14px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
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
            background: #ffc0d4; /* header pink */
            color: #333;
            padding: 12px;
            font-weight: 700;
            border-bottom: 1px solid #f1a9c1;
        }
        .data-table.theme-pink thead th:nth-child(2) {
            background: #ff9fc1; /* accent header for second column */
            color: #fff;
        }
        .data-table.theme-pink tbody td {
            padding: 12px;
            border-bottom: 1px solid #f6c8d7;
        }
        .data-table.theme-pink tbody tr:last-child td { border-bottom: none; }
        .data-table.theme-pink tbody td:nth-child(2) {
            color: #ff4d6d; /* accent value */
            font-weight: 700;
        }
    </style>
</head>
<body>
    <!-- Sidebar (shared) -->
    @include('admin.partials.sidebar')

    <!-- Main Content -->
    <div class="main-content top-bar-space">
        @include('admin.partials.top-bar', [
            'pageTitle' => 'Reports Dashboard'
        ])

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="reports-tabs" role="tablist">
            <button type="button" class="reports-tab active" data-panel="panel-requests" role="tab" aria-controls="panel-requests" aria-selected="true">Breastmilk Requests</button>
            <button type="button" class="reports-tab" data-panel="panel-donations" role="tab" aria-controls="panel-donations" aria-selected="false">Breastmilk Donations</button>
            <button type="button" class="reports-tab" data-panel="panel-inventory" role="tab" aria-controls="panel-inventory" aria-selected="false">Inventory</button>
        </div>

        <div id="panel-requests" class="reports-panel active">
            <div class="reports-grid">
                <!-- Breastmilk Request Reports -->
                <div class="report-category">
                <div class="category-header">
                    <div class="category-title">Breastmilk Request Reports</div>
                </div>
                <div class="inline-controls">
                    <label for="req-year-select" style="font-size:13px; color:#555; align-self:center;">Year</label>
                    <select id="req-year-select" aria-label="Year"></select>
                    <label for="req-month-select" style="font-size:13px; color:#555; align-self:center;">Month</label>
                    <select id="req-month-select" aria-label="Month"></select>
                    <button type="button" id="req-print" class="btn-primary report-action-btn" aria-label="Print Requests Report"><i class="fas fa-print" aria-hidden="true"></i><span>Print</span></button>
                </div>
                <div id="req-inline-content" class="pink-card"></div>
                </div>
            </div>
        </div>

        <div id="panel-donations" class="reports-panel">
            <div class="reports-grid">
                <!-- Breastmilk Donation Reports -->
                <div class="report-category">
                <div class="category-header">
                    <div class="category-title">Breastmilk Donation Reports</div>
                </div>
                <div class="inline-controls">
                    <label for="don-year-select" style="font-size:13px; color:#555; align-self:center;">Year</label>
                    <select id="don-year-select" aria-label="Year"></select>
                    <label for="don-month-select" style="font-size:13px; color:#555; align-self:center;">Month</label>
                    <select id="don-month-select" aria-label="Month"></select>
                    <button type="button" id="don-print" class="btn-primary report-action-btn" aria-label="Print Donations Report"><i class="fas fa-print" aria-hidden="true"></i><span>Print</span></button>
                </div>
                <div id="don-inline-content" class="pink-card"></div>
                </div>
            </div>
        </div>

        <div id="panel-inventory" class="reports-panel">
            <div class="reports-grid">
                <!-- Inventory Reports -->
                <div class="report-category">
                <div class="category-header">
                    <div class="category-title">Inventory Reports</div>
                </div>
                <div class="inline-controls">
                    <label for="inv-year-select" style="font-size:13px; color:#555; align-self:center;">Year</label>
                    <select id="inv-year-select" aria-label="Year"></select>
                    <label for="inv-month-select" style="font-size:13px; color:#555; align-self:center;">Month</label>
                    <select id="inv-month-select" aria-label="Month"></select>
                    <button type="button" id="inv-print" class="btn-primary report-action-btn" aria-label="Print Inventory Report"><i class="fas fa-print" aria-hidden="true"></i><span>Print</span></button>
                </div>
                <div id="inv-inline-content" class="pink-card"></div>
                </div>
            </div>
        </div>

        
    </div>

    <!-- Report Modal -->
    <div id="reportModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitle">Report</h3>
                <button class="close-btn" onclick="closeModal('reportModal')">&times;</button>
            </div>
            <div class="modal-body" id="modalBody">
                <div class="loading">Loading report data...</div>
            </div>
        </div>
    </div>

    

    <script>
        let currentReportType = '';

        // Top-level tabs switching (cards in tabs)
        document.addEventListener('DOMContentLoaded', function(){
            const tabs = Array.from(document.querySelectorAll('.reports-tab'));
            const panels = Array.from(document.querySelectorAll('.reports-panel'));
            const activate = (panelId)=>{
                tabs.forEach(t=>{ const on = t.getAttribute('data-panel')===panelId; t.classList.toggle('active', on); t.setAttribute('aria-selected', on?'true':'false'); });
                panels.forEach(p=> p.classList.toggle('active', p.id===panelId));
            };
            tabs.forEach(t=> t.addEventListener('click', ()=> activate(t.getAttribute('data-panel'))));
            // No special tab activation; Requests is active by default
        });

        function loadReport(reportType, title) {
            currentReportType = reportType;
            const modal = document.getElementById('reportModal');
            const modalBody = document.getElementById('modalBody');
            document.getElementById('modalTitle').textContent = title;
            modalBody.innerHTML = '<div class="loading">Loading report data...</div>';
            modal.style.display = 'block';

            let endpoint = '';
            let tableHeaders = [];

            switch (reportType) {
                case 'all-requests':
                    endpoint = '/admin/reports/breastmilk-requests/all';
                    tableHeaders = ['Full Name', 'Phone Number', 'Date', 'Time', 'Prescription', 'Status'];
                    break;
                case 'accepted-requests':
                    endpoint = '/admin/reports/breastmilk-requests/accepted';
                    tableHeaders = ['Guardian Name', 'Address', 'Phone Number', 'Infant Name', 'Age', 'Dispensed Volume (ml)', 'Date', 'Time'];
                    break;
                case 'declined-requests':
                    endpoint = '/admin/reports/breastmilk-requests/declined';
                    tableHeaders = ['Guardian Name', 'Address', 'Phone Number', 'Date', 'Feedback'];
                    break;
                case 'all-donations':
                    endpoint = '/admin/reports/breastmilk-donations/all';
                    tableHeaders = ['Full Name', 'Donation Method', 'Number of Bags', 'Total Volume', 'Date', 'Time'];
                    break;
                case 'walk-in-donations':
                    endpoint = '/admin/reports/breastmilk-donations/walk-in';
                    tableHeaders = ['Donor Full Name', 'Number of Bags', 'Total Volume', 'Date', 'Time'];
                    break;
                case 'pickup-donations':
                    endpoint = '/admin/reports/breastmilk-donations/pickup';
                    tableHeaders = ['Donor Full Name', 'Address', 'Number of Bags', 'Total Volume', 'Date', 'Time'];
                    break;
                case 'unpasteurized':
                    endpoint = '/admin/reports/inventory/unpasteurized';
                    tableHeaders = ['Donor Name', 'Number of Bags', 'Total Volume', 'Date', 'Time'];
                    break;
                case 'pasteurized':
                    endpoint = '/admin/reports/inventory/pasteurized';
                    tableHeaders = ['Batch Number', 'Total Volume', 'Date Pasteurized', 'Time Pasteurized'];
                    break;
                case 'dispensed':
                    endpoint = '/admin/reports/inventory/dispensed';
                    tableHeaders = ['Guardian Name', 'Volume', 'Recipient Name', 'Batch Number', 'Date', 'Time'];
                    break;
                case 'monthly':
                    endpoint = '/admin/reports/monthly';
                    tableHeaders = ['Month', 'Total Donations', 'Total Volume', 'Total Requests', 'Total Volume Requested'];
                    break;
            }

            fetch(endpoint)
                .then(r => r.json())
                .then(data => {
                    if (data && data.success) {
                        displayReportData(data.data, tableHeaders, reportType);
                    } else {
                        modalBody.innerHTML = '<div class="no-data">Error loading report data</div>';
                    }
                })
                .catch(() => {
                    modalBody.innerHTML = '<div class="no-data">Error loading report data</div>';
                });
        }

        function displayReportData(data, headers, reportType) {
            const modalBody = document.getElementById('modalBody');
            if (!data || data.length === 0) {
                modalBody.innerHTML = '<div class="no-data">No data available for this report</div>';
                return;
            }

            const formatTime12h = (timeStr)=>{
                if(!timeStr) return 'N/A';
                const str = String(timeStr).trim();
                // If already has AM/PM, return as-is
                if (/\b(AM|PM)$/i.test(str)) return str.toUpperCase();
                const m = str.match(/^(\d{1,2}):(\d{2})(?::(\d{2}))?/);
                if(!m) return str;
                let h = parseInt(m[1],10);
                const min = m[2];
                const ampm = h>=12 ? 'PM' : 'AM';
                h = h%12; if (h===0) h = 12;
                return `${h}:${min} ${ampm}`;
            };

            let tableHTML = '<div class="table-container"><table class="data-table"><thead><tr>';
            headers.forEach(h => { tableHTML += `<th>${h}</th>`; });
            tableHTML += '</tr></thead><tbody>';

            data.forEach(item => {
                tableHTML += '<tr>';
                switch (reportType) {
                    case 'all-requests':
                        tableHTML += `
                            <td>${item.Full_Name || 'N/A'}</td>
                            <td>${item.Contact_Number || 'N/A'}</td>
                            <td>${item.Date || 'N/A'}</td>
                            <td>${formatTime12h(item.Time)}</td>
                            <td>${item.Prescription || 'N/A'}</td>
                            <td><span class="status-badge status-${item.status || 'pending'}">${(item.status || 'pending').toUpperCase()}</span></td>
                        `;
                        break;
                    case 'accepted-requests':
                        tableHTML += `
                            <td>${item.Guardian_Name || 'N/A'}</td>
                            <td>${item.Address || 'N/A'}</td>
                            <td>${item.Phone_Number || item.Contact_Number || 'N/A'}</td>
                            <td>${item.Infant_Name || 'N/A'}</td>
                            <td>${item.Age || 'N/A'}</td>
                            <td>${item.Dispensed_Volume != null ? Number(item.Dispensed_Volume).toLocaleString()+' ml' : 'N/A'}</td>
                            <td>${item.Date || 'N/A'}</td>
                            <td>${formatTime12h(item.Time)}</td>
                        `;
                        break;
                    case 'declined-requests':
                        tableHTML += `
                            <td>${item.Guardian_Name || 'N/A'}</td>
                            <td>${item.Address || 'N/A'}</td>
                            <td>${item.Contact_Number || 'N/A'}</td>
                            <td>${item.Date || 'N/A'}</td>
                            <td>${item.Feedback || 'N/A'}</td>
                        `;
                        break;
                    case 'all-donations':
                        tableHTML += `
                            <td>${item.Full_Name || 'N/A'}</td>
                            <td>${item.Donation_Method || 'N/A'}</td>
                            <td>${item.Number_Of_Bag || 'N/A'}</td>
                            <td>${item.Volume_Per_Bag || 'N/A'} ml</td>
                            <td>${item.Donation_Date || 'N/A'}</td>
                            <td>${formatTime12h(item.Donation_Time)}</td>
                        `;
                        break;
                    case 'walk-in-donations':
                        tableHTML += `
                            <td>${item.Full_Name || 'N/A'}</td>
                            <td><strong style="color: #ff69b4;">${item.number_of_bags || 'N/A'}</strong> bags</td>
                            <td>${item.total_volume || 'N/A'} ml</td>
                            <td>${item.donation_date || 'N/A'}</td>
                            <td>${formatTime12h(item.donation_time)}</td>
                        `;
                        break;
                    case 'pickup-donations':
                        tableHTML += `
                            <td>${item.Full_Name || 'N/A'}</td>
                            <td>${item.pickup_address || 'N/A'}</td>
                            <td>${item.number_of_bags || 'N/A'}</td>
                            <td>${item.total_volume || 'N/A'} ml</td>
                            <td>${item.donation_date || 'N/A'}</td>
                            <td>${formatTime12h(item.donation_time)}</td>
                        `;
                        break;
                    case 'unpasteurized':
                        tableHTML += `
                            <td>${item.Full_Name || 'N/A'}</td>
                            <td>${item.Number_Of_Bag || 'N/A'}</td>
                            <td>${item.Volume_Per_Bag || 'N/A'} ml</td>
                            <td>${item.Donation_Date || 'N/A'}</td>
                            <td>${formatTime12h(item.Donation_Time)}</td>
                        `;
                        break;
                    case 'pasteurized':
                        tableHTML += `
                            <td>${item.Batch_Number || 'N/A'}</td>
                            <td>${item.Full_Name || 'N/A'}</td>
                            <td>${item.Number_Of_Bag || 'N/A'}</td>
                            <td>${item.Volume_Per_Bag || 'N/A'} ml</td>
                            <td>${item.Pasteurized_Date || 'N/A'}</td>
                            <td>${formatTime12h(item.Pasteurized_Time)}</td>
                        `;
                        break;
                    case 'dispensed':
                        tableHTML += `
                            <td>${item.Guardian_Name || 'N/A'}</td>
                            <td>${item.Volume || 'N/A'} ml</td>
                            <td>${item.Recipient_Name || 'N/A'}</td>
                            <td>${item.Donor_Name || 'N/A'}</td>
                            <td>${item.Batch_Number || 'N/A'}</td>
                            <td>${item.Date || 'N/A'}</td>
                            <td>${formatTime12h(item.Time)}</td>
                        `;
                        break;
                    case 'monthly':
                        // Handled by renderMonthlyTabs; keep fallback minimal if ever called
                        tableHTML += `
                            <td>${item.Month || 'N/A'}</td>
                            <td>${item.Total_Donation || '0'}</td>
                            <td>${item.Total_Volume || '0'} ml</td>
                            <td>${item.Total_Requests || '0'}</td>
                            <td>${item.Total_Volume_Requested || '0'} ml</td>
                        `;
                        break;
                }
                tableHTML += '</tr>';
            });

            tableHTML += '</tbody></table></div>';
            modalBody.innerHTML = tableHTML;
        }


        

        // Panel-specific inline overall reports
        document.addEventListener('DOMContentLoaded', async function(){
            // Load months once
            let monthsPayload = null;
            try {
                const res = await fetch('/admin/reports/monthly-sections');
                const payload = await res.json();
                if(payload && payload.success){ monthsPayload = payload.data; }
            } catch(e){}
            const parsed = (monthsPayload?.months_meta||[]).map(m=>({label:m.label, year:m.year, month:m.month}));
            const years = [...new Set(parsed.map(p=>p.year))];
            const fillYearMonth = (yearSel, monthSel)=>{
                if(!yearSel || !monthSel) return; yearSel.innerHTML='';
                years.forEach(y=>{ const o=document.createElement('option'); o.value=String(y); o.textContent=String(y); yearSel.appendChild(o); });
                if(years.length){ yearSel.value=String(years[years.length-1]); }
                const applyMonths=()=>{ const y=parseInt(yearSel.value||String(new Date().getFullYear()),10); const list=parsed.filter(p=>p.year===y); monthSel.innerHTML=''; list.forEach(p=>{ const o=document.createElement('option'); o.value=String(p.month); o.textContent=new Date(y,p.month-1,1).toLocaleString('en-US',{month:'long'}); monthSel.appendChild(o); }); if(list.length){ monthSel.value=String(list[list.length-1].month); } };
                applyMonths(); yearSel.onchange=applyMonths;
            };

            // Helper to auto fetch on change
            function bindAutoFetch(yearId, monthId, renderFn){
                const yEl=document.getElementById(yearId); const mEl=document.getElementById(monthId);
                if(!yEl || !mEl) return;
                const run=()=>{ renderFn(yEl.value, mEl.value); };
                yEl.addEventListener('change', run);
                mEl.addEventListener('change', run);
                // initial render once options are filled below
                setTimeout(run, 0);
            }

            // Requests
            fillYearMonth(document.getElementById('req-year-select'), document.getElementById('req-month-select'));
            const reqPrint = document.getElementById('req-print');
            const renderReq = (year, month)=>{
                const sub = 'all';
                const container = document.getElementById('req-inline-content');
                fetch(`/admin/reports/monthly-query?category=req&sub=${encodeURIComponent(sub)}&year=${encodeURIComponent(year)}&month=${encodeURIComponent(month)}`)
                    .then(r=>r.json()).then(d=>{
                        if(!(d&&d.success)){ container.innerHTML='<div class="no-data">No data found</div>'; return; }
                        const data=d.data||{};
                        const rows = data.rows || data.list || data.items || [];

                        if (Array.isArray(rows) && rows.length) {
                            // Render only successfully dispensed requests: Requestor, Total Volume (ml), Date & Time Received
                            const headers = ['Requestor', 'Total Volume (ml)', 'Date & Time Received'];
                            let html = '<div class="table-container"><table class="data-table theme-pink"><thead><tr>'
                                + headers.map(h=>`<th>${h}</th>`).join('')
                                + '</tr></thead><tbody>';

                            const formatTime12h = (timeStr)=>{
                                if(!timeStr) return '';
                                const m = String(timeStr).match(/^(\d{1,2}):(\d{2})(?::(\d{2}))?/);
                                if(!m) return String(timeStr);
                                let h = parseInt(m[1],10);
                                const min = m[2];
                                const ampm = h>=12 ? 'PM' : 'AM';
                                h = h%12; if (h===0) h = 12;
                                return `${h}:${min} ${ampm}`;
                            };

                            const formatDateTime = (dt, dOnly, tOnly)=>{
                                // Prefer combined datetime string
                                if (dt) {
                                    if (dt.includes(' ')) {
                                        const parts = dt.split(' ');
                                        const d = parts[0];
                                        const t = formatTime12h(parts[1]||'');
                                        return `${d}${t?(' '+t):''}`;
                                    }
                                    if (dt.includes('T')) {
                                        const [d, rest] = dt.split('T');
                                        const t = formatTime12h((rest||'').slice(0,8));
                                        return `${d}${t?(' '+t):''}`;
                                    }
                                    return dt; // fallback as-is
                                }
                                // Build from separate parts
                                const d = dOnly || '';
                                const t = formatTime12h(tOnly||'');
                                return d ? (t?`${d} ${t}`: d) : 'N/A';
                            };

                            rows.forEach(item=>{
                                const requestor = item.Full_Name || item.full_name || 'N/A';
                                const volRaw = item.total_volume ?? item.Total_Volume ?? item.decided_total_volume;
                                const volume = (volRaw!==undefined && volRaw!==null && volRaw!=='') ? `${Number(volRaw).toLocaleString()}` : '—';
                                const date = formatDateTime(item.datetime_received, (item.date_received||item.date), item.time_received);
                                html += '<tr>'
                                    + `<td>${requestor}</td>`
                                    + `<td>${volume} ${volume==='—'?'':'ml'}</td>`
                                    + `<td>${date}</td>`
                                    + '</tr>';
                            });

                            html += '</tbody></table></div>';
                            container.innerHTML = html;
                        } else {
                            // Fallback: render monthly summary as before
                            const headers=['Month'].concat((data.cards||[]).map(c=>c.label));
                            let html='<div class="table-container"><table class="data-table theme-pink"><thead><tr>'
                                + headers.map(h=>`<th>${h}</th>`).join('')
                                + "</tr></thead><tbody>";
                            const row=[data.label].concat((data.cards||[]).map(c=>`${Number(c.val||0).toLocaleString()}${c.suffix||''}`));
                            html += '<tr>'+row.map(v=>`<td>${v}</td>`).join('')+'</tr></tbody></table></div>';
                            container.innerHTML=html;
                        }
                    }).catch(()=>{ container.innerHTML='<div class="no-data">Failed to load</div>'; });
            };
            bindAutoFetch('req-year-select','req-month-select', renderReq);
            if(reqPrint){ reqPrint.onclick = ()=>{
                const year = document.getElementById('req-year-select').value;
                const month = document.getElementById('req-month-select').value;
                const url = `/admin/reports/monthly-print?year=${encodeURIComponent(year)}&month=${encodeURIComponent(month)}`;
                window.open(url, '_blank');
            }; }

            // Donations
            fillYearMonth(document.getElementById('don-year-select'), document.getElementById('don-month-select'));
            const donPrint = document.getElementById('don-print');
            const renderDon = (year, month)=>{
                const sub = 'all';
                const container = document.getElementById('don-inline-content');
                fetch(`/admin/reports/monthly-query?category=don&sub=${encodeURIComponent(sub)}&year=${encodeURIComponent(year)}&month=${encodeURIComponent(month)}`)
                    .then(r=>r.json()).then(d=>{
                        if(!(d&&d.success)){ container.innerHTML='<div class="no-data">No data found</div>'; return; }
                        const data=d.data||{}; const headers=['Month'].concat((data.cards||[]).map(c=>c.label));
                        let html='<div class="table-container"><table class="data-table theme-pink"><thead><tr>'+headers.map(h=>`<th>${h}</th>`).join('')+"</tr></thead><tbody>";
                        const row=[data.label].concat((data.cards||[]).map(c=>`${Number(c.val||0).toLocaleString()}${c.suffix||''}`));
                        html += '<tr>'+row.map(v=>`<td>${v}</td>`).join('')+'</tr></tbody></table></div>';
                        container.innerHTML=html;
                    }).catch(()=>{ container.innerHTML='<div class="no-data">Failed to load</div>'; });
            };
            bindAutoFetch('don-year-select','don-month-select', renderDon);
            if(donPrint){ donPrint.onclick = ()=>{
                const year = document.getElementById('don-year-select').value;
                const month = document.getElementById('don-month-select').value;
                const url = `/admin/reports/monthly-print-donations?year=${encodeURIComponent(year)}&month=${encodeURIComponent(month)}`;
                window.open(url, '_blank');
            }; }

            // Inventory
            fillYearMonth(document.getElementById('inv-year-select'), document.getElementById('inv-month-select'));
            const invPrint = document.getElementById('inv-print');
            const renderInv = (year, month)=>{
                let sub = 'pasteurized';
                const container = document.getElementById('inv-inline-content');
                fetch(`/admin/reports/monthly-query?category=inv&sub=${encodeURIComponent(sub)}&year=${encodeURIComponent(year)}&month=${encodeURIComponent(month)}`)
                    .then(r=>r.json()).then(d=>{
                        if(!(d&&d.success)){ container.innerHTML='<div class="no-data">No data found</div>'; return; }
                        const data=d.data||{}; const headers=['Month'].concat((data.cards||[]).map(c=>c.label));
                        let html='<div class="table-container"><table class="data-table theme-pink"><thead><tr>'+headers.map(h=>`<th>${h}</th>`).join('')+"</tr></thead><tbody>";
                        const row=[data.label].concat((data.cards||[]).map(c=>`${Number(c.val||0).toLocaleString()}${c.suffix||''}`));
                        html += '<tr>'+row.map(v=>`<td>${v}</td>`).join('')+'</tr></tbody></table></div>';
                        container.innerHTML=html;
                    }).catch(()=>{ container.innerHTML='<div class="no-data">Failed to load</div>'; });
            };
            bindAutoFetch('inv-year-select','inv-month-select', renderInv);
            if(invPrint){ invPrint.onclick = ()=>{
                const year = document.getElementById('inv-year-select').value;
                const month = document.getElementById('inv-month-select').value;
                const url = `/admin/reports/monthly-print-inventory?year=${encodeURIComponent(year)}&month=${encodeURIComponent(month)}`;
                window.open(url, '_blank');
            }; }
        });

        async function renderMonthlySections(){
            const modalBody = document.getElementById('modalBody');
            modalBody.innerHTML = '<div class="loading">Loading monthly data...</div>';
            try {
                const res = await fetch('/admin/reports/monthly-sections');
                const payload = await res.json();
                if(!payload || !payload.success){ modalBody.innerHTML = '<div class="no-data">Failed to load monthly data</div>'; return; }
                const { months, requests, donations, inventory } = payload.data || {};

                // Category tabs
                let catTabs = '<div class="tabs-bar" id="monthly-cat-tabs">'
                    + '<button type="button" class="tab-btn active" data-cat="req">Requests</button>'
                    + '<button type="button" class="tab-btn" data-cat="don">Donations</button>'
                    + '<button type="button" class="tab-btn" data-cat="inv">Inventory</button>'
                    + '</div>';

                // Month tabs
                let monthTabs = '<div class="tabs-bar" id="monthly-month-tabs">';
                (months||[]).forEach((label, idx)=>{
                    monthTabs += `<button type="button" class="tab-btn${idx===0?' active':''}" data-idx="${idx}">${label}</button>`;
                });
                monthTabs += '</div>';

                // Content container
                const contentId = 'monthly-content';
                modalBody.innerHTML = catTabs + monthTabs + `<div id="${contentId}"></div>`;

                const renderActive = ()=>{
                    const activeMonthBtn = document.querySelector('#monthly-month-tabs .tab-btn.active');
                    const activeCatBtn = document.querySelector('#monthly-cat-tabs .tab-btn.active');
                    const idx = parseInt(activeMonthBtn?.getAttribute('data-idx')||'0',10) || 0;
                    const cat = activeCatBtn?.getAttribute('data-cat')||'req';
                    let html = '';
                    if(cat==='req'){
                        const it = (requests||[])[idx] || {};
                        html = renderMonthSummaryCards(it, [
                            { label:'Total Requests', val: it.total },
                            { label:'Approved', val: it.approved },
                            { label:'Declined', val: it.declined },
                            { label:'Pending', val: it.pending },
                        ], it.Month);
                    } else if(cat==='don'){
                        const it = (donations||[])[idx] || {};
                        html = renderMonthSummaryCards(it, [
                            { label:'Total Donations', val: it.total },
                            { label:'Walk-in', val: it.walk_in },
                            { label:'Pickup', val: it.pickup },
                            { label:'Total Volume', val: it.total_volume, suffix:' ml' },
                        ], it.Month);
                    } else {
                        const it = (inventory||[])[idx] || {};
                        html = renderMonthSummaryCards(it, [
                            { label:'Pasteurized Added', val: it.pasteurized_added, suffix:' ml' },
                            { label:'Dispensed', val: it.dispensed, suffix:' ml' },
                        ], it.Month);
                    }
                    document.getElementById(contentId).innerHTML = html;
                };

                // Wire buttons
                const catBtns = Array.from(document.querySelectorAll('#monthly-cat-tabs .tab-btn'));
                const monthBtns = Array.from(document.querySelectorAll('#monthly-month-tabs .tab-btn'));
                catBtns.forEach(b=> b.addEventListener('click', function(){ catBtns.forEach(x=>x.classList.remove('active')); this.classList.add('active'); renderActive(); }));
                monthBtns.forEach(b=> b.addEventListener('click', function(){ monthBtns.forEach(x=>x.classList.remove('active')); this.classList.add('active'); renderActive(); }));

                renderActive();
            } catch(e){
                modalBody.innerHTML = '<div class="no-data">Failed to load monthly data</div>';
            }
        }

        function renderMonthSummaryCards(item, cards, monthLabel){
            const month = monthLabel || item.Month || item.month || 'N/A';
            const format = (v)=> (Number(v)||0).toLocaleString();
            const cardsHtml = (cards||[]).map(c=>{
                const suffix = c.suffix || '';
                return `<div class="summary-card">
                    <div class="summary-title">${c.label}</div>
                    <div class="summary-value">${format(c.val)}${suffix}</div>
                </div>`; 
            }).join('');
            return `
                <div style="margin-bottom:12px;">
                    <h4 style="margin:0; font-size:16px; font-weight:700; color:#212529;">${month}</h4>
                    <div style="font-size:12px; color:#6c757d;">Monthly aggregation</div>
                </div>
                <div class="month-summary">${cardsHtml}</div>
            `;
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const reportModal = document.getElementById('reportModal');
            if (event.target === reportModal) closeModal('reportModal');
        }

        // Auto-load report if specified in URL
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const reportParam = urlParams.get('report');
            
            if (reportParam) {
                const reportMapping = {
                    'requested-breastmilk': 'all-requests',
                    'accepted-requests': 'accepted-requests',
                    'declined-requests': 'declined-requests',
                    'all-donations': 'all-donations',
                    'walk-in-donations': 'walk-in-donations',
                    'pickup-donations': 'pickup-donations',
                    'unpasteurized-breastmilk': 'unpasteurized',
                    'pasteurized-breastmilk': 'pasteurized',
                    'dispensed-breastmilk': 'dispensed',
                    'monthly-summary': 'monthly'
                };
                
                const reportType = reportMapping[reportParam];
                if (reportType) {
                    const reportTitles = {
                        'all-requests': 'All Breastmilk Requests',
                        'accepted-requests': 'Accepted Breastmilk Requests',
                        'declined-requests': 'Declined Breastmilk Requests',
                        'all-donations': 'All Breastmilk Donations',
                        'walk-in-donations': 'Walk-in Donations',
                        'pickup-donations': 'Pickup Donations',
                        'unpasteurized': 'Unpasteurized Donations',
                        'pasteurized': 'Pasteurized Donations',
                        'dispensed': 'Dispensed Donations',
                        'monthly': 'Monthly Summary'
                    };
                    
                    loadReport(reportType, reportTitles[reportType]);
                }
            }
        });
    </script>
    <script>
        function toggleSidebar(){ if (typeof window.toggleSidebar === 'function') { return window.toggleSidebar(); } }
        function openModal(id){ var m=document.getElementById(id); if(m) m.style.display='flex'; }
        function closeModal(id){ var m=document.getElementById(id); if(m) m.style.display='none'; }
        function logout(){ openModal('logoutModal'); setTimeout(function(){ var c=document.getElementById('logout-cancel-btn'); if(c) c.focus(); }, 0); }
        function confirmLogout(){
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
        window.addEventListener('click', function(e){ var m=document.getElementById('logoutModal'); if(m && e.target===m){ closeModal('logoutModal'); }});
    </script>
    <!-- Logout Confirmation Modal -->
    <div id="logoutModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="logout-modal-title" aria-describedby="logout-modal-desc" style="display:none; position: fixed; z-index: 9999; inset: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); justify-content: center; align-items: center; padding: 20px;">
        <div class="modal-content" style="background-color: #fff; padding: 0; border-radius: 12px; width: 90%; max-width: 440px; display: flex; flex-direction: column; box-shadow: 0 10px 25px rgba(0,0,0,0.3); text-align:center;">
            <div class="modal-header" style="background: linear-gradient(135deg, #ffb6ce, #ff69b4); color: #fff; padding: 18px 22px; border-radius: 12px 12px 0 0; display:flex; align-items:center; justify-content: space-between;">
                <h3 class="modal-title" id="logout-modal-title" style="margin:0; font-size:18px;">Confirm Logout</h3>
                <button class="close-btn" aria-label="Close" onclick="closeModal('logoutModal')" type="button" style="background:none; border:none; color:#fff; font-size:24px; cursor:pointer;">&times;</button>
            </div>
            <div class="modal-body" id="logout-modal-desc" style="padding:22px 24px 26px;">
                <div style="font-size: 44px; margin-bottom: 14px; color: #ff4d6d;">
                    <i class="fas fa-right-from-bracket"></i>
                </div>
                <p style="font-size: 15px; color: #333; margin: 0 12px 22px;">You’re about to log out of the admin dashboard.</p>
                <div style="background:#fff7fb; border:1px solid #ffd1e6; color:#7a2944; padding:10px 12px; border-radius:8px; font-size:13px; margin:0 16px 18px;">Make sure any unsaved changes are saved.</div>
                <div style="display: flex; gap: 10px; justify-content: center;">
                    <button id="logout-cancel-btn" onclick="closeModal('logoutModal')" class="btn" type="button" style="padding: 10px 20px; min-width: 110px; background:#6c757d; color:#fff; border-radius:6px;">Cancel</button>
                    <button id="logout-confirm-btn" onclick="confirmLogout()" class="btn" type="button" style="padding: 10px 20px; min-width: 110px; display:inline-flex; align-items:center; justify-content:center; gap:8px; background:#ff69b4; color:#fff; border-radius:6px;">
                        <i class="fas fa-circle-notch fa-spin" id="logout-spinner" style="display:none;"></i>
                        <span id="logout-confirm-text">Logout</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
