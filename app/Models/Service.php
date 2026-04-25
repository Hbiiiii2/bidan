<?php
namespace App\Models;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'name',
        'tag',
        'description',
        'available_date',
        'available_from_date',
        'available_until_date',
        'available_start_time',
        'available_end_time',
        'price',
        'type',
    ];

    protected $casts = [
        'available_date' => 'date',
        'available_from_date' => 'date',
        'available_until_date' => 'date',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function midwives()
    {
        return $this->belongsToMany(User::class, 'service_user')
            ->withPivot('max_daily_quota')
            ->withTimestamps();
    }

    // Backward compatibility - get first midwife
    public function midwife()
    {
        return $this->midwives()->first();
    }
}
