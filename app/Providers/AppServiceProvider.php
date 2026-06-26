<?php

namespace App\Providers;

use App\Models\Attendance;
use App\Policies\AttendanceRecordPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
    
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        Gate::policy(Attendance::class, AttendanceRecordPolicy::class);


        View::composer('layouts.app', function ($view) {
            $currentStatus = 0;
        
            if(auth()->check()) {
               $todayAttendance = Attendance::query()
                    ->where('user_id', auth()->id())
                    ->where('date', now()->toDateString())
                    ->first();

                $currentStatus = $todayAttendance ? $todayAttendance->status : 0;
            }

            $view->with('currentStatus', $currentStatus);
        });
    }
}