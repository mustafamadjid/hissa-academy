<?php

namespace App\Features\UserProgress\Enums;

enum LessonProgressStatus: string
{
    case NOT_STARTED = 'not_started';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
}
?>