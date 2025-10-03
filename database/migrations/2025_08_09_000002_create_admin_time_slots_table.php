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
    private const TABLE_NAME = 'admin_time_slots';

    /**
     * Run the migrations.
     *
     * Creates the admin time slots table for managing
     * specific time slot availability for appointments.
     */
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table): void {
            $table->id();
            $table->date('date')->comment('Date for the time slot');
            $table->time('time_slot')->comment('Specific time slot for appointments');
            $table->boolean('is_available')->default(true)->comment('Whether this time slot is available for booking');
            $table->timestamps();

            // Unique constraint to prevent duplicate date/time combinations
            $table->unique(['date', 'time_slot']);

            // Add indexes for commonly queried fields
            $table->index(['date', 'is_available'], 'admin_time_slots_date_available_idx');
            $table->index('is_available', 'admin_time_slots_available_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * Drops the admin time slots table.
     */
    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
};
