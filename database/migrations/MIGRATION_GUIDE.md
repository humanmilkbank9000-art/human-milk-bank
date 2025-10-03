# Health Screening Migration Guide

## Overview
This guide helps you migrate from the scattered health screening migrations to the new unified system while maintaining all existing functionality.

## Current System Analysis

### ✅ **Currently Used Tables (Keep These)**
- `health_screenings` - Main table ✓
- `medical_history_answers` - Flexible answer storage ✓
- `sexual_history_answers` - Flexible answer storage ✓
- `donor_infant_answers` - Flexible answer storage ✓
- `notifications` - Notification system ✓

### ❌ **Redundant Tables (Can Be Removed)**
- `health_screening_medical_history` - Has Answer_1 through Answer_15 columns
- `health_screening_sexual_history` - Has Answer_1 through Answer_4 columns  
- `health_screening_donors_infant` - Has Answer_1 through Answer_5 columns
- `medical_history_questions` - Static question storage (questions are now in models)
- `sexual_history_questions` - Static question storage (questions are now in models)
- `donors_infant_questions` - Static question storage (questions are now in models)

## Migration Steps

### Step 1: Backup Your Database
```bash
php artisan db:backup  # or your preferred backup method
```

### Step 2: Check Current Data
Run this query to see if you have data in the redundant tables:
```sql
SELECT COUNT(*) FROM health_screening_medical_history;
SELECT COUNT(*) FROM health_screening_sexual_history;
SELECT COUNT(*) FROM health_screening_donors_infant;
SELECT COUNT(*) FROM medical_history_questions;
SELECT COUNT(*) FROM sexual_history_questions;
SELECT COUNT(*) FROM donors_infant_questions;
```

### Step 3: Data Migration (If Needed)
If you have data in the redundant tables, you'll need to migrate it to the answer tables first.

### Step 4: Remove Old Migrations
After confirming your data is safe, you can remove these migration files:
- `2025_08_08_034118_create_medical_history_questions_table.php`
- `2025_08_08_034141_create_sexual_history_questions_table.php`
- `2025_08_08_034151_create_donors_infant_questions_table.php`
- `2025_08_08_034423_create_health_screening_sexual_history_table.php`
- `2025_08_08_034434_create_health_screening_donors_infant_table.php`
- `2025_08_08_034508_create_health_screening_medical_history_table.php`

### Step 5: Use the New Unified Migration
The new migration `2025_08_27_000000_create_unified_health_screening_system.php` contains everything you need.

## Benefits of the New System

1. **Single Source of Truth**: All health screening tables in one migration
2. **Better Performance**: Proper indexing and relationships
3. **Cleaner Code**: Eloquent models with relationships
4. **Bilingual Support**: Questions and translations built into models
5. **Maintainable**: Easier to modify and extend

## New Models Available

- `HealthScreening` - Main model with relationships
- `MedicalHistoryAnswer` - With question text and Bisaya translations
- `SexualHistoryAnswer` - With question text and Bisaya translations  
- `DonorInfantAnswer` - With question text and Bisaya translations

## Usage Examples

```php
// Get a health screening with all related data
$screening = HealthScreening::with([
    'user',
    'medicalHistoryAnswers',
    'sexualHistoryAnswers',
    'donorInfantAnswers'
])->find($id);

// Get question text and Bisaya translation
$answer = MedicalHistoryAnswer::find(1);
echo $answer->question_text;  // English question
echo $answer->question_bisaya;  // Bisaya translation

// Create new answers
MedicalHistoryAnswer::create([
    'health_screening_id' => $screeningId,
    'question_number' => 1,
    'answer' => 'yes',
    'additional_info' => 'Some details'
]);
```

## Verification

After migration, verify that:
1. All health screening functionality still works
2. Admin dashboard displays questions correctly
3. Bisaya translations appear in italics
4. All relationships work properly

## Rollback Plan

If you need to rollback:
1. Restore your database backup
2. Revert the controller changes
3. Keep using the old system until issues are resolved
