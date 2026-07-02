<?php

namespace App\Providers\Certificate;

use App\Features\Certificate\Contracts\CertificateRepositoryContract;
use App\Features\Certificate\Contracts\CertificateGeneratorContract;
use App\Features\Certificate\Repositories\EloquentCertificateRepository;
use App\Features\Certificate\Services\CertificateGeneratorService;
use Illuminate\Support\ServiceProvider;

final class CertificateServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CertificateRepositoryContract::class, EloquentCertificateRepository::class);
        $this->app->bind(CertificateGeneratorContract::class, CertificateGeneratorService::class);
    }
}
