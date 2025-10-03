<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\Admin;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('create:admin', function () {
    $fullName = $this->ask('Enter admin full name');
    $username = $this->ask('Enter admin username');
    $password = $this->secret('Enter admin password');
    $contactNumber = $this->ask('Enter admin contact number');

    // Create the admin (password will be hashed automatically)
    $admin = Admin::create([
        'Full_Name' => $fullName,
        'username' => $username,
        'Password' => $password,
        'Contact_Number' => $contactNumber,
    ]);

    $this->info("Admin created successfully with ID: {$admin->Admin_ID}");
})->purpose('Create a new admin user directly in the database');

Artisan::command('create:admin:default', function () {
    // Check if admin already exists
    $existingAdmin = Admin::where('username', 'adminhmblsc')->first();
    if ($existingAdmin) {
        $this->error('Admin already exists!');
        return;
    }

    // Create admin with default values (password will be hashed automatically)
    $admin = Admin::create([
        'Full_Name' => 'HMBLSC Administrator',
        'username' => 'adminhmblsc',
        'Password' => 'admin123',
        'Contact_Number' => '09123456789',
    ]);

    $this->info("Default admin created successfully!");
    $this->info("Username: adminhmblsc");
    $this->info("Password: admin123");
    $this->info("Contact: 09123456789");
    $this->info("Admin ID: {$admin->Admin_ID}");
})->purpose('Create a default admin user with predefined credentials');
