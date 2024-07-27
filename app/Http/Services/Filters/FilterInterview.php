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
}
