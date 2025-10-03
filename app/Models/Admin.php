<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'Admin_ID';
    protected $fillable = [
        'Contact_Number',
        'Full_Name',
        'username',
        'Password'
    ];

    protected $hidden = [
        'Password',
    ];

    /**
     * Hash the password when setting it
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['Password'] = Hash::make($value);
    }

    /**
     * Get the password for the user.
     */
    public function getAuthPassword()
    {
        return $this->Password;
    }
}
