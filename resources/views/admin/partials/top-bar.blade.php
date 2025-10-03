@php
    $title = isset($pageTitle) ? $pageTitle : (isset($title) ? $title : 'Dashboard');
    $titleId = isset($pageTitleId) ? $pageTitleId : (isset($titleId) ? $titleId : null);
    $rightHtml = isset($rightHtml) ? $rightHtml : (isset($actionsHtml) ? $actionsHtml : null);
    $subtitle = isset($subtitle) ? $subtitle : 'Cagayan de Oro City - Human Milk Bank & Lactation Support Center';
@endphp
<div class="top-bar admin-top-bar">
    <div class="top-bar-left">
        <button id="admin-hamburger" class="hamburger-btn" onclick="toggleSidebar()" aria-label="Open sidebar" aria-controls="sidebar" aria-expanded="false"><i class="fas fa-bars"></i></button>
        <div class="top-bar-logos">
            <img src="{{ asset('hospital logo.png') }}" alt="Hospital Logo" loading="lazy" />
            <img src="{{ asset('logo.png') }}" alt="HMBLSC Logo" loading="lazy" />
        </div>
        <div class="top-bar-title-wrap">
            @if($titleId)
                <h1 id="{{ $titleId }}" class="page-title">{{ $title }}</h1>
            @else
                <h1 class="page-title">{{ $title }}</h1>
            @endif
            <div class="top-bar-subtitle">{{ $subtitle }}</div>
        </div>
    </div>
    <div class="top-bar-right">
        @if(!empty($rightHtml))
            <div class="top-actions">{!! $rightHtml !!}</div>
        @endif
        <button id="adminNotificationsBtn" class="notifications-btn" type="button" aria-label="View notifications" title="Notifications">
            <i class="fas fa-bell"></i>
            <span id="adminNotificationsBadge" class="notifications-badge" aria-hidden="true" style="display:none;">0</span>
        </button>
        <button class="logout-btn" onclick="adminLogout()">Logout</button>
    </div>
