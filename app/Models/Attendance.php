<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'date',
        'start_time',
        'end_time',
        'status',
    ];

    const STATUS_OFF = 0; // 勤務外
    const STATUS_WORKING = 1; // 出勤中
    const STATUS_BREAK = 2; // 休憩中
    const STATUS_FINISHED = 3; // 退勤済み

    public function isOff()
    {
        return $this->status === self::STATUS_OFF;
    }

    public function isWorking()
    {
        return $this->status === self::STATUS_WORKING;
    } 

    public function isBreak()
    {
        return $this->status === self::STATUS_BREAK;
    }

    public function isFinished()
    {
        return $this->status === self::STATUS_FINISHED;
    }

    public function getStatusName()
    {
        if ($this->isOff()) {
            return '勤務外';
        } elseif ($this->isWorking()) {
            return '出勤中';
        } elseif ($this->isBreak()) {
            return '休憩中';
        } elseif ($this->isFinished()) {
            return '退勤済み';
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rests()
    {
        return $this->hasMany(Rest::class);
    }

    public function attendanceCorrections()
    {
        return $this->hasMany(AttendanceCorrection::class);
    }
}
