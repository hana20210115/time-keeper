<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Rest;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\AttendanceCorrection;

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
    public function list(Request $request)
    {
       
        $monthInput = $request->query('month', Carbon::now()->format('Y-m'));
        $targetDate = Carbon::parse($monthInput . '-01');

        $firstDay = $targetDate->copy()->startOfMonth();
        $lastDay = $targetDate->copy()->endOfMonth();

       
        $attendance = Attendance::with('rests')->where('user_id', Auth::id())
            ->whereBetween('date', [$firstDay->format('Y-m-d'), $lastDay->format('Y-m-d')])
            ->get()
            ->keyBy('date');

        $calendarData = [];
        $weekdays = ['日', '月', '火', '水', '木', '金', '土'];


        for ($date = $firstDay->copy(); $date->lte($lastDay); $date->addDay()) 
                {
            $dateString = $date->format('Y-m-d');
            $record = $attendance->get($dateString); 

            $startTime = '';
            $endTime = '';
            $breakTimeDisplay = '';
            $totalTimeDisplay = '';

            if ($record) {
                $startTime = $record->start_time ? Carbon::parse($record->start_time)->format('H:i') : '';
                $endTime = $record->end_time ? Carbon::parse($record->end_time)->format('H:i') : '';


                $totalBreakMinutes = 0;
                foreach ($record->rests as $rest) {
                    if ($rest->start && $rest->end) {
                        $breakStart = Carbon::parse($rest->start);
                        $breakEnd = Carbon::parse($rest->end);
                        $totalBreakMinutes += $breakStart->diffInMinutes($breakEnd);
                    }
                }

                if ($totalBreakMinutes > 0) {
                    $breakHours = floor($totalBreakMinutes / 60);
                    $breakMins = $totalBreakMinutes % 60;
                    $breakTimeDisplay = sprintf('%02d:%02d', $breakHours, $breakMins);
                } elseif ($record->start_time && $record->end_time) {
                    $breakTimeDisplay = '00:00';
                }


                if ($record->start_time && $record->end_time) {
                    $attendanceStart = Carbon::parse($record->start_time);
                    $attendanceEnd = Carbon::parse($record->end_time);
                    $totalStayMinutes = $attendanceStart->diffInMinutes($attendanceEnd);
                    $workMinutes = $totalStayMinutes - $totalBreakMinutes;

                    if ($workMinutes > 0) {
                        $workHours = floor($workMinutes / 60);
                        $workMins = $workMinutes % 60;
                        $totalTimeDisplay = sprintf('%02d:%02d', $workHours, $workMins);
                    } else {
                        $totalTimeDisplay = '00:00';
                    }
                }
            }

            $calendarData[] = [
                'date_display' => $date->format('m/d') . '(' . $weekdays[$date->dayOfWeek] . ')',
                'start_time'   => $startTime,
                'end_time'     => $endTime,
                'break_time'   => $breakTimeDisplay,
                'total_time'   => $totalTimeDisplay,
                'id'           => $record ? $record->id : null,
            ];
        }



        $currentMonth = $firstDay->format('Y/m');
        $prevMonth = $firstDay->copy()->subMonth()->format('Y-m');
        $nextMonth = $firstDay->copy()->addMonth()->format('Y-m');


        return view('attendance.list', compact('calendarData', 'currentMonth', 'prevMonth', 'nextMonth'));
    }



    public function detail($id)
    {
        $attendance = Attendance::with('rests')->findOrFail($id);
        $correction = AttendanceCorrection::where('attendance_id', $id)->latest()->first();

        $isPending = $correction && $correction->status === AttendanceCorrection::STATUS_PENDING;
        $reason = $correction ? $correction->reason : '';

        
        $restsData = $attendance->rests->map(function ($rest, $index) {
            return [
                'id'    => $rest->id, 
                'label' => $index === 0 ? '休憩' : '休憩' . ($index + 1),
                'start' => Carbon::parse($rest->start)->format('H:i'),
                'end'   => Carbon::parse($rest->end)->format('H:i'),
            ];
        })->toArray();


        if (!$isPending) {
            $count = count($restsData);
            $restsData[] = [
                'id'    => 'new', 
                'label' => $count === 0 ? '休憩' : '休憩' . ($count + 1),
                'start' => '', 
                'end'   => '', 
            ];
        }

        $viewData = [
            'id'         => $attendance->id,
            'name'       => $attendance->user->name,
            'date_year'  => Carbon::parse($attendance->date)->format('Y年'),
            'date_md'    => Carbon::parse($attendance->date)->format('n月j日'),
            'start_time' => Carbon::parse($attendance->start_time)->format('H:i'),
            'end_time'   => Carbon::parse($attendance->end_time)->format('H:i'),
            'is_pending' => $isPending,
            'reason'     => $reason,
            'rests'      => $restsData,
        ];

        return view('attendance.detail', compact('viewData'));
    }

    




}



