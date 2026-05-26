<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestCorrection extends Model
{
    protected $fillable = [
        'attendance_correction_id',
        'rest_id',
        'start',
        'end',
        'status',
    ];

    public function attendanceCorrection()
    {
        return $this->belongsTo(AttendanceCorrection::class);
    }

    
}