</div>
<!-- SweetAlert2 (available on all admin pages) -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
/* Global SweetAlert2 button theme for admin: purple confirm, gray cancel */
.swal2-container { z-index: 100000 !important; }
.swal2-actions { gap: 8px !important; }
.swal2-styled { box-shadow: none !important; border: none !important; }
.swal2-styled:focus { outline: none !important; box-shadow: 0 0 0 3px rgba(124, 77, 255, 0.28) !important; }
.swal2-styled.swal2-confirm {
    background-color: #7C4DFF !important; /* primary purple */
    color: #ffffff !important;
    border-radius: 10px !important;
    padding: 10px 18px !important;
    font-weight: 600 !important;
}
.swal2-styled.swal2-confirm:hover { background-color: #6A3EF3 !important; }

.swal2-styled.swal2-cancel {
    background-color: #E5E7EB !important; /* neutral gray */
    color: #111827 !important;
    border-radius: 10px !important;
    padding: 10px 18px !important;
    font-weight: 600 !important;
}
.swal2-styled.swal2-cancel:hover { background-color: #D1D5DB !important; }
/* Notifications button */
.notifications-btn {
    position: relative;
    background: transparent;
    border: none;
    color: #4B5563; /* gray-600 */
    cursor: pointer;
    font-size: 20px;
    padding: 8px 10px;
    margin-right: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    transition: background-color .15s, color .15s;
}
.notifications-btn:hover, .notifications-btn:focus {
    background: #F3F4F6; /* gray-100 */
    color: #111827; /* gray-900 */
    outline: none;
}
.notifications-btn:focus { box-shadow: 0 0 0 3px rgba(124,77,255,0.35); }
.notifications-badge {
    position: absolute;
    top: 4px;
    right: 4px;
    min-width: 18px;
    height: 18px;
    background: #EF4444; /* red-500 */
    color: #ffffff;
    font-size: 11px;
    font-weight: 600;
    line-height: 18px;
    text-align: center;
    border-radius: 999px;
    padding: 0 4px;
    box-shadow: 0 0 0 2px #ffffff;
    <script>
        // Notifications badge logic
        function updateAdminNotifications(){
            var badge = document.getElementById('adminNotificationsBadge');
            if (!badge) return;
            fetch('/admin/notifications', { headers: { 'Accept':'application/json' } })
                .then(function(r){ return r.json(); })
                .then(function(json){
                    if (!json || !json.success) throw new Error('Bad response');
                    var data = json.data || [];
                    var unread = Array.isArray(data) ? data.length : 0;
                    if (unread > 0) {
                        badge.textContent = unread > 99 ? '99+' : unread;
                        badge.style.display = 'inline-block';
                    } else {
                        badge.style.display = 'none';
                    }
                })
                .catch(function(){ /* silent fail */ });
        }
        // Initial fetch after a slight delay to ensure session ready
        window.addEventListener('load', function(){ setTimeout(updateAdminNotifications, 300); });
        // Poll every 30 seconds
        setInterval(updateAdminNotifications, 30000);

        // Optional click behavior placeholder
        var btn = document.getElementById('adminNotificationsBtn');
        if (btn) {
            btn.addEventListener('click', function(){
                // Future: open dropdown or navigate to notifications page
                updateAdminNotifications();
            });
        }
    </script>
    pointer-events: none;
}
</style>
<script>
// Global, defensive sidebar toggle for admin
(function(){
    if (window.__adminSidebarToggleInit) return; // avoid double-bind
    window.__adminSidebarToggleInit = true;

    function isMobile(){ return window.matchMedia('(max-width: 768px)').matches; }

    function ensureOverlay(){
        var overlay = document.getElementById('admin-sidebar-overlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.id = 'admin-sidebar-overlay';
            overlay.className = 'sidebar-overlay';
            document.body.appendChild(overlay);
            overlay.addEventListener('click', function(){ closeMobileSidebar(); });
        }
        return overlay;
    }

    function openMobileSidebar(){
        var sidebar = document.getElementById('sidebar') || document.querySelector('.sidebar');
        if (!sidebar) return;
        sidebar.classList.add('open');
        var overlay = ensureOverlay();
        overlay.classList.add('active');
        document.body.classList.add('no-scroll');
        try {
            var btn = document.getElementById('admin-hamburger');
            if (btn) {
                var icon = btn.querySelector('i');
                if (icon) { icon.classList.remove('fa-bars','fa-times'); icon.classList.add('fa-times'); }
                btn.setAttribute('aria-expanded', 'true');
                btn.setAttribute('aria-label', 'Close sidebar');
            }
        } catch(e){}
    }
    function closeMobileSidebar(){
        var sidebar = document.getElementById('sidebar') || document.querySelector('.sidebar');
        var overlay = document.getElementById('admin-sidebar-overlay');
        if (sidebar) sidebar.classList.remove('open');
        if (overlay) overlay.classList.remove('active');
        document.body.classList.remove('no-scroll');
        try {
            var btn = document.getElementById('admin-hamburger');
            if (btn) {
                var icon = btn.querySelector('i');
                if (icon) { icon.classList.remove('fa-bars','fa-times'); icon.classList.add('fa-bars'); }
                btn.setAttribute('aria-expanded', 'false');
                btn.setAttribute('aria-label', 'Open sidebar');
            }
        } catch(e){}
    }

    // Expose a single global function used by the hamburger
    window.toggleSidebar = function(){
        var sidebar = document.getElementById('sidebar') || document.querySelector('.sidebar');
        var main = document.querySelector('.main-content');
        if (!sidebar) return;
        if (isMobile()) {
            // Mobile behavior: slide-in/out + overlay
            if (sidebar.classList.contains('open')) {
                closeMobileSidebar();
            } else {
                openMobileSidebar();
            }
        } else {
            // Desktop behavior: collapse/expand layout
            sidebar.classList.toggle('collapsed');
            if (main) main.classList.toggle('expanded');
        }
    };

    // Close on Escape in mobile
    document.addEventListener('keydown', function(e){
        if (e.key === 'Escape' && isMobile()) closeMobileSidebar();
    });

    // Auto-close mobile sidebar when a nav item is tapped
    document.addEventListener('click', function(e){
        var link = e.target && e.target.closest ? e.target.closest('.nav-menu a') : null;
        if (!link) return;
        if (isMobile()) {
            closeMobileSidebar();
        }
    });

    // Cleanup when resizing from mobile -> desktop
    var resizeTimer = null;
    window.addEventListener('resize', function(){
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function(){
            var sidebar = document.getElementById('sidebar') || document.querySelector('.sidebar');
            if (!sidebar) return;
            if (!isMobile()) {
                // Ensure mobile classes are cleared
                closeMobileSidebar();
            }
        }, 150);
    });
})();

// Measure and set CSS var for top-bar height to keep content spacing correct
(function(){
    function setTopBarHeightVar(){
        try {
            var tb = document.querySelector('.admin-top-bar');
            if (!tb) return;
            var h = tb.getBoundingClientRect().height;
            // Add safe-area inset top to account for notches when applicable
            var safeTop = 0;
            try {
                // Read computed safe-area via env if supported (fallback 0)
                var tmp = getComputedStyle(document.documentElement).getPropertyValue('--safe-area-top');
                safeTop = parseInt(tmp) || 0;
            } catch(e) {}
            document.documentElement.style.setProperty('--admin-topbar-height', (h + safeTop) + 'px');
        } catch(e){}
    }
    window.addEventListener('load', setTopBarHeightVar);
    window.addEventListener('resize', function(){
        clearTimeout(window.__tbH);
        window.__tbH = setTimeout(setTopBarHeightVar, 100);
    });
    // Recompute after fonts/icons load
    document.addEventListener('readystatechange', function(){ if (document.readyState==='complete') setTopBarHeightVar(); });
})();

// Provide a global adminLogout() with SweetAlert confirmation (works across pages)
window.adminLogout = function() {
    if (typeof Swal === 'undefined') { return window.confirm('Logout?') && (function(){
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.logout") }}';
        form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}">';
        document.body.appendChild(form); form.submit(); })(); }

    Swal.fire({
        title: 'Confirm Logout',
        text: 'You will be signed out of the admin dashboard.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Logout',
        cancelButtonText: 'Cancel'
    }).then(function(result){
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.logout") }}';
            form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}">';
            document.body.appendChild(form);
            form.submit();
        }
    });
}
// Backward compatibility: expose as window.logout too (pages may call it)
window.logout = window.adminLogout;

