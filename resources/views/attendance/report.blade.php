@extends('layouts.app')

@section('bg-color', 'bg-white')

@section('content')
<div class="max-w-5xl mx-auto w-full px-4 py-8">

    <div class="mb-8">
        <h2 class="text-2xl font-bold tracking-widest text-black mb-4">マイ勤怠レポート</h2>
        <p class="text-sm text-gray-800 font-bold">過去6ヶ月の勤怠データから集計しています。</p>
    </div>

    <div class="mb-10">
        <h3 class="text-base font-bold mb-4 text-black">基本サマリー</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gray-50 border border-gray-200 rounded-md p-6">
                <p class="text-xs text-gray-500 font-bold mb-3">総労働時間</p>
                <p class="text-3xl font-bold text-black">{{ $viewData['total_work_time'] }}</p>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-md p-6">
                <p class="text-xs text-gray-500 font-bold mb-3">総残業時間</p>
                <p class="text-3xl font-bold text-black">{{ $viewData['total_overtime'] }}</p>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-md p-6">
                <p class="text-xs text-gray-500 font-bold mb-3">平均労働時間 / 日</p>
                <p class="text-3xl font-bold text-black">{{ $viewData['average_work_time'] }}</p>
            </div>
        </div>
    </div>

    <div class="mb-10">
        <h3 class="text-base font-bold mb-4 text-black">月次推移（過去6ヶ月）</h3>
        <div class="w-full">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-300">
                        <th class="py-3 px-2 font-bold text-sm text-black w-1/3">月</th>
                        <th class="py-3 px-2 font-bold text-sm text-black w-1/3">労働時間</th>
                        <th class="py-3 px-2 font-bold text-sm text-black w-1/3">残業時間</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($viewData['monthly_data'] as $data)
                    <tr class="border-b border-gray-200 last:border-b-0">
                        <td class="py-4 px-2 text-sm font-bold text-black">{{ $data['month'] }}</td>
                        <td class="py-4 px-2 text-sm font-bold text-black">{{ $data['work_time'] }}</td>
                        <td class="py-4 px-2 text-sm font-bold text-black">{{ $data['overtime'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mb-10">
        <h3 class="text-base font-bold mb-2 text-black">今月の異常検知</h3>
        <p class="text-xs text-gray-500 font-bold mb-4">基準: 始業 09:00 / 終業 18:00 / 長時間労働は1日10時間超</p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gray-50 border border-gray-200 rounded-md p-6">
                <p class="text-xs text-gray-500 font-bold mb-3">遅刻回数</p>
                <p class="text-3xl font-bold text-black">{{ $viewData['late_count'] }} <span class="text-lg font-bold">回</span></p>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-md p-6">
                <p class="text-xs text-gray-500 font-bold mb-3">早退回数</p>
                <p class="text-3xl font-bold text-black">{{ $viewData['early_leave_count'] }} <span class="text-lg font-bold">回</span></p>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-md p-6">
                <p class="text-xs text-gray-500 font-bold mb-3">長時間労働日数</p>
                <p class="text-3xl font-bold text-black">{{ $viewData['overwork_count'] }} <span class="text-lg font-bold">日</span></p>
            </div>
        </div>
    </div>

</div>
@endsection