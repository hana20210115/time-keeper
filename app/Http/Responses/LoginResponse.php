<?php
namespace App\Http\Responses;


use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
class LoginResponse implements LoginResponseContract
{

    public function toResponse($request)
    {
        $user = Auth::user();

        
        if ($user->isAdmin()) {

            return redirect()->route('admin.attendance_list');
        }


        return redirect()->route('attendance.index');
    }
}