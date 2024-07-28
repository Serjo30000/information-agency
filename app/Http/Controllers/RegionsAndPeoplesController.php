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
