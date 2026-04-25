<?php
namespace App\Models;

use App\Models\Immunization;
use Illuminate\Database\Eloquent\Model;

class Vaccine extends Model
{
    protected $fillable = [
        'name', 'description', 'recommended_age',
    ];

    public function immunizations()
    {
        return $this->hasMany(Immunization::class);
    }
}
