<div class="content-box">
    <h1 class="content-title">Welcome to Your Dashboard</h1>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <!-- Quick Stats -->
        <div style="background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <h3 style="margin-bottom: 10px; font-size: 18px;">📊 Quick Stats</h3>
            <div id="quickStats">
                <p>Loading statistics...</p>
            </div>
        </div>

        <!-- Recent Activity -->
        <div style="background: linear-gradient(135deg, #ff6b6b, #ee5a24); color: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <h3 style="margin-bottom: 10px; font-size: 18px;">🕒 Recent Activity</h3>
            <div id="recentActivity">
                <p>Loading recent activity...</p>
            </div>
        </div>

        <!-- Health Screening Status -->
        <div style="background: linear-gradient(135deg, #26de81, #20bf6b); color: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <h3 style="margin-bottom: 10px; font-size: 18px;">🏥 Health Screening</h3>
            <div id="healthScreeningStatus">
                <p>Loading status...</p>
            </div>
        </div>
    </div>

    <!-- Notifications -->
    <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 20px;">
        <h3 style="margin-bottom: 15px; color: #333; font-size: 20px;">🔔 Notifications</h3>
        <div id="notifications">
            <p style="color: #666;">Loading notifications...</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        <h3 style="margin-bottom: 15px; color: #333; font-size: 20px;">⚡ Quick Actions</h3>
        <div style="display: flex; gap: 15px; flex-wrap: wrap;">
            <button onclick="showHealthScreening()" style="background: #667eea; color: white; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.3s ease;">
                📋 Complete Health Screening
            </button>
            <button onclick="showDonate()" style="background: #ff6b6b; color: white; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.3s ease;">
                ❤️ Donate Breastmilk
            </button>
            <button onclick="showDonationHistory()" style="background: #26de81; color: white; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.3s ease;">
                📚 View History
            </button>
        </div>
    </div>
</div>

<script>
// Load dashboard data when this partial is loaded
document.addEventListener('DOMContentLoaded', function() {
    loadDashboardData();
});

function loadDashboardData() {
    // Load notifications
    fetch('/user/notifications')
        .then(response => response.json())
        .then(data => {
            const notificationsDiv = document.getElementById('notifications');
            if (data.success && data.notifications && data.notifications.length > 0) {
                notificationsDiv.innerHTML = data.notifications.slice(0, 5).map(notification => `
                    <div style="padding: 12px; border-left: 4px solid #667eea; background: #f8f9ff; margin-bottom: 10px; border-radius: 4px;">
                        <p style="margin: 0; color: #333; font-weight: 500;">${notification.title}</p>
                        <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">${notification.message}</p>
                        <small style="color: #999;">${new Date(notification.created_at).toLocaleDateString()}</small>
                    </div>
                `).join('');
            } else {
                notificationsDiv.innerHTML = '<p style="color: #666; font-style: italic;">No new notifications</p>';
            }
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
            document.getElementById('notifications').innerHTML = '<p style="color: #ff6b6b;">Error loading notifications</p>';
        });

    // Load health screening status
    fetch('/health-screening/check-existing')
        .then(response => response.json())
        .then(data => {
            const statusDiv = document.getElementById('healthScreeningStatus');
            if (!data.hasExisting) {
                statusDiv.innerHTML = '<p>❌ Not completed</p><small>Complete your health screening to start donating</small>';
            } else {
                const statusText = data.status === 'pending' ? '⏳ Under Review' : 
                                 data.status === 'accepted' ? '✅ Approved' : '❌ Declined';
                statusDiv.innerHTML = `<p>${statusText}</p><small>Status: ${data.status}</small>`;
            }
        })
        .catch(error => {
            console.error('Error loading health screening status:', error);
            document.getElementById('healthScreeningStatus').innerHTML = '<p>Error loading status</p>';
        });

    // Load quick stats
    fetch('/donation/pending-requests')
        .then(response => response.json())
        .then(data => {
            const statsDiv = document.getElementById('quickStats');
            const pendingCount = data.success ? data.data.length : 0;
            statsDiv.innerHTML = `
                <p><strong>${pendingCount}</strong> Pending Requests</p>
                <small>Donation requests awaiting processing</small>
            `;
        })
        .catch(error => {
            console.error('Error loading stats:', error);
            document.getElementById('quickStats').innerHTML = '<p>Error loading stats</p>';
        });

    // Set recent activity
    document.getElementById('recentActivity').innerHTML = `
        <p>Dashboard accessed</p>
        <small>Just now</small>
    `;
}
</script>