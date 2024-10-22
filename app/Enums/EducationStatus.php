<?php

namespace App\Enums;

enum EducationStatus: string
{
    case SECONDARY = 'Secondary';
    case VOCATIONAL_CERTIFICATE = 'Vocational certificate';
    case DIPLOMA = 'Diploma';
    case BACHELOR_DEGREE = 'Bachelor degree';
    case MASTER_DEGREE = 'Master degree';
    case DOCTOR_DEGREE = 'Doctor degree';

    public function labelEn(): string
    {
        return match ($this) {
            self::SECONDARY => 'Secondary',
            self::VOCATIONAL_CERTIFICATE => 'Vocational Certificate',
            self::DIPLOMA => 'Vocational certificate / Diploma',
            self::BACHELOR_DEGREE => 'Bachelor’s Degree',
            self::MASTER_DEGREE => 'Master\'s Degree',
            self::DOCTOR_DEGREE => 'Doctor\'s Degree',
        };
    }

    public function labelTh(): string
    {
        return match ($this) {
            self::SECONDARY => 'มัธยมศึกษา',
            self::VOCATIONAL_CERTIFICATE => 'ประกาศนียบัตรวิชาชีพ - ปวช.',
            self::DIPLOMA => 'ประกาศนียบัตรวิชาชีพชั้นสูง - ปวส. / อนุปริญญา',
            self::BACHELOR_DEGREE => 'ปริญญาตรี',
            self::MASTER_DEGREE => 'ปริญญาโท',
            self::DOCTOR_DEGREE => 'ปริญญาเอก',
        };
    }
}
