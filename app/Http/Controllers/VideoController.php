<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Video;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class VideoController extends Controller
{
    public function allVideos()
    {
        $videos = Video::all();

        return response()->json($videos);
    }

    public function listVideosTopFour()
    {
        $videos = Video::take(4)->get();

        return response()->json($videos);
    }

    public function listVideosTopTen()
    {
        $videos = Video::take(10)->get();

        return response()->json($videos);
    }

    public function allVideosPaginate(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $videos = Video::all();

        if ($startDate) {
            $startDate = Carbon::parse($startDate)->startOfDay();
            $videos = $videos->filter(function($video) use ($startDate) {
                return Carbon::parse($video->publication_date)->greaterThanOrEqualTo($startDate);
            });
        }

        if ($endDate) {
            $endDate = Carbon::parse($endDate)->endOfDay();
            $videos = $videos->filter(function($video) use ($endDate) {
                return Carbon::parse($video->publication_date)->lessThanOrEqualTo($endDate);
            });
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $videos->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginatedDTO = new LengthAwarePaginator(
            $currentItems,
            $videos->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return response()->json($paginatedDTO);
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
