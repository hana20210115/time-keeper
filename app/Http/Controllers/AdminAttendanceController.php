<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;
use App\Models\Rest;
use App\Models\AttendanceCorrection;
use App\Http\Requests\StoreCorrectionRequest;

class AdminAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->query('date');

        if($date){
            $currentDate = Carbon::parse($date);
        } else{
            $currentDate = Carbon::today();
        }

        $prevDate = $currentDate->copy()->subDay()->format('Y-m-d');
        $nextDate = $currentDate->copy()->addDay()->format('Y-m-d');


        $attendances = Attendance::with(['user', 'rests'])
            ->whereDate('date', $currentDate->format('Y-m-d'))
            ->get();

        $attendances->transform(function ($attendance) {
            $attendance->formatted_start_time = $attendance->start_time ? Carbon::parse($attendance->start_time)->format('H:i') : '';
            $attendance->formatted_end_time   = $attendance->end_time ? Carbon::parse($attendance->end_time)->format('H:i') : '';
            

            $totalBreakMinutes = 0;
            foreach ($attendance->rests as $rest) {
                if ($rest->start && $rest->end) {
                    $breakStart = Carbon::parse($rest->start);
                    $breakEnd = Carbon::parse($rest->end);
                    $totalBreakMinutes += $breakStart->diffInMinutes($breakEnd);
                }
            }

            if ($totalBreakMinutes > 0) {
                $breakHours = floor($totalBreakMinutes / 60);
                $breakMins = $totalBreakMinutes % 60;
                $attendance->formatted_rest_time = sprintf('%02d:%02d', $breakHours, $breakMins);
            } elseif ($attendance->start_time && $attendance->end_time) {
                $attendance->formatted_rest_time = '00:00';
            } else {
                $attendance->formatted_rest_time = '';
            }

            if ($attendance->start_time && $attendance->end_time) {
                $attendanceStart = Carbon::parse($attendance->start_time);
                $attendanceEnd = Carbon::parse($attendance->end_time);
                $totalStayMinutes = $attendanceStart->diffInMinutes($attendanceEnd);
                $workMinutes = $totalStayMinutes - $totalBreakMinutes;

                if ($workMinutes > 0) {
                    $workHours = floor($workMinutes / 60);
                    $workMins = $workMinutes % 60;
                    $attendance->formatted_work_time = sprintf('%02d:%02d', $workHours, $workMins);
                } else {
                    $attendance->formatted_work_time = '00:00';
                }
            } else {
                $attendance->formatted_work_time = '';
            }
            
            return $attendance;
        });

        return view('admin.attendance_list', compact('currentDate', 'prevDate', 'nextDate', 'attendances'));
    }


    public function show($id)
    {
        $attendance = Attendance::with(['user', 'rests'])->findOrFail($id);

        $correction = AttendanceCorrection::where('attendance_id', $id)->latest()->first();
        $isPending = $correction && $correction->status == AttendanceCorrection::STATUS_PENDING;
        $isLocked = $isPending || session()->has('success');

        $attendance->formatted_date_year = Carbon::parse($attendance->date)->format('Y年');
        $attendance->formatted_date_month_day = Carbon::parse($attendance->date)->format('n月j日');
        $attendance->formatted_start_time = $attendance->start_time ? Carbon::parse($attendance->start_time)->format('H:i') : '';
        $attendance->formatted_end_time = $attendance->end_time ? Carbon::parse($attendance->end_time)->format('H:i') : '';

        foreach ($attendance->rests as $rest) {
            $rest->formatted_start = $rest->start ? Carbon::parse($rest->start)->format('H:i') : '';
            $rest->formatted_end = $rest->end ? Carbon::parse($rest->end)->format('H:i') : '';
        }

    
        $nextRestNum = $attendance->rests->count() + 1;

        return view('admin.attendance_detail', compact('attendance', 'isPending', 'isLocked', 'nextRestNum'));
    }



    public function update(StoreCorrectionRequest $request, $id)
    {   

        $correction = AttendanceCorrection::where('attendance_id', $id)->latest()->first();
        $isPending = $correction && $correction->status === AttendanceCorrection::STATUS_PENDING;

        if($isPending){
            return redirect()->route('admin.attendance_detail',['id' => $id])
            ->withErrors('承認待ちのため修正できません');
        }


        $attendance = Attendance::findOrFail($id);
        $date = Carbon::parse($attendance->date)->format('Y-m-d');


        $attendance->update([
            'start_time' => $date . ' ' . $request->start_time . ':00',
            'end_time'   => $date . ' ' . $request->end_time . ':00',
            'reason'     => $request->reason,
        ]);


        if ($request->has('rests')) {
            foreach ($request->rests as $restId => $restData) {
                Rest::where('id', $restId)->update([
                    'start' => $date . ' ' . $restData['start'] . ':00',
                    'end'   => $date . ' ' . $restData['end'] . ':00',
                ]);
            }
        }


        return redirect()->route('admin.attendance_detail', ['id' => $id])
                        ->with('success', '勤怠情報を修正しました。');
    }

}
