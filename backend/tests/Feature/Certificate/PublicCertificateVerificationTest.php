<?php

use App\Features\Certificate\Models\Certificate;
use App\Features\Course\Models\Course;
use App\Features\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('returns public certificate metadata without authentication', function () {
    $student = User::factory()->create([
        'full_name' => 'Alya Student',
        'email' => 'alya@example.com',
    ]);
    $course = Course::factory()->create(['course_name' => 'Laravel Basics']);
    $certificate = Certificate::factory()->create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'certificate_number' => 'HISSA-2026-PUBLIC1',
        'status' => 'issued',
        'pdf_path' => 'certificates/private.pdf',
    ]);

    $response = $this->getJson("/api/v1/public/certificates/{$certificate->certificate_number}");

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Status sertifikat berhasil diambil.')
        ->assertJsonPath('data.certificate_number', 'HISSA-2026-PUBLIC1')
        ->assertJsonPath('data.status', 'issued')
        ->assertJsonPath('data.participant_name', 'Alya Student')
        ->assertJsonPath('data.course.name', 'Laravel Basics')
        ->assertJsonPath('data.verification_url', url('/verify/HISSA-2026-PUBLIC1'))
        ->assertJsonMissingPath('data.pdf_path')
        ->assertJsonMissingPath('data.student.email')
        ->assertJsonMissingPath('data.student.id');
});

it('returns not found for an unknown public certificate number', function () {
    $response = $this->getJson('/api/v1/public/certificates/HISSA-2026-UNKNOWN');

    $response->assertNotFound()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Sertifikat tidak ditemukan.');
});

it('renders the public verification page for a certificate', function () {
    $student = User::factory()->create(['full_name' => 'Alya Student']);
    $course = Course::factory()->create(['course_name' => 'Laravel Basics']);
    $certificate = Certificate::factory()->create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'certificate_number' => 'HISSA-2026-PAGE01',
        'status' => 'issued',
    ]);

    $response = $this->get("/verify/{$certificate->certificate_number}");

    $response->assertOk()
        ->assertSee('HISSA-2026-PAGE01')
        ->assertSee('Alya Student')
        ->assertSee('Laravel Basics')
        ->assertSee('issued');
});

it('renders not found on the public verification page for an unknown certificate number', function () {
    $response = $this->get('/verify/HISSA-2026-MISSING');

    $response->assertNotFound()
        ->assertSee('Sertifikat tidak ditemukan');
});

it('serves a private certificate PDF by certificate UUID without exposing its internal path', function () {
    Storage::fake('private');
    $path = 'certificates/11111111-1111-4111-8111-111111111111.pdf';
    $certificate = Certificate::factory()->create(['pdf_path' => $path]);
    Storage::disk('private')->put($path, '%PDF-1.4 private certificate');

    $response = $this->get("/api/v1/certificates/{$certificate->id}/file");

    $response->assertOk()
        ->assertHeader('content-type', 'application/pdf')
        ->assertHeader('x-content-type-options', 'nosniff');
    expect($response->streamedContent())->toStartWith('%PDF-1.4')
        ->and($response->headers->get('content-disposition'))->not->toContain($path);
});

it('rejects an unsafe stored certificate path', function () {
    Storage::fake('private');
    $certificate = Certificate::factory()->create([
        'pdf_path' => 'certificates/../../.env',
    ]);
    Storage::disk('private')->put('.env', 'APP_KEY=secret');

    $this->get("/api/v1/certificates/{$certificate->id}/file")
        ->assertNotFound()
        ->assertJsonPath('success', false);
});

it('rejects a non UUID certificate file identifier', function () {
    $this->get('/api/v1/certificates/..%2F..%2F.env/file')->assertNotFound();
});
