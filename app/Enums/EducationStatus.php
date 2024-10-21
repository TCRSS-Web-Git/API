<?php

namespace App\Enums;

enum EducationStatus: string
{
    case SECONDARY = 'Secondary';
    case VOCATIONAL_CERTIFICATE = 'Vocational certificate';
    case DIPLOMA = 'diploma';
    case BACHELOR_DEGREE = 'Bachelor_degree';

    case MASTER_DEGREE = 'Master_degree';
}
