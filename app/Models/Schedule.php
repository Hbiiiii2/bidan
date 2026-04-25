<?php
namespace App\Models;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'midwife_id', 'date', 'start_time', 'end_time', 'quota',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function midwife()
    {
        return $this->belongsTo(User::class, 'midwife_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
