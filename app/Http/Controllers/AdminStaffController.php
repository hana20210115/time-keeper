<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Attendance;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminStaffController extends Controller
{
    public function index()
    {
        $staffs= User::where('role',0)->get();
        return view('admin.staff_list',compact('staffs'));
    }

    public function show(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $targetMonth = $request->query('month',Carbon::now()->format('Y-m'));
        $currentDate = Carbon::parse($targetMonth . '-01');

        $prevMonth = $currentDate->copy()->subMonth()->format('Y-m');
        $nextMonth = $currentDate->copy()->addMonth()->format('Y-m');
        $displayMonth = $currentDate->format('Y/m');
        
        $firstDay = $currentDate->copy()->startOfMonth();
        $lastDay = $currentDate->copy()->endOfMonth();

        $attendances = Attendance::with('rests')
            ->where('user_id', $id)
            ->whereBetween('date', [$firstDay->format('Y-m-d'), $lastDay->format('Y-m-d')])
            ->get()
            ->keyBy('date');

        $monthlyDate = [];
        $weekdays = ['日', '月', '火', '水', '木', '金', '土'];

        for ($date = $firstDay->copy(); $date->lte($lastDay); $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            $dayOfWeek = $weekdays[$date->dayOfWeek];
            $record = $attendances->get($dateStr);

            $startTime = '';
            $endTime = '';
            $restDisplay = '';
            $workDisplay = '';


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
                    $restDisplay = sprintf('%02d:%02d', $breakHours, $breakMins);
                } elseif ($record->start_time && $record->end_time) {
                    $restDisplay = '00:00';
                }

                if ($record->start_time && $record->end_time) {
                    $attendanceStart = Carbon::parse($record->start_time);
                    $attendanceEnd = Carbon::parse($record->end_time);
                    $totalStayMinutes = $attendanceStart->diffInMinutes($attendanceEnd);
                    $workMinutes = $totalStayMinutes - $totalBreakMinutes;

                    if ($workMinutes > 0) {
                        $workHours = floor($workMinutes / 60);
                        $workMins = $workMinutes % 60;
                        $workDisplay = sprintf('%02d:%02d', $workHours, $workMins);
                    } else {
                        $workDisplay = '00:00';
                    }
                }
            }


            $monthlyDate[] = [
                'date_display'   => $date->format('m/d') . '(' . $dayOfWeek . ')',
                'start_time'     => $startTime,
                'end_time'       => $endTime,
                'rest_time'      => $restDisplay,
                'work_time'      => $workDisplay,
                'attendances_id' => $record ? $record->id : null,
            ];
        }

        return view('admin.staff_attendance_list', compact('user','monthlyDate', 'displayMonth', 'prevMonth', 'nextMonth', 'targetMonth'));
    }
    
    public function exportCsv(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $targetMonth = $request->query('month', Carbon::now()->format('Y-m'));
        $currentDate = Carbon::parse($targetMonth . '-01');

        $firstDay = $currentDate->copy()->startOfMonth();
        $lastDay = $currentDate->copy()->endOfMonth();

        $attendances = Attendance::with('rests')
            ->where('user_id', $id)
            ->whereBetween('date', [$firstDay->format('Y-m-d'), $lastDay->format('Y-m-d')])
            ->get()
            ->keyBy('date');

        $response = new StreamedResponse(function () use ($firstDay, $lastDay, $attendances) {
            $stream = fopen('php://output', 'w');
            fwrite($stream, "\xEF\xBB\xBF");

            fputcsv($stream, ['日付', '出勤', '退勤', '休憩', '合計']); 

            $weekdays = ['日', '月', '火', '水', '木', '金', '土'];

            for ($date = $firstDay->copy(); $date->lte($lastDay); $date->addDay()) {
                $dateStr = $date->format('Y-m-d');
                $dayOfWeek = $weekdays[$date->dayOfWeek];
                $record = $attendances->get($dateStr);

                $startTime = '';
                $endTime = '';
                $restDisplay = '';
                $workDisplay = '';


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
                        $restDisplay = sprintf('%02d:%02d', $breakHours, $breakMins);
                    } elseif ($record->start_time && $record->end_time) {
                        $restDisplay = '00:00';
                    }

                    if ($record->start_time && $record->end_time) {
                        $attendanceStart = Carbon::parse($record->start_time);
                        $attendanceEnd = Carbon::parse($record->end_time);
                        $totalStayMinutes = $attendanceStart->diffInMinutes($attendanceEnd);
                        $workMinutes = $totalStayMinutes - $totalBreakMinutes;

                        if ($workMinutes > 0) {
                            $workHours = floor($workMinutes / 60);
                            $workMins = $workMinutes % 60;
                            $workDisplay = sprintf('%02d:%02d', $workHours, $workMins);
                        } else {
                            $workDisplay = '00:00';
                        }
                    }
                }

                fputcsv($stream, [
                    $date->format('m/d') . '(' . $dayOfWeek . ')',
                    $startTime,
                    $endTime,
                    $restDisplay,
                    $workDisplay,
                ]);
            }
            fclose($stream);
        });

        $filename = "{$user->name}_{$currentDate->format('Y年m月')}勤怠.csv";
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
        
        return $response;
    }
}