<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceCorrectionController;
use App\Http\Controllers\AdminAttendanceController;
use App\Http\Controllers\AdminStaffController;
use App\Http\Controllers\AdminCorrectionController;


Route::get('/', function () {return view('auth.login');});


Route::middleware(['auth', 'verified'])->group(function () {
    
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/start', [AttendanceController::class, 'start'])->name('attendance.start');
    Route::post('/attendance/end', [AttendanceController::class, 'end'])->name('attendance.end');
    Route::get('/attendance/detail/{id}', [AttendanceController::class, 'detail'])->name('attendance.detail');
    Route::post('/attendance/correction/{id}', [AttendanceCorrectionController::class, 'store'])->name('attendance.correction.store');
    Route::post('/attendance/rest/start', [AttendanceController::class, 'restStart'])->name('attendance.rest.start');
    Route::post('/attendance/rest/end', [AttendanceController::class, 'restEnd'])->name('attendance.rest.end');
    Route::get('/attendance/list', [AttendanceController::class, 'list'])->name('attendance.list'); 
    Route::get('/stamp_correction_request/list',[AttendanceCorrectionController::class,'index'])->name('stamp_correction_request.list');
    
});

Route::middleware('guest')->get('/admin/login', function () {
    return view('admin.login');
})->name('admin.login');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/admin/attendance/list/{date?}', [AdminAttendanceController::class, 'index'])
        ->name('admin.attendance_list');
    Route::get('/admin/attendance/detail/{id}',[AdminAttendanceController::class, 'show'])->name('admin.attendance_detail');
    Route::post('/admin/attendance/detail/{id}/update',[AdminAttendanceController::class,'update'])->name('admin.attendance_update');
    Route::get('/admin/staff/list',[AdminStaffController::class,'index'])->name('admin.staff_list');
    Route::get('/admin/staff/detail/{id}',[AdminStaffController::class,'show'])->name('admin.staff_detail');
    Route::get('/admin/staff/detail/{id}/csv',[AdminStaffController::class,
    'exportCsv'])->name('admin.staff_detail_csv');
    Route::get('/admin/stamp_correction_request/list',[AdminCorrectionController::class,'index'])->name('admin.correction_request_list');
});