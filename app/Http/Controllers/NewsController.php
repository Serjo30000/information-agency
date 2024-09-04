<?php

namespace App\Http\Controllers;

use App\Http\Services\Filters\FilterRegion;
use App\Http\Services\StatusManagement\StatusMatcher;
use App\Models\News;
use App\Models\RegionsAndPeoples;
use App\Models\Status;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class NewsController extends Controller
{
    public function allNews()
    {
        $news = News::whereHas('status', function ($query) {
            $query->where('status', 'Опубликовано');
        })->with(['status', 'regionsAndPeoples'])
        ->get();

        return response()->json($news, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function listNewsTopTenByRegion($id_region)
    {
        $region = FilterRegion::findRegionByFilter($id_region);

        if (!$region) {
            return response()->json(['message' => 'Region not found'], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $news = $region->getNews()
            ->whereHas('status', function ($query) {
                $query->where('status', 'Опубликовано');
            })->with(['status', 'regionsAndPeoples'])
            ->take(10)
            ->get();

        return response()->json($news, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function allNewsPaginate(Request $request)
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

        $news = News::whereHas('status', function ($query) {
            $query->where('status', 'Опубликовано');
        })->with(['status', 'regionsAndPeoples'])->get();

        if ($startDate) {
            $startDate = Carbon::parse($startDate)->startOfDay();
            $news = $news->filter(function($newsOne) use ($startDate) {
                return Carbon::parse($newsOne->publication_date)->greaterThanOrEqualTo($startDate);
            });
        }

        if ($endDate) {
            $endDate = Carbon::parse($endDate)->endOfDay();
            $news = $news->filter(function($newsOne) use ($endDate) {
                return Carbon::parse($newsOne->publication_date)->lessThanOrEqualTo($endDate);
            });
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $news->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginatedDTO = new LengthAwarePaginator(
            $currentItems,
            $news->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return response()->json($paginatedDTO, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function allNewsPaginateAndSearch(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $searchTerm = $request->input('searchContent');
        $selectedRegions = $request->input('selected_regions', []);

        if ($perPage<=0){
            return response()->json([
                'success' => false,
                'message' => 'Paginate not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $regionIds = RegionsAndPeoples::where('type', 'Region')->pluck('id')->toArray();

        $news = News::whereIn('regions_and_peoples_id', $regionIds)
            ->whereHas('status', function ($query) {
                $query->where('status', 'опубликовано');
            })->with(['status', 'regionsAndPeoples'])->get();

        if (!empty($selectedRegions)) {
            $news = $news->filter(function($newsOne) use ($selectedRegions) {
                return in_array($newsOne->regions_and_peoples_id, $selectedRegions);
            });
        }

        if ($startDate) {
            $startDate = Carbon::parse($startDate)->startOfDay();
            $news = $news->filter(function($newsOne) use ($startDate) {
                return Carbon::parse($newsOne->publication_date)->greaterThanOrEqualTo($startDate);
            });
        }

        if ($endDate) {
            $endDate = Carbon::parse($endDate)->endOfDay();
            $news = $news->filter(function($newsOne) use ($endDate) {
                return Carbon::parse($newsOne->publication_date)->lessThanOrEqualTo($endDate);
            });
        }

        if ($searchTerm) {
            $searchTerm = mb_strtolower($searchTerm, 'UTF-8');
            $news = $news->filter(function($newsOne) use ($searchTerm) {
                return stripos(mb_strtolower($newsOne->content, 'UTF-8'), $searchTerm) !== false;
            });
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $news->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginatedDTO = new LengthAwarePaginator(
            $currentItems,
            $news->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return response()->json($paginatedDTO, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function findNewsOne($id){
        $newsOne = News::where('id', $id)
            ->whereHas('status', function ($query) {
                $query->where('status', 'Опубликовано');
            })->with(['status', 'regionsAndPeoples'])
            ->first();

        if ($newsOne){
            return response()->json($newsOne, 200, [], JSON_UNESCAPED_UNICODE);
        }
        else{
            return response()->json(['message' => 'NewsOne not found'], 404, [], JSON_UNESCAPED_UNICODE);
        }
    }




    public function allNewsForPanel()
    {
        $news = News::with(['status', 'regionsAndPeoples'])->get();

        return response()->json($news, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function allNewsBySearchAndFiltersAndStatusesAndSortForPanel(Request $request)
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

        if ($sortField === 'fio_or_name_region') {
            $news = News::with(['status', 'regionsAndPeoples'])
                ->where('news.user_id', $user->id)
                ->when($search, function ($query, $search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('news.title', 'like', "%{$search}%")
                            ->orWhere('news.source', 'like', "%{$search}%")
                            ->orWhere('news.sys_Comment', 'like', "%{$search}%")
                            ->orWhereHas('regionsAndPeoples', function ($query) use ($search) {
                                $query->where('regions_and_peoples.fio_or_name_region', 'like', "%{$search}%");
                            });
                    });
                })
                ->when($startDate, function ($query, $startDate) {
                    $query->whereDate('news.publication_date', '>=', $startDate);
                })
                ->when($endDate, function ($query, $endDate) {
                    $query->whereDate('news.publication_date', '<=', $endDate);
                })
                ->when(!empty($selectedStatuses), function ($query) use ($selectedStatuses) {
                    $query->whereHas('status', function ($query) use ($selectedStatuses) {
                        $query->whereIn('statuses.status', $selectedStatuses);
                    });
                })
                ->leftJoin('regions_and_peoples', 'news.regions_and_peoples_id', '=', 'regions_and_peoples.id')
                ->groupBy('news.id', 'regions_and_peoples.id')
                ->orderBy('regions_and_peoples.' . $sortField, $sortDirection)
                ->select('news.*')
                ->get();
        } else {
            $news = News::with(['status', 'regionsAndPeoples'])
                ->where('user_id', $user->id)
                ->when($search, function ($query, $search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('title', 'like', "%{$search}%")
                            ->orWhere('source', 'like', "%{$search}%")
                            ->orWhere('sys_Comment', 'like', "%{$search}%")
                            ->orWhereHas('regionsAndPeoples', function ($query) use ($search) {
                                $query->where('fio_or_name_region', 'like', "%{$search}%");
                            });
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
        }

        return response()->json($news, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function findNewsOneForPanel($id){
        $newsOne = News::with(['status', 'regionsAndPeoples'])->find($id);

        if ($newsOne){
            return response()->json($newsOne, 200, [], JSON_UNESCAPED_UNICODE);
        }
        else{
            return response()->json(['message' => 'NewsOne not found'], 404, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function createNews(Request $request){
        $rules = [
            'path_to_image_or_video' => 'required|string',
            'title' => 'required|string',
            'content' => 'required|string',
            'source' => 'nullable|string',
            'sys_Comment' => 'nullable|string',
            'publication_date' => [
                'required',
                'string',
                'date_format:"Y-m-d H:i:s"',
            ],
            'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
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

        $regionsAndPeoples = RegionsAndPeoples::find($request->input('regions_and_peoples_id'));

        if (!$regionsAndPeoples) {
            return response()->json([
                'success' => false,
                'message' => 'Region and People record not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        if ($regionsAndPeoples->type !== 'Region') {
            return response()->json([
                'success' => false,
                'message' => 'Invalid type. Expected "Region".'
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

        $news = News::create([
            'path_to_image_or_video' => $request->input('path_to_image_or_video'),
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'source' => $request->input('source'),
            'publication_date' => $request->input('publication_date'),
            'sys_Comment' => $request->input('sys_Comment'),
            'user_id' => $user->id,
            'regions_and_peoples_id' => $request->input('regions_and_peoples_id'),
            'status_id' => $status->id,
        ]);

        $newsWithStatusAndRegionsAndPeoples = News::with(['status', 'regionsAndPeoples'])->find($news->id);

        return response()->json([
            'success' => true,
            'news' => $newsWithStatusAndRegionsAndPeoples,
            'message' => 'Create successful'
        ], 201, [], JSON_UNESCAPED_UNICODE);
    }

    public function createNewsForCheck(Request $request){
        $rules = [
            'path_to_image_or_video' => 'required|string',
            'title' => 'required|string',
            'content' => 'required|string',
            'source' => 'nullable|string',
            'sys_Comment' => 'nullable|string',
            'publication_date' => [
                'required',
                'string',
                'date_format:"Y-m-d H:i:s"',
            ],
            'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
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

        $regionsAndPeoples = RegionsAndPeoples::find($request->input('regions_and_peoples_id'));

        if (!$regionsAndPeoples) {
            return response()->json([
                'success' => false,
                'message' => 'Region and People record not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        if ($regionsAndPeoples->type !== 'Region') {
            return response()->json([
                'success' => false,
                'message' => 'Invalid type. Expected "Region".'
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

        $news = News::create([
            'path_to_image_or_video' => $request->input('path_to_image_or_video'),
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'source' => $request->input('source'),
            'publication_date' => $request->input('publication_date'),
            'sys_Comment' => $request->input('sys_Comment'),
            'user_id' => $user->id,
            'regions_and_peoples_id' => $request->input('regions_and_peoples_id'),
            'status_id' => $status->id,
        ]);

        $newsWithStatusAndRegionsAndPeoples = News::with(['status', 'regionsAndPeoples'])->find($news->id);

        return response()->json([
            'success' => true,
            'news' => $newsWithStatusAndRegionsAndPeoples,
            'message' => 'Create successful'
        ], 201, [], JSON_UNESCAPED_UNICODE);
    }

    public function deleteNewsMark(Request $request){
        $rules = [
            'news_ids' => 'required|array',
            'news_ids.*' => 'integer|exists:news,id',
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

        $newsItems = News::whereIn('id', $request->input('news_ids'))->where('user_id', $user->id)->get();

        foreach ($newsItems as $item) {
            $item->delete_mark = !$item->delete_mark;
            $item->save();
        }

        $newsItems = News::with(['status', 'regionsAndPeoples'])
            ->whereIn('id', $request->input('news_ids'))->where('user_id', $user->id)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'News delete_mark updated successfully',
            'news' => $newsItems,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function deleteNews($id){
        $news = News::find($id);

        if (!$news) {
            return response()->json([
                'success' => false,
                'message' => 'News not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        if (!$news->delete_mark){
            return response()->json([
                'success' => false,
                'message' => 'Cannot be deleted'
            ], 403, [], JSON_UNESCAPED_UNICODE);
        }

        $news->delete();

        return response()->json([
            'success' => true,
            'message' => 'News deleted successfully'
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function editNews($id, Request $request){
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401, [], JSON_UNESCAPED_UNICODE);
        }

        $news = News::find($id);

        if (!$news) {
            return response()->json([
                'success' => false,
                'message' => 'News not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $status = Status::find($news->status_id);

        if (!$status) {
            return response()->json([
                'success' => false,
                'message' => 'Status not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        if ($user->id == $news->user_id && $status->status == "Редактируется"){
            $rules = [
                'path_to_image_or_video' => 'required|string',
                'title' => 'required|string',
                'content' => 'required|string',
                'source' => 'nullable|string',
                'sys_Comment' => 'nullable|string',
                'publication_date' => [
                    'required',
                    'string',
                    'date_format:"Y-m-d H:i:s"',
                ],
                'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
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

            $regionsAndPeoples = RegionsAndPeoples::find($request->input('regions_and_peoples_id'));

            if (!$regionsAndPeoples) {
                return response()->json([
                    'success' => false,
                    'message' => 'Region and People record not found'
                ], 404, [], JSON_UNESCAPED_UNICODE);
            }

            if ($regionsAndPeoples->type !== 'Region') {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid type. Expected "Region".'
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

            $news->path_to_image_or_video = $request->input('path_to_image_or_video');
            $news->title = $request->input('title');
            $news->content = $request->input('content');
            $news->source = $request->input('source');
            $news->publication_date = $request->input('publication_date');
            $news->sys_Comment = $request->input('sys_Comment');
            $news->regions_and_peoples_id = $request->input('regions_and_peoples_id');
            $news->user_id = $user->id;

            $news->save();

            $newsWithStatusAndRegionsAndPeoples = News::with(['status', 'regionsAndPeoples'])->find($news->id);

            return response()->json([
                'success' => true,
                'message' => 'News updated successfully',
                'news' => $newsWithStatusAndRegionsAndPeoples,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Cannot edit news'
            ], 403, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function editNewsForCheck($id, Request $request){
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401, [], JSON_UNESCAPED_UNICODE);
        }

        $news = News::find($id);

        if (!$news) {
            return response()->json([
                'success' => false,
                'message' => 'News not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $status = Status::find($news->status_id);

        if (!$status) {
            return response()->json([
                'success' => false,
                'message' => 'Status not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        if ($user->id == $news->user_id && $status->status == "Редактируется"){
            $rules = [
                'path_to_image_or_video' => 'required|string',
                'title' => 'required|string',
                'content' => 'required|string',
                'source' => 'nullable|string',
                'sys_Comment' => 'nullable|string',
                'publication_date' => [
                    'required',
                    'string',
                    'date_format:"Y-m-d H:i:s"',
                ],
                'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
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

            $regionsAndPeoples = RegionsAndPeoples::find($request->input('regions_and_peoples_id'));

            if (!$regionsAndPeoples) {
                return response()->json([
                    'success' => false,
                    'message' => 'Region and People record not found'
                ], 404, [], JSON_UNESCAPED_UNICODE);
            }

            if ($regionsAndPeoples->type !== 'Region') {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid type. Expected "Region".'
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

            $news->path_to_image_or_video = $request->input('path_to_image_or_video');
            $news->title = $request->input('title');
            $news->content = $request->input('content');
            $news->source = $request->input('source');
            $news->publication_date = $request->input('publication_date');
            $news->sys_Comment = $request->input('sys_Comment');
            $news->regions_and_peoples_id = $request->input('regions_and_peoples_id');
            $news->user_id = $user->id;
            $news->status_id = $status->id;

            $news->save();

            $newsWithStatusAndRegionsAndPeoples = News::with(['status', 'regionsAndPeoples'])->find($news->id);

            return response()->json([
                'success' => true,
                'message' => 'News updated successfully',
                'news' => $newsWithStatusAndRegionsAndPeoples,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Cannot edit news'
            ], 403, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function editNewsByStatus($id, Request $request){
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401, [], JSON_UNESCAPED_UNICODE);
        }

        $news = News::find($id);

        if (!$news) {
            return response()->json([
                'success' => false,
                'message' => 'News not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $oldStatus = Status::find($news->status_id);

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

        if ($user->hasRole('editor') && !$user->hasRole('admin') && !$user->hasRole('super_admin') && $user->id == $news->user_id){
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
            $news->status_id = $status->id;

            $news->save();

            $newsWithStatusAndRegionsAndPeoples = News::with(['status', 'regionsAndPeoples'])->find($news->id);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'news' => $newsWithStatusAndRegionsAndPeoples,
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
