<?php

use App\Features\Auth\DTOs\GoogleUserData;
use App\Features\Auth\Services\GoogleAuthService;
use App\Features\User\Enums\UserRole;
use App\Features\User\Models\Role;
use App\Features\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('creates a new user from a verified Google account', function () {
    $this->travelTo(now()->startOfSecond());
    $role = Role::query()->create(['name' => UserRole::STUDENT->value]);

    $service = new GoogleAuthService;

    $user = $service->loginOrRegister(new GoogleUserData(
        googleId: 'google-123',
        email: 'student@example.com',
        fullName: 'Student Example',
        avatarUrl: 'https://example.com/avatar.png',
        emailVerified: true,
    ));

    expect($user->exists)->toBeTrue()
        ->and($user->role_id)->toBe($role->id)
        ->and($user->google_id)->toBe('google-123')
        ->and($user->email)->toBe('student@example.com')
        ->and($user->email_verified_at?->equalTo(now()))->toBeTrue()
        ->and($user->last_login_at?->equalTo(now()))->toBeTrue();

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'google_id' => 'google-123',
        'email' => 'student@example.com',
        'full_name' => 'Student Example',
        'avatar_url' => 'https://example.com/avatar.png',
        'role_id' => $role->id,
    ]);
});

it('updates an existing user matched by email with Google account details', function () {
    $this->travelTo(now()->startOfSecond());
    Role::query()->create(['name' => UserRole::STUDENT->value]);

    $existingUser = User::factory()->create([
        'email' => 'student@example.com',
        'google_id' => null,
        'full_name' => 'Old Name',
        'avatar_url' => null,
        'email_verified_at' => null,
        'last_login_at' => null,
    ]);

    $service = new GoogleAuthService;

    $user = $service->loginOrRegister(new GoogleUserData(
        googleId: 'google-456',
        email: 'student@example.com',
        fullName: 'Updated Student',
        avatarUrl: 'https://example.com/new-avatar.png',
        emailVerified: true,
    ));

    expect($user->is($existingUser))->toBeTrue()
        ->and(User::query()->count())->toBe(1);

    $freshUser = $existingUser->fresh();

    expect($freshUser->google_id)->toBe('google-456')
        ->and($freshUser->full_name)->toBe('Updated Student')
        ->and($freshUser->avatar_url)->toBe('https://example.com/new-avatar.png')
        ->and($freshUser->email_verified_at?->equalTo(now()))->toBeTrue()
        ->and($freshUser->last_login_at?->equalTo(now()))->toBeTrue();
});

it('uses an existing user matched by google id before matching by email', function () {
    $this->travelTo(now()->startOfSecond());
    Role::query()->create(['name' => UserRole::STUDENT->value]);

    $googleUser = User::factory()->create([
        'email' => 'old-google-email@example.com',
        'google_id' => 'google-789',
        'full_name' => 'Old Google Name',
    ]);

    User::factory()->create([
        'email' => 'student@example.com',
        'google_id' => null,
    ]);

    $service = new GoogleAuthService;

    $user = $service->loginOrRegister(new GoogleUserData(
        googleId: 'google-789',
        email: 'student@example.com',
        fullName: 'Google Owner',
        avatarUrl: null,
        emailVerified: false,
    ));

    expect($user->is($googleUser))->toBeTrue()
        ->and(User::query()->count())->toBe(2);

    $freshUser = $googleUser->fresh();

    expect($freshUser->email)->toBe('old-google-email@example.com')
        ->and($freshUser->full_name)->toBe('Google Owner')
        ->and($freshUser->email_verified_at)->toBeNull()
        ->and($freshUser->last_login_at?->equalTo(now()))->toBeTrue();
});
