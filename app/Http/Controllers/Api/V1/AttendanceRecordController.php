<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\Api\V1\StoreAttendanceRecordRequest;
use App\Http\Resources\AttendanceRecordResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Models\Attendance;
use App\Http\Requests\Api\V1\IndexAttendanceRecordRequest;
use App\Http\Requests\Api\V1\UpdateAttendanceRecordRequest;


class AttendanceRecordController extends Controller
{
    /**
     * 勤怠レコードの一覧を検索・絞り込み・ページネーション付きで取得する
     * 
     * @param IndexAttendanceRecordRequest $request
     * @return AnonymousResourceCollection
     */

    use AuthorizesRequests;
    public function index(IndexAttendanceRecordRequest $request): AnonymousResourceCollection
    {
    

        $query = Attendance::query()->where('user_id',auth()->id());

        if($request->filled('user_id')){
            $query->where('user_id',$request->user_id);
        }

        if ($request->filled('date')){
            $query->whereDate('date',$request->date);
        }

        if ($request->filled('month')){
            $query->where('date','like',$request->month.'%');
        }

        $perPage = $request->input('per_page',20);
        $attendances = $query->paginate($perPage);

        return AttendanceRecordResource::collection($attendances);
    
    }


    /**
     * 勤怠レコードを新規作成する
     * 
     * ログインユーザーの勤怠情報を登録し、
     * 休憩および修正申請の関連情報を含めて返却する。
     * @param StoreAttendanceRecordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreAttendanceRecordRequest $request)
    {   
        $validated = $request->validated();

        $attendance = $request->user()->attendances()->create($validated);

        $attendance->load(['rests', 'attendanceCorrections']);

        return (new AttendanceRecordResource($attendance))
            ->response()
            ->setStatusCode(201);

        
    }

    /**
     * 指定された勤怠レコードの詳細（休憩・修正申請含む）を取得する
     * 
     * @param int $id
     * @return 
     */
    public function show(int $id)
    {
        $attendance = Attendance::find($id);

        if (!$attendance){
            return response()->json(['error'=> '勤怠情報が見つかりませんでした。'],404);
        }

        $attendance -> load(['rests','attendanceCorrections']);

        return new AttendanceRecordResource($attendance);
        
    }

    /**
     * 勤怠レコードを更新する
     * 
     * @param  UpdateAttendanceRecordRequest $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateAttendanceRecordRequest $request, string $id):\Illuminate\Http\JsonResponse
    {
        $attendance = Attendance::findOrFail($id);

        $this->authorize('update',$attendance);

        $validated = $request->validated();

        $attendance->update($validated);

        $attendance->load(['rests','attendanceCorrections']);

        return(new AttendanceRecordResource($attendance))->response()->setStatusCode(200);
    }

    /**
     * 指定された勤怠レコードを削除する
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $id):\Illuminate\Http\Response
    {
        $attendance = Attendance::findOrFail($id);

        $this->authorize('delete',$attendance);

        $attendance->delete();

        return response()->noContent();
    }
}
