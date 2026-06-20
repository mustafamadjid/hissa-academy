<?php

namespace App\Features\Auth\Http\Controllers;

use App\Features\Auth\DTOs\GoogleUserData;
use App\Features\Auth\Services\GoogleAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Socialite;

class GoogleAuthController
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

     public function callback(Request $request, GoogleAuthService $googleAuthService)
    {
        $socialiteUser = Socialite::driver('google')->user();

        $googleUserData = GoogleUserData::fromSocialite($socialiteUser);

        $user = $googleAuthService->loginOrRegister($googleUserData);

        Auth::guard('web')->login($user);

        $request->session()->regenerate();

        return redirect()->away(config('app.frontend_url') . '/auth/callback');
    }
}
