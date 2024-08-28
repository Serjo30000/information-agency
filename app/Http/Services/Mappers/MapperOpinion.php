<?php

namespace App\Http\Services\Mappers;

use App\Models\DTO\Opinion;
use App\Models\PeopleContent;

class MapperOpinion
{
    public static function toOpinion(PeopleContent $peopleContent): Opinion
    {
        $opinion = new Opinion(
            $peopleContent->id,
            $peopleContent->path_to_image,
            $peopleContent->title,
            $peopleContent->content,
            $peopleContent->type,
            $peopleContent->publication_date,
            $peopleContent->regions_and_peoples_id,
            $peopleContent->user_id,
            $peopleContent->status_id,
        );

        $opinion->getRegionsAndPeoples();

        $opinion->getStatus();

        return $opinion;
    }

    public static function toPeopleContent(Opinion $opinion): PeopleContent
    {
        $peopleContent = new PeopleContent(
            [
                'id' => $opinion->id,
                'path_to_image' => $opinion->path_to_image,
                'title' => $opinion->title,
                'content' => $opinion->content,
                'type' => $opinion->type,
                'publication_date' => $opinion->publication_date,
                'regions_and_peoples_id' => $opinion->regions_and_peoples_id,
                'user_id' => $opinion->user_id,
                'status_id' => $opinion->status_id,
            ]
        );

        return $peopleContent;
    }
}
