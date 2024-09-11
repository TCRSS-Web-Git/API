<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class UserResource extends JsonResource
{
    public function __construct(User $resource)
    {
        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->hashid,
            'name' => $this->name,
            'email' => $this->email,
            'permissions' => $this->when($request->routeIs('users.me'), $this->getAllPermissions()->pluck('name')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
