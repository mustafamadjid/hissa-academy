<?php

use App\Features\Certificate\Models\Certificate;
use App\Features\Course\Models\Course;
use App\Features\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

it('creates certificates with the expected schema and factory defaults', function () {
    expect(Schema::hasColumns('certificates', [
        'id',
        'public_uuid',
        'user_id',
        'course_id',
        'certificate_number',
        'issued_at',
        'status',
        'pdf_path',
        'created_at',
        'updated_at',
    ]))->toBeTrue();

    $certificate = Certificate::factory()->create();

    expect($certificate->public_uuid)->not->toBeEmpty()
        ->and($certificate->user)->toBeInstanceOf(User::class)
        ->and($certificate->course)->toBeInstanceOf(Course::class)
        ->and($certificate->certificate_number)->toMatch('/^HISSA-' . now()->format('Y') . '-[A-Z0-9]{8}$/')
        ->and($certificate->issued_at)->not->toBeNull()
        ->and($certificate->status)->not->toBeEmpty()
        ->and($certificate->pdf_path)->not->toBeEmpty();
});

it('generates unique certificate numbers from the model', function () {
    $existingCertificate = Certificate::factory()->create();

    $certificateNumber = Certificate::generateCertificateNumber();

    expect($certificateNumber)->toMatch('/^HISSA-' . now()->format('Y') . '-[A-Z0-9]{8}$/')
        ->and($certificateNumber)->not->toBe($existingCertificate->certificate_number);
});
