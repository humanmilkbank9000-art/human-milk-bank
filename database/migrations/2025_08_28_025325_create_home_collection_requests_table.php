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
    private const TABLE_NAME = 'home_collection_requests';

    /**
     * Referenced table name.
     */
    private const USERS_TABLE = 'users';

    /**
     * Status options.
     */
    private const STATUS_OPTIONS = ['pending', 'scheduled', 'completed', 'cancelled'];

    /**
     * Run the migrations.
     *
     * Creates the home collection requests table for managing
     * home pickup requests for breast milk donations.
     */
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('User_ID')->comment('Reference to user making the home collection request');
            $table->string('donor_name')->comment('Name of the donor for home collection');
            $table->integer('number_of_bags')->comment('Number of breast milk bags to be collected');
            $table->decimal('total_volume', 8, 2)->comment('Total volume of breast milk in milliliters');
            $table->date('date_collected')->comment('Date when breast milk was collected');
            $table->text('pickup_address')->comment('Address for home collection pickup');
            $table->enum('status', self::STATUS_OPTIONS)->default('pending')->comment('Current status of the collection request');
            $table->date('scheduled_pickup_date')->nullable()->comment('Scheduled date for pickup');
            $table->time('scheduled_pickup_time')->nullable()->comment('Scheduled time for pickup');
            $table->text('admin_notes')->nullable()->comment('Administrative notes about the collection');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('User_ID')
                ->references('User_ID')
                ->on(self::USERS_TABLE)
                ->onDelete('cascade');

            // Add indexes for commonly queried fields
            $table->index(['User_ID', 'status'], 'home_collection_user_status_idx');
            $table->index(['status', 'scheduled_pickup_date'], 'home_collection_status_pickup_idx');
            $table->index('date_collected', 'home_collection_date_collected_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * Drops the home collection requests table.
     */
    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
};
