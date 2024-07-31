<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegionsAndPeoples extends Model
{
    use HasFactory;
    protected $table = 'regions_and_peoples';

    protected $fillable = [
        'path_to_image',
        'position_or_type_region',
        'fio_or_name_region',
        'place_work',
        'content',
        'type',
        'date_birth_or_date_foundation',
    ];

    protected $casts = [
        'date_birth_or_date_foundation' => 'datetime:d.m.Y',
    ];

    public function peopleContent()
    {
        return $this->hasMany(PeopleContent::class);
    }

    public function news()
    {
        return $this->hasMany(News::class);
    }
}
