<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AttendanceRecordPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AttendanceRecord $attendanceRecord): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * 更新の認可
     * 
     * @param \App\Models\User $user
     * @param \App\Models\Attendance $attendance
     * @return \Illuminate\Auth\Access\Response
     */
    public function update(User $user, Attendance $attendance): Response
    {
        return $user->id === $attendance->user_id
        ? Response::allow()
        :Response::deny('この操作を実行する権限がありません。');
    }

    /**
     * 削除の認可
     * 
     * @param \App\Models\User $user
     * @param \App\Models\Attendance $attendance
     * @return \Illuminate\Auth\Access\Response
     */
    public function delete(User $user, Attendance $attendance): Response
    {
        return $user->id === $attendance->user_id
        ? Response::allow()
        : Response::deny('この操作を実行する権限がありません');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AttendanceRecord $attendanceRecord): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AttendanceRecord $attendanceRecord): bool
    {
        return false;
    }
}
