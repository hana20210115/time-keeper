@extends('layouts.guest')

@section('content')
<div class="flex flex-col items-center pt-20 min-h-screen">
    
    <div class="w-full max-w-2xl px-6">
        <h1 class="text-2xl font-bold text-center mb-12">会員登録</h1>

        <form method="POST" action="{{ route('register') }}" novalidate onsubmit="this.querySelector('button[type=submit]').disabled=true;">
            @csrf

            <div class="mb-8">
                <label for="name" class="block font-bold mb-2 text-base">名前</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" class="w-full border border-gray-400 bg-white p-3 focus:outline-none focus:border-gray-600">
                
                @error('name')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-8">
                <label for="email" class="block font-bold mb-2 text-base">メールアドレス</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" class="w-full border border-gray-400 bg-white p-3 focus:outline-none focus:border-gray-600">
                
                @error('email')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-8">
                <label for="password" class="block font-bold mb-2 text-base">パスワード</label>
                <input id="password" type="password" name="password" required class="w-full border border-gray-400 bg-white p-3 focus:outline-none focus:border-gray-600">
                
                @error('password')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-8">
                <label for="password_confirmation" class="block font-bold mb-2 text-base">パスワード確認</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required class="w-full border border-gray-400 bg-white p-3 focus:outline-none focus:border-gray-600">
            </div>

            <button type="submit" class="w-full bg-black hover:bg-gray-800 text-white font-bold py-3 transition mb-3">
                登録する
            </button>
                
            <div class="text-center">
                <a href="{{ route('login') }}" class="text-blue-500 hover:text-blue-700 text-xs font-bold">
                    ログインはこちら
                </a>
            </div>
        </form>
    </div>
</div>
@endsection