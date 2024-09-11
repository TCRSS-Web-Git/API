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
     * Display a listing of the resource.
     */
    public function index()
    {
        return UserResource::collection(User::paginate());
    }

    /**
     * Store a newly created resource in storage.
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
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    public function me()
    {
        $user = auth()->user();

        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        Gate::authorize('delete', $user);

        $user->delete();

        return response()->noContent();
    }
}
