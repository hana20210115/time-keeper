<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>time-keeper</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <header class="bg-black py-5 px-10">
        <div class="max-w-7xl mx-auto">
            <x-header-logo />
        </div>
    </header>

    <main class="max-w-7xl mx-auto py-10 px-4 bg-gray-100">
        @yield('content')
    </main>
</body>
</html>