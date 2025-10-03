<!-- Pending Home Collection Requests Modal -->
<div id="pending-home-collection-requests-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Pending Home Collection Requests</h3>
            <button class="close-btn" onclick="closeModal('pending-home-collection-requests-modal')">&times;</button>
        </div>
        <div class="modal-body">
            <table class="modal-table">
                <thead>
                    <tr>
                        <th>Donor Full Name</th>
                        <th>Number of Bags</th>
                        <th>Total Volume (ml)</th>
                        <th>Pickup Address</th>
                        <th>Assign</th>
                    </tr>
                </thead>
                <tbody id="pending-home-collection-requests-tbody"></tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Function to populate home collection requests modal
    function populatePendingHomeCollectionRequestsModal(requests) {
        const tbody = document.getElementById('pending-home-collection-requests-tbody');
        if (!tbody) return;

        tbody.innerHTML = '';
        if (requests.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5">No pending home collection requests.</td></tr>';
            return;
        }

        requests.forEach(request => {
            const row = document.createElement('tr');
            const address = request.pickup_address || 'Address not provided';

            // Get today's date in YYYY-MM-DD format for min date
            const today = new Date().toISOString().split('T')[0];

            row.innerHTML = `
                <td>${request.donor_name}</td>
                <td><span class="badge badge-info">${request.number_of_bags} bags</span></td>
                <td><span class="badge badge-success">${request.total_volume}ml</span></td>
                <td style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${address}">${address}</td>
                <td>
                    <button class="btn btn-success btn-sm" onclick="openAssignPickupModal(${request.id}, '${request.donor_name.replace(/'/g, "\\'")}")" style="font-size: 11px; padding: 4px 8px;">Assign</button>
                </td>`;
            tbody.appendChild(row);
        });
    }

    // Assignment handled via openAssignPickupModal in donations index script.

</script>

<style>
    .badge {
        display: inline-block;
        padding: 4px 8px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 4px;
        text-align: center;
        white-space: nowrap;
    }

    .badge-info {
        background-color: #d1ecf1;
        color: #0c5460;
        border: 1px solid #bee5eb;
    }

    .badge-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .modal-table td {
        vertical-align: middle;
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 12px;
        border-radius: 4px;
    }
</style>