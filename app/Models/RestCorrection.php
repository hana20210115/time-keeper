<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestCorrection extends Model
{
    protected $fillable = [
        'attendance_correction_id',
        'start',
        'end',
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

    public function attendanceCorrection()
    {
        return $this->belongsTo(AttendanceCorrection::class);
    }
}
