<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Table name.
     */
    private const TABLE_NAME = 'admin_availability_settings';

    /**
     * Days of the week.
     */
    private const DAYS_OF_WEEK = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

    /**
     * Run the migrations.
     *
     * Creates the admin availability settings table for managing
     * administrator availability schedules throughout the week.
     */
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table): void {
            $table->id();
            $table->enum('day_of_week', self::DAYS_OF_WEEK)->comment('Day of the week for availability');
            $table->time('start_time')->comment('Start time for availability');
            $table->time('end_time')->comment('End time for availability');
            $table->boolean('is_available')->default(true)->comment('Whether admin is available on this day/time');
            $table->timestamps();

            // Add indexes for commonly queried fields
            $table->index(['day_of_week', 'is_available'], 'admin_availability_day_available_idx');
            $table->index('is_available', 'admin_availability_available_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * Drops the admin availability settings table.
     */
    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
};
