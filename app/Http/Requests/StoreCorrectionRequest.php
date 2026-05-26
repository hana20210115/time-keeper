<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCorrectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

   
    protected function prepareForValidation()
    {
        $rests = $this->input('rests', []);
        
    
        $filteredRests = array_filter($rests, function ($rest) {
            return !empty($rest['start']) || !empty($rest['end']);
        });

    
        $this->merge([
            'rests' => $filteredRests,
        ]);
    }

    public function rules(): array
    {
        return [
            'date' => ['sometimes', 'required', 'date', 'before_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i', 'before:end_time'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'rests.*.start' => ['required', 'date_format:H:i', 'after:start_time', 'before:end_time'],
            'rests.*.end' => ['required', 'date_format:H:i', 'after:start_time', 'before:end_time', 'after:rests.*.start'],
            'reason' => ['required', 'string', 'max:255'],
        ];
    }

    
    public function messages(): array
    {
        return [
            'start_time.required'    => '出勤時間もしくは退勤時間が不適切な値です',
            'start_time.date_format' => '出勤時間もしくは退勤時間が不適切な値です',
            'start_time.before'      => '出勤時間もしくは退勤時間が不適切な値です',

            'end_time.required'      => '出勤時間もしくは退勤時間が不適切な値です',
            'end_time.date_format'   => '出勤時間もしくは退勤時間が不適切な値です',
            'end_time.after'         => '出勤時間もしくは退勤時間が不適切な値です',

            'rests.*.start.required'    => '休憩時間が不適切な値です',
            'rests.*.start.date_format' => '休憩時間が不適切な値です',
            'rests.*.start.after'       => '休憩時間が不適切な値です',
            'rests.*.start.before'      => '休憩時間が不適切な値です',

            'rests.*.end.required'      => '休憩時間もしくは退勤時間が不適切な値です',
            'rests.*.end.date_format'   => '休憩時間もしくは退勤時間が不適切な値です',
            'rests.*.end.after'         => '休憩時間もしくは退勤時間が不適切な値です',
            'rests.*.end.before'        => '休憩時間もしくは退勤時間が不適切な値です',

            'reason.required' => '備考を記入して下さい',
            'reason.max'      => '備考は255文字以内で入力して下さい',
        ];
    }
}