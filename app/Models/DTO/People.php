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
    public $sys_Comment;
    public $delete_mark;
    private $peopleContents;
    private $news;

    public function __construct($id, $path_to_image, $position, $fio, $place_work, $content, $type, $date_birth, $sys_Comment, $delete_mark) {
        $this->id = $id;
        $this->path_to_image = $path_to_image;
        $this->position = $position;
        $this->fio = $fio;
        $this->place_work = $place_work;
        $this->content = $content;
        $this->type = $type;
        $this->date_birth = $date_birth;
        $this->sys_Comment = $sys_Comment;
        $this->delete_mark = $delete_mark;
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
