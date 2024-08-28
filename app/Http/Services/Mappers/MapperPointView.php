<?php

namespace App\Http\Services\Mappers;

use App\Models\DTO\PointView;
use App\Models\PeopleContent;

class MapperPointView
{
    public static function toPointView(PeopleContent $peopleContent): PointView
    {
        $pointView = new PointView(
            $peopleContent->id,
            $peopleContent->title,
            $peopleContent->content,
            $peopleContent->type,
            $peopleContent->regions_and_peoples_id,
            $peopleContent->user_id,
            $peopleContent->status_id,
        );

        $pointView->getRegionsAndPeoples();

        $pointView->getStatus();

        return $pointView;
    }

    public static function toPeopleContent(PointView $pointView): PeopleContent
    {
        $peopleContent = new PeopleContent(
            [
                'id' => $pointView->id,
                'title' => $pointView->title,
                'content' => $pointView->content,
                'type' => $pointView->type,
                'regions_and_peoples_id' => $pointView->regions_and_peoples_id,
                'user_id' => $pointView->user_id,
                'status_id' => $pointView->status_id,
            ]
        );

        return $peopleContent;
    }
}
