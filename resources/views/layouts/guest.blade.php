<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>time-keeper</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <header class="bg-black py-5 px-10">
        <div class="max-w-7xl mx-auto">
            <x-header-logo />
        </div>
    </header>

    <main>
        @yield('content')
    </main>
</body>
</html>