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

    public static function allRegionsByFilterAndSearch($searchTerm = null, $selectedRegions = [])
    {
        $query = RegionsAndPeoples::where('type', 'Region')->where('position_or_type_region','!=','ПГТ')->where('position_or_type_region','!=','Город')->where('position_or_type_region','!=','Страна');

        if ($searchTerm) {
            $query->where('fio_or_name_region', 'LIKE', '%' . $searchTerm . '%');
        }

        $regionsAndPeoples = $query->get();

        if (!empty($selectedRegions)) {
            $selectedRegionsAndPeoples = RegionsAndPeoples::where('type', 'Region')
                ->whereIn('id', $selectedRegions)
                ->get();
            $regionsAndPeoples = $regionsAndPeoples->merge($selectedRegionsAndPeoples);
        }

        $regions = $regionsAndPeoples->map(function ($regionsAndPeople) use ($selectedRegions) {
            $region = MapperRegion::toRegion($regionsAndPeople);
            $region->selected = in_array($region->id, $selectedRegions);
            return $region;
        });

        $regions = $regions->unique('id')->values();

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
