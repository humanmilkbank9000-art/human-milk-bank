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
    private const TABLE_NAME = 'donation_history';

    /**
     * Referenced table name.
     */
    private const USERS_TABLE = 'users';

    /**
     * Donation type options.
     */
    private const DONATION_TYPES = ['walk_in', 'home_collection'];

    /**
     * Status options.
     */
    private const STATUS_OPTIONS = ['pending', 'scheduled', 'approved', 'completed', 'rejected'];

    /**
     * Run the migrations.
     *
     * Creates the donation history table for tracking breast milk donations
     * including walk-in and home collection donations with scheduling support.
     */
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('User_ID')->comment('Reference to user who made the donation');
            $table->enum('donation_type', self::DONATION_TYPES)->comment('Type of donation: walk-in or home collection');
            $table->integer('number_of_bags')->nullable()->comment('Number of breast milk bags donated');
            $table->decimal('total_volume', 8, 2)->nullable()->comment('Total volume in ml, nullable for walk-in donations');
            $table->date('donation_date')->comment('Date of donation');
            $table->time('donation_time')->comment('Time of donation');
            $table->string('pickup_address')->nullable()->comment('Address for home collection pickup');
            $table->date('scheduled_date')->nullable()->comment('Scheduled date for donation processing');
            $table->time('scheduled_time')->nullable()->comment('Scheduled time for donation processing');
            $table->enum('status', self::STATUS_OPTIONS)->default('pending')->comment('Current status of the donation');
            $table->text('admin_notes')->nullable()->comment('Administrative notes about the donation');
            $table->timestamp('validated_at')->nullable()->comment('Timestamp when donation was validated');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('User_ID')
                ->references('User_ID')
                ->on(self::USERS_TABLE)
                ->onDelete('cascade');

            // Add indexes for commonly queried fields
            $table->index(['User_ID', 'donation_date']);
            $table->index(['status', 'donation_date']);
            $table->index('donation_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * Drops the donation history table.
     */
    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
};
