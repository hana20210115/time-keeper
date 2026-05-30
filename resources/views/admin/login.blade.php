@extends('layouts.guest')

@section('content')
<div class="max-w-xl mx-auto w-full pt-16 px-4">

    <h1 class="text-2xl font-bold text-center mb-12 tracking-wider">
        管理者ログイン
    </h1>

    <form method="POST" action="{{route('login')}}" novalidate>
        @csrf
        <input type="hidden" name="login_type" value="admin">

        <div class="mb-6">
            <label for="email" class="block text-sm font-bold mb-2">
                メールアドレス
            </label>

            <input
                type="email"
                name="email"
                id="email"
                class="w-full border border-gray-400 rounded-sm px-4 py-3 focus:outline-none focus:border-black transition-colors"
                required
                autofocus
            >
            
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-10">
            <label for="password" class="block text-sm font-bold mb-2">
                パスワード
            </label>
            <input
                type="password"
                name="password"
                id="password"
                class="w-full border border-gray-400 rounded-sm px-4 py-3 focus:outline-none focus:border-black transition-colors"
                required
            >
            @error('password')
                <p class="text-red-500 text-sm my-1">{{ $message }}</p>
            @enderror
        </div>

        <button
            type="submit"
            class="w-full bg-black text-white font-bold py-4 rounded-sm hover:opacity-80 transition-opacity"
        >
            管理者ログインする
        </button>
    </form>
</div>
@endsection