<?php 

namespace App\Features\User\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case STUDENT = 'student';
}
?>