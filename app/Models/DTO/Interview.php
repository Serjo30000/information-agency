<?php

namespace App\Models\DTO;

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
    public $user_id;
    public $status_id;
    private $user;
    private $status;

    public function __construct($id, $path_to_image, $title, $content, $source, $type, $publication_date, $user_id, $status_id) {
        $this->id = $id;
        $this->path_to_image = $path_to_image;
        $this->title = $title;
        $this->content = $content;
        $this->source = $source;
        $this->type = $type;
        $this->publication_date = $publication_date;
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

    public function getUser()
    {
        return $this->user;
    }

    public function getStatus()
    {
        return $this->status;
    }
}
