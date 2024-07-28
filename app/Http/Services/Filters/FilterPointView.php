<?php

namespace App\Http\Services\Filters;

use App\Http\Services\Mappers\MapperPointView;
use App\Models\PeopleContent;

class FilterPointView
{
    public static function allPointViewsByFilter()
    {
        $peopleContents = PeopleContent::where('type', 'PointView')->get();
        $pointViews = $peopleContents->map(function ($peopleContent) {
            return MapperPointView::toPointView($peopleContent);
        });
        return $pointViews;
    }

    public static function findPointViewByFilter($id)
    {
        $peopleContent = PeopleContent::where('type', 'PointView')->where('id', $id)->first();
        $pointView = MapperPointView::toPointView($peopleContent);
        return $pointView;
    }
}
