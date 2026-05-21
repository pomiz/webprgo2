<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    private const ALLOWED_PROVIDERS = ['google'];

    public function redirect(string $provider)
    {
        if (!in_array($provider, self::ALLOWED_PROVIDERS)) {
            abort(404);
        }

        if (!$this->isProviderConfigured($provider)) {
            return redirect()->route('login')
                ->with('error', 'Login dengan ' . ucfirst($provider) . ' belum tersedia.');
        }

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider)
    {
        if (!in_array($provider, self::ALLOWED_PROVIDERS)) {
            abort(404);
        }

        if (!$this->isProviderConfigured($provider)) {
            return redirect()->route('login')
                ->with('error', 'Login dengan ' . ucfirst($provider) . ' belum tersedia.');
        }

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Gagal login dengan ' . ucfirst($provider) . '. Silakan coba lagi.');
        }

        $user = User::where('google_id', $socialUser->getId())
            ->orWhere('email', $socialUser->getEmail())
            ->first();

        if ($user) {
            // Update google_id if not set (existing user linking account)
            if (!$user->google_id) {
                $user->update(['google_id' => $socialUser->getId()]);
            }
            if (!$user->avatar) {
                $user->update(['avatar' => $socialUser->getAvatar()]);
            }
        } else {
            // Create new user
            $user = User::create([
                'username' => $this->generateUsername($socialUser->getName()),
                'name' => $socialUser->getName(),
                'email' => $socialUser->getEmail(),
                'google_id' => $socialUser->getId(),
                'avatar' => $socialUser->getAvatar(),
                'password' => null,
                'role' => 'user',
            ]);
        }

        Auth::login($user, true);

        return redirect()->route('home');
    }

    private function isProviderConfigured(string $provider): bool
    {
        $config = config("services.{$provider}");
        return !empty($config['client_id']) && !empty($config['client_secret']);
    }

    private function generateUsername(string $name): string
    {
        $base = Str::slug($name, '');
        $username = $base;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = $base . $counter;
            $counter++;
        }

        return $username;
    }
}
