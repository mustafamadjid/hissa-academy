<?php

use App\Features\Certificate\Models\Certificate;
use App\Features\Course\Models\Course;
use App\Features\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
