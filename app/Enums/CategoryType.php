<?php

namespace App\Enums;

enum CategoryType: string
{
    case BLOG = 'blog';
    case DEPARTMENT = 'department';
    case LOCATION = 'location';
    case CAREER_TYPE = 'career_type';
}
