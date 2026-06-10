@extends('layouts.admin')

@section('content')
<div class="bg-gray-100 min-h-screen py-12 px-4 font-sans">
    <div class="max-w-4xl mx-auto">
        
        <div class="flex items-center mb-8">
            <div class="w-1.5 h-6 bg-black mr-4"></div>
            <h1 class="text-xl font-bold text-black tracking-widest">勤怠詳細</h1>
        </div>

        <form action="{{ route('admin.correction_request_approve', ['id' => $correction->id]) }}" method="POST">
            @csrf

            <div class="bg-white rounded border border-gray-200 mb-2 overflow-hidden">
                
                <div class="flex border-b border-gray-200 px-10 py-7 items-center">
                    <div class="w-[30%] text-gray-700 font-medium tracking-[0.2em] text-sm">名前</div>
                    <div class="w-[70%] text-black font-medium tracking-[0.2em] text-sm">
                        {{ $correction->attendance->user->name }}
                    </div>
                </div>

                <div class="flex border-b border-gray-200 px-10 py-7 items-center">
                    <div class="w-[30%] text-gray-700 font-medium tracking-[0.2em] text-sm">日付</div>
                    <div class="w-[70%] flex gap-10 text-black font-medium tracking-[0.2em] text-sm">
                        <span>{{ $correction->formatted_date }}</span>
                    </div>
                </div>

                <div class="flex border-b border-gray-200 px-10 py-7 items-start">
                    <div class="w-[30%] text-gray-700 font-medium tracking-[0.2em] text-sm">出勤・退勤</div>
                    <div class="w-[70%]">
                        <div class="flex items-center gap-6">
                            <div class="w-36 text-center text-sm font-medium tracking-wider text-black">
                                {{ $correction->formatted_start_time }}
                            </div>
                            <span class="text-black font-bold">〜</span>
                            <div class="w-36 text-center text-sm font-medium tracking-wider text-black">
                                {{ $correction->formatted_end_time }}
                            </div>
                        </div>
                    </div>
                </div>

                @foreach($correction->restCorrections as $index => $rest)
                <div class="flex border-b border-gray-200 px-10 py-7 items-start">
                    <div class="w-[30%] text-gray-700 font-medium tracking-[0.2em] text-sm">
                        休憩{{ $index > 0 ? $index + 1 : '' }}
                    </div>
                    <div class="w-[70%]">
                        <div class="flex items-center gap-6">
                            <div class="w-36 text-center text-sm font-medium tracking-wider text-black">
                                {{ $rest->formatted_start }}
                            </div>
                            <span class="text-black font-bold">〜</span>
                            <div class="w-36 text-center text-sm font-medium tracking-wider text-black">
                                {{ $rest->formatted_end }}
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

                <div class="flex px-10 py-7 items-start">
                    <div class="w-[30%] text-gray-700 font-medium tracking-[0.2em] text-sm">備考</div>
                    <div class="w-[70%]">
                        <div class="w-full max-w-lg text-sm text-black font-medium tracking-wider leading-relaxed">
                            {!! nl2br(e($correction->reason ?? '')) !!}
                        </div>
                    </div>
                </div>

            </div>

            <div class="flex justify-end mt-8">
                @if($correction->status == 0)
                    <button type="submit" class="bg-black text-white px-10 py-3 rounded text-sm font-bold tracking-[0.3em] hover:bg-gray-800 transition-colors">
                        承認
                    </button>
                @else
                    <button type="button" class="bg-gray-400 text-white px-10 py-3 rounded text-sm font-bold tracking-[0.3em] cursor-not-allowed" disabled>
                        承認済み
                    </button>
                @endif
            </div>

        </form>
    </div>
</div>
@endsection