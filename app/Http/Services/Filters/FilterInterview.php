<?php

namespace App\Http\Services\Filters;

use App\Http\Services\Mappers\MapperInterview;
use App\Models\PeopleContent;

class FilterInterview
{
    public static function allInterviewsByFilter()
    {
        $peopleContents = PeopleContent::where('type', 'Interview')->get();
        $interviews = $peopleContents->map(function ($peopleContent) {
            return MapperInterview::toInterview($peopleContent);
        });
        return $interviews;
    }

    public static function listInterviewsByFilterByTop($count)
    {
        $peopleContents = PeopleContent::where('type', 'Interview')->take($count)->get();
        $interviews = $peopleContents->map(function ($peopleContent) {
            return MapperInterview::toInterview($peopleContent);
        });
        return $interviews;
    }

    public static function findInterviewByFilter($id)
    {
        $peopleContent = PeopleContent::where('type', 'Interview')->where('id', $id)->first();
        if (!$peopleContent){
            return null;
        }
        $interview = MapperInterview::toInterview($peopleContent);
        return $interview;
    }
}
