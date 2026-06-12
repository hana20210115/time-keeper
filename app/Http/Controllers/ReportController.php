<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * マイ勤怠レポート画面の表示と集計処理を行う
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $now = Carbon::now();
        $sixMonthsAgo = $now->copy()->subMonths(5)->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        $attendances = Attendance::with('rests')
            ->where('user_id', Auth::id())
            ->whereBetween('date', [$sixMonthsAgo->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
            ->get();

        $processedData = $attendances->map(function ($attendance) {
            $start = Carbon::parse($attendance->start_time);
            $end = $attendance->end_time ? Carbon::parse($attendance->end_time) : $start->copy();
            $workMinutes = $start->diffInMinutes($end);

            $restMinutes = $attendance->rests->sum(function ($rest) {
                if (!$rest->end) return 0;
                return Carbon::parse($rest->start)->diffInMinutes(Carbon::parse($rest->end));
            });


            $actualWorkMinutes = max(0, $workMinutes - $restMinutes);

            return [
                'date' => Carbon::parse($attendance->date),
                'start_time' => $attendance->start_time,
                'end_time' => $attendance->end_time,
                'work_minutes' => $actualWorkMinutes,
            ];
        });


        $formatTime = function ($minutes) {
            $h = floor($minutes / 60);
            $m = round($minutes % 60);
            return "{$h}h {$m}m";
        };




        $totalWorkMinutes = $processedData->sum('work_minutes');
        
        $totalOvertimeMinutes = $processedData->sum(function ($item) {
            return $item['work_minutes'] > 480 ? $item['work_minutes'] - 480 : 0;
        });

        $averageWorkMinutes = $processedData->count() > 0 
            ? $totalWorkMinutes / $processedData->count() 
            : 0;

        $monthlyData = [];

        for ($i = 5; $i >= 0; $i--) {
            $monthStr = $now->copy()->subMonths($i)->format('Y-m');
            $monthlyData[$monthStr] = ['work_minutes' => 0, 'overtime_minutes' => 0];
        }


        $groupedByMonth = $processedData->groupBy(function ($item) {
            return $item['date']->format('Y-m');
        });


        foreach ($groupedByMonth as $month => $items) {
            if (isset($monthlyData[$month])) {
                $monthlyData[$month]['work_minutes'] = $items->sum('work_minutes');
                $monthlyData[$month]['overtime_minutes'] = $items->sum(function ($item) {
                    return $item['work_minutes'] > 480 ? $item['work_minutes'] - 480 : 0;
                });
            }
        }


        $formattedMonthlyData = [];
        foreach ($monthlyData as $month => $data) {
            $formattedMonthlyData[] = [
                'month' => $month,
                'work_time' => $formatTime($data['work_minutes']),
                'overtime' => $formatTime($data['overtime_minutes']),
            ];
        }

        
        $thisMonthData = $processedData->filter(function ($item) use ($now) {
            return $item['date']->isSameMonth($now);
        });

        $lateCount = $thisMonthData->filter(function ($item) {
            if (!$item['start_time']) return false;
            return Carbon::parse($item['start_time'])->gt(Carbon::createFromTimeString('09:00:00'));
        })->count();

        $earlyLeaveCount = $thisMonthData->filter(function ($item) {
            if (!$item['end_time']) return false;
            return Carbon::parse($item['end_time'])->lt(Carbon::createFromTimeString('18:00:00'));
        })->count();

        $overworkCount = $thisMonthData->filter(function ($item) {
            return $item['work_minutes'] > 600;
        })->count();

        $viewData = [
            'total_work_time' => $formatTime($totalWorkMinutes),
            'total_overtime' => $formatTime($totalOvertimeMinutes),
            'average_work_time' => $formatTime($averageWorkMinutes),
            'monthly_data' => $formattedMonthlyData,
            'late_count' => $lateCount,
            'early_leave_count' => $earlyLeaveCount,
            'overwork_count' => $overworkCount,
        ];

        return view('attendance.report', compact('viewData'));
    }
}