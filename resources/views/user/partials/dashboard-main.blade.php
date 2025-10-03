<div class="content-box">
    <h1 class="content-title">Welcome, {{ session('user_name', 'User') }}!</h1>
    <h2 class="content-title">Step-by-step guide on how to donate breastmilk:</h2>

    <div class="step-guide">
        <div class="step-card" onclick="openStepModal(1)">
            <div class="step-number">1</div>
            <div class="step-title">Undergo Health Screening</div>
            <div class="step-preview">Complete medical history and lifestyle assessment to ensure you're healthy and eligible to donate...</div>
        </div>

        <div class="step-card" onclick="openStepModal(2)">
            <div class="step-number">2</div>
            <div class="step-title">Get Approved as a Donor</div>
            <div class="step-preview">Once your health screening is successful, you will be approved as a donor and can begin...</div>
        </div>

        <div class="step-card" onclick="openStepModal(3)">
            <div class="step-number">3</div>
            <div class="step-title">Follow Safe Milk Expression</div>
            <div class="step-preview">Use clean hands and sterilized equipment when expressing milk. Store properly with labels...</div>
        </div>

        <div class="step-card" onclick="openStepModal(4)">
            <div class="step-number">4</div>
            <div class="step-title">Freeze and Store Properly</div>
            <div class="step-preview">Freeze the milk immediately after pumping and store in a deep freezer until collection...</div>
        </div>

        <div class="step-card" onclick="openStepModal(5)">
            <div class="step-number">5</div>
            <div class="step-title">Coordinate Pickup/Drop-off</div>
            <div class="step-preview">Some milk banks offer pickup services, while others require you to drop off the milk...</div>
        </div>

        <div class="step-card" onclick="openStepModal(6)">
            <div class="step-number">6</div>
            <div class="step-title">Repeat as Needed</div>
            <div class="step-preview">Continue donating as long as you are approved and producing excess milk for babies in need...</div>
        </div>
    </div>
</div>