<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password','role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens,HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    const ADMIN = 1; // 管理者
    const USER = 0; // 一般ユーザー

    public function isAdmin(): bool
    {
        return $this->role == self::ADMIN  ;
    }

    public function isUser(): bool
    {
        return $this->role == self::USER;
    }

    

    public function attendances() :HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function attendanceCorrections() :HasMany
    {
        return $this->hasMany(AttendanceCorrection::class);
    }
}
