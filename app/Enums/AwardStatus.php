<?php

namespace App\Enums;

enum AwardStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
}
