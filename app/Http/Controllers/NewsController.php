<?php

namespace App\Http\Controllers;

use App\Http\Services\Filters\FilterRegion;
use App\Models\News;
use App\Models\RegionsAndPeoples;
use App\Models\Status;
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
        })->get();

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
            })
            ->take(10)
            ->get();

        return response()->json($news, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function allNewsPaginate(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $news = News::whereHas('status', function ($query) {
            $query->where('status', 'Опубликовано');
        })->get();

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

        $regionIds = RegionsAndPeoples::where('type', 'Region')->pluck('id')->toArray();

        $news = News::whereIn('regions_and_peoples_id', $regionIds)
            ->whereHas('status', function ($query) {
                $query->where('status', 'опубликовано');
            })->get();

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
            $searchTerm = strtolower($searchTerm);
            $news = $news->filter(function($newsOne) use ($searchTerm) {
                return stripos(strtolower($newsOne->content), $searchTerm) !== false;
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
            })
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
        $news = News::all();

        return response()->json($news, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function allNewsPaginateForPanel(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $news = News::all();

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

    public function findNewsOneForPanel($id){
        $newsOne = News::find($id);

        if ($newsOne){
            return response()->json($newsOne, 200, [], JSON_UNESCAPED_UNICODE);
        }
        else{
            return response()->json(['message' => 'NewsOne not found'], 404, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function createNews(Request $request){
        $rules = [
            'path_to_image_or_video' => 'required|string|unique:news,path_to_image_or_video',
            'title' => 'required|string',
            'content' => 'required|string',
            'source' => 'string',
            'publication_date' => [
                'required',
                'string',
                'date_format:"Y-m-d H:i:s"',
            ],
            'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
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

        $user = Auth::guard('sanctum')->user();

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
            'user_id' => $user->id,
            'regions_and_peoples_id' => $request->input('regions_and_peoples_id'),
            'status_id' => $status->id,
        ]);

        return response()->json([
            'success' => true,
            'news' => $news,
            'message' => 'Create successful'
        ], 201, [], JSON_UNESCAPED_UNICODE);
    }

    public function deleteNews($id){
        $news = News::find($id);

        if (!$news) {
            return response()->json([
                'success' => false,
                'message' => 'News not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
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
                'path_to_image_or_video' => [
                    'required',
                    'string',
                    Rule::unique('news', 'path_to_image_or_video')->ignore($news->id),
                ],
                'title' => 'required|string',
                'content' => 'required|string',
                'source' => 'string',
                'publication_date' => [
                    'required',
                    'string',
                    'date_format:"Y-m-d H:i:s"',
                ],
                'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
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

            $news->path_to_image_or_video = $request->input('path_to_image_or_video');
            $news->title = $request->input('title');
            $news->content = $request->input('content');
            $news->source = $request->input('source');
            $news->publication_date = $request->input('publication_date');
            $news->regions_and_peoples_id = $request->input('regions_and_peoples_id');

            $news->save();

            return response()->json([
                'success' => true,
                'message' => 'News updated successfully',
                'news' => $news,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Cannot edit news'
            ], 403, [], JSON_UNESCAPED_UNICODE);
        }
    }
}
