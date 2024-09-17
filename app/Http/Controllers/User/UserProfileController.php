<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserProfile\UpdateUserPasswordRequest;
use App\Http\Requests\UserProfile\UpdateUserProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

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
    public function updateProfile(UpdateUserProfileRequest $request)
    {
        $data = $request->validated();

        $user = auth()->user();

        $user->title = $data['title'] ?? null;
        $user->first_name = $data['first_name'];
        $user->last_name = $data['last_name'];
        $user->phone = $data['phone'] ?? null;
        $user->save();

        return new UserResource($user);
    }

    /**
     * Update Password
     *
     * @group Users
     */
    public function updatePassword(UpdateUserPasswordRequest $request)
    {
        $data = $request->validated();
        $user = auth()->user();

        if (! isset($data['current_password']) || ! Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => __('validation.current_password'),
            ]);
        }

        $user->password = Hash::make($data['password']);
        $user->save();

        return new UserResource($user);
    }
}
