<!-- Pending Walk-in Requests Modal -->
<div id="pending-walk-in-requests-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Pending Donation Requests</h3>
            <button class="close-btn" onclick="closeModal('pending-walk-in-requests-modal')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="filter-bar" style="display:flex; gap:10px; align-items:center; margin-bottom:10px; flex-wrap:wrap;">
                <label style="font-size:12px; color:#555;">From</label>
                <input type="date" id="walkin-filter-from" class="form-control" style="font-size:12px; padding:6px 8px;">
                <label style="font-size:12px; color:#555;">To</label>
                <input type="date" id="walkin-filter-to" class="form-control" style="font-size:12px; padding:6px 8px;">
                <button type="button" id="walkin-filter-apply" class="btn btn-primary btn-sm" style="padding:6px 10px; font-size:12px;">Apply</button>
                <button type="button" id="walkin-filter-clear" class="btn btn-secondary btn-sm" style="padding:6px 10px; font-size:12px;">Clear</button>
            </div>
            <table class="modal-table">
                <thead>
                    <tr>
                        <th>Donor Full Name</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Details</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="pending-walk-in-requests-tbody"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Walk-in Donation Confirmation Modal -->
<div id="confirm-walk-in-modal" class="modal">
    <div class="modal-content walk-in-confirm-modal">
        <div class="modal-header walk-in-header">
            <div class="header-icon">
                <i class="icon fas fa-hospital" aria-hidden="true"></i>
            </div>
            <div class="header-text">
                <h3 class="modal-title">Confirm Walk-in Donation</h3>
                <p class="modal-subtitle">Record the actual donation details</p>
            </div>
            <button class="close-btn" onclick="closeModal('confirm-walk-in-modal')">&times;</button>
        </div>

        <div class="modal-body walk-in-body">
            <!-- Donor Information Card -->
            <div class="donor-info-card">
                <h4 class="card-title">
                    <i class="icon fas fa-user" aria-hidden="true"></i>
                    Donor Information
                </h4>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Donor Full Name</label>
                        <div class="info-value" id="confirm-donor-name"></div>
                    </div>
                    <div class="info-item">
                        <label>Scheduled Date</label>
                        <div class="info-value" id="confirm-date"></div>
                    </div>
                    <div class="info-item">
                        <label>Scheduled Time</label>
                        <div class="info-value" id="confirm-time"></div>
                    </div>
                </div>
            </div>

            <!-- Donation Details Form -->
            <div class="donation-form-card">
                <h4 class="card-title">
                    <i class="icon fas fa-chart-bar" aria-hidden="true"></i>
                    Donation Details
                </h4>
                <form id="confirm-walk-in-form">
                    @csrf
                    <input type="hidden" name="request_id" id="walk-in-request-id">

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="icon fas fa-wine-bottle" aria-hidden="true"></i>
                                Number of Bags *
                            </label>
                            <input type="number" name="number_of_bags" class="form-control enhanced-input"
                                   min="1" max="20" required placeholder="Enter number of bags">
                            <small class="form-help">Typical range: 1-10 bags</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="icon fas fa-ruler" aria-hidden="true"></i>
                                Total Volume Donated (ml) *
                            </label>
                            <input type="number" name="total_volume_donated" class="form-control enhanced-input"
                                   min="1" max="5000" step="0.01" required placeholder="Enter volume in ml">
                            <small class="form-help">Typical range: 50-1000 ml per bag</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="icon fas fa-pen-to-square" aria-hidden="true"></i>
                            Additional Notes (Optional)
                        </label>
                        <textarea name="admin_notes" class="form-control enhanced-textarea"
                                  rows="3" placeholder="Any additional observations or notes..."></textarea>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal-footer walk-in-footer">
            <div class="footer-actions">
                <button type="button" class="btn btn-cancel" onclick="closeModal('confirm-walk-in-modal')">
                    <i class="icon fas fa-xmark" aria-hidden="true"></i>
                    Cancel
                </button>
                <button type="submit" form="confirm-walk-in-form" class="btn btn-confirm">
                    <i class="icon fas fa-check-circle" aria-hidden="true"></i>
                    Confirm Donation
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function openWalkInConfirmationModal(requestId, donorName, date, time) {
        document.getElementById('walk-in-request-id').value = requestId;
        document.getElementById('confirm-donor-name').textContent = donorName;
        document.getElementById('confirm-date').textContent = date;
        document.getElementById('confirm-time').textContent = time;

        // Reset form
        document.getElementById('confirm-walk-in-form').reset();
        document.getElementById('walk-in-request-id').value = requestId;

        // Focus on first input
        setTimeout(() => {
            const firstInput = document.querySelector('#confirm-walk-in-form input[name="number_of_bags"]');
            if (firstInput) firstInput.focus();
        }, 300);

        openModal('confirm-walk-in-modal');
    }

    document.getElementById('confirm-walk-in-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const submitBtn = document.querySelector('.btn-confirm');
        const formData = new FormData(this);
        const requestId = formData.get('request_id');

        // Validation
        const numberOfBags = parseInt(formData.get('number_of_bags'));
        const totalVolume = parseFloat(formData.get('total_volume_donated'));

        if (numberOfBags < 1 || numberOfBags > 20) {
            if (window.saToast) saToast('Please enter a valid number of bags (1-20)', 'error'); else alert('Please enter a valid number of bags (1-20)');
            return;
        }

        if (totalVolume < 1 || totalVolume > 5000) {
            if (window.saToast) saToast('Please enter a valid volume (1-5000 ml)', 'error'); else alert('Please enter a valid volume (1-5000 ml)');
            return;
        }

        // Show loading state
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;

        fetch(`/admin/walk-in-requests/${requestId}/confirm`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                closeModal('confirm-walk-in-modal');
                const proceedRefresh = () => {
                    if (typeof loadReportData === 'function') {
                        loadReportData('pending-walk-in-requests');
                        loadReportData('walk-in-donations');
                    }
                    if (typeof window.refreshAnalyticsTotals === 'function') {
                        window.refreshAnalyticsTotals();
                    }
                    document.getElementById('confirm-walk-in-form').reset();
                };
                if (window.Swal) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Validation Successful',
                        text: 'Walk-in donation confirmed successfully!',
                        confirmButtonText: 'OK'
                    }).then(proceedRefresh);
                } else {
                    if (window.saToast) saToast('Walk-in donation confirmed successfully!', 'success'); else alert('Walk-in donation confirmed successfully!');
                    proceedRefresh();
                }
            } else {
                if (window.saError) saError('Failed to confirm donation: ' + (data.message || 'Unknown error')); else alert('Failed to confirm donation: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (window.saError) saError('An error occurred while confirming the donation.'); else alert('An error occurred while confirming the donation.');
        })
        .finally(() => {
            // Remove loading state
            submitBtn.classList.remove('loading');
            submitBtn.disabled = false;
        });
    });

    // Removed custom notification UI in favor of SweetAlert2 helpers
