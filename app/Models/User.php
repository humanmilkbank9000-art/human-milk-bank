<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

/**
 * User Model
 *
 * Represents a user in the breast milk donation system.
 * Users can be donors who submit health screenings and have infants.
 *
 * @property int $User_ID
 * @property string $Contact_Number
 * @property string $Full_Name
 * @property int $Age
 * @property string $Address
 * @property string $User_Type
 * @property string $Password
 * @property string $Date_Of_Birth
 * @property string $Sex
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'User_ID';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'Contact_Number',
        'Full_Name',
        'Age',
        'Address',
        'User_Type',
        'Password',
        'Date_Of_Birth',
        'Sex',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'Password',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'Age' => 'integer',
        'Date_Of_Birth' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * User type constants.
     */
    public const USER_TYPE_DONOR = 'donor';
    public const USER_TYPE_ADMIN = 'admin';

    /**
     * Sex constants.
     */
    public const SEX_MALE = 'Male';
    public const SEX_FEMALE = 'Female';

    /**
     * Get the infants associated with the user.
     */
    public function infants(): HasMany
    {
        return $this->hasMany(Infant::class, 'User_ID', 'User_ID');
    }

    /**
     * Get the health screenings associated with the user.
     */
    public function healthScreenings(): HasMany
    {
        return $this->hasMany(HealthScreening::class, 'User_ID', 'User_ID');
    }

    /**
     * Get the notifications associated with the user.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'User_ID', 'User_ID');
    }

    /**
     * Check if the user is a donor.
     */
    public function isDonor(): bool
    {
        return $this->User_Type === self::USER_TYPE_DONOR;
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->User_Type === self::USER_TYPE_ADMIN;
    }

    /**
     * Check if the user is female.
     */
    public function isFemale(): bool
    {
        return $this->Sex === self::SEX_FEMALE;
    }

    /**
     * Check if the user is male.
     */
    public function isMale(): bool
    {
        return $this->Sex === self::SEX_MALE;
    }

    /**
     * Get the user's full contact information.
     */
    public function getFullContactAttribute(): string
    {
        return "{$this->Full_Name} - {$this->Contact_Number}";
    }

    /**
     * Get all available user types.
     */
    public static function getUserTypes(): array
    {
        return [
            self::USER_TYPE_DONOR,
            self::USER_TYPE_ADMIN,
        ];
    }

    /**
     * Get all available sex options.
     */
    public static function getSexOptions(): array
    {
        return [
            self::SEX_MALE,
            self::SEX_FEMALE,
        ];
    }

    /**
     * Automatically hash the password when setting it.
     */
    public function setPasswordAttribute($value): void
    {
        if (empty($value)) {
            $this->attributes['Password'] = $value;
            return;
        }

        // Avoid double-hashing: only hash if not already a bcrypt hash
        if (is_string($value) && !preg_match('/^\$2y\$/', $value)) {
            $this->attributes['Password'] = Hash::make($value);
        } else {
            $this->attributes['Password'] = $value;
        }
    }

    /**
     * Get the authentication password for the user model.
     */
    public function getAuthPassword()
    {
        return $this->Password;
    }
}
