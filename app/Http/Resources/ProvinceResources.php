<?php

namespace App\Http\Resources;

use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Province
 */
class ProvinceResources extends JsonResource
{
    public function __construct(Province $resource)
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
            'iso_code' => $this->iso3166_2,
        ];
    }
}
