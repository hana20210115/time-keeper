<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attendance>
 */
class AttendanceFactory extends Factory
{
    
    public function definition(): array
    {
        return [
            'user_id' =>User::factory(),
            'date' =>now()->format('Y-m-d'),
            'start_time' =>'09:00:00',
            'end_time' => '18:00:00',
            'status'=>'1',
        ];
    }
}
