<!-- Availability Settings Modal -->
<div id="availability-settings-modal" class="modal">
    <div class="modal-content" style="max-width: 720px;">
        <div class="modal-header" style="padding: 14px 18px;">
            <h3 class="modal-title" style="font-size: 18px;">Set Daily Availability & Time Slots</h3>
            <button class="close-btn" onclick="closeModal('availability-settings-modal')">&times;</button>
        </div>
        <div class="modal-body" style="padding: 18px;">
            <div class="availability-info" style="margin-bottom: 10px;">
                <p style="margin:0 0 4px; font-size: 13px;"><strong>Selected Date:</strong> <span id="selected-date-display"></span></p>
                <p style="color: #666; font-size: 12px; margin:0;">Set available time slots for walk-in donations. Each slot is 1 hour long.</p>
            </div>
            <form id="availability-form">
                @csrf
                <input type="hidden" name="selected_date" id="selected_date">

                <div id="time-slots-container" style="display: block;">
                    <h4 style="margin: 8px 0 10px 0; color: #333; font-size: 14px;">Available Time Slots</h4>
                    <div class="time-slots-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(90px, 1fr)); gap: 8px; margin-bottom: 8px;">
                        <!-- Time slots will be generated here -->
                    </div>
                    <div id="time-slots-error" aria-live="polite" style="display:none; color:#b91c1c; font-size:12px; margin: 0 0 8px 0;">Please select at least one time slot.</div>
                    <div style="margin: 8px 0; display:flex; gap:8px;">
                        <button type="button" class="btn btn-secondary" style="padding:6px 10px; font-size:12px;" onclick="selectAllTimeSlots()">Select All</button>
                        <button type="button" class="btn btn-secondary" style="padding:6px 10px; font-size:12px;" onclick="clearAllTimeSlots()">Clear All</button>
                    </div>
                </div>

                <div style="text-align: right; margin-top: 10px; display:flex; gap:8px; justify-content:flex-end;">
                    <button type="button" class="btn btn-secondary" style="padding:8px 12px;" onclick="closeModal('availability-settings-modal')">Cancel</button>
                    <button type="submit" id="availability-save-btn" class="btn btn-primary" style="padding:8px 12px;">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Compact styling for availability modal */
#availability-settings-modal .time-slot-label { display:flex; align-items:center; padding:6px 8px !important; border:1px solid #ddd; border-radius:6px; background:#fafafa; cursor:pointer; }
#availability-settings-modal .time-slot-label input { margin-right:6px; }
#availability-settings-modal .time-slot-label .slot-text { font-size:12px; }
</style>

