<?php

namespace App\Http\Services\Filters;

use App\Http\Services\Mappers\MapperOpinion;
use App\Models\PeopleContent;

class FilterOpinion
{
    public static function allOpinionsByFilter()
    {
        $peopleContents = PeopleContent::whereHas('status', function ($query) {
            $query->where('status', 'Опубликовано');
        })->where('type', 'Opinion')->get();

        $opinions = $peopleContents->map(function ($peopleContent) {
            return MapperOpinion::toOpinion($peopleContent);
        });
        return $opinions;
    }

    public static function listOpinionsByFilterByTop($count)
    {
        $peopleContents = PeopleContent::whereHas('status', function ($query) {
            $query->where('status', 'Опубликовано');
        })->where('type', 'Opinion')->take($count)->get();

        $opinions = $peopleContents->map(function ($peopleContent) {
            return MapperOpinion::toOpinion($peopleContent);
        });
        return $opinions;
    }

    public static function findOpinionByFilter($id)
    {
        $peopleContent = PeopleContent::whereHas('status', function ($query) {
            $query->where('status', 'Опубликовано');
        })->where('type', 'Opinion')->where('id', $id)->first();

        if (!$peopleContent){
            return null;
        }
        $opinion = MapperOpinion::toOpinion($peopleContent);
        return $opinion;
    }

    public static function allOpinionsByFilterForPanel()
    {
        $peopleContents = PeopleContent::where('type', 'Opinion')->get();
        $opinions = $peopleContents->map(function ($peopleContent) {
            return MapperOpinion::toOpinion($peopleContent);
        });
        return $opinions;
    }

    public static function listOpinionsByFilterByTopForPanel($count)
    {
        $peopleContents = PeopleContent::where('type', 'Opinion')->take($count)->get();
        $opinions = $peopleContents->map(function ($peopleContent) {
            return MapperOpinion::toOpinion($peopleContent);
        });
        return $opinions;
    }

    public static function findOpinionByFilterForPanel($id)
    {
        $peopleContent = PeopleContent::where('type', 'Opinion')->where('id', $id)->first();
        if (!$peopleContent){
            return null;
        }
        $opinion = MapperOpinion::toOpinion($peopleContent);
        return $opinion;
    }
}
