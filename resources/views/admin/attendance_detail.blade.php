@extends('layouts.admin')

@section('content')
<div class="bg-gray-100 min-h-screen py-12 px-4 font-sans">
    <div class="max-w-4xl mx-auto">
        
        <div class="flex items-center mb-8">
            <div class="w-1.5 h-6 bg-black mr-4"></div>
            <h1 class="text-xl font-bold text-black tracking-widest">勤怠詳細</h1>
        </div>

        <form action="{{ route('admin.attendance_update', ['id' => $attendance->id]) }}" method="POST">
            @csrf

            <div class="bg-white rounded border border-gray-200 mb-2 overflow-hidden">
                
                <div class="flex border-b border-gray-200 px-10 py-7 items-center">
                    <div class="w-[30%] text-gray-700 font-medium tracking-[0.2em] text-sm">名前</div>
                    <div class="w-[70%] text-black font-medium tracking-[0.2em] text-sm">
                        {{ $attendance->user->name }}
                    </div>
                </div>

                <div class="flex border-b border-gray-200 px-10 py-7 items-center">
                    <div class="w-[30%] text-gray-700 font-medium tracking-[0.2em] text-sm">日付</div>
                    <div class="w-[70%] flex gap-10 text-black font-medium tracking-[0.2em] text-sm">
                        <span>{{ $attendance->formatted_date_year }}</span>
                        <span>{{ $attendance->formatted_date_month_day }}</span>
                    </div>
                </div>

                <div class="flex border-b border-gray-200 px-10 py-7 items-start">
                    <div class="w-[30%] text-gray-700 font-medium tracking-[0.2em] text-sm {{ $isLocked ? '' : 'pt-3' }}">出勤・退勤</div>
                    <div class="w-[70%]">
                        <div class="flex items-center gap-6">
                            @if($isLocked)
                                <div class="w-36 text-center text-sm font-medium tracking-wider text-black">
                                    {{ $attendance->formatted_start_time }}
                                </div>
                                <span class="text-black font-bold">〜</span>
                                <div class="w-36 text-center text-sm font-medium tracking-wider text-black">
                                    {{ $attendance->formatted_end_time }}
                                </div>
                            @else
                                <input type="{{ old('start_time', $attendance->formatted_start_time) ? 'time' : 'text' }}" 
                                    onfocus="this.type='time'" onblur="if(!this.value) this.type='text'"
                                    name="start_time" 
                                    value="{{ old('start_time', $attendance->formatted_start_time) }}"
                                    class="border border-gray-200 rounded px-4 py-2 w-36 text-center text-sm font-medium tracking-wider focus:ring-black focus:border-black [&::-webkit-calendar-picker-indicator]:hidden">
                                <span class="text-black font-bold">〜</span>
                                <input type="{{ old('end_time', $attendance->formatted_end_time) ? 'time' : 'text' }}" 
                                    onfocus="this.type='time'" onblur="if(!this.value) this.type='text'"
                                    name="end_time" 
                                    value="{{ old('end_time', $attendance->formatted_end_time) }}"
                                    class="border border-gray-200 rounded px-4 py-2 w-36 text-center text-sm font-medium tracking-wider focus:ring-black focus:border-black [&::-webkit-calendar-picker-indicator]:hidden">
                            @endif
                        </div>
                        @if(!$isLocked && ($errors->has('start_time') || $errors->has('end_time')))
                            <p class="text-red-500 text-sm mt-3">{{ $errors->first('start_time') ?: $errors->first('end_time') }}</p>
                        @endif
                    </div>
                </div>

                @foreach($attendance->rests as $index => $rest)
                <div class="flex border-b border-gray-200 px-10 py-7 items-start">
                    <div class="w-[30%] text-gray-700 font-medium tracking-[0.2em] text-sm {{ $isLocked ? '' : 'pt-3' }}">
                        休憩{{ $index > 0 ? $index + 1 : '' }}
                    </div>
                    <div class="w-[70%]">
                        <div class="flex items-center gap-6">
                            @if($isLocked)
                                <div class="w-36 text-center text-sm font-medium tracking-wider text-black">
                                    {{ $rest->formatted_start }}
                                </div>
                                <span class="text-black font-bold">〜</span>
                                <div class="w-36 text-center text-sm font-medium tracking-wider text-black">
                                    {{ $rest->formatted_end }}
                                </div>
                            @else
                                <input type="{{ old('rests.'.$rest->id.'.start', $rest->formatted_start) ? 'time' : 'text' }}" 
                                    onfocus="this.type='time'" onblur="if(!this.value) this.type='text'"
                                    name="rests[{{ $rest->id }}][start]" 
                                    value="{{ old('rests.'.$rest->id.'.start', $rest->formatted_start) }}"
                                    class="border border-gray-200 rounded px-4 py-2 w-36 text-center text-sm font-medium tracking-wider focus:ring-black focus:border-black [&::-webkit-calendar-picker-indicator]:hidden">
                                <span class="text-black font-bold">〜</span>
                                <input type="{{ old('rests.'.$rest->id.'.end', $rest->formatted_end) ? 'time' : 'text' }}" 
                                    onfocus="this.type='time'" onblur="if(!this.value) this.type='text'"
                                    name="rests[{{ $rest->id }}][end]" 
                                    value="{{ old('rests.'.$rest->id.'.end', $rest->formatted_end) }}"
                                    class="border border-gray-200 rounded px-4 py-2 w-36 text-center text-sm font-medium tracking-wider focus:ring-black focus:border-black [&::-webkit-calendar-picker-indicator]:hidden">
                            @endif
                        </div>
                        @if(!$isLocked && ($errors->has("rests.{$rest->id}.start") || $errors->has("rests.{$rest->id}.end")))
                            <p class="text-red-500 text-sm mt-3">{{ $errors->first("rests.{$rest->id}.start") ?: $errors->first("rests.{$rest->id}.end") }}</p>
                        @endif
                    </div>
                </div>
                @endforeach

                @if($isLocked && $attendance->rests->isEmpty())
                <div class="flex border-b border-gray-200 px-10 py-7 items-center">
                    <div class="w-[30%] text-gray-700 font-medium tracking-[0.2em] text-sm">休憩</div>
                    <div class="w-[70%] text-gray-400 text-sm tracking-widest">休憩記録はありません</div>
                </div>
                @endif

                @if(!$isLocked)
                @php $nextRestNum = $attendance->rests->count() + 1; @endphp
                <div class="flex border-b border-gray-200 px-10 py-7 items-start">
                    <div class="w-[30%] text-gray-700 font-medium tracking-[0.2em] text-sm pt-3">
                        休憩{{ $nextRestNum > 1 ? $nextRestNum : '' }}
                    </div>
                    <div class="w-[70%]">
                        <div class="flex items-center gap-6">
                            <input type="{{ old('new_rest.start') ? 'time' : 'text' }}" 
                                onfocus="this.type='time'" onblur="if(!this.value) this.type='text'"
                                name="new_rest[start]" 
                                value="{{ old('new_rest.start') }}"
                                class="border border-gray-200 rounded px-4 py-2 w-36 text-center text-sm font-medium tracking-wider focus:ring-black focus:border-black [&::-webkit-calendar-picker-indicator]:hidden">
                            <span class="text-black font-bold">〜</span>
                            <input type="{{ old('new_rest.end') ? 'time' : 'text' }}" 
                                onfocus="this.type='time'" onblur="if(!this.value) this.type='text'"
                                name="new_rest[end]" 
                                value="{{ old('new_rest.end') }}"
                                class="border border-gray-200 rounded px-4 py-2 w-36 text-center text-sm font-medium tracking-wider focus:ring-black focus:border-black [&::-webkit-calendar-picker-indicator]:hidden">
                        </div>
                        @if($errors->has('new_rest.start') || $errors->has('new_rest.end'))
                            <p class="text-red-500 text-sm mt-3">{{ $errors->first('new_rest.start') ?: $errors->first('new_rest.end') }}</p>
                        @endif
                    </div>
                </div>
                @endif
                
                <div class="flex px-10 py-7 items-start">
                    <div class="w-[30%] text-gray-700 font-medium tracking-[0.2em] text-sm {{ $isLocked ? '' : 'pt-3' }}">備考</div>
                    <div class="w-[70%]">
                        @if($isLocked)
                            <div class="w-full max-w-lg text-sm text-black font-medium tracking-wider leading-relaxed">
                                {!! nl2br(e($attendance->reason ?? '')) !!}
                            </div>
                        @else
                            <textarea name="reason" rows="3" 
                                class="w-full max-w-lg border border-gray-200 rounded p-3 text-sm focus:ring-black focus:border-black">{{ old('reason', $attendance->reason ?? '') }}</textarea>
                            @error('reason')
                                <p class="text-red-500 text-sm mt-3">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>
                </div>

            </div>

            @if(session('success'))
                <div class="text-right mt-3 mb-8">
                    <span class="text-[#16a34a] text-sm font-bold tracking-widest">{{ session('success') }}</span>
                </div>
            @elseif($isPending)
                <div class="text-right mt-3 mb-8">
                    <span class="text-[#f87171] text-sm font-bold tracking-widest">*承認待ちのため修正はできません。</span>
                </div>
            @else
                <div class="flex justify-end mt-8">
                    <button type="submit" class="bg-black text-white px-10 py-3 rounded text-sm font-bold tracking-[0.3em] hover:bg-gray-800 transition-colors">
                        修正
                    </button>
                </div>
            @endif

        </form>
    </div>
</div>
@endsection