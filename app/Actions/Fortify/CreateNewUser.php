<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Fortify\Fortify;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     *
     * @throws ValidationException
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:20'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'お名前を入力して下さい。',
            'name.string' => 'お名前は文字列で入力して下さい。',
            'name.max' => 'お名前は20文字以内で入力して下さい。',
            'email.required' => 'メールアドレスを入力して下さい。',
            'email.email' => '有効なメールアドレスを入力して下さい。',
            'email.max' => 'メールアドレスは255文字以内で入力して下さい。',
            'email.unique' => 'このメールアドレスは既に使用されています。',
            'password.required' => 'パスワードを入力して下さい。',
            'password.string' => 'パスワードは文字列で入力して下さい。',
            'password.min' => 'パスワードは8文字以上で入力して下さい。',
            'password.confirmed' => 'パスワードと一致しません。',
        ])->validate();

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
