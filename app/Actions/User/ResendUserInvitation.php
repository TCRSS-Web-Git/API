<?php

namespace App\Actions\User;

use App\Mail\UserInvitation;
use App\Models\Invite;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class ResendUserInvitation
{
    public function execute(User $user): Invite
    {
        $invite = Invite::whereUserId($user->id)->first();

        if (! $invite) {
            $token = Str::random(40);
            $invite = Invite::create([
                'email' => $user->email,
                'token' => $token,
                'user_id' => $user->id,
            ]);
        }

        $this->sendInviteEmail($invite, $user, $invite->token);

        return $invite;
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
