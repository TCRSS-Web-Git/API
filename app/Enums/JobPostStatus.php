<?php

namespace App\Enums;

enum JobPostStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
}
