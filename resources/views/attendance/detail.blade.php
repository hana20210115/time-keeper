@extends('layouts.app')

@section('content')

<div class="max-w-5xl mx-auto w-full">
    
    <div class="flex items-center mb-6">
        <div class="w-1.5 h-6 bg-black mr-3"></div>
        <h2 class="text-xl font-bold tracking-widest text-black">勤怠詳細</h2>
    </div>

    <form action="{{ route('attendance.correction.store', ['id' => $viewData['id']]) }}" method="POST">
        @csrf

        <div class="bg-white rounded-md shadow-sm border border-gray-100 mb-6 overflow-hidden">
            
            <table class="w-full border-collapse">
                <tr class="border-b border-gray-200">
                    <th class="py-8 pl-12 pr-4 text-left font-medium text-gray-700 w-1/4 whitespace-nowrap">名前</th>
                    <td class="py-8 pr-12 pl-4 text-gray-800 font-medium">{{ $viewData['name'] }}</td>
                </tr>

                <tr class="border-b border-gray-200">
                    <th class="py-8 pl-12 pr-4 text-left font-medium text-gray-700 whitespace-nowrap">日付</th>
                    <td class="py-8 pr-12 pl-4 text-gray-800 font-medium">{{ $viewData['date_year'] }} {{ $viewData['date_md'] }}</td>
                </tr>

                <tr class="border-b border-gray-200">
                    <th class="py-8 pl-12 pr-4 text-left font-medium text-gray-700 whitespace-nowrap align-top">出勤・退勤</th>
                    <td class="py-8 pr-12 pl-4 text-gray-800">
                        @if($viewData['is_pending'])
                            {{ $viewData['start_time'] }} 〜 {{ $viewData['end_time'] }}
                        @else
                            <div class="flex items-center space-x-6">
                                <input type="{{ empty($viewData['start_time']) && empty(old('start_time')) ? 'text' : 'time' }}" name="start_time" value="{{ old('start_time', $viewData['start_time']) }}" onfocus="this.type='time'" onblur="if(!this.value) this.type='text'" class="border border-gray-200 rounded px-4 py-2 w-32 text-center focus:outline-none focus:ring-1 focus:ring-black">
                                <span class="font-bold">〜</span>
                                <input type="{{ empty($viewData['end_time']) && empty(old('end_time')) ? 'text' : 'time' }}" name="end_time" value="{{ old('end_time', $viewData['end_time']) }}" onfocus="this.type='time'" onblur="if(!this.value) this.type='text'" class="border border-gray-200 rounded px-4 py-2 w-32 text-center focus:outline-none focus:ring-1 focus:ring-black">
                            </div>
                            @if($errors->has('start_time') || $errors->has('end_time'))
                                <p class="text-red-500 text-sm mt-2 font-medium">
                                    {{ $errors->first('start_time') ?: $errors->first('end_time') }}
                                </p>
                            @endif
                        @endif

                    </td>
                </tr>
                
                @foreach($viewData['rests'] as $rest)
                    <tr class="border-b border-gray-200">
                        <th class="py-8 pl-12 pr-4 text-left font-medium text-gray-700 whitespace-nowrap align-top">{{ $rest['label'] }}</th>
                        <td class="py-8 pr-12 pl-4 text-gray-800">
                            @if($viewData['is_pending'])
                                {{ $rest['start'] }} 〜 {{ $rest['end'] }}
                            @else
                                <div class="flex items-center space-x-6">
                                    <input type="{{ empty($rest['start']) && empty(old('rests.'.$rest['id'].'.start')) ? 'text' : 'time' }}" name="rests[{{ $rest['id'] }}][start]" value="{{ old('rests.'.$rest['id'].'.start', $rest['start']) }}" onfocus="this.type='time'" onblur="if(!this.value) this.type='text'" class="border border-gray-200 rounded px-4 py-2 w-32 text-center focus:outline-none focus:ring-1 focus:ring-black">
                                    <span class="font-bold">〜</span>
                                    <input type="{{ empty($rest['end']) && empty(old('rests.'.$rest['id'].'.end')) ? 'text' : 'time' }}" name="rests[{{ $rest['id'] }}][end]" value="{{ old('rests.'.$rest['id'].'.end', $rest['end']) }}" onfocus="this.type='time'" onblur="if(!this.value) this.type='text'" class="border border-gray-200 rounded px-4 py-2 w-32 text-center focus:outline-none focus:ring-1 focus:ring-black">
                                </div>
                                @if($errors->has('rests.' . $rest['id'] . '.start') || $errors->has('rests.' . $rest['id'] . '.end'))
                                    <p class="text-red-500 text-sm mt-2 font-medium">
                                    {{ $errors->first('rests.' . $rest['id'] . '.start') ?: $errors->first('rests.' . $rest['id'] . '.end') }}
                                    </p>
                                @endif
                            @endif
                        </td>
                    </tr>
                @endforeach

                <tr>
                    <th class="py-8 pl-12 pr-4 text-left font-medium text-gray-700 align-top whitespace-nowrap">備考</th>
                    <td class="py-8 pr-12 pl-4 text-gray-800">
                        @if($viewData['is_pending'])
                            {{ $viewData['reason'] }}
                        @else
                            <textarea name="reason" class="w-full border border-gray-200 rounded p-4 h-24 resize-none focus:outline-none focus:ring-1 focus:ring-black">{{ old('reason', $viewData['reason']) }}</textarea>
                            @error('reason')
                                <p class="text-red-500 text-sm mt-2 font-medium">{{ $message }}</p>
                            @enderror
                        @endif
                    </td>
                </tr>
            </table>

        </div>

        <div class="flex justify-end">
            @if($viewData['is_pending'])
                <p class="text-red-500 font-medium">*承認待ちのため修正はできません。</p>
            @else
                <button type="submit" class="bg-black text-white font-bold py-3 px-12 rounded tracking-widest hover:bg-gray-800 transition">修正</button>
            @endif
        </div>

    </form>
</div>

@endsection