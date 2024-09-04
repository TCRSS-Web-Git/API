<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/dev', function (Request $request) {
    return 'this is dev environtment';
});
