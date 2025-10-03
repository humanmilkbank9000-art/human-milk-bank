<?php

declare(strict_types=1);

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

/**
 * Test Data Seeder for Admin Availability
 *
 * This script creates test availability data for the next 10 days
 * to ensure the system has data for testing purposes.
 */
class TestDataSeeder
{
    /**
     * Number of days to generate test data for.
     */
    private const TEST_DAYS = 10;

    /**
     * Default working hours for testing.
     */
    private const DEFAULT_START_TIME = '08:00';
    private const DEFAULT_END_TIME = '17:00';

    /**
     * Test time slots.
     */
    private const TIME_SLOTS = ['09:00', '11:00', '13:00', '15:00'];

    /**
     * Table names.
     */
    private const ADMIN_TIME_SLOTS_TABLE = 'admin_time_slots';
    private const ADMIN_WEEK_AVAILABILITY_TABLE = 'admin_week_availability';

    /**
     * Bootstrap the Laravel application.
     */
    private function bootstrap(): void
    {
        $app = require_once 'bootstrap/app.php';
        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    }

    /**
     * Clear existing test data from today onwards.
     */
    private function clearExistingTestData(): void
    {
        $today = date('Y-m-d');
        
        DB::table(self::ADMIN_TIME_SLOTS_TABLE)
            ->where('date', '>=', $today)
            ->delete();
            
        DB::table(self::ADMIN_WEEK_AVAILABILITY_TABLE)
            ->where('date', '>=', $today)
            ->delete();
    }

    /**
     * Generate test availability for a specific date.
     */
    private function generateTestAvailabilityForDate(string $date): void
    {
        $this->addTestDayAvailability($date);
        $this->addTestTimeSlots($date);
        echo "Added availability for {$date}\n";
    }

    /**
     * Add test day availability record.
     */
    private function addTestDayAvailability(string $date): void
    {
        DB::table(self::ADMIN_WEEK_AVAILABILITY_TABLE)->insert([
            'date' => $date,
            'is_available' => true,
            'start_time' => self::DEFAULT_START_TIME,
            'end_time' => self::DEFAULT_END_TIME,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Add test time slots for a specific date.
     */
    private function addTestTimeSlots(string $date): void
    {
        foreach (self::TIME_SLOTS as $timeSlot) {
            DB::table(self::ADMIN_TIME_SLOTS_TABLE)->insert([
                'date' => $date,
                'time_slot' => $timeSlot,
                'is_available' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Display test data summary.
     */
    private function displayTestSummary(): void
    {
        echo "Test data created successfully!\n";
        
        $availableDates = DB::table(self::ADMIN_WEEK_AVAILABILITY_TABLE)
            ->where('is_available', true)
            ->count();
        echo "Available dates: {$availableDates}\n";

        $availableTimeSlots = DB::table(self::ADMIN_TIME_SLOTS_TABLE)
            ->where('is_available', true)
            ->count();
        echo "Available time slots: {$availableTimeSlots}\n";
    }

    /**
     * Run the test data seeder.
     */
    public function run(): void
    {
        $this->bootstrap();
        
        echo "Adding test availability data...\n";
        
        $this->clearExistingTestData();

        for ($i = 0; $i < self::TEST_DAYS; $i++) {
            $date = date('Y-m-d', strtotime("+{$i} days"));
            $this->generateTestAvailabilityForDate($date);
        }

        $this->displayTestSummary();
    }
}

// Execute the test data seeder
(new TestDataSeeder())->run();
