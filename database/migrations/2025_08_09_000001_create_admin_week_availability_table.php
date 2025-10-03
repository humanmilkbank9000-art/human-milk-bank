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
    private const TABLE_NAME = 'admin_week_availability';

    /**
     * Run the migrations.
     *
     * Creates the admin week availability table for managing
     * specific date-based administrator availability overrides.
     */
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table): void {
            $table->id();
            $table->date('date')->comment('Specific date for availability override');
            $table->time('start_time')->nullable()->comment('Start time for availability on this date');
            $table->time('end_time')->nullable()->comment('End time for availability on this date');
            $table->boolean('is_available')->default(false)->comment('Whether admin is available on this specific date');
            $table->timestamps();

            // Unique constraint to prevent duplicate date entries
            $table->unique(['date']);

            // Add indexes for commonly queried fields
            $table->index(['date', 'is_available'], 'admin_week_date_available_idx');
            $table->index('is_available', 'admin_week_available_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * Drops the admin week availability table.
     */
    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
};


