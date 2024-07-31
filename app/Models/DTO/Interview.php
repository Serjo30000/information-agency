<?php

namespace App\Models\DTO;

use App\Models\RegionsAndPeoples;
use App\Models\Status;
use App\Models\User;

class Interview
{
    public $id;
    public $path_to_image;
    public $title;
    public $content;
    public $source;
    public $type;
    public $publication_date;
    public $regions_and_peoples_id;
    public $user_id;
    public $status_id;
    private $user;
    private $status;
    private $regions_and_peoples;

    public function __construct($id, $path_to_image, $title, $content, $source, $type, $publication_date, $regions_and_peoples_id, $user_id, $status_id) {
        $this->id = $id;
        $this->path_to_image = $path_to_image;
        $this->title = $title;
        $this->content = $content;
        $this->source = $source;
        $this->type = $type;
        $this->publication_date = $publication_date;
        $this->regions_and_peoples_id = $regions_and_peoples_id;
        $this->user_id = $user_id;
        $this->status_id = $status_id;
    }

    private function loadRegionsAndPeoples()
    {
        if ($this->regions_and_peoples_id) {
            $this->regions_and_peoples = RegionsAndPeoples::find($this->regions_and_peoples_id);
        }
    }

    private function loadUser()
    {
        if ($this->user_id) {
            $this->user = User::find($this->user_id);
        }
    }

    private function loadStatus()
    {
        if ($this->status_id) {
            $this->status = Status::find($this->status_id);
        }
    }

    public function getRegionsAndPeoples()
    {
        return $this->regions_and_peoples;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getStatus()
    {
        return $this->status;
    }
}
