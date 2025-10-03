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
    private const TABLE_NAME = 'breastmilk_requests';

    /**
     * Referenced table name.
     */
    private const USERS_TABLE = 'users';

    /**
     * Status options.
     */
    private const STATUS_OPTIONS = ['pending', 'approved', 'declined', 'dispensed'];

    /**
     * Run the migrations.
     *
     * Creates the breastmilk requests table for managing requests
     * for breast milk from recipients with medical prescriptions.
     */
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('User_ID')->comment('Reference to user making the request');
            $table->string('recipient_name')->comment('Name of the breast milk recipient');
            $table->date('recipient_dob')->comment('Date of birth of the recipient');
            $table->decimal('recipient_weight', 5, 2)->comment('Weight of recipient in kilograms');
            $table->string('contact_number', 20)->comment('Contact number for the request');
            $table->text('medical_condition')->comment('Medical condition requiring breast milk');
            $table->integer('requested_volume')->comment('Requested volume in milliliters');
            $table->date('needed_by_date')->comment('Date when breast milk is needed by');
            $table->string('prescription_image_path')->nullable()->comment('Path to uploaded prescription image');
            $table->enum('status', self::STATUS_OPTIONS)->default('pending')->comment('Current status of the request');
            $table->text('admin_notes')->nullable()->comment('Administrative notes about the request');
            $table->timestamp('approved_at')->nullable()->comment('Timestamp when request was approved');
            $table->timestamp('dispensed_at')->nullable()->comment('Timestamp when breast milk was dispensed');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('User_ID')
                ->references('User_ID')
                ->on(self::USERS_TABLE)
                ->onDelete('cascade');

            // Add indexes for commonly queried fields
            $table->index(['User_ID', 'status'], 'requests_user_status_idx');
            $table->index(['status', 'needed_by_date'], 'requests_status_needed_date_idx');
            $table->index('created_at', 'requests_created_at_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * Drops the breastmilk requests table.
     */
    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
};
