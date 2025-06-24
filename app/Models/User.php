<?php

namespace App\Models;

use Carbon\Carbon;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $timezone
 */
class User extends Authenticatable implements MustVerifyEmail, UserContract, FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'timezone',
        'google_id',
        'email_verified_at',
        'profile_image',
        'phone_number',
        'facebook_profile',
        'super_admin'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function isAdmin(): bool
    {
        return true;
    }


    public function isSuperAdmin(): bool
    {
        return !! auth()->user()->super_admin;
    }


    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function travels(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Travel::class);
    }
}
