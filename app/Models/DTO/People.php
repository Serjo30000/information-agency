<?php

namespace App\Models\DTO;

use App\Models\News;
use App\Models\PeopleContent;

class People
{
    public $id;
    public $path_to_image;
    public $position;
    public $fio;
    public $place_work;
    public $content;
    public $type;
    public $date_birth;
    private $peopleContents;
    private $news;

    public function __construct($id, $path_to_image, $position, $fio, $place_work, $content, $type, $date_birth) {
        $this->id = $id;
        $this->path_to_image = $path_to_image;
        $this->position = $position;
        $this->fio = $fio;
        $this->place_work = $place_work;
        $this->content = $content;
        $this->type = $type;
        $this->date_birth = $date_birth;
    }

    public function getPeopleContents()
    {
        $this->peopleContents = PeopleContent::where('regions_and_peoples_id', $this->id);

        return $this->peopleContents;
    }

    public function getNews()
    {
        $this->news = News::where('regions_and_peoples_id', $this->id);

        return $this->news;
    }
}
