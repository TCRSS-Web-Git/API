<?php

namespace App\Http\Controllers\User;

use App\Actions\User\ResendUserInvitation;
use App\Actions\User\SaveUserInvitation;
use App\Actions\User\SetPasswordForNewUser;
use App\Exceptions\InvalidInviteTokenException;
use App\Http\Controllers\Controller;
use App\Http\Requests\AcceptUserInvitationRequest;
use App\Http\Requests\CreateUserRequest;
use App\Models\User;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class InviteController extends Controller
{
    use ResponseTrait;

    /**
     * @throws Exception
     */
    public function invite(CreateUserRequest $request, SaveUserInvitation $saveUserInvitation): JsonResponse
    {
        Gate::authorize('create', User::class);

        $data = $request->validated();

        $saveUserInvitation->execute($data);

        return $this->successResponse([], Response::HTTP_NO_CONTENT);
    }

    public function resend(User $user, ResendUserInvitation $resendUserInvitation): JsonResponse
    {
        $resendUserInvitation->execute($user);

        return $this->successResponse([], Response::HTTP_NO_CONTENT);
    }

    /**
     * @throws InvalidInviteTokenException
     */
    public function accept(AcceptUserInvitationRequest $request, SetPasswordForNewUser $setPasswordForNewUser): JsonResponse
    {
        $data = $request->validated();
        $email = $request->query('email');
        $token = $request->query('token');

        $setPasswordForNewUser->execute($data, $email, $token);

        return $this->successResponse([], Response::HTTP_NO_CONTENT);
    }
}
