<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAttendanceRecordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $attendanceId = $this->route('attendanceRecord');

        return [
            'date' =>[
                'nullable',
                'date',
                Rule::unique('attendances')->where(function ($query){
                    return $query->where('user_id',auth()->id());
                })->ignore($attendanceId),
            ],
            'start_time' =>['nullable','date_format:H:i:s'],
            'end_time' => ['nullable','date_format:H:i:s','after:start_time'],
        ];
    }

    public function messages():array
    {
        return [
            
            'date.date' =>'正しい日付の形式で入力して下さい',
            'date,unique' =>'この日付の勤怠はすでに登録されております',
            'start_time.required' =>'出勤時間は必須です',
            'start_time.date_format' =>'出勤時間はHH:MM:SSの形式で入力して下さい',
            'end_time'.'date_format' => '退勤時間はHH:MM:SSの形式で入力して下さい',
        ];
    }

}
