<?php

use App\Features\User\Contracts\UserRepositoryContract;
use App\Features\User\Repositories\EloquentUserRepository;

it('binds user repository contract to eloquent implementation', function () {
    expect(app(UserRepositoryContract::class))->toBeInstanceOf(EloquentUserRepository::class);
});
