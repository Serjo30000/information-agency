<?php

namespace App\Http\Controllers;

use App\Http\Services\Filters\FilterRegion;
use App\Models\News;
use App\Models\RegionsAndPeoples;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

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
}
