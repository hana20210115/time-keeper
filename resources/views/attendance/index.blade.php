@extends('layouts.app')

@section('content')


<div class="flex flex-col items-center pt-32 min-h-screen">
    
    <div class="mb-6">
        <span class="bg-gray-300 text-gray-600 px-6 py-2 rounded-full text-sm font-bold tracking-widest">
            {{ $statusLabel }}
        </span>
    </div>

    <div class="mb-5 text-2xl">
        {{ $currentDate }}
    </div>

    <div class="mb-18 text-7xl font-bold tracking-wider">
        {{ $currentTime }}
    </div>


    <div class="w-full">

        @if ($statusLabel === '勤務外')
            <form method="POST" action="{{ route('attendance.start') }}" class="w-48 mx-auto">
                @csrf
                <button type="submit" class="w-full bg-black hover:bg-gray-800 text-white font-bold py-2 rounded-xl text-2xl transition tracking-widest">
                    出勤
                </button>
            </form>

       @elseif ($statusLabel === '出勤中')
            <div class="flex justify-center space-x-8 mx-auto">
                
                <form method="POST" action="{{ route('attendance.end') }}" class="w-40">
                    @csrf
                    <button type="submit" class="w-full bg-black hover:bg-gray-800 text-white font-bold py-2 rounded-xl text-2xl transition tracking-widest">
                        退勤
                    </button>
                </form>

                <form method="POST" action="{{ route('attendance.rest.start') }}" class="w-40">
                    @csrf
                    <button type="submit" class="w-full bg-white hover:bg-gray-150 font-bold py-2 rounded-xl text-2xl transition tracking-widest">
                        休憩入
                    </button>
                </form>
                
            </div>

        @elseif ($statusLabel === '休憩中')
            <form method="POST" action="{{ route('attendance.rest.end') }}" class="w-48 mx-auto">
                @csrf
                <button type="submit" class="w-full bg-white hover:bg-gray-150 font-bold py-2 rounded-xl text-2xl transition tracking-widest">
                    休憩戻
                </button>
            </form>

        @else
            <div class="text-center   mt-4 tracking-widest font-semibold">
                お疲れ様でした。
            </div>
        @endif
    </div>
</div>

@endsection