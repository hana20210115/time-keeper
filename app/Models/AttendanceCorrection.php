<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AttendanceCorrection extends Model
{
    protected $fillable = [
        'user_id',
        'attendance_id',
        'date',
        'start',
        'end',
        'reason',
        'status',
    ];

        const STATUS_PENDING = 0; // 承認待ち
        const STATUS_APPROVED = 1; // 承認済み
    
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }
    
    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
    
    public function restCorrections()
    {
        return $this->hasMany(RestCorrection::class);
    }

    public function getUserNameAttribute()
    {
        return $this->user->name;
    }
    
    public function getFormattedDateAttribute()
    {
        return Carbon::parse($this->attendance->date)->format('Y/m/d');
    }

    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format('Y/m/d');
    }
}
