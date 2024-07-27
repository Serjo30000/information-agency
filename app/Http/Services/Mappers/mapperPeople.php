<?php

namespace App\Http\Services\Mappers;

use App\Models\DTO\People;
use App\Models\DTO\Region;
use App\Models\RegionsAndPeoples;

class mapperPeople
{
    public static function toPeople(RegionsAndPeoples $regionsAndPeoples): People
    {
        $people = new People(
            $regionsAndPeoples->id,
            $regionsAndPeoples->path_to_image,
            $regionsAndPeoples->position_or_type_region,
            $regionsAndPeoples->fio_or_name_region,
            $regionsAndPeoples->place_work,
            $regionsAndPeoples->content,
            $regionsAndPeoples->type,
            $regionsAndPeoples->date_birth_or_date_foundation,
        );

        return $people;
    }

    public static function toRegionsAndPeoples(People $people): RegionsAndPeoples
    {
        $regionsAndPeoples = new RegionsAndPeoples(
            [
                'id' => $people->id,
                'path_to_image' => $people->path_to_image,
                'position_or_type_region' => $people->position,
                'fio_or_name_region' => $people->content,
                'place_work' => $people->place_work,
                'content' => $people->content,
                'type' => $people->type,
                'date_birth_or_date_foundation' => $people->date_birth,
            ]
        );

        return $regionsAndPeoples;
    }
}
