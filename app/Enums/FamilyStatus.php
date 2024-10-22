<?php

namespace App\Enums;

enum FamilyStatus: string
{
    case SINGLE = 'Single';
    case MARRIED = 'Married';
    case DIVORCED = 'Divorced';

    public function labelEn(): string
    {
        return match ($this) {
            self::SINGLE => 'Single',
            self::MARRIED => 'Married',
            self::DIVORCED => 'Divorced',
        };
    }

    public function labelTh(): string
    {
        return match ($this) {
            self::SINGLE => 'โสด',
            self::MARRIED => 'แต่งงานแล้ว',
            self::DIVORCED => 'หย่า',
        };
    }
}
