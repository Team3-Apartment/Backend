<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'images' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function rents()
    {
        return $this->hasMany(Rent::class);
    }
    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }

}
