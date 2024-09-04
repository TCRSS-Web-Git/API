<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/dev-api', function (Request $request) {
    return response()->json(['message' => 'this is dev environtment']);
});
