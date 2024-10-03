<?php

namespace App\Http\Controllers\User;

use App\Actions\User\SaveUserInvitation;
use App\Enums\Permission;
use App\Filters\UserFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Get All Users
     *
     * @group Users
     *
     * @queryParam sort string Data field(s) to sort by. Separate multiple fields with commas. Denote descending sort with a minus sign. Example: first_name,-createdAt
     * @queryParam filter[id] string Filter user by id.
     * @queryParam filter[email] string Filter user by email.
     * @queryParam search string Search user by name, email, phone.
     */
    public function index(UserFilter $filter)
    {
        return UserResource::collection(User::filter($filter)->paginate($this->getPerPage()));
    }

    /**
     * Create User
     *
     * @group Users
     */
    public function store(CreateUserRequest $request, SaveUserInvitation $saveUserInvitation)
    {
        Gate::authorize('create', User::class);

        $data = $request->validated();

        //        $data['password'] = Hash::make($data['password']);

        $user = $saveUserInvitation->execute($data);

        return new UserResource($user);
    }

    /**
     * Get User by ID
     *
     * @group Users
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    /**
     * Update User
     *
     * @group Users
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        Gate::authorize('update', $user);

        $data = $request->validated();

        //        $data['password'] = Hash::make($data['password']);

        $user->update($data);

        return new UserResource($user);
    }

    /**
     * Delete User
     *
     * @group Users
     */
    public function destroy(User $user)
    {
        $authUser = auth()->user();
        $role = $authUser->roles[0];
        $hasPermission = $authUser->hasPermissionTo(Permission::USERS_DELETE) ? 'true' : 'false';
        echo "($authUser->id {$role->name} {$role->permissions->count()} $hasPermission)";

        Gate::authorize('delete', $user);

        $user->delete();

        return response()->noContent();
    }
}
