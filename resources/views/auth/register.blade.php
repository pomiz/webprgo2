<x-guest-layout>
    <div class="w-full max-w-md px-4">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-brand-600/10 backdrop-blur-sm border border-brand-200/30 mb-4">
                <svg class="w-8 h-8 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                </svg>
            </div>
            <a href="/" class="font-serif text-3xl font-bold text-gray-900 dark:text-white">Ruang Baju</a>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Buat akun baru</p>
        </div>

        {{-- Glass Card --}}
        <div class="glass-card p-8">

            {{-- Google SSO Button --}}
            <a href="{{ route('social.redirect', 'google') }}"
               class="w-full flex items-center justify-center gap-3 bg-white/70 dark:bg-white/10 backdrop-blur-sm border border-white/40 dark:border-white/10 rounded-xl py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-white/90 dark:hover:bg-white/20 hover:shadow-md transition-all duration-200">
                <svg class="w-5 h-5" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Daftar dengan Google
            </a>

            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200/50 dark:border-white/10"></div>
                </div>
                <div class="relative flex justify-center text-xs">
                    <span class="px-3 bg-white/60 dark:bg-white/5 backdrop-blur-sm rounded-full text-gray-500 dark:text-gray-400">atau</span>
                </div>
            </div>

            {{-- Register Form --}}
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Username</label>
                        <input type="text" name="username" value="{{ old('username') }}" required autofocus
                               class="w-full glass-input px-4 py-3 text-sm dark:text-white placeholder-gray-400"
                               placeholder="username_anda">
                        @error('username')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="w-full glass-input px-4 py-3 text-sm dark:text-white placeholder-gray-400"
                               placeholder="nama@email.com">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Password</label>
                        <input type="password" name="password" required
                               class="w-full glass-input px-4 py-3 text-sm dark:text-white placeholder-gray-400"
                               placeholder="Minimal 8 karakter">
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full glass-input px-4 py-3 text-sm dark:text-white placeholder-gray-400"
                               placeholder="Ulangi password">
                        @error('password_confirmation')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full btn-primary py-3 text-sm">Daftar</button>
                </div>
            </form>
        </div>

        <p class="text-center text-sm text-gray-500 dark:text-gray-400 mt-6">
            Sudah punya akun? <a href="{{ route('login') }}" class="text-brand-600 hover:text-brand-700 font-medium">Masuk</a>
        </p>
    </div>
</x-guest-layout>
