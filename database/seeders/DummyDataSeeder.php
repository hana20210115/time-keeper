<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $user1 = User::firstOrCreate(['email' => 'user1@example.com'], [
            'name' => 'ユーザー1', 'password' => Hash::make('password'), 'role' => 0, 'email_verified_at' => now()
        ]);
        $user2 = User::firstOrCreate(['email'=> 'user2@example.com'], [
            'name' => 'ユーザー2', 'password' => Hash::make('password'), 'role' => 0, 'email_verified_at' => now()
        ]);
        $user3 = User::firstOrCreate(['email' => 'user3@example.com'], [
            'name' => 'ユーザー3(管理者)', 'password' => Hash::make('password'), 'role' => 1, 'email_verified_at' => now()
        ]);

        $now = Carbon::now();

        $this->generateUserAttendances($user1->id, 5, 15, 17, true, $now);
        
        //$this->generateUserAttendances($user2->id, 3, 10, 10, false, $now);
        //$this->generateUserAttendances($user3->id, 1, 5, 5, false, $now);
    }

    private function generateUserAttendances($userId, $monthsBack, $pastDays, $currentDays, $useComplexPattern, $now)
    {
        for ($i = $monthsBack; $i >= 1; $i--) {
            $pastMonth = $now->copy()->subMonths($i)->startOfMonth();
            $daysAdded = 0;

            for($day = 1; $day <= $pastMonth->daysInMonth; $day++) {
                $currentDay = $pastMonth->copy()->addDays($day - 1);
                if($currentDay->isWeekday() && $daysAdded < $pastDays) {
                    $this->createAttendanceRecord($userId, $currentDay, '09:00', '18:00');
                    $daysAdded++;
                }
            }
        }

        $targetMonth = $now->copy()->startOfMonth();
        $daysAdded = 0;
        
        $complexPatterns = array_merge(
            array_fill(0, 10, ['start' => '09:00', 'end' => '18:00']),
            array_fill(0, 3,  ['start' => '09:00', 'end' => '20:00']),
            array_fill(0, 2,  ['start' => '09:30', 'end' => '18:00']),
            array_fill(0, 1,  ['start' => '09:00', 'end' => '17:00']),
            array_fill(0, 1,  ['start' => '08:00', 'end' => '21:00'])
        );

        for($day = 1; $day <= $targetMonth->daysInMonth; $day++) {
            $currentDay = $targetMonth->copy()->addDays($day - 1);
            if($currentDay->isWeekday() && $daysAdded < $currentDays) {
                
                if ($useComplexPattern) {
                    $pattern = $complexPatterns[$daysAdded];
                    $this->createAttendanceRecord($userId, $currentDay, $pattern['start'], $pattern['end']);
                } else {
                    $this->createAttendanceRecord($userId, $currentDay, '09:00', '18:00');
                }
                $daysAdded++;
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
            'status' => 3,
        ]);
        Rest::create([
            'attendance_id' => $attendance->id,
            'start' => $dateStr . ' 12:00:00',
            'end' => $dateStr . ' 13:00:00',
        ]);
    }
}