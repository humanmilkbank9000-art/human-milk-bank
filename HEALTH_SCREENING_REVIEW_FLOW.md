# Health Screening Review Flow

## Overview
The health screening process now includes a review step that allows users to preview all their answers before final submission. This prevents mistakes and gives users confidence in their responses.

## Flow Diagram
```
User Dashboard → Health Screening Form → Review Page → Final Submission → Dashboard
```

## Step-by-Step Process

### 1. **User Fills Health Screening Form**
- User accesses the health screening form from their dashboard
- Form includes:
  - Personal Information (civil status, occupation, donor type)
  - Medical History (15 questions with Bisaya translations)
  - Sexual History (4 questions with Bisaya translations)  
  - Donor's Infant Information (5 questions with Bisaya translations)

### 2. **Form Submission → Review Page**
- When user clicks "Submit", form data is validated
- All answers are stored temporarily in session
- User is redirected to review page (`/health-screening/review`)

### 3. **Review Page Features**
- **Beautiful Design**: Clean, professional layout with smooth styling
- **Organized Sections**: Personal info, medical history, sexual history, donor infant
- **Bilingual Display**: Questions shown in both English and Bisaya (italics)
- **Answer Highlighting**: Clear YES/NO badges with color coding
- **Additional Info**: Shows follow-up answers when applicable
- **Warning Message**: Reminds users that answers cannot be changed after submission

### 4. **User Options on Review Page**
- **Go Back to Edit**: Returns to dashboard to modify answers
- **Submit Health Screening**: Proceeds with final submission

### 5. **Final Submission**
- Data is retrieved from session and saved to database
- Creates health screening record and all answer records
- Session data is cleared
- User redirected to dashboard with success message
- Admin notification is created

## Technical Implementation

### Routes
```php
POST /health-screening/submit          → Store in session, redirect to review
GET  /health-screening/review          → Show review page
POST /health-screening/final-submit    → Save to database, clear session
```

### Controller Methods
- `submit()` - Validates and stores data in session
- `showReview()` - Displays review page with session data
- `finalSubmit()` - Saves session data to database

### Session Storage
Data is temporarily stored in `health_screening_data` session key with structure:
```php
[
    'civil_status' => 'Single',
    'occupation' => 'Teacher',
    'type_of_donor' => 'community',
    'medical_history' => [
        'mhq_1' => 'yes',
        'mhq_2_reason' => 'Additional info...',
        // ... all medical questions
    ],
    'sexual_history' => [
        'shq_1' => 'no',
        'shq_3_bisexual' => true,
        // ... all sexual history questions
    ],
    'donor_infant' => [
        'diq_1' => 'yes',
        'diq_4_reason' => 'Additional info...',
        // ... all donor infant questions
    ]
]
```

## Benefits

### For Users
- **Confidence**: Review all answers before submission
- **Error Prevention**: Catch mistakes before final submission
- **Transparency**: Clear view of what will be submitted
- **Bilingual Support**: Questions in both English and Bisaya

### For Administrators
- **Data Quality**: Fewer submission errors
- **User Satisfaction**: Users feel more confident about their submissions
- **Professional Experience**: Modern, user-friendly interface

## Security Features
- **Session-based**: Data stored securely in server session
- **Authentication Check**: User must be logged in
- **Data Validation**: All data validated before storage
- **Session Cleanup**: Data cleared after successful submission

## Error Handling
- **Missing Session Data**: Redirects to dashboard with error message
- **Authentication Issues**: Redirects to login
- **Validation Errors**: Shows appropriate error messages
- **Database Errors**: Graceful error handling with user-friendly messages

## Styling Features
- **Responsive Design**: Works on all device sizes
- **Smooth Animations**: Professional transitions and effects
- **Color-coded Answers**: YES (red), NO (green) for easy scanning
- **Clear Typography**: Easy to read with proper hierarchy
- **Warning Indicators**: Important notices highlighted
- **Action Buttons**: Clear call-to-action buttons

## Future Enhancements
- **Print Preview**: Allow users to print their review
- **Email Copy**: Send review copy to user's email
- **Save Draft**: Allow users to save incomplete forms
- **Edit Individual Sections**: Allow editing specific sections from review page
