<?php

namespace App\Http\Controllers;

use App\Models\GrandNews;
use App\Models\News;
use App\Models\RegionsAndPeoples;
use App\Models\Status;
use App\Models\User;
use App\Rules\DateRange;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class GrandNewsController extends Controller
{
    public function allGrandNews()
    {
        $grandNews = GrandNews::where('isActivate', true)
            ->whereHas('news', function ($query) {
            $query->whereHas('status', function ($statusQuery) {
                $statusQuery->where('status', 'Опубликовано');
            });
        })->with('news')->get();

        return response()->json($grandNews, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function listGrandNewsTopFive()
    {
        $grandNews = GrandNews::where('isActivate', true)
            ->whereHas('news', function ($query) {
            $query->whereHas('status', function ($statusQuery) {
                $statusQuery->where('status', 'Опубликовано');
            });
        })->with('news')->take(5)->get();

        return response()->json($grandNews, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function listGrandNewsTopSix()
    {
        $grandNews = GrandNews::where('isActivate', true)
            ->whereHas('news', function ($query) {
                $query->whereHas('status', function ($statusQuery) {
                    $statusQuery->where('status', 'Опубликовано');
                });
            })->with('news')->orderBy('priority', 'asc')->take(6)->get();

        return response()->json($grandNews, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function listGrandNewsTopTwenty()
    {
        $grandNews = GrandNews::where('isActivate', true)
            ->whereHas('news', function ($query) {
            $query->whereHas('status', function ($statusQuery) {
                $statusQuery->where('status', 'Опубликовано');
            });
        })->with('news')->take(20)->get();

        return response()->json($grandNews, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function allGrandNewsPaginate(Request $request)
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

        $grandNews = GrandNews::where('isActivate', true)
            ->whereHas('news', function ($query) {
            $query->whereHas('status', function ($statusQuery) {
                $statusQuery->where('status', 'Опубликовано');
            });
        })->with('news')->get();

        if ($startDate) {
            $startDate = Carbon::parse($startDate)->startOfDay();
            $grandNews = $grandNews->filter(function($grandNewsOne) use ($startDate) {
                return Carbon::parse($grandNewsOne->start_publication_date)->greaterThanOrEqualTo($startDate);
            });
        }

        if ($endDate) {
            $endDate = Carbon::parse($endDate)->endOfDay();
            $grandNews = $grandNews->filter(function($grandNewsOne) use ($endDate) {
                return Carbon::parse($grandNewsOne->end_publication_date)->lessThanOrEqualTo($endDate);
            });
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $grandNews->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginatedDTO = new LengthAwarePaginator(
            $currentItems,
            $grandNews->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return response()->json($paginatedDTO, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function findGrandNewsOne($id){
        $grandNewsOne = GrandNews::where('isActivate', true)
            ->where('id', $id)
            ->whereHas('news', function ($query) {
            $query->whereHas('status', function ($statusQuery) {
                $statusQuery->where('status', 'Опубликовано');
            });
        })->with('news')->first();

        if ($grandNewsOne){
            return response()->json($grandNewsOne, 200, [], JSON_UNESCAPED_UNICODE);
        }
        else{
            return response()->json(['message' => 'GrandNewsOne not found'], 404, [], JSON_UNESCAPED_UNICODE);
        }
    }





    public function allGrandNewsForPanel()
    {
        $grandNews = GrandNews::with('news')->get();

        return response()->json($grandNews, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function allGrandNewsPaginateForPanel(Request $request)
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

        $grandNews = GrandNews::with('news')->get();

        if ($startDate) {
            $startDate = Carbon::parse($startDate)->startOfDay();
            $grandNews = $grandNews->filter(function($grandNewsOne) use ($startDate) {
                return Carbon::parse($grandNewsOne->start_publication_date)->greaterThanOrEqualTo($startDate);
            });
        }

        if ($endDate) {
            $endDate = Carbon::parse($endDate)->endOfDay();
            $grandNews = $grandNews->filter(function($grandNewsOne) use ($endDate) {
                return Carbon::parse($grandNewsOne->end_publication_date)->lessThanOrEqualTo($endDate);
            });
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $grandNews->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginatedDTO = new LengthAwarePaginator(
            $currentItems,
            $grandNews->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return response()->json($paginatedDTO, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function allGrandNewsBySearchAndFiltersAndStatusesAndSortAndIsActiveForPanel(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $selectedStatuses = $request->input('selected_statuses', []);
        $sortField = $request->input('sort_field', 'publication_date');
        $sortDirection = $request->input('sort_direction', 'desc');

        if ($perPage<=0){
            return response()->json([
                'success' => false,
                'message' => 'Paginate not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401, [], JSON_UNESCAPED_UNICODE);
        }

        $grandNews = GrandNews::with(['news.regionsAndPeoples', 'news.status'])
            ->where('grand_news.isActivate', 1)
            ->whereHas('news', function ($query) use ($user, $search, $startDate, $endDate, $selectedStatuses) {
                $query->where('news.user_id', $user->id);
                if (!empty($selectedStatuses)) {
                    $query->whereHas('status', function ($query) use ($selectedStatuses) {
                        $query->whereIn('statuses.status', $selectedStatuses);
                    });
                }
                $query->when($search, function ($query, $search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('news.title', 'like', "%{$search}%")
                            ->orWhere('news.source', 'like', "%{$search}%")
                            ->orWhere('grand_news.sys_Comment', 'like', "%{$search}%")
                            ->orWhereHas('regionsAndPeoples', function ($query) use ($search) {
                                $query->where('regions_and_peoples.fio_or_name_region', 'like', "%{$search}%");
                            });
                    });
                });
                $query->when($startDate, function ($query, $startDate) {
                    $query->whereDate('news.publication_date', '>=', $startDate);
                });

                $query->when($endDate, function ($query, $endDate) {
                    $query->whereDate('news.publication_date', '<=', $endDate);
                });
            })
            ->leftJoin('news', 'grand_news.news_id', '=', 'news.id')
            ->leftJoin('regions_and_peoples', 'news.regions_and_peoples_id', '=', 'regions_and_peoples.id')
            ->groupBy('grand_news.id', 'news.id', 'regions_and_peoples.id')
            ->orderBy($sortField, $sortDirection)
            ->select('grand_news.*')
            ->get();

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $grandNews->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginatedDTO = new LengthAwarePaginator(
            $currentItems,
            $grandNews->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return response()->json($paginatedDTO, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function allGrandNewsBySearchAndFiltersAndStatusesAndSortAndIsNotActiveForPanel(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $currentDate = $request->input('current_date');
        $selectedStatuses = $request->input('selected_statuses', []);
        $sortField = $request->input('sort_field', 'publication_date');
        $sortDirection = $request->input('sort_direction', 'desc');

        if ($perPage<=0){
            return response()->json([
                'success' => false,
                'message' => 'Paginate not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401, [], JSON_UNESCAPED_UNICODE);
        }

        $grandNews = GrandNews::with(['news.regionsAndPeoples', 'news.status'])
            ->where('grand_news.isActivate', 0)
            ->whereHas('news', function ($query) use ($user, $search, $startDate, $endDate, $currentDate, $selectedStatuses) {
                $query->where('news.user_id', $user->id);
                if (!empty($selectedStatuses)) {
                    $query->whereHas('status', function ($query) use ($selectedStatuses) {
                        $query->whereIn('statuses.status', $selectedStatuses);
                    });
                }
                $query->when($search, function ($query, $search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('news.title', 'like', "%{$search}%")
                            ->orWhere('news.source', 'like', "%{$search}%")
                            ->orWhere('grand_news.sys_Comment', 'like', "%{$search}%")
                            ->orWhereHas('regionsAndPeoples', function ($query) use ($search) {
                                $query->where('regions_and_peoples.fio_or_name_region', 'like', "%{$search}%");
                            });
                    });
                });
                $query->when($currentDate, function ($query, $currentDate) {
                    $year = date('Y', strtotime($currentDate));
                    $month = date('m', strtotime($currentDate));
                    $day = date('d', strtotime($currentDate));

                    $query->whereYear('news.publication_date', $year)
                        ->whereMonth('news.publication_date', $month)
                        ->whereDay('news.publication_date', $day);
                });
                $query->when($startDate, function ($query, $startDate) {
                    $query->whereDate('news.publication_date', '>=', $startDate);
                });

                $query->when($endDate, function ($query, $endDate) {
                    $query->whereDate('news.publication_date', '<=', $endDate);
                });
            })
            ->leftJoin('news', 'grand_news.news_id', '=', 'news.id')
            ->leftJoin('regions_and_peoples', 'news.regions_and_peoples_id', '=', 'regions_and_peoples.id')
            ->groupBy('grand_news.id', 'news.id', 'regions_and_peoples.id')
            ->orderBy($sortField, $sortDirection)
            ->select('grand_news.*')
            ->get();

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $grandNews->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginatedDTO = new LengthAwarePaginator(
            $currentItems,
            $grandNews->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return response()->json($paginatedDTO, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function findGrandNewsOneForPanel($id){
        $grandNewsOne = GrandNews::with('news')->find($id);

        if ($grandNewsOne){
            return response()->json($grandNewsOne, 200, [], JSON_UNESCAPED_UNICODE);
        }
        else{
            return response()->json(['message' => 'GrandNewsOne not found'], 404, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function createGrandNews(Request $request){
        $rules = [
            'start_publication_date' => [
                'required',
                'string',
                'date_format:"Y-m-d H:i:s"',
            ],
            'end_publication_date' => [
                'required',
                'string',
                'date_format:"Y-m-d H:i:s"',
            ],
            'priority' => 'required|integer|min:0',
            'sys_Comment' => 'nullable|string',
            'isActivate' => 'required|boolean',
            'news_id' => 'required|integer|exists:news,id',
        ];

        $startDate = $request->input('start_publication_date');
        $endDate = $request->input('end_publication_date');

        $dateRangeRule = new DateRange($startDate, $endDate);

        try {
            $request->validate($rules);

            if (!$dateRangeRule->passes('date_range', null)) {
                return response()->json([
                    'success' => false,
                    'message' => $dateRangeRule->message()
                ], 422, [], JSON_UNESCAPED_UNICODE);
            }
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed'
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }

        $news = News::find($request->input('news_id'));
        if (!$news) {
            return response()->json([
                'success' => false,
                'message' => 'News not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $status = Status::find($news->status_id);

        if (!$status || !in_array($status->status, ['Редактируется'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid status for the news'
            ], 400, [], JSON_UNESCAPED_UNICODE);
        }

        $grandNews = GrandNews::create([
            'start_publication_date' => $request->input('start_publication_date'),
            'end_publication_date' => $request->input('end_publication_date'),
            'priority' => $request->input('priority'),
            'sys_Comment' => $request->input('sys_Comment'),
            'isActivate' => $request->input('isActivate'),
            'news_id' => $request->input('news_id'),
        ]);

        $grandNewsWithNews = GrandNews::with('news')->find($grandNews->id);

        return response()->json([
            'success' => true,
            'grandNews' => $grandNewsWithNews,
            'message' => 'Create successful'
        ], 201, [], JSON_UNESCAPED_UNICODE);
    }

    public function createNewsWithGrandNews(Request $request){
        $rules = [
            'path_to_image_or_video' => 'required|string',
            'title' => 'required|string',
            'content' => 'required|string',
            'source' => 'nullable|string',
            'publication_date' => [
                'required',
                'string',
                'date_format:"Y-m-d H:i:s"',
            ],
            'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
            'user_id' => 'nullable|integer|exists:users,id',
            'start_publication_date' => [
                'required',
                'string',
                'date_format:"Y-m-d H:i:s"',
            ],
            'end_publication_date' => [
                'required',
                'string',
                'date_format:"Y-m-d H:i:s"',
            ],
            'priority' => 'required|integer|min:0',
            'sys_Comment' => 'nullable|string',
            'isActivate' => 'required|boolean',
        ];

        $startDate = $request->input('start_publication_date');
        $endDate = $request->input('end_publication_date');

        $dateRangeRule = new DateRange($startDate, $endDate);

        try {
            $request->validate($rules);

            if (!$dateRangeRule->passes('date_range', null)) {
                return response()->json([
                    'success' => false,
                    'message' => $dateRangeRule->message()
                ], 422, [], JSON_UNESCAPED_UNICODE);
            }
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
            'user_id' => $user->id,
            'regions_and_peoples_id' => $request->input('regions_and_peoples_id'),
            'status_id' => $status->id,
        ]);

        $newsWithStatusAndRegionsAndPeoples = News::with(['status', 'regionsAndPeoples'])->find($news->id);

        if (!$newsWithStatusAndRegionsAndPeoples) {
            return response()->json([
                'success' => false,
                'message' => 'News not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $grandNews = GrandNews::create([
            'start_publication_date' => $request->input('start_publication_date'),
            'end_publication_date' => $request->input('end_publication_date'),
            'priority' => $request->input('priority'),
            'sys_Comment' => $request->input('sys_Comment'),
            'isActivate' => $request->input('isActivate'),
            'news_id' => $newsWithStatusAndRegionsAndPeoples->id,
        ]);

        $grandNewsWithNews = GrandNews::with('news')->find($grandNews->id);

        return response()->json([
            'success' => true,
            'grandNews' => $grandNewsWithNews,
            'message' => 'Create successful'
        ], 201, [], JSON_UNESCAPED_UNICODE);
    }

    public function deleteGrandNews($id){
        $grandNews = GrandNews::find($id);

        if (!$grandNews) {
            return response()->json([
                'success' => false,
                'message' => 'GrandNews not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $grandNews->delete();

        return response()->json([
            'success' => true,
            'message' => 'GrandNews deleted successfully'
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function editGrandNews($id, Request $request){
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401, [], JSON_UNESCAPED_UNICODE);
        }

        $grandNews = GrandNews::find($id);

        if (!$grandNews) {
            return response()->json([
                'success' => false,
                'message' => 'GrandNews not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $news = News::find($grandNews->news_id);

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
                'start_publication_date' => [
                    'required',
                    'string',
                    'date_format:"Y-m-d H:i:s"',
                ],
                'end_publication_date' => [
                    'required',
                    'string',
                    'date_format:"Y-m-d H:i:s"',
                ],
                'priority' => 'required|integer|min:0',
                'sys_Comment' => 'nullable|string',
                'isActivate' => 'required|boolean',
                'news_id' => 'required|integer|exists:news,id',
            ];

            $startDate = $request->input('start_publication_date');
            $endDate = $request->input('end_publication_date');

            $dateRangeRule = new DateRange($startDate, $endDate);

            try {
                $request->validate($rules);

                if (!$dateRangeRule->passes('date_range', null)) {
                    return response()->json([
                        'success' => false,
                        'message' => $dateRangeRule->message()
                    ], 422, [], JSON_UNESCAPED_UNICODE);
                }
            } catch (ValidationException $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed'
                ], 422, [], JSON_UNESCAPED_UNICODE);
            }

            $grandNews->start_publication_date = $request->input('start_publication_date');
            $grandNews->end_publication_date = $request->input('end_publication_date');
            $grandNews->priority = $request->input('priority');
            $grandNews->sys_Comment = $request->input('sys_Comment');
            $grandNews->isActivate = $request->input('isActivate');
            $grandNews->news_id = $request->input('news_id');

            $grandNews->save();

            $grandNewsWithNews = GrandNews::with('news')->find($grandNews->id);

            return response()->json([
                'success' => true,
                'message' => 'GrandNews updated successfully',
                'grandNews' => $grandNewsWithNews,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Cannot edit grandNews'
            ], 403, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function editNewsWithGrandNews($id, Request $request){
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401, [], JSON_UNESCAPED_UNICODE);
        }

        $grandNews = GrandNews::find($id);

        if (!$grandNews) {
            return response()->json([
                'success' => false,
                'message' => 'GrandNews not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $news = News::find($grandNews->news_id);

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
                'start_publication_date' => [
                    'required',
                    'string',
                    'date_format:"Y-m-d H:i:s"',
                ],
                'end_publication_date' => [
                    'required',
                    'string',
                    'date_format:"Y-m-d H:i:s"',
                ],
                'priority' => 'required|integer|min:0',
                'sys_Comment' => 'nullable|string',
                'isActivate' => 'required|boolean',
                'news_id' => 'required|integer|exists:news,id',
                'path_to_image_or_video' => 'required|string',
                'title' => 'required|string',
                'content' => 'required|string',
                'source' => 'nullable|string',
                'publication_date' => [
                    'required',
                    'string',
                    'date_format:"Y-m-d H:i:s"',
                ],
                'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
                'user_id' => 'nullable|integer|exists:users,id',
            ];

            $startDate = $request->input('start_publication_date');
            $endDate = $request->input('end_publication_date');

            $dateRangeRule = new DateRange($startDate, $endDate);

            try {
                $request->validate($rules);

                if (!$dateRangeRule->passes('date_range', null)) {
                    return response()->json([
                        'success' => false,
                        'message' => $dateRangeRule->message()
                    ], 422, [], JSON_UNESCAPED_UNICODE);
                }
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
            $news->regions_and_peoples_id = $request->input('regions_and_peoples_id');
            $news->user_id = $user->id;

            $news->save();

            $grandNews->start_publication_date = $request->input('start_publication_date');
            $grandNews->end_publication_date = $request->input('end_publication_date');
            $grandNews->priority = $request->input('priority');
            $grandNews->sys_Comment = $request->input('sys_Comment');
            $grandNews->isActivate = $request->input('isActivate');
            $grandNews->news_id = $request->input('news_id');

            $grandNews->save();

            $grandNewsWithNews = GrandNews::with('news')->find($grandNews->id);

            return response()->json([
                'success' => true,
                'message' => 'GrandNews updated successfully',
                'grandNews' => $grandNewsWithNews,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Cannot edit grandNews'
            ], 403, [], JSON_UNESCAPED_UNICODE);
        }
    }
}
