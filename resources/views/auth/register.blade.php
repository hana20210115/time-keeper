@extends('layouts.guest')

@section('content')

    <div class="flex justify-center items-center mt-10">
        <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-md border border-gray-200">

            <h1 class="text-2xl font-bold text-center mb-6">会員登録</h1>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="mb-4">
                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">名前</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}"  class="w-full border border-gray-300 rounded p-2">
                    @error('name')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-sm font-bold mb-2">メールアドレス</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" class="w-full border border-gray-300 rounded-md p-2">
                    @error('email')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-sm font-bold mb-2">パスワード</label>
                    <input id="password" type="password" name="password" class="w-full border border-gray-300 rounded-md p-2">
                    @error('password')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-8">
                    <label for="password_confirmation" class="block text-sm font-bold mb-2">パスワード確認</label>
                    <input id="password_confirmation" type="password" name="password_confirmation"class="w-full border border-gray-300 rounded p-2">
                </div>

                    <button type="submit"
                    class="w-full bg-black text-white font-bold py-3 rounded hover:bg-gray-800">
                    登録する
                    </button>
                
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-sm text-blue-500 hover:underline font-bold">ログインはこちら</a>
            </div>
        </div>
    </div>
@endsection