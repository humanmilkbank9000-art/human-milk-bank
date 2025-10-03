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
    private const TABLE_NAME = 'breastmilk_donations';

    /**
     * Referenced table name.
     */
    private const HEALTH_SCREENINGS_TABLE = 'health_screenings';

    /**
     * Donation method options.
     */
    private const DONATION_METHODS = ['manual', 'pump'];

    /**
     * Run the migrations.
     *
     * Creates the breastmilk donations table for tracking
     * breast milk donations from approved donors.
     */
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table): void {
            $table->id('Breastmilk_Donation_ID');
            $table->unsignedBigInteger('Health_Screening_ID')->comment('Reference to approved health screening');
            $table->enum('Donation_Method', self::DONATION_METHODS)->comment('Method used for donation: manual or pump');
            $table->integer('Number_Of_Bag')->comment('Number of breast milk bags donated');
            $table->decimal('Volume_Per_Bag', 5, 2)->comment('Volume per bag in milliliters');
            $table->date('Donation_Date')->comment('Date of the donation');
            $table->time('Donation_Time')->comment('Time of the donation');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('Health_Screening_ID')
                ->references('Health_Screening_ID')
                ->on(self::HEALTH_SCREENINGS_TABLE)
                ->onDelete('cascade');

            // Add indexes for commonly queried fields
            $table->index(['Health_Screening_ID', 'Donation_Date'], 'donations_screening_date_idx');
            $table->index('Donation_Date', 'donations_date_idx');
            $table->index('Donation_Method', 'donations_method_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * Drops the breastmilk donations table.
     */
    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
};
