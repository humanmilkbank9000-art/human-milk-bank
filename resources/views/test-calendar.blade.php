<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Calendar - Pink Highlighting</title>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f5f5f5;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: #ff69b4;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .info-box {
            background: #e7f3ff;
            border: 1px solid #b3d9ff;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        #calendar {
            margin-top: 20px;
        }
        
        /* Force pink highlighting */
        .fc-daygrid-day.available-day {
            background-color: #ffc0cb !important;
            border-color: #ff69b4 !important;
        }
        
        .fc-daygrid-day.available-day .fc-daygrid-day-number {
            color: #ff1493 !important;
            font-weight: bold !important;
        }
        
        .debug-info {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🗓️ Calendar Test - Pink Highlighting</h1>
        
        <div class="info-box">
            <h3>📋 Test Instructions:</h3>
            <ul>
                <li>Available dates should be highlighted in <strong style="color: #ff69b4;">pink</strong></li>
                <li>Click on a pink date to see available time slots</li>
                <li>If no pink dates are visible, check the debug info below</li>
            </ul>
        </div>
        
        <div id="calendar"></div>
        
        <div class="debug-info">
            <h4>🔍 Debug Information:</h4>
            <div id="debug-output">Loading debug info...</div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Load debug info
            fetch('/debug/availability')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('debug-output').innerHTML = `
                        <p><strong>Available Days:</strong> ${data.count_days}</p>
                        <p><strong>Available Time Slots:</strong> ${data.count_slots}</p>
                        <p><strong>Sample Dates:</strong> ${data.availability_days.map(d => d.date).join(', ')}</p>
                    `;
                })
                .catch(error => {
                    document.getElementById('debug-output').innerHTML = `<p style="color: red;">Error loading debug info: ${error}</p>`;
                });

            // Initialize calendar
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth'
                },
                events: function(fetchInfo, successCallback, failureCallback) {
                    console.log('Fetching availability...');
                    fetch(`/donation/availability?month=${fetchInfo.start.getMonth() + 1}&year=${fetchInfo.start.getFullYear()}`)
                        .then(response => response.json())
                        .then(data => {
                            console.log('Received data:', data);
                            if (data.success && data.availability) {
                                const events = data.availability.map(day => {
                                    console.log('Creating event for:', day.date);
                                    return {
                                        start: day.date,
                                        display: 'background',
                                        backgroundColor: '#ffc0cb',
                                        borderColor: '#ff69b4',
                                        classNames: ['available-day']
                                    };
                                });
                                console.log('Created events:', events);
                                successCallback(events);
                            } else {
                                console.log('No availability data');
                                successCallback([]);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            failureCallback(error);
                        });
                },
                eventDidMount: function(info) {
                    console.log('Mounting event:', info.event);
                    if (info.event.display === 'background') {
                        var dayEl = info.el.closest('.fc-daygrid-day');
                        if (dayEl) {
                            console.log('Applying styles to:', dayEl);
                            dayEl.style.backgroundColor = '#ffc0cb !important';
                            dayEl.style.borderColor = '#ff69b4 !important';
                            dayEl.classList.add('available-day');
                            
                            const dayNumber = dayEl.querySelector('.fc-daygrid-day-number');
                            if (dayNumber) {
                                dayNumber.style.color = '#ff1493';
                                dayNumber.style.fontWeight = 'bold';
                            }
                        }
                    }
                },
                dateClick: function(info) {
                    alert('Clicked on: ' + info.dateStr);
                }
            });
            
            calendar.render();
        });
    </script>
</body>
</html>
