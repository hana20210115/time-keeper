<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;
use Carbon\Carbon;
class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {   
        

        $user =User::first();
        $start =Carbon::now()->subMonths(6)->startOfMonth();
        $end = Carbon::now()->addMonths(6)->endOfMonth();


        $faker = Faker::create('ja_JP');

        for ($date =$start->copy(); $date->lte($end); $date->addDay()) {
            if ($date->isWeekend()){
                continue;
            }
            if ($date->isToday()) {
                continue;
            }
        
        $startHour = $faker->numberBetween(8,9);
        $startMinute = $faker->numberBetween(0,59);
        $startTime = $date->format('Y-m-d') . sprintf(' %02d:%02d:00',$startHour, $startMinute);

        $endHour = $faker->numberBetween(17,22);
        $endMin = $faker->numberBetween(0,59);
        $endTime = $date->format('Y-m-d') . sprintf(' %02d:%02d:00', $endHour, $endMin);


        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $date->format('Y-m-d'),
            'start_time' => $startTime,
            'end_time' => $endTime,
        ]);

        $breakCount = $faker->numberBetween(1, 4);

        if ($breakCount >  1) {
            Rest::create([
                'attendance_id' => $attendance->id,
                'start' => $date->format('Y-m-d') . ' 12:00:00',
                'end' =>$date->format('Y-m-d') . ' 13:00:00',
            ]);
        }
        if ($breakCount >=  2) {
            Rest::create([
                'attendance_id' => $attendance->id,
                'start' => $date->format('Y-m-d') . ' 15:00:00',
                'end' =>$date->format('Y-m-d') . ' 15:20:00',
            ]);
        }
        if ($breakCount >= 3 && $endHour >= 19) {
            Rest::create([
                'attendance_id' => $attendance->id,
                'start' => $date->format('Y-m-d') . ' 18:00:00',
                'end' =>$date->format('Y-m-d') . ' 18:20:00',
            ]);
        }
        if ($breakCount >= 4 && $endHour >= 22) {
            Rest::create([
                'attendance_id' => $attendance->id,
                'start' => $date->format('Y-m-d') . ' 21:00:00',
                'end' =>$date->format('Y-m-d') . ' 21:20:00',
            ]);
        }



        }

    }
}
