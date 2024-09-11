<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\InviteUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class InviteController extends Controller
{
    public function invite(InviteUserRequest $request)
    {
        Gate::authorize('create', User::class);

        $data = $request->validated();

    }

    public function resend() {}

    public function accept() {}
}
