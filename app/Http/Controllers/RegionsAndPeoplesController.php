<?php

namespace App\Http\Controllers;

use App\Http\Services\Filters\FilterPeople;
use App\Http\Services\Filters\FilterRegion;
use App\Http\Services\Mappers\MapperPeople;
use App\Http\Services\Mappers\MapperRegion;
use App\Models\DTO\Region;
use App\Models\RegionsAndPeoples;
use App\Rules\FullName;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

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

        if ($perPage<=0){
            return response()->json([
                'success' => false,
                'message' => 'Paginate not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

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

        if ($perPage<=0){
            return response()->json([
                'success' => false,
                'message' => 'Paginate not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

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

        if ($perPage<=0){
            return response()->json([
                'success' => false,
                'message' => 'Paginate not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

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

    public function createRegion(Request $request){
        $rules = [
            'path_to_image' => 'required|string',
            'type_region' => 'required|string',
            'name_region' => 'required|string',
            'content' => 'required|string',
            'date_foundation' => 'required|date_format:Y-m-d',
            'sys_Comment' => 'nullable|string',
        ];

        try {
            $request->validate($rules);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed'
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }

        $regionsAndPeoples = RegionsAndPeoples::create([
            'path_to_image' => $request->input('path_to_image'),
            'position_or_type_region' => $request->input('type_region'),
            'fio_or_name_region' => $request->input('name_region'),
            'content' => $request->input('content'),
            'type' => "Region",
            'date_birth_or_date_foundation' => $request->input('date_foundation'),
            'sys_Comment' => $request->input('sys_Comment'),
        ]);

        $regionsAndPeoplesNew = RegionsAndPeoples::find($regionsAndPeoples->id);

        $region = MapperRegion::toRegion($regionsAndPeoplesNew);

        return response()->json([
            'success' => true,
            'region' => $region,
            'message' => 'Create successful'
        ], 201, [], JSON_UNESCAPED_UNICODE);
    }

    public function createPeople(Request $request){
        $rules = [
            'path_to_image' => 'required|string',
            'position' => 'required|string',
            'fio' => ['required','string','max:255',new FullName()],
            'place_work' => 'required|string',
            'content' => 'required|string',
            'date_birth' => 'required|date_format:Y-m-d',
            'sys_Comment' => 'nullable|string',
        ];

        try {
            $request->validate($rules);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed'
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }

        $regionsAndPeoples = RegionsAndPeoples::create([
            'path_to_image' => $request->input('path_to_image'),
            'position_or_type_region' => $request->input('position'),
            'fio_or_name_region' => $request->input('fio'),
            'place_work' => $request->input('place_work'),
            'content' => $request->input('content'),
            'type' => "People",
            'date_birth_or_date_foundation' => $request->input('date_birth'),
            'sys_Comment' => $request->input('sys_Comment'),
        ]);

        $regionsAndPeoplesNew = RegionsAndPeoples::find($regionsAndPeoples->id);

        $people = MapperPeople::toPeople($regionsAndPeoplesNew);

        return response()->json([
            'success' => true,
            'people' => $people,
            'message' => 'Create successful'
        ], 201, [], JSON_UNESCAPED_UNICODE);
    }

    public function deleteRegion($id){
        $regionsAndPeoples = RegionsAndPeoples::find($id);

        if (!$regionsAndPeoples) {
            return response()->json([
                'success' => false,
                'message' => 'RegionsAndPeoples not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        if ($regionsAndPeoples->type !== 'Region'){
            return response()->json([
                'success' => false,
                'message' => 'Region not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $regionsAndPeoples->delete();

        return response()->json([
            'success' => true,
            'message' => 'Region deleted successfully'
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function deletePeople($id){
        $regionsAndPeoples = RegionsAndPeoples::find($id);

        if (!$regionsAndPeoples) {
            return response()->json([
                'success' => false,
                'message' => 'RegionsAndPeoples not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        if ($regionsAndPeoples->type !== 'People'){
            return response()->json([
                'success' => false,
                'message' => 'People not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $regionsAndPeoples->delete();

        return response()->json([
            'success' => true,
            'message' => 'People deleted successfully'
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function editRegion($id, Request $request){
        $regionsAndPeoples = RegionsAndPeoples::find($id);

        if (!$regionsAndPeoples) {
            return response()->json([
                'success' => false,
                'message' => 'RegionsAndPeoples not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        if ($regionsAndPeoples->type !== 'Region'){
            return response()->json([
                'success' => false,
                'message' => 'Region not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $rules = [
            'path_to_image' => 'required|string',
            'type_region' => 'required|string',
            'name_region' => 'required|string',
            'content' => 'required|string',
            'date_foundation' => 'required|date_format:Y-m-d',
            'sys_Comment' => 'nullable|string',
        ];

        try {
            $request->validate($rules);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed'
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }

        $regionsAndPeoples->path_to_image = $request->input('path_to_image');
        $regionsAndPeoples->position_or_type_region = $request->input('type_region');
        $regionsAndPeoples->fio_or_name_region = $request->input('name_region');
        $regionsAndPeoples->content = $request->input('content');
        $regionsAndPeoples->date_birth_or_date_foundation = $request->input('date_foundation');
        $regionsAndPeoples->sys_Comment = $request->input('sys_Comment');

        $regionsAndPeoples->save();

        $region = MapperRegion::toRegion($regionsAndPeoples);

        return response()->json([
            'success' => true,
            'message' => 'Region updated successfully',
            'region' => $region,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function editPeople($id, Request $request){
        $regionsAndPeoples = RegionsAndPeoples::find($id);

        if (!$regionsAndPeoples) {
            return response()->json([
                'success' => false,
                'message' => 'RegionsAndPeoples not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        if ($regionsAndPeoples->type !== 'People'){
            return response()->json([
                'success' => false,
                'message' => 'People not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $rules = [
            'path_to_image' => 'required|string',
            'position' => 'required|string',
            'fio' => ['required','string','max:255',new FullName()],
            'place_work' => 'required|string',
            'content' => 'required|string',
            'date_birth' => 'required|date_format:Y-m-d',
            'sys_Comment' => 'nullable|string',
        ];

        try {
            $request->validate($rules);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed'
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }

        $regionsAndPeoples->path_to_image = $request->input('path_to_image');
        $regionsAndPeoples->position_or_type_region = $request->input('position');
        $regionsAndPeoples->fio_or_name_region = $request->input('fio');
        $regionsAndPeoples->place_work = $request->input('place_work');
        $regionsAndPeoples->content = $request->input('content');
        $regionsAndPeoples->date_birth_or_date_foundation = $request->input('date_birth');
        $regionsAndPeoples->sys_Comment = $request->input('sys_Comment');

        $regionsAndPeoples->save();

        $people = MapperPeople::toPeople($regionsAndPeoples);

        return response()->json([
            'success' => true,
            'message' => 'People updated successfully',
            'people' => $people,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
