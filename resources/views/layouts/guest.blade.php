<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>time-keeper</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white min-h-screen flex flex-col">
    
    <header class="bg-black py-5 px-10">
        <div class="max-w-7xl mx-auto">
            <x-header-logo />
        </div>
    </header>

    <main class="flex-1 max-w-7xl mx-auto w-full py-10 px-4 flex flex-col">
        @yield('content')
    </main>

</body>
</html>