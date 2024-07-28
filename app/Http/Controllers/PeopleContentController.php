<?php

namespace App\Http\Controllers;

use App\Http\Services\Filters\FilterInterview;
use App\Http\Services\Filters\FilterOpinion;
use App\Http\Services\Filters\FilterPointView;
use App\Models\PeopleContent;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PeopleContentController extends Controller
{
    public function allPeopleContents()
    {
        $peopleContents = PeopleContent::all();

        return response()->json($peopleContents);
    }

    public function findPeopleContent($id){
        $peopleContent = PeopleContent::find($id);

        if ($peopleContent){
            return response()->json($peopleContent);
        }
        else{
            return response()->json(['message' => 'PeopleContent not found'], 404);
        }
    }

    public function allInterviews()
    {
        $interviews = FilterInterview::allInterviewsByFilter();

        return response()->json($interviews);
    }

    public function allInterviewsPaginate(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $interviews = collect(FilterInterview::allInterviewsByFilter());

        if ($startDate) {
            $startDate = Carbon::parse($startDate)->startOfDay();
            $interviews = $interviews->filter(function($interview) use ($startDate) {
                return Carbon::parse($interview->publication_date)->greaterThanOrEqualTo($startDate);
            });
        }

        if ($endDate) {
            $endDate = Carbon::parse($endDate)->endOfDay();
            $interviews = $interviews->filter(function($interview) use ($endDate) {
                return Carbon::parse($interview->publication_date)->lessThanOrEqualTo($endDate);
            });
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $interviews->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginatedDTO = new LengthAwarePaginator(
            $currentItems,
            $interviews->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return response()->json($paginatedDTO);
    }

    public function findInterview($id){
        $interview = FilterInterview::findInterviewByFilter($id);

        if ($interview){
            return response()->json($interview);
        }
        else{
            return response()->json(['message' => 'Interview not found'], 404);
        }
    }

    public function allOpinions()
    {
        $opinions = FilterOpinion::allOpinionsByFilter();

        return response()->json($opinions);
    }

    public function allOpinionsPaginate(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $opinions = collect(FilterOpinion::allOpinionsByFilter());

        if ($startDate) {
            $startDate = Carbon::parse($startDate)->startOfDay();
            $opinions = $opinions->filter(function($opinion) use ($startDate) {
                return Carbon::parse($opinion->publication_date)->greaterThanOrEqualTo($startDate);
            });
        }

        if ($endDate) {
            $endDate = Carbon::parse($endDate)->endOfDay();
            $opinions = $opinions->filter(function($opinion) use ($endDate) {
                return Carbon::parse($opinion->publication_date)->lessThanOrEqualTo($endDate);
            });
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $opinions->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginatedDTO = new LengthAwarePaginator(
            $currentItems,
            $opinions->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return response()->json($paginatedDTO);
    }

    public function findOpinion($id){
        $opinion = FilterOpinion::findOpinionByFilter($id);

        if ($opinion){
            return response()->json($opinion);
        }
        else{
            return response()->json(['message' => 'Opinion not found'], 404);
        }
    }

    public function allPointViews()
    {
        $pointViews = FilterPointView::allPointViewsByFilter();

        return response()->json($pointViews);
    }

    public function findPointView($id){
        $pointView = FilterPointView::findPointViewByFilter($id);

        if ($pointView){
            return response()->json($pointView);
        }
        else{
            return response()->json(['message' => 'PointView not found'], 404);
        }
    }
}
