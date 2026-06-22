<?php

use App\Features\User\Enums\UserRole;
use App\Features\User\Models\Role;
use App\Features\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

uses(RefreshDatabase::class);

it('logs in a Google user and redirects to the frontend callback URL', function () {
    $this->travelTo(now()->startOfSecond());
    config(['app.frontend_url' => 'https://frontend.example.com']);
    Role::query()->create(['name' => UserRole::STUDENT->value]);

    $provider = Mockery::mock();
    $provider->shouldReceive('user')
        ->once()
        ->andReturn(SocialiteUser::fake([
            'id' => 'google-callback-123',
            'name' => 'Google Student',
            'email' => 'google-student@example.com',
            'avatar' => 'https://example.com/google-avatar.png',
            'email_verified' => true,
        ]));

    Socialite::shouldReceive('driver')
        ->once()
        ->with('google')
        ->andReturn($provider);

    $response = $this->get('/api/v1/auth/google/callback');

    $response->assertRedirect('https://frontend.example.com/auth/callback');

    $user = User::query()->where('email', 'google-student@example.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->google_id)->toBe('google-callback-123')
        ->and($user->last_login_at?->equalTo(now()))->toBeTrue();

    $this->assertAuthenticatedAs($user, 'web');
});
