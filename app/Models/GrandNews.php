<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrandNews extends Model
{
    use HasFactory;

    protected $table = 'grand_news';

    protected $fillable = [
        'start_publication_date',
        'end_publication_date',
        'priority',
        'news_id',
    ];

    protected $casts = [
        'start_publication_date' => 'datetime:d.m.Y H:i:s',
        'end_publication_date' => 'datetime:d.m.Y H:i:s',
    ];

    public function news()
    {
        return $this->belongsTo(News::class);
    }
}
