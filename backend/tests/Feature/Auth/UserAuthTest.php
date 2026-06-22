<?php

use App\Features\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('logs in a user with valid credentials and hides sensitive fields', function () {
    $this->travelTo(now()->startOfSecond());
    Log::spy();

    $user = User::factory()->create([
        'email' => 'student@example.com',
        'password' => Hash::make('correct-password'),
        'last_login_at' => null,
    ]);

    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'student@example.com',
        'password' => 'correct-password',
    ]);

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Login berhasil.')
        ->assertJsonPath('data.user.email', 'student@example.com')
        ->assertJsonMissingPath('data.user.password')
        ->assertJsonMissingPath('data.user.remember_token');

    expect($user->fresh()->last_login_at?->equalTo(now()))->toBeTrue();
});

it('returns the same invalid credential response when the email does not exist', function () {
    Log::spy();

    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'missing@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(422)
        ->assertJsonPath('message', 'Email atau password salah.');
});

it('returns the authenticated Sanctum user with role data', function () {
    $user = User::factory()->create([
        'email' => 'student@example.com',
    ]);

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/v1/auth/me');

    $response->assertOk()
        ->assertJsonPath('email', 'student@example.com')
        ->assertJsonStructure([
            'id',
            'email',
            'role' => [
                'id',
                'name',
            ],
        ])
        ->assertJsonMissingPath('password')
        ->assertJsonMissingPath('remember_token');
});
