<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Request\API\V1\StoreAttendanceRecordRequest;
use Illuminate\Http\Request;
use App\Http\Resources\AttendanceRecordResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Models\Attendance;

class AttendanceRecordController extends Controller
{
    /**
     * 勤怠レコードの一覧を検索・絞り込み・ページネーション付きで取得する
     * 
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Attendance::query();

        if ($request->has('user_id')){
            $query->where('user_id',$request->user_id);
        }
        if ($request->has('date')){
            $query->where('date',$request->date);
        }
        if ($request->has('month')){
            $query->where('date','like',$request->month . '%');
        }
        $perPage = $request->input('per_page',20);
        $records = $query->paginate($perPage);

        return AttendanceRecordResource::collection($records);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAttendanceRecordRequest $request)
    {   
        $validated = $request->Validated();

        $attendance = $request->user()->attendance()->create($validated);

        $attendance->load(['rests', 'attendanceCorrections']);

        return (new AttendanceRecordResource($attendance))
            response()
            satStatusCode(201);

        
    }

    /**
     * 指定された勤怠レコードの詳細（休憩・修正申請含む）を取得する
     * 
     * @param int $id
     * @return AttendanceRecordResource
     */
    public function show(int $id):AttendanceRecordResource
    {
        $attendance = Attendance::findOrFail($id);

        $attendance -> load(['rests','attendanceCorrections']);

        return new AttendanceRecordResource($attendance);
        
    }

    /**
     * 
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
