<?php

namespace App\Http\Services\Filters;

use App\Http\Services\Mappers\MapperPointView;
use App\Models\PeopleContent;

class FilterPointView
{
    public static function allPointViewsByFilter()
    {
        $peopleContents = PeopleContent::whereHas('status', function ($query) {
            $query->where('status', 'Опубликовано');
        })->where('type', 'PointView')->get();

        $pointViews = $peopleContents->map(function ($peopleContent) {
            return MapperPointView::toPointView($peopleContent);
        });
        return $pointViews;
    }

    public static function listPointViewsByFilterByTop($count)
    {
        $peopleContents = PeopleContent::whereHas('status', function ($query) {
            $query->where('status', 'Опубликовано');
        })->where('type', 'PointView')->take($count)->get();

        $pointViews = $peopleContents->map(function ($peopleContent) {
            return MapperPointView::toPointView($peopleContent);
        });
        return $pointViews;
    }

    public static function findPointViewByFilter($id)
    {
        $peopleContent = PeopleContent::whereHas('status', function ($query) {
            $query->where('status', 'Опубликовано');
        })->where('type', 'PointView')->where('id', $id)->first();

        if (!$peopleContent){
            return null;
        }
        $pointView = MapperPointView::toPointView($peopleContent);
        return $pointView;
    }

    public static function allPointViewsByFilterForPanel()
    {
        $peopleContents = PeopleContent::where('type', 'PointView')->get();
        $pointViews = $peopleContents->map(function ($peopleContent) {
            return MapperPointView::toPointView($peopleContent);
        });
        return $pointViews;
    }

    public static function listPointViewsByFilterByTopForPanel($count)
    {
        $peopleContents = PeopleContent::where('type', 'PointView')->take($count)->get();
        $pointViews = $peopleContents->map(function ($peopleContent) {
            return MapperPointView::toPointView($peopleContent);
        });
        return $pointViews;
    }

    public static function findPointViewByFilterForPanel($id)
    {
        $peopleContent = PeopleContent::where('type', 'PointView')->where('id', $id)->first();
        if (!$peopleContent){
            return null;
        }
        $pointView = MapperPointView::toPointView($peopleContent);
        return $pointView;
    }
}
