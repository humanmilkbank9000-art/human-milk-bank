<!DOCTYPE html>
<html>
<head>
    <title>Test Auto-Fill Functionality</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { margin: 20px 0; padding: 20px; border: 1px solid #ccc; }
        .form-control { padding: 8px; margin: 5px; width: 200px; }
        .btn { padding: 10px 20px; margin: 5px; cursor: pointer; }
        .btn-primary { background: #007bff; color: white; border: none; }
        .debug-log { background: #f8f9fa; padding: 10px; margin: 10px 0; font-family: monospace; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Auto-Fill Test Page</h1>
    
    <div class="test-section">
        <h2>Test Data</h2>
        <p><strong>Request ID:</strong> {{ $request->id }}</p>
        <p><strong>Donor:</strong> {{ $request->User_ID }}</p>
        <p><strong>Number of Bags:</strong> {{ $request->number_of_bags }}</p>
        <p><strong>Total Volume:</strong> {{ $request->total_volume }}</p>
        <p><strong>Status:</strong> {{ $request->status }}</p>
    </div>

    <div class="test-section">
        <h2>Manual Test Form</h2>
        <table class="modal-table">
            <thead>
                <tr>
                    <th>Donor Full Name</th>
                    <th>Type</th>
                    <th>Number of Bags</th>
                    <th>Total Volume (ml)</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <input type="text" id="walk-in-donor-name" class="form-control" readonly value="Test Donor">
                    </td>
                    <td>
                        <span id="walk-in-request-type" class="pill" style="background: #f3e5f5; color: #7b1fa2; padding: 4px 8px; border-radius: 4px;">Home Collection</span>
                    </td>
                    <td>
                        <input type="number" name="number_of_bags" id="walk-in-number-of-bags" class="form-control" readonly placeholder="Loading..." style="background: #f8f9fa;">
                        <small style="color: #666; font-size: 10px; display: block;">From original request (read-only)</small>
                    </td>
                    <td>
                        <input type="number" name="total_volume_donated" id="walk-in-total-volume" class="form-control" readonly placeholder="Loading..." style="background: #f8f9fa;">
                        <small style="color: #666; font-size: 10px; display: block;">From original request (read-only)</small>
                    </td>
                    <td>
                        <input type="date" name="date" class="form-control" required>
                    </td>
                    <td>
                        <input type="time" name="time" class="form-control" required>
                    </td>
                    <td>
                        <button type="button" class="btn btn-primary" onclick="testAutoFill()">Test Auto-Fill</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="test-section">
        <h2>Debug Log</h2>
        <div id="debug-log" class="debug-log">Click "Test Auto-Fill" to see debug information...</div>
    </div>

    <script>
        function log(message) {
            const debugLog = document.getElementById('debug-log');
            debugLog.innerHTML += new Date().toLocaleTimeString() + ': ' + message + '<br>';
        }

        function testAutoFill() {
            const donationId = {{ $request->id }};
            const requestType = 'home_collection';
            
            log('🔄 Starting auto-fill test...');
            log('Request ID: ' + donationId);
            log('Request Type: ' + requestType);
            
            // Clear fields first
            document.getElementById('walk-in-number-of-bags').value = '';
            document.getElementById('walk-in-total-volume').value = '';
            document.getElementById('walk-in-number-of-bags').placeholder = 'Loading...';
            document.getElementById('walk-in-total-volume').placeholder = 'Loading...';
            
            const endpoint = `/admin/donation-history/${donationId}`;
            log('📡 Fetching from endpoint: ' + endpoint);
            
            fetch(endpoint)
                .then(response => {
                    log('📥 Response status: ' + response.status);
                    return response.json();
                })
                .then(data => {
                    log('📦 Received data: ' + JSON.stringify(data, null, 2));
                    
                    if (data.success) {
                        const donation = data.data;
                        log('✅ Success! Processing donation data...');
                        
                        if (donation.number_of_bags) {
                            document.getElementById('walk-in-number-of-bags').value = donation.number_of_bags;
                            log('✅ Set number of bags to: ' + donation.number_of_bags);
                        } else {
                            log('⚠️ No number_of_bags found in data');
                        }
                        
                        if (donation.total_volume) {
                            document.getElementById('walk-in-total-volume').value = donation.total_volume;
                            log('✅ Set total volume to: ' + donation.total_volume);
                        } else {
                            log('⚠️ No total_volume found in data');
                        }
                        
                        document.getElementById('walk-in-number-of-bags').placeholder = 'Enter number of bags';
                        document.getElementById('walk-in-total-volume').placeholder = 'Enter total volume';
                        
                        log('🎉 Auto-fill completed successfully!');
                    } else {
                        log('❌ API returned error: ' + data.message);
                    }
                })
                .catch(error => {
                    log('❌ Fetch error: ' + error.message);
                    console.error('Full error:', error);
                });
        }

        // Auto-run the test when page loads
        window.onload = function() {
            log('🚀 Page loaded. Ready to test auto-fill.');
            // Set default date and time
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            document.querySelector('input[name="date"]').value = tomorrow.toISOString().split('T')[0];
            document.querySelector('input[name="time"]').value = '09:00';
        };
    </script>
</body>
</html>
