@extends('layouts.user')
@section('title', 'Profil - Ruang Baju')

@section('content')
    <div class="max-w-3xl mx-auto">
        <h1 class="font-serif text-3xl font-bold text-gray-900 dark:text-white mb-8">Profil Saya</h1>

        <div class="space-y-6">
            {{-- Update Profile Information --}}
            <div class="glass-card p-6 sm:p-8">
                <h2 class="font-serif text-lg font-semibold text-gray-900 dark:text-white mb-1">Informasi Profil</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Perbarui informasi profil dan alamat email Anda.</p>

                <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                    @csrf
                </form>

                <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
                    @csrf
                    @method('patch')

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nama</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus
                               class="w-full glass-input px-4 py-3 text-sm dark:text-white" autocomplete="name">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required
                               class="w-full glass-input px-4 py-3 text-sm dark:text-white" autocomplete="username">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror

                        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                            <div class="mt-2">
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Email Anda belum diverifikasi.
                                    <button form="send-verification" class="text-brand-600 hover:text-brand-700 underline text-sm font-medium">
                                        Kirim ulang email verifikasi.
                                    </button>
                                </p>
                                @if (session('status') === 'verification-link-sent')
                                    <p class="mt-2 text-sm text-green-600 dark:text-green-400 font-medium">
                                        Link verifikasi baru telah dikirim ke email Anda.
                                    </p>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="submit" class="btn-primary text-sm">Simpan</button>
                        @if (session('status') === 'profile-updated')
                            <p x-data="{ show: true }" x-show="show" x-transition
                               x-init="setTimeout(() => show = false, 2000)"
                               class="text-sm text-green-600 dark:text-green-400 font-medium">Tersimpan.</p>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Update Password --}}
            <div class="glass-card p-6 sm:p-8">
                <h2 class="font-serif text-lg font-semibold text-gray-900 dark:text-white mb-1">Ubah Password</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Pastikan akun Anda menggunakan password yang kuat dan aman.</p>

                <form method="post" action="{{ route('password.update') }}" class="space-y-5">
                    @csrf
                    @method('put')

                    <div>
                        <label for="update_password_current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Password Saat Ini</label>
                        <input id="update_password_current_password" name="current_password" type="password"
                               class="w-full glass-input px-4 py-3 text-sm dark:text-white" autocomplete="current-password"
                               placeholder="Masukkan password saat ini">
                        @error('current_password', 'updatePassword')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="update_password_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Password Baru</label>
                        <input id="update_password_password" name="password" type="password"
                               class="w-full glass-input px-4 py-3 text-sm dark:text-white" autocomplete="new-password"
                               placeholder="Minimal 8 karakter">
                        @error('password', 'updatePassword')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="update_password_password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Konfirmasi Password Baru</label>
                        <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                               class="w-full glass-input px-4 py-3 text-sm dark:text-white" autocomplete="new-password"
                               placeholder="Ulangi password baru">
                        @error('password_confirmation', 'updatePassword')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="submit" class="btn-primary text-sm">Ubah Password</button>
                        @if (session('status') === 'password-updated')
                            <p x-data="{ show: true }" x-show="show" x-transition
                               x-init="setTimeout(() => show = false, 2000)"
                               class="text-sm text-green-600 dark:text-green-400 font-medium">Tersimpan.</p>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Delete Account --}}
            <div class="glass-card p-6 sm:p-8">
                <h2 class="font-serif text-lg font-semibold text-gray-900 dark:text-white mb-1">Hapus Akun</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Setelah akun dihapus, semua data akan hilang secara permanen. Pastikan Anda sudah mengunduh data yang diperlukan.</p>

                <div x-data="{ showDelete: false }">
                    <button @click="showDelete = true" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2.5 px-5 rounded-xl transition-all duration-200 shadow-md hover:shadow-lg text-sm">
                        Hapus Akun
                    </button>

                    {{-- Confirmation Modal --}}
                    <div x-show="showDelete" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
                        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showDelete = false"></div>
                        <div class="relative glass-card p-6 w-full max-w-md">
                            <form method="post" action="{{ route('profile.destroy') }}">
                                @csrf
                                @method('delete')

                                <h3 class="font-serif text-lg font-semibold text-gray-900 dark:text-white mb-2">Yakin ingin menghapus akun?</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Masukkan password untuk konfirmasi penghapusan akun secara permanen.</p>

                                <div class="mb-4">
                                    <input name="password" type="password" placeholder="Password Anda"
                                           class="w-full glass-input px-4 py-3 text-sm dark:text-white">
                                    @error('password', 'userDeletion')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex justify-end gap-3">
                                    <button type="button" @click="showDelete = false" class="btn-outline text-sm">Batal</button>
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2.5 px-5 rounded-xl transition-all duration-200 text-sm">Hapus Akun</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
