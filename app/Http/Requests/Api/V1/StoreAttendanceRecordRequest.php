<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAttendanceRecordRequest extends FormRequest
{
    /**
     * ユーザーがこのリクエストを実行する権限があるか判断する
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * 送られてきたデータに適用するバリデーション（チェック）ルール
     */
    public function rules(): array
    {
        return [
            'date' =>[
                'required',
                'date',
                Rule::unique('attendances')->where(function ($query){
                    return $query->where('user_id',auth()->id());
                }),
            ],
            'start_time' => ['required','date_format:H:i:s'],
            'end_time' => ['nullable', 'date_format:H:i:s','after:start_time'],
            
            
        ];
    }

    public function messages(): array
    {
        return[
            'date.required' =>'勤怠日は必須です。',
            'date.date' =>'正しい日付の形式で入力して下さい',
            'date.unique' =>'この日付の勤怠はすでに登録されております',
            'start_time.required' => '出勤時刻は必須です',
            'start_time.date_format' => '出勤時刻はHH:MM:SSの形式で入力して下さい',
            'end_time.date_format' => '退勤時間はHH:MM:SSの形式で入力して下さい',
            'end_time.after' => '退勤時刻は出勤時刻より後の時間を入力して下さい',
            
        ];
    }
}
