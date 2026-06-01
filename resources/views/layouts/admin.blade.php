<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>time-keeper</title>
    @vite(['resources/css/app.css'])
</head>

<body>
    <header class="bg-black text-white py-5 px-10 flex justify-between items-center">
    
        <x-header-logo />
        
        <nav>
            <ul class="flex items-center space-x-8 font-bold">
                @auth
                        <li>
                            <a href="{{route('admin.attendance_list')}}" class="hover:text-gray-300">勤怠一覧</a></li>
                        <li>
                            <a href="{{route('admin.staff_list')}}" class="hover:text-gray-300">スタッフ一覧</a></li>
                        <li>
                            <a href="#" class="hover:text-gray-300">申請一覧</a></li>
        
                    
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="hover:text-gray-300">ログアウト</button>
                            </form>
                        </li>
                @endauth
            </ul>
        </nav>
    </header>

    <main class=" mx-auto py-10 px-4 bg-gray-100">
        @yield('content')
    </main>




</body>
</html>

