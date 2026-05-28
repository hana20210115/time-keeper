<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;

class AdminAttendanceController extends Controller
{
    public function index($date = null)
    {

        if($date){
            $currentDate = Carbon::parse($date);
        } else{
            $currentDate = Carbon::today();
        }


        $prevDate = $currentDate->copy()->subDay()->format('Y-m-d');
        $nextDate = $currentDate->copy()->addDay()->format('Y-m-d');


        $attendances = Attendance::with('user')
            ->whereDate('date', $currentDate->format('Y-m-d'))
            ->get();


        $attendances->transform(function ($attendance) {

            $attendance->formatted_start_time = $attendance->start_time ? Carbon::parse($attendance->start_time)->format('H:i') : '';
            $attendance->formatted_end_time   = $attendance->end_time ? Carbon::parse($attendance->end_time)->format('H:i') : '';
            $attendance->formatted_rest_time  = $attendance->rest_time ? Carbon::parse($attendance->rest_time)->format('H:i') : '';
            $attendance->formatted_work_time  = $attendance->work_time ? Carbon::parse($attendance->work_time)->format('H:i') : '';
            
            return $attendance;
        });


        return view('admin.attendance_list', compact('currentDate', 'prevDate', 'nextDate', 'attendances'));
    }



    public function show($id)
    {

        $attendance = Attendance::with(['user', 'rests'])->findOrFail($id);


        return view('admin.attendance_detail', compact('attendance'));
    }
}
