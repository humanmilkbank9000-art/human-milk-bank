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
    private const TABLE_NAME = 'walk_in_requests';

    /**
     * Referenced table name.
     */
    private const USERS_TABLE = 'users';

    /**
     * Status options.
     */
    private const STATUS_OPTIONS = ['pending', 'confirmed', 'validated', 'cancelled'];

    /**
     * Run the migrations.
     *
     * Creates the walk-in requests table for managing walk-in donation appointments
     * with validation tracking support.
     */
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('Reference to user making the walk-in request');
            $table->string('donor_name')->comment('Name of the donor for the walk-in appointment');
            $table->date('donation_date')->comment('Requested date for walk-in donation');
            $table->time('donation_time')->comment('Requested time for walk-in donation');
            $table->enum('status', self::STATUS_OPTIONS)->default('pending')->comment('Current status of the walk-in request');
            $table->timestamp('validated_at')->nullable()->comment('Timestamp when request was validated');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('user_id')
                ->references('User_ID')
                ->on(self::USERS_TABLE)
                ->onDelete('cascade');

            // Add indexes for commonly queried fields
            $table->index(['user_id', 'donation_date']);
            $table->index(['status', 'donation_date']);
            $table->index('donation_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * Drops the walk-in requests table.
     */
    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
};
