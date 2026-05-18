@extends('layouts.guest')

@section('content')

<div class="flex flex-col items-center justify-center min-h-[70vh] text-center px-4">
    <div class="mb-10 text-lg md:text-xl font-bold leading-relaxed text-gray-800">
        登録していただいたメールアドレスに認証メールを送信しました。<br>
        メール認証を完了してください。
    </div>

    <a href="https://mailtrap.io/inboxes" target="_blank"
       class="mb-6 px-10 py-4 bg-gray-200 border border-gray-400 text-black text-lg rounded shadow-sm hover:bg-gray-300 transition block no-underline">
        認証はこちらから
    </a>

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="text-blue-600 font-bold hover:underline bg-transparent border-none cursor-pointer">
            認証メールを再送する
        </button>
    </form>
</div>
@endsection