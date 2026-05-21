@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center pt-20 min-h-screen">
    
    <div class="w-full max-w-2xl px-6">
        <h2 class="text-xl font-bold text-center mb-12">ログイン</h2>

        <form method="POST" action="{{ route('login') }}" novalidate onsubmit="this.querySelector('button[type=submit]').disabled=true;">
            @csrf

            <div class="mb-8">
                <label for="email" class="block font-bold mb-2 text-base">メールアドレス</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}"  class="w-full border border-gray-400 bg-white p-3 focus:outline-none focus:border-gray-600">
                
                @error('email')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-8">
                <label for="password" class="block font-bold mb-2 text-base">パスワード</label>
                <input type="password" id="password" name="password" required class="w-full border border-gray-400 bg-white p-3 focus:outline-none focus:border-gray-600">
                
                @error('password')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full bg-black hover:bg-gray-800 text-white font-bold py-3 transition mb-3">
                ログインする
            </button>

            <div class="text-center">
                <a href="{{ route('register') }}" class="text-blue-500 hover:text-blue-700 text-xs font-bold">
                    会員登録はこちら
                </a>
            </div>
        </form>
    </div>
</div>
@endsection