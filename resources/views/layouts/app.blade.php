<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Secure Storage')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>

<body class="bg-gray-100">
    <nav class="bg-white shadow p-4">
        <div class="container mx-auto flex justify-between">
            <div><a href="/" class="font-bold">Secure Storage</a></div>
            <div>
                @auth
                    <form method="POST" action="{{ route('logout') }}" class="inline">@csrf<button
                            class="text-sm">Logout</button></form>
                @endauth
            </div>
        </div>
    </nav>
    <main class="container mx-auto mt-6">
        @if (session('success'))
            <div class="bg-green-100 p-2 mb-4">{{ session('success') }}</div>
        @endif
        @yield('content')
    </main>
    <script src="/js/uploader.js"></script>
</body>

</html>
