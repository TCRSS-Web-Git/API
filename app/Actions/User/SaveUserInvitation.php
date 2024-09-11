<?php

namespace App\Actions\User;

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
    public function execute(array $data): Invite
    {
        $token = Str::random(40);

        DB::beginTransaction();

        try {
            $data['password'] = bcrypt($data['password']);

            $user = User::create($data);

            $user->assignRole(Role::find($data['role_id']));

            $invite = Invite::create([
                'email' => $data['email'],
                'token' => $token,
                'user_id' => $user->id,
            ]);

            DB::commit();

            //            $url = URL::temporarySignedRoute('accept.users.invitation', now()->addHours(24), ['email' => $invite->email, 'token' => $token], false);

            //            Mail::to($invite->email)->send(new EmployeeInvitation($user, $url));

        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        return $invite;
    }
}
