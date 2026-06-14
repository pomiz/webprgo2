<!DOCTYPE html>
<html lang="id" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Ruang Baju')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen text-gray-800 dark:text-gray-100 transition-colors duration-300" :class="{ 'dark-body': darkMode }">
    {{-- Animated gradient background --}}
    <div class="fixed inset-0 -z-10">
        <div class="absolute inset-0 bg-gradient-to-br from-brand-50/50 via-purple-50/30 to-blue-50/40 dark:from-surface-950 dark:via-surface-900 dark:to-surface-950"></div>
        <div class="absolute top-0 -left-40 w-80 h-80 bg-brand-200/20 dark:bg-brand-600/5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 -right-40 w-96 h-96 bg-purple-200/15 dark:bg-purple-600/5 rounded-full blur-3xl"></div>
        <div class="absolute inset-0 opacity-[0.012] dark:opacity-[0.02]" style="background-image: radial-gradient(circle, #000 1px, transparent 1px); background-size: 40px 40px;"></div>
    </div>

    {{-- Navigation --}}
    <nav class="sticky top-0 z-50 glass-nav">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                {{-- Logo --}}
                <a href="{{ route('home') }}" class="font-serif text-xl font-bold text-gray-900 dark:text-white">
                    Ruang Baju
                </a>

                {{-- Nav Links --}}
                <div class="hidden md:flex items-center space-x-6">
                    <a href="{{ route('home') }}" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-brand-600 transition-colors">Home</a>
                    <a href="{{ route('products.index') }}" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-brand-600 transition-colors">Produk</a>
                    <a href="{{ route('orders.index') }}" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-brand-600 transition-colors">Pesanan</a>
                </div>

                {{-- Right Side --}}
                <div class="flex items-center space-x-4">
                    {{-- Dark Mode Toggle --}}
                    <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)"
                            class="p-2 rounded-xl text-gray-500 dark:text-gray-400 hover:bg-white/50 dark:hover:bg-white/10 transition-colors">
                        <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                        <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </button>

                    {{-- Cart --}}
                    <a href="{{ route('cart.index') }}" class="relative p-2 rounded-xl text-gray-500 dark:text-gray-400 hover:bg-white/50 dark:hover:bg-white/10 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                        </svg>
                        @php $cartCount = \App\Models\CartItem::where('user_id', auth()->id())->count(); @endphp
                        @if($cartCount > 0)
                            <span class="absolute -top-1 -right-1 bg-brand-600 text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center">{{ $cartCount }}</span>
                        @endif
                    </a>

                    {{-- User Menu --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center space-x-2 text-sm font-medium text-gray-700 dark:text-gray-200">
                            <span>{{ auth()->user()->username }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition
                             class="absolute right-0 mt-2 w-48 glass-card p-2">
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-white/50 dark:hover:bg-white/10 rounded-xl transition-colors">Profil</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-white/50 dark:hover:bg-white/10 rounded-xl transition-colors">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg text-sm">
                {{ session('error') }}
            </div>
        </div>
    @endif

    {{-- Main Content --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="border-t border-gray-100 dark:border-gray-800 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center text-sm text-gray-500 dark:text-gray-400">
                &copy; {{ date('Y') }} Ruang Baju. All rights reserved.
            </div>
        </div>
    </footer>

</body>
</html>