// SweetAlert2 global helpers
(function(){
    if (typeof window.Swal === 'undefined') return; // will load from CDN
    // Toast mixin
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
    window.saToast = function(message, type){
        try { Toast.fire({ icon: type || 'info', title: message }); } catch(e) { console.log(message); }
    };
    window.saSuccess = function(message, title){
        Swal.fire({ icon: 'success', title: title || 'Success', text: message });
    };
    window.saError = function(message, title){
        Swal.fire({ icon: 'error', title: title || 'Error', text: message });
    };
    window.saConfirm = function(opts){
        const o = opts || {};
        return Swal.fire({
            title: o.title || 'Are you sure?',
            text: o.text || '',
            icon: o.icon || 'warning',
            showCancelButton: true,
            confirmButtonText: o.confirmButtonText || 'Yes',
            cancelButtonText: o.cancelButtonText || 'Cancel'
        });
    };
})();

// Responsive tables: add data-labels from thead to each td for mobile stacked view
(function(){
    if (window.__adminTableStackInit) return; window.__adminTableStackInit = true;
    function makeTablesStackable(root){
        try {
            var scope = root && root.querySelectorAll ? root : document;
            scope.querySelectorAll('table').forEach(function(table){
                var thead = table.querySelector('thead');
                if (!thead) return;
                var headers = Array.from(thead.querySelectorAll('th')).map(function(th){ return (th.textContent||'').trim(); });
                if (!headers.length) return;
                table.querySelectorAll('tbody tr').forEach(function(tr){
                    Array.from(tr.children).forEach(function(td, idx){
                        if (td && td.nodeName === 'TD' && !td.hasAttribute('data-label')) {
                            td.setAttribute('data-label', headers[idx] || '');
                        }
                    });
                });
            });
        } catch(e){}
    }
    // Expose for pages that render tables dynamically
    window.makeTablesStackable = makeTablesStackable;
    document.addEventListener('DOMContentLoaded', function(){ makeTablesStackable(document); });
    // Observe DOM for newly added tables (e.g., reports fetch results)
    try {
        var mo = new MutationObserver(function(mutations){
            mutations.forEach(function(m){
                m.addedNodes && m.addedNodes.forEach(function(n){
                    if (n && n.nodeType === 1){
                        if (n.matches && n.matches('table')) { makeTablesStackable(n.parentNode || n); }
                        else if (n.querySelectorAll) { var ts = n.querySelectorAll('table'); ts.forEach(function(t){ makeTablesStackable(t.parentNode || t); }); }
                    }
                });
            });
        });
        mo.observe(document.body, { subtree:true, childList:true });
    } catch(e){}
})();

// Ensure no stale overlays remain on initial load (e.g., after actions or reloads)
document.addEventListener('DOMContentLoaded', function(){
    try {
        // Sidebar overlay cleanup
        var overlay = document.getElementById('admin-sidebar-overlay');
        if (overlay) overlay.classList.remove('active');
        document.body.classList.remove('no-scroll');
        var sidebar = document.getElementById('sidebar') || document.querySelector('.sidebar');
        if (sidebar) sidebar.classList.remove('open');

        // Hide any generic modals that might have been left visible accidentally
        document.querySelectorAll('.modal').forEach(function(m){
            if (m && getComputedStyle(m).display !== 'none') m.style.display = 'none';
        });
    } catch(e) { /* noop */ }
});
</script>