<?php
/**
 * Test script to verify the calendar matching system
 * Run this script to add sample availability data for testing
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Calendar Matching System...\n\n";

try {
    // Clear existing test data
    DB::table('admin_time_slots')->where('date', '>=', date('Y-m-d'))->delete();
    DB::table('admin_week_availability')->where('date', '>=', date('Y-m-d'))->delete();
    
    echo "✓ Cleared existing test data\n";
    
    // Add sample availability for the next 7 days
    $dates = [];
    for ($i = 0; $i < 7; $i++) {
        $date = date('Y-m-d', strtotime("+$i days"));
        $dates[] = $date;
        
        // Make every other day available
        $isAvailable = ($i % 2 == 0);
        
        if ($isAvailable) {
            // Add day availability
            DB::table('admin_week_availability')->insert([
                'date' => $date,
                'is_available' => true,
                'start_time' => '08:00',
                'end_time' => '17:00',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Add sample time slots (9 AM, 11 AM, 1 PM, 3 PM)
            $timeSlots = ['09:00', '11:00', '13:00', '15:00'];
            foreach ($timeSlots as $timeSlot) {
                DB::table('admin_time_slots')->insert([
                    'date' => $date,
                    'time_slot' => $timeSlot,
                    'is_available' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            echo "✓ Added availability for $date with " . count($timeSlots) . " time slots\n";
        } else {
            echo "- Skipped $date (not available)\n";
        }
    }
    
    echo "\n📊 Test Data Summary:\n";
    echo "- Available dates: " . DB::table('admin_week_availability')->where('is_available', true)->count() . "\n";
    echo "- Total time slots: " . DB::table('admin_time_slots')->where('is_available', true)->count() . "\n";
    
    echo "\n🎯 Test Instructions:\n";
    echo "1. Go to http://127.0.0.1:8000/admin/pin (PIN: 1234)\n";
    echo "2. View the calendar - available days should be highlighted in pink\n";
    echo "3. Click on an available date to modify time slots\n";
    echo "4. Go to http://127.0.0.1:8000 and login as a user\n";
    echo "5. Navigate to Donate > Walk-in Donation\n";
    echo "6. Available days should be highlighted in pink\n";
    echo "7. Click on a pink date to see available time slots\n";
    echo "8. Select a time slot and book an appointment\n";
    
    echo "\n✅ Calendar system test data created successfully!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
