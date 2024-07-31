<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $table = 'news';

    protected $fillable = [
        'path_to_image_or_video',
        'title',
        'content',
        'source',
        'publication_date',
        'user_id',
        'regions_and_peoples_id',
        'status_id',
    ];

    protected $casts = [
        'publication_date' => 'datetime:d.m.Y H:i',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function regionsAndPeoples()
    {
        return $this->belongsTo(RegionsAndPeoples::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function grandNews()
    {
        return $this->hasMany(GrandNews::class);
    }
}
