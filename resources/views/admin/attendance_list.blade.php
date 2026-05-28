@extends('layouts.admin') 

@section('content')
<div class="bg-gray-100 min-h-screen py-10 px-4">
    <div class="max-w-5xl mx-auto">
        
        <div class="flex items-center mb-8">
            <div class="w-1.5 h-7 bg-black mr-4"></div>
            <h1 class="text-2xl font-bold text-black tracking-wide">
                {{ $currentDate->format('Y年n月j日') }}の勤怠
            </h1>
        </div>

        <div class="bg-white rounded-lg py-4 px-8 flex justify-between items-center mb-8">
            <a href="{{ route('admin.attendance_list', ['date' => $prevDate]) }}" class="text-gray-400 hover:text-black font-medium flex items-center transition-colors">
                <span class="mr-2">&larr;</span> 前日
            </a>
            
            <div class="text-lg font-bold flex items-center tracking-wider text-black">
                <svg class="w-5 h-5 mr-3 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                {{ $currentDate->format('Y/m/d') }}
            </div>

            <a href="{{ route('admin.attendance_list', ['date' => $nextDate]) }}" class="text-gray-400 hover:text-black font-medium flex items-center transition-colors">
                翌日 <span class="ml-2">&rarr;</span>
            </a>
        </div>

        <div class="bg-white rounded-lg overflow-hidden">
            <table class="w-full text-center border-collapse">
                <thead class="border-b border-gray-200">
                    <tr>
                        <th class="py-5 font-medium text-gray-500 text-sm tracking-widest">名前</th>
                        <th class="py-5 font-medium text-gray-500 text-sm tracking-widest">出勤</th>
                        <th class="py-5 font-medium text-gray-500 text-sm tracking-widest">退勤</th>
                        <th class="py-5 font-medium text-gray-500 text-sm tracking-widest">休憩</th>
                        <th class="py-5 font-medium text-gray-500 text-sm tracking-widest">合計</th>
                        <th class="py-5 font-medium text-gray-500 text-sm tracking-widest">詳細</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    
                    @forelse($attendances as $attendance)
                        <tr class="border-b border-gray-100 last:border-none hover:bg-gray-50 transition-colors">
                            <td class="py-5">{{ $attendance->user->name }}</td>
                            
                            <td class="py-5 font-medium tracking-wider">
                                {{ $attendance->formatted_start_time }}
                            </td>
                            
                            <td class="py-5 font-medium tracking-wider">
                                {{ $attendance->formatted_end_time }}
                            </td>
                            
                            <td class="py-5 font-medium tracking-wider">
                                {{ $attendance->formatted_rest_time }}
                            </td>
                            
                            <td class="py-5 font-medium tracking-wider">
                                {{ $attendance->formatted_work_time }}
                            </td>
                            
                            <td class="py-5">
                                <a href="{{ route('admin.attendance_detail', ['id' => $attendance->id]) }}" class="font-bold text-black hover:underline">詳細</a>
                            </td>
                        </tr>
                    
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-16 text-gray-400">
                                この日の勤怠データはありません。
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection