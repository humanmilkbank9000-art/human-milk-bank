<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_rejects_weak_password(): void
    {
        $payload = [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'middle_name' => null,
            'gender' => 'female',
            'birthday' => '1995-06-15',
            'age' => 29,
            'contact_number' => '09123456789',
            'address' => '123 Main St',
            'password' => 'pass1234', // too weak (no upper, no symbol, <12)
            'confirm_password' => 'pass1234',
        ];

        $response = $this->post('/create-account', $payload);
        $response->assertSessionHasErrors(['password']);
    }

    public function test_password_is_hashed_on_persist(): void
    {
        // Simulate multi-step registration: store user data in session then post infant info
        $this->withSession([
            'user_data' => [
                'first_name' => 'Jane',
                'last_name' => 'Doe',
                'middle_name' => null,
                'gender' => 'female',
                'birthday' => '1995-06-15',
                'age' => 29,
                'contact_number' => '09123456780',
                'address' => '123 Main St',
                'password' => 'Str0ngP@ssword!!',
            ],
        ]);

        $payload = [
            'first_name' => 'Baby',
            'last_name' => 'Doe',
            'middle_name' => null,
            'gender' => 'female',
            'birthday' => '2024-01-01',
            'age' => 12, // months
            'birth_weight' => 7.5,
        ];

        $response = $this->post('/infant-information', $payload);
        $response->assertRedirect('/dashboard');

        $user = User::where('Contact_Number', '09123456780')->first();
        $this->assertNotNull($user, 'User should be created');
        $this->assertNotSame('Str0ngP@ssword!!', $user->Password);
        $this->assertTrue(Hash::check('Str0ngP@ssword!!', $user->Password));
    }
}
