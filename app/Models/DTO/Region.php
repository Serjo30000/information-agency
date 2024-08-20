<?php

namespace App\Models\DTO;

use App\Models\News;
use App\Models\PeopleContent;

class Region
{
    public $id;
    public $path_to_image;
    public $type_region;
    public $name_region;
    public $content;
    public $type;
    public $date_foundation;
    private $peopleContents;
    private $news;

    public function __construct($id, $path_to_image, $type_region, $name_region, $content, $type, $date_foundation) {
        $this->id = $id;
        $this->path_to_image = $path_to_image;
        $this->type_region = $type_region;
        $this->name_region = $name_region;
        $this->content = $content;
        $this->type = $type;
        $this->date_foundation = $date_foundation;
    }

    private function loadPeopleContents()
    {
        $this->peopleContents = PeopleContent::where('regions_and_peoples_id', $this->id);
    }

    public function getPeopleContents()
    {
        return $this->peopleContents;
    }

    public function getNews()
    {
        $this->news = News::where('regions_and_peoples_id', $this->id);

        return $this->news;
    }
}
