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
        $videos = Video::whereHas('status', function ($query) {
            $query->where('status', 'Опубликовано');
        })->get();

        return response()->json($videos, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function listVideosTopFour()
    {
        $videos = Video::whereHas('status', function ($query) {
            $query->where('status', 'Опубликовано');
        })
            ->take(4)
            ->get();

        return response()->json($videos, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function listVideosTopTen()
    {
        $videos = Video::whereHas('status', function ($query) {
            $query->where('status', 'Опубликовано');
        })
            ->take(10)
            ->get();

        return response()->json($videos, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function allVideosPaginate(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $videos = Video::whereHas('status', function ($query) {
            $query->where('status', 'Опубликовано');
        })->get();

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

        return response()->json($paginatedDTO, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function findVideo($id){
        $video = Video::where('id', $id)
            ->whereHas('status', function ($query) {
                $query->where('status', 'Опубликовано');
            })
            ->first();

        if ($video){
            return response()->json($video, 200, [], JSON_UNESCAPED_UNICODE);
        }
        else{
            return response()->json(['message' => 'Video not found'], 404, [], JSON_UNESCAPED_UNICODE);
        }
    }






    public function allVideosForPanel()
    {
        $videos = Video::all();

        return response()->json($videos, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function allVideosPaginateForPanel(Request $request)
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

        return response()->json($paginatedDTO, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function findVideoForPanel($id){
        $video = Video::find($id);

        if ($video){
            return response()->json($video, 200, [], JSON_UNESCAPED_UNICODE);
        }
        else{
            return response()->json(['message' => 'Video not found'], 404, [], JSON_UNESCAPED_UNICODE);
        }
    }
}
