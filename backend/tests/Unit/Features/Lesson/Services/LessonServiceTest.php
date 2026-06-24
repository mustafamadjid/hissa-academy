<?php

use App\Features\Lesson\Services\LessonService;
use App\Helper\EnsureAdminForService;

it('uses the shared admin helper for authorization checks', function () {
    $service = new ReflectionClass(LessonService::class);
    $constructorParameterTypes = array_map(
        fn (ReflectionParameter $parameter): ?string => $parameter->getType()?->getName(),
        $service->getConstructor()->getParameters(),
    );

    expect($constructorParameterTypes)->toContain(EnsureAdminForService::class)
        ->and($service->hasMethod('ensureAdmin'))->toBeFalse();
});
