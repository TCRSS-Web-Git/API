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
        if (app()->getLocale() === 'th') {
            $orderBy = 'name_th';
        }

        return [
            'id' => $this->hashid,
            'name' => app()->getLocale() === 'th' ? $this->name_th : $this->name_en,
            'name_th' => $this->name_th,
            'name_en' => $this->name_en,
            'iso_code' => $this->iso3166_2,
        ];
    }
}
