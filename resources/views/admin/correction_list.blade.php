@extends('layouts.admin')

@section('content')
<div class="bg-gray-100 min-h-screen py-12 px-4 font-sans">
    <div class="max-w-5xl mx-auto">
        
        <div class="flex items-center mb-10">
            <div class="w-1.5 h-6 bg-black mr-4"></div>
            <h1 class="text-xl font-bold text-black tracking-widest">申請一覧</h1>
        </div>

        <div class="flex gap-12 border-b border-gray-400 mb-8 pl-4">
            <a href="{{ route('admin.correction_request_list', ['tab' => 'pending']) }}"
               class="pb-3 text-sm font-bold tracking-widest transition-colors {{ $statusTab === 'pending' ? 'text-black' : 'text-gray-400 hover:text-black' }}">
                承認待ち
            </a>
            <a href="{{ route('admin.correction_request_list', ['tab' => 'approved']) }}"
               class="pb-3 text-sm font-bold tracking-widest transition-colors {{ $statusTab === 'approved' ? 'text-black' : 'text-gray-400 hover:text-black' }}">
                承認済み
            </a>
        </div>

        <div class="bg-white rounded overflow-hidden">
            <table class="w-full text-center border-collapse">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="py-6 font-medium text-gray-500 tracking-[0.2em] text-sm">状態</th>
                        <th class="py-6 font-medium text-gray-500 tracking-[0.2em] text-sm">名前</th>
                        <th class="py-6 font-medium text-gray-500 tracking-[0.2em] text-sm">対象日時</th>
                        <th class="py-6 font-medium text-gray-500 tracking-[0.2em] text-sm">申請理由</th>
                        <th class="py-6 font-medium text-gray-500 tracking-[0.2em] text-sm">申請日時</th>
                        <th class="py-6 font-medium text-gray-500 tracking-[0.2em] text-sm">詳細</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($corrections as $correction)
                    <tr class="border-b border-gray-200 last:border-0">
                        <td class="py-6 text-black tracking-wider text-sm font-medium">
                            {{ $statusTab === 'pending' ? '承認待ち' : '承認済み' }}
                        </td>
                        <td class="py-6 text-black tracking-wider text-sm font-medium">
                            {{ $correction->attendance->user->name }}
                        </td>
                        <td class="py-6 text-black tracking-wider text-sm font-medium">
                            {{ $correction->formatted_target_date }}
                        </td>
                        <td class="py-6 text-black tracking-wider text-sm font-medium">
                            {{ $correction->reason }}
                        </td>
                        <td class="py-6 text-black tracking-wider text-sm font-medium">
                            {{ $correction->formatted_apply_date }}
                        </td>
                        <td class="py-6">
                            <a href="#" class="text-black font-bold tracking-widest text-sm hover:text-gray-500 transition-colors">
                                詳細
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-10 text-gray-400 text-sm tracking-widest text-center">
                            申請データがありません
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection