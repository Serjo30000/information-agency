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
use App\Models\User;
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
        })->with(['status', 'regionsAndPeoples'])->get();

        return response()->json($peopleContents, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function allPeopleContentsPaginate(Request $request)
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

        $peopleContents = PeopleContent::whereHas('status', function ($query) {
            $query->where('status', 'Опубликовано');
        })->with(['status', 'regionsAndPeoples'])->get();

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
            })->with(['status', 'regionsAndPeoples'])
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
        $peopleContents = PeopleContent::with(['status', 'regionsAndPeoples'])->get();

        return response()->json($peopleContents, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function allPeopleContentsPaginateForPanel(Request $request)
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

        $peopleContents = PeopleContent::with(['status', 'regionsAndPeoples'])->get();

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
        $peopleContent = PeopleContent::with(['status', 'regionsAndPeoples'])->find($id);

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

        if ($perPage<=0){
            return response()->json([
                'success' => false,
                'message' => 'Paginate not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

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

        if ($perPage<=0){
            return response()->json([
                'success' => false,
                'message' => 'Paginate not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

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

        if ($perPage<=0){
            return response()->json([
                'success' => false,
                'message' => 'Paginate not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

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

    public function findPointViewForPanel($id){
        $pointView = FilterPointView::findPointViewByFilterForPanel($id);

        if ($pointView){
            return response()->json($pointView, 200, [], JSON_UNESCAPED_UNICODE);
        }
        else{
            return response()->json(['message' => 'PointView not found'], 404, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function allInterviewsBySearchAndFiltersAndStatusesAndSortForPanel(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $selectedStatuses = $request->input('selected_statuses', []);
        $sortField = $request->input('sort_field', 'publication_date');
        $sortDirection = $request->input('sort_direction', 'desc');

        if ($perPage<=0){
            return response()->json([
                'success' => false,
                'message' => 'Paginate not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401, [], JSON_UNESCAPED_UNICODE);
        }

        if ($sortField === 'fio_or_name_region'){
            $peopleContentForInterviews = PeopleContent::with(['status', 'regionsAndPeoples'])
                ->where('people_contents.type','Interview')
                ->where('people_contents.user_id', $user->id)
                ->when($search, function ($query, $search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('people_contents.title', 'like', "%{$search}%")
                            ->orWhere('people_contents.source', 'like', "%{$search}%")
                            ->orWhere('people_contents.sys_Comment', 'like', "%{$search}%")
                            ->orWhereHas('regionsAndPeoples', function ($query) use ($search) {
                                $query->where('regions_and_peoples.fio_or_name_region', 'like', "%{$search}%");
                            });
                    });
                })
                ->when($startDate, function ($query, $startDate) {
                    $query->whereDate('people_contents.publication_date', '>=', $startDate);
                })
                ->when($endDate, function ($query, $endDate) {
                    $query->whereDate('people_contents.publication_date', '<=', $endDate);
                })
                ->when(!empty($selectedStatuses), function ($query) use ($selectedStatuses) {
                    $query->whereHas('status', function ($query) use ($selectedStatuses) {
                        $query->whereIn('statuses.status', $selectedStatuses);
                    });
                })
                ->leftJoin('regions_and_peoples', 'people_contents.regions_and_peoples_id', '=', 'regions_and_peoples.id')
                ->groupBy('people_contents.id', 'regions_and_peoples.id')
                ->orderBy('regions_and_peoples.' . $sortField, $sortDirection)
                ->select('people_contents.*')
                ->get();
        }
        else{
            $peopleContentForInterviews = PeopleContent::with(['status', 'regionsAndPeoples'])
                ->where('type','Interview')
                ->where('user_id', $user->id)
                ->when($search, function ($query, $search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('title', 'like', "%{$search}%")
                            ->orWhere('source', 'like', "%{$search}%")
                            ->orWhere('sys_Comment', 'like', "%{$search}%")
                            ->orWhereHas('regionsAndPeoples', function ($query) use ($search) {
                                $query->where('fio_or_name_region', 'like', "%{$search}%");
                            });
                    });
                })
                ->when($startDate, function ($query, $startDate) {
                    $query->whereDate('publication_date', '>=', $startDate);
                })
                ->when($endDate, function ($query, $endDate) {
                    $query->whereDate('publication_date', '<=', $endDate);
                })
                ->when(!empty($selectedStatuses), function ($query) use ($selectedStatuses) {
                    $query->whereHas('status', function ($query) use ($selectedStatuses) {
                        $query->whereIn('status', $selectedStatuses);
                    });
                })
                ->orderBy($sortField, $sortDirection)
                ->get();
        }

        $interviews = $peopleContentForInterviews->map(function ($interview) {
            return MapperInterview::toInterview($interview);
        });

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

    public function allInterviewsBySearchAndFiltersAndStatusesAndSortForPanelCensor(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $selectedStatuses = $request->input('selected_statuses', []);
        $sortField = $request->input('sort_field', 'publication_date');
        $sortDirection = $request->input('sort_direction', 'desc');

        if ($perPage<=0){
            return response()->json([
                'success' => false,
                'message' => 'Paginate not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        if ($sortField === 'fio_or_name_region'){
            $peopleContentForInterviews = PeopleContent::with(['status', 'regionsAndPeoples'])
                ->where('people_contents.type','Interview')
                ->when($search, function ($query, $search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('people_contents.title', 'like', "%{$search}%")
                            ->orWhere('people_contents.source', 'like', "%{$search}%")
                            ->orWhere('people_contents.sys_Comment', 'like', "%{$search}%")
                            ->orWhereHas('regionsAndPeoples', function ($query) use ($search) {
                                $query->where('regions_and_peoples.fio_or_name_region', 'like', "%{$search}%");
                            });
                    });
                })
                ->when($startDate, function ($query, $startDate) {
                    $query->whereDate('people_contents.publication_date', '>=', $startDate);
                })
                ->when($endDate, function ($query, $endDate) {
                    $query->whereDate('people_contents.publication_date', '<=', $endDate);
                })
                ->when(!empty($selectedStatuses), function ($query) use ($selectedStatuses) {
                    $query->whereHas('status', function ($query) use ($selectedStatuses) {
                        $query->whereIn('statuses.status', $selectedStatuses);
                    });
                })
                ->whereHas('status', function ($query) {
                    $query->where('statuses.status', '!=', 'Редактируется');
                })
                ->leftJoin('regions_and_peoples', 'people_contents.regions_and_peoples_id', '=', 'regions_and_peoples.id')
                ->groupBy('people_contents.id', 'regions_and_peoples.id')
                ->orderBy('regions_and_peoples.' . $sortField, $sortDirection)
                ->select('people_contents.*')
                ->get();
        }
        else{
            $peopleContentForInterviews = PeopleContent::with(['status', 'regionsAndPeoples'])
                ->where('type','Interview')
                ->when($search, function ($query, $search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('title', 'like', "%{$search}%")
                            ->orWhere('source', 'like', "%{$search}%")
                            ->orWhere('sys_Comment', 'like', "%{$search}%")
                            ->orWhereHas('regionsAndPeoples', function ($query) use ($search) {
                                $query->where('fio_or_name_region', 'like', "%{$search}%");
                            });
                    });
                })
                ->when($startDate, function ($query, $startDate) {
                    $query->whereDate('publication_date', '>=', $startDate);
                })
                ->when($endDate, function ($query, $endDate) {
                    $query->whereDate('publication_date', '<=', $endDate);
                })
                ->when(!empty($selectedStatuses), function ($query) use ($selectedStatuses) {
                    $query->whereHas('status', function ($query) use ($selectedStatuses) {
                        $query->whereIn('status', $selectedStatuses);
                    });
                })
                ->whereHas('status', function ($query) {
                    $query->where('status', '!=', 'Редактируется');
                })
                ->orderBy($sortField, $sortDirection)
                ->get();
        }

        $interviews = $peopleContentForInterviews->map(function ($interview) {
            return MapperInterview::toInterview($interview);
        });

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

    public function allOpinionsBySearchAndFiltersAndStatusesAndSortForPanel(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $selectedStatuses = $request->input('selected_statuses', []);
        $sortField = $request->input('sort_field', 'publication_date');
        $sortDirection = $request->input('sort_direction', 'desc');

        if ($perPage<=0){
            return response()->json([
                'success' => false,
                'message' => 'Paginate not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401, [], JSON_UNESCAPED_UNICODE);
        }

        if ($sortField === 'fio_or_name_region'){
            $peopleContentForOpinions = PeopleContent::with(['status', 'regionsAndPeoples'])
                ->where('people_contents.type','Opinion')
                ->where('people_contents.user_id', $user->id)
                ->when($search, function ($query, $search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('people_contents.title', 'like', "%{$search}%")
                            ->orWhere('people_contents.sys_Comment', 'like', "%{$search}%")
                            ->orWhereHas('regionsAndPeoples', function ($query) use ($search) {
                                $query->where('regions_and_peoples.fio_or_name_region', 'like', "%{$search}%");
                            });
                    });
                })
                ->when($startDate, function ($query, $startDate) {
                    $query->whereDate('people_contents.publication_date', '>=', $startDate);
                })
                ->when($endDate, function ($query, $endDate) {
                    $query->whereDate('people_contents.publication_date', '<=', $endDate);
                })
                ->when(!empty($selectedStatuses), function ($query) use ($selectedStatuses) {
                    $query->whereHas('status', function ($query) use ($selectedStatuses) {
                        $query->whereIn('statuses.status', $selectedStatuses);
                    });
                })
                ->leftJoin('regions_and_peoples', 'people_contents.regions_and_peoples_id', '=', 'regions_and_peoples.id')
                ->groupBy('people_contents.id', 'regions_and_peoples.id')
                ->orderBy('regions_and_peoples.' . $sortField, $sortDirection)
                ->select('people_contents.*')
                ->get();
        }
        else{
            $peopleContentForOpinions = PeopleContent::with(['status', 'regionsAndPeoples'])
                ->where('type','Opinion')
                ->where('user_id', $user->id)
                ->when($search, function ($query, $search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('title', 'like', "%{$search}%")
                            ->orWhere('sys_Comment', 'like', "%{$search}%")
                            ->orWhereHas('regionsAndPeoples', function ($query) use ($search) {
                                $query->where('fio_or_name_region', 'like', "%{$search}%");
                            });
                    });
                })
                ->when($startDate, function ($query, $startDate) {
                    $query->whereDate('publication_date', '>=', $startDate);
                })
                ->when($endDate, function ($query, $endDate) {
                    $query->whereDate('publication_date', '<=', $endDate);
                })
                ->when(!empty($selectedStatuses), function ($query) use ($selectedStatuses) {
                    $query->whereHas('status', function ($query) use ($selectedStatuses) {
                        $query->whereIn('status', $selectedStatuses);
                    });
                })
                ->orderBy($sortField, $sortDirection)
                ->get();
        }

        $opinions = $peopleContentForOpinions->map(function ($opinion) {
            return MapperOpinion::toOpinion($opinion);
        });

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

    public function allOpinionsBySearchAndFiltersAndStatusesAndSortForPanelCensor(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $selectedStatuses = $request->input('selected_statuses', []);
        $sortField = $request->input('sort_field', 'publication_date');
        $sortDirection = $request->input('sort_direction', 'desc');

        if ($perPage<=0){
            return response()->json([
                'success' => false,
                'message' => 'Paginate not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        if ($sortField === 'fio_or_name_region'){
            $peopleContentForOpinions = PeopleContent::with(['status', 'regionsAndPeoples'])
                ->where('people_contents.type','Opinion')
                ->when($search, function ($query, $search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('people_contents.title', 'like', "%{$search}%")
                            ->orWhere('people_contents.sys_Comment', 'like', "%{$search}%")
                            ->orWhereHas('regionsAndPeoples', function ($query) use ($search) {
                                $query->where('regions_and_peoples.fio_or_name_region', 'like', "%{$search}%");
                            });
                    });
                })
                ->when($startDate, function ($query, $startDate) {
                    $query->whereDate('people_contents.publication_date', '>=', $startDate);
                })
                ->when($endDate, function ($query, $endDate) {
                    $query->whereDate('people_contents.publication_date', '<=', $endDate);
                })
                ->when(!empty($selectedStatuses), function ($query) use ($selectedStatuses) {
                    $query->whereHas('status', function ($query) use ($selectedStatuses) {
                        $query->whereIn('statuses.status', $selectedStatuses);
                    });
                })
                ->whereHas('status', function ($query) {
                    $query->where('statuses.status', '!=', 'Редактируется');
                })
                ->leftJoin('regions_and_peoples', 'people_contents.regions_and_peoples_id', '=', 'regions_and_peoples.id')
                ->groupBy('people_contents.id', 'regions_and_peoples.id')
                ->orderBy('regions_and_peoples.' . $sortField, $sortDirection)
                ->select('people_contents.*')
                ->get();
        }
        else{
            $peopleContentForOpinions = PeopleContent::with(['status', 'regionsAndPeoples'])
                ->where('type','Opinion')
                ->when($search, function ($query, $search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('title', 'like', "%{$search}%")
                            ->orWhere('sys_Comment', 'like', "%{$search}%")
                            ->orWhereHas('regionsAndPeoples', function ($query) use ($search) {
                                $query->where('fio_or_name_region', 'like', "%{$search}%");
                            });
                    });
                })
                ->when($startDate, function ($query, $startDate) {
                    $query->whereDate('publication_date', '>=', $startDate);
                })
                ->when($endDate, function ($query, $endDate) {
                    $query->whereDate('publication_date', '<=', $endDate);
                })
                ->when(!empty($selectedStatuses), function ($query) use ($selectedStatuses) {
                    $query->whereHas('status', function ($query) use ($selectedStatuses) {
                        $query->whereIn('status', $selectedStatuses);
                    });
                })
                ->whereHas('status', function ($query) {
                    $query->where('status', '!=', 'Редактируется');
                })
                ->orderBy($sortField, $sortDirection)
                ->get();
        }

        $opinions = $peopleContentForOpinions->map(function ($opinion) {
            return MapperOpinion::toOpinion($opinion);
        });

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

    public function allPointViewsBySearchAndFiltersAndStatusesAndSortForPanel(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $selectedStatuses = $request->input('selected_statuses', []);
        $sortField = $request->input('sort_field', 'updated_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        if ($perPage<=0){
            return response()->json([
                'success' => false,
                'message' => 'Paginate not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401, [], JSON_UNESCAPED_UNICODE);
        }

        if ($sortField === 'fio_or_name_region'){
            $peopleContentForPointViews = PeopleContent::with(['status', 'regionsAndPeoples'])
                ->where('people_contents.type','PointView')
                ->where('people_contents.user_id', $user->id)
                ->when($search, function ($query, $search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('people_contents.title', 'like', "%{$search}%")
                            ->orWhere('people_contents.sys_Comment', 'like', "%{$search}%")
                            ->orWhereHas('regionsAndPeoples', function ($query) use ($search) {
                                $query->where('regions_and_peoples.fio_or_name_region', 'like', "%{$search}%");
                            });
                    });
                })
                ->when($startDate, function ($query, $startDate) {
                    $query->whereDate('people_contents.updated_at', '>=', $startDate);
                })
                ->when($endDate, function ($query, $endDate) {
                    $query->whereDate('people_contents.updated_at', '<=', $endDate);
                })
                ->when(!empty($selectedStatuses), function ($query) use ($selectedStatuses) {
                    $query->whereHas('status', function ($query) use ($selectedStatuses) {
                        $query->whereIn('statuses.status', $selectedStatuses);
                    });
                })
                ->leftJoin('regions_and_peoples', 'people_contents.regions_and_peoples_id', '=', 'regions_and_peoples.id')
                ->groupBy('people_contents.id', 'regions_and_peoples.id')
                ->orderBy('regions_and_peoples.' . $sortField, $sortDirection)
                ->select('people_contents.*')
                ->get();
        }
        else{
            $peopleContentForPointViews = PeopleContent::with(['status', 'regionsAndPeoples'])
                ->where('type','PointView')
                ->where('user_id', $user->id)
                ->when($search, function ($query, $search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('title', 'like', "%{$search}%")
                            ->orWhere('sys_Comment', 'like', "%{$search}%")
                            ->orWhereHas('regionsAndPeoples', function ($query) use ($search) {
                                $query->where('fio_or_name_region', 'like', "%{$search}%");
                            });
                    });
                })
                ->when($startDate, function ($query, $startDate) {
                    $query->whereDate('updated_at', '>=', $startDate);
                })
                ->when($endDate, function ($query, $endDate) {
                    $query->whereDate('updated_at', '<=', $endDate);
                })
                ->when(!empty($selectedStatuses), function ($query) use ($selectedStatuses) {
                    $query->whereHas('status', function ($query) use ($selectedStatuses) {
                        $query->whereIn('status', $selectedStatuses);
                    });
                })
                ->orderBy($sortField, $sortDirection)
                ->get();
        }

        $pointViews = $peopleContentForPointViews->map(function ($pointView) {
            return MapperPointView::toPointView($pointView);
        });

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

    public function createInterview(Request $request){
        $rules = [
            'path_to_image' => 'required|string',
            'title' => 'required|string',
            'content' => 'required|string',
            'source' => 'required|string',
            'publication_date' => 'required|date_format:Y-m-d',
            'sys_Comment' => 'nullable|string',
            'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
            'user_id' => 'nullable|integer|exists:users,id',
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

        $user = null;

        if ($request->input('user_id') == null || $request->input('user_id') == 0){
            $user = Auth::guard('sanctum')->user();
        }
        else{
            $user = User::where('id', $request->input('user_id'))->first();
        }

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
            'sys_Comment' => $request->input('sys_Comment'),
            'regions_and_peoples_id' => $request->input('regions_and_peoples_id'),
            'user_id' => $user->id,
            'status_id' => $status->id,
        ]);

        $peopleContentNew = PeopleContent::find($peopleContent->id);

        $interview = MapperInterview::toInterview($peopleContentNew);

        return response()->json([
            'success' => true,
            'interview' => $interview,
            'message' => 'Create successful'
        ], 201, [], JSON_UNESCAPED_UNICODE);
    }

    public function createInterviewForCheck(Request $request){
        $rules = [
            'path_to_image' => 'required|string',
            'title' => 'required|string',
            'content' => 'required|string',
            'source' => 'required|string',
            'publication_date' => 'required|date_format:Y-m-d',
            'sys_Comment' => 'nullable|string',
            'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
            'user_id' => 'nullable|integer|exists:users,id',
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

        $user = null;

        if ($request->input('user_id') == null || $request->input('user_id') == 0){
            $user = Auth::guard('sanctum')->user();
        }
        else{
            $user = User::where('id', $request->input('user_id'))->first();
        }

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401, [], JSON_UNESCAPED_UNICODE);
        }

        $status = Status::where('status', 'Ожидает подтверждения')->first();

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
            'sys_Comment' => $request->input('sys_Comment'),
            'regions_and_peoples_id' => $request->input('regions_and_peoples_id'),
            'user_id' => $user->id,
            'status_id' => $status->id,
        ]);

        $peopleContentNew = PeopleContent::find($peopleContent->id);

        $interview = MapperInterview::toInterview($peopleContentNew);

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
            'sys_Comment' => 'nullable|string',
            'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
            'user_id' => 'nullable|integer|exists:users,id',
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

        $user = null;

        if ($request->input('user_id') == null || $request->input('user_id') == 0){
            $user = Auth::guard('sanctum')->user();
        }
        else{
            $user = User::where('id', $request->input('user_id'))->first();
        }

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
            'sys_Comment' => $request->input('sys_Comment'),
            'regions_and_peoples_id' => $request->input('regions_and_peoples_id'),
            'user_id' => $user->id,
            'status_id' => $status->id,
        ]);

        $peopleContentNew = PeopleContent::find($peopleContent->id);

        $opinion = MapperOpinion::toOpinion($peopleContentNew);

        return response()->json([
            'success' => true,
            'opinion' => $opinion,
            'message' => 'Create successful'
        ], 201, [], JSON_UNESCAPED_UNICODE);
    }

    public function createOpinionForCheck(Request $request){
        $rules = [
            'path_to_image' => 'required|string',
            'title' => 'required|string',
            'content' => 'required|string',
            'publication_date' => 'required|date_format:Y-m-d',
            'sys_Comment' => 'nullable|string',
            'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
            'user_id' => 'nullable|integer|exists:users,id',
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

        $user = null;

        if ($request->input('user_id') == null || $request->input('user_id') == 0){
            $user = Auth::guard('sanctum')->user();
        }
        else{
            $user = User::where('id', $request->input('user_id'))->first();
        }

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401, [], JSON_UNESCAPED_UNICODE);
        }

        $status = Status::where('status', 'Ожидает подтверждения')->first();

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
            'sys_Comment' => $request->input('sys_Comment'),
            'regions_and_peoples_id' => $request->input('regions_and_peoples_id'),
            'user_id' => $user->id,
            'status_id' => $status->id,
        ]);

        $peopleContentNew = PeopleContent::find($peopleContent->id);

        $opinion = MapperOpinion::toOpinion($peopleContentNew);

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
            'sys_Comment' => 'nullable|string',
            'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
            'user_id' => 'nullable|integer|exists:users,id',
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

        $user = null;

        if ($request->input('user_id') == null || $request->input('user_id') == 0){
            $user = Auth::guard('sanctum')->user();
        }
        else{
            $user = User::where('id', $request->input('user_id'))->first();
        }

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
            'sys_Comment' => $request->input('sys_Comment'),
            'regions_and_peoples_id' => $request->input('regions_and_peoples_id'),
            'user_id' => $user->id,
            'status_id' => $status->id,
        ]);

        $peopleContentNew = PeopleContent::find($peopleContent->id);

        $pointView = MapperPointView::toPointView($peopleContentNew);

        return response()->json([
            'success' => true,
            'pointView' => $pointView,
            'message' => 'Create successful'
        ], 201, [], JSON_UNESCAPED_UNICODE);
    }

    public function createPointViewForCheck(Request $request){
        $rules = [
            'title' => 'required|string',
            'content' => 'required|string',
            'sys_Comment' => 'nullable|string',
            'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
            'user_id' => 'nullable|integer|exists:users,id',
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

        $user = null;

        if ($request->input('user_id') == null || $request->input('user_id') == 0){
            $user = Auth::guard('sanctum')->user();
        }
        else{
            $user = User::where('id', $request->input('user_id'))->first();
        }

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401, [], JSON_UNESCAPED_UNICODE);
        }

        $status = Status::where('status', 'Ожидает подтверждения')->first();

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
            'sys_Comment' => $request->input('sys_Comment'),
            'regions_and_peoples_id' => $request->input('regions_and_peoples_id'),
            'user_id' => $user->id,
            'status_id' => $status->id,
        ]);

        $peopleContentNew = PeopleContent::find($peopleContent->id);

        $pointView = MapperPointView::toPointView($peopleContentNew);

        return response()->json([
            'success' => true,
            'pointView' => $pointView,
            'message' => 'Create successful'
        ], 201, [], JSON_UNESCAPED_UNICODE);
    }

    public function deleteInterviewMark(Request $request){
        $rules = [
            'interview_ids' => 'required|array',
            'interview_ids.*' => 'integer|exists:people_contents,id',
        ];

        try {
            $request->validate($rules);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed'
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }

        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401, [], JSON_UNESCAPED_UNICODE);
        }

        $peopleContentItems = PeopleContent::whereIn('id', $request->input('interview_ids'))->where('type', 'Interview')->where('user_id', $user->id)->get();

        foreach ($peopleContentItems as $item) {
            $item->delete_mark = !$item->delete_mark;
            $item->save();
        }

        $peopleContentItems = PeopleContent::with('status')
            ->whereIn('id', $request->input('interview_ids'))->where('type', 'Interview')->where('user_id', $user->id)
            ->get();

        $interviewItems = $peopleContentItems->map(function($item) {
            return MapperInterview::toInterview($item);
        });

        return response()->json([
            'success' => true,
            'message' => 'Interview delete_mark updated successfully',
            'interview' => $interviewItems,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function deleteOpinionMark(Request $request){
        $rules = [
            'opinion_ids' => 'required|array',
            'opinion_ids.*' => 'integer|exists:people_contents,id',
        ];

        try {
            $request->validate($rules);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed'
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }

        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401, [], JSON_UNESCAPED_UNICODE);
        }

        $peopleContentItems = PeopleContent::whereIn('id', $request->input('opinion_ids'))->where('type', 'Opinion')->where('user_id', $user->id)->get();

        foreach ($peopleContentItems as $item) {
            $item->delete_mark = !$item->delete_mark;
            $item->save();
        }

        $peopleContentItems = PeopleContent::with('status')
            ->whereIn('id', $request->input('opinion_ids'))->where('type', 'Opinion')->where('user_id', $user->id)
            ->get();

        $opinionItems = $peopleContentItems->map(function($item) {
            return MapperOpinion::toOpinion($item);
        });

        return response()->json([
            'success' => true,
            'message' => 'Opinion delete_mark updated successfully',
            'opinion' => $opinionItems,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function deletePointViewMark(Request $request){
        $rules = [
            'point_view_ids' => 'required|array',
            'point_view_ids.*' => 'integer|exists:people_contents,id',
        ];

        try {
            $request->validate($rules);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed'
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }

        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401, [], JSON_UNESCAPED_UNICODE);
        }

        $peopleContentItems = PeopleContent::whereIn('id', $request->input('point_view_ids'))->where('type', 'PointView')->where('user_id', $user->id)->get();

        foreach ($peopleContentItems as $item) {
            $item->delete_mark = !$item->delete_mark;
            $item->save();
        }

        $peopleContentItems = PeopleContent::with('status')
            ->whereIn('id', $request->input('point_view_ids'))->where('type', 'PointView')->where('user_id', $user->id)
            ->get();

        $pointViewItems = $peopleContentItems->map(function($item) {
            return MapperPointView::toPointView($item);
        });

        return response()->json([
            'success' => true,
            'message' => 'PointView delete_mark updated successfully',
            'pointView' => $pointViewItems,
        ], 200, [], JSON_UNESCAPED_UNICODE);
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

        if (!$peopleContent->delete_mark){
            return response()->json([
                'success' => false,
                'message' => 'Cannot be deleted'
            ], 403, [], JSON_UNESCAPED_UNICODE);
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

        if (!$peopleContent->delete_mark){
            return response()->json([
                'success' => false,
                'message' => 'Cannot be deleted'
            ], 403, [], JSON_UNESCAPED_UNICODE);
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

        if (!$peopleContent->delete_mark){
            return response()->json([
                'success' => false,
                'message' => 'Cannot be deleted'
            ], 403, [], JSON_UNESCAPED_UNICODE);
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
                'sys_Comment' => 'nullable|string',
                'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
                'user_id' => 'nullable|integer|exists:users,id',
            ];

            try {
                $request->validate($rules);
            } catch (ValidationException $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed'
                ], 422, [], JSON_UNESCAPED_UNICODE);
            }

            if ($request->input('user_id') != null && $request->input('user_id') != 0){
                $user = User::where('id', $request->input('user_id'))->first();
            }

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not authenticated'
                ], 401, [], JSON_UNESCAPED_UNICODE);
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
            $peopleContent->sys_Comment = $request->input('sys_Comment');
            $peopleContent->regions_and_peoples_id = $request->input('regions_and_peoples_id');
            $peopleContent->user_id = $user->id;

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

    public function editInterviewCensor($id, Request $request){
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
                'message' => 'Status not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $rules = [
            'path_to_image' => 'required|string',
            'title' => 'required|string',
            'content' => 'required|string',
            'source' => 'required|string',
            'publication_date' => 'required|date_format:Y-m-d',
            'sys_Comment' => 'nullable|string',
            'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
            'user_id' => 'nullable|integer|exists:users,id',
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

        if ($user->hasRole('editor') && !$user->hasRole('admin') && !$user->hasRole('super_admin') && $user->id == $news->user_id){
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

        if ($request->input('user_id') != null && $request->input('user_id') != 0){
            $user = User::where('id', $request->input('user_id'))->first();
        }

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401, [], JSON_UNESCAPED_UNICODE);
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
        $peopleContent->sys_Comment = $request->input('sys_Comment');
        $peopleContent->regions_and_peoples_id = $request->input('regions_and_peoples_id');
        $peopleContent->user_id = $user->id;
        $peopleContent->status_id = $status->id;

        $peopleContent->save();

        $interview = MapperInterview::toInterview($peopleContent);

        if ($is_status){
            return response()->json([
                'success' => true,
                'message' => 'Interview updated successfully',
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

    public function editInterviewForCheck($id, Request $request){
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
                'sys_Comment' => 'nullable|string',
                'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
                'user_id' => 'nullable|integer|exists:users,id',
            ];

            try {
                $request->validate($rules);
            } catch (ValidationException $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed'
                ], 422, [], JSON_UNESCAPED_UNICODE);
            }

            if ($request->input('user_id') != null && $request->input('user_id') != 0){
                $user = User::where('id', $request->input('user_id'))->first();
            }

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not authenticated'
                ], 401, [], JSON_UNESCAPED_UNICODE);
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

            $status = Status::where('status', 'Ожидает подтверждения')->first();

            if (!$status) {
                return response()->json([
                    'success' => false,
                    'message' => 'Status not found'
                ], 404, [], JSON_UNESCAPED_UNICODE);
            }

            $peopleContent->path_to_image = $request->input('path_to_image');
            $peopleContent->title = $request->input('title');
            $peopleContent->content = $request->input('content');
            $peopleContent->source = $request->input('source');
            $peopleContent->publication_date = $request->input('publication_date');
            $peopleContent->sys_Comment = $request->input('sys_Comment');
            $peopleContent->regions_and_peoples_id = $request->input('regions_and_peoples_id');
            $peopleContent->user_id = $user->id;
            $peopleContent->status_id = $status->id;

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

    public function editInterviewForCheckCensor($id, Request $request){
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

        $rules = [
            'path_to_image' => 'required|string',
            'title' => 'required|string',
            'content' => 'required|string',
            'source' => 'required|string',
            'publication_date' => 'required|date_format:Y-m-d',
            'sys_Comment' => 'nullable|string',
            'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
            'user_id' => 'nullable|integer|exists:users,id',
        ];

        try {
            $request->validate($rules);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed'
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }

        if ($request->input('user_id') != null && $request->input('user_id') != 0){
            $user = User::where('id', $request->input('user_id'))->first();
        }

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401, [], JSON_UNESCAPED_UNICODE);
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

        $status = Status::where('status', 'Ожидает подтверждения')->first();

        if (!$status) {
            return response()->json([
                'success' => false,
                'message' => 'Status not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $peopleContent->path_to_image = $request->input('path_to_image');
        $peopleContent->title = $request->input('title');
        $peopleContent->content = $request->input('content');
        $peopleContent->source = $request->input('source');
        $peopleContent->publication_date = $request->input('publication_date');
        $peopleContent->sys_Comment = $request->input('sys_Comment');
        $peopleContent->regions_and_peoples_id = $request->input('regions_and_peoples_id');
        $peopleContent->user_id = $user->id;
        $peopleContent->status_id = $status->id;

        $peopleContent->save();

        $interview = MapperInterview::toInterview($peopleContent);

        return response()->json([
            'success' => true,
            'message' => 'Interview updated successfully',
            'interview' => $interview,
        ], 200, [], JSON_UNESCAPED_UNICODE);
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
                'sys_Comment' => 'nullable|string',
                'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
                'user_id' => 'nullable|integer|exists:users,id',
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

            if ($request->input('user_id') != null && $request->input('user_id') != 0){
                $user = User::where('id', $request->input('user_id'))->first();
            }

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not authenticated'
                ], 401, [], JSON_UNESCAPED_UNICODE);
            }

            $peopleContent->path_to_image = $request->input('path_to_image');
            $peopleContent->title = $request->input('title');
            $peopleContent->content = $request->input('content');
            $peopleContent->publication_date = $request->input('publication_date');
            $peopleContent->sys_Comment = $request->input('sys_Comment');
            $peopleContent->regions_and_peoples_id = $request->input('regions_and_peoples_id');
            $peopleContent->user_id = $user->id;

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

    public function editOpinionCensor($id, Request $request){
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
                'message' => 'Status not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $rules = [
            'path_to_image' => 'required|string',
            'title' => 'required|string',
            'content' => 'required|string',
            'publication_date' => 'required|date_format:Y-m-d',
            'sys_Comment' => 'nullable|string',
            'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
            'user_id' => 'nullable|integer|exists:users,id',
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

        $status = Status::where('status', $request->input('status'))->first();

        if (!$status) {
            return response()->json([
                'success' => false,
                'message' => 'Status not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $is_status = false;

        if ($user->hasRole('editor') && !$user->hasRole('admin') && !$user->hasRole('super_admin') && $user->id == $news->user_id){
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

        if ($request->input('user_id') != null && $request->input('user_id') != 0){
            $user = User::where('id', $request->input('user_id'))->first();
        }

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401, [], JSON_UNESCAPED_UNICODE);
        }

        $peopleContent->path_to_image = $request->input('path_to_image');
        $peopleContent->title = $request->input('title');
        $peopleContent->content = $request->input('content');
        $peopleContent->publication_date = $request->input('publication_date');
        $peopleContent->sys_Comment = $request->input('sys_Comment');
        $peopleContent->regions_and_peoples_id = $request->input('regions_and_peoples_id');
        $peopleContent->user_id = $user->id;
        $peopleContent->status_id = $status->id;

        $peopleContent->save();

        $opinion = MapperOpinion::toOpinion($peopleContent);

        if ($is_status){
            return response()->json([
                'success' => true,
                'message' => 'Opinion updated successfully',
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

    public function editOpinionForCheck($id, Request $request){
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
                'sys_Comment' => 'nullable|string',
                'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
                'user_id' => 'nullable|integer|exists:users,id',
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

            if ($request->input('user_id') != null && $request->input('user_id') != 0){
                $user = User::where('id', $request->input('user_id'))->first();
            }

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not authenticated'
                ], 401, [], JSON_UNESCAPED_UNICODE);
            }

            $status = Status::where('status', 'Ожидает подтверждения')->first();

            if (!$status) {
                return response()->json([
                    'success' => false,
                    'message' => 'Status not found'
                ], 404, [], JSON_UNESCAPED_UNICODE);
            }

            $peopleContent->path_to_image = $request->input('path_to_image');
            $peopleContent->title = $request->input('title');
            $peopleContent->content = $request->input('content');
            $peopleContent->publication_date = $request->input('publication_date');
            $peopleContent->sys_Comment = $request->input('sys_Comment');
            $peopleContent->regions_and_peoples_id = $request->input('regions_and_peoples_id');
            $peopleContent->user_id = $user->id;
            $peopleContent->status_id = $status->id;

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

    public function editOpinionForCheckCensor($id, Request $request){
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

        $rules = [
            'path_to_image' => 'required|string',
            'title' => 'required|string',
            'content' => 'required|string',
            'publication_date' => 'required|date_format:Y-m-d',
            'sys_Comment' => 'nullable|string',
            'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
            'user_id' => 'nullable|integer|exists:users,id',
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

        if ($request->input('user_id') != null && $request->input('user_id') != 0){
            $user = User::where('id', $request->input('user_id'))->first();
        }

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401, [], JSON_UNESCAPED_UNICODE);
        }

        $status = Status::where('status', 'Ожидает подтверждения')->first();

        if (!$status) {
            return response()->json([
                'success' => false,
                'message' => 'Status not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $peopleContent->path_to_image = $request->input('path_to_image');
        $peopleContent->title = $request->input('title');
        $peopleContent->content = $request->input('content');
        $peopleContent->publication_date = $request->input('publication_date');
        $peopleContent->sys_Comment = $request->input('sys_Comment');
        $peopleContent->regions_and_peoples_id = $request->input('regions_and_peoples_id');
        $peopleContent->user_id = $user->id;
        $peopleContent->status_id = $status->id;

        $peopleContent->save();

        $opinion = MapperOpinion::toOpinion($peopleContent);

        return response()->json([
            'success' => true,
            'message' => 'Opinion updated successfully',
            'opinion' => $opinion,
        ], 200, [], JSON_UNESCAPED_UNICODE);
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
                'sys_Comment' => 'nullable|string',
                'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
                'user_id' => 'nullable|integer|exists:users,id',
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

            if ($request->input('user_id') != null && $request->input('user_id') != 0){
                $user = User::where('id', $request->input('user_id'))->first();
            }

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not authenticated'
                ], 401, [], JSON_UNESCAPED_UNICODE);
            }

            $peopleContent->title = $request->input('title');
            $peopleContent->content = $request->input('content');
            $peopleContent->sys_Comment = $request->input('sys_Comment');
            $peopleContent->regions_and_peoples_id = $request->input('regions_and_peoples_id');
            $peopleContent->user_id = $user->id;

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

    public function editPointViewForCheck($id, Request $request){
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
                'sys_Comment' => 'nullable|string',
                'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
                'user_id' => 'nullable|integer|exists:users,id',
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

            if ($request->input('user_id') != null && $request->input('user_id') != 0){
                $user = User::where('id', $request->input('user_id'))->first();
            }

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not authenticated'
                ], 401, [], JSON_UNESCAPED_UNICODE);
            }

            $status = Status::where('status', 'Ожидает подтверждения')->first();

            if (!$status) {
                return response()->json([
                    'success' => false,
                    'message' => 'Status not found'
                ], 404, [], JSON_UNESCAPED_UNICODE);
            }

            $peopleContent->title = $request->input('title');
            $peopleContent->content = $request->input('content');
            $peopleContent->sys_Comment = $request->input('sys_Comment');
            $peopleContent->regions_and_peoples_id = $request->input('regions_and_peoples_id');
            $peopleContent->user_id = $user->id;
            $peopleContent->status_id = $status->id;

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
//                    'Ожидает публикации' => ['Ожидает подтверждения'],
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
