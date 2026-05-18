<?php

use Illuminate\Support\Facades\Route;

Route::get('/home', function () {
    return '<h1>メール認証完了！マイページへようこそ！</h1>';})->name('home');



