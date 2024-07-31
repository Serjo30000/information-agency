<?php

namespace App\Http\Controllers;

use App\Http\Services\Filters\FilterRegion;
use App\Models\News;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class NewsController extends Controller
{
    public function allNews()
    {
        $news = News::all();

        return response()->json($news);
    }

    public function listNewsTopTenByRegion($id_region)
    {
        $region = FilterRegion::findRegionByFilter($id_region);
        $region->getNews()->take(10);
        $news = News::all();

        return response()->json($news);
    }

    public function allNewsPaginate(Request $request)
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

        return response()->json($paginatedDTO);
    }

    public function findNewsOne($id){
        $newsOne = News::find($id);

        if ($newsOne){
            return response()->json($newsOne);
        }
        else{
            return response()->json(['message' => 'NewsOne not found'], 404);
        }
    }
}
