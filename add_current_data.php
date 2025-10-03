<?php

declare(strict_types=1);

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

/**
 * Admin Availability Data Seeder
 *
 * This script populates the admin availability tables with current data
 * for the next 15 days, including time slots and availability windows.
 */
class AdminAvailabilitySeeder
{
    /**
     * Number of days to generate availability for.
     */
    private const AVAILABILITY_DAYS = 15;

    /**
     * Default working hours.
     */
    private const DEFAULT_START_TIME = '08:00';
    private const DEFAULT_END_TIME = '17:00';

    /**
     * Available time slots throughout the day.
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
     * Display current date information.
     */
    private function displayCurrentInfo(): void
    {
        echo "Current date: " . date('Y-m-d') . "\n";
        echo "Current month: " . date('Y-m') . "\n";
    }

    /**
     * Clear existing availability data.
     */
    private function clearExistingData(): void
    {
        DB::table(self::ADMIN_TIME_SLOTS_TABLE)->delete();
        DB::table(self::ADMIN_WEEK_AVAILABILITY_TABLE)->delete();
    }

    /**
     * Generate availability for a specific date.
     */
    private function generateAvailabilityForDate(string $date): void
    {
        $this->addDayAvailability($date);
        $this->addTimeSlots($date);
        echo "Added availability for {$date}\n";
    }

    /**
     * Add day availability record.
     */
    private function addDayAvailability(string $date): void
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
     * Add time slots for a specific date.
     */
    private function addTimeSlots(string $date): void
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
     * Display summary of generated data.
     */
    private function displaySummary(): void
    {
        echo "\nData summary:\n";
        
        $availableDates = DB::table(self::ADMIN_WEEK_AVAILABILITY_TABLE)
            ->where('is_available', true)
            ->count();
        echo "Available dates: {$availableDates}\n";

        $availableTimeSlots = DB::table(self::ADMIN_TIME_SLOTS_TABLE)
            ->where('is_available', true)
            ->count();
        echo "Available time slots: {$availableTimeSlots}\n";

        $this->displaySampleDates();
    }

    /**
     * Display sample available dates.
     */
    private function displaySampleDates(): void
    {
        $sampleDates = DB::table(self::ADMIN_WEEK_AVAILABILITY_TABLE)
            ->where('is_available', true)
            ->limit(5)
            ->pluck('date');

        echo "Sample available dates: " . implode(', ', $sampleDates->toArray()) . "\n";
    }

    /**
     * Run the seeder.
     */
    public function run(): void
    {
        $this->bootstrap();
        $this->displayCurrentInfo();
        $this->clearExistingData();

        for ($i = 0; $i < self::AVAILABILITY_DAYS; $i++) {
            $date = date('Y-m-d', strtotime("+{$i} days"));
            $this->generateAvailabilityForDate($date);
        }

        $this->displaySummary();
    }
}

// Execute the seeder
(new AdminAvailabilitySeeder())->run();
