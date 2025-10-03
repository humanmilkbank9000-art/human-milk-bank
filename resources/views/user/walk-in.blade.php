<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.theme')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Walk-in Donation - Breastmilk Donation System</title>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <style>
        /* Add your styles here */
    </style>
</head>
<body>
    <div class="container">
        <h1>Walk-in Donation</h1>
        @if(session('success'))
            <div style="background:#d4edda;color:#155724;border:1px solid #c3e6cb;border-radius:6px;padding:10px 12px;margin-bottom:12px;">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div style="background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;border-radius:6px;padding:10px 12px;margin-bottom:12px;">{{ session('error') }}</div>
        @endif
        <div id="calendar-container"></div>
        <div id="time-slots-container" style="margin-top: 20px;"></div>
        <form id="walkInForm" action="{{ route('donation.walk-in') }}" method="POST">
            @csrf
            <input type="hidden" name="donation_date" id="walkInDate">
            <select name="donation_time" id="walkInTime" required>
                <option value="">Select a date to see available times</option>
            </select>
            <button id="walkInSubmit" type="submit">Book Appointment</button>
        </form>

        <!-- Confirmation Modal -->
        <div id="confirmModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:1000; align-items:center; justify-content:center;">
            <div style="background:#fff; border-radius:10px; padding:16px 18px; width: 90%; max-width:360px; box-shadow:0 10px 30px rgba(0,0,0,.15);">
                <div style="font-weight:700; font-size:16px; margin-bottom:8px; color:#333;">Confirm Walk-in Appointment</div>
                <div id="confirmText" style="font-size:14px; color:#444; line-height:1.5;">Please confirm your appointment.</div>
                <div style="margin-top:14px; display:flex; gap:8px; justify-content:flex-end;">
                    <button type="button" id="cancelConfirm" style="padding:8px 12px; border-radius:8px; border:1px solid #dee2e6; background:#f8f9fa; color:#333; cursor:pointer;">Cancel</button>
                    <button type="button" id="okConfirm" style="padding:8px 12px; border-radius:8px; border:0; background:#ff69b4; color:#fff; cursor:pointer;">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar-container');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: function(fetchInfo, successCallback, failureCallback) {
                    fetch(`/donation/availability?month=${fetchInfo.start.getMonth() + 1}&year=${fetchInfo.start.getFullYear()}`)
                        .then(response => response.json())
                        .then(data => {
                            let events = [];
                            if (data.success) {
                                data.availability.forEach(day => {
                                    if (day.is_available) {
                                        events.push({
                                            title: 'Available',
                                            start: day.date,
                                            allDay: true,
                                            backgroundColor: '#ffc0cb',
                                            borderColor: '#ffc0cb'
                                        });
                                    }
                                });
                            }
                            successCallback(events);
                        })
                        .catch(error => failureCallback(error));
                },
                dateClick: function(info) {
                    document.getElementById('walkInDate').value = info.dateStr;
                    loadAvailableSlots('walkIn');
                }
            });
            calendar.render();
        });

        function loadAvailableSlots(formType) {
            const dateInput = document.getElementById(formType + 'Date');
            const timeSelect = document.getElementById(formType + 'Time');
            const selectedDate = dateInput.value;

            if (!selectedDate) {
                timeSelect.innerHTML = '<option value="">Select Date First</option>';
                return;
            }

            // Show loading
            timeSelect.innerHTML = '<option value="">Loading available slots...</option>';

            fetch(`/donation/available-slots?date=${selectedDate}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.slots.length > 0) {
                        timeSelect.innerHTML = '<option value="">Select Time</option>';
                        data.slots.forEach(slot => {
                            const timeDisplay = new Date(`2000-01-01T${slot}`).toLocaleTimeString('en-US', {
                                hour: 'numeric',
                                minute: '2-digit',
                                hour12: true
                            });
                            timeSelect.innerHTML += `<option value="${slot}">${timeDisplay}</option>`;
                        });
                    } else {
                        timeSelect.innerHTML = '<option value="">No available slots for this date</option>';
                    }
                })
                .catch(error => {
                    console.error('Error loading available slots:', error);
                    timeSelect.innerHTML = '<option value="">Error loading slots</option>';
                });
        }

        // Client-side guard + custom confirmation modal
        (function() {
            const form = document.getElementById('walkInForm');
            const submitBtn = document.getElementById('walkInSubmit');
            const dateInput = document.getElementById('walkInDate');
            const timeSelect = document.getElementById('walkInTime');
            const modal = document.getElementById('confirmModal');
            const confirmText = document.getElementById('confirmText');
            const okBtn = document.getElementById('okConfirm');
            const cancelBtn = document.getElementById('cancelConfirm');
            let pendingSubmit = false;

            form.addEventListener('submit', function(e) {
                // If this was triggered by Confirm button, allow submit
                if (pendingSubmit) {
                    pendingSubmit = false;
                    return true;
                }

                // Validate date/time selected
                if (!dateInput.value) {
                    e.preventDefault();
                    alert('Please select a date from the calendar.');
                    return false;
                }
                if (!timeSelect.value) {
                    e.preventDefault();
                    alert('Please select a time slot.');
                    return false;
                }

                // Show custom confirmation modal
                e.preventDefault();
                const selectedDate = dateInput.value;
                const selectedTime = timeSelect.value;
                const timeDisplay = new Date(`2000-01-01T${selectedTime}`).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
                confirmText.textContent = `Please confirm your walk-in appointment on ${selectedDate} at ${timeDisplay}.`;
                modal.style.display = 'flex';
            });

            cancelBtn.addEventListener('click', function() {
                modal.style.display = 'none';
            });

            okBtn.addEventListener('click', function() {
                modal.style.display = 'none';
                pendingSubmit = true;
                form.requestSubmit ? form.requestSubmit() : form.submit();
            });
        })();
    </script>
</body>
</html>
