<?php

namespace App\Http\Controllers\User;

use App\Filters\UserFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateOrUpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\PaginateTrait;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use PaginateTrait;

    /**
     * Get All Users
     *
     * @group Users
     *
     * @queryParam sort string Data field(s) to sort by. Separate multiple fields with commas. Denote descending sort with a minus sign. Example: title,-createdAt
     * @queryParam filter[search] string Search user by name, email.
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
    public function store(CreateOrUpdateUserRequest $request)
    {
        Gate::authorize('create', User::class);

        $data = $request->validated();

        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

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
    public function update(CreateOrUpdateUserRequest $request, User $user)
    {
        Gate::authorize('update', $user);

        $data = $request->validated();

        $data['password'] = Hash::make($data['password']);

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
        Gate::authorize('delete', $user);

        $user->delete();

        return response()->noContent();
    }
}
