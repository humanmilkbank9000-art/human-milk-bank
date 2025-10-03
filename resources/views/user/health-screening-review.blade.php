<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Health Screening - HMBLSC</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #ff69b4 0%, #ffb6ce 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #ff69b4, #ffb6ce);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 16px;
            opacity: 0.9;
        }

        .content {
            padding: 40px;
        }

        .review-section {
            margin-bottom: 32px;
            border: 1px solid #e9ecef;
            border-radius: 16px;
            overflow: hidden;
            background: #fff;
        }

        /* Section header bar to match screenshot */
        .section-header {
            background: linear-gradient(90deg, #ffc1d8 0%, #ff69b4 60%, #ff5fb0 100%);
            padding: 12px 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.25);
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
        }

        /* Ensure the title text is visible (override any gradient text styles) */
        .section-title {
            font-size: 16px;
            font-weight: 800;
            color: #ffffff !important;
            -webkit-text-fill-color: #ffffff !important;
            -webkit-background-clip: border-box !important;
            background: none !important;
            text-shadow: 0 1px 2px rgba(0,0,0,0.12);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-body {
            padding: 25px;
        }

        .question-item {
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 4px solid #ff69b4;
        }

        .question-text {
            font-weight: 600;
            color: #1f2937; /* slate-800 */
            margin: 0 0 6px;
            line-height: 1.35;
            font-size: 15px;
            -webkit-font-smoothing: antialiased;
            text-rendering: optimizeLegibility;
            font-feature-settings: "kern","liga";
        }
        .question-text small {
            display:block;
            font-weight:500;
            font-size:11px;
            color:#5f6368;
            margin-top:4px;
            letter-spacing:.25px;
            font-style:italic;
        }

        .answer {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
        }

        .answer-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
        }

        .answer-yes {
            background: #ffebee;
            color: #c62828;
        }

        .answer-no {
            background: #e8f5e8;
            color: #2e7d32;
        }
        .answer-neutral {
            background: #f1f5f9;
            color: #475569;
        }

        .additional-info {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 10px;
            margin-top: 8px;
            font-size: 14px;
            color: #856404;
        }

        .personal-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #ff69b4;
        }

        .info-label {
            font-weight: bold;
            color: #666;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 16px;
            color: #333;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 2px solid #e9ecef;
        }

        /* Base button style (non-box shape, consistent rounded corners) */
        .btn {
            padding: 14px 28px;
            border: 1px solid transparent;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            line-height: 1.2;
        }

        /* Ensure buttons in the actions row never appear boxy due to global overrides */
        .actions .btn { border-radius: 10px !important; }

        /* Back button: subtle neutral style */
        .btn-back {
            background: #f8fafc;
            color: #0f172a;
            border-color: #cbd5e1;
        }

        .btn-back:hover {
            background: #eef2f7;
            transform: translateY(-1px);
        }

        /* Submit button: green (success) */
        .btn-submit {
            background: #28a745;
            color: #ffffff;
            border-color: #1e7e34;
            box-shadow: 0 6px 18px rgba(40, 167, 69, 0.25);
        }

        .btn-submit:hover {
            background: #218838;
            transform: translateY(-1px);
            box-shadow: 0 10px 24px rgba(33, 136, 56, 0.30);
        }

        .warning-box {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .warning-icon {
            font-size: 24px;
            color: #856404;
        }

        .warning-text {
            color: #856404;
            line-height: 1.5;
        }

        @media (max-width: 768px) {
            .container {
                margin: 10px;
                border-radius: 15px;
            }

            .content {
                padding: 20px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📋 Review Your Health Screening</h1>
            <p>Please review all your answers carefully before submitting</p>
        </div>

        <div class="content">

            @php $hs = session('health_screening_data', []); @endphp
            @if(session('success'))
                <div id="success-message" role="alert" aria-live="assertive" style="background-color: #d4edda; color: #155724; padding: 18px; border-radius: 12px; margin-bottom: 24px; border: 2px solid #218838; font-size: 18px; font-weight: 600; box-shadow: 0 2px 8px rgba(33,136,56,0.08); position: relative;">
                    <button onclick="hideSuccessMessage()" aria-label="Close success message" style="position: absolute; top: 10px; right: 15px; background: none; border: none; font-size: 20px; color: #155724; cursor: pointer; font-weight: bold;">&times;</button>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div role="alert" aria-live="assertive" style="background-color: #f8d7da; color: #721c24; padding: 18px; border-radius: 12px; margin-bottom: 24px; border: 2px solid #dc3545; font-size: 18px; font-weight: 600; box-shadow: 0 2px 8px rgba(220,53,69,0.08);">
                    {{ session('error') }}
                </div>
            @endif

            <div class="warning-box inline-notification info">
                <div class="warning-icon">⚠️</div>
                <div class="warning-text">
                    <strong>Important:</strong> Please review all your answers carefully. Once submitted, you cannot modify your responses. If you need to make changes, click "Go Back to Edit" below.
                </div>
            </div>

            <!-- Personal Information Section -->
            <div class="review-section">
                <div class="section-header">
                    <div class="section-title">
                        👤 Personal Information
                    </div>
                </div>
                <div class="section-body">
                    <div class="personal-info">
                        <div class="info-card">
                            <div class="info-label">Civil Status</div>
                            <div class="info-value">{{ $hs['civil_status'] ?? '—' }}</div>
                        </div>
                        <div class="info-card">
                            <div class="info-label">Occupation</div>
                            <div class="info-value">{{ $hs['occupation'] ?? '—' }}</div>
                        </div>
                        <div class="info-card">
                            <div class="info-label">Type of Donor</div>
                            <div class="info-value">{{ isset($hs['type_of_donor']) ? ucfirst(str_replace('_', ' ', $hs['type_of_donor'])) : '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Medical History Section -->
            <div class="review-section">
                <div class="section-header">
                    <div class="section-title">
                        Medical History ({{ count(session('health_screening_data.medical_history', [])) }} questions)
                    </div>
                </div>
                <div class="section-body" id="medical-history-section">
                    <!-- Medical history questions will be populated by JavaScript -->
                </div>
            </div>

            <!-- Sexual History Section -->
            <div class="review-section">
                <div class="section-header">
                    <div class="section-title">
                         Sexual History ({{ count(session('health_screening_data.sexual_history', [])) }} questions)
                    </div>
                </div>
                <div class="section-body" id="sexual-history-section">
                    <!-- Sexual history questions will be populated by JavaScript -->
                </div>
            </div>

            <!-- Donor's Infant Section -->
            <div class="review-section">
                <div class="section-header">
                    <div class="section-title">
                         Donor's Infant Information ({{ count(session('health_screening_data.donor_infant', [])) }} questions)
                    </div>
                </div>
                <div class="section-body" id="donor-infant-section">
                    <!-- Donor infant questions will be populated by JavaScript -->
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons actions">
                <a href="{{ route('dashboard') }}" class="btn btn-back">
                    ← Go Back to Edit
                </a>
                <form method="POST" action="{{ route('health-screening.final-submit') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-submit">
                        ✓ Submit Health Screening
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script type="application/json" id="health-screening-data">
        {!! json_encode(session('health_screening_data', [])) !!}
    </script>

    <script>
        // Question data from models
        const medicalQuestions = [
            "Have you donated breastmilk before?",
            "Have you for any reason been deferred as a breastmilk donor?",
            "Did you have a normal pregnancy and delivery for your most recent pregnancy?",
            "Do you have any acute or chronic infection such as but not limited to: tuberculosis, hepatitis, systemic disorders?",
            "Have you been diagnosed with a chronic non-infectious illness such as but not limited to: diabetes, hypertension, heart disease?",
            "Have you received any blood transfusion or any blood products within the last twelve (12) months?",
            "Have you received any organ or tissue transplant within the last twelve (12) months?",
            "Have you had any intake of any alcohol or hard liquor within the last twenty four (24) hours?",
            "Do you use megadose vitamins or pharmacologically active herbal preparations?",
            "Do you regularly use over-the-counter medications or systemic preparations such as replacement hormones, birth control hormones, anti-hypoglycemics, blood thinners?",
            "Are you a total vegetarian/vegan?",
            "Do you use illicit drugs?",
            "Do you smoke?",
            "Are you around people who smoke (passive smoking)?",
            "Have you had breast augmentation surgery, using silicone breast implants?"
        ];

        const medicalQuestionsBisaya = [
            "Nakahatag ka na ba ug gatas sa inahan kaniadto?",
            "Aduna ka bay rason nga gidili ka isip naghatag ug gatas sa inahan?",
            "Aduna ka bay normal nga pagmabdos ug pagpanganak sa imong pinakabag-o nga pagmabdos?",
            "Aduna ka bay grabe o dugay nga impeksyon sama sa: tuberculosis, hepatitis, mga sakit sa lawas?",
            "Na-diagnose ka na ba ug dugay nga sakit nga dili makatakod sama sa: diabetes, hypertension, sakit sa kasingkasing?",
            "Nakadawat ka ba ug dugo o mga produkto sa dugo sulod sa miaging dose ka (12) bulan?",
            "Nakadawat ka ba ug organ o tissue transplant sulod sa miaging dose ka (12) bulan?",
            "Nag-inom ka ba ug alkohol o lig-on nga ilimnon sulod sa miaging kawhaan ug upat (24) ka oras?",
            "Naggamit ka ba ug daghan kaayo nga bitamina o mga herbal nga tambal?",
            "Kanunay ka bang naggamit ug mga tambal nga walay reseta o mga sistemang tambal sama sa replacement hormones, birth control hormones, anti-hypoglycemics, blood thinners?",
            "Vegetarian/vegan ka ba nga kompleto?",
            "Naggamit ka ba ug mga dili legal nga droga?",
            "Nagsigarilyo ka ba?",
            "Naa ka ba sa palibot sa mga tawo nga nagsigarilyo (passive smoking)?",
            "Nakaoperasyon ka na ba sa dughan gamit ang silicone breast implants?"
        ];

        // Get session data from JSON script tag
        const sessionData = JSON.parse(document.getElementById('health-screening-data').textContent);

        // Normalize raw answer values into badge class and display text
        function normalizeAnswer(raw) {
            const val = (raw || '').toString().toLowerCase().trim();
            if (val === 'yes' || val === 'y' || val === 'true') return { class: 'answer-yes', text: 'YES' };
            if (val === 'no' || val === 'n' || val === 'false') return { class: 'answer-no', text: 'NO' };
            if (val === 'not answered' || val === '' || val === 'null' || val === 'undefined') return { class: 'answer-neutral', text: 'Not answered' };
            // For checkbox aggregated answers like 'yes' but with details, treat as yes
            if (val.includes('yes') || val.includes('no')) return { class: val.includes('yes') ? 'answer-yes' : 'answer-no', text: raw.toString().toUpperCase() };
            // Default fallback
            return { class: 'answer-neutral', text: raw.toString() };
        }

        // Populate sections
        document.addEventListener('DOMContentLoaded', function() {
            populateMedicalHistory();
            populateSexualHistory();
            populateDonorInfant();

            // Auto-hide success message after 5 seconds
            const successMessage = document.getElementById('success-message');
            if (successMessage) {
                setTimeout(function() {
                    hideSuccessMessage();
                }, 5000); // 5 seconds
            }

            // Add form submission validation
            const submitForm = document.querySelector('form[action*="final-submit"]');
            if (submitForm) {
                submitForm.addEventListener('submit', function(e) {
                    const missingFields = validateHealthScreeningData();
                    if (missingFields.length > 0) {
                        e.preventDefault();
                        showIncompletePopup(missingFields);
                        return false;
                    }
                });
            }
        });

        // Function to hide success message with animation
        function hideSuccessMessage() {
            const successMessage = document.getElementById('success-message');
            if (successMessage) {
                successMessage.style.opacity = '0';
                successMessage.style.transform = 'translateY(-20px)';
                setTimeout(function() {
                    successMessage.style.display = 'none';
                }, 500); // Wait for animation to complete
            }
        }

        function populateMedicalHistory() {
            const container = document.getElementById('medical-history-section');
            const medicalHistory = sessionData.medical_history || {};
            
            medicalQuestions.forEach((question, index) => {
                const questionNum = index + 1;
                let answer = (medicalHistory[`mhq_${questionNum}`] || 'Not answered').toString();
                const normalized = normalizeAnswer(answer);
                const additionalInfo = getAdditionalInfo('medical', questionNum, medicalHistory);
                
                const questionHtml = `
                    <div class="question-item">
                        <p class="question-text">${question}<small>${medicalQuestionsBisaya[index] || ''}</small></p>
                        <div class="answer">
                            <span class="answer-badge ${normalized.class}">${normalized.text}</span>
                        </div>
                        ${additionalInfo ? `<div class="additional-info"><strong>Additional Info:</strong> ${additionalInfo}</div>` : ''}
                    </div>
                `;
                container.innerHTML += questionHtml;
            });
        }

        function populateSexualHistory() {
            const container = document.getElementById('sexual-history-section');
            const sexualHistory = sessionData.sexual_history || {};
            
            const sexualQuestions = [
                "Have you ever had syphilis, HIV, herpes or any sexually transmitted disease (STD)?",
                "Do you have multiple sexual partners?",
                "Have you had a sexual partner who is: Bisexual, Promiscuous, Has had an STD/AIDS/HIV, Received blood for bleeding problems, or Is an intravenous drug user?",
                "Have you had a tattoo applied or had an accidental needlestick injury or contact with someone else's blood?"
            ];

            const sexualQuestionsBisaya = [
                "Nakaangkon ka na ba ug syphilis, HIV, herpes o bisan unsang sakit nga makuha pinaagi sa pakighilawas (STD)?",
                "Aduna ka bay daghang kauban sa pakighilawas?",
                "Aduna ka bay kauban sa pakighilawas nga: Bisexual, Promiscuous, Adunay STD/AIDS/HIV, Nakadawat ug dugo tungod sa pagdugo, o Naggamit ug droga pinaagi sa injection?",
                "Nakapatattoo ka na ba o nakaangkon ug aksidenteng pagkatusok sa injection o nakahikap sa dugo sa uban?"
            ];

            sexualQuestions.forEach((question, index) => {
                const questionNum = index + 1;
                let answer = (sexualHistory[`shq_${questionNum}`] || 'Not answered').toString();
                
                if (questionNum === 3) {
                    // Handle checkbox group for question 3
                    const checkboxes = ['shq_3_bisexual', 'shq_3_promiscuous', 'shq_3_std', 'shq_3_blood', 'shq_3_drugs'];
                    const selectedOptions = checkboxes.filter(cb => sexualHistory[cb]).map(cb => cb.replace('shq_3_', ''));
                    answer = selectedOptions.length > 0 ? 'yes' : 'no';
                } else {
                    answer = sexualHistory[`shq_${questionNum}`] || 'Not answered';
                }
                
                const normalized = normalizeAnswer(answer);
                    const questionHtml = `
                    <div class="question-item">
                        <p class="question-text">${question}<small>${sexualQuestionsBisaya[index] || ''}</small></p>
                        <div class="answer">
                            <span class="answer-badge ${normalized.class}">${normalized.text}</span>
                        </div>
                    </div>
                `;
                container.innerHTML += questionHtml;
            });
        }

        function populateDonorInfant() {
            const container = document.getElementById('donor-infant-section');
            const donorInfant = sessionData.donor_infant || {};
            
            const infantQuestions = [
                "Is your child healthy?",
                "Was your child delivered full term?",
                "Are you exclusively breastfeeding your child?",
                "Is/was your youngest child jaundiced?",
                "Has your child ever received milk from another mother?"
            ];

            const infantQuestionsBisaya = [
                "Himsog ba ang imong anak?",
                "Natawo ba ang imong anak sa hustong panahon (full term)?",
                "Gatas sa inahan ra ba ang imong gihatag sa imong anak (exclusively breastfeeding)?",
                "Nangitag ba o nangitag na ba ang imong pinakagamay nga anak?",
                "Nakadawat na ba ang imong anak ug gatas gikan sa laing inahan?"
            ];

            infantQuestions.forEach((question, index) => {
                const questionNum = index + 1;
                let answer = (donorInfant[`diq_${questionNum}`] || 'Not answered').toString();
                const normalized = normalizeAnswer(answer);
                const additionalInfo = getAdditionalInfo('donor_infant', questionNum, donorInfant);
                
                const questionHtml = `
                    <div class="question-item">
                        <p class="question-text">${question}<small>${infantQuestionsBisaya[index] || ''}</small></p>
                        <div class="answer">
                            <span class="answer-badge ${normalized.class}">${normalized.text}</span>
                        </div>
                        ${additionalInfo ? `<div class="additional-info"><strong>Additional Info:</strong> ${additionalInfo}</div>` : ''}
                    </div>
                `;
                container.innerHTML += questionHtml;
            });
        }

        function getAdditionalInfo(section, questionNum, data) {
            if (section === 'medical') {
                const fieldMap = {
                    2: 'mhq_2_reason',
                    4: 'mhq_4_reason',
                    5: 'mhq_5_reason',
                    8: 'mhq_8_amount',
                    10: 'mhq_10_reason',
                    11: 'mhq_11_supplement',
                    13: 'mhq_13_amount'
                };
                return data[fieldMap[questionNum]] || null;
            } else if (section === 'donor_infant') {
                const fieldMap = {
                    4: 'diq_4_reason',
                    5: 'diq_5_reason'
                };
                return data[fieldMap[questionNum]] || null;
            }
            return null;
        }

        // Validate health screening data completeness
        function validateHealthScreeningData() {
            const data = JSON.parse(document.getElementById('health-screening-data').textContent);
            const missingFields = [];

            // Check basic information
            if (!data.civil_status) missingFields.push('Civil Status');
            if (!data.occupation) missingFields.push('Occupation');
            if (!data.type_of_donor) missingFields.push('Type of Donor');

            // Check medical history questions
            const medicalHistory = data.medical_history || {};
            for (let i = 1; i <= 15; i++) {
                const fieldName = `mhq_${i}`;
                if (!medicalHistory[fieldName]) {
                    missingFields.push(`Medical History Question ${i}`);
                }

                // Check additional fields for specific questions
                if (i === 2 && medicalHistory[fieldName] === 'yes' && !medicalHistory.mhq_2_reason) {
                    missingFields.push('Medical History Q2 - Reason');
                }
                if (i === 4 && medicalHistory[fieldName] === 'yes' && !medicalHistory.mhq_4_reason) {
                    missingFields.push('Medical History Q4 - Specific disease(s)');
                }
                if (i === 5 && medicalHistory[fieldName] === 'yes' && !medicalHistory.mhq_5_reason) {
                    missingFields.push('Medical History Q5 - Specific disease(s)');
                }
                if (i === 8 && medicalHistory[fieldName] === 'yes' && !medicalHistory.mhq_8_amount) {
                    missingFields.push('Medical History Q8 - Amount');
                }
                if (i === 10 && medicalHistory[fieldName] === 'yes' && !medicalHistory.mhq_10_reason) {
                    missingFields.push('Medical History Q10 - Medication(s)');
                }
                if (i === 11 && medicalHistory[fieldName] === 'yes' && !medicalHistory.mhq_11_supplement) {
                    missingFields.push('Medical History Q11 - Supplement with vitamins');
                }
                if (i === 13 && medicalHistory[fieldName] === 'yes' && !medicalHistory.mhq_13_amount) {
                    missingFields.push('Medical History Q13 - Sticks/packs per day');
                }
            }

            // Check sexual history questions
            const sexualHistory = data.sexual_history || {};
            if (!sexualHistory.shq_1) missingFields.push('Sexual History Question 1');
            if (!sexualHistory.shq_2) missingFields.push('Sexual History Question 2');
            if (!sexualHistory.shq_4) missingFields.push('Sexual History Question 4');

            // Check donor infant questions
            const donorInfant = data.donor_infant || {};
            for (let i = 1; i <= 5; i++) {
                const fieldName = `diq_${i}`;
                if (!donorInfant[fieldName]) {
                    missingFields.push(`Donor's Infant Question ${i}`);
                }

                // Check additional fields for specific questions
                if (i === 4 && donorInfant[fieldName] === 'yes' && !donorInfant.diq_4_reason) {
                    missingFields.push("Donor's Infant Q4 - Details");
                }
                if (i === 5 && donorInfant[fieldName] === 'yes' && !donorInfant.diq_5_reason) {
                    missingFields.push("Donor's Infant Q5 - When did this happen");
                }
            }

            return missingFields;
        }

        // Show incomplete submission popup
        function showIncompletePopup(missingFields) {
            const modal = document.createElement('div');
            modal.id = 'incomplete-modal';
            modal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.8);
                backdrop-filter: blur(8px);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 10000;
                animation: modalFadeIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            `;

            const modalContent = document.createElement('div');
            modalContent.style.cssText = `
                background: linear-gradient(145deg, #ffffff 0%, #fefefe 50%, #f8f9fa 100%);
                border-radius: 24px;
                padding: 0;
                max-width: 600px;
                width: 95%;
                max-height: 90vh;
                overflow: hidden;
                box-shadow:
                    0 25px 80px rgba(255, 105, 180, 0.3),
                    0 0 0 1px rgba(255, 255, 255, 0.1),
                    inset 0 1px 0 rgba(255, 255, 255, 0.8);
                border: 3px solid transparent;
                background-clip: padding-box;
                animation: modalSlideIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
                position: relative;
            `;

            // Add gradient border effect
            modalContent.style.backgroundImage += `,
                linear-gradient(135deg,
                    #ff69b4 0%,
                    #ff1493 25%,
                    #dc143c 50%,
                    #ff69b4 75%,
                    #ff1493 100%
                )`;

            const header = document.createElement('div');
            header.style.cssText = `
                background: linear-gradient(135deg,
                    #ff69b4 0%,
                    #ff1493 30%,
                    #dc143c 70%,
                    #c2185b 100%
                );
                color: white;
                padding: 30px 35px;
                text-align: center;
                position: relative;
                overflow: hidden;
            `;

            // Add header pattern
            header.style.backgroundImage += `,
                radial-gradient(circle at 20% 80%, rgba(255,255,255,0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255,255,255,0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(255,255,255,0.05) 0%, transparent 50%)`;

            const closeBtn = document.createElement('button');
            closeBtn.innerHTML = '✕';
            closeBtn.style.cssText = `
                position: absolute;
                top: 20px;
                right: 25px;
                background: rgba(255,255,255,0.2);
                border: 2px solid rgba(255,255,255,0.3);
                font-size: 16px;
                cursor: pointer;
                color: white;
                width: 35px;
                height: 35px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                font-weight: bold;
                backdrop-filter: blur(10px);
            `;
            closeBtn.onmouseover = () => {
                closeBtn.style.background = 'rgba(255,255,255,0.3)';
                closeBtn.style.transform = 'scale(1.1) rotate(90deg)';
                closeBtn.style.boxShadow = '0 4px 20px rgba(255,255,255,0.3)';
            };
            closeBtn.onmouseout = () => {
                closeBtn.style.background = 'rgba(255,255,255,0.2)';
                closeBtn.style.transform = 'scale(1) rotate(0deg)';
                closeBtn.style.boxShadow = 'none';
            };
            closeBtn.onclick = () => {
                modal.style.animation = 'modalFadeOut 0.3s ease-out';
                modalContent.style.animation = 'modalSlideOut 0.3s ease-out';
                setTimeout(() => modal.remove(), 300);
            };

            const iconContainer = document.createElement('div');
            iconContainer.style.cssText = `
                position: relative;
                margin-bottom: 20px;
            `;

            const icon = document.createElement('div');
            icon.textContent = '🚨';
            icon.style.cssText = `
                font-size: 56px;
                filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));
                animation: iconPulse 2s ease-in-out infinite;
                display: inline-block;
            `;

            const glow = document.createElement('div');
            glow.style.cssText = `
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 80px;
                height: 80px;
                background: radial-gradient(circle, rgba(255,105,180,0.3) 0%, transparent 70%);
                border-radius: 50%;
                animation: glowPulse 2s ease-in-out infinite;
                pointer-events: none;
            `;

            const title = document.createElement('h2');
            title.textContent = 'Health Screening Incomplete';
            title.style.cssText = `
                margin: 0;
                font-size: 28px;
                font-weight: 800;
                text-shadow: 0 2px 4px rgba(0,0,0,0.2);
                letter-spacing: -0.5px;
                background: linear-gradient(135deg, #ffffff 0%, #f0f0f0 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            `;

            const subtitle = document.createElement('p');
            const totalMissing = missingFields.length;
            subtitle.textContent = totalMissing > 0 ? `Please complete all required fields (${totalMissing} item${totalMissing === 1 ? '' : 's'} missing)` : 'Please complete all required fields';
            subtitle.style.cssText = `
                margin: 8px 0 0 0;
                font-size: 16px;
                opacity: 0.9;
                font-weight: 400;
                text-shadow: 0 1px 2px rgba(0,0,0,0.1);
            `;

            const body = document.createElement('div');
            body.style.cssText = `
                padding: 35px;
                background: linear-gradient(180deg, #ffffff 0%, #fafafa 100%);
            `;

            const message = document.createElement('div');
            message.style.cssText = `
                background: linear-gradient(135deg, #fff8f8 0%, #fef2f2 100%);
                border: 2px solid #fee2e2;
                border-radius: 16px;
                padding: 20px 25px;
                margin-bottom: 30px;
                text-align: center;
                box-shadow: 0 4px 12px rgba(255, 105, 180, 0.1);
                position: relative;
                overflow: hidden;
            `;

            const messageIcon = document.createElement('div');
            messageIcon.textContent = '💡';
            messageIcon.style.cssText = `
                font-size: 24px;
                margin-bottom: 10px;
            `;

            const messageText = document.createElement('p');
            messageText.textContent = 'Your health screening form is missing some important information. Complete all fields to ensure accurate health assessment.';
            messageText.style.cssText = `
                color: #7f1d1d;
                margin: 0;
                line-height: 1.6;
                font-size: 15px;
                font-weight: 500;
            `;

            // Add decorative elements to message
            message.style.backgroundImage += `,
                radial-gradient(circle at 10% 20%, rgba(255,105,180,0.05) 0%, transparent 50%),
                radial-gradient(circle at 90% 80%, rgba(255,20,147,0.05) 0%, transparent 50%)`;

            const listTitle = document.createElement('h3');
            listTitle.style.cssText = `
                color: #1f2937;
                margin-bottom: 16px;
                font-size: 20px;
                font-weight: 700;
                display: flex;
                align-items: center;
                gap: 10px;
                padding-bottom: 10px;
                border-bottom: 3px solid #ff69b4;
            `;

            const listIcon = document.createElement('span');
            listIcon.textContent = '📋';
            listIcon.style.cssText = `
                font-size: 24px;
                filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
            `;

            const listText = document.createElement('span');
            listText.textContent = 'Missing Information by Section';
            listTitle.appendChild(listIcon);
            listTitle.appendChild(listText);


            // Group missing fields by section and render cards with direct edit links
            const groups = { 'Personal Information': [], 'Medical History': [], 'Sexual History': [], "Donor's Infant Information": [] };
            missingFields.forEach((f) => {
                if (f.startsWith('Medical History')) groups['Medical History'].push(f);
                else if (f.startsWith('Sexual History')) groups['Sexual History'].push(f);
                else if (f.startsWith("Donor's Infant")) groups["Donor's Infant Information"].push(f);
                else groups['Personal Information'].push(f);
            });

            const sectionContainer = document.createElement('div');
            sectionContainer.style.cssText = 'display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:16px;margin-bottom:20px;';
            Object.entries(groups).forEach(([section, items], sectionIndex) => {
                if (items.length === 0) return;
                const card = document.createElement('div');
                card.style.cssText = `background:linear-gradient(145deg,#fefefe 0%,#f9f9f9 100%);border:2px solid #e5e7eb;border-radius:16px;padding:16px;box-shadow:0 4px 16px rgba(0,0,0,0.08),inset 0 1px 0 rgba(255,255,255,0.8);animation:itemSlideIn 0.4s cubic-bezier(0.34,1.56,0.64,1) ${sectionIndex * 0.06}s both;`;

                const headerRow = document.createElement('div'); headerRow.style.cssText = 'display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;';
                const sectionTitle = document.createElement('div'); sectionTitle.textContent = section; sectionTitle.style.cssText = 'font-weight:700;color:#111827;';
                const countBadge = document.createElement('span'); countBadge.textContent = items.length + ' missing'; countBadge.style.cssText = 'background:#fee2e2;color:#991b1b;border:1px solid #fecaca;padding:4px 8px;border-radius:9999px;font-size:12px;font-weight:700;';
                headerRow.appendChild(sectionTitle); headerRow.appendChild(countBadge); card.appendChild(headerRow);

                const ul = document.createElement('ul'); ul.style.cssText = 'margin:0;padding-left:16px;max-height:180px;overflow:auto;';
                items.forEach((field) => {
                    const li = document.createElement('li');
                    li.textContent = field;
                    li.style.cssText = 'color:#dc2626;margin-bottom:8px;font-weight:600;font-size:14px;';
                    // Add direct edit link for each missing field
                    const editLink = document.createElement('a');
                    editLink.textContent = 'Edit';
                    editLink.href = '{{ route("dashboard") }}';
                    editLink.setAttribute('aria-label', 'Edit ' + field);
                    editLink.style.cssText = 'margin-left:10px;color:#2563eb;text-decoration:underline;font-size:13px;font-weight:500;';
                    li.appendChild(editLink);
                    ul.appendChild(li);
                });
                card.appendChild(ul);

                sectionContainer.appendChild(card);
            });

            const buttonContainer = document.createElement('div');
            buttonContainer.style.cssText = `
                display: flex;
                gap: 20px;
                justify-content: center;
                flex-wrap: wrap;
                padding-top: 10px;
            `;

            const backBtn = document.createElement('button');
            backBtn.innerHTML = '<span style="font-size: 18px;">←</span> Go Back to Edit';
            backBtn.style.cssText = `
                background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
                color: white;
                border: none;
                padding: 16px 32px;
                border-radius: 14px;
                cursor: pointer;
                font-size: 16px;
                font-weight: 700;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow:
                    0 6px 20px rgba(16, 185, 129, 0.3),
                    0 2px 8px rgba(16, 185, 129, 0.2);
                display: flex;
                align-items: center;
                gap: 10px;
                position: relative;
                overflow: hidden;
            `;

            backBtn.onmouseover = () => {
                backBtn.style.transform = 'translateY(-3px) scale(1.02)';
                backBtn.style.boxShadow = `
                    0 12px 32px rgba(16, 185, 129, 0.4),
                    0 4px 16px rgba(16, 185, 129, 0.3)`;
            };
            backBtn.onmouseout = () => {
                backBtn.style.transform = 'translateY(0) scale(1)';
                backBtn.style.boxShadow = `
                    0 6px 20px rgba(16, 185, 129, 0.3),
                    0 2px 8px rgba(16, 185, 129, 0.2)`;
            };
            backBtn.onclick = () => {
                modal.style.animation = 'modalFadeOut 0.3s ease-out';
                modalContent.style.animation = 'modalSlideOut 0.3s ease-out';
                setTimeout(() => {
                    modal.remove();
                    window.location.href = '{{ route("dashboard") }}';
                }, 300);
            };

            const closeBtn2 = document.createElement('button');
            closeBtn2.innerHTML = '<span style="font-size: 16px;">✕</span> Close';
            closeBtn2.style.cssText = `
                background: linear-gradient(135deg, #ef4444 0%, #dc2626 50%, #b91c1c 100%);
                color: white;
                border: none;
                padding: 16px 32px;
                border-radius: 14px;
                cursor: pointer;
                font-size: 16px;
                font-weight: 700;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow:
                    0 6px 20px rgba(239, 68, 68, 0.3),
                    0 2px 8px rgba(239, 68, 68, 0.2);
                display: flex;
                align-items: center;
                gap: 10px;
                position: relative;
                overflow: hidden;
            `;

            closeBtn2.onmouseover = () => {
                closeBtn2.style.transform = 'translateY(-3px) scale(1.02)';
                closeBtn2.style.boxShadow = `
                    0 12px 32px rgba(239, 68, 68, 0.4),
                    0 4px 16px rgba(239, 68, 68, 0.3)`;
            };
            closeBtn2.onmouseout = () => {
                closeBtn2.style.transform = 'translateY(0) scale(1)';
                closeBtn2.style.boxShadow = `
                    0 6px 20px rgba(239, 68, 68, 0.3),
                    0 2px 8px rgba(239, 68, 68, 0.2)`;
            };
            closeBtn2.onclick = () => {
                modal.style.animation = 'modalFadeOut 0.3s ease-out';
                modalContent.style.animation = 'modalSlideOut 0.3s ease-out';
                setTimeout(() => modal.remove(), 300);
            };

            // Add CSS animations
            const style = document.createElement('style');
            style.textContent = `
                @keyframes modalFadeIn {
                    from {
                        opacity: 0;
                        backdrop-filter: blur(0px);
                    }
                    to {
                        opacity: 1;
                        backdrop-filter: blur(8px);
                    }
                }
                @keyframes modalSlideIn {
                    from {
                        opacity: 0;
                        transform: translateY(-60px) scale(0.85) rotate(-2deg);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0) scale(1) rotate(0deg);
                    }
                }
                @keyframes modalFadeOut {
                    from { opacity: 1; }
                    to { opacity: 0; }
                }
                @keyframes modalSlideOut {
                    from {
                        opacity: 1;
                        transform: translateY(0) scale(1);
                    }
                    to {
                        opacity: 0;
                        transform: translateY(-60px) scale(0.85);
                    }
                }
                @keyframes iconPulse {
                    0%, 100% {
                        transform: scale(1);
                        filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));
                    }
                    50% {
                        transform: scale(1.1);
                        filter: drop-shadow(0 6px 12px rgba(255,105,180,0.3));
                    }
                }
                @keyframes glowPulse {
                    0%, 100% {
                        opacity: 0.3;
                        transform: translate(-50%, -50%) scale(1);
                    }
                    50% {
                        opacity: 0.6;
                        transform: translate(-50%, -50%) scale(1.2);
                    }
                }
                @keyframes itemSlideIn {
                    from {
                        opacity: 0;
                        transform: translateX(-30px) scale(0.9);
                    }
                    to {
                        opacity: 1;
                        transform: translateX(0) scale(1);
                    }
                }
            `;
            document.head.appendChild(style);

            buttonContainer.appendChild(backBtn);
            buttonContainer.appendChild(closeBtn2);

            iconContainer.appendChild(glow);
            iconContainer.appendChild(icon);

            header.appendChild(closeBtn);
            header.appendChild(iconContainer);
            header.appendChild(title);
            header.appendChild(subtitle);

            message.appendChild(messageIcon);
            message.appendChild(messageText);

            body.appendChild(message);
            body.appendChild(listTitle);
            body.appendChild(sectionContainer);
            body.appendChild(buttonContainer);

            modalContent.appendChild(header);
            modalContent.appendChild(body);
            modal.appendChild(modalContent);

            document.body.appendChild(modal);
        }
    </script>
</body>
</html>
