<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable([
    'user_id',
    'course_id',
    'certificate_number',
    'issued_at',
    'status',
    'pdf_path',
])]
class Certificate extends Model
{
    /** @use HasFactory<\Database\Factories\CertificateFactory> */
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (Certificate $certificate): void {
            $certificate->public_uuid ??= (string) Str::uuid();
            $certificate->certificate_number ??= self::generateCertificateNumber();
        });
    }

    public static function generateCertificateNumber(): string
    {
        do {
            $certificateNumber = sprintf(
                'HISSA-%s-%s',
                now()->format('Y'),
                Str::upper(Str::random(8))
            );
        } while (
            self::query()
                ->where('certificate_number', $certificateNumber)
                ->exists()
        );

        return $certificateNumber;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
        ];
    }
}
