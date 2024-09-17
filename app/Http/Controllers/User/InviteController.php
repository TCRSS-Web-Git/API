<?php

namespace App\Http\Controllers\User;

use App\Actions\User\SaveUserInvitation;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class InviteController extends Controller
{
    use ResponseTrait;

    public function invite(CreateUserRequest $request, SaveUserInvitation $saveUserInvitation)
    {
        Gate::authorize('create', User::class);

        $data = $request->validated();

        $saveUserInvitation->execute($data);

        return $this->successResponse([], Response::HTTP_NO_CONTENT);
    }

    public function resend() {}

    public function accept() {}
}
