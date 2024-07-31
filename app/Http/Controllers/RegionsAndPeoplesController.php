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

        return response()->json($regionsAndPeoples, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function allRegionsAndPeoplesPaginate(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $regionsAndPeoples = RegionsAndPeoples::all();

        if ($startDate) {
            $startDate = Carbon::parse($startDate)->startOfDay();
            $regionsAndPeoples = $regionsAndPeoples->filter(function($regionAndPeople) use ($startDate) {
                return Carbon::parse($regionAndPeople->date_birth_or_date_foundation)->greaterThanOrEqualTo($startDate);
            });
        }

        if ($endDate) {
            $endDate = Carbon::parse($endDate)->endOfDay();
            $regionsAndPeoples = $regionsAndPeoples->filter(function($regionAndPeople) use ($endDate) {
                return Carbon::parse($regionAndPeople->date_birth_or_date_foundation)->lessThanOrEqualTo($endDate);
            });
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $regionsAndPeoples->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginatedDTO = new LengthAwarePaginator(
            $currentItems,
            $regionsAndPeoples->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return response()->json($paginatedDTO, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function findRegionAndPeople($id){
        $regionAndPeople = RegionsAndPeoples::find($id);

        if ($regionAndPeople){
            return response()->json($regionAndPeople, 200, [], JSON_UNESCAPED_UNICODE);
        }
        else{
            return response()->json(['message' => 'RegionAndPeople not found'], 404, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function allPeoples()
    {
        $peoples = FilterPeople::allPeoplesByFilter();

        return response()->json($peoples, 200, [], JSON_UNESCAPED_UNICODE);
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
                return Carbon::parse($people->date_birth)->greaterThanOrEqualTo($startDate);
            });
        }

        if ($endDate) {
            $endDate = Carbon::parse($endDate)->endOfDay();
            $peoples = $peoples->filter(function($people) use ($endDate) {
                return Carbon::parse($people->date_birth)->lessThanOrEqualTo($endDate);
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

        return response()->json($paginatedDTO, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function findPeople($id){
        $people = FilterPeople::findPeopleByFilter($id);

        if ($people){
            return response()->json($people, 200, [], JSON_UNESCAPED_UNICODE);
        }
        else{
            return response()->json(['message' => 'People not found'], 404, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function allRegions()
    {
        $regions = FilterRegion::allRegionsByFilter();

        return response()->json($regions, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function listRegionsBySearch(Request $request)
    {
        $searchTerm = $request->input('search');
        $selectedRegions = $request->input('selected_regions', []);

        $regions = FilterRegion::allRegionsByFilterAndSearch($searchTerm, $selectedRegions);

        return response()->json([
            'regions' => $regions,
            'searchTerm' => $searchTerm,
            'selectedRegions' => $selectedRegions,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function allRegionsPaginate(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $regions = collect(FilterRegion::allRegionsByFilter());

        if ($startDate) {
            $startDate = Carbon::parse($startDate)->startOfDay();
            $regions = $regions->filter(function($region) use ($startDate) {
                return Carbon::parse($region->date_foundation)->greaterThanOrEqualTo($startDate);
            });
        }

        if ($endDate) {
            $endDate = Carbon::parse($endDate)->endOfDay();
            $regions = $regions->filter(function($region) use ($endDate) {
                return Carbon::parse($region->date_foundation)->lessThanOrEqualTo($endDate);
            });
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $regions->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginatedDTO = new LengthAwarePaginator(
            $currentItems,
            $regions->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return response()->json($paginatedDTO, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function findRegion($id){
        $region = FilterRegion::findRegionByFilter($id);

        if ($region){
            return response()->json($region, 200, [], JSON_UNESCAPED_UNICODE);
        }
        else{
            return response()->json(['message' => 'Region not found'], 404, [], JSON_UNESCAPED_UNICODE);
        }
    }
}
