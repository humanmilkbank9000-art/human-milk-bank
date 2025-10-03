<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Infant extends Model
{
    use HasFactory;

    protected $primaryKey = 'Infant_ID';
    protected $fillable = [
        'Full_Name',
        'Sex',
        'Date_Of_Birth',
        'Age',
        'Birthweight',
        'User_ID'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'User_ID', 'User_ID');
    }
}

