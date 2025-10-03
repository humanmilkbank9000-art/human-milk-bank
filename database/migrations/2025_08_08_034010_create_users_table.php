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
    private const TABLE_NAME = 'users';

    /**
     * User type options.
     */
    private const USER_TYPES = ['donor', 'recipient', 'both'];

    /**
     * Sex options.
     */
    private const SEX_OPTIONS = ['Male', 'Female'];

    /**
     * Run the migrations.
     *
     * Creates the users table for storing user information
     * including donors and recipients in the breast milk donation system.
     */
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table): void {
            $table->id('User_ID');
            $table->string('Contact_Number')->comment('User contact phone number');
            $table->string('Full_Name')->comment('User full name');
            $table->integer('Age')->comment('User age in years');
            $table->text('Address')->comment('User residential address');
            $table->enum('User_Type', self::USER_TYPES)->comment('User role in the system');
            $table->string('Password')->comment('Encrypted user password');
            $table->date('Date_Of_Birth')->comment('User date of birth');
            $table->enum('Sex', self::SEX_OPTIONS)->comment('User biological sex');
            $table->timestamps();

            // Add indexes for commonly queried fields
            $table->index('Contact_Number');
            $table->index('User_Type');
            $table->index(['User_Type', 'Sex']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * Drops the users table.
     */
    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
};
