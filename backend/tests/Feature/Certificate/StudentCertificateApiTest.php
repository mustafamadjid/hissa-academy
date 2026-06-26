<?php

use App\Features\Certificate\Models\Certificate;
use App\Features\Course\Models\Course;
use App\Features\User\Models\Role;
use App\Features\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('returns certificates owned by the active student without exposing private paths', function () {
    $student = studentCertificateUser('Alya Student');
    $this->actingAs($student);

    $course = Course::factory()->create(['course_name' => 'Laravel Basics']);
    $certificate = Certificate::factory()->create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'certificate_number' => 'HISSA-2026-STUDENT1',
        'status' => 'issued',
    ]);
    Certificate::factory()->create([
        'user_id' => studentCertificateUser('Other Student')->id,
    ]);

    $response = $this->getJson('/api/v1/certificates');

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Daftar sertifikat berhasil diambil.')
        ->assertJsonPath('data.0.uuid', $certificate->id)
        ->assertJsonPath('data.0.certificate_number', 'HISSA-2026-STUDENT1')
        ->assertJsonPath('data.0.course.uuid', $course->id)
        ->assertJsonPath('data.0.course.name', 'Laravel Basics')
        ->assertJsonMissingPath('data.0.pdf_path')
        ->assertJsonMissingPath('data.1');
});

it('returns certificate detail only for the owner', function () {
    $student = studentCertificateUser('Alya Student');
    $this->actingAs($student);

    $course = Course::factory()->create(['course_name' => 'Laravel Basics']);
    $certificate = Certificate::factory()->create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'certificate_number' => 'HISSA-2026-DETAIL1',
        'status' => 'issued',
    ]);

    $response = $this->getJson("/api/v1/certificates/{$certificate->id}");

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Detail sertifikat berhasil diambil.')
        ->assertJsonPath('data.uuid', $certificate->id)
        ->assertJsonPath('data.participant_name', 'Alya Student')
        ->assertJsonPath('data.course.name', 'Laravel Basics')
        ->assertJsonPath('data.download_url', "/api/v1/certificates/{$certificate->id}/download")
        ->assertJsonMissingPath('data.pdf_path');
});

it('streams a certificate pdf from private storage for the owner', function () {
    Storage::fake('local');

    $student = studentCertificateUser('Alya Student');
    $this->actingAs($student);
    $certificate = Certificate::factory()->create([
        'user_id' => $student->id,
        'certificate_number' => 'HISSA-2026-DOWNLOAD',
        'status' => 'issued',
        'pdf_path' => 'certificates/HISSA-2026-DOWNLOAD.pdf',
    ]);
    Storage::disk('local')->put($certificate->pdf_path, '%PDF-1.4 test certificate');

    $response = $this->get("/api/v1/certificates/{$certificate->id}/download");

    $response->assertOk()
        ->assertHeader('content-type', 'application/pdf')
        ->assertHeader('content-disposition', 'attachment; filename=HISSA-2026-DOWNLOAD.pdf');
});

it('does not allow a student to view another user certificate', function () {
    $this->actingAs(studentCertificateUser('Alya Student'));
    $certificate = Certificate::factory()->create([
        'user_id' => studentCertificateUser('Other Student')->id,
    ]);

    $response = $this->getJson("/api/v1/certificates/{$certificate->id}");

    $response->assertNotFound()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Sertifikat tidak ditemukan.');
});

function studentCertificateUser(string $name): User
{
    return User::factory()->create([
        'role_id' => studentCertificateRole('student')->id,
        'full_name' => $name,
    ]);
}

function studentCertificateRole(string $roleName): Role
{
    return Role::query()->firstOrCreate(['name' => $roleName]);
}
