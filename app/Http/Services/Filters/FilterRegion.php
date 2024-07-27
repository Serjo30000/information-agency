<?php

namespace App\Http\Services\Filters;

use App\Http\Services\Mappers\MapperRegion;
use App\Models\RegionsAndPeoples;

class FilterRegion
{
    public static function allRegionsByFilter()
    {
        $regionsAndPeoples = RegionsAndPeoples::where('type', 'Region')->get();
        $regions = $regionsAndPeoples->map(function ($regionsAndPeople) {
            return MapperRegion::toRegion($regionsAndPeople);
        });
        return $regions;
    }
}
