<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeopleContent extends Model
{
    use HasFactory;

    protected $table = 'people_contents';

    protected $fillable = [
        'path_to_image',
        'title',
        'content',
        'source',
        'type',
        'publication_date',
        'sys_Comment',
        'delete_mark',
        'regions_and_peoples_id',
        'user_id',
        'status_id',
    ];

    protected $casts = [
        'publication_date' => 'datetime:d.m.Y H:i',
    ];

    public function regionsAndPeoples()
    {
        return $this->belongsTo(RegionsAndPeoples::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }
}