</script>

<style>
    /* Walk-in Confirmation Modal Styles */
    .walk-in-confirm-modal {
        max-width: 700px;
        width: 90vw;
        max-height: 90vh;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }

    .walk-in-header {
        background: linear-gradient(135deg, #ff69b4, #d63384);
        color: white;
        padding: 20px 25px;
        display: flex;
        align-items: center;
        gap: 15px;
        border-bottom: none;
    }

    .header-icon {
        font-size: 24px;
        background: rgba(255, 255, 255, 0.2);
        padding: 10px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 50px;
        height: 50px;
    }

    .header-text {
        flex: 1;
    }

    .walk-in-header .modal-title {
        margin: 0;
        font-size: 22px;
        font-weight: 600;
        color: white;
    }

    .modal-subtitle {
        margin: 5px 0 0 0;
        font-size: 14px;
        opacity: 0.9;
        color: white;
    }

    .walk-in-header .close-btn {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border: none;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        font-size: 20px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .walk-in-header .close-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: scale(1.1);
    }

    .walk-in-body {
        padding: 25px;
        background: #f8f9fa;
        max-height: 60vh;
        overflow-y: auto;
    }

    .donor-info-card, .donation-form-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        border: 1px solid #e9ecef;
    }

    .card-title {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0 0 15px 0;
        font-size: 18px;
        font-weight: 600;
        color: #333;
        padding-bottom: 10px;
        border-bottom: 2px solid #f0f0f0;
    }

    .card-title .icon {
        font-size: 20px;
        background: linear-gradient(135deg, #ff69b4, #d63384);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
    }

    .info-item {
        background: #f8f9fa;
        padding: 12px;
        border-radius: 8px;
        border-left: 4px solid #ff69b4;
    }

    .info-item label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 5px;
    }

    .info-value {
        font-size: 16px;
        font-weight: 500;
        color: #333;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .form-label .icon {
        font-size: 16px;
        color: #ff69b4;
    }

    .enhanced-input, .enhanced-textarea {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: white;
    }

    .enhanced-input:focus, .enhanced-textarea:focus {
        outline: none;
        border-color: #ff69b4;
        box-shadow: 0 0 0 3px rgba(255, 105, 180, 0.1);
        transform: translateY(-1px);
    }

    .enhanced-input:valid {
        border-color: #28a745;
    }

    .form-help {
        display: block;
        font-size: 12px;
        color: #666;
        margin-top: 5px;
        font-style: italic;
    }

    .walk-in-footer {
        background: white;
        padding: 20px 25px;
        border-top: 1px solid #e9ecef;
    }

    .footer-actions {
        display: flex;
        justify-content: flex-end;
        gap: 15px;
    }

    .btn-cancel, .btn-confirm {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-cancel {
        background: #6c757d;
        color: white;
    }

    .btn-cancel:hover {
        background: #5a6268;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
    }

    .btn-confirm {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
    }

    .btn-confirm:hover {
        background: linear-gradient(135deg, #218838, #1e7e34);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
    }

    .btn-confirm:active, .btn-cancel:active {
        transform: translateY(0);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .walk-in-confirm-modal {
            width: 95vw;
            margin: 10px;
        }

        .form-row {
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .footer-actions {
            flex-direction: column;
        }

        .btn-cancel, .btn-confirm {
            width: 100%;
            justify-content: center;
        }
    }

    /* Loading State */
    .btn-confirm.loading {
        opacity: 0.7;
        cursor: not-allowed;
    }

    .btn-confirm.loading::after {
        content: '';
        width: 16px;
        height: 16px;
        border: 2px solid transparent;
        border-top: 2px solid white;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-left: 8px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
