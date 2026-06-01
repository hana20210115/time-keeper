<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdminStaffController extends Controller
{
    public function index()
    {
        $staffs= User::where('role',0)->get();
        return view('admin.staff_list',compact('staffs'));
    }
}
