<?php

namespace App\Models\DTO;

use App\Models\RegionsAndPeoples;
use App\Models\Status;
use App\Models\User;

class PointView
{
    public $id;
    public $title;
    public $content;
    public $type;
    public $regions_and_peoples_id;
    public $user_id;
    public $status_id;
    private $user;
    private $status;
    private $regions_and_peoples;

    public function __construct($id, $title, $content, $type, $regions_and_peoples_id, $user_id, $status_id) {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->regions_and_peoples_id = $regions_and_peoples_id;
        $this->type = $type;
        $this->user_id = $user_id;
        $this->status_id = $status_id;
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

    private function loadRegionsAndPeoples()
    {
        if ($this->regions_and_peoples_id) {
            $this->regions_and_peoples = RegionsAndPeoples::find($this->regions_and_peoples_id);
        }
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getRegionsAndPeoples()
    {
        return $this->regions_and_peoples;
    }
}
