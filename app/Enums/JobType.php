<?php

namespace App\Enums;

enum JobType: int
{
    case FULL_TIME = 10;
    case PART_TIME = 20;

    public function label(): string
    {
        return match ($this) {
            self::FULL_TIME => 'Full-Time',
            self::PART_TIME => 'Part-Time',
        };
    }
}
