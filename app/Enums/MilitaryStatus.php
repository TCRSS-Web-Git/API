<?php

namespace App\Enums;

enum MilitaryStatus: string
{
    case IGNORE = 'ignore';
    case WOMAN = 'woman';
    case COMPLETE = 'complete';
    case ROTC = 'rotc';
}
