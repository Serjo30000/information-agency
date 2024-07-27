<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function allVideos()
    {
        $videos = Video::all();

        return response()->json($videos);
    }
}
