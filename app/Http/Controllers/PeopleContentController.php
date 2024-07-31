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

        return response()->json($peopleContents, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function allPeopleContentsPaginate(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $peopleContents = PeopleContent::all();

        if ($startDate) {
            $startDate = Carbon::parse($startDate)->startOfDay();
            $peopleContents = $peopleContents->filter(function($peopleContent) use ($startDate) {
                return Carbon::parse($peopleContent->publication_date)->greaterThanOrEqualTo($startDate);
            });
        }

        if ($endDate) {
            $endDate = Carbon::parse($endDate)->endOfDay();
            $peopleContents = $peopleContents->filter(function($peopleContent) use ($endDate) {
                return Carbon::parse($peopleContent->publication_date)->lessThanOrEqualTo($endDate);
            });
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $peopleContents->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginatedDTO = new LengthAwarePaginator(
            $currentItems,
            $peopleContents->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return response()->json($paginatedDTO, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function findPeopleContent($id){
        $peopleContent = PeopleContent::find($id);

        if ($peopleContent){
            return response()->json($peopleContent, 200, [], JSON_UNESCAPED_UNICODE);
        }
        else{
            return response()->json(['message' => 'PeopleContent not found'], 404, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function allInterviews()
    {
        $interviews = FilterInterview::allInterviewsByFilter();

        return response()->json($interviews, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function listInterviewsTopThree()
    {
        $interviews = FilterInterview::listInterviewsByFilterByTop(3);

        return response()->json($interviews, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function listInterviewsTopFour()
    {
        $interviews = FilterInterview::listInterviewsByFilterByTop(4);

        return response()->json($interviews, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function listInterviewsTopFourteen()
    {
        $interviews = FilterInterview::listInterviewsByFilterByTop(14);

        return response()->json($interviews, 200, [], JSON_UNESCAPED_UNICODE);
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

        return response()->json($paginatedDTO, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function findInterview($id){
        $interview = FilterInterview::findInterviewByFilter($id);

        if ($interview){
            return response()->json($interview, 200, [], JSON_UNESCAPED_UNICODE);
        }
        else{
            return response()->json(['message' => 'Interview not found'], 404, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function allOpinions()
    {
        $opinions = FilterOpinion::allOpinionsByFilter();

        return response()->json($opinions, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function listOpinionsTopFour()
    {
        $opinions = FilterOpinion::listOpinionsByFilterByTop(4);

        return response()->json($opinions, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function listOpinionsTopFourteen()
    {
        $opinions = FilterOpinion::listOpinionsByFilterByTop(14);

        return response()->json($opinions, 200, [], JSON_UNESCAPED_UNICODE);
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

        return response()->json($paginatedDTO, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function findOpinion($id){
        $opinion = FilterOpinion::findOpinionByFilter($id);

        if ($opinion){
            return response()->json($opinion, 200, [], JSON_UNESCAPED_UNICODE);
        }
        else{
            return response()->json(['message' => 'Opinion not found'], 404, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function allPointViews()
    {
        $pointViews = FilterPointView::allPointViewsByFilter();

        return response()->json($pointViews, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function listPointViewsTopFour()
    {
        $pointViews = FilterPointView::listPointViewsByFilterByTop(4);

        return response()->json($pointViews, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function allPointViewsPaginate(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $pointViews = collect(FilterPointView::allPointViewsByFilter());

        if ($startDate) {
            $startDate = Carbon::parse($startDate)->startOfDay();
            $pointViews = $pointViews->filter(function($pointView) use ($startDate) {
                return Carbon::parse($pointView->created_at)->greaterThanOrEqualTo($startDate);
            });
        }

        if ($endDate) {
            $endDate = Carbon::parse($endDate)->endOfDay();
            $pointViews = $pointViews->filter(function($pointView) use ($endDate) {
                return Carbon::parse($pointView->created_at)->lessThanOrEqualTo($endDate);
            });
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $pointViews->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginatedDTO = new LengthAwarePaginator(
            $currentItems,
            $pointViews->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return response()->json($paginatedDTO, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function findPointView($id){
        $pointView = FilterPointView::findPointViewByFilter($id);

        if ($pointView){
            return response()->json($pointView, 200, [], JSON_UNESCAPED_UNICODE);
        }
        else{
            return response()->json(['message' => 'PointView not found'], 404, [], JSON_UNESCAPED_UNICODE);
        }
    }
}
