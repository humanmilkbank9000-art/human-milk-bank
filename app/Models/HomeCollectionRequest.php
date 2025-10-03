<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeCollectionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'User_ID',
        'donor_name',
        'number_of_bags',
        'total_volume',
        'date_collected',
        'pickup_address',
        'status',
        'scheduled_pickup_date',
        'scheduled_pickup_time',
        'admin_notes'
    ];

    protected $casts = [
        'date_collected' => 'date',
        'scheduled_pickup_date' => 'date',
        'scheduled_pickup_time' => 'datetime:H:i',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'User_ID', 'User_ID');
    }
}
