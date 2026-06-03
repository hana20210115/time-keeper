@extends('layouts.admin')

@section('content')
<div class="bg-gray-100 min-h-screen py-12 px-4 font-sans">
    <div class="max-w-5xl mx-auto">

        <div class="flex items-center mb-8">
            <div class="w-1.5 h-6 bg-black mr-4"></div>
            <h1 class="text-xl font-bold text-black tracking-widest">{{ $user->name }}さんの勤怠</h1>
        </div>
        
        <div class="bg-white rounded border border-gray-200 mb-4 flex justify-between items-center px-8 py-4">
            <a href="{{route('admin.staff_detail',['id' => $user->id, 'month' => $prevMonth])}}" class="text-gray-400 text-sm font-bold tracking-widest hover:text-black transition-colors">
                <- 前月
            </a>
            <div class="text-black font-bold tracking-widest flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                </svg>
                {{ $displayMonth }}
            </div>

            <a href="{{ route ('admin.staff_detail', ['id' => $user->id ,'month' =>$nextMonth])}}" class="text-gray-400 text-sm font-bold tracking-widest hover:text-black transition-colors">
                翌月 ->
            </a>
        </div>

        <div class="bg-white rounded border border-gray-200 overflow-hidden mb-6">
            <table class="w-full text-center border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 bg-white">
                        <th class="py-6 font-medium text-gray-700 tracking-[0.2em] text-sm w-1/6">日付</th>
                        <th class="py-6 font-medium text-gray-700 tracking-[0.2em] text-sm w-1/6">出勤</th>
                        <th class="py-6 font-medium text-gray-700 tracking-[0.2em] text-sm w-1/6">退勤</th>
                        <th class="py-6 font-medium text-gray-700 tracking-[0.2em] text-sm w-1/6">休憩</th>
                        <th class="py-6 font-medium text-gray-700 tracking-[0.2em] text-sm w-1/6">合計</th>
                        <th class="py-6 font-medium text-gray-700 tracking-[0.2em] text-sm w-1/6">詳細</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monthlyDate as $date)
                    <tr class="border-b border-gray-200 last:border-b-0">
                        <td class="py-6 text-black tracking-widest text-sm font-medium">{{ $date['date_display'] }}</td>
                        <td class="py-6 text-black tracking-widest text-sm font-medium">{{ $date['start_time'] }}</td>
                        <td class="py-6 text-black tracking-widest text-sm font-medium">{{ $date['end_time'] }}</td>
                        <td class="py-6 text-black tracking-widest text-sm font-medium">{{ $date['rest_time'] }}</td>
                        <td class="py-6 text-black tracking-widest text-sm font-medium">{{ $date['work_time'] }}</td>
                        <td class="py-6">
                            @if($date['attendances_id'])
                                <a href="{{ route('admin.attendance_detail', ['id' => $date['attendances_id']]) }}"
                                class="text-black font-bold tracking-widest text-sm hover:text-gray-500 transition-colors">
                                    詳細
                                </a>
                            @else
                                <span class="text-black font-bold tracking-widest text-sm">詳細</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex justify-end mb-12">
            <a href="{{ route('admin.staff_detail_csv',['id' => $user->id, 'month' => $targetMonth]) }}" class="bg-black text-white px-12 py-3 rounded text-sm font-bold tracking-[0.3em] hover:bg-gray-800 transition-colors inline-block text-center">
                CSV出力
            </a>
        </div>
    </div>
</div>
@endsection