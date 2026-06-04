<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\AttendanceCorrection; 
use Carbon\Carbon;

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
}