<?php

namespace App\Http\Controllers;

use App\Http\Resources\DistrictResources;
use App\Http\Resources\ProvinceResources;
use App\Http\Resources\SubDistrictResources;
use App\Models\District;
use App\Models\Province;

class ThailandGeographyController extends Controller
{
    public function provinces()
    {
        $orderBy = 'name_en';
        if (app()->getLocale() === 'th') {
            $orderBy = 'name_th';
        }

        $province = Province::all()->sortBy($orderBy);

        return ProvinceResources::collection($province);
    }

    public function districts(Province $province)
    {
        $orderBy = 'name_en';
        if (app()->getLocale() === 'th') {
            $orderBy = 'name_th';
        }

        $districts = $province->districts->sortBy($orderBy);

        return DistrictResources::collection($districts);
    }

    public function subdistricts(District $district)
    {
        $orderBy = 'name_en';
        if (app()->getLocale() === 'th') {
            $orderBy = 'name_th';
        }

        $subdistrict = $district->subdistricts->sortBy($orderBy);

        return SubDistrictResources::collection($subdistrict);
    }
}
