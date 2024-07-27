<?php

namespace App\Http\Services\Mappers;

use App\Models\DTO\PointView;
use App\Models\PeopleContent;

class mapperPointView
{
    public static function toPointView(PeopleContent $peopleContent)
    {
        $pointView = new PointView(
            $peopleContent->id,
            $peopleContent->title,
            $peopleContent->content,
            $peopleContent->type,
            $peopleContent->user_id,
            $peopleContent->status_id,
        );

        return $pointView;
    }

    public static function toPeopleContent(PointView $pointView)
    {
        $peopleContent = new PeopleContent(
            [
                'id' => $pointView->id,
                'title' => $pointView->title,
                'content' => $pointView->content,
                'type' => $pointView->type,
                'user_id' => $pointView->user_id,
                'status_id' => $pointView->status_id,
            ]
        );

        return $peopleContent;
    }
}
