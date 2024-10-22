<?php

namespace App\Enums;

enum MilitaryStatus: string
{
    case EXEMPTED = 'Exempted';
    case WOMAN = 'Woman';
    case CONSCRIPTED = 'Conscripted';
    case ROTC = 'ROTC';

    public function labelEn(): string
    {
        return match ($this) {
            self::EXEMPTED => 'Exempted',
            self::WOMAN => 'Woman',
            self::CONSCRIPTED => 'Conscripted',
            self::ROTC => 'ROTC',
        };
    }

    public function labelTh(): string
    {
        return match ($this) {
            self::EXEMPTED => 'ได้รับการยกเว้น',
            self::WOMAN => 'เพศหญิง (ยกเว้น)',
            self::CONSCRIPTED => 'รับราชการทหารแล้ว',
            self::ROTC => 'สำเร็จวิชารักษาดินแดน',
        };
    }
}
