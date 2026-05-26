<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceCorrectionController;

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

    
});