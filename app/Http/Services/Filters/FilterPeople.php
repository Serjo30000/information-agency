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

    public static function listPeoplesByFilterByTop($count)
    {
        $regionsAndPeoples = RegionsAndPeoples::where('type', 'People')->take($count)->get();
        $peoples = $regionsAndPeoples->map(function ($regionsAndPeople) {
            return MapperPeople::toPeople($regionsAndPeople);
        });
        return $peoples;
    }

    public static function findPeopleByFilter($id)
    {
        $regionAndPeople = RegionsAndPeoples::where('type', 'People')->where('id', $id)->first();
        if (!$regionAndPeople){
            return null;
        }
        $people = MapperPeople::toPeople($regionAndPeople);
        return $people;
    }
}
