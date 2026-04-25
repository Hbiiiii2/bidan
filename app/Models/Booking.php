<?php
namespace App\Models;

use App\Models\Child;
use App\Models\Immunization;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Vaccine;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'child_id',
        'service_id',
        'schedule_id',
        'vaccine_id',
        'status',
        'notes',
        'midwife_notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function child()
    {
        return $this->belongsTo(Child::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }

    public function vaccine()
    {
        return $this->belongsTo(Vaccine::class);
    }

    public function immunization()
    {
        return $this->hasOne(Immunization::class);
    }
}
