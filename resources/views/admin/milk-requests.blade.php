<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Breastmilk Requests - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin.css?v=20250921') }}">
    <style>
        body { background:#f8f9fa; display:flex; min-height:100vh; }
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
    /* Wrapper to constrain search width to tab row width */
    .tabs-block { display:inline-block; max-width:100%; }
    .tabs-block form.search { margin:4px 0 18px; width:100%; }
    .tabs-block form.search input { width:100%; }
        .toolbar { display:flex; justify-content:space-between; align-items:center; margin:10px 0 16px; gap:10px; flex-wrap:wrap; }
        .search { display:flex; align-items:center; gap:6px; }
        .search input { padding:8px 10px; border:1px solid #ddd; border-radius:6px; }
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
    /* Use global .btn-primary (blue) */
    .btn-primary { }
    .btn-danger { border-color:#dc3545; background-color:#dc3545; color:#fff; }
    .btn-danger:hover { filter: brightness(0.95); }
    .btn-success { border-color:#28a745; background-color:#28a745; color:#fff; }
    .btn-success:hover { filter: brightness(0.95); }
        .empty { padding:18px; text-align:center; color:#666; }
    </style>
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
    </script>
</head>
<body>
    @include('admin.partials.sidebar')
    <div class="main-content top-bar-space">
        @include('admin.partials.top-bar', ['pageTitle' => 'Breastmilk Requests'])
        <div class="container">
            <div class="tabs-block">
                <nav class="tabs" aria-label="Breastmilk Request views" role="tablist">
                    <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
                        <a class="tab {{ ($view ?? 'pending')==='pending' ? 'active' : '' }}" href="{{ route('admin.milk-requests') }}?view=pending">Pending <span class="count" id="count-pending">{{ $counts['pending'] ?? 0 }}</span></a>
                        <a class="tab {{ ($view ?? 'pending')==='accepted' ? 'active' : '' }}" href="{{ route('admin.milk-requests') }}?view=accepted">Accepted <span class="count" id="count-accepted">{{ $counts['accepted'] ?? 0 }}</span></a>
                        <a class="tab {{ ($view ?? 'pending')==='declined' ? 'active' : '' }}" href="{{ route('admin.milk-requests') }}?view=declined">Declined <span class="count" id="count-declined">{{ $counts['declined'] ?? 0 }}</span></a>
                        <a class="tab {{ ($view ?? 'pending')==='archived' ? 'active' : '' }}" href="{{ route('admin.milk-requests') }}?view=archived">Archived <span class="count" id="count-archived">{{ $counts['archived'] ?? 0 }}</span></a>
                    </div>
                </nav>
                <!-- Search directly below tabs; full width equal to total tab row -->
                <form method="get" action="{{ route('admin.milk-requests') }}" class="search">
                    <input type="hidden" name="view" value="{{ $view ?? 'pending' }}" />
                    <input aria-label="Search requests" type="text" name="q" value="{{ $q ?? '' }}" placeholder="Search" style="padding:8px 12px; border:1px solid #e0e0e0; border-radius:30px; min-width:200px; font-size:13px; height:38px; line-height:1;" />
                </form>
            </div>

            @if(!($detailMode ?? false))
            <!-- toolbar removed: search moved inline with tabs for alignment -->

                <div class="card pink-card" id="records-container">
                <div class="card-header">Records</div>
                @if(($requests ?? null) && $requests->count() > 0)
                    <div style="overflow-x:auto;">
                        <table class="data-table theme-pink">
                            <thead>
                                <tr>
                                    <th>Requestor</th>
                                    <th>Recipient</th>
                                    <th>Appointment</th>
                                    @if(($view ?? 'pending') !== 'pending')
                                        <th>Bags</th>
                                        <th>Total Volume (ml)</th>
                                    @endif
                                    <th>Status</th>
                                    @if(($view ?? 'pending')==='pending')
                                        <th>Submitted</th>
                                    @else
                                        <th>Date & Time Received</th>
                                    @endif
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($requests as $row)
                                    <tr>
                                        <td>{{ $row->user_full_name }}</td>
                                        <td>{{ $row->recipient_name }}</td>
                                        <td>
                                            {{ $row->scheduled_date ? \Carbon\Carbon::parse($row->scheduled_date)->format('M d, Y') : '—' }}
                                            @if($row->scheduled_time)
                                                • {{ \Carbon\Carbon::createFromFormat('H:i:s', $row->scheduled_time, config('app.timezone'))->format('g:i A') }}
                                            @endif
                                        </td>
                                        @if(($view ?? 'pending') !== 'pending')
                                            <td>{{ $row->decided_number_of_bags ? number_format($row->decided_number_of_bags) : '—' }}</td>
                                            <td>{{ $row->decided_total_volume ? number_format($row->decided_total_volume) : '—' }}</td>
                                        @endif
                                        <td><span class="status {{ $row->status }}">{{ ucfirst($row->status === 'approved' ? 'accepted' : $row->status) }}</span></td>
                                        @if(($view ?? 'pending')==='pending')
                                            <td>
                                                @php $tz = config('app.timezone', 'UTC'); @endphp
                                                {{ !empty($row->created_at) ? \Carbon\Carbon::parse($row->created_at)->timezone($tz)->format('M d, Y h:i A') : '—' }}
                                            </td>
                                        @else
                                            <td>
                                                @php $tz = config('app.timezone', 'UTC'); @endphp
                                                {{ !empty($row->dispensed_at) ? \Carbon\Carbon::parse($row->dispensed_at)->timezone($tz)->format('M d, Y h:i A') : '—' }}
                                            </td>
                                        @endif
                                        <td class="actions">
                                            <a class="btn-xs btn-primary" href="{{ route('admin.milk-requests.show', $row->id) }}?view={{ $view ?? 'pending' }}" onclick="return openMilkRequestModal({{ $row->id }})">Review</a>
                                            @if($row->status==='pending')
                                                <form method="POST" action="{{ route('admin.milk-requests.update-status', $row->id) }}" class="decline-request-form" data-request-id="{{ $row->id }}" style="display:inline-block;" onsubmit="return handleDeclineSubmit(event,this)">
                                                    @csrf
                                                    <input type="hidden" name="status" value="declined" />
                                                    <input type="hidden" name="admin_notes" value="" />
                                                    <button type="submit" class="btn-xs btn-danger">Decline</button>
                                                </form>
                                            @elseif(($view ?? 'pending')!=='archived' && in_array($row->status, ['approved','declined','dispensed']))
                                                <form method="POST" action="{{ route('admin.milk-requests.archive', $row->id) }}" style="display:inline-block;" onsubmit="if(window.saConfirm){ event.preventDefault(); saConfirm({title:'Archive this request?', icon:'warning', confirmButtonText:'Archive'}).then(r=>{ if(r.isConfirmed){ try{ localStorage.setItem('mrAction','archive'); }catch(e){} this.submit(); } }); return false;} else { try{ localStorage.setItem('mrAction','archive'); }catch(e){} return confirm('Archive this request?'); }">
                                                    @csrf
                                                    <button type="submit" class="btn-xs btn-success">Archive</button>
                                                </form>
                                            @elseif(($view ?? 'pending')==='archived')
                                                <form method="POST" action="{{ route('admin.milk-requests.unarchive', $row->id) }}" style="display:inline-block;" onsubmit="if(window.saConfirm){ event.preventDefault(); saConfirm({title:'Unarchive this request?', confirmButtonText:'Unarchive'}).then(r=>{ if(r.isConfirmed){ try{ localStorage.setItem('mrAction','unarchive'); }catch(e){} this.submit(); } }); return false;} else { try{ localStorage.setItem('mrAction','unarchive'); }catch(e){} return confirm('Unarchive this request?'); }">
                                                    @csrf
                                                    <button type="submit" class="btn-xs btn-success">Unarchive</button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.milk-requests.destroy', $row->id) }}" style="display:inline-block;" onsubmit="if(window.saConfirm){ event.preventDefault(); saConfirm({title:'Delete this request permanently?', icon:'warning', confirmButtonText:'Delete', confirmButtonColor:'#dc3545'}).then(r=>{ if(r.isConfirmed){ try{ localStorage.setItem('mrAction','delete'); }catch(e){} this.submit(); } }); return false;} else { try{ localStorage.setItem('mrAction','delete'); }catch(e){} return confirm('Delete this archived request permanently?'); }">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-xs btn-danger">Delete</button>
                                                </form>
                                            @else
                                                <span style="color:#666; font-size:12px;">No actions</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                @else
                    <div class="empty">No records found.</div>
                @endif
            </div>
            @endif

            @if($detailMode ?? false)
            <div class="card">
                <div class="card-header">Breastmilk Request Detail</div>
                <div class="card-body" style="padding:16px;">
                    <div style="display:flex; gap:20px; align-items:flex-start; flex-wrap:wrap;">
                        <div style="flex:1 1 360px; min-width:300px;">
                            <h3 style="margin:0 0 10px; font-size:18px; color:#333;">Overview</h3>
                            <div style="display:grid; grid-template-columns: repeat(auto-fit,minmax(220px,1fr)); gap:10px;">
                                <div style="background:#f8f9fa; border:1px solid #eceff2; padding:10px; border-radius:8px;">
                                    <div style="font-size:12px; color:#666;">Requestor</div>
                                    <div style="font-weight:600;">{{ $requestRow->user_full_name ?? 'N/A' }}</div>
                                </div>
                                <div style="background:#f8f9fa; border:1px solid #eceff2; padding:10px; border-radius:8px;">
                                    <div style="font-size:12px; color:#666;">Contact</div>
                                    <div style="font-weight:600;">{{ $requestRow->contact ?? $requestRow->contact_number ?? 'N/A' }}</div>
                                </div>
                                <div style="background:#f8f9fa; border:1px solid #eceff2; padding:10px; border-radius:8px;">
                                    <div style="font-size:12px; color:#666;">Status</div>
                                    <div><span class="status {{ $requestRow->status }}">{{ $requestRow->status==='approved'?'accepted':$requestRow->status }}</span></div>
                                </div>
                                <div style="background:#f8f9fa; border:1px solid #eceff2; padding:10px; border-radius:8px;">
                                    <div style="font-size:12px; color:#666;">Submitted</div>
                                    <div style="font-weight:600;">{{ optional($requestRow->created_at)->format('M d, Y h:i A') }}</div>
                                </div>
                                
                            </div>

                            <h3 style="margin:14px 0 8px; font-size:16px; color:#333;">Recipient</h3>
                            <div style="display:grid; grid-template-columns: repeat(auto-fit,minmax(220px,1fr)); gap:10px;">
                                <div style="background:#f8f9fa; border:1px solid #eceff2; padding:10px; border-radius:8px;">
                                    <div style="font-size:12px; color:#666;">Name</div>
                                    <div style="font-weight:600;">{{ $requestRow->recipient_name ?? 'N/A' }}</div>
                                </div>
                                <div style="background:#f8f9fa; border:1px solid #eceff2; padding:10px; border-radius:8px;">
                                    <div style="font-size:12px; color:#666;">DOB</div>
                                    <div style="font-weight:600;">{{ $requestRow->recipient_dob ? \Carbon\Carbon::parse($requestRow->recipient_dob)->format('M d, Y') : 'N/A' }}</div>
                                </div>
                                <div style="background:#f8f9fa; border:1px solid #eceff2; padding:10px; border-radius:8px;">
                                    <div style="font-size:12px; color:#666;">Weight (kg)</div>
                                    <div style="font-weight:600;">{{ $requestRow->recipient_weight ?? '—' }}</div>
                                </div>
                                <div style="background:#f8f9fa; border:1px solid #eceff2; padding:10px; border-radius:8px;">
                                    <div style="font-size:12px; color:#666;">Needed By</div>
                                    <div style="font-weight:600;">{{ $requestRow->needed_by_date ? \Carbon\Carbon::parse($requestRow->needed_by_date)->format('M d, Y') : '—' }}</div>
                                </div>
                                <div style="background:#f8f9fa; border:1px solid #eceff2; padding:10px; border-radius:8px;">
                                    <div style="font-size:12px; color:#666;">Appointment</div>
                                    <div style="font-weight:600;">
                                        {{ $requestRow->scheduled_date ? \Carbon\Carbon::parse($requestRow->scheduled_date)->format('M d, Y') : '—' }}
                                        @if($requestRow->scheduled_time)
                                            • {{ \Carbon\Carbon::createFromFormat('H:i:s', $requestRow->scheduled_time, config('app.timezone'))->format('g:i A') }}
                                        @endif
                                    </div>
                                </div>
                                {{-- Medical condition removed from request flow --}}
                                @if($requestRow->decided_number_of_bags || $requestRow->decided_total_volume)
                                <div style="background:#e7f3ff; border:1px solid #b3d9ff; padding:10px; border-radius:8px; grid-column:1/-1; display:grid; grid-template-columns: repeat(auto-fit,minmax(220px,1fr)); gap:10px;">
                                    <div>
                                        <div style="font-size:12px; color:#666;">Dispensed Bags</div>
                                        <div style="font-weight:600;">{{ $requestRow->decided_number_of_bags ? number_format($requestRow->decided_number_of_bags) : '—' }}</div>
                                    </div>
                                    <div>
                                        <div style="font-size:12px; color:#666;">Dispensed Volume (ml)</div>
                                        <div style="font-weight:600;">{{ $requestRow->decided_total_volume ? number_format($requestRow->decided_total_volume) : '—' }}</div>
                                    </div>
                                    @if(!empty($requestRow->dispensed_at))
                                    <div>
                                        <div style="font-size:12px; color:#666;">Dispensed At</div>
                                        <div style="font-weight:600;">{{ \Carbon\Carbon::parse($requestRow->dispensed_at)->timezone(config('app.timezone','UTC'))->format('M d, Y h:i A') }}</div>
                                    </div>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>
                        <div style="flex:1 1 320px; max-width:420px;">
                            @if(($requestRow->status ?? 'pending') === 'pending')
                                
                                <form method="POST" action="{{ route('admin.milk-requests.update-status', $requestRow->id) }}" style="display:grid; gap:10px;">
                                    @csrf
                                    <h3 style="margin:14px 0 8px; font-size:16px; color:#333;">Select Pasteurized Batch</h3>
                                    <div style="display:grid; grid-template-columns: repeat(auto-fit,minmax(220px,1fr)); gap:10px; align-items:end;">
                                        <label style="display:flex; flex-direction:column; gap:6px;">
                                            <select name="selected_batch" id="selected-batch" style="padding:8px; border:1px solid #e1e5ea; border-radius:6px;">
                                                <option value="">— Any batch (FIFO) —</option>
                                            </select>
                                        </label>
                                        <div id="selected-batch-meta" style="font-size:12px; color:#666; display:none;">Batch info will appear here</div>
                                    </div>
                                    <textarea name="admin_notes" placeholder="Notes (optional)" style="width:100%; min-height:80px; padding:10px; border:1px solid #e1e5ea; border-radius:6px;">{{ old('admin_notes', $requestRow->admin_notes) }}</textarea>
                                    <h3 style="margin:4px 0 0; font-size:16px; color:#333;">Dispense Milk</h3>
                                    <div style="display:grid; grid-template-columns: repeat(auto-fit,minmax(160px,1fr)); gap:10px;">
                                        <label style="display:flex; flex-direction:column; gap:6px;">
                                            <span style="font-size:12px; color:#666;">Number of Bags</span>
                                            <input type="number" min="1" name="decided_number_of_bags" value="{{ old('decided_number_of_bags', $requestRow->decided_number_of_bags) }}" placeholder="e.g., 2" style="padding:8px; border:1px solid #e1e5ea; border-radius:6px;">
                                        </label>
                                        <label style="display:flex; flex-direction:column; gap:6px;">
                                            <span style="font-size:12px; color:#666;">Total Volume (ml)</span>
                                            <input type="number" min="1" name="decided_total_volume" value="{{ old('decided_total_volume', $requestRow->decided_total_volume) }}" placeholder="e.g., 500" style="padding:8px; border:1px solid #e1e5ea; border-radius:6px;">
                                        </label>
                                    </div>
                                    <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                        <button name="status" value="approved" class="btn btn-success" type="submit" onclick="try{ localStorage.setItem('mrAction','accept'); }catch(e){}">Accept</button>
                                        <button name="status" value="declined" class="btn btn-danger" type="submit" onclick="if(!ensureDeclineNotes(this)){return false;} try{ localStorage.setItem('mrAction','decline'); }catch(e){}">Decline</button>
                                        <a class="btn" href="{{ route('admin.milk-requests', ['view' => $view ?? 'pending']) }}">Back</a>
                                    </div>
                                </form>
                            @endif

                            @if(($requestRow->status ?? '') === 'approved' && empty($requestRow->dispensed_at))
                                <h3 style="margin:16px 0 10px; font-size:18px; color:#333;">Finalize Dispense</h3>
                                <form method="POST" action="{{ route('admin.milk-requests.dispense', $requestRow->id) }}" style="display:grid; gap:10px;">
                                    @csrf
                                    <h3 style="margin:4px 0 0; font-size:16px; color:#333;">Select Pasteurized Batch</h3>
                                    <div style="display:grid; grid-template-columns: repeat(auto-fit,minmax(220px,1fr)); gap:10px; align-items:end;">
                                        <label style="display:flex; flex-direction:column; gap:6px;">
                                            <select name="selected_batch" id="selected-batch" style="padding:8px; border:1px solid #e1e5ea; border-radius:6px;">
                                                <option value="">— Any batch (FIFO) —</option>
                                            </select>
                                        </label>
                                        <label style="display:flex; flex-direction:column; gap:6px;">
                                            <span style="font-size:12px; color:#666;">Number of Bags</span>
                                            <input type="number" min="1" name="decided_number_of_bags" value="{{ old('decided_number_of_bags', $requestRow->decided_number_of_bags) }}" placeholder="e.g., 2" style="padding:8px; border:1px solid #e1e5ea; border-radius:6px;" required>
                                        </label>
                                        <label style="display:flex; flex-direction:column; gap:6px;">
                                            <span style="font-size:12px; color:#666;">Total Volume (ml)</span>
                                            <input type="number" min="1" name="decided_total_volume" value="{{ old('decided_total_volume', $requestRow->decided_total_volume) }}" placeholder="e.g., 500" style="padding:8px; border:1px solid #e1e5ea; border-radius:6px;" required>
                                        </label>
                                        <label style="display:flex; flex-direction:column; gap:6px;">
                                            <span style="font-size:12px; color:#666;">Dispense Date (optional)</span>
                                            <input type="date" name="dispense_date" style="padding:8px; border:1px solid #e1e5ea; border-radius:6px;" value="{{ old('dispense_date') }}">
                                        </label>
                                        <label style="display:flex; flex-direction:column; gap:6px;">
                                            <span style="font-size:12px; color:#666;">Dispense Time (optional)</span>
                                            <input type="time" name="dispense_time" style="padding:8px; border:1px solid #e1e5ea; border-radius:6px;" value="{{ old('dispense_time') }}">
                                        </label>
                                    </div>
                                    <div id="selected-batch-meta" style="font-size:12px; color:#666; display:none;">Batch info will appear here</div>
                                    <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                        <button class="btn btn-success" type="submit">Mark as Dispensed</button>
                                        <a class="btn" href="{{ route('admin.milk-requests', ['view' => $view ?? 'accepted']) }}">Back</a>
                                    </div>
                                </form>
                                <script>setTimeout(function(){ populateBatchDropdown(document); }, 0);</script>
                            @endif

                            @if($requestRow->status!=='pending' && $requestRow->status!=='approved')
                                <div style="margin-top:10px; display:flex; gap:10px; flex-wrap:wrap;">
                                    @if(!$requestRow->archived_at)
                                        <form method="POST" action="{{ route('admin.milk-requests.archive', $requestRow->id) }}" onsubmit="try{ localStorage.setItem('mrAction','archive'); }catch(e){}">@csrf<button class="btn btn-success" type="submit">Archive</button></form>
                                    @else
                                        <form method="POST" action="{{ route('admin.milk-requests.unarchive', $requestRow->id) }}" onsubmit="try{ localStorage.setItem('mrAction','unarchive'); }catch(e){}">@csrf<button class="btn btn-success" type="submit">Unarchive</button></form>
                                    @endif
                                </div>
                            @endif
                            @if(!empty($requestRow->dispensed_at))
                            <div style="margin-top:12px; background:#fff7fb; border:1px solid #ffd1e6; padding:10px; border-radius:8px;">
                                <div style="font-weight:600; margin-bottom:8px; color:#7a2944;">Adjust Received Date & Time</div>
                                <form method="POST" action="{{ route('admin.milk-requests.update-dispense-time', $requestRow->id) }}" style="display:flex; gap:8px; flex-wrap:wrap; align-items:end;">
                                    @csrf
                                    <label style="display:flex; flex-direction:column; gap:6px;">
                                        <span style="font-size:12px; color:#666;">Date</span>
                                        <input type="date" name="dispense_date" required style="padding:8px; border:1px solid #e1e5ea; border-radius:6px;" value="{{ optional(\Carbon\Carbon::parse($requestRow->dispensed_at))->timezone(config('app.timezone','UTC'))->format('Y-m-d') }}">
                                    </label>
                                    <label style="display:flex; flex-direction:column; gap:6px;">
                                        <span style="font-size:12px; color:#666;">Time</span>
                                        <input type="time" name="dispense_time" required style="padding:8px; border:1px solid #e1e5ea; border-radius:6px;" value="{{ optional(\Carbon\Carbon::parse($requestRow->dispensed_at))->timezone(config('app.timezone','UTC'))->format('H:i') }}">
                                    </label>
                                    <button class="btn btn-primary" type="submit">Update</button>
                                </form>
                            </div>
                            @endif
                        </div>
                    </div>

                    <h3 style="margin:18px 0 10px; font-size:18px; color:#333;">Prescription</h3>
                    @if(!empty($prescriptionUrl))
                        <div style="background:#fff; border:1px solid #eceff2; padding:10px; border-radius:8px; max-width:760px;">
                            <div style="margin-bottom:8px; display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                                <a href="{{ $prescriptionUrl }}" target="_blank" rel="noopener" class="btn btn-primary btn-sm">Open in new tab</a>
                                <a href="{{ $prescriptionUrl }}" download class="btn btn-secondary btn-sm">Download</a>
                            </div>
                            @if(($prescriptionType ?? '')==='image')
                                <img src="{{ $prescriptionUrl }}" alt="Prescription Image" style="width:100%; height:auto; border-radius:6px; border:1px solid #e9ecef;" onerror="this.style.display='none'; this.insertAdjacentHTML('afterend','<div style=&quot;color:#a00; padding:8px 0;&quot;>Image not found or cannot be displayed.</div>');" />
                            @elseif(($prescriptionType ?? '')==='pdf')
                                <iframe src="{{ $prescriptionUrl }}" style="width:100%; height:600px; border:1px solid #e9ecef; border-radius:6px;" title="Prescription PDF"></iframe>
                            @else
                                <div style="color:#555;">This file type cannot be previewed here. Use the buttons above to open or download.</div>
                            @endif
                        </div>
                    @else
                        <div class="empty">No prescription file uploaded.</div>
                    @endif

                    @if($requestRow->admin_notes)
                        <div style="background:#e7f3ff; border:1px solid #b3d9ff; padding:12px 14px; border-radius:8px; margin:14px 0 0;">
                            <strong>Admin Notes:</strong> {{ $requestRow->admin_notes }}
                        </div>
                    @endif

                    <div style="margin-top:18px;">
                        <a class="btn" href="{{ route('admin.milk-requests', ['view' => $view ?? 'pending']) }}">← Back to list</a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Logout Confirmation Modal (shared) -->
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

    <!-- Milk Request Detail Modal -->
    <div id="milk-request-detail-modal" class="modal" role="dialog" aria-modal="true" aria-labelledby="milk-request-detail-title" style="display:none;">
        <div class="modal-content" style="max-width: 900px;">
            <div class="modal-header" style="background: linear-gradient(135deg, #ffb6ce, #ff69b4); color: #fff;">
                <h3 class="modal-title" id="milk-request-detail-title">Breastmilk Request Detail</h3>
                <button class="close-btn" aria-label="Close" onclick="closeModal('milk-request-detail-modal')" type="button">&times;</button>
            </div>
            <div class="modal-body" id="milk-request-detail-body">
                <div style="text-align:center; color:#666; padding:20px;">Loading details...</div>
            </div>
        </div>
    </div>

    <script>
        // Handle decline submit from list: require notes
        function handleDeclineSubmit(ev, form){
            ev.preventDefault();
            // If SweetAlert available, use it for input; else fallback to prompt
            var submitAction = function(notes){
                if(!notes || !notes.trim()){
                    if(window.saError) saError('Comments/Notes are required to decline.'); else alert('Comments/Notes are required to decline.');
                    return;
                }
                form.querySelector('input[name="admin_notes"]').value = notes.trim();
                if(window.saConfirm){
                    saConfirm({title:'Decline this request?', icon:'warning', confirmButtonText:'Decline'}).then(function(r){ if(r.isConfirmed){ form.submit(); }});
                } else {
                    if(confirm('Decline this request?')) form.submit();
                }
            };
            if(window.Swal){
                Swal.fire({
                    title:'Decline Request',
                    html:'<textarea id="decline-notes" class="swal2-textarea" placeholder="Enter comments/notes (required)"></textarea>',
                    icon:'warning',
                    showCancelButton:true,
                    confirmButtonText:'Decline',
                    preConfirm:()=>{
                        var val = document.getElementById('decline-notes').value;
                        if(!val.trim()){
                            Swal.showValidationMessage('Comments/Notes required');
                            return false;
                        }
                        return val;
                    }
                }).then(function(res){ if(res.isConfirmed){ submitAction(res.value); } });
            } else {
                var txt = prompt('Enter comments/notes (required)');
                submitAction(txt);
            }
            return false;
        }

        function ensureDeclineNotes(btn){
            try {
                var form = btn.closest('form');
                if(!form) return true;
                var statusVal = btn.getAttribute('value') || btn.value;
                if(statusVal !== 'declined') return true;
                // Look for textarea[name=admin_notes]
                var notes = form.querySelector('textarea[name="admin_notes"]');
                if(notes){
                    if(!notes.value.trim()){
                        if(window.saError) saError('Comments/Notes are required to decline.'); else alert('Comments/Notes are required to decline.');
                        notes.focus();
                        return false;
                    }
                } else {
                    // If not present (unlikely in detail view), fallback to prompt
                    var val = prompt('Enter comments/notes (required)');
                    if(!val || !val.trim()) return false;
                    var hidden = document.createElement('input');
                    hidden.type='hidden'; hidden.name='admin_notes'; hidden.value=val.trim();
                    form.appendChild(hidden);
                }
                return true;
            } catch(e){ return true; }
        }

        // Live search for this page: fetch updates while typing
        (function(){
            var searchForm = document.querySelector('form.search');
            var searchInput = searchForm ? searchForm.querySelector('input[name="q"]') : null;
            var debounceId = null;
            function replaceFrom(doc){
                var newContainer = doc.querySelector('#records-container');
                var curContainer = document.getElementById('records-container');
                if (newContainer && curContainer) { curContainer.innerHTML = newContainer.innerHTML; }
                ['pending','accepted','declined','archived'].forEach(function(key){
                    var src = doc.querySelector('#count-'+key);
                    var dst = document.querySelector('#count-'+key);
                    if (src && dst) dst.textContent = src.textContent;
                });
            }
            async function runFetch(){
                if (!searchForm) return;
                var url = new URL(searchForm.action, window.location.origin);
                url.searchParams.set('view', '{{ $view ?? 'pending' }}');
                var q = searchInput ? searchInput.value.trim() : '';
                if (q) url.searchParams.set('q', q); else url.searchParams.delete('q');
                try{
                    var res = await fetch(url.toString(), { headers: { 'X-Requested-With':'XMLHttpRequest' } });
                    var html = await res.text();
                    var parser = new DOMParser();
                    var doc = parser.parseFromString(html, 'text/html');
                    replaceFrom(doc);
                }catch(e){/* ignore */}
            }
            function schedule(){ if (debounceId) clearTimeout(debounceId); debounceId = setTimeout(runFetch, 250); }
            if (searchInput){
                searchInput.addEventListener('input', schedule);
                searchForm.addEventListener('submit', function(ev){ ev.preventDefault(); runFetch(); });
            }
        })();
        async function populateBatchDropdown(container){
            try{
                var root = container || document;
                var sel = root.querySelector('#selected-batch');
                if(!sel) return;
                // Avoid double-populating
                if(sel.getAttribute('data-loaded')==='1') return;
                const r = await fetch('/admin/inventory/batches');
                const d = await r.json();
                if(d && d.success && Array.isArray(d.data)){
                    d.data.forEach(function(b){
                        var opt=document.createElement('option');
                        opt.value=b.batch_number;
                        opt.textContent=b.batch_number + ' • ' + (b.items_count||0) + ' items • ' + (b.total_volume||0) + ' ml';
                        sel.appendChild(opt);
                    });
                    sel.setAttribute('data-loaded','1');
                    var meta = root.querySelector('#selected-batch-meta');
                    if(meta){ meta.style.display='block'; meta.textContent='Pick a batch to use. If omitted, the system will allocate FIFO.'; }
                }
            }catch(e){ /* ignore */ }
        }
        function openMilkRequestModal(id){
            var modalId = 'milk-request-detail-modal';
            var bodyEl = document.getElementById('milk-request-detail-body');
            if (bodyEl) {
                bodyEl.innerHTML = '<div style="text-align:center; color:#666; padding:20px;">Loading details...</div>';
            }
            openModal(modalId);

            var viewParam = encodeURIComponent('{{ $view ?? 'pending' }}');
            var url = '{{ url('/admin/milk-requests') }}/' + id + '?view=' + viewParam;
            fetch(url, { headers: { 'X-Requested-With':'XMLHttpRequest' } })
                .then(function(r){ return r.text(); })
                .then(function(html){
                    try {
                        var parser = new DOMParser();
                        var doc = parser.parseFromString(html, 'text/html');
                        // Try to locate the detail card content from the show page
                        var card = doc.querySelector('.card');
                        var content = card ? card.innerHTML : '<div style="color:#a00;">Unable to load detail content.</div>';
                        if (bodyEl) {
                            bodyEl.innerHTML = content;
                            // Populate batch dropdown if present in loaded content
                            setTimeout(function(){ populateBatchDropdown(bodyEl); }, 0);
                        }
                    } catch(e) {
                        if (bodyEl) bodyEl.innerHTML = '<div style="color:#a00;">Failed to render detail content.</div>';
                    }
                })
                .catch(function(){ if (bodyEl) bodyEl.innerHTML = '<div style="color:#a00;">Error loading details.</div>'; });

            return false; // prevent navigation
        }

        // Convert archive/unarchive success into SweetAlert modal; clear overlays on submit
        document.addEventListener('DOMContentLoaded', function(){
            // Close any open modal overlays before submitting forms on this page
            document.addEventListener('submit', function(ev){
                try{
                    var form = ev.target;
                    if (form && form.action && form.action.indexOf('/admin/milk-requests') !== -1) {
                        var m = document.getElementById('milk-request-detail-modal');
                        if (m) m.style.display = 'none';
                        var overlay = document.getElementById('admin-sidebar-overlay') || document.querySelector('.sidebar-overlay');
                        if (overlay && overlay.classList) overlay.classList.remove('active');
                        document.body.classList.remove('no-scroll');
                    }
                } catch(e){ /* noop */ }
            }, true);

            @if(session('success'))
                (function(){
                    var msg = @json(session('success'));
                    var action = null;
                    try { action = localStorage.getItem('mrAction'); } catch(e) {}
                    try { localStorage.removeItem('mrAction'); } catch(e) {}
                    if (window.Swal && (action === 'archive' || action === 'unarchive' || action === 'delete' || action === 'accept')) {
                        var titles = { archive: 'Archived', unarchive: 'Unarchived', delete: 'Deleted', accept: 'Accepted' };
                        Swal.fire({ icon: 'success', title: titles[action] || 'Success', text: msg, confirmButtonText: 'OK' });
                    } else {
                        if (window.saToast) saToast(msg, 'success');
                    }
                })();
            @endif
            @if(session('error'))
                if (window.saToast) saToast(@json(session('error')), 'error');
            @endif
        });
    </script>
</body>
</html>
