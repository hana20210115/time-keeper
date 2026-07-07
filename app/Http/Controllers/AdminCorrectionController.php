<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceCorrection; 
use Carbon\Carbon;
use App\Models\Rest;
use App\Models\RestCorrection;

class AdminCorrectionController extends Controller
{
    public function index(Request $request)
    {
        $statusTab = $request->query('tab', 'pending');

        $query = AttendanceCorrection::with(['attendance.user']);

        
        if ($statusTab === 'approved') {
            
            $query->where('status', '1'); 
        } else {
        
            $query->where('status', '0'); 
        }


        $corrections = $query->orderBy('created_at', 'desc')->get();

        foreach ($corrections as $correction) {

            $correction->formatted_target_date = Carbon::parse($correction->attendance->date)->format('Y/m/d');
            $correction->formatted_apply_date = Carbon::parse($correction->created_at)->format('Y/m/d');
        }

        
        return view('admin.correction_list', compact('corrections', 'statusTab'));
    }

    public function show($id)
    {
        $correction = AttendanceCorrection::with(['attendance.user','restCorrections'])->findOrFail($id);

        $correction->formatted_date = Carbon::parse($correction->attendance->date)->format('Y年n月j日');

        $correction->formatted_start_time = $correction->start ? Carbon::parse($correction->start)->format('H:i') : '';

        $correction->formatted_end_time = $correction->end ? carbon::parse($correction->end)->format('H:i') : '';

        foreach($correction->restCorrections as $rest){
            $rest->formatted_start = $rest->start ? Carbon::parse($rest->start)->format('H:i') : '';
            $rest->formatted_end =$rest->end ? Carbon::parse($rest->end)->format('H:i') : '';

        }

        return view('admin.correction_detail',compact('correction'));


    }

    public function approve($id)
    {


        $correction = AttendanceCorrection::with('attendance')->findOrFail($id);

        if ($correction->status == 1){
            return back();
        }

        \DB::transaction(function () use ($correction){
            $correction->attendance->update([
                'start_time' => $correction->start,
                'end_time' => $correction->end,
            ]);

            $restCorrections = RestCorrection::where('attendance_correction_id',$correction->id)->get();

            foreach ($restCorrections as $restCorrection){
                if($restCorrection->rest_id){
                    Rest::where('id', $restCorrection->rest_id)->update([
                        'start' =>$restCorrection->start,
                        'end' =>$restCorrection->end,
                    ]);
                } else {
                    Rest::create([
                        'attendance_id' => $correction->attendance_id,
                        'start' => $restCorrection->start,
                        'end' => $restCorrection->end,
                    ]);
                }

                $restCorrection->update([
                    'status' => 1
                ]);

            
        }

            $correction->update([
                'status' => 1

            ]);
        });

        return redirect()->route('admin.correction_request_detail',['id' => $id]);
    }
}