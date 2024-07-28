<?php

namespace App\Http\Controllers;

use App\Http\Services\Filters\FilterPeople;
use App\Http\Services\Filters\FilterRegion;
use App\Models\RegionsAndPeoples;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class RegionsAndPeoplesController extends Controller
{
    public function allRegionsAndPeoples()
    {
        $regionsAndPeoples = RegionsAndPeoples::all();

        return response()->json($regionsAndPeoples);
    }

    public function findRegionAndPeople($id){
        $regionAndPeople = RegionsAndPeoples::find($id);

        if ($regionAndPeople){
            return response()->json($regionAndPeople);
        }
        else{
            return response()->json(['message' => 'RegionAndPeople not found'], 404);
        }
    }

    public function allPeoples()
    {
        $peoples = FilterPeople::allPeoplesByFilter();

        return response()->json($peoples);
    }

    public function allPeoplesPaginate(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $peoples = collect(FilterPeople::allPeoplesByFilter());

        if ($startDate) {
            $startDate = Carbon::parse($startDate)->startOfDay();
            $peoples = $peoples->filter(function($people) use ($startDate) {
                return Carbon::parse($people->publication_date)->greaterThanOrEqualTo($startDate);
            });
        }

        if ($endDate) {
            $endDate = Carbon::parse($endDate)->endOfDay();
            $peoples = $peoples->filter(function($people) use ($endDate) {
                return Carbon::parse($people->publication_date)->lessThanOrEqualTo($endDate);
            });
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $peoples->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginatedDTO = new LengthAwarePaginator(
            $currentItems,
            $peoples->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return response()->json($paginatedDTO);
    }

    public function findPeople($id){
        $people = FilterPeople::findPeopleByFilter($id);

        if ($people){
            return response()->json($people);
        }
        else{
            return response()->json(['message' => 'People not found'], 404);
        }
    }

    public function allRegions()
    {
        $regions = FilterRegion::allRegionsByFilter();

        return response()->json($regions);
    }

    public function findRegion($id){
        $region = FilterRegion::findRegionByFilter($id);

        if ($region){
            return response()->json($region);
        }
        else{
            return response()->json(['message' => 'Region not found'], 404);
        }
    }
}
