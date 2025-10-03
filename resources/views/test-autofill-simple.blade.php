<!DOCTYPE html>
<html>
<head>
    <title>Simple Auto-Fill Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .test-section { margin: 20px 0; padding: 20px; border: 1px solid #ccc; border-radius: 5px; }
        .form-group { margin: 10px 0; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-control { padding: 8px; width: 200px; border: 1px solid #ddd; border-radius: 3px; }
        .btn { padding: 10px 20px; margin: 5px; cursor: pointer; border: none; border-radius: 3px; }
        .btn-primary { background: #007bff; color: white; }
        .btn-success { background: #28a745; color: white; }
        .debug-log { background: #f8f9fa; padding: 15px; margin: 10px 0; font-family: monospace; font-size: 12px; border-radius: 3px; max-height: 400px; overflow-y: auto; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .info { color: #17a2b8; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Auto-Fill Debug Test</h1>
        
        <div class="test-section">
            <h2>Step 1: Check Database</h2>
            <button class="btn btn-primary" onclick="checkDatabase()">Check Database for Home Collection Data</button>
            <div id="database-result" class="debug-log">Click button to check database...</div>
        </div>

        <div class="test-section">
            <h2>Step 2: Test Auto-Fill</h2>
            <div class="form-group">
                <label>Home Collection ID:</label>
                <input type="number" id="test-id" class="form-control" placeholder="Enter ID from Step 1">
            </div>
            <button class="btn btn-success" onclick="testAutoFill()">Test Auto-Fill for This ID</button>
            
            <h3>Form Fields (like in admin modal):</h3>
            <div class="form-group">
                <label>Number of Bags:</label>
                <input type="number" id="bags-field" class="form-control" placeholder="Should auto-fill">
            </div>
            <div class="form-group">
                <label>Total Volume (ml):</label>
                <input type="number" id="volume-field" class="form-control" placeholder="Should auto-fill">
            </div>
            
            <div id="autofill-result" class="debug-log">Click "Test Auto-Fill" to see results...</div>
        </div>

        <div class="test-section">
            <h2>Step 3: Manual Endpoint Test</h2>
            <div class="form-group">
                <label>Test Endpoint URL:</label>
                <input type="text" id="endpoint-url" class="form-control" style="width: 400px;" placeholder="/admin/donation-history/1">
            </div>
            <button class="btn btn-primary" onclick="testEndpoint()">Test Endpoint Directly</button>
            <div id="endpoint-result" class="debug-log">Click button to test endpoint...</div>
        </div>
    </div>

    <script>
        function log(elementId, message, type = 'info') {
            const element = document.getElementById(elementId);
            const timestamp = new Date().toLocaleTimeString();
            const className = type === 'error' ? 'error' : type === 'success' ? 'success' : 'info';
            element.innerHTML += `<span class="${className}">[${timestamp}] ${message}</span><br>`;
            element.scrollTop = element.scrollHeight;
        }

        function clearLog(elementId) {
            document.getElementById(elementId).innerHTML = '';
        }

        async function checkDatabase() {
            clearLog('database-result');
            log('database-result', '🔄 Checking database for home collection data...', 'info');
            
            try {
                const response = await fetch('/debug-autofill-issue');
                const data = await response.json();
                
                if (data.error) {
                    log('database-result', `❌ Error: ${data.error}`, 'error');
                    return;
                }
                
                const dbCheck = data.step_1_database_check;
                log('database-result', `✅ Found ${dbCheck.total_home_collections} home collection records`, 'success');
                
                if (dbCheck.test_record) {
                    log('database-result', `📋 Test Record ID: ${dbCheck.test_record.id}`, 'info');
                    log('database-result', `📋 Bags: ${dbCheck.bags_value} (has_bags: ${dbCheck.has_bags})`, 'info');
                    log('database-result', `📋 Volume: ${dbCheck.volume_value} (has_volume: ${dbCheck.has_volume})`, 'info');
                    
                    // Auto-fill the test ID
                    document.getElementById('test-id').value = dbCheck.test_record.id;
                    document.getElementById('endpoint-url').value = `/admin/donation-history/${dbCheck.test_record.id}`;
                    
                    log('database-result', `✅ Auto-filled test ID: ${dbCheck.test_record.id}`, 'success');
                }
                
                const controllerTest = data.step_2_controller_test;
                log('database-result', `🔧 Controller Status: ${controllerTest.status_code}`, controllerTest.status_code === 200 ? 'success' : 'error');
                log('database-result', `🔧 Controller Success: ${controllerTest.success}`, controllerTest.success ? 'success' : 'error');
                log('database-result', `🔧 Has Data: ${controllerTest.has_data}`, controllerTest.has_data ? 'success' : 'error');
                
            } catch (error) {
                log('database-result', `❌ Fetch Error: ${error.message}`, 'error');
            }
        }

        async function testAutoFill() {
            const testId = document.getElementById('test-id').value;
            if (!testId) {
                alert('Please enter a Home Collection ID first (run Step 1)');
                return;
            }
            
            clearLog('autofill-result');
            log('autofill-result', `🔄 Testing auto-fill for ID: ${testId}`, 'info');
            
            // Clear fields first
            document.getElementById('bags-field').value = '';
            document.getElementById('volume-field').value = '';
            document.getElementById('bags-field').placeholder = 'Loading...';
            document.getElementById('volume-field').placeholder = 'Loading...';
            
            const endpoint = `/admin/donation-history/${testId}`;
            log('autofill-result', `📡 Calling endpoint: ${endpoint}`, 'info');
            
            try {
                const response = await fetch(endpoint);
                log('autofill-result', `📥 Response status: ${response.status}`, response.ok ? 'success' : 'error');
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                log('autofill-result', `📦 Response data: ${JSON.stringify(data, null, 2)}`, 'info');
                
                if (data.success && data.data) {
                    const donation = data.data;
                    log('autofill-result', `✅ Processing donation data...`, 'success');
                    
                    const bags = donation.number_of_bags || 0;
                    const volume = donation.total_volume || 0;
                    
                    document.getElementById('bags-field').value = bags;
                    document.getElementById('volume-field').value = volume;
                    
                    document.getElementById('bags-field').placeholder = 'From user request';
                    document.getElementById('volume-field').placeholder = 'From user request';
                    
                    log('autofill-result', `🎉 SUCCESS! Auto-filled: ${bags} bags, ${volume}ml`, 'success');
                } else {
                    log('autofill-result', `❌ API returned error: ${data.message || 'No data'}`, 'error');
                    document.getElementById('bags-field').placeholder = 'Error loading';
                    document.getElementById('volume-field').placeholder = 'Error loading';
                }
                
            } catch (error) {
                log('autofill-result', `❌ Fetch error: ${error.message}`, 'error');
                document.getElementById('bags-field').placeholder = 'Error loading';
                document.getElementById('volume-field').placeholder = 'Error loading';
            }
        }

        async function testEndpoint() {
            const endpointUrl = document.getElementById('endpoint-url').value;
            if (!endpointUrl) {
                alert('Please enter an endpoint URL');
                return;
            }
            
            clearLog('endpoint-result');
            log('endpoint-result', `🔄 Testing endpoint: ${endpointUrl}`, 'info');
            
            try {
                const response = await fetch(endpointUrl);
                log('endpoint-result', `📥 Status: ${response.status} ${response.statusText}`, response.ok ? 'success' : 'error');
                
                const data = await response.json();
                log('endpoint-result', `📦 Response: ${JSON.stringify(data, null, 2)}`, 'info');
                
                if (data.success) {
                    log('endpoint-result', `✅ Endpoint working correctly!`, 'success');
                } else {
                    log('endpoint-result', `❌ Endpoint returned error: ${data.message}`, 'error');
                }
                
            } catch (error) {
                log('endpoint-result', `❌ Error: ${error.message}`, 'error');
            }
        }

        // Auto-run database check on page load
        window.onload = function() {
            log('database-result', '🚀 Page loaded. Click "Check Database" to start testing.', 'info');
        };
    </script>
</body>
</html>
