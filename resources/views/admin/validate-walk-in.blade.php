<!-- Walk-in Donation Validation Modal (Redesigned) -->
<div id="validate-walk-in-modal" class="modal" role="dialog" aria-modal="true" aria-labelledby="walk-in-validate-title" style="display:none;">
    <div class="modal-content walkin-validate-modal" style="max-width:760px; width:95%;">
        <div class="modal-header gradient-header" style="align-items:center;">
            <h3 class="modal-title" id="walk-in-validate-title" style="font-size:18px;">Validate Walk-in Donation</h3>
            <button class="close-btn" aria-label="Close" onclick="closeModal('validate-walk-in-modal')" type="button">&times;</button>
        </div>
        <div class="modal-body" style="padding:24px 28px 26px;">
            <form id="validate-walk-in-form" novalidate style="display:flex; flex-direction:column; gap:22px;">
                @csrf
                <input type="hidden" name="donation_id" id="walk-in-donation-id">

                <div class="walkin-grid" style="display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:18px;">
                    <div class="field-group" style="display:flex; flex-direction:column; gap:6px;">
                        <label for="walk-in-donor-name" style="font-size:12px; font-weight:700; letter-spacing:.5px; text-transform:uppercase; color:#555;">Donor</label>
                        <input type="text" id="walk-in-donor-name" readonly class="form-control" style="background:#f4f6f8; border:1px solid #e2e6ea; border-radius:8px; padding:10px 12px; font-weight:600;">
                    </div>
                    <div class="field-group" style="display:flex; flex-direction:column; gap:6px;">
                        <label for="walk-in-bags" style="font-size:12px; font-weight:700; letter-spacing:.5px; text-transform:uppercase; color:#555;">Number of Bags <span style="color:#dc3545;">*</span></label>
                        <input type="number" id="walk-in-bags" name="number_of_bags" required min="1" step="1" placeholder="e.g., 3" class="form-control" style="border:1px solid #e2e6ea; border-radius:8px; padding:10px 12px;">
                        <small class="help" style="font-size:11px; color:#777;">Positive whole number (1–50 recommended)</small>
                    </div>
                    <div class="field-group" style="display:flex; flex-direction:column; gap:6px;">
                        <label for="walk-in-volume" style="font-size:12px; font-weight:700; letter-spacing:.5px; text-transform:uppercase; color:#555;">Total Volume (ml) <span style="color:#dc3545;">*</span></label>
                        <div style="display:flex; align-items:stretch;">
                            <input type="number" id="walk-in-volume" name="total_volume_donated" required min="1" step="1" placeholder="e.g., 500" class="form-control" style="flex:1; border:1px solid #e2e6ea; border-radius:8px 0 0 8px; padding:10px 12px;">
                            <span style="background:#ffe3ef; border:1px solid #e2e6ea; border-left:none; padding:10px 12px; font-size:12px; font-weight:600; display:inline-flex; align-items:center; border-radius:0 8px 8px 0;">ml</span>
                        </div>
                        <small class="help" style="font-size:11px; color:#777;">Total expressed milk volume</small>
                    </div>
                    <div class="field-group" style="display:flex; flex-direction:column; gap:6px;">
                        <label for="walk-in-date" style="font-size:12px; font-weight:700; letter-spacing:.5px; text-transform:uppercase; color:#555;">Date</label>
                        <input type="date" id="walk-in-date" name="date" readonly class="form-control" style="background:#f4f6f8; border:1px solid #e2e6ea; border-radius:8px; padding:10px 12px;">
                        <small class="help" style="font-size:11px; color:#777;">User's selected date</small>
                    </div>
                    <div class="field-group" style="display:flex; flex-direction:column; gap:6px;">
                        <label for="walk-in-time" style="font-size:12px; font-weight:700; letter-spacing:.5px; text-transform:uppercase; color:#555;">Time</label>
                        <input type="time" id="walk-in-time" name="time" readonly class="form-control" style="background:#f4f6f8; border:1px solid #e2e6ea; border-radius:8px; padding:10px 12px;">
                        <small class="help" style="font-size:11px; color:#777;">User's selected time</small>
                    </div>
                </div>

                <div id="walk-in-error" style="display:none; background:#f8d7da; border:1px solid #f5c2c7; color:#842029; padding:10px 12px; border-radius:8px; font-size:13px; font-weight:500;"></div>

                <div class="modal-actions" style="display:flex; gap:10px; justify-content:flex-end; padding-top:4px;">
                    <button type="submit" class="btn btn-success" id="walk-in-submit-btn" style="padding:10px 18px; border-radius:8px; font-weight:600;">Validate Donation</button>
                    <button type="button" class="btn btn-secondary" style="padding:10px 18px; border-radius:8px; font-weight:600;" onclick="closeModal('validate-walk-in-modal')">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Normalize a date string to HTML date input format (YYYY-MM-DD)
    function normalizeDateForInput(dateStr) {
        if (!dateStr) return '';
        try {
            // If already YYYY-MM-DD
            if (/^\d{4}-\d{2}-\d{2}$/.test(dateStr)) return dateStr;
            // If YYYY-MM-DD HH:MM:SS or similar
            const match = dateStr.match(/^(\d{4}-\d{2}-\d{2})/);
            if (match) return match[1];
            // If ISO like 2025-09-23T00:00:00Z
            const dIso = new Date(dateStr);
            if (!isNaN(dIso.getTime())) {
                const y = dIso.getFullYear();
                const m = String(dIso.getMonth() + 1).padStart(2, '0');
                const d = String(dIso.getDate()).padStart(2, '0');
                return `${y}-${m}-${d}`;
            }
            // If mm/dd/yyyy or m/d/yyyy
            const us = dateStr.match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/);
            if (us) {
                const m = String(parseInt(us[1], 10)).padStart(2, '0');
                const d = String(parseInt(us[2], 10)).padStart(2, '0');
                const y = us[3];
                return `${y}-${m}-${d}`;
            }
        } catch (_) {}
        return '';
    }
    function openWalkInValidationModal(donationId, donorName, donationDate = null, donationTime = null) {
        console.log(`🔄 Opening walk-in validation modal:`, {
            donationId,
            donorName,
            donationDate,
            donationTime
        });

        document.getElementById('walk-in-donation-id').value = donationId;
        document.getElementById('walk-in-donor-name').value = donorName;
        
        // If date and time are provided, set them as read-only (normalize date)
        if (donationDate && donationTime && donationTime !== 'TBD') {
            const normalizedDate = normalizeDateForInput(donationDate);
            console.log(`✅ Using provided date and time: ${donationDate} -> ${normalizedDate}, ${donationTime}`);
            if (normalizedDate) document.getElementById('walk-in-date').value = normalizedDate;
            document.getElementById('walk-in-time').value = donationTime;
        } else {
            console.log(`🔄 Date/time not provided or invalid, fetching from API...`);
            // Fetch the walk-in request details to get the user's selected date and time
            fetchWalkInRequestDetails(donationId);
        }
        
        openModal('validate-walk-in-modal');
    }

    // Function to fetch walk-in request details
    function fetchWalkInRequestDetails(requestId) {
        console.log(`🔄 Fetching walk-in request details for ID: ${requestId}`);
        
        fetch(`/admin/walk-in-requests/${requestId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            console.log(`📥 Walk-in request response status: ${response.status}`);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('📦 Walk-in request data:', data);
            
            if (data.success && data.data) {
                const request = data.data;
                const dateField = document.getElementById('walk-in-date');
                const timeField = document.getElementById('walk-in-time');
                
                if (dateField && request.donation_date) {
                    const normalizedDate = normalizeDateForInput(request.donation_date);
                    if (normalizedDate) {
                        dateField.value = normalizedDate;
                        console.log(`✅ Set walk-in date to: ${normalizedDate} (original: ${request.donation_date})`);
                    } else {
                        console.warn('⚠️ Unable to normalize walk-in date:', request.donation_date);
                    }
                }
                
                if (timeField && request.donation_time) {
                    // Format time if needed (remove seconds if present)
                    let timeValue = request.donation_time;
                    
                    // Handle different time formats
                    if (timeValue.includes(':')) {
                        // If it's in HH:MM:SS format, take only HH:MM
                        timeValue = timeValue.substring(0, 5);
                    } else if (timeValue.length === 8) {
                        // If it's in HHMMSS format, convert to HH:MM
                        timeValue = timeValue.substring(0, 2) + ':' + timeValue.substring(2, 4);
                    }
                    
                    timeField.value = timeValue;
                    console.log(`✅ Set walk-in time to: ${timeValue} (original: ${request.donation_time})`);
                } else {
                    console.error('❌ Time field not found or no donation_time in request:', {
                        timeField: !!timeField,
                        donation_time: request.donation_time
                    });
                }
            } else {
                console.error('❌ No walk-in request data found:', data.message || 'No data');
            }
        })
        .catch(error => {
            console.error('❌ Error fetching walk-in request details:', error.message);
        });
    }

    document.getElementById('validate-walk-in-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const errBox = document.getElementById('walk-in-error');
        if (errBox) { errBox.style.display='none'; errBox.textContent=''; }

        const formData = new FormData(this);
        const donationId = formData.get('donation_id');
        const numberOfBags = formData.get('number_of_bags');
        const totalVolume = formData.get('total_volume_donated');

        // Basic front-end validation
        const bagsNum = parseInt(numberOfBags, 10);
        const volNum = parseInt(totalVolume, 10);
        if (!bagsNum || bagsNum < 1) {
            return showWalkInError('Please enter a valid number of bags (>=1).');
        }
        if (!volNum || volNum < 1) {
            return showWalkInError('Please enter a valid total volume in ml (>=1).');
        }
        if (bagsNum > 50) {
            return showWalkInError('Number of bags seems unusually high. Please verify (<=50).');
        }
        if (volNum > 20000) { // 20 liters sanity upper bound
            return showWalkInError('Total volume seems too large. Please verify (<=20000 ml).');
        }

        const submitButton = document.getElementById('walk-in-submit-btn');
        const originalText = submitButton.textContent;
        submitButton.textContent = 'Validating...';
        submitButton.disabled = true;

        fetch(`/admin/donations/${donationId}/validate-walk-in`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                donation_id: donationId,
                number_of_bags: bagsNum,
                total_volume_donated: volNum
            })
        })
        .then(r => { if(!r.ok) throw new Error(`HTTP ${r.status}`); return r.json(); })
        .then(data => {
            submitButton.textContent = originalText;
            submitButton.disabled = false;
            if (data && data.success) {
                closeModal('validate-walk-in-modal');
                if (window.Swal) {
                    Swal.fire({ icon:'success', title:'Validation Successful', text:'Walk-in donation validated successfully!', confirmButtonText:'OK' })
                        .then(()=>{ if (typeof loadReportData === 'function') loadReportData('pending-walk-in-requests'); });
                } else if (typeof showSuccessModal === 'function') {
                    showSuccessModal('Validation Successful','Walk-in donation validated successfully!');
                    if (typeof loadReportData === 'function') loadReportData('pending-walk-in-requests');
                } else {
                    alert('Walk-in donation validated successfully!');
                    if (typeof loadReportData === 'function') loadReportData('pending-walk-in-requests');
                }
            } else {
                showWalkInError((data && data.message) ? data.message : 'Failed to validate donation.');
            }
        })
        .catch(err => {
            submitButton.textContent = originalText;
            submitButton.disabled = false;
            showWalkInError('Error validating donation: '+ err.message);
        });
    });

    function showWalkInError(msg){
        const errBox = document.getElementById('walk-in-error');
        if(errBox){ errBox.textContent = msg; errBox.style.display='block'; }
    }

    // Close modal when clicking the dimmed backdrop
    (function(){
        const modalEl = document.getElementById('validate-walk-in-modal');
        if (modalEl && !modalEl.__backdropCloseBound) {
            modalEl.addEventListener('click', function(ev){ if (ev.target === modalEl) closeModal('validate-walk-in-modal'); });
            modalEl.__backdropCloseBound = true;
        }
    })();

    // Close on Escape key for accessibility
    document.addEventListener('keydown', function(ev){
        if (ev.key === 'Escape') {
            try { closeModal('validate-walk-in-modal'); } catch(_) {}
        }
    });
</script>
