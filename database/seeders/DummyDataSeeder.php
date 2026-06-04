<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $user1 = User::firstOrCreate
        (['email' => 'user1@example.com'],
        ['name' => 'ユーザー1',
        'password' => Hash::make('password'),
        'email_verified_at' => now(),
        'role' => 0,
        ]);
        
        $user2 = User::firstOrCreate
        (['email'=> 'user2@example.com'],
        ['name' => 'ユーザー2',
        'password' => Hash::make('password'),
        'email_verified_at' => now(),
        'role' => 0,
        ]);

        $user3 = User::firstOrCreate
        (['email' => 'user3@example.com'],
        ['name' => 'ユーザー3(管理者)',
        'password' => Hash::make('password'),
        'email_verified_at' => now(),
        'role' => 1,
        ]);

        $now = Carbon::now();
        $targetMonth = $now->copy()->startOfMonth();


        for ($i = 5; $i >= 1; $i--){
            $pastMonth = $now->copy()->subMonths($i)->startOfMonth();
            $daysAdded = 0;

            for($day = 1; $day <= $pastMonth->daysInMonth; $day++){
                $currentDay = $pastMonth->copy()->addDays($day - 1);

                if($currentDay->isWeekday() && $daysAdded < 15){
                    $this->createAttendanceRecord($user1->id, $currentDay, '09:00', '18:00');
                    $daysAdded++;
                }
            }
        }


        $currentMonthPatterns = array_merge(
            array_fill(0, 10, ['start' => '09:00', 'end' => '18:00']), // 通常
            array_fill(0, 3,  ['start' => '09:00', 'end' => '20:00']), // 残業
            array_fill(0, 2,  ['start' => '09:30', 'end' => '18:00']), // 遅刻
            array_fill(0, 1,  ['start' => '09:00', 'end' => '17:00']), // 早退
            array_fill(0, 1,  ['start' => '08:00', 'end' => '21:00'])  // 長時間
        );

        $daysAdded = 0;
        for($day = 1; $day <= $targetMonth->daysInMonth; $day++){
            $currentDay = $targetMonth->copy()->addDays($day - 1);

            if($currentDay->isWeekday() && $daysAdded < 17){
                $pattern = $currentMonthPatterns[$daysAdded];
                $this->createAttendanceRecord($user1->id, $currentDay, $pattern['start'], $pattern['end']);
                $daysAdded++;
            }
        }
        

        for ($day = 1; $day <= 5; $day++){
            $dummyDay = $targetMonth->copy()->addDays($day - 1);

            if($dummyDay->isWeekday()){
                $this->createAttendanceRecord($user2->id, $dummyDay, '09:00', '18:00');
                $this->createAttendanceRecord($user3->id, $dummyDay, '09:00', '18:00');
            }
        }
    }


    private function createAttendanceRecord($userId, $date, $startTime, $endTime)
    {
        $dateStr = $date->format('Y-m-d');

        $attendance = Attendance::create([
            'user_id' => $userId,
            'date' => $dateStr,
            'start_time' => $dateStr . ' ' . $startTime . ':00',
            'end_time' => $dateStr . ' ' . $endTime . ':00',
        ]);

        Rest::create([
            'attendance_id' => $attendance->id,
            'start' => $dateStr . ' 12:00:00',
            'end' => $dateStr . ' 13:00:00', 
        ]);
    }
}
