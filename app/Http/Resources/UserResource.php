<?php

namespace App\Http\Resources;

use App\Models\Role;
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
        /** @var Role $role */
        $role = $this->roles()->first();

        return [
            'id' => $this->hashid,
            'title' => $this->title,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'permissions' => $this->when($request->routeIs('user.me'), function () {
                return $this->getAllPermissions()->pluck('name');
            }),
            'role' => new RoleResource($role),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
