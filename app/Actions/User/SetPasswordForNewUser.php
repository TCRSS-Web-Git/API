<?php

namespace App\Actions\User;

use App\Exceptions\InvalidInviteTokenException;
use App\Models\Invite;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SetPasswordForNewUser
{
    /**
     * @throws InvalidInviteTokenException
     * @throws Exception
     */
    public function execute(array $data, string $email, string $token): User
    {
        $invite = $this->getInvite($email, $token);

        if (! $invite) {
            throw new InvalidInviteTokenException;
        }

        try {
            /* @var User $user */
            $user = $invite->user;
            $this->updateUser($user, $data);
            $invite->delete();

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        return $user;
    }

    private function getInvite(string $email, string $token): ?Invite
    {
        return Invite::where('email', $email)
            ->where('token', $token)
            ->first();
    }

    private function updateUser(User $user, array $data): void
    {
        $user->update([
            'password' => Hash::make($data['password']),
            'email_verified_at' => now(),
        ]);
    }
}
