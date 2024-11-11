<?php

namespace App\Http\Controllers\User;

use App\Enums\UserTitle;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserTitleController extends Controller
{
    /**
     * Get user title options
     *
     * @unauthenticated
     *
     * @group Users
     */
    public function index(Request $request)
    {
        $titles = collect(UserTitle::cases())->map(function ($title) {
            return [
                'value' => $title,
                'label' => $title->label(),
            ];
        });

        return response()->json([
            'data' => $titles,
        ]);
    }
}
