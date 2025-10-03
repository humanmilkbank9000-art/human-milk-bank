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
    private const TABLE_NAME = 'infants';

    /**
     * Referenced table name.
     */
    private const USERS_TABLE = 'users';

    /**
     * Sex options.
     */
    private const SEX_OPTIONS = ['Male', 'Female'];

    /**
     * Run the migrations.
     *
     * Creates the infants table for storing information about
     * infants related to breast milk donors.
     */
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table): void {
            $table->id('Infant_ID');
            $table->string('Full_Name')->comment('Full name of the infant');
            $table->enum('Sex', self::SEX_OPTIONS)->comment('Biological sex of the infant');
            $table->date('Date_Of_Birth')->comment('Date of birth of the infant');
            $table->integer('Age')->comment('Age of the infant in months');
            $table->decimal('Birthweight', 5, 2)->comment('Birthweight in kilograms with 2 decimal places');
            $table->unsignedBigInteger('User_ID')->comment('Reference to parent user');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('User_ID')
                ->references('User_ID')
                ->on(self::USERS_TABLE)
                ->onDelete('cascade');

            // Add indexes for commonly queried fields
            $table->index('User_ID');
            $table->index('Date_Of_Birth');
        });
    }

    /**
     * Reverse the migrations.
     *
     * Drops the infants table.
     */
    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
};
