<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Health Screening - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin.css?v=20250921') }}">
    <style>
    /* Page-specific styles only (tabs, table layout, cards) */
    body { background:#f8f9fa; display:flex; min-height:100vh; }
    /* Readable question blocks */
    .qa-section-title { margin:24px 0 10px; font-size:18px; font-weight:700; letter-spacing:.5px; color:#222; }
    .qa-grid { display:grid; gap:12px; margin-bottom:22px; }
    .qa-card { background:#fff; border:1px solid #e4e7eb; border-left:4px solid #ff69b4; padding:14px 16px 12px; border-radius:12px; position:relative; box-shadow:0 2px 4px rgba(0,0,0,.04); transition:box-shadow .25s, transform .25s; }
    .qa-card:hover { box-shadow:0 6px 14px rgba(255,105,180,.18); transform:translateY(-2px); }
    .qa-q { font-size:15px; font-weight:600; color:#000; line-height:1.35; margin:0 0 4px; }
    .qa-q small { display:block; font-weight:500; font-size:11px; color:#666; margin-top:4px; letter-spacing:.3px; }
    .qa-answer { font-size:14px; margin-top:4px; }
    .qa-answer strong { color:#333; font-weight:600; }
    .qa-answer .ans-value { font-weight:700; text-transform:uppercase; letter-spacing:.6px; color:#000; }
    .qa-extra { margin-top:8px; background:#fff8e1; border:1px solid #ffe7a3; padding:8px 10px; border-radius:8px; font-size:12px; line-height:1.4; color:#7a5a00; }
    .qa-badge { position:absolute; top:8px; right:10px; background:#ffe3f1; color:#000; font-size:10px; padding:3px 7px; border-radius:999px; letter-spacing:.5px; font-weight:600; }
    @media (max-width:640px){ .qa-card { padding:12px 14px 10px; } .qa-q { font-size:14px; } }
    @media (prefers-reduced-motion:reduce){ .qa-card, .qa-card:hover { transition:none; transform:none; } }
        .container { padding:22px; }
    .tabs { display:flex; gap:10px; flex-wrap:wrap; margin:0 0 18px; position:relative; }
    .tab { --tab-bg:#ffffff; --tab-border:#e5e7eb; --tab-color:#000; display:inline-flex; align-items:center; gap:8px; padding:10px 18px; border-radius:999px; background:var(--tab-bg); border:1px solid var(--tab-border); color:var(--tab-color); text-decoration:none; font-size:14px; font-weight:700; line-height:1; position:relative; box-shadow:0 2px 4px rgba(0,0,0,0.05); transition: background .35s, color .25s, box-shadow .35s, transform .35s, border-color .35s; }
    .tab .count { background:#f1f3f5; color:#444; padding:3px 8px; border-radius:999px; font-weight:600; font-size:12px; line-height:1; transition:background .35s, color .35s; }
    .tab:hover { background:#fff7fb; border-color:#ffc1d8; box-shadow:0 4px 10px rgba(255,105,180,0.18); transform:translateY(-2px); }
    .tab:focus-visible { outline:none; box-shadow:0 0 0 3px rgba(255,105,180,0.45), 0 4px 10px rgba(255,105,180,0.25); }
    .tab.active { background:linear-gradient(135deg,#ffb6ce,#ff69b4); color:#000 !important; border-color:#ff69b4; font-weight:700; box-shadow:0 6px 16px rgba(255,105,180,0.35); }
    .tab.active .count { background:#f1f3f5; color:#000 !important; }
    .tab:active:not(.active) { transform:translateY(0); box-shadow:0 2px 4px rgba(0,0,0,0.08); }
    @media (max-width:640px){ .tab { padding:9px 14px; font-size:13px; } }
    @media (prefers-reduced-motion:reduce){ .tab, .tab:hover { transition:none; } }
    .tabs::after { content:""; position:absolute; inset:auto 0 -6px 0; height:2px; background:linear-gradient(90deg,#ffd1e6,#ff69b4,#ffd1e6); border-radius:2px; opacity:.4; pointer-events:none; }
    .count { background:#f1f3f5; padding:2px 6px; border-radius:999px; font-weight:600; font-size:12px; }
        .toolbar { display:flex; justify-content:space-between; align-items:center; margin:10px 0 16px; gap:10px; flex-wrap:wrap; }
        .search { display:flex; align-items:center; gap:6px; }
        .search input, .search select { padding:8px 10px; border:1px solid #ddd; border-radius:6px; }
    .card { background:#fff; border:1px solid #e9ecef; border-radius:10px; overflow:hidden; }
    /* Pink theme reused */
    .pink-card { background:#ffe3ef; border:1px solid #ffc9dd; border-radius:16px; padding:14px; box-shadow:0 2px 8px rgba(0,0,0,0.06); }
    .data-table.theme-pink{ width:100%; border-collapse:separate; border-spacing:0; background:#fff; border-radius:12px; overflow:hidden; }
    .data-table.theme-pink thead th{ background:#ffc0d4; color:#333; padding:12px; font-weight:700; border-bottom:1px solid #f1a9c1; }
    .data-table.theme-pink thead th:nth-child(2){ background:#ff9fc1; color:#fff; }
    .data-table.theme-pink tbody td{ padding:12px; border-bottom:1px solid #f6c8d7; }
    .data-table.theme-pink tbody tr:last-child td{ border-bottom:none; }
    .data-table.theme-pink tbody td:nth-child(2){ color:#ff4d6d; font-weight:700; }
        .card-header { padding:12px 14px; border-bottom:1px solid #e9ecef; font-weight:600; color:#333; }
        table { width:100%; border-collapse:collapse; }
        th, td { text-align:left; padding:10px 12px; border-bottom:1px solid #f1f3f5; font-size:14px; }
        .actions { display:flex; gap:6px; }
        .btn-xs { padding:6px 10px; font-size:12px; border-radius:6px; border:1px solid #e9ecef; background:#fff; cursor:pointer; }
    .btn-sm { padding:8px 14px; font-size:12px; border-radius:6px; border:1px solid #e9ecef; background:#fff; cursor:pointer; }
    /* Use global .btn-primary styles */
    .btn-primary {}
    .btn-danger { border-color:#dc3545; background-color:#dc3545; color:#fff; }
    .btn-danger:hover { filter: brightness(0.95); }
    .btn-success { border-color:#28a745; background-color:#28a745; color:#fff; }
    .btn-success:hover { filter: brightness(0.95); }
        .pagination { display:flex; gap:6px; align-items:center; padding:12px; justify-content:flex-end; }
        .pagination a { padding:6px 10px; border:1px solid #e9ecef; border-radius:6px; text-decoration:none; color:#333; }
        .empty { padding:20px; text-align:center; color:#666; }
        /* Pink card title included inside themed card */
        .pink-title { font-weight:700; color:#333; font-size:16px; margin: 0 8px 10px 8px; }
    </style>
    <script>
        function toggleSidebar(){ if (typeof window.toggleSidebar === 'function') { return window.toggleSidebar(); } }
        function closeAllModals(){
            try{
                // Hide any app modals
                var modals = document.querySelectorAll('.modal, #health-screening-detail-modal');
                modals.forEach(function(m){ if (m && m.style) m.style.display='none'; });
                // Also clear mobile sidebar overlay/body lock if active
                var overlay = document.getElementById('admin-sidebar-overlay') || document.querySelector('.sidebar-overlay');
                if (overlay && overlay.classList) overlay.classList.remove('active');
                var sidebar = document.getElementById('sidebar') || document.querySelector('.sidebar');
                if (sidebar && sidebar.classList) sidebar.classList.remove('open');
                document.body.classList.remove('no-scroll');
            }catch(e){ /* noop */ }
        }
        function post(url, method='POST'){
            closeAllModals();
            return fetch(url, { method, headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content } })
                .then(function(){ location.reload(); });
        }
        function openModal(id){ var m=document.getElementById(id); if(m) m.style.display='flex'; }
        function closeModal(id){ var m=document.getElementById(id); if(m) m.style.display='none'; }
    function logout(){ if (window.logout) return window.logout(); openModal('logoutModal'); setTimeout(function(){ var c=document.getElementById('logout-cancel-btn'); if(c) c.focus(); }, 0); }
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
    <style>
        /* Remove SweetAlert2 backdrop (dark overlay) for this page only */
        body.health-screening-page .swal2-container.swal2-backdrop-show, 
        body.health-screening-page .swal2-container.swal2-noanimation { background: transparent !important; }
    </style>
    <script>
        // Add a body class so CSS override is scoped
        document.addEventListener('DOMContentLoaded', function(){ document.body.classList.add('health-screening-page'); });
        // Fallback: if any Swal calls explicitly set a backdrop, strip it unless developer opts out with data-allow-backdrop
        (function(){
            if(!window.Swal) return; const _fire = Swal.fire.bind(Swal);
            Swal.fire = function(opts){
                if(typeof opts === 'string' || Array.isArray(opts)) return _fire(opts);
                opts = opts || {};
                if(opts && (opts.backdrop === undefined || opts.backdrop === true)){
                    opts.backdrop = false; // disable overlay
                }
                return _fire(opts);
            };
        })();
    </script>
    </head>
<body>
    @include('admin.partials.sidebar')
    <div class="main-content top-bar-space">
    @include('admin.partials.top-bar', ['pageTitle' => 'Health Screening'])
    <div class="container">
            @if(session('success'))
                <div style="margin: 0 0 10px; padding:10px 12px; border:1px solid #c3e6cb; background:#d4edda; color:#155724; border-radius:8px;">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div style="margin: 0 0 10px; padding:10px 12px; border:1px solid #f5c6cb; background:#f8d7da; color:#721c24; border-radius:8px;">{{ session('error') }}</div>
            @endif

            <div class="tabs-block" style="display:inline-block; max-width:100%;">
                <nav class="tabs" aria-label="Health Screening views" role="tablist">
                    <a class="tab {{ ($view ?? 'pending')==='pending' ? 'active' : '' }}" href="{{ route('admin.health-screening', ['view'=>'pending']) }}" {{ ($view ?? 'pending')==='pending' ? 'aria-current=page' : '' }}>Pending <span class="count" id="hs-count-pending">{{ $counts['pending'] ?? 0 }}</span></a>
                    <a class="tab {{ ($view ?? 'pending')==='accepted' ? 'active' : '' }}" href="{{ route('admin.health-screening', ['view'=>'accepted']) }}" {{ ($view ?? 'pending')==='accepted' ? 'aria-current=page' : '' }}>Accepted <span class="count" id="hs-count-accepted">{{ $counts['accepted'] ?? 0 }}</span></a>
                    <a class="tab {{ ($view ?? 'pending')==='declined' ? 'active' : '' }}" href="{{ route('admin.health-screening', ['view'=>'declined']) }}" {{ ($view ?? 'pending')==='declined' ? 'aria-current=page' : '' }}>Declined <span class="count" id="hs-count-declined">{{ $counts['declined'] ?? 0 }}</span></a>
                    <a class="tab {{ ($view ?? 'pending')==='archived' ? 'active' : '' }}" href="{{ route('admin.health-screening', ['view'=>'archived']) }}" {{ ($view ?? 'pending')==='archived' ? 'aria-current=page' : '' }}>Archived <span class="count" id="hs-count-archived">{{ $counts['archived'] ?? 0 }}</span></a>
                </nav>
                <form id="hs-search-form" method="get" action="{{ route('admin.health-screening') }}" class="search" style="position:relative; margin:4px 0 18px; width:100%;">
                    <input type="hidden" name="view" value="{{ $view ?? 'pending' }}" />
                    <input id="hs-search" aria-label="Search screenings" type="text" name="q" value="{{ $q ?? '' }}" placeholder="Search" style="padding:8px 34px 8px 12px; border:1px solid #e0e0e0; border-radius:30px; min-width:220px; height:38px; font-size:13px; width:100%; line-height:1;" />
                    <button type="button" id="hs-clear" aria-label="Clear search" title="Clear" style="display:none; position:absolute; right:10px; top:50%; transform:translateY(-50%); background:transparent; border:none; cursor:pointer; padding:4px; color:#888; font-size:16px; line-height:1;">&times;</button>
                </form>
            </div>
            @if(!($detailMode ?? false))
            {{-- Removed search toolbar for non-archived views as requested --}}

            <div class="card" id="hs-records-container">
                @php
                    $titleMap = [
                        'pending' => 'Pending Health Screenings',
                        'accepted' => 'Accepted Health Screenings',
                        'declined' => 'Declined Health Screenings',
                        'archived' => 'Archived Health Screenings',
                    ];
                    $currentTitle = $titleMap[$view ?? 'pending'] ?? 'Records';
                @endphp
                @if(($view ?? 'pending')==='archived')
                    <form id="hs-archived-form" method="get" action="{{ route('admin.health-screening') }}">
                        <input type="hidden" name="view" value="archived" />
                        <div style="padding:10px 12px; border-bottom:1px solid #e9ecef; display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                            <select name="archived_status" style="padding:8px 10px; border:1px solid #ddd; border-radius:6px;">
                                <option value="all" {{ ($archived_status ?? 'all')==='all'?'selected':'' }}>All</option>
                                <option value="accepted" {{ ($archived_status ?? '')==='accepted'?'selected':'' }}>Accepted</option>
                                <option value="declined" {{ ($archived_status ?? '')==='declined'?'selected':'' }}>Declined</option>
                            </select>
                            <select name="year" style="padding:8px 10px; border:1px solid #ddd; border-radius:6px;">
                                <option value="all" {{ ($year ?? 'all')==='all'?'selected':'' }}>All Years</option>
                                @for($y = now()->year; $y >= 2020; $y--)
                                    <option value="{{ $y }}" {{ (string)$y === (string)($year ?? '') ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                            <select name="month" style="padding:8px 10px; border:1px solid #ddd; border-radius:6px;">
                                <option value="all" {{ ($month ?? 'all')==='all'?'selected':'' }}>All Months</option>
                                @for($m=1;$m<=12;$m++)
                                    <option value="{{ $m }}" {{ (string)$m === (string)($month ?? '') ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                                @endfor
                            </select>
                            <button type="submit" class="btn-sm" style="padding:8px 14px;">Apply</button>
                        </div>
                    </form>
                @endif
                <div class="pink-card" style="overflow-x:auto;">
                    <div class="pink-title">{{ $currentTitle }}</div>
                    @if(($screenings ?? null) && count($screenings) > 0)
                    <table class="data-table theme-pink">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Contact</th>
                                <th>
                                    @if(($view ?? 'pending')==='pending')
                                        Submitted
                                    @elseif(($view ?? 'pending')==='archived')
                                        Archived
                                    @else
                                        Updated
                                    @endif
                                </th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($screenings as $row)
                                @php
                                    if(($view ?? 'pending')==='pending'){
                                        $dateRef = $row->created_at;
                                    } elseif(($view ?? 'pending')==='archived') {
                                        $dateRef = $row->archived_at ?? $row->updated_at ?? $row->created_at;
                                    } else {
                                        $dateRef = $row->updated_at ?? $row->created_at;
                                    }
                                @endphp
                                <tr>
                                    <td>{{ $row->Full_Name }}</td>
                                    <td>{{ $row->Contact_Number }}</td>
                                    <td>{{ $dateRef ? \Carbon\Carbon::parse($dateRef)->format('M d, Y h:i A') : '—' }}</td>
                                    <td><span class="status {{ $row->status }}">{{ ucfirst($row->status) }}</span></td>
                                    <td class="actions">
                                        <a class="btn-xs btn-primary" href="{{ route('admin.health-screening.show', $row->Health_Screening_ID) }}?view={{ $view ?? 'pending' }}" onclick="return openHealthScreeningModal({{ $row->Health_Screening_ID }})">Review</a>
                                        @if(($view ?? 'pending')==='pending')
                                            {{-- Inline Accept/Decline buttons removed; actions now handled inside the review modal/page --}}
                                        @endif
                                        @if(($view ?? 'pending')!=='pending' && ($view ?? 'pending')!=='archived')
                                            <button type="button" class="btn-xs btn-success" onclick="if(window.saConfirm){ saConfirm({title:'Archive this screening?', icon:'warning', confirmButtonText:'Archive'}).then(r=>{ if(r.isConfirmed){ try{ localStorage.setItem('hsAction','archive'); }catch(e){} post('{{ url('/admin/health-screening') }}/{{ $row->Health_Screening_ID }}/archive'); } }); } else { if(confirm('Archive this screening?')){ try{ localStorage.setItem('hsAction','archive'); }catch(e){} post('{{ url('/admin/health-screening') }}/{{ $row->Health_Screening_ID }}/archive'); } }">Archive</button>
                                        @endif
                                        @if(($view ?? 'pending')==='archived')
                                            <button type="button" class="btn-xs btn-success" onclick="if(window.saConfirm){ saConfirm({title:'Unarchive this screening?', confirmButtonText:'Unarchive'}).then(r=>{ if(r.isConfirmed){ try{ localStorage.setItem('hsAction','unarchive'); }catch(e){} post('{{ url('/admin/health-screening') }}/{{ $row->Health_Screening_ID }}/unarchive'); } }); } else { if(confirm('Unarchive this screening?')){ try{ localStorage.setItem('hsAction','unarchive'); }catch(e){} post('{{ url('/admin/health-screening') }}/{{ $row->Health_Screening_ID }}/unarchive'); } }">Unarchive</button>
                                            <button type="button" class="btn-xs btn-danger" onclick="if(window.saConfirm){ saConfirm({title:'Delete screening permanently?', text:'This cannot be undone.', icon:'error', confirmButtonText:'Delete'}).then(r=>{ if(r.isConfirmed){ try{ localStorage.setItem('hsAction','delete'); }catch(e){} post('{{ url('/admin/health-screening') }}/{{ $row->Health_ScreenING_ID ?? $row->Health_Screening_ID }}','DELETE'); } }); } else { if(confirm('Permanently delete this screening?')){ try{ localStorage.setItem('hsAction','delete'); }catch(e){} post('{{ url('/admin/health-screening') }}/{{ $row->Health_Screening_ID }}','DELETE'); } }">Delete</button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                        <div class="empty" style="background:#fff; border:1px solid #f6c8d7; border-radius:10px;">No records found.</div>
                    @endif
                </div>
            </div>
            @endif

            @if($detailMode ?? false)
                <div class="card" style="margin-top:10px;">
                    <div class="card-header">Health Screening Detail</div>
                    <div class="card-body" style="padding:16px;">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:20px; flex-wrap:wrap; margin-bottom:12px;">
                            <div style="flex:1 1 340px;">
                                <h3 style="margin:0 0 10px; font-size:18px; color:#333;">Overview</h3>
                                <div style="display:grid; grid-template-columns: repeat(auto-fit,minmax(220px,1fr)); gap:10px;">
                                    <div style="background:#f8f9fa; border:1px solid #eceff2; padding:10px; border-radius:8px;">
                                        <div style="font-size:12px; color:#666;">Donor</div>
                                        <div style="font-weight:600;">{{ $screening->user->Full_Name ?? 'N/A' }}</div>
                                    </div>
                                    <div style="background:#f8f9fa; border:1px solid #eceff2; padding:10px; border-radius:8px;">
                                        <div style="font-size:12px; color:#666;">Contact</div>
                                        <div style="font-weight:600;">{{ $screening->user->Contact_Number ?? 'N/A' }}</div>
                                    </div>
                                    <div style="background:#f8f9fa; border:1px solid #eceff2; padding:10px; border-radius:8px;">
                                        <div style="font-size:12px; color:#666;">Status</div>
                                        <div><span class="status {{ $screening->status }}">{{ $screening->status }}</span></div>
                                    </div>
                                    <div style="background:#f8f9fa; border:1px solid #eceff2; padding:10px; border-radius:8px;">
                                        <div style="font-size:12px; color:#666;">Submitted</div>
                                        <div style="font-weight:600;">{{ optional($screening->created_at)->format('M d, Y h:i A') }}</div>
                                    </div>
                                    <div style="background:#f8f9fa; border:1px solid #eceff2; padding:10px; border-radius:8px;">
                                        <div style="font-size:12px; color:#666;">Updated</div>
                                        <div style="font-weight:600;">{{ optional($screening->updated_at)->format('M d, Y h:i A') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div style="flex:1 1 320px; max-width:420px;">
                                @if(($screening->status ?? 'pending') === 'pending')
                                    <form method="POST" action="{{ route('admin.health-screening.update-status', $screening->Health_Screening_ID) }}" style="display:grid; gap:10px;" onsubmit="try{ if(event.submitter && event.submitter.name==='status' && event.submitter.value==='accepted'){ localStorage.setItem('hsAction','accept'); } }catch(e){}">
                                        @csrf
                                        <label style="font-size:12px; color:#555; font-weight:600; display:flex; align-items:center; gap:6px;">
                                            <span>Admin Notes <span style="font-weight:400; color:#888;">(required only when declining)</span></span>
                                        </label>
                                        <textarea id="admin-notes-textarea" name="admin_notes" placeholder="Enter notes explaining the reason if declining" style="width:100%; min-height:90px; padding:10px; border:1px solid #e1e5ea; border-radius:6px; resize:vertical;">{{ old('admin_notes', $screening->admin_notes) }}</textarea>
                                        <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                            <button name="status" value="accepted" class="btn btn-success" type="submit">Accept</button>
                                            <button name="status" value="declined" class="btn btn-danger" type="submit" onclick="var ta=document.getElementById('admin-notes-textarea'); if(ta){ if(!ta.value.trim()){ ta.focus(); if(window.saToast){ saToast('Please enter notes before declining.','error'); } else { alert('Notes are required when declining.'); } event.preventDefault(); return false; } } try{ localStorage.setItem('hsAction','decline'); }catch(e){}">Decline</button>
                                            <a class="btn" href="{{ route('admin.health-screening', ['view' => $view ?? 'pending']) }}">Back</a>
                                        </div>
                                        @error('admin_notes')
                                            <div style="color:#b00020; font-size:12px;">{{ $message }}</div>
                                        @enderror
                                    </form>
                                @endif
                            </div>
                        </div>

                        @if($screening->archived_at)
                            <div style="margin:10px 0 20px; display:flex; gap:10px; flex-wrap:wrap;">
                                <form method="POST" action="{{ url('/admin/health-screening/'.$screening->Health_Screening_ID.'/unarchive') }}">@csrf<button class="btn btn-success" type="submit">Unarchive</button></form>
                                <form method="POST" action="{{ url('/admin/health-screening/'.$screening->Health_Screening_ID) }}" onsubmit="if(window.saConfirm){ event.preventDefault(); saConfirm({title:'Delete screening permanently?', text:'This cannot be undone.', icon:'error', confirmButtonText:'Delete'}).then(r=>{ if(r.isConfirmed){ try{ localStorage.setItem('hsAction','delete'); }catch(e){} this.submit(); } }); return false;} else { if(confirm('Permanently delete this screening?')){ try{ localStorage.setItem('hsAction','delete'); }catch(e){} return true; } return false; }">@csrf @method('DELETE')<button class="btn btn-danger" type="submit">Delete</button></form>
                            </div>
                        @elseif(($view ?? 'pending')!=='pending')
                            <div style="margin:10px 0 20px;">
                                <form method="POST" action="{{ url('/admin/health-screening/'.$screening->Health_Screening_ID.'/archive') }}">@csrf<button class="btn btn-success" type="submit">Archive</button></form>
                            </div>
                        @endif

                        @if($screening->admin_notes)
                            <div style="background:#e7f3ff; border:1px solid #b3d9ff; padding:12px 14px; border-radius:8px; margin:0 0 20px;">
                                <strong>Admin Notes:</strong> {{ $screening->admin_notes }}
                            </div>
                        @endif

                        @if(($infants ?? null) && count($infants) > 0)
                            <h3 style="margin:20px 0 10px; font-size:18px;">Infant Information</h3>
                            @foreach($infants as $infant)
                                <div style="border:1px solid #e1e5ea; border-radius:8px; padding:12px 14px; margin:0 0 12px;">
                                    <strong>Infant #{{ $loop->iteration }}</strong>
                                    <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:8px; margin-top:8px; font-size:13px;">
                                        <div><span style="color:#666;">Name:</span> {{ $infant->infant_name ?? $infant->Full_Name ?? 'N/A' }}</div>
                                        <div><span style="color:#666;">DOB:</span> {{ isset($infant->date_of_birth) ? \Carbon\Carbon::parse($infant->date_of_birth)->format('M d, Y') : (isset($infant->Date_Of_Birth)?\Carbon\Carbon::parse($infant->Date_Of_Birth)->format('M d, Y'):'N/A') }}</div>
                                        <div><span style="color:#666;">Age:</span> {{ $infant->age ?? $infant->Age ?? '—' }} {{ isset($infant->age)||isset($infant->Age) ? 'months' : '' }}</div>
                                        <div><span style="color:#666;">Sex:</span> {{ ucfirst($infant->sex ?? $infant->Sex ?? 'n/a') }}</div>
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        <div class="qa-section-title">Medical History</div>
                        <div class="qa-grid">
                            @foreach(($medicalHistory ?? []) as $answer)
                                <div class="qa-card">
                                    <span class="qa-badge">Q{{ $answer->question_number }}</span>
                                    <p class="qa-q">{{ $answer->question_text }}<small>Bisaya: {{ $answer->question_bisaya }}</small></p>
                                    <div class="qa-answer"><strong>Answer:</strong> <span class="ans-value">{{ $answer->answer }}</span></div>
                                    @if($answer->additional_info)
                                        <div class="qa-extra">{{ $answer->additional_info }}</div>
                                    @endif
                                </div>
                            @endforeach
                            @if(empty($medicalHistory) || count($medicalHistory)===0)
                                <div style="color:#666; font-size:13px;">No medical history answers.</div>
                            @endif
                        </div>

                        <div class="qa-section-title">Sexual History</div>
                        <div class="qa-grid">
                            @foreach(($sexualHistory ?? []) as $answer)
                                <div class="qa-card">
                                    <span class="qa-badge">Q{{ $answer->question_number }}</span>
                                    <p class="qa-q">{{ $answer->question_text }}<small>Bisaya: {{ $answer->question_bisaya }}</small></p>
                                    <div class="qa-answer"><strong>Answer:</strong> <span class="ans-value">{{ $answer->answer }}</span></div>
                                    @if($answer->additional_info)
                                        <div class="qa-extra">{{ $answer->additional_info }}</div>
                                    @endif
                                </div>
                            @endforeach
                            @if(empty($sexualHistory) || count($sexualHistory)===0)
                                <div style="color:#666; font-size:13px;">No sexual history answers.</div>
                            @endif
                        </div>

                        <div class="qa-section-title">Donor's Infant Questions</div>
                        <div class="qa-grid" style="margin-bottom:10px;">
                            @foreach(($donorInfant ?? []) as $answer)
                                <div class="qa-card">
                                    <span class="qa-badge">Q{{ $answer->question_number }}</span>
                                    <p class="qa-q">{{ $answer->question_text }}<small>Bisaya: {{ $answer->question_bisaya }}</small></p>
                                    <div class="qa-answer"><strong>Answer:</strong> <span class="ans-value">{{ $answer->answer }}</span></div>
                                    @if($answer->additional_info)
                                        <div class="qa-extra">{{ $answer->additional_info }}</div>
                                    @endif
                                </div>
                            @endforeach
                            @if(empty($donorInfant) || count($donorInfant)===0)
                                <div style="color:#666; font-size:13px;">No donor infant answers.</div>
                            @endif
                        </div>
                        <div style="margin-top:24px;">
                            <a class="btn" href="{{ route('admin.health-screening', ['view' => $view ?? 'pending']) }}">← Back to list</a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <script>
            // Unified global search + archived filter integration (milk-requests style)
            (function(){
                var searchForm = document.getElementById('hs-search-form');
                var searchInput = document.getElementById('hs-search');
                var clearBtn = document.getElementById('hs-clear');
                var archForm = document.getElementById('hs-archived-form');
                var debounceId = null;
                function updateFromDoc(doc){
                    var newContainer = doc.querySelector('#hs-records-container');
                    var curContainer = document.getElementById('hs-records-container');
                    if (newContainer && curContainer) curContainer.innerHTML = newContainer.innerHTML;
                    ['pending','accepted','declined','archived'].forEach(function(key){
                        var src = doc.querySelector('#hs-count-'+key);
                        var dst = document.querySelector('#hs-count-'+key);
                        if (src && dst) dst.textContent = src.textContent;
                    });
                }
                async function runFetch(){
                    if(!searchForm) return;
                    var url = new URL(searchForm.action, window.location.origin);
                    var view = '{{ $view ?? 'pending' }}';
                    url.searchParams.set('view', view);
                    var q = searchInput ? searchInput.value.trim() : '';
                    if(q) url.searchParams.set('q', q); else url.searchParams.delete('q');
                    if(view==='archived' && archForm){
                        var sels = archForm.querySelectorAll('select[name]');
                        sels.forEach(function(el){ var v=(el.value||'').trim(); if(v && v!=='all'){ url.searchParams.set(el.name, v); } else { url.searchParams.delete(el.name); } });
                    }
                    try {
                        var res = await fetch(url.toString(), { headers:{'X-Requested-With':'XMLHttpRequest'} });
                        var html = await res.text();
                        var parser = new DOMParser();
                        var doc = parser.parseFromString(html, 'text/html');
                        updateFromDoc(doc);
                    }catch(e){ /* silent */ }
                }
                function schedule(){ if(debounceId) clearTimeout(debounceId); debounceId=setTimeout(runFetch,250); }
                if(searchInput){
                    searchInput.addEventListener('input', function(){ if(clearBtn){ clearBtn.style.display = searchInput.value.trim()? 'inline-flex':'none'; } schedule(); });
                    if(clearBtn){
                        clearBtn.addEventListener('click', function(){ searchInput.value=''; clearBtn.style.display='none'; schedule(); searchInput.focus(); });
                        if(searchInput.value.trim()) clearBtn.style.display='inline-flex';
                    }
                }
                if(searchForm){ searchForm.addEventListener('submit', function(ev){ ev.preventDefault(); runFetch(); }); }
                if(archForm){
                    var sels=archForm.querySelectorAll('select[name]');
                    sels.forEach(function(s){ s.addEventListener('change', schedule); s.addEventListener('input', schedule); });
                    archForm.addEventListener('submit', function(ev){ ev.preventDefault(); runFetch(); });
                }
            })();
            // Turn session banners into SweetAlert toasts by default; for delete, show modal success
            document.addEventListener('DOMContentLoaded', function(){
                // Ensure any open modals are closed before form submissions related to health-screening
                document.addEventListener('submit', function(ev){
                    try{
                        var form = ev.target;
                        if (form && form.action && form.action.indexOf('/admin/health-screening') !== -1) {
                            closeAllModals();
                        }
                    } catch(e){ /* noop */ }
                }, true);
                @if(session('success'))
                    (function(){
                        var msg = @json(session('success'));
                        var action = null;
                        try { action = localStorage.getItem('hsAction'); } catch(e) {}
                        // Always clear flag
                        try { localStorage.removeItem('hsAction'); } catch(e) {}
                        if (window.Swal && (action === 'delete' || action === 'archive' || action === 'unarchive' || action === 'accept' || action === 'decline')) {
                            var titles = { delete: 'Deleted', archive: 'Archived', unarchive: 'Unarchived', accept: 'Accepted', decline: 'Declined' };
                            Swal.fire({ icon: 'success', title: titles[action] || 'Success', text: msg, confirmButtonText: 'OK' });
                        } else { if (window.saToast) saToast(msg, 'success'); }
                    })();
                @endif
                @if(session('error'))
                    if (window.saToast) saToast(@json(session('error')), 'error');
                @endif
            });
        </script>
    </div>

    <!-- Logout Confirmation Modal (match original admin) -->
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

    <!-- Health Screening Detail Modal -->
    <div id="health-screening-detail-modal" class="modal" role="dialog" aria-modal="true" aria-labelledby="hs-detail-title" style="display:none;">
        <div class="modal-content" style="max-width: 980px;">
            <div class="modal-header" style="background: linear-gradient(135deg, #ffb6ce, #ff69b4); color: #fff;">
                <h3 class="modal-title" id="hs-detail-title">Health Screening Detail</h3>
                <button class="close-btn" aria-label="Close" onclick="closeModal('health-screening-detail-modal')" type="button">&times;</button>
            </div>
            <div class="modal-body" id="health-screening-detail-body">
                <div style="text-align:center; color:#666; padding:20px;">Loading details...</div>
            </div>
        </div>
    </div>

    <script>
        function openHealthScreeningModal(id){
            var modalId = 'health-screening-detail-modal';
            var bodyEl = document.getElementById('health-screening-detail-body');
            if (bodyEl) bodyEl.innerHTML = '<div style="text-align:center; color:#666; padding:20px;">Loading details...</div>';
            openModal(modalId);

            var viewParam = encodeURIComponent('{{ $view ?? 'pending' }}');
            var url = '{{ url('/admin/health-screening') }}/' + id + '?view=' + viewParam;
            fetch(url, { headers: { 'X-Requested-With':'XMLHttpRequest' } })
                .then(function(r){ return r.text(); })
                .then(function(html){
                    try {
                        var parser = new DOMParser();
                        var doc = parser.parseFromString(html, 'text/html');
                        // Try to grab the inner detail area
                        var detail = doc.querySelector('.card-body');
                        var content = detail ? detail.innerHTML : '<div style="color:#a00;">Unable to load detail content.</div>';
                        if (bodyEl) bodyEl.innerHTML = content;
                    } catch(e) {
                        if (bodyEl) bodyEl.innerHTML = '<div style="color:#a00;">Failed to render detail content.</div>';
                    }
                })
                .catch(function(){ if (bodyEl) bodyEl.innerHTML = '<div style="color:#a00;">Error loading details.</div>'; });
            return false;
        }
    </script>
</body>
</html>
