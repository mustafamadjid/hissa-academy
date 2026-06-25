<?php

use App\Features\Certificate\Models\Certificate;
use App\Features\Course\Models\Course;
use App\Features\User\Models\Role;
use App\Features\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns paginated certificates for audit with student and course data', function () {
    $this->actingAs(adminCertificateAdminUser());

    $student = User::factory()->create([
        'full_name' => 'Alya Student',
        'email' => 'alya.student@example.com',
        'role_id' => adminCertificateRole('student')->id,
    ]);
    $course = Course::factory()->create(['course_name' => 'Laravel Basics']);
    $certificate = Certificate::factory()->create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'certificate_number' => 'HISSA-2026-AUDIT001',
        'status' => 'issued',
    ]);

    $response = $this->getJson('/api/v1/admin/certificates?limit=5&page=1');

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Daftar sertifikat berhasil diambil.')
        ->assertJsonPath('meta.total', 1)
        ->assertJsonPath('data.0.id', $certificate->id)
        ->assertJsonPath('data.0.certificate_number', 'HISSA-2026-AUDIT001')
        ->assertJsonPath('data.0.status', 'issued')
        ->assertJsonPath('data.0.student.id', $student->id)
        ->assertJsonPath('data.0.student.full_name', 'Alya Student')
        ->assertJsonPath('data.0.student.email', 'alya.student@example.com')
        ->assertJsonPath('data.0.course.id', $course->id)
        ->assertJsonPath('data.0.course.name', 'Laravel Basics')
        ->assertJsonMissingPath('data.0.student.password')
        ->assertJsonMissingPath('data.0.student.remember_token');
});

it('revokes a certificate and stores the reason', function () {
    $this->actingAs(adminCertificateAdminUser());
    $this->travelTo(now()->startOfSecond());

    $certificate = Certificate::factory()->create([
        'certificate_number' => 'HISSA-2026-REVOKE01',
        'status' => 'issued',
    ]);

    $response = $this->patchJson("/api/v1/admin/certificates/{$certificate->id}/revoke", [
        'reason' => 'Student requested a corrected legal name.',
    ]);

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Sertifikat berhasil dicabut.')
        ->assertJsonPath('data.id', $certificate->id)
        ->assertJsonPath('data.status', 'revoked')
        ->assertJsonPath('data.revoked_reason', 'Student requested a corrected legal name.')
        ->assertJsonPath('data.revoked_at', now()->toISOString());

    $this->assertDatabaseHas('certificates', [
        'id' => $certificate->id,
        'status' => 'revoked',
        'revoked_reason' => 'Student requested a corrected legal name.',
    ]);
});

it('returns validation errors when revoke reason is missing', function () {
    $this->actingAs(adminCertificateAdminUser());

    $certificate = Certificate::factory()->create(['status' => 'issued']);

    $response = $this->patchJson("/api/v1/admin/certificates/{$certificate->id}/revoke", []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['reason']);
});

it('returns not found when revoking an unknown certificate', function () {
    $this->actingAs(adminCertificateAdminUser());

    $response = $this->patchJson('/api/v1/admin/certificates/00000000-0000-0000-0000-000000000000/revoke', [
        'reason' => 'Invalid certificate record.',
    ]);

    $response->assertNotFound()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Sertifikat tidak ditemukan.');
});

it('rejects certificate admin endpoints when the user is not an admin', function () {
    $this->actingAs(User::factory()->create([
        'role_id' => adminCertificateRole('student')->id,
    ]));

    $response = $this->getJson('/api/v1/admin/certificates');

    $response->assertForbidden()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Anda tidak memiliki akses.');
});

function adminCertificateAdminUser(): User
{
    return User::factory()->create([
        'role_id' => adminCertificateRole('admin')->id,
    ]);
}

function adminCertificateRole(string $roleName): Role
{
    return Role::query()->firstOrCreate(['name' => $roleName]);
}
