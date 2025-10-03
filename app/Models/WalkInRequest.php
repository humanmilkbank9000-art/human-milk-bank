<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalkInRequest extends Model
{
    protected $fillable = [
        'user_id',
        'donor_name',
        'donation_date',
        'donation_time',
        'status'
    ];

    protected $casts = [
        'donation_date' => 'date',
        'donation_time' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'User_ID');
    }
}
