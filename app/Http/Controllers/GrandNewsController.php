<?php

namespace App\Http\Controllers;

use App\Models\GrandNews;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class GrandNewsController extends Controller
{
    public function allGrandNews()
    {
        $grandNews = GrandNews::all();

        return response()->json($grandNews);
    }

    public function listGrandNewsTopFive()
    {
        $grandNews = GrandNews::take(5)->get();

        return response()->json($grandNews);
    }

    public function listGrandNewsTopTwenty()
    {
        $grandNews = GrandNews::take(20)->get();

        return response()->json($grandNews);
    }

    public function allGrandNewsPaginate(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $grandNews = GrandNews::all();

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

        return response()->json($paginatedDTO);
    }

    public function findGrandNewsOne($id){
        $grandNewsOne = GrandNews::find($id);

        if ($grandNewsOne){
            return response()->json($grandNewsOne);
        }
        else{
            return response()->json(['message' => 'GrandNewsOne not found'], 404);
        }
    }
}
