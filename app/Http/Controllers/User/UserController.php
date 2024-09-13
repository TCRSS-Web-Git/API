<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateOrUpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    /**
     * Get All Users
     *
     * @group Users
     * @queryParam sort string Data field(s) to sort by. Separate multiple fields with commas. Denote descending sort with a minus sign. Example: title,-createdAt
     * @queryParam filter[search] string Search user by name, email.
     */
    public function index()
    {
        return UserResource::collection(User::paginate());
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

        $data['password'] = bcrypt($data['password']);

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
     * Update User
     *
     * @group Users
     */
    public function update(CreateOrUpdateUserRequest $request, User $user)
    {
        Gate::authorize('update', $user);

        $data = $request->validated();

        $data['password'] = bcrypt($data['password']);

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
