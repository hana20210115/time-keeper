@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto w-full pt-8">

    <div class="flex items-center mb-8">
        <div class="w-1.5 h-6 bg-black mr-3"></div>
        <h1 class="text-xl font-bold tracking-widest text-black">申請一覧</h1>
    </div>

    <div class="flex border-b border-gray-300 mb-8">
        <a href="{{ route('stamp_correction_request.list', ['tab' => 'pending']) }}" 
           class="px-10 py-3 text-sm {{ $activeTab === 'pending' ? 'font-bold border-b-2 border-black' : 'font-medium text-gray-500 border-b-2 border-transparent hover:text-black' }} focus:outline-none transition-colors">
            承認待ち
        </a>
        <a href="{{ route('stamp_correction_request.list', ['tab' => 'approved']) }}" 
           class="px-10 py-3 text-sm {{ $activeTab === 'approved' ? 'font-bold border-b-2 border-black' : 'font-medium text-gray-500 border-b-2 border-transparent hover:text-black' }} focus:outline-none transition-colors">
            承認済み
        </a>
    </div>

    @if($activeTab === 'pending')
        <div class="bg-white rounded-md shadow-sm overflow-hidden">
            <table class="w-full text-center border-collapse">
                <thead>
                    <tr class="bg-white border-b border-gray-200 text-gray-600 text-sm">
                        <th class="py-4 font-medium">状態</th>
                        <th class="py-4 font-medium">名前</th>
                        <th class="py-4 font-medium">対象日時</th>
                        <th class="py-4 font-medium">申請理由</th>
                        <th class="py-4 font-medium">申請日時</th>
                        <th class="py-4 font-medium">詳細</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingCorrections as $correction)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                            <td class="py-5 text-gray-800">承認待ち</td>
                            <td class="py-5 text-gray-800">{{ $correction->user_name }}</td>
                            <td class="py-5 text-gray-800">{{ $correction->formatted_date }}</td>
                            <td class="py-5 text-gray-800">{{ $correction->reason }}</td>
                            <td class="py-5 text-gray-800">{{ $correction->formatted_created_at }}</td>
                            <td class="py-5">
                                <a href="{{ route('attendance.detail', ['id' => $correction->attendance_id]) }}" class="font-bold text-black hover:underline">詳細</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-gray-500 text-center">承認待ちの申請はありません</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @else
        <div class="bg-white rounded-md shadow-sm overflow-hidden">
            <table class="w-full text-center border-collapse">
                <thead>
                    <tr class="bg-white border-b border-gray-200 text-gray-600 text-sm">
                        <th class="py-4 font-medium">状態</th>
                        <th class="py-4 font-medium">名前</th>
                        <th class="py-4 font-medium">対象日時</th>
                        <th class="py-4 font-medium">申請理由</th>
                        <th class="py-4 font-medium">申請日時</th>
                        <th class="py-4 font-medium">詳細</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($approvedCorrections as $correction)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                            <td class="py-5 text-gray-800">承認済み</td>
                            <td class="py-5 text-gray-800">{{ $correction->user_name }}</td>
                            <td class="py-5 text-gray-800">{{ $correction->formatted_date }}</td>
                            <td class="py-5 text-gray-800">{{ $correction->reason }}</td>
                            <td class="py-5 text-gray-800">{{ $correction->formatted_created_at }}</td>
                            <td class="py-5">
                                <a href="{{ route('attendance.detail', ['id' => $correction->attendance_id]) }}" class="font-bold text-black hover:underline">詳細</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-gray-500 text-center">承認済みの申請はありません</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

</div>
@endsection