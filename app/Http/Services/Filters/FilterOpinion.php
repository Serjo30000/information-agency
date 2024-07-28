<?php

namespace App\Http\Services\Filters;

use App\Http\Services\Mappers\MapperOpinion;
use App\Models\PeopleContent;

class FilterOpinion
{
    public static function allOpinionsByFilter()
    {
        $peopleContents = PeopleContent::where('type', 'Opinion')->get();
        $opinions = $peopleContents->map(function ($peopleContent) {
            return MapperOpinion::toOpinion($peopleContent);
        });
        return $opinions;
    }

    public static function findOpinionByFilter($id)
    {
        $peopleContent = PeopleContent::where('type', 'Opinion')->where('id', $id)->first();
        $opinion = MapperOpinion::toOpinion($peopleContent);
        return $opinion;
    }
}
