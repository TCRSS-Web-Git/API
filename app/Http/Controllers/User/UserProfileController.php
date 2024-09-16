<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateOrUpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class UserProfileController extends Controller
{

    /**
     * Get authenticated user profile
     *
     * @group Users
     */
    public function me()
    {
        $user = auth()->user();

        return new UserResource($user);
    }

    /**
     * Update User Profile
     *
     * @group Users
     */
    public function updateProfile(CreateOrUpdateUserRequest $request)
    {
        $data = $request->validated();

        $user = auth()->user();

        $user->prefix = $data['prefix'];
        $user->first_name = $data['first_name'];
        $user->last_name = $data['last_name'];
        $user->save();

        return new UserResource($user);
    }

    /**
     * Update Password
     *
     * @group Users
     */
    public function updatePassword(CreateOrUpdateUserRequest $request)
    {
        $data = $request->validated();

        $user = auth()->user();

        $user->password = bcrypt($data['password']);
        $user->save();

        return new UserResource($user);
    }
}
