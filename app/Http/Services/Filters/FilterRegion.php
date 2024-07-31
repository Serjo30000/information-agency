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

    public static function listRegionsByFilterByTop($count)
    {
        $regionsAndPeoples = RegionsAndPeoples::where('type', 'Region')->take($count)->get();
        $regions = $regionsAndPeoples->map(function ($regionsAndPeople) {
            return MapperRegion::toRegion($regionsAndPeople);
        });
        return $regions;
    }

    public static function findRegionByFilter($id)
    {
        $regionAndPeople = RegionsAndPeoples::where('type', 'Region')->where('id', $id)->first();
        if (!$regionAndPeople){
            return null;
        }
        $region = MapperRegion::toRegion($regionAndPeople);
        return $region;
    }
}
