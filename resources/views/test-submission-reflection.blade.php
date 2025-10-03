<!DOCTYPE html>
<html>
<head>
    <title>Test Submission Time Reflection & Auto-Fill</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 1000px; margin: 0 auto; }
        .test-section { margin: 20px 0; padding: 20px; border: 1px solid #ccc; border-radius: 5px; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .info { color: #17a2b8; }
        .warning { color: #ffc107; }
        .debug-log { background: #f8f9fa; padding: 15px; margin: 10px 0; font-family: monospace; font-size: 12px; border-radius: 3px; max-height: 400px; overflow-y: auto; }
        .btn { padding: 10px 20px; margin: 5px; cursor: pointer; border: none; border-radius: 3px; }
        .btn-primary { background: #007bff; color: white; }
        .btn-success { background: #28a745; color: white; }
        .comparison-table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        .comparison-table th, .comparison-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .comparison-table th { background-color: #f2f2f2; }
        .highlight { background-color: #fff3cd; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Test Submission Time Reflection & Auto-Fill</h1>
        
        <div class="test-section">
            <h2>Step 1: Create Test Data</h2>
            <button class="btn btn-primary" onclick="createTestData()">Create Test Data</button>
            <div id="test-data-result" class="debug-log">Click button to create test data...</div>
        </div>

        <div class="test-section">
            <h2>Step 2: Test Walk-in Submission Time Reflection</h2>
            <p><strong>Expected:</strong> Date/time should reflect when the request was <strong>submitted</strong>, not the appointment time.</p>
            
            <div id="walk-in-test-info"></div>
            
            <button class="btn btn-success" onclick="testWalkInSubmissionTime()">Test Walk-in Submission Time</button>
            <div id="walk-in-result" class="debug-log">Click button to test walk-in submission time...</div>
        </div>

        <div class="test-section">
            <h2>Step 3: Test Home Collection Auto-Fill</h2>
            <p><strong>Expected:</strong> Number of bags and total volume should be fetched from user's original request.</p>
            
            <div id="home-collection-test-info"></div>
            
            <button class="btn btn-success" onclick="testHomeCollectionAutoFill()">Test Home Collection Auto-Fill</button>
            <div id="home-collection-result" class="debug-log">Click button to test home collection auto-fill...</div>
        </div>

        <div class="test-section">
            <h2>Step 4: Manual Admin Dashboard Test</h2>
            <p>After the above tests pass, test in the actual admin dashboard:</p>
            <ol>
                <li>Go to <a href="/admin/dashboard" target="_blank">Admin Dashboard</a></li>
                <li>Click "Pending Walk-in Requests"</li>
                <li>Click "Validate Donation" - check date/time reflects submission time</li>
                <li>Click "Pending Home Collection Requests"</li>
                <li>Click "Schedule Collection" - check bags/volume auto-fill</li>
            </ol>
        </div>
    </div>

    <script>
        function log(elementId, message, type = 'info') {
            const element = document.getElementById(elementId);
            const timestamp = new Date().toLocaleTimeString();
            const className = type === 'error' ? 'error' : type === 'success' ? 'success' : type === 'warning' ? 'warning' : 'info';
            element.innerHTML += `<span class="${className}">[${timestamp}] ${message}</span><br>`;
            element.scrollTop = element.scrollHeight;
        }

        function clearLog(elementId) {
            document.getElementById(elementId).innerHTML = '';
        }

        let testData = null;

        async function createTestData() {
            clearLog('test-data-result');
            log('test-data-result', '🔄 Creating test data...', 'info');
            
            try {
                const response = await fetch('/test-submission-reflection');
                const data = await response.json();
                
                if (data.error) {
                    log('test-data-result', `❌ Error: ${data.error}`, 'error');
                    return;
                }
                
                testData = data;
                
                log('test-data-result', '✅ Test data created successfully!', 'success');
                log('test-data-result', `📋 Walk-in Request ID: ${data.walk_in_submission_test.id}`, 'info');
                log('test-data-result', `📋 Home Collection ID: ${data.home_collection_autofill_test.id}`, 'info');
                
                // Update test info sections
                updateWalkInTestInfo(data.walk_in_submission_test);
                updateHomeCollectionTestInfo(data.home_collection_autofill_test);
                
            } catch (error) {
                log('test-data-result', `❌ Fetch Error: ${error.message}`, 'error');
            }
        }

        function updateWalkInTestInfo(walkInData) {
            const infoDiv = document.getElementById('walk-in-test-info');
            infoDiv.innerHTML = `
                <table class="comparison-table">
                    <tr>
                        <th>Aspect</th>
                        <th>Appointment Time</th>
                        <th>Submission Time</th>
                        <th>Should Show</th>
                    </tr>
                    <tr>
                        <td>Date</td>
                        <td>${walkInData.appointment_date}</td>
                        <td class="highlight">${walkInData.submission_date}</td>
                        <td class="highlight">${walkInData.expected_behavior.date_field_should_show}</td>
                    </tr>
                    <tr>
                        <td>Time</td>
                        <td>${walkInData.appointment_time}</td>
                        <td class="highlight">${walkInData.submission_time}</td>
                        <td class="highlight">${walkInData.expected_behavior.time_field_should_show}</td>
                    </tr>
                </table>
            `;
        }

        function updateHomeCollectionTestInfo(homeData) {
            const infoDiv = document.getElementById('home-collection-test-info');
            infoDiv.innerHTML = `
                <table class="comparison-table">
                    <tr>
                        <th>Field</th>
                        <th>User's Original Request</th>
                        <th>Should Auto-Fill To</th>
                    </tr>
                    <tr>
                        <td>Number of Bags</td>
                        <td class="highlight">${homeData.user_requested_bags}</td>
                        <td class="highlight">${homeData.expected_behavior.bags_field_should_show}</td>
                    </tr>
                    <tr>
                        <td>Total Volume</td>
                        <td class="highlight">${homeData.user_requested_volume}ml</td>
                        <td class="highlight">${homeData.expected_behavior.volume_field_should_show}ml</td>
                    </tr>
                    <tr>
                        <td>Address</td>
                        <td>${homeData.pickup_address}</td>
                        <td>Should be displayed (read-only)</td>
                    </tr>
                </table>
            `;
        }

        async function testWalkInSubmissionTime() {
            if (!testData) {
                alert('Please create test data first');
                return;
            }
            
            clearLog('walk-in-result');
            log('walk-in-result', '🔄 Testing walk-in submission time reflection...', 'info');
            
            const walkInData = testData.walk_in_submission_test;
            const endpoint = walkInData.endpoint_test.url;
            
            log('walk-in-result', `📡 Testing endpoint: ${endpoint}`, 'info');
            
            try {
                const response = await fetch(endpoint);
                const data = await response.json();
                
                log('walk-in-result', `📥 Response status: ${response.status}`, response.ok ? 'success' : 'error');
                log('walk-in-result', `📦 Response data: ${JSON.stringify(data, null, 2)}`, 'info');
                
                if (data.success && data.data) {
                    const request = data.data;
                    
                    if (request.created_at) {
                        const submissionDate = new Date(request.created_at);
                        const expectedDate = submissionDate.toISOString().split('T')[0];
                        const expectedTime = submissionDate.toTimeString().split(' ')[0].substring(0, 5);
                        
                        log('walk-in-result', `✅ Found created_at: ${request.created_at}`, 'success');
                        log('walk-in-result', `📅 Parsed submission date: ${expectedDate}`, 'info');
                        log('walk-in-result', `⏰ Parsed submission time: ${expectedTime}`, 'info');
                        log('walk-in-result', `🎯 Expected in modal: Date=${expectedDate}, Time=${expectedTime}`, 'success');
                        
                        // Compare with appointment time
                        log('walk-in-result', `📋 Appointment date: ${walkInData.appointment_date} (should NOT be used)`, 'warning');
                        log('walk-in-result', `📋 Appointment time: ${walkInData.appointment_time} (should NOT be used)`, 'warning');
                        
                        if (expectedDate !== walkInData.appointment_date) {
                            log('walk-in-result', '✅ SUCCESS: Submission date is different from appointment date', 'success');
                        } else {
                            log('walk-in-result', '⚠️ WARNING: Submission date same as appointment date - might be coincidence', 'warning');
                        }
                        
                    } else {
                        log('walk-in-result', '❌ No created_at field found in response', 'error');
                    }
                } else {
                    log('walk-in-result', `❌ API error: ${data.message || 'No data'}`, 'error');
                }
                
            } catch (error) {
                log('walk-in-result', `❌ Fetch error: ${error.message}`, 'error');
            }
        }

        async function testHomeCollectionAutoFill() {
            if (!testData) {
                alert('Please create test data first');
                return;
            }
            
            clearLog('home-collection-result');
            log('home-collection-result', '🔄 Testing home collection auto-fill...', 'info');
            
            const homeData = testData.home_collection_autofill_test;
            const endpoint = homeData.endpoint_test.url;
            
            log('home-collection-result', `📡 Testing endpoint: ${endpoint}`, 'info');
            
            try {
                const response = await fetch(endpoint);
                const data = await response.json();
                
                log('home-collection-result', `📥 Response status: ${response.status}`, response.ok ? 'success' : 'error');
                log('home-collection-result', `📦 Response data: ${JSON.stringify(data, null, 2)}`, 'info');
                
                if (data.success && data.data) {
                    const donation = data.data;
                    
                    log('home-collection-result', `📊 Found donation data:`, 'info');
                    log('home-collection-result', `   - Number of bags: ${donation.number_of_bags}`, 'info');
                    log('home-collection-result', `   - Total volume: ${donation.total_volume}`, 'info');
                    
                    // Check if values match expected
                    if (donation.number_of_bags == homeData.user_requested_bags) {
                        log('home-collection-result', `✅ Bags match: ${donation.number_of_bags} = ${homeData.user_requested_bags}`, 'success');
                    } else {
                        log('home-collection-result', `❌ Bags mismatch: ${donation.number_of_bags} ≠ ${homeData.user_requested_bags}`, 'error');
                    }
                    
                    if (donation.total_volume == homeData.user_requested_volume) {
                        log('home-collection-result', `✅ Volume match: ${donation.total_volume} = ${homeData.user_requested_volume}`, 'success');
                    } else {
                        log('home-collection-result', `❌ Volume mismatch: ${donation.total_volume} ≠ ${homeData.user_requested_volume}`, 'error');
                    }
                    
                    log('home-collection-result', '🎯 These values should auto-fill in the modal fields', 'success');
                    
                } else {
                    log('home-collection-result', `❌ API error: ${data.message || 'No data'}`, 'error');
                }
                
            } catch (error) {
                log('home-collection-result', `❌ Fetch error: ${error.message}`, 'error');
            }
        }

        // Auto-create test data on page load
        window.onload = function() {
            log('test-data-result', '🚀 Page loaded. Click "Create Test Data" to start testing.', 'info');
        };
    </script>
</body>
</html>