<script>
    function openAvailabilityModal(date) {
        document.getElementById('selected_date').value = date;
        document.getElementById('selected-date-display').textContent = new Date(date).toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

        // Generate time slots first, then load existing availability for this date
        generateTimeSlots();
        updateSaveButtonState();
        loadExistingAvailability(date);
        // Ensure the calendar modal is hidden so it doesn't overlay the settings modal
        try { closeModal('availability-calendar-modal'); } catch(e) {}
        openModal('availability-settings-modal');
    }

    function loadExistingAvailability(date) {
        fetch(`/admin/availability/day?date=${date}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.availability) {
                    if (data.availability.time_slots) {
                        // Mark existing time slots as selected
                        data.availability.time_slots.forEach(slot => {
                            const checkbox = document.querySelector(`input[name="time_slots[]"][value="${slot}"]`);
                            if (checkbox) checkbox.checked = true;
                        });
                    }
                    updateSaveButtonState();
                } else {
                    // No availability set; leave all slots unchecked
                    updateSaveButtonState();
                }
            })
            .catch(error => {
                console.error('Error loading availability:', error);
                updateSaveButtonState();
            });
    }

    function generateTimeSlots() {
        const container = document.querySelector('.time-slots-grid');
        container.innerHTML = '';

        // Generate time slots from 8:00 AM to 5:00 PM (1-hour slots)
        const startHour = 8;
        const endHour = 17;

        for (let hour = startHour; hour < endHour; hour++) {
            const timeValue = `${hour.toString().padStart(2, '0')}:00`;
            const timeDisplay = new Date(`2000-01-01T${timeValue}`).toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });

            const slotHTML = `
                <label class="time-slot-label">
                    <input type="checkbox" name="time_slots[]" value="${timeValue}">
                    <span class="slot-text">${timeDisplay}</span>
                </label>
            `;
            container.innerHTML += slotHTML;
        }

        // Bind change event to update Save button state
        document.querySelectorAll('input[name="time_slots[]"]').forEach(cb => {
            cb.addEventListener('change', updateSaveButtonState);
        });
        updateSaveButtonState();
    }

    function selectAllTimeSlots() {
        document.querySelectorAll('input[name="time_slots[]"]').forEach(checkbox => {
            checkbox.checked = true;
        });
        updateSaveButtonState();
    }

    function clearAllTimeSlots() {
        document.querySelectorAll('input[name="time_slots[]"]').forEach(checkbox => {
            checkbox.checked = false;
        });
        updateSaveButtonState();
    }

    function countSelectedTimeSlots(){
        return document.querySelectorAll('input[name="time_slots[]"]:checked').length;
    }
    function updateSaveButtonState(){
        const saveBtn = document.getElementById('availability-save-btn');
        if (!saveBtn) return;
        const count = countSelectedTimeSlots();
        // Keep button enabled so click shows SweetAlert if invalid
        saveBtn.disabled = false;
        saveBtn.title = count === 0 ? 'Pick at least one time slot' : '';
        // Toggle inline error and visual highlight
        const err = document.getElementById('time-slots-error');
        const grid = document.querySelector('.time-slots-grid');
        if (err && grid) {
            if (count === 0) {
                err.style.display = 'block';
                grid.style.outline = '2px solid #ef4444';
                grid.style.outlineOffset = '4px';
                grid.style.borderRadius = '8px';
            } else {
                err.style.display = 'none';
                grid.style.outline = '';
                grid.style.outlineOffset = '';
            }
        }
    }

    document.getElementById('availability-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        const selectedDate = formData.get('selected_date');
        const timeSlots = [];

        document.querySelectorAll('input[name="time_slots[]"]:checked').forEach(checkbox => {
            timeSlots.push(checkbox.value);
        });

        if (timeSlots.length === 0) {
            if (window.Swal) {
                Swal.fire({ title: 'Pick time slots', text: 'Please select at least one time slot before saving.', icon: 'warning' });
            } else {
                alert('Please select at least one time slot before saving.');
            }
            updateSaveButtonState();
            return;
        }

        const isAvailable = true;

        const data = {
            date: selectedDate,
            is_available: isAvailable,
            time_slots: timeSlots
        };

        fetch('{{ route("admin.availability.day.update") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => { throw new Error(text || 'Server error') });
            }
            return response.json();
        })
        .then(data => {
            if(data.success) {
                if (window.saSuccess) saSuccess('Availability updated successfully!'); else showSuccessModal('Success','Availability updated successfully!');
                closeModal('availability-settings-modal');
                // Refresh the calendar to show updated availability
                try {
                    // FullCalendar legacy (kept for backward compatibility)
                    if (typeof availabilityCalendar !== 'undefined' && availabilityCalendar && typeof availabilityCalendar.refetchEvents === 'function') {
                        availabilityCalendar.refetchEvents();
                    } else if (typeof adminCalendar !== 'undefined' && adminCalendar && typeof adminCalendar.refetchEvents === 'function') {
                        adminCalendar.refetchEvents();
                    } else if (typeof buildVanillaCalendar === 'function') {
                        const container = document.getElementById('availability-calendar');
                        if (container) buildVanillaCalendar(container);
                    } else {
                        // Fallback: reload the page if calendar refresh fails
                        setTimeout(() => window.location.reload(), 1000);
                    }
                } catch (calendarError) {
                    console.warn('Calendar refresh failed, reloading page:', calendarError);
                    setTimeout(() => window.location.reload(), 1000);
                }
            } else {
                if (window.saError) saError('Failed to update availability: ' + (data.message || 'Unknown error')); else alert('Failed to update availability: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (window.saError) saError('An error occurred while updating availability. Please try again.'); else alert('An error occurred while updating availability. Please try again.');
        });
    });
</script>
