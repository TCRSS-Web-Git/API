<?php

namespace App\Actions\User;

use App\Mail\UserInvitation;
use App\Models\Invite;
use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class SaveUserInvitation
{
    /**
     * @throws Exception
     */
    public function execute(array $data): User
    {
        $token = Str::random(40);

        DB::beginTransaction();

        try {
            $user = User::create($data);

            $user->assignRole(Role::find($data['role_id']));

            $invite = Invite::create([
                'email' => $data['email'],
                'token' => $token,
                'user_id' => $user->id,
            ]);

            DB::commit();

            $this->sendInviteEmail($invite, $user, $token);
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        return $user;
    }

    private function sendInviteEmail(Invite $invite, User $user, string $token): void
    {
        $url = URL::temporarySignedRoute(
            'accept.users.invitation',
            now()->addHours(24),
            ['email' => $invite->email, 'token' => $token],
            false
        );

        Mail::to($invite->email)->send(new UserInvitation($user, $url));
    }
}
