<?php

namespace App\Http\Controllers;

use App\Models\Tag;

class TagController extends Controller
{
    /**
     * Get all categories of a type.
     *
     * @group Categories
     */
    public function index(string $type)
    {
        $tags = Tag::where('type', $type)->pluck('name');

        return response()->json([
            'data' => $tags,
        ]);
    }
}
