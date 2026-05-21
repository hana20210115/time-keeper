@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 py-10">
    <div class="max-w-4xl mx-auto">
        
        <h1 class="text-2xl font-bold text-black border-l-4 border-black pl-3 mb-6">
            勤怠一覧
        </h1>

        <div class="bg-white p-4 rounded-md shadow-sm flex justify-between items-center mb-8 px-8">
            <a href="{{ route('attendance.list', ['month' => $prevMonth]) }}" class="text-gray-500 hover:text-black transition">← 前月</a>
            <div class="text-xl font-bold flex items-center gap-2">
            <img src="{{ asset('img/calender-icon.png') }}" alt="Calendar Icon" class="w-auto h-6">
            <span class="ml-1">{{ $currentMonth }}</span>

            </div>
            <a href="{{ route('attendance.list', ['month' => $nextMonth]) }}" class="text-gray-500 hover:text-black transition">翌月 →</a>
        </div>

        <div class="bg-white p-8 rounded-md shadow-md">
            <table class="w-full text-center border-collapse">
                <thead>
                    <tr class="border-b-2 border-gray-300 text-gray-600">
                        <th class="py-3 font-medium">日付</th>
                        <th class="py-3 font-medium">出勤</th>
                        <th class="py-3 font-medium">退勤</th>
                        <th class="py-3 font-medium">休憩</th>
                        <th class="py-3 font-medium">合計</th>
                        <th class="py-3 font-medium">詳細</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($calendarData as $data)
                        <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                            <td class="py-4">{{ $data['date_display'] }}</td>
                            <td class="py-4">{{ $data['start_time'] }}</td>
                            <td class="py-4">{{ $data['end_time'] }}</td>
                            <td class="py-4">{{ $data['break_time'] }}</td>
                            <td class="py-4">{{ $data['total_time'] }}</td>
                            @if (!empty($data['id']))
                                <td class="py-4">
                                    <a href="{{ route('attendance.detail', ['id' => $data['id']]) }}" class="font-bold hover:underline">詳細</a>
                                </td>
                            @endif
                             
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection