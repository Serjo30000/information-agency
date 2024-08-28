<?php

namespace App\Http\Services\Mappers;

use App\Models\DTO\Interview;
use App\Models\PeopleContent;

class MapperInterview
{
    public static function toInterview(PeopleContent $peopleContent): Interview
    {
        $interview = new Interview(
            $peopleContent->id,
            $peopleContent->path_to_image,
            $peopleContent->title,
            $peopleContent->content,
            $peopleContent->source,
            $peopleContent->type,
            $peopleContent->publication_date,
            $peopleContent->regions_and_peoples_id,
            $peopleContent->user_id,
            $peopleContent->status_id,
        );

        $interview->getRegionsAndPeoples();

        $interview->getStatus();

        return $interview;
    }

    public static function toPeopleContent(Interview $interview): PeopleContent
    {
        $peopleContent = new PeopleContent(
            [
                'id' => $interview->id,
                'path_to_image' => $interview->path_to_image,
                'title' => $interview->title,
                'content' => $interview->content,
                'source' => $interview->source,
                'type' => $interview->type,
                'publication_date' => $interview->publication_date,
                'regions_and_peoples_id' => $interview->regions_and_peoples_id,
                'user_id' => $interview->user_id,
                'status_id' => $interview->status_id,
            ]
        );

        return $peopleContent;
    }
}
