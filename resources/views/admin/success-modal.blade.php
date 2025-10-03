<!-- Success Modal -->
<div id="success-modal" class="modal">
    <div class="modal-content" style="max-width: 400px; text-align: center;">
        <div class="modal-header" style="border-bottom: none; padding-bottom: 10px;">
            <div class="success-icon" style="font-size: 48px; color: #28a745; margin-bottom: 10px;">
                ✅
            </div>
            <h3 class="modal-title" id="success-title" style="color: #28a745; margin: 0;">Success!</h3>
            <button class="close-btn" onclick="closeModal('success-modal')" style="position: absolute; top: 15px; right: 20px;">&times;</button>
        </div>

        <div class="modal-body" style="padding: 20px;">
            <p id="success-message" style="font-size: 16px; color: #333; margin: 0;">Operation completed successfully!</p>
        </div>

        <div class="modal-footer" style="border-top: none; padding-top: 10px;">
            <button type="button" class="btn btn-primary" onclick="closeModal('success-modal')" style="padding: 10px 30px;">OK</button>
        </div>
    </div>
</div>

<script>
    function showSuccessModal(title, message) {
        document.getElementById('success-title').textContent = title || 'Success!';
        document.getElementById('success-message').textContent = message || 'Operation completed successfully!';
        openModal('success-modal');
    }
</script>

<style>
    #success-modal .modal-content {
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    }
    
    #success-modal .success-icon {
        animation: successPulse 0.6s ease-in-out;
    }
    
    @keyframes successPulse {
        0% { transform: scale(0.8); opacity: 0; }
        50% { transform: scale(1.1); opacity: 1; }
        100% { transform: scale(1); opacity: 1; }
    }
    
    #success-modal .btn-primary {
        background-color: #28a745;
        border-color: #28a745;
        border-radius: 5px;
        font-weight: 500;
    }
    
    #success-modal .btn-primary:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }
</style>