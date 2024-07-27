<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    protected $table = 'statuses';

    protected $fillable = [
        'order',
        'status',
    ];

    public function news()
    {
        return $this->hasMany(News::class);
    }

    public function peopleContent()
    {
        return $this->hasMany(PeopleContent::class);
    }
}
