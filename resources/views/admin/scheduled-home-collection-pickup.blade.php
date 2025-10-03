<!-- Scheduled Home Collection Pickup Modal -->
<div id="scheduled-home-collection-pickup-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Scheduled Home Collection Pickup</h3>
            <button class="close-btn" onclick="closeModal('scheduled-home-collection-pickup-modal')">&times;</button>
        </div>
        <div class="modal-body">
            <table class="modal-table">
                <thead>
                    <tr>
                        <th>Donor Full Name</th>
                        <th>Number of Bags</th>
                        <th>Total Volume (ml)</th>
                        <th>Pickup Date</th>
                        <th>Pickup Time</th>
                        <th>Pickup Address</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="scheduled-home-collection-pickup-tbody"></tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Function to populate scheduled home collection pickup modal
    function populateScheduledHomeCollectionPickupModal(requests) {
        console.log('🔄 Populating scheduled home collection pickup modal with', requests.length, 'requests');
        const tbody = document.getElementById('scheduled-home-collection-pickup-tbody');
        if (!tbody) return;

        tbody.innerHTML = '';
        if (requests.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7">No scheduled home collection pickups.</td></tr>';
            return;
        }

        requests.forEach(request => {
            const row = document.createElement('tr');
            const pickupDate = new Date(request.scheduled_date).toLocaleDateString();
            
            // Format pickup time
            let pickupTime = 'TBD';
            if (request.scheduled_time) {
                try {
                    const timeStr = request.scheduled_time.length === 5 ? request.scheduled_time + ':00' : request.scheduled_time;
                    const timeDate = new Date(`2000-01-01T${timeStr}`);
                    if (!isNaN(timeDate.getTime())) {
                        pickupTime = timeDate.toLocaleTimeString('en-US', {
                            hour: 'numeric',
                            minute: '2-digit',
                            hour12: true
                        });
                    }
                } catch (e) {
                    pickupTime = request.scheduled_time;
                }
            }

            const address = request.pickup_address || 'Address not provided';
            const escapedAddress = address.replace(/'/g, "\\'");

            row.innerHTML = `
                <td>${request.donor_name}</td>
                <td>
                    <input type="number" id="bags-${request.id}" value="${request.number_of_bags}" min="1" 
                           style="width: 70px; padding: 4px; border: 1px solid #ddd; border-radius: 4px; text-align: center;">
                </td>
                <td>
                    <input type="number" id="volume-${request.id}" value="${request.total_volume}" min="1" 
                           style="width: 90px; padding: 4px; border: 1px solid #ddd; border-radius: 4px; text-align: center;">
                    <small style="color: #666; font-size: 10px;">ml</small>
                </td>
                <td>${pickupDate}</td>
                <td>${pickupTime}</td>
                <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${address}">${address}</td>
                <td>
                    <button class="btn btn-success btn-sm" onclick="confirmPickupDirectly(${request.id}, '${request.donor_name}', '${request.scheduled_date}', '${request.scheduled_time}')">
                        ✅ Confirm Pickup
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    // Function to confirm pickup directly from the table
    function confirmPickupDirectly(collectionId, donorName, scheduledDate, scheduledTime) {
        console.log(`🔄 Confirming pickup directly for collection ID: ${collectionId}`);
        
        // Get the edited values from the input fields
        const numberOfBags = document.getElementById(`bags-${collectionId}`).value;
        const totalVolume = document.getElementById(`volume-${collectionId}`).value;
        
        // Validate inputs
        if (!numberOfBags || numberOfBags < 1) {
            alert('Please enter a valid number of bags (minimum 1)');
            return;
        }
        
        if (!totalVolume || totalVolume < 1) {
            alert('Please enter a valid total volume (minimum 1ml)');
            return;
        }
        
        // Show loading state
        const button = document.querySelector(`button[onclick*="confirmPickupDirectly(${collectionId}"]`);
        const originalText = button.textContent;
        button.textContent = 'Confirming...';
        button.disabled = true;
        
        // Submit the confirmation
        fetch(`/admin/home-collection/${collectionId}/validate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                number_of_bags: parseInt(numberOfBags),
                total_volume_donated: parseFloat(totalVolume)
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('📦 Pickup confirmation response:', data);
            
            if (data.success) {
                console.log('✅ Home collection pickup confirmed successfully');
                if (window.Swal) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Pickup Confirmed',
                        text: `Pickup for ${donorName} confirmed successfully! The donation has been moved to Pickup Donations.`,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        loadReportData('scheduled-home-collection-pickup');
                        loadReportData('pickup-donations');
                        if (typeof window.refreshAnalyticsTotals === 'function') {
                            window.refreshAnalyticsTotals();
                        }
                    });
                } else {
                    showSuccessModal('Pickup Confirmed', `Pickup for ${donorName} confirmed successfully! The donation has been moved to Pickup Donations.`);
                    loadReportData('scheduled-home-collection-pickup');
                    loadReportData('pickup-donations');
                    if (typeof window.refreshAnalyticsTotals === 'function') {
                        window.refreshAnalyticsTotals();
                    }
                }
            } else {
                console.error('❌ Pickup confirmation failed:', data.message);
                alert('Error confirming pickup: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('❌ Error confirming home collection pickup:', error);
            alert('Error confirming pickup: ' + error.message);
        })
        .finally(() => {
            // Reset button state
            button.textContent = originalText;
            button.disabled = false;
        });
    }
</script>

<style>
    .badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        color: white;
    }
    
    .badge-info {
        background-color: #17a2b8;
    }
    
    .badge-success {
        background-color: #28a745;
    }
</style>