<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Health Screening Details - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body{margin:0; font-family: Arial, Helvetica, sans-serif; background:#f8f9fa; display:flex; min-height:100vh;}
        .main-content{flex:1; margin-left:280px; min-height:100vh; background:#f8f9fa; transition: margin-left .3s ease;}
        .main-content.expanded{ margin-left:0; }
        .container{padding:22px;}
        .card{background:#fff; border:1px solid #e9ecef; border-radius:10px; overflow:hidden; margin-bottom:16px;}
        .card-header{padding:12px 16px; border-bottom:1px solid #e9ecef; font-weight:600; color:#333;}
        .card-body{padding:16px;}
        .grid{display:grid; grid-template-columns: repeat(auto-fit, minmax(220px,1fr)); gap:12px;}
        .item{background:#f8f9fa; border:1px solid #eceff2; border-radius:8px; padding:12px;}
        .label{font-size:12px; color:#666;}
        .value{font-size:15px; color:#222; font-weight:600;}
        .section-title{font-size:14px; font-weight:700; color:#333; margin:16px 0 8px;}
        .actions{display:flex; gap:8px; flex-wrap:wrap;}
        .btn{padding:8px 12px; border-radius:6px; border:1px solid #e9ecef; background:#fff; cursor:pointer; text-decoration:none; color:#333}
        .btn.primary{border-color:#ff69b4; color:#ff69b4;}
        .btn.success{border-color:#28a745; color:#28a745;}
        .btn.danger{border-color:#dc3545; color:#dc3545;}
        textarea{width:100%; min-height:80px; padding:10px; border:1px solid #e1e5ea; border-radius:6px;}
        .status{display:inline-block; padding:2px 8px; border-radius:999px; font-size:12px; font-weight:700; text-transform:capitalize;}
        .status.pending{background:#fff3cd; color:#856404;}
        .status.accepted{background:#d4edda; color:#155724;}
        .status.declined{background:#f8d7da; color:#721c24;}
    </style>
</head>
<body>
    @include('admin.partials.sidebar')
    <div class="main-content top-bar-space">
        @include('admin.partials.top-bar', ['pageTitle' => 'Health Screening Details'])
        <div class="container">
            @if(session('success'))
                <div class="card"><div class="card-body" style="color:#155724; background:#d4edda; border:1px solid #c3e6cb; border-radius:10px">{{ session('success') }}</div></div>
            @endif
            @if(session('error'))
                <div class="card"><div class="card-body" style="color:#721c24; background:#f8d7da; border:1px solid #f5c6cb; border-radius:10px">{{ session('error') }}</div></div>
            @endif

            <div class="card">
                <div class="card-header">Overview</div>
                <div class="card-body grid">
                    <div class="item"><div class="label">Donor</div><div class="value">{{ $screening->user->Full_Name ?? 'N/A' }}</div></div>
                    <div class="item"><div class="label">Contact</div><div class="value">{{ $screening->user->Contact_Number ?? 'N/A' }}</div></div>
                    <div class="item"><div class="label">Status</div><div class="value"><span class="status {{ $screening->status }}">{{ $screening->status }}</span></div></div>
                    <div class="item"><div class="label">Submitted</div><div class="value">{{ optional($screening->created_at)->format('M d, Y h:i A') }}</div></div>
                    <div class="item"><div class="label">Updated</div><div class="value">{{ optional($screening->updated_at)->format('M d, Y h:i A') }}</div></div>
                </div>
            </div>

            <div class="card">
                <div class="card-header" style="display:none;"></div>
                <div class="card-body">
                    </div>
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
