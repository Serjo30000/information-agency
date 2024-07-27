<?php

namespace App\Http\Services\Mappers;

use App\Models\DTO\Interview;
use App\Models\DTO\Region;
use App\Models\PeopleContent;
use App\Models\RegionsAndPeoples;

class MapperRegion
{
    public static function toRegion(RegionsAndPeoples $regionsAndPeoples): Region
    {
        $region = new Region(
            $regionsAndPeoples->id,
            $regionsAndPeoples->path_to_image,
            $regionsAndPeoples->position_or_type_region,
            $regionsAndPeoples->fio_or_name_region,
            $regionsAndPeoples->content,
            $regionsAndPeoples->type,
            $regionsAndPeoples->date_birth_or_date_foundation,
        );

        return $region;
    }

    public static function toRegionsAndPeoples(Region $region): RegionsAndPeoples
    {
        $regionsAndPeoples = new RegionsAndPeoples(
            [
                'id' => $region->id,
                'path_to_image' => $region->path_to_image,
                'position_or_type_region' => $region->type_region,
                'fio_or_name_region' => $region->content,
                'content' => $region->content,
                'type' => $region->type,
                'date_birth_or_date_foundation' => $region->date_foundation,
            ]
        );

        return $regionsAndPeoples;
    }
}
