<?php

namespace App\Http\Services\Filters;

use App\Http\Services\Mappers\MapperPeople;
use App\Models\RegionsAndPeoples;

class FilterPeople
{
    public static function allPeoplesByFilter()
    {
        $regionsAndPeoples = RegionsAndPeoples::where('type', 'People')->get();
        $peoples = $regionsAndPeoples->map(function ($regionsAndPeople) {
            return MapperPeople::toPeople($regionsAndPeople);
        });
        return $peoples;
    }
}
