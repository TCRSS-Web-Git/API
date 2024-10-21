<?php

namespace App\Enums;

enum FamilyStatus: string
{
    case SINGLE = 'Single';
    case MARRIED = 'Married';
    case DIVORCED = 'Divorced';
}
