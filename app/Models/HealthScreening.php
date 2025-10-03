<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Health Screening Model
 *
 * Represents a health screening record for breast milk donors,
 * including medical history, sexual history, and donor infant information.
 *
 * @property int $Health_Screening_ID
 * @property int $User_ID
 * @property string $civil_status
 * @property string $occupation
 * @property string $type_of_donor
 * @property string $status
 * @property string|null $admin_notes
 */
class HealthScreening extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'Health_Screening_ID';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'User_ID',
        'civil_status',
        'occupation',
        'type_of_donor',
        'status',
        'admin_notes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'archived_at' => 'datetime',
    ];

    /**
     * Status constants.
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_DECLINED = 'declined';

    /**
     * Donor type constants.
     */
    public const DONOR_TYPE_COMMUNITY = 'community';
    public const DONOR_TYPE_PRIVATE = 'private';
    public const DONOR_TYPE_EMPLOYEE = 'employee';
    public const DONOR_TYPE_NETWORK_OFFICE_AGENCY = 'network_office_agency';

    /**
     * Get the user that owns the health screening.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'User_ID', 'User_ID');
    }

    /**
     * Get the medical history answers for the health screening.
     */
    public function medicalHistoryAnswers(): HasMany
    {
        return $this->hasMany(MedicalHistoryAnswer::class, 'health_screening_id', 'Health_Screening_ID')
            ->orderBy('question_number');
    }

    /**
     * Get the sexual history answers for the health screening.
     */
    public function sexualHistoryAnswers(): HasMany
    {
        return $this->hasMany(SexualHistoryAnswer::class, 'health_screening_id', 'Health_Screening_ID')
            ->orderBy('question_number');
    }

    /**
     * Get the donor infant answers for the health screening.
     */
    public function donorInfantAnswers(): HasMany
    {
        return $this->hasMany(DonorInfantAnswer::class, 'health_screening_id', 'Health_Screening_ID')
            ->orderBy('question_number');
    }

    /**
     * Get the notifications for the health screening.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'health_screening_id', 'Health_Screening_ID');
    }

    /**
     * Scope a query to only include pending screenings.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include accepted screenings.
     */
    public function scopeAccepted(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACCEPTED);
    }

    /**
     * Scope a query to only include declined screenings.
     */
    public function scopeDeclined(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_DECLINED);
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to filter by donor type.
     */
    public function scopeByDonorType(Builder $query, string $donorType): Builder
    {
        return $query->where('type_of_donor', $donorType);
    }

    /**
     * Get the full name from the related user.
     */
    public function getFullNameAttribute(): ?string
    {
        return $this->user?->Full_Name;
    }

    /**
     * Get the contact number from the related user.
     */
    public function getContactNumberAttribute(): ?string
    {
        return $this->user?->Contact_Number;
    }

    /**
     * Check if the health screening is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the health screening is accepted.
     */
    public function isAccepted(): bool
    {
        return $this->status === self::STATUS_ACCEPTED;
    }

    /**
     * Check if the health screening is declined.
     */
    public function isDeclined(): bool
    {
        return $this->status === self::STATUS_DECLINED;
    }

    /**
     * Get all available status options.
     */
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_ACCEPTED,
            self::STATUS_DECLINED,
        ];
    }

    /**
     * Get all available donor type options.
     */
    public static function getDonorTypeOptions(): array
    {
        return [
            self::DONOR_TYPE_COMMUNITY,
            self::DONOR_TYPE_PRIVATE,
            self::DONOR_TYPE_EMPLOYEE,
            self::DONOR_TYPE_NETWORK_OFFICE_AGENCY,
        ];
    }

    /**
     * Scope a query to only include archived screenings.
     */
    public function scopeArchived(Builder $query): Builder
    {
        return $query->whereNotNull('archived_at');
    }

    /**
     * Scope a query to exclude archived screenings.
     */
    public function scopeNotArchived(Builder $query): Builder
    {
        return $query->whereNull('archived_at');
    }
}
