<?php

namespace App\Http\Controllers;

use App\Models\GrandNews;
use App\Models\News;
use App\Models\Status;
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
}
