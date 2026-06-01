@extends('layouts.admin')

@section('content')
<div class="bg-white min-h-screen py-12 px-4 font-sans">
    <div class="max-w-5xl mx-auto">
        
        <div class="flex items-center mb-8">
            <div class="w-1.5 h-6 bg-black mr-4"></div>
            <h1 class="text-xl font-bold text-black tracking-widest">スタッフ一覧</h1>
        </div>
        <div class="bg-white rounded border-gray-200 overflow-hidden">
            <table class="w-full text-center border-collapse">
                <thead>
                    <tr class="border-b border-gray 200">
                        <th class="py-6 font-medium text-gray-700 tracking-[0.2em] text-sm w-1/3">名前</th>
                        <th class="py-6 font-medium text-gray-700 tracking-[0.2em] text-sm w-1/3">メールアドレス</th>
                        <th class="py-6 font-medium text-gray-700 tracking-[0.2em] text-sm w-1/3">月次勤怠</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staffs as $staff)
                    <tr class="border-b border-gray-200 last:border-0">
                        <td class="py-6 text-black tracking-wider text-sm font-medium">
                            {{ $staff->name }}
                        </td>
                        <td class="py-6 text-black tracking-wider text-sm font-medium">
                            {{ $staff->email }}
                        </td>
                        <td class="py-6">
                            <a href="#" class="text-black font-bold tracking-widest text-sm hover:text-gra          y-500 transition-colors">詳細
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <tb colspan="3" class="py-10 text-gray-400 text-sm tracking-widest text-center">
                            スタッフが登録されていません
                        </tb>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
