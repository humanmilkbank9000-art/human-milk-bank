<!-- Milk Requests Modal -->
<div id="milkRequestsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Milk Requests Management</h3>
            <button class="close-btn" onclick="closeModal('milkRequestsModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div id="milk-requests-title" style="margin-bottom: 20px; font-size: 18px; font-weight: bold; color: #333;">
                Loading...
            </div>

            <div class="table-container" style="overflow-x: auto;">
                <table class="modal-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Recipient</th>
                            <th>Appointment</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="milk-requests-body">
                        <tr><td colspan="7" style="text-align: center; padding: 40px; color: #666;">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.tab {
    border: 1px solid #ff69b4;
    color: #ff69b4;
    background: #fff;
    padding: 10px 16px;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 500;
}

.tab:hover {
    background: #ffd1df;
}

.tab.active {
    background: #ff69b4;
    color: #fff;
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

.status-approved {
    background-color: #d4edda;
    color: #155724;
}

.status-declined {
    background-color: #f8d7da;
    color: #721c24;
}

.btn {
    padding: 6px 12px;
    border-radius: 6px;
    border: 1px solid transparent;
    cursor: pointer;
    font-size: 12px;
    font-weight: 500;
    transition: all 0.3s ease;
    margin-right: 5px;
}

.btn-view {
    background: #6c757d;
    color: #fff;
}

.btn-approve {
    background: #28a745;
    color: #fff;
}

.btn-decline {
    background: #dc3545;
    color: #fff;
}

.btn:hover {
    opacity: 0.8;
    transform: translateY(-1px);
}
</style>

<script>
let currentMilkStatus = '';

function openMilkRequestsModal(status) {
    currentMilkStatus = status;
    openModal('milkRequestsModal');
    loadMilkRequests(status);
}

function loadMilkRequests(status) {
    const qs = status ? ('?status=' + status) : '';
    const tbody = document.getElementById('milk-requests-body');
    const title = document.getElementById('milk-requests-title');
    
    // Update title based on status
    const titles = {
        '': 'All Breastmilk Requests',
        'pending': 'Pending Breastmilk Requests',
        'approved': 'Approved Breastmilk Requests',
        'declined': 'Declined Breastmilk Requests'
    };
    title.textContent = titles[status] || 'Breastmilk Requests';
    
    tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 40px; color: #666;">Loading...</td></tr>';
    
    fetch('/admin/milk-requests/list' + qs)
        .then(r => r.json())
        .then(data => {
            if (!data.success) { 
                tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 40px; color: #666;">Failed to load</td></tr>'; 
                return; 
            }
            if (!Array.isArray(data.data) || data.data.length === 0) { 
                tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 40px; color: #666;">No requests found</td></tr>'; 
                return; 
            }
                const STORAGE_BASE = @json(url('storage'));
                tbody.innerHTML = data.data.map(row => `
                <tr>
                    <td>${row.id}</td>
                    <td>${row.user_full_name}</td>
                    <td>${row.recipient_name}</td>
                    <td>${(row.scheduled_date||'—')} ${row.scheduled_time?('• '+new Date('2000-01-01T'+row.scheduled_time).toLocaleTimeString('en-US',{hour:'numeric',minute:'2-digit',hour12:true})):''}</td>
                    <td><span class="status-badge status-${row.status}">${row.status.toUpperCase()}</span></td>
                    <td>
                            ${(() => { const raw = row.prescription_image_path || ''; const norm = raw.replace(/^\\?\/?(storage|public)\//, ''); return raw ? `<a class="btn btn-view" target="_blank" href="${STORAGE_BASE}/${norm}">View Rx</a>` : '' })()}
                        ${row.status === 'pending' ? `
                            <button class="btn btn-approve" onclick="updateMilkStatus(${row.id}, 'approved')">Approve</button>
                            <button class="btn btn-decline" onclick="updateMilkStatus(${row.id}, 'declined')">Decline</button>
                        ` : ''}
                    </td>
                </tr>
            `).join('');
        })
        .catch(() => { 
            tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 40px; color: #666;">Error loading requests</td></tr>'; 
        });
}

function updateMilkStatus(id, status) {
    const notes = status === 'declined' ? (prompt('Add decline notes (optional):') || '') : '';
    fetch('/admin/milk-requests/' + id + '/status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: status, admin_notes: notes })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            loadMilkRequests(currentMilkStatus);
            // Refresh dashboard analytics (approved requests count) if available
            if (typeof window.refreshAnalyticsTotals === 'function') {
                try { window.refreshAnalyticsTotals(); } catch (e) { /* noop */ }
            }
            alert('Request updated successfully');
        } else {
            alert(data.message || 'Failed to update request');
        }
    })
    .catch(() => alert('Error updating request status'));
}
</script>
