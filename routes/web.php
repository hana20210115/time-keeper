<?php

use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('auth.login');
});

Route::get('/attendance', function () {
    return view('attendance.index');
})->name('attendance.index');


