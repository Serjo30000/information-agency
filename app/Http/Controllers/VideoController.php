<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Status;
use App\Models\Video;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

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

    public function createVideo(Request $request){
        $rules = [
            'path_to_video' => 'required|string|unique:videos,path_to_video',
            'title' => 'required|string',
            'source' => 'required|string',
            'publication_date' => [
                'required',
                'string',
                'date_format:"Y-m-d H:i:s"',
            ],
        ];

        try {
            $request->validate($rules);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed'
            ], 422);
        }

        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401);
        }

        $status = Status::where('status', 'Создал')->first();

        if (!$status) {
            return response()->json([
                'success' => false,
                'message' => 'Status not found'
            ], 404);
        }

        $video = Video::create([
            'path_to_video' => $request->input('path_to_video'),
            'title' => $request->input('title'),
            'source' => $request->input('source'),
            'publication_date' => $request->input('publication_date'),
            'user_id' => $user->id,
            'status_id' => $status->id,
        ]);

        return response()->json([
            'success' => true,
            'video' => $video,
            'message' => 'Create successful'
        ], 201);
    }
}
