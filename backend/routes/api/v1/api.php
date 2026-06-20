<?php
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->name('api.v1.')
    ->group(function () {
        foreach ([
            'auth.php',
            'courses.php',
            'lessons.php',
            'progress.php',
            'quizzes.php',
            'certificates.php',
        ] as $routeFile) {
            $routePath = __DIR__.'/'.$routeFile;

            if (file_exists($routePath)) {
                require $routePath;
            }
        }
    });
?>
