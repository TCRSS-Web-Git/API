<?php

namespace App\Http\Controllers;

use App\Actions\SaveTemporaryMedia;
use App\Http\Requests\UploadTemporaryMediaRequest;

class TemporaryMediaController extends Controller
{
    public function store(UploadTemporaryMediaRequest $request, SaveTemporaryMedia $saveTemporaryMedia)
    {
        $request->validated();

        $path = $saveTemporaryMedia->execute('media');

        return response()->json([
            'media' => $path,
        ]);
    }
}
