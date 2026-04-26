<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImmunizationRecallLog extends Model
{
    protected $fillable = [
        'child_id',
        'user_id',
        'vaccine_id',
        'immunization_id',
        'next_dose',
        'due_date',
        'sent_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'sent_at' => 'datetime',
    ];
}
