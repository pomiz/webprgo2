<!DOCTYPE html>
<html lang="id" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Ruang Baju') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans text-gray-800 dark:text-gray-100 antialiased relative overflow-hidden">
    {{-- Animated gradient background --}}
    <div class="fixed inset-0 -z-10">
        <div class="absolute inset-0 bg-gradient-to-br from-brand-50/80 via-purple-50/40 to-blue-50/60 dark:from-surface-950 dark:via-surface-900 dark:to-surface-950"></div>
        {{-- Floating orbs --}}
        <div class="absolute top-1/4 -left-20 w-72 h-72 bg-brand-300/20 dark:bg-brand-600/10 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-1/4 -right-20 w-96 h-96 bg-purple-300/20 dark:bg-purple-600/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-blue-200/15 dark:bg-blue-600/5 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
        {{-- Subtle grid pattern --}}
        <div class="absolute inset-0 opacity-[0.015] dark:opacity-[0.03]" style="background-image: radial-gradient(circle, #000 1px, transparent 1px); background-size: 40px 40px;"></div>
    </div>

    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative z-10">
        {{ $slot }}
    </div>
</body>
</html>
