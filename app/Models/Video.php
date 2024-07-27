<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $table = 'videos';

    protected $fillable = [
        'path_to_video',
        'title',
        'source',
        'publication_date',
        'user_id',
        'status_id',
    ];

    protected $casts = [
        'publication_date' => 'datetime:d.m.Y H:i',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }
}
