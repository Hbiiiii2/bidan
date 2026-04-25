<?php
namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Booking;
use App\Models\Child;
use App\Models\Immunization;
use App\Models\Schedule;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    // /** @use HasFactory<\Database\Factories\UserFactory> */
    // use HasFactory, Notifiable;

    use HasRoles;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'nip',
        'profile_photo',
        'hospital_institution',
        'address',
        'career_start_year',
        'available_days',
        'available_start_time',
        'available_end_time',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'available_days'    => 'array',
        ];
    }

    public function children()
    {
        return $this->hasMany(Child::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'midwife_id');
    }

    public function immunizations()
    {
        return $this->hasMany(Immunization::class, 'midwife_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function getExperienceAttribute()
    {
        if ($this->career_start_year) {
            return now()->year - $this->career_start_year;
        }
        return null;
    }

    public function getProfilePhotoUrlAttribute(): ?string
    {
        if (!$this->profile_photo) {
            return null;
        }

        if (filter_var($this->profile_photo, FILTER_VALIDATE_URL)) {
            return $this->profile_photo;
        }

        $photoPath = ltrim($this->profile_photo, '/');
        $candidates = [
            str_starts_with($photoPath, 'profile_photos/') ? $photoPath : 'profile_photos/' . $photoPath,
            $photoPath,
        ];

        foreach ($candidates as $candidate) {
            if (Storage::disk('public')->exists($candidate)) {
                return Storage::url($candidate);
            }
        }

        return null;
    }
}