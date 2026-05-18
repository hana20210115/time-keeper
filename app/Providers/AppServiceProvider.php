<?php

namespace App\Providers;
use Laravel\Fortify\Fortify;
use App\Models\Attendance;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        View::composer('layouts.app',function ($view) {
            $currentStatus= 0;
        


        if(auth()->check()) {
           $todayAttendance = Attendance::query()
                ->where('user_id', auth()->id())
                ->where('date', now()->toDateString())
                ->first();

            $currentStatus = $todayAttendance  ? $todayAttendance->status : 0;
        
            }

            $view->with('currentStatus', $currentStatus);
        });
    
    
    
    
    }
}
