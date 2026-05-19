<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Rest;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = carbon::today();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        $statusLabel = $attendance ? $attendance->getStatusName() :'勤務外';


        $now = Carbon::now();
        $weekdays = ['日', '月', '火', '水', '木', '金', '土'];
        $currentDate = $now->format('Y年m月d日').'('.$weekdays[$now->dayOfWeek].')';
        $currentTime = $now->format('H:i');

        return view('attendance.index', compact('attendance', 'statusLabel', 'currentDate', 'currentTime'));
    }

    public function start()
    {
        $user = Auth::user();
        $today = Carbon::today();

        $existingAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();
        

        if ($existingAttendance) {
            return redirect()->back()->with('error', '既に出勤しています。');
        }

        Attendance::create([
            'user_id' => $user->id,
            'date'    => $today,
            'start_time'   => Carbon::now(),
            'status'  => Attendance::STATUS_WORKING,
        ]);

        return redirect()->route('attendance.index')->with('success', '出勤しました。');

    }

    public function restStart()
    {
        $user = Auth::user();
        $today = Carbon::today();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        Rest::create([
            'attendance_id' => $attendance->id,
            'start'    => Carbon::now(),
        ]);


        $attendance->update([
            'status' => Attendance::STATUS_BREAK,
        ]);

        return redirect()->route('attendance.index')->with('success', '休憩を開始しました。');
    }

    public function restEnd()
    {   
        $user = Auth::user();
        $today = Carbon::today();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        $rest = Rest::where('attendance_id', $attendance->id)
            ->whereNull('end')
            ->first();

        if ($rest) {
            $rest->update([
                'end' => Carbon::now(),
            ]);
        }

        $attendance->update([
            'status' => Attendance::STATUS_WORKING,
        ]);

        return redirect()->route('attendance.index')->with('success', '休憩を終了しました。');
    }

        public function end()
        {
            $user = Auth::user();
            $today = Carbon::today();
    
            $attendance = Attendance::where('user_id', $user->id)
                ->whereDate('date', $today)
                ->first();
    
            if ($attendance) {
                $attendance->update([
                    'end_time' => Carbon::now(),
                    'status'   => Attendance::STATUS_FINISHED,
                ]);
    
                return redirect()->route('attendance.index')->with('success', '退勤しました。');
            }
    
        }











}
