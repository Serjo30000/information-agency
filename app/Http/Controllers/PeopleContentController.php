<?php

namespace App\Http\Controllers;

use App\Http\Services\Filters\FilterInterview;
use App\Http\Services\Filters\FilterOpinion;
use App\Http\Services\Filters\FilterPointView;
use App\Http\Services\Mappers\MapperInterview;
use App\Http\Services\Mappers\MapperOpinion;
use App\Http\Services\Mappers\MapperPointView;
use App\Http\Services\StatusManagement\StatusMatcher;
use App\Models\PeopleContent;
use App\Models\RegionsAndPeoples;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PeopleContentController extends Controller
{
    public function allPeopleContents()
    {
        $peopleContents = PeopleContent::whereHas('status', function ($query) {
            $query->where('status', 'Опубликовано');
        })->get();

        return response()->json($peopleContents, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function allPeopleContentsPaginate(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $peopleContents = PeopleContent::whereHas('status', function ($query) {
            $query->where('status', 'Опубликовано');
        })->get();

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
        $peopleContent = PeopleContent::where('id', $id)
            ->whereHas('status', function ($query) {
                $query->where('status', 'Опубликовано');
            })
            ->first();

        if ($peopleContent){
            return response()->json($peopleContent, 200, [], JSON_UNESCAPED_UNICODE);
        }
        else{
            return response()->json(['message' => 'PeopleContent not found'], 404, [], JSON_UNESCAPED_UNICODE);
        }
    }



    public function allPeopleContentsForPanel()
    {
        $peopleContents = PeopleContent::all();

        return response()->json($peopleContents, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function allPeopleContentsPaginateForPanel(Request $request)
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

    public function findPeopleContentForPanel($id){
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




    public function allInterviewsForPanel()
    {
        $interviews = FilterInterview::allInterviewsByFilterForPanel();

        return response()->json($interviews, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function allInterviewsPaginateForPanel(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $interviews = collect(FilterInterview::allInterviewsByFilterForPanel());

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

    public function findInterviewForPanel($id){
        $interview = FilterInterview::findInterviewByFilterForPanel($id);

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




    public function allOpinionsForPanel()
    {
        $opinions = FilterOpinion::allOpinionsByFilterForPanel();

        return response()->json($opinions, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function allOpinionsPaginateForPanel(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $opinions = collect(FilterOpinion::allOpinionsByFilterForPanel());

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

    public function findOpinionForPanel($id){
        $opinion = FilterOpinion::findOpinionByFilterForPanel($id);

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




    public function allPointViewsForPanel()
    {
        $pointViews = FilterPointView::allPointViewsByFilterForPanel();

        return response()->json($pointViews, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function allPointViewsPaginateForPanel(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $pointViews = collect(FilterPointView::allPointViewsByFilterForPanel());

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

    public function findPointViewForPanel($id){
        $pointView = FilterPointView::findPointViewByFilterForPanel($id);

        if ($pointView){
            return response()->json($pointView, 200, [], JSON_UNESCAPED_UNICODE);
        }
        else{
            return response()->json(['message' => 'PointView not found'], 404, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function createInterview(Request $request){
        $rules = [
            'path_to_image' => 'required|string',
            'title' => 'required|string',
            'content' => 'required|string',
            'source' => 'required|string',
            'publication_date' => 'required|date_format:Y-m-d',
            'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
        ];

        try {
            $request->validate($rules);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed'
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }

        $regionsAndPeoples = RegionsAndPeoples::find($request->input('regions_and_peoples_id'));

        if (!$regionsAndPeoples) {
            return response()->json([
                'success' => false,
                'message' => 'Region and People record not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        if ($regionsAndPeoples->type !== 'People') {
            return response()->json([
                'success' => false,
                'message' => 'Invalid type. Expected "People".'
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }

        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401, [], JSON_UNESCAPED_UNICODE);
        }

        $status = Status::where('status', 'Редактируется')->first();

        if (!$status) {
            return response()->json([
                'success' => false,
                'message' => 'Status not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $peopleContent = PeopleContent::create([
            'path_to_image' => $request->input('path_to_image'),
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'source' => $request->input('source'),
            'type' => "Interview",
            'publication_date' => $request->input('publication_date'),
            'regions_and_peoples_id' => $request->input('regions_and_peoples_id'),
            'user_id' => $user->id,
            'status_id' => $status->id,
        ]);

        $interview = MapperInterview::toInterview($peopleContent);

        return response()->json([
            'success' => true,
            'interview' => $interview,
            'message' => 'Create successful'
        ], 201, [], JSON_UNESCAPED_UNICODE);
    }

    public function createOpinion(Request $request){
        $rules = [
            'path_to_image' => 'required|string',
            'title' => 'required|string',
            'content' => 'required|string',
            'publication_date' => 'required|date_format:Y-m-d',
            'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
        ];

        try {
            $request->validate($rules);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed'
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }

        $regionsAndPeoples = RegionsAndPeoples::find($request->input('regions_and_peoples_id'));

        if (!$regionsAndPeoples) {
            return response()->json([
                'success' => false,
                'message' => 'Region and People record not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        if ($regionsAndPeoples->type !== 'People') {
            return response()->json([
                'success' => false,
                'message' => 'Invalid type. Expected "People".'
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }

        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401, [], JSON_UNESCAPED_UNICODE);
        }

        $status = Status::where('status', 'Редактируется')->first();

        if (!$status) {
            return response()->json([
                'success' => false,
                'message' => 'Status not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $peopleContent = PeopleContent::create([
            'path_to_image' => $request->input('path_to_image'),
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'type' => "Opinion",
            'publication_date' => $request->input('publication_date'),
            'regions_and_peoples_id' => $request->input('regions_and_peoples_id'),
            'user_id' => $user->id,
            'status_id' => $status->id,
        ]);

        $opinion = MapperOpinion::toOpinion($peopleContent);

        return response()->json([
            'success' => true,
            'opinion' => $opinion,
            'message' => 'Create successful'
        ], 201, [], JSON_UNESCAPED_UNICODE);
    }

    public function createPointView(Request $request){
        $rules = [
            'title' => 'required|string',
            'content' => 'required|string',
            'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
        ];

        try {
            $request->validate($rules);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed'
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }

        $regionsAndPeoples = RegionsAndPeoples::find($request->input('regions_and_peoples_id'));

        if (!$regionsAndPeoples) {
            return response()->json([
                'success' => false,
                'message' => 'Region and People record not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        if ($regionsAndPeoples->type !== 'People') {
            return response()->json([
                'success' => false,
                'message' => 'Invalid type. Expected "People".'
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }

        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401, [], JSON_UNESCAPED_UNICODE);
        }

        $status = Status::where('status', 'Редактируется')->first();

        if (!$status) {
            return response()->json([
                'success' => false,
                'message' => 'Status not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $peopleContent = PeopleContent::create([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'type' => "PointView",
            'regions_and_peoples_id' => $request->input('regions_and_peoples_id'),
            'user_id' => $user->id,
            'status_id' => $status->id,
        ]);

        $pointView = MapperPointView::toPointView($peopleContent);

        return response()->json([
            'success' => true,
            'pointView' => $pointView,
            'message' => 'Create successful'
        ], 201, [], JSON_UNESCAPED_UNICODE);
    }

    public function deleteInterview($id){
        $peopleContent = PeopleContent::find($id);

        if (!$peopleContent) {
            return response()->json([
                'success' => false,
                'message' => 'PeopleContent not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        if ($peopleContent->type !== 'Interview'){
            return response()->json([
                'success' => false,
                'message' => 'Interview not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $peopleContent->delete();

        return response()->json([
            'success' => true,
            'message' => 'Interview deleted successfully'
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function deleteOpinion($id){
        $peopleContent = PeopleContent::find($id);

        if (!$peopleContent) {
            return response()->json([
                'success' => false,
                'message' => 'PeopleContent not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        if ($peopleContent->type !== 'Opinion'){
            return response()->json([
                'success' => false,
                'message' => 'Opinion not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $peopleContent->delete();

        return response()->json([
            'success' => true,
            'message' => 'Opinion deleted successfully'
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function deletePointView($id){
        $peopleContent = PeopleContent::find($id);

        if (!$peopleContent) {
            return response()->json([
                'success' => false,
                'message' => 'PeopleContent not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        if ($peopleContent->type !== 'PointView'){
            return response()->json([
                'success' => false,
                'message' => 'PointView not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $peopleContent->delete();

        return response()->json([
            'success' => true,
            'message' => 'PointView deleted successfully'
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function editInterview($id, Request $request){
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401, [], JSON_UNESCAPED_UNICODE);
        }

        $peopleContent = PeopleContent::find($id);

        if (!$peopleContent) {
            return response()->json([
                'success' => false,
                'message' => 'PeopleContent not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        if ($peopleContent->type !== 'Interview'){
            return response()->json([
                'success' => false,
                'message' => 'Interview not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $status = Status::find($peopleContent->status_id);

        if (!$status) {
            return response()->json([
                'success' => false,
                'message' => 'Status not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        if ($user->id == $peopleContent->user_id && $status->status == "Редактируется"){
            $rules = [
                'path_to_image' => 'required|string',
                'title' => 'required|string',
                'content' => 'required|string',
                'source' => 'required|string',
                'publication_date' => 'required|date_format:Y-m-d',
                'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
            ];

            try {
                $request->validate($rules);
            } catch (ValidationException $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed'
                ], 422, [], JSON_UNESCAPED_UNICODE);
            }

            $regionsAndPeoples = RegionsAndPeoples::find($request->input('regions_and_peoples_id'));

            if (!$regionsAndPeoples) {
                return response()->json([
                    'success' => false,
                    'message' => 'Region and People record not found'
                ], 404, [], JSON_UNESCAPED_UNICODE);
            }

            if ($regionsAndPeoples->type !== 'People') {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid type. Expected "People".'
                ], 422, [], JSON_UNESCAPED_UNICODE);
            }

            $peopleContent->path_to_image = $request->input('path_to_image');
            $peopleContent->title = $request->input('title');
            $peopleContent->content = $request->input('content');
            $peopleContent->source = $request->input('source');
            $peopleContent->publication_date = $request->input('publication_date');
            $peopleContent->regions_and_peoples_id = $request->input('regions_and_peoples_id');

            $peopleContent->save();

            $interview = MapperInterview::toInterview($peopleContent);

            return response()->json([
                'success' => true,
                'message' => 'Interview updated successfully',
                'interview' => $interview,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Cannot edit interview'
            ], 403, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function editOpinion($id, Request $request){
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401, [], JSON_UNESCAPED_UNICODE);
        }

        $peopleContent = PeopleContent::find($id);

        if (!$peopleContent) {
            return response()->json([
                'success' => false,
                'message' => 'PeopleContent not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        if ($peopleContent->type !== 'Opinion'){
            return response()->json([
                'success' => false,
                'message' => 'Opinion not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $status = Status::find($peopleContent->status_id);

        if (!$status) {
            return response()->json([
                'success' => false,
                'message' => 'Status not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        if ($user->id == $peopleContent->user_id && $status->status == "Редактируется"){
            $rules = [
                'path_to_image' => 'required|string',
                'title' => 'required|string',
                'content' => 'required|string',
                'publication_date' => 'required|date_format:Y-m-d',
                'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
            ];

            try {
                $request->validate($rules);
            } catch (ValidationException $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed'
                ], 422, [], JSON_UNESCAPED_UNICODE);
            }

            $regionsAndPeoples = RegionsAndPeoples::find($request->input('regions_and_peoples_id'));

            if (!$regionsAndPeoples) {
                return response()->json([
                    'success' => false,
                    'message' => 'Region and People record not found'
                ], 404, [], JSON_UNESCAPED_UNICODE);
            }

            if ($regionsAndPeoples->type !== 'People') {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid type. Expected "People".'
                ], 422, [], JSON_UNESCAPED_UNICODE);
            }

            $peopleContent->path_to_image = $request->input('path_to_image');
            $peopleContent->title = $request->input('title');
            $peopleContent->content = $request->input('content');
            $peopleContent->publication_date = $request->input('publication_date');
            $peopleContent->regions_and_peoples_id = $request->input('regions_and_peoples_id');

            $peopleContent->save();

            $opinion = MapperOpinion::toOpinion($peopleContent);

            return response()->json([
                'success' => true,
                'message' => 'Opinion updated successfully',
                'opinion' => $opinion,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Cannot edit opinion'
            ], 403, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function editPointView($id, Request $request){
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401, [], JSON_UNESCAPED_UNICODE);
        }

        $peopleContent = PeopleContent::find($id);

        if (!$peopleContent) {
            return response()->json([
                'success' => false,
                'message' => 'PeopleContent not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        if ($peopleContent->type !== 'PointView'){
            return response()->json([
                'success' => false,
                'message' => 'PointView not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $status = Status::find($peopleContent->status_id);

        if (!$status) {
            return response()->json([
                'success' => false,
                'message' => 'Status not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        if ($user->id == $peopleContent->user_id && $status->status == "Редактируется"){
            $rules = [
                'title' => 'required|string',
                'content' => 'required|string',
                'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
            ];

            try {
                $request->validate($rules);
            } catch (ValidationException $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed'
                ], 422, [], JSON_UNESCAPED_UNICODE);
            }

            $regionsAndPeoples = RegionsAndPeoples::find($request->input('regions_and_peoples_id'));

            if (!$regionsAndPeoples) {
                return response()->json([
                    'success' => false,
                    'message' => 'Region and People record not found'
                ], 404, [], JSON_UNESCAPED_UNICODE);
            }

            if ($regionsAndPeoples->type !== 'People') {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid type. Expected "People".'
                ], 422, [], JSON_UNESCAPED_UNICODE);
            }

            $peopleContent->title = $request->input('title');
            $peopleContent->content = $request->input('content');
            $peopleContent->regions_and_peoples_id = $request->input('regions_and_peoples_id');

            $peopleContent->save();

            $pointView = MapperPointView::toPointView($peopleContent);

            return response()->json([
                'success' => true,
                'message' => 'PointView updated successfully',
                'pointView' => $pointView,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Cannot edit pointView'
            ], 403, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function editInterviewByStatus($id, Request $request){
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401, [], JSON_UNESCAPED_UNICODE);
        }

        $peopleContent = PeopleContent::find($id);

        if (!$peopleContent) {
            return response()->json([
                'success' => false,
                'message' => 'PeopleContent not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        if ($peopleContent->type !== 'Interview'){
            return response()->json([
                'success' => false,
                'message' => 'Interview not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $oldStatus = Status::find($peopleContent->status_id);

        if (!$oldStatus) {
            return response()->json([
                'success' => false,
                'message' => 'OldStatus not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $rules = [
            'status' => 'required|string|exists:statuses,status',
        ];

        try {
            $request->validate($rules);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed'
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }

        $status = Status::where('status', $request->input('status'))->first();

        if (!$status) {
            return response()->json([
                'success' => false,
                'message' => 'Status not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $is_status = false;

        if ($user->hasRole('editor') && !$user->hasRole('admin') && !$user->hasRole('super_admin') && $user->id == $peopleContent->user_id){
            if ($status->order === 0){
                $is_status = StatusMatcher::isMatchingStatus($status, $oldStatus, [
                    'Редактируется' => ['Снято с публикации', 'Ожидает подтверждения'],
                    'Ожидает подтверждения' => ['Редактируется'],
                    'Снято с публикации' => ['Опубликовано'],
                ]);
            }
        }
        else if ($user->hasRole('admin') || $user->hasRole('super_admin')){
            if ($status->order === 0 || $status->order === 1){
                $is_status = StatusMatcher::isMatchingStatus($status, $oldStatus, [
                    'Редактируется' => ['Снято с публикации', 'Ожидает подтверждения', 'Заблокировано'],
                    'Ожидает подтверждения' => ['Редактируется'],
                    'Снято с публикации' => ['Опубликовано'],
                    'Опубликовано' => ['Ожидает подтверждения'],
                    'Ожидает публикации' => ['Ожидает подтверждения'],
                    'Заблокировано' => ['Редактируется', 'Ожидает подтверждения', 'Снято с публикации', 'Опубликовано', 'Ожидает публикации'],
                ]);
            }
        }

        if ($is_status){
            $peopleContent->status_id = $status->id;

            $peopleContent->save();

            $interview = MapperInterview::toInterview($peopleContent);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'interview' => $interview,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Cannot edit status'
            ], 403, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function editOpinionByStatus($id, Request $request){
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401, [], JSON_UNESCAPED_UNICODE);
        }

        $peopleContent = PeopleContent::find($id);

        if (!$peopleContent) {
            return response()->json([
                'success' => false,
                'message' => 'PeopleContent not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        if ($peopleContent->type !== 'Opinion'){
            return response()->json([
                'success' => false,
                'message' => 'Opinion not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $oldStatus = Status::find($peopleContent->status_id);

        if (!$oldStatus) {
            return response()->json([
                'success' => false,
                'message' => 'OldStatus not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $rules = [
            'status' => 'required|string|exists:statuses,status',
        ];

        try {
            $request->validate($rules);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed'
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }

        $status = Status::where('status', $request->input('status'))->first();

        if (!$status) {
            return response()->json([
                'success' => false,
                'message' => 'Status not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $is_status = false;

        if ($user->hasRole('editor') && !$user->hasRole('admin') && !$user->hasRole('super_admin') && $user->id == $peopleContent->user_id){
            if ($status->order === 0){
                $is_status = StatusMatcher::isMatchingStatus($status, $oldStatus, [
                    'Редактируется' => ['Снято с публикации', 'Ожидает подтверждения'],
                    'Ожидает подтверждения' => ['Редактируется'],
                    'Снято с публикации' => ['Опубликовано'],
                ]);
            }
        }
        else if ($user->hasRole('admin') || $user->hasRole('super_admin')){
            if ($status->order === 0 || $status->order === 1){
                $is_status = StatusMatcher::isMatchingStatus($status, $oldStatus, [
                    'Редактируется' => ['Снято с публикации', 'Ожидает подтверждения', 'Заблокировано'],
                    'Ожидает подтверждения' => ['Редактируется'],
                    'Снято с публикации' => ['Опубликовано'],
                    'Опубликовано' => ['Ожидает подтверждения'],
                    'Ожидает публикации' => ['Ожидает подтверждения'],
                    'Заблокировано' => ['Редактируется', 'Ожидает подтверждения', 'Снято с публикации', 'Опубликовано', 'Ожидает публикации'],
                ]);
            }
        }

        if ($is_status){
            $peopleContent->status_id = $status->id;

            $peopleContent->save();

            $opinion = MapperOpinion::toOpinion($peopleContent);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'opinion' => $opinion,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Cannot edit status'
            ], 403, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function editPointViewByStatus($id, Request $request){
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401, [], JSON_UNESCAPED_UNICODE);
        }

        $peopleContent = PeopleContent::find($id);

        if (!$peopleContent) {
            return response()->json([
                'success' => false,
                'message' => 'PeopleContent not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        if ($peopleContent->type !== 'PointView'){
            return response()->json([
                'success' => false,
                'message' => 'PointView not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $oldStatus = Status::find($peopleContent->status_id);

        if (!$oldStatus) {
            return response()->json([
                'success' => false,
                'message' => 'OldStatus not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $rules = [
            'status' => 'required|string|exists:statuses,status',
        ];

        try {
            $request->validate($rules);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed'
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }

        $status = Status::where('status', $request->input('status'))->first();

        if (!$status) {
            return response()->json([
                'success' => false,
                'message' => 'Status not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $is_status = false;

        if ($user->hasRole('editor') && !$user->hasRole('admin') && !$user->hasRole('super_admin') && $user->id == $peopleContent->user_id){
            if ($status->order === 0){
                $is_status = StatusMatcher::isMatchingStatus($status, $oldStatus, [
                    'Редактируется' => ['Снято с публикации', 'Ожидает подтверждения'],
                    'Ожидает подтверждения' => ['Редактируется'],
                    'Снято с публикации' => ['Опубликовано'],
                ]);
            }
        }
        else if ($user->hasRole('admin') || $user->hasRole('super_admin')){
            if ($status->order === 0 || $status->order === 1){
                $is_status = StatusMatcher::isMatchingStatus($status, $oldStatus, [
                    'Редактируется' => ['Снято с публикации', 'Ожидает подтверждения', 'Заблокировано'],
                    'Ожидает подтверждения' => ['Редактируется'],
                    'Снято с публикации' => ['Опубликовано'],
                    'Опубликовано' => ['Ожидает подтверждения'],
                    'Ожидает публикации' => ['Ожидает подтверждения'],
                    'Заблокировано' => ['Редактируется', 'Ожидает подтверждения', 'Снято с публикации', 'Опубликовано', 'Ожидает публикации'],
                ]);
            }
        }

        if ($is_status){
            $peopleContent->status_id = $status->id;

            $peopleContent->save();

            $pointView = MapperPointView::toPointView($peopleContent);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'pointView' => $pointView,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Cannot edit status'
            ], 403, [], JSON_UNESCAPED_UNICODE);
        }
    }
}
