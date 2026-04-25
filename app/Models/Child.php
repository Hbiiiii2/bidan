<?php
namespace App\Models;

use App\Models\Booking;
use App\Models\Immunization;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Child extends Model
{
    protected $fillable = [
        'user_id', 'name', 'birth_date', 'gender',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function immunizations()
    {
        return $this->hasMany(Immunization::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
