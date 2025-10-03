<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Table name.
     */
    private const TABLE_NAME = 'admins';

    /**
     * Default admin credentials.
     */
    private const DEFAULT_ADMIN = [
        'full_name' => 'System Administrator',
        'username' => 'adminhmblsc',
        'contact_number' => '09123456789',
        'password' => 'admin123',
    ];

    /**
     * Run the migrations.
     *
     * Creates the admins table for system administrators
     * with default admin account.
     */
    public function up(): void
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table): void {
            $table->id('Admin_ID');
            $table->string('Full_Name')->comment('Administrator full name');
            $table->string('username')->unique()->comment('Administrator username for login');
            $table->string('Password')->comment('Encrypted administrator password');
            $table->string('Contact_Number')->comment('Administrator contact phone number');
            $table->timestamps();

            // Add indexes for commonly queried fields
            $table->index('username');
            $table->index('Contact_Number');
        });

        // Insert default admin credentials
        DB::table(self::TABLE_NAME)->insert([
            'Full_Name' => self::DEFAULT_ADMIN['full_name'],
            'username' => self::DEFAULT_ADMIN['username'],
            'Contact_Number' => self::DEFAULT_ADMIN['contact_number'],
            'Password' => Hash::make(self::DEFAULT_ADMIN['password']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * Drops the admins table.
     */
    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
};
