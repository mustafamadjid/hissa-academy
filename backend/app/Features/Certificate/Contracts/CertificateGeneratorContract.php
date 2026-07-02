<?php

namespace App\Features\Certificate\Contracts;

use App\Features\Certificate\Models\Certificate;
use App\Features\Course\Models\Course;
use App\Features\User\Models\User;

interface CertificateGeneratorContract
{
    public function issue(User $student, Course $course): Certificate;
}
