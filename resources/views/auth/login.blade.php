<x-guest-layout>
    <div class="w-full max-w-md px-4">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <a href="/" class="font-serif text-3xl font-bold text-gray-900 dark:text-white">Ruang Baju</a>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Masuk ke akun Anda</p>
        </div>

        {{-- Card --}}
        <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-8">

            {{-- Session Status / Errors --}}
            @if(session('status'))
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-600 dark:text-green-300 text-sm rounded-lg px-4 py-3 mb-4">
                    {{ session('status') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-300 text-sm rounded-lg px-4 py-3 mb-4">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Google SSO Button --}}
            <a href="{{ route('social.redirect', 'google') }}"
               class="w-full flex items-center justify-center gap-3 border border-gray-200 dark:border-gray-600 rounded-lg py-2.5 px-4 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-surface-700 transition-colors">
                <svg class="w-5 h-5" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Masuk dengan Google
            </a>

            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200 dark:border-gray-700"></div>
                </div>
                <div class="relative flex justify-center text-xs">
                    <span class="px-2 bg-white dark:bg-surface-800 text-gray-500">atau</span>
                </div>
            </div>

            {{-- Email/Password Form --}}
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                               class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-surface-900 dark:text-white text-sm focus:ring-brand-500 focus:border-brand-500">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
                        <input type="password" name="password" required
                               class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-surface-900 dark:text-white text-sm focus:ring-brand-500 focus:border-brand-500">
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="remember" class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Ingat saya</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm text-brand-600 hover:text-brand-700">Lupa password?</a>
                        @endif
                    </div>

                    <button type="submit" class="w-full btn-primary">Masuk</button>
                </div>
            </form>
        </div>

        <p class="text-center text-sm text-gray-500 dark:text-gray-400 mt-6">
            Belum punya akun? <a href="{{ route('register') }}" class="text-brand-600 hover:text-brand-700 font-medium">Daftar</a>
        </p>
    </div>
</x-guest-layout>
