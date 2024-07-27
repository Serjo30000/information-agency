<?php

namespace App\Http\Controllers;

use App\Http\Services\Filters\FilterPeople;
use App\Http\Services\Filters\FilterRegion;
use App\Models\RegionsAndPeoples;
use Illuminate\Http\Request;

class RegionsAndPeoplesController extends Controller
{
    public function allRegionsAndPeoples()
    {
        $regionsAndPeoples = RegionsAndPeoples::all();

        return response()->json($regionsAndPeoples);
    }

    public function allPeoples()
    {
        $peoples = FilterPeople::allPeoplesByFilter();

        return response()->json($peoples);
    }

    public function allRegions()
    {
        $regions = FilterRegion::allRegionsByFilter();

        return response()->json($regions);
    }
}
