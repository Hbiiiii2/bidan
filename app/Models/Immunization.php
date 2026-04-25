<?php
namespace App\Models;

use App\Models\Booking;
use App\Models\Child;
use App\Models\User;
use App\Models\Vaccine;
use Illuminate\Database\Eloquent\Model;

class Immunization extends Model
{
    protected $fillable = [
        'booking_id', 'child_id', 'vaccine_id', 'midwife_id', 'date', 'immunized_at', 'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'immunized_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function child()
    {
        return $this->belongsTo(Child::class);
    }

    public function vaccine()
    {
        return $this->belongsTo(Vaccine::class);
    }

    public function midwife()
    {
        return $this->belongsTo(User::class, 'midwife_id');
    }
}
