<?php

namespace App\Models\DTO;

use App\Models\Status;
use App\Models\User;

class PointView
{
    public $id;
    public $title;
    public $content;
    public $type;
    public $user_id;
    public $status_id;
    private $user;
    private $status;

    public function __construct($id, $title, $content, $type, $user_id, $status_id) {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->type = $type;
        $this->user_id = $user_id;
        $this->status_id = $status_id;

        $this->loadUser();
        $this->loadStatus();
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

    public function getUser()
    {
        return $this->user;
    }

    public function getStatus()
    {
        return $this->status;
    }
}
