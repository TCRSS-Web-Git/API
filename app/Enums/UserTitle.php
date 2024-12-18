<?php

namespace App\Enums;

enum UserTitle: string
{
    case MR = 'Mr.'; // นาย
    case MRS = 'Mrs.'; // นาง
    //    case MISS = 'Miss';
    case MS = 'Ms.'; // นางสาว
    //    case OTHER = 'Other';

    public function label(): string
    {
        // check locale and return correct label
        return match (app()->getLocale()) {
            'en' => $this->labelEn(),
            'th' => $this->labelTh(),
            default => $this->value,
        };
    }

    public function labelEn(): string
    {
        return match ($this) {
            self::MR => 'Mr.',
            self::MRS => 'Mrs.',
            self::MS => 'Ms.',
        };
    }

    public function labelTh(): string
    {
        return match ($this) {
            self::MR => 'นาย',
            self::MRS => 'นาง',
            self::MS => 'นางสาว',
        };
    }
}
