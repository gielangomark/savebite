<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Save Bite') }} - Login</title>

    <!-- Fonts & Tailwind -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .bg-pattern {
            background-color: #f7f3eb;
            background-image: radial-gradient(circle at 10% 20%, rgba(200, 180, 150, 0.15) 0%, transparent 40%),
                              radial-gradient(circle at 90% 80%, rgba(200, 180, 150, 0.15) 0%, transparent 40%);
        }
    </style>
</head>
<body class="bg-pattern text-slate-800 antialiased min-h-screen flex flex-col">
    <main class="flex flex-1 items-center justify-center p-4 sm:p-6 md:p-10">
        {{ $slot }}
    </main>

    <footer class="relative border-t border-slate-900/10 bg-white/70 px-4 py-4 text-center text-xs text-slate-500 backdrop-blur-sm">
        <p>&copy; {{ now()->year }} Save Bite. All rights reserved.</p>
    </footer>
    @include('components.live-clock')
</body>
</html>