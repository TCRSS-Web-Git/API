<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomingRequestLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'method',
        'ip',
        'uri',
        'header',
        'body',
        'response_status',
        'response_header',
        'response',
    ];

    protected $casts = [
        'header' => 'json',
        'response_header' => 'json',
    ];
}
