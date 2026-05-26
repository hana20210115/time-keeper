<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCorrectionRequest;
use App\Models\Attendance;
use App\Models\RestCorrection;
use App\Models\AttendanceCorrection;
use Illuminate\Http\Request;

class AttendanceCorrectionController extends Controller
{
    public function store(StoreCorrectionRequest $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        $correction = AttendanceCorrection::updateOrCreate(
            ['attendance_id' => $id],
            [
            'user_id' => auth()->id(),
            'date' => $attendance->date,
            'start' =>$request->input('start_time'),
            'end' => $request->input('end_time'),
            'reason' => $request->input('reason'),
            'status' => AttendanceCorrection::STATUS_PENDING,
            ]
            
        );

        if ($request->has('rests'))
        {
            foreach ($request->input('rests') as $restId => $restData)
            {
                
            
            
                if (is_numeric($restId))
                {
                    RestCorrection::updateOrCreate(
                    [
                        'attendance_correction_id' => $correction->id,
                        'rest_id' => $restId
                    ],
                    [
                        'start' => $restData['start'],
                        'end' => $restData['end'],
                    ]
                    );
                }
                else
                {
                    RestCorrection::create([
                        'attendance_correction_id' => $correction->id,
                        'rest_id' =>null,
                        'start' => $restData['start'],
                        'end' => $restData['end'],
                    ]);




            
                }
            }
        }
         return redirect()->route('attendance.detail', ['id' => $id])->with('success', '修正申請を送信しました。');
    }


    public function index(Request $request)
    {
        $userId = auth()->id();

        $activeTab =$request->query('tab','pending');

        $pendingCorrections = AttendanceCorrection::with(['user','attendance'])
            ->where('user_id',$userId)
            ->where('status',AttendanceCorrection::STATUS_PENDING)
            ->orderBy('created_at','desc')
            ->get();

        $approvedCorrections = AttendanceCorrection::with(['user','attendance'])
            ->where('user_id',$userId)
            ->where('status',AttendanceCorrection::STATUS_APPROVED)
            ->orderBy('created_at','desc')
            ->get();

        return view('correction.list',compact('pendingCorrections','approvedCorrections','activeTab'));




    }
}
