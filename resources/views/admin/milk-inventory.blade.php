<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Milk Inventory</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css?v=20250921') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background:#f8f9fa; display:flex; min-height:100vh; }
        .main-content { flex: 1; display:flex; flex-direction:column; }
        .content { padding: 22px; }
        .tabs { display:flex; gap:10px; flex-wrap:wrap; margin:0 0 18px; position:relative; }
        .tab { --tab-bg:#ffffff; --tab-border:#e5e7eb; --tab-color:#333; display:inline-flex; align-items:center; gap:8px; padding:10px 18px; border-radius:999px; background:var(--tab-bg); border:1px solid var(--tab-border); color:var(--tab-color); text-decoration:none; font-size:14px; font-weight:500; line-height:1; position:relative; box-shadow:0 2px 4px rgba(0,0,0,0.05); transition: background .35s, color .25s, box-shadow .35s, transform .35s, border-color .35s; }
        .tab .count { background:#f1f3f5; color:#444; padding:3px 8px; border-radius:999px; font-weight:600; font-size:12px; line-height:1; transition:background .35s, color .35s; }
        .tab:hover { background:#fff7fb; border-color:#ffc1d8; box-shadow:0 4px 10px rgba(255,105,180,0.18); transform:translateY(-2px); }
        .tab.active { background:linear-gradient(135deg,#ffb6ce,#ff69b4); color:#fff; border-color:#ff69b4; font-weight:600; box-shadow:0 6px 16px rgba(255,105,180,0.35); }
        .tab.active .count { background:rgba(255,255,255,0.25); color:#fff; }
        .tabs::after { content:""; position:absolute; inset:auto 0 -6px 0; height:2px; background:linear-gradient(90deg,#ffd1e6,#ff69b4,#ffd1e6); border-radius:2px; opacity:.4; pointer-events:none; }
        .card { background:#fff; border:1px solid #e9ecef; border-radius:10px; overflow:hidden; }
        .card-header { padding:12px 14px; border-bottom:1px solid #e9ecef; font-weight:600; color:#333; }
        table { width:100%; border-collapse:collapse; }
        th, td { text-align:left; padding:10px 12px; border-bottom:1px solid #f1f3f5; font-size:14px; }
        .empty { padding:18px; text-align:center; color:#666; }
        .btn-sm { padding:6px 10px; font-size:12px; border-radius:6px; border:1px solid #e9ecef; background:#fff; cursor:pointer; }
    /* Button color utilities */
    .btn-blue { background:#0d6efd; border-color:#0d6efd; color:#fff; }
    .btn-blue:hover { filter:brightness(0.95); }
    .btn-green { background:#28a745; border-color:#28a745; color:#fff; }
    .btn-green:hover { filter:brightness(0.95); }
    /* Pink themed card + table to match Donation History */
    .pink-card { background:#ffe3ef; border:1px solid #ffc9dd; border-radius:16px; padding:14px; box-shadow:0 2px 8px rgba(0,0,0,0.06); }
    .pink-title { font-weight:700; color:#333; font-size:16px; margin: 0 8px 10px 8px; }
    .data-table.theme-pink{ width:100%; border-collapse:separate; border-spacing:0; background:#fff; border-radius:12px; overflow:hidden; }
    .data-table.theme-pink thead th{ background:#ffc0d4; color:#333; padding:12px; font-weight:700; border-bottom:1px solid #f1a9c1; }
    .data-table.theme-pink thead th:nth-child(2){ background:#ff9fc1; color:#fff; }
    .data-table.theme-pink tbody td{ padding:12px; border-bottom:1px solid #f6c8d7; }
    .data-table.theme-pink tbody tr:last-child td{ border-bottom:none; }
    .data-table.theme-pink tbody td:nth-child(2){ color:#ff4d6d; font-weight:700; }
    </style>
    <script>
        function toggleSidebar(){ if (typeof window.toggleSidebar === 'function') { return window.toggleSidebar(); } }
        function openModal(id){ const el=document.getElementById(id); if(el) el.style.display='flex'; }
        function closeModal(id){ const el=document.getElementById(id); if(el) el.style.display='none'; }
        function getParam(name){ const url=new URL(window.location.href); return url.searchParams.get(name); }
        function setActiveTab(tab){
            const tabs=['unpasteurized','pasteurized','dispensed'];
            tabs.forEach(t=>{
                const a=document.getElementById('tab-'+t); const p=document.getElementById('panel-'+t);
                if(a) a.classList.toggle('active', t===tab);
                if(p) p.style.display = t===tab ? 'block' : 'none';
            });
        }
        function formatDateStr(s){ try{ return s? new Date(s).toLocaleDateString(): 'N/A'; }catch(e){ return s||'N/A'; } }
        function formatTimeStr(t){ 
            if(!t) return 'TBD'; 
            try{ 
                const s=t.length===5? t+':00':t; 
                // Create date with Singapore timezone (UTC+8)
                const d=new Date('2000-01-01T'+s+'+08:00'); 
                if(!isNaN(d.getTime())){ 
                    return d.toLocaleTimeString('en-US',{
                        hour:'numeric',
                        minute:'2-digit',
                        hour12:true,
                        timeZone:'Asia/Singapore'
                    }); 
                } 
            }catch(e){} 
            return t; 
        }

        function loadUnpasteurized(){
            const tbody=document.getElementById('inventory-unpasteurized-tbody'); if(!tbody) return;
            fetch('/admin/reports/inventory/unpasteurized')
                .then(r=>{ if(!r.ok) throw new Error('HTTP '+r.status); return r.json(); })
                .then(d=>{
                    if(!(d && d.success && Array.isArray(d.data))) return; // keep preloaded content on bad shape
                    const rows=d.data;
                    tbody.innerHTML='';
                    if(!rows.length){ tbody.innerHTML='<tr><td colspan="5" class="empty">No unpasteurized stock.</td></tr>'; }
                    else {
                        rows.forEach(x=>{
                            const tr=document.createElement('tr');
                            tr.innerHTML = `<td>${x.Donor_Name||'N/A'}</td>
                                            <td>${x.Number_of_Bags||'—'}</td>
                                            <td>${x.Total_Volume||'—'}</td>
                                            <td>${formatDateStr(x.Date)}</td>
                                            <td>${formatTimeStr(x.Time)}</td>`;
                            tbody.appendChild(tr);
                        });
                    }
                    const c=document.getElementById('count-tab-unpasteurized'); if(c) c.textContent=rows.length;
                })
                .catch(err=>{ console.warn('Unpasteurized refresh failed:', err); /* Keep preloaded content */ });
        }

        // SweetAlert prompt to pasteurize an unpasteurized inventory row
        function promptPasteurize(unpasteurizedId, bags, volume){
            if (typeof Swal === 'undefined') {
                const batch = window.prompt('Enter Batch Number to create for pasteurization:');
                if(!batch) return;
                return doPasteurize(unpasteurizedId, batch);
            }
            Swal.fire({
                title: 'Add to Pasteurized',
                html: '<div style="text-align:left">'
                    + '<label style="font-size:12px;color:#666;">Batch Number</label>'
                    + '<input id="swal-batch" class="swal2-input" placeholder="e.g., B-' + new Date().toISOString().slice(0,10) + '-001" />'
                    + '<div style="font-size:12px;color:#666;margin-top:6px;">This will create a pasteurized entry. Current stock: '
                    + String(bags||'—') + ' bags, ' + String(volume||'—') + ' ml.</div>'
                    + '</div>',
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Create Batch',
                preConfirm: () => {
                    const val = document.getElementById('swal-batch')?.value?.trim();
                    if(!val) { Swal.showValidationMessage('Batch number is required'); return false; }
                    return val;
                }
            }).then(res=>{
                if(res.isConfirmed){ doPasteurize(unpasteurizedId, res.value); }
            });
        }

        function doPasteurize(unpasteurizedId, batchNumber){
            fetch('/admin/inventory/pasteurize', {
                method: 'POST',
                headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                body: JSON.stringify({ unpasteurized_id: unpasteurizedId, batch_number: batchNumber })
            }).then(r=>r.json()).then(d=>{
                if(d && d.success){
                    if (typeof Swal !== 'undefined') Swal.fire({ icon:'success', title: 'Added to Pasteurized', timer:1200, showConfirmButton:false });
                    // Refresh both panels to reflect changes
                    loadPasteurized();
                    loadUnpasteurized();
                } else {
                    const msg = d && d.message ? d.message : 'Failed to create pasteurized batch';
                    if (typeof Swal !== 'undefined') Swal.fire({ icon:'error', title:'Error', text: msg }); else alert(msg);
                }
            }).catch(()=>{
                if (typeof Swal !== 'undefined') Swal.fire({ icon:'error', title:'Network Error', text:'Unable to create pasteurized batch right now.' });
            });
        }

        function loadPasteurized(){
            const tbody=document.getElementById('inventory-pasteurized-tbody'); if(!tbody) return;
            fetch('/admin/reports/inventory/pasteurized')
                .then(r=>{ if(!r.ok) throw new Error('HTTP '+r.status); return r.json(); })
                .then(d=>{
                    if(!(d && d.success && Array.isArray(d.data))) return; // keep preloaded content
                    const rows=d.data;
                    tbody.innerHTML='';
                    let totalVol=0;
                    if(!rows.length){ tbody.innerHTML='<tr><td colspan="4" class="empty">No pasteurized stock.</td></tr>'; }
                    else {
                        rows.forEach(x=>{
                            const vol  = Number(x.Total_Volume||0);
                            totalVol  += isNaN(vol)?0:vol;
                            const tr=document.createElement('tr');
                            tr.innerHTML = `<td>${x.Batch_Number||'—'}</td>
                                            <td>${x.Total_Volume||'—'}</td>
                                            <td>${formatDateStr(x.Date_Pasteurized)}</td>
                                            <td>${formatTimeStr(x.Time_Pasteurized)}</td>`;
                            tbody.appendChild(tr);
                        });
                    }
                    const tv=document.getElementById('pasteurized-total-vol'); if(tv) tv.textContent = (totalVol||0).toLocaleString();
                    const c=document.getElementById('count-tab-pasteurized'); if(c) c.textContent=rows.length;
                })
                .catch(err=>{ console.warn('Pasteurized refresh failed:', err); });
        }

        function loadDispensed(){
            const tbody=document.getElementById('inventory-dispensed-tbody'); if(!tbody) return;
            fetch('/admin/reports/inventory/dispensed')
                .then(r=>{ if(!r.ok) throw new Error('HTTP '+r.status); return r.json(); })
                .then(d=>{
                    if(!(d && d.success && Array.isArray(d.data))) return;
                    const rows=d.data;
                    tbody.innerHTML='';
                    if(!rows.length){ tbody.innerHTML='<tr><td colspan="6" class="empty">No dispensed records.</td></tr>'; }
                    else {
                        rows.forEach(x=>{
                            const tr=document.createElement('tr');
                            tr.innerHTML = `<td>${x.Guardian_Name||'N/A'}</td>
                                            <td>${x.Volume||'—'}</td>
                                            <td>${x.Recipient_Name||'—'}</td>
                                            <td>${x.Batch_Number||'—'}</td>
                                            <td>${formatDateStr(x.Date)}</td>
                                            <td>${formatTimeStr(x.Time)}</td>`;
                            tbody.appendChild(tr);
                        });
                    }
                    const c=document.getElementById('count-tab-dispensed'); if(c) c.textContent=rows.length;
                })
                .catch(err=>{ console.warn('Dispensed refresh failed:', err); });
        }

        function backfillUnpasteurized(){
            const btn = document.getElementById('btn-backfill-unpasteurized');
            if(btn) { btn.disabled = true; btn.textContent = 'Backfilling…'; }
            fetch('/admin/reports/inventory/unpasteurized/backfill', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
            }).then(r=>r.json()).then(d=>{
                alert(d.success ? (`Backfill complete. Inserted: ${d.inserted}`) : (d.message||'Backfill failed'));
                loadUnpasteurized();
            }).catch(()=>alert('Backfill request failed')).finally(()=>{
                if(btn) { btn.disabled = false; btn.textContent = 'Backfill Missing'; }
            });
        }

        function loadActiveTab(){
            const view=(getParam('view')||'unpasteurized').toLowerCase();
            const v=['unpasteurized','pasteurized','dispensed'].includes(view)? view: 'unpasteurized';
            setActiveTab(v);
            if(v==='unpasteurized') loadUnpasteurized();
            if(v==='pasteurized') loadPasteurized();
            if(v==='dispensed') loadDispensed();
        }

        document.addEventListener('DOMContentLoaded', function(){
            loadActiveTab();
        });
    </script>
</head>
<body>
    @include('admin.partials.sidebar')
    <div class="main-content top-bar-space">
        @include('admin.partials.top-bar', ['pageTitle' => 'Milk Inventory'])

        <div class="content">
            <nav class="tabs" aria-label="Milk Inventory views" role="tablist">
                <a class="tab" id="tab-unpasteurized" href="{{ route('admin.milk-inventory') }}?view=unpasteurized">Unpasteurized <span class="count" id="count-tab-unpasteurized">{{ isset($unpasteurized) ? $unpasteurized->count() : 0 }}</span></a>
                <a class="tab" id="tab-pasteurized" href="{{ route('admin.milk-inventory') }}?view=pasteurized">Pasteurized <span class="count" id="count-tab-pasteurized">{{ isset($pasteurized) ? $pasteurized->count() : 0 }}</span></a>
                <a class="tab" id="tab-dispensed" href="{{ route('admin.milk-inventory') }}?view=dispensed">Dispensed <span class="count" id="count-tab-dispensed">{{ isset($dispensed) ? $dispensed->count() : 0 }}</span></a>
            </nav>

            <!-- Unpasteurized -->
            <div class="card" id="panel-unpasteurized" style="display:none;">
                <div class="pink-card" style="overflow-x:auto;">
                    <div class="pink-title" style="display:flex; align-items:center; justify-content:space-between; gap:12px;">
                        <span>Unpasteurized Breastmilk</span>
                        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                            <button class="btn-sm btn-blue" onclick="openModal('batchManagerModal')">Manage Batches</button>
                        </div>
                    </div>
                    <table class="data-table theme-pink">
                        <thead>
                            <tr>
                                <th>Donor Name</th>
                                <th>Number of Bags</th>
                                <th>Total Volume</th>
                                <th>Date</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody id="inventory-unpasteurized-tbody">
                            @forelse(($unpasteurized ?? []) as $x)
                                <tr>
                                    <td>{{ $x->Donor_Name ?? 'Unknown Donor' }}</td>
                                    <td>{{ $x->Number_of_Bags ?? '—' }}</td>
                                    <td>{{ $x->Total_Volume ?? '—' }}</td>
                                    <td>{{ $x->Date }}</td>
                                    <td>{{ $x->Time }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="empty">No unpasteurized stock.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pasteurized -->
            <div class="card" id="panel-pasteurized" style="display:none;">
                <div class="pink-card" style="overflow-x:auto;">
                    <div class="pink-title" style="display:flex; align-items:center; justify-content:space-between; gap:12px;">
                        <span>Pasteurized Breastmilk</span>
                        <div style="display:flex; align-items:center; gap:12px; font-size:13px; color:#333;">
                            <strong>Total Volume:</strong> <span id="pasteurized-total-vol">{{ isset($pasteurized) ? number_format($pasteurized->sum('Total_Volume')) : 0 }}</span> ml
                        </div>
                    </div>
                    <table class="data-table theme-pink">
                        <thead>
                            <tr>
                                <th>Batch Number</th>
                                <th>Total Volume</th>
                                <th>Date Pasteurized</th>
                                <th>Time Pasteurized</th>
                            </tr>
                        </thead>
                        <tbody id="inventory-pasteurized-tbody">
                            @forelse(($pasteurized ?? []) as $x)
                                <tr>
                                    <td>{{ $x->Batch_Number ?? '—' }}</td>
                                    <td>{{ $x->Total_Volume ?? '—' }}</td>
                                    <td>{{ $x->Date_Pasteurized }}</td>
                                    <td>{{ $x->Time_Pasteurized }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="empty">No pasteurized stock.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Dispensed -->
            <div class="card" id="panel-dispensed" style="display:none;">
                <div class="pink-card" style="overflow-x:auto;">
                    <div class="pink-title">Dispensed Breastmilk</div>
                    <table class="data-table theme-pink">
                        <thead>
                            <tr>
                                <th>Guardian Name</th>
                                <th>Volume</th>
                                <th>Recipient Name</th>
                                <th>Batch Number (Pasteurized)</th>
                                <th>Date</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody id="inventory-dispensed-tbody">
                            @forelse(($dispensed ?? []) as $x)
                                <tr>
                                    <td>{{ $x->Guardian_Name ?? 'N/A' }}</td>
                                    <td>{{ $x->Volume ?? '—' }}</td>
                                    <td>{{ $x->Recipient_Name ?? '—' }}</td>
                                    <td>{{ $x->Batch_Number ?? '—' }}</td>
                                    <td>{{ $x->Date }}</td>
                                    <td>{{ $x->Time }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="empty">No dispensed records.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Batch Manager Modal -->
    <div id="batchManagerModal" class="modal" style="display:none;">
        <div class="modal-content" style="max-width:980px; position:relative;">
            <div class="modal-header" style="display:flex; align-items:center; justify-content:space-between; gap:8px;">
                <h3 class="modal-title">Pasteurized Batch Manager</h3>
                <button class="close-btn" aria-label="Close" onclick="closeModal('batchManagerModal')" style="background:transparent; border:none; font-size:22px; line-height:1; color:#333; cursor:pointer;">&times;</button>
            </div>
            <div class="modal-body">
                <div style="display:grid; grid-template-columns: 1fr; gap:14px;">
                    <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                        <label style="font-size:12px; color:#666;">Select Batch:</label>
                        <select id="bm-batch-select" style="padding:6px 10px; border:1px solid #ddd; border-radius:6px; min-width:200px;"></select>
                        <span style="color:#666; font-size:12px;">or</span>
                        <input id="bm-batch-new" type="text" placeholder="New batch number (e.g., B-2025-09-20-001)" style="padding:6px 10px; border:1px solid #ddd; border-radius:6px; min-width:260px;">
                        <button class="btn-sm btn-green" onclick="bmAddSelectedToBatch()">Add Selected to Batch</button>
                    </div>

                    <div class="table-wrapper">
                        <div style="display:flex; align-items:center; justify-content:space-between; padding:8px 10px; border-bottom:1px solid #eee;">
                            <div style="font-weight:600;">Unpasteurized Stock</div>
                        </div>
                        <table class="modern-table">
                            <thead>
                                <tr>
                                    <th style="width:36px;"><input type="checkbox" id="bm-check-all" onclick="bmToggleAll(this)"></th>
                                    <th>Donor</th>
                                    <th>Bags</th>
                                    <th>Volume (ml)</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody id="bm-unpasteurized-tbody"><tr><td colspan="6" class="empty">Loading…</td></tr></tbody>
                        </table>
                    </div>

                    <div class="table-wrapper">
                        <div style="display:flex; align-items:center; justify-content:space-between; padding:8px 10px; border-bottom:1px solid #eee;">
                            <div style="font-weight:600;">Batch Items</div>
                            <div style="font-size:12px; color:#666;">Batch: <span id="bm-current-batch" style="font-weight:700;">—</span></div>
                        </div>
                        <table class="modern-table">
                            <thead>
                                <tr>
                                    <th>Donor</th>
                                    <th>Bags</th>
                                    <th>Volume (ml)</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="bm-batch-items-tbody"><tr><td colspan="6" class="empty">Select a batch to view its items.</td></tr></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function bmLoadBatches(){
            fetch('/admin/inventory/batches').then(r=>r.json()).then(d=>{
                const sel=document.getElementById('bm-batch-select'); if(!sel) return;
                sel.innerHTML='';
                const opt = document.createElement('option'); opt.value=''; opt.textContent='— Select existing batch —'; sel.appendChild(opt);
                if(d && d.success && Array.isArray(d.data)){
                    d.data.forEach(b=>{
                        const o=document.createElement('option'); o.value=b.batch_number; o.textContent=`${b.batch_number} • ${b.items_count} items • ${b.total_volume||0} ml`; sel.appendChild(o);
                    });
                }
                // When selection changes, refresh items automatically
                sel.onchange = function(){ bmRefreshBatchItems(); };
            }).catch(()=>{});
        }
        function bmLoadUnpasteurizedForModal(){
            fetch('/admin/reports/inventory/unpasteurized').then(r=>r.json()).then(d=>{
                const tbody=document.getElementById('bm-unpasteurized-tbody'); if(!tbody) return;
                tbody.innerHTML='';
                const rows=(d && d.success && Array.isArray(d.data))? d.data: [];
                if(!rows.length){ tbody.innerHTML='<tr><td colspan="6" class="empty">No unpasteurized stock.</td></tr>'; return; }
                rows.forEach(x=>{
                    const tr=document.createElement('tr');
                    tr.innerHTML = `<td><input type="checkbox" class="bm-chk" value="${x.Breastmilk_Donation_ID}"></td>
                                    <td>${x.Donor_Name||'N/A'}</td>
                                    <td>${x.Number_of_Bags||'—'}</td>
                                    <td>${x.Total_Volume||'—'}</td>
                                    <td>${x.Date||'—'}</td>
                                    <td>${x.Time||'—'}</td>`;
                    tbody.appendChild(tr);
                });
            });
        }
        function bmToggleAll(master){
            document.querySelectorAll('#bm-unpasteurized-tbody .bm-chk').forEach(cb=>{ cb.checked = master.checked; });
        }
        function bmCurrentBatch(){
            const sel=document.getElementById('bm-batch-select');
            const manual=document.getElementById('bm-batch-new');
            const val = (manual && manual.value.trim()) || (sel && sel.value) || '';
            return val;
        }
        // If user types a batch number, refresh the items list as they stop typing
        (function(){
            document.addEventListener('DOMContentLoaded', function(){
                const manual=document.getElementById('bm-batch-new');
                if(manual){
                    let t=null;
                    manual.addEventListener('input', function(){
                        clearTimeout(t);
                        t=setTimeout(bmRefreshBatchItems, 400);
                    });
                }
            });
        })();
        function bmRefreshBatchItems(){
            const batch = bmCurrentBatch();
            const label=document.getElementById('bm-current-batch'); if(label) label.textContent = batch || '—';
            const tbody=document.getElementById('bm-batch-items-tbody'); if(!tbody) return;
            if(!batch){ tbody.innerHTML='<tr><td colspan="6" class="empty">Enter or select a batch above.</td></tr>'; return; }
            fetch('/admin/inventory/batch/items?batch='+encodeURIComponent(batch)).then(r=>r.json()).then(d=>{
                tbody.innerHTML='';
                const rows=(d && d.success && Array.isArray(d.data))? d.data: [];
                if(!rows.length){ tbody.innerHTML='<tr><td colspan="6" class="empty">No items in this batch.</td></tr>'; return; }
                rows.forEach(x=>{
                    const tr=document.createElement('tr');
                    tr.innerHTML = `<td>${x.Donor_Name||'N/A'}</td>
                                    <td>${x.Number_of_Bags||'—'}</td>
                                    <td>${x.Total_Volume||'—'}</td>
                                    <td>${x.Date_Pasteurized||'—'}</td>
                                    <td>${x.Time_Pasteurized||'—'}</td>
                                    <td><button class="btn-sm" onclick="bmRemoveItem(${x.Pasteurized_ID})">Remove</button></td>`;
                    tbody.appendChild(tr);
                });
            });
        }
        function bmAddSelectedToBatch(){
            const batch = bmCurrentBatch();
            if(!batch){ if(window.Swal){ Swal.fire({icon:'warning', title:'Batch required', text:'Enter or select a batch first.'}); } return; }
            const ids = Array.from(document.querySelectorAll('#bm-unpasteurized-tbody .bm-chk:checked')).map(cb=>parseInt(cb.value,10)).filter(Boolean);
            if(!ids.length){ if(window.Swal){ Swal.fire({icon:'info', title:'No items selected'});} return; }
            const proceed = ()=>{
                fetch('/admin/inventory/batch/add-items', {
                    method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},
                    body: JSON.stringify({ batch_number: batch, unpasteurized_ids: ids })
                }).then(r=>r.json()).then(d=>{
                    if(d && d.success){ if(window.Swal){ Swal.fire({icon:'success', title:'Items added', timer:1200, showConfirmButton:false}); }
                        bmRefreshBatchItems(); loadPasteurized(); loadUnpasteurized();
                    } else { const msg=(d&&d.message)||'Failed to add items'; if(window.Swal){ Swal.fire({icon:'error', title:'Error', text:msg}); } }
                }).catch(()=>{ if(window.Swal){ Swal.fire({icon:'error', title:'Network error'});} });
            };
            if(window.Swal){ Swal.fire({title:'Add selected items?', icon:'question', showCancelButton:true, confirmButtonText:'Add to Batch'}).then(r=>{ if(r.isConfirmed) proceed(); }); } else { if(confirm('Add selected items to batch?')) proceed(); }
        }
        function bmRemoveItem(pasteurizedId){
            const go=()=>{ fetch('/admin/inventory/batch/items/'+pasteurizedId, { method:'DELETE', headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content} })
                .then(r=>r.json()).then(d=>{ if(d&&d.success){ bmRefreshBatchItems(); loadPasteurized(); } else { const msg=(d&&d.message)||'Failed to remove'; if(window.Swal){ Swal.fire({icon:'error', title:'Error', text:msg});} } }); };
            if(window.Swal){ Swal.fire({title:'Remove item from batch?', icon:'warning', showCancelButton:true, confirmButtonText:'Remove'}).then(r=>{ if(r.isConfirmed) go(); }); } else { if(confirm('Remove item from batch?')) go(); }
        }

        // Open modal hook: preload data
        (function(){
            const modal=document.getElementById('batchManagerModal'); if(!modal) return;
            modal.addEventListener('click', function(e){ if(e.target===modal) closeModal('batchManagerModal'); });
            const origOpen = window.openModal;
            window.openModal = function(id){ if(id==='batchManagerModal'){ bmLoadBatches(); bmLoadUnpasteurizedForModal(); setTimeout(bmRefreshBatchItems, 50); } return (origOpen? origOpen(id): (document.getElementById(id).style.display='flex')); };
        })();
    </script>
    <!-- Keep the original modals for future use -->
    <!-- Unpasteurized Milk Modal -->
    <div id="unpasteurized-breastmilk-modal" class="modal" style="display:none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Unpasteurized Breastmilk</h3>
                <button class="close-btn" onclick="closeModal('unpasteurized-breastmilk-modal')">&times;</button>
            </div>
            <div class="modal-body">
                <table class="modal-table">
                    <thead>
                        <tr>
                            <th>Donor Name</th>
                            <th>Number of bag</th>
                            <th>Total Volume</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="unpasteurized-breastmilk-tbody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pasteurized Milk Modal -->
    <div id="pasteurized-breastmilk-modal" class="modal" style="display:none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Pasteurized Breastmilk</h3>
                <button class="close-btn" onclick="closeModal('pasteurized-breastmilk-modal')">&times;</button>
            </div>
            <div class="modal-body">
                <table class="modal-table">
                    <thead>
                        <tr>
                            <th>Batch Number</th>
                            <th>Total volume</th>
                            <th>Date pasteurized</th>
                            <th>Time pasteurized</th>
                        </tr>
                    </thead>
                    <tbody id="pasteurized-breastmilk-tbody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Dispensed Milk Modal -->
    <div id="dispensed-breastmilk-modal" class="modal" style="display:none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Dispensed Breastmilk</h3>
                <button class="close-btn" onclick="closeModal('dispensed-breastmilk-modal')">&times;</button>
            </div>
            <div class="modal-body">
                <table class="modal-table">
                    <thead>
                        <tr>
                            <th>Guardian Name</th>
                            <th>Volume</th>
                            <th>Recipient Name</th>
                            <th>Batch number(Pasteurized)</th>
                            <th>Date</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody id="dispensed-breastmilk-tbody"></tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
