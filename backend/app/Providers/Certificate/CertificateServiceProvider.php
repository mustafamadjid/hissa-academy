<?php

namespace App\Providers\Certificate;

use App\Features\Certificate\Contracts\CertificateRepositoryContract;
use App\Features\Certificate\Repositories\EloquentCertificateRepository;
use Illuminate\Support\ServiceProvider;

final class CertificateServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CertificateRepositoryContract::class, EloquentCertificateRepository::class);
    }
}
