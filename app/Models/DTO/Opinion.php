<?php

namespace App\Models\DTO;

use App\Models\RegionsAndPeoples;
use App\Models\Status;
use App\Models\User;

class Opinion
{
    public $id;
    public $path_to_image;
    public $title;
    public $content;
    public $type;
    public $publication_date;
    public $regions_and_peoples_id;
    public $user_id;
    public $status_id;
    private $user;
    public $status;
    public $regions_and_peoples;

    public function __construct($id, $path_to_image, $title, $content, $type, $publication_date, $regions_and_peoples_id, $user_id, $status_id) {
        $this->id = $id;
        $this->path_to_image = $path_to_image;
        $this->title = $title;
        $this->content = $content;
        $this->type = $type;
        $this->publication_date = $publication_date;
        $this->regions_and_peoples_id = $regions_and_peoples_id;
        $this->user_id = $user_id;
        $this->status_id = $status_id;
    }

    public function getUser()
    {
        if ($this->user_id) {
            $this->user = User::find($this->user_id);
        }

        return $this->user;
    }

    public function getStatus()
    {
        if ($this->status_id) {
            $this->status = Status::find($this->status_id);
        }

        return $this->status;
    }

    public function getRegionsAndPeoples()
    {
        if ($this->regions_and_peoples_id) {
            $this->regions_and_peoples = RegionsAndPeoples::find($this->regions_and_peoples_id);
        }

        return $this->regions_and_peoples;
    }
}
