<?php

namespace App\Http\Controllers;

use App\Http\Services\StatusManagement\StatusMatcher;
use App\Models\News;
use App\Models\Status;
use App\Models\User;
use App\Models\Video;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class VideoController extends Controller
{
    public function allVideos()
    {
        $videos = Video::whereHas('status', function ($query) {
            $query->where('status', 'Опубликовано');
        })->with('status')->get();

        return response()->json($videos, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function listVideosTopFour()
    {
        $videos = Video::whereHas('status', function ($query) {
            $query->where('status', 'Опубликовано');
        })->with('status')
            ->take(4)
            ->get();

        return response()->json($videos, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function listVideosTopTen()
    {
        $videos = Video::whereHas('status', function ($query) {
            $query->where('status', 'Опубликовано');
        })->with('status')
            ->take(10)
            ->get();

        return response()->json($videos, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function allVideosPaginate(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($perPage<=0){
            return response()->json([
                'success' => false,
                'message' => 'Paginate not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $videos = Video::whereHas('status', function ($query) {
            $query->where('status', 'Опубликовано');
        })->with('status')->get();

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
            })->with('status')
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
        $videos = Video::with('status')->get();

        return response()->json($videos, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function allVideosPaginateForPanel(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($perPage<=0){
            return response()->json([
                'success' => false,
                'message' => 'Paginate not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $videos = Video::with('status')->get();

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

    public function allVideosBySearchAndFiltersAndStatusesAndSortForPanel(Request $request)
    {
        $search = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $selectedStatuses = $request->input('selected_statuses', []);
        $sortField = $request->input('sort_field', 'publication_date');
        $sortDirection = $request->input('sort_direction', 'desc');

        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401, [], JSON_UNESCAPED_UNICODE);
        }

        $videos = Video::with('status')
            ->where('user_id', $user->id)
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('source', 'like', "%{$search}%")
                        ->orWhere('sys_Comment', 'like', "%{$search}%");
                });
            })
            ->when($startDate, function ($query, $startDate) {
                $query->whereDate('publication_date', '>=', $startDate);
            })
            ->when($endDate, function ($query, $endDate) {
                $query->whereDate('publication_date', '<=', $endDate);
            })
            ->when(!empty($selectedStatuses), function ($query) use ($selectedStatuses) {
                $query->whereHas('status', function ($query) use ($selectedStatuses) {
                    $query->whereIn('status', $selectedStatuses);
                });
            })
            ->orderBy($sortField, $sortDirection)
            ->get();

        return response()->json($videos, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function findVideoForPanel($id){
        $video = Video::with('status')->find($id);

        if ($video){
            return response()->json($video, 200, [], JSON_UNESCAPED_UNICODE);
        }
        else{
            return response()->json(['message' => 'Video not found'], 404, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function createVideo(Request $request){
        $rules = [
            'path_to_video' => 'required|string',
            'title' => 'required|string',
            'source' => 'required|string',
            'publication_date' => [
                'required',
                'string',
                'date_format:"Y-m-d H:i:s"',
            ],
            'sys_Comment' => 'nullable|string',
            'user_id' => 'nullable|integer|exists:users,id',
        ];

        try {
            $request->validate($rules);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed'
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }

        $user = null;

        if ($request->input('user_id') == null || $request->input('user_id') == 0){
            $user = Auth::guard('sanctum')->user();
        }
        else{
            $user = User::where('id', $request->input('user_id'))->first();
        }

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401, [], JSON_UNESCAPED_UNICODE);
        }

        $status = Status::where('status', 'Редактируется')->first();

        if (!$status) {
            return response()->json([
                'success' => false,
                'message' => 'Status not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $video = Video::create([
            'path_to_video' => $request->input('path_to_video'),
            'title' => $request->input('title'),
            'source' => $request->input('source'),
            'publication_date' => $request->input('publication_date'),
            'sys_Comment' => $request->input('sys_Comment'),
            'user_id' => $user->id,
            'status_id' => $status->id,
        ]);

        $videoWithStatus = Video::with('status')->find($video->id);

        return response()->json([
            'success' => true,
            'video' => $videoWithStatus,
            'message' => 'Create successful'
        ], 201, [], JSON_UNESCAPED_UNICODE);
    }

    public function createVideoForCheck(Request $request){
        $rules = [
            'path_to_video' => 'required|string',
            'title' => 'required|string',
            'source' => 'required|string',
            'publication_date' => [
                'required',
                'string',
                'date_format:"Y-m-d H:i:s"',
            ],
            'sys_Comment' => 'nullable|string',
            'user_id' => 'nullable|integer|exists:users,id',
        ];

        try {
            $request->validate($rules);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed'
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }

        $user = null;

        if ($request->input('user_id') == null || $request->input('user_id') == 0){
            $user = Auth::guard('sanctum')->user();
        }
        else{
            $user = User::where('id', $request->input('user_id'))->first();
        }

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401, [], JSON_UNESCAPED_UNICODE);
        }

        $status = Status::where('status', 'Ожидает подтверждения')->first();

        if (!$status) {
            return response()->json([
                'success' => false,
                'message' => 'Status not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $video = Video::create([
            'path_to_video' => $request->input('path_to_video'),
            'title' => $request->input('title'),
            'source' => $request->input('source'),
            'publication_date' => $request->input('publication_date'),
            'sys_Comment' => $request->input('sys_Comment'),
            'user_id' => $user->id,
            'status_id' => $status->id,
        ]);

        $videoWithStatus = Video::with('status')->find($video->id);

        return response()->json([
            'success' => true,
            'video' => $videoWithStatus,
            'message' => 'Create successful'
        ], 201, [], JSON_UNESCAPED_UNICODE);
    }

    public function deleteVideoMark(Request $request){
        $rules = [
            'video_ids' => 'required|array',
            'video_ids.*' => 'integer|exists:videos,id',
        ];

        try {
            $request->validate($rules);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed'
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }

        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401, [], JSON_UNESCAPED_UNICODE);
        }

        $videosItems = Video::whereIn('id', $request->input('video_ids'))->where('user_id', $user->id)->get();

        foreach ($videosItems as $item) {
            $item->delete_mark = !$item->delete_mark;
            $item->save();
        }

        $videosItems = Video::with('status')
            ->whereIn('id', $request->input('video_ids'))->where('user_id', $user->id)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Video delete_mark updated successfully',
            'video' => $videosItems,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function deleteVideo($id){
        $video = Video::find($id);

        if (!$video) {
            return response()->json([
                'success' => false,
                'message' => 'Video not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        if (!$video->delete_mark){
            return response()->json([
                'success' => false,
                'message' => 'Cannot be deleted'
            ], 403, [], JSON_UNESCAPED_UNICODE);
        }

        $video->delete();

        return response()->json([
            'success' => true,
            'message' => 'Video deleted successfully'
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function editVideo($id, Request $request){
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401, [], JSON_UNESCAPED_UNICODE);
        }

        $video = Video::find($id);

        if (!$video) {
            return response()->json([
                'success' => false,
                'message' => 'Video not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $status = Status::find($video->status_id);

        if (!$status) {
            return response()->json([
                'success' => false,
                'message' => 'Status not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        if ($user->id == $video->user_id && $status->status == "Редактируется"){
            $rules = [
                'path_to_video' => 'required|string',
                'title' => 'required|string',
                'source' => 'required|string',
                'publication_date' => [
                    'required',
                    'string',
                    'date_format:"Y-m-d H:i:s"',
                ],
                'sys_Comment' => 'nullable|string',
                'user_id' => 'nullable|integer|exists:users,id',
            ];

            try {
                $request->validate($rules);
            } catch (ValidationException $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed'
                ], 422, [], JSON_UNESCAPED_UNICODE);
            }

            if ($request->input('user_id') != null && $request->input('user_id') != 0){
                $user = User::where('id', $request->input('user_id'))->first();
            }

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not authenticated'
                ], 401, [], JSON_UNESCAPED_UNICODE);
            }

            $video->path_to_video = $request->input('path_to_video');
            $video->title = $request->input('title');
            $video->source = $request->input('source');
            $video->publication_date = $request->input('publication_date');
            $video->sys_Comment = $request->input('sys_Comment');
            $video->user_id = $user->id;

            $video->save();

            $videoWithStatus = Video::with('status')->find($video->id);

            return response()->json([
                'success' => true,
                'message' => 'Video updated successfully',
                'video' => $videoWithStatus,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Cannot edit video'
            ], 403, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function editVideoForCheck($id, Request $request){
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401, [], JSON_UNESCAPED_UNICODE);
        }

        $video = Video::find($id);

        if (!$video) {
            return response()->json([
                'success' => false,
                'message' => 'Video not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $status = Status::find($video->status_id);

        if (!$status) {
            return response()->json([
                'success' => false,
                'message' => 'Status not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        if ($user->id == $video->user_id && $status->status == "Редактируется"){
            $rules = [
                'path_to_video' => 'required|string',
                'title' => 'required|string',
                'source' => 'required|string',
                'publication_date' => [
                    'required',
                    'string',
                    'date_format:"Y-m-d H:i:s"',
                ],
                'sys_Comment' => 'nullable|string',
                'user_id' => 'nullable|integer|exists:users,id',
            ];

            try {
                $request->validate($rules);
            } catch (ValidationException $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed'
                ], 422, [], JSON_UNESCAPED_UNICODE);
            }

            if ($request->input('user_id') != null && $request->input('user_id') != 0){
                $user = User::where('id', $request->input('user_id'))->first();
            }

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not authenticated'
                ], 401, [], JSON_UNESCAPED_UNICODE);
            }

            $status = Status::where('status', 'Ожидает подтверждения')->first();

            if (!$status) {
                return response()->json([
                    'success' => false,
                    'message' => 'Status not found'
                ], 404, [], JSON_UNESCAPED_UNICODE);
            }

            $video->path_to_video = $request->input('path_to_video');
            $video->title = $request->input('title');
            $video->source = $request->input('source');
            $video->publication_date = $request->input('publication_date');
            $video->sys_Comment = $request->input('sys_Comment');
            $video->user_id = $user->id;
            $video->status_id = $status->id;

            $video->save();

            $videoWithStatus = Video::with('status')->find($video->id);

            return response()->json([
                'success' => true,
                'message' => 'Video updated successfully',
                'video' => $videoWithStatus,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Cannot edit video'
            ], 403, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function editVideoByStatus($id, Request $request){
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401, [], JSON_UNESCAPED_UNICODE);
        }

        $video = Video::find($id);

        if (!$video) {
            return response()->json([
                'success' => false,
                'message' => 'Video not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $oldStatus = Status::find($video->status_id);

        if (!$oldStatus) {
            return response()->json([
                'success' => false,
                'message' => 'OldStatus not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $rules = [
            'status' => 'required|string|exists:statuses,status',
        ];

        try {
            $request->validate($rules);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed'
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }

        $status = Status::where('status', $request->input('status'))->first();

        if (!$status) {
            return response()->json([
                'success' => false,
                'message' => 'Status not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $is_status = false;

        if ($user->hasRole('editor') && !$user->hasRole('admin') && !$user->hasRole('super_admin') && $user->id == $video->user_id){
            if ($status->order === 0){
                $is_status = StatusMatcher::isMatchingStatus($status, $oldStatus, [
                    'Редактируется' => ['Снято с публикации', 'Ожидает подтверждения'],
                    'Ожидает подтверждения' => ['Редактируется'],
                    'Снято с публикации' => ['Опубликовано'],
                ]);
            }
        }
        else if ($user->hasRole('admin') || $user->hasRole('super_admin')){
            if ($status->order === 0 || $status->order === 1){
                $is_status = StatusMatcher::isMatchingStatus($status, $oldStatus, [
                    'Редактируется' => ['Снято с публикации', 'Ожидает подтверждения', 'Заблокировано'],
                    'Ожидает подтверждения' => ['Редактируется'],
                    'Снято с публикации' => ['Опубликовано'],
                    'Опубликовано' => ['Ожидает подтверждения'],
                    'Ожидает публикации' => ['Ожидает подтверждения'],
                    'Заблокировано' => ['Редактируется', 'Ожидает подтверждения', 'Снято с публикации', 'Опубликовано', 'Ожидает публикации'],
                ]);
            }
        }

        if ($is_status){
            $video->status_id = $status->id;

            $video->save();

            $videoWithStatus = Video::with('status')->find($video->id);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'video' => $videoWithStatus,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Cannot edit status'
            ], 403, [], JSON_UNESCAPED_UNICODE);
        }
    }
}
