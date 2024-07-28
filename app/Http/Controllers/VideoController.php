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

    public function findVideo($id){
        $video = Video::find($id);

        if ($video){
            return response()->json($video);
        }
        else{
            return response()->json(['message' => 'Video not found'], 404);
        }
    }
}
