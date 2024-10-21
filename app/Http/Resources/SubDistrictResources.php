<?php

namespace App\Http\Resources;

use App\Models\Subdistrict;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Subdistrict
 */
class SubDistrictResources extends JsonResource
{
    public function __construct(Subdistrict $resource)
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
            'name_th' => $this->name_th,
            'name_en' => $this->name_en,
            'zip' => $this->zip,
        ];
    }
}
