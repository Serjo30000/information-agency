<?php

namespace App\Http\Controllers;

use App\Http\Services\Filters\FilterInterview;
use App\Http\Services\Filters\FilterOpinion;
use App\Http\Services\Filters\FilterPeople;
use App\Http\Services\Filters\FilterRegion;
use App\Http\Services\Mappers\MapperInterview;
use App\Http\Services\Mappers\MapperOpinion;
use App\Http\Services\Mappers\MapperPeople;
use App\Http\Services\Mappers\MapperPointView;
use App\Http\Services\Mappers\MapperRegion;
use App\Models\GrandNews;
use App\Models\News;
use App\Models\PeopleContent;
use App\Models\RegionsAndPeoples;
use App\Models\User;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReportController extends Controller
{
    public function allRandomSections()
    {
        $randomIndexOpinion = 0;
        $randomIndexPeople = 0;
        $randomIndexInterview = 0;
        $randomIndexRegion = 0;

        $opinions = FilterOpinion::allOpinionsByFilter();
        $peoples = FilterPeople::allPeoplesByFilter();
        $interviews = FilterInterview::allInterviewsByFilter();
        $regions = FilterRegion::allRegionsByFilter();

        $opinionsCount = count($opinions);
        $peoplesCount = count($peoples);
        $interviewsCount = count($interviews);
        $regionsCount = count($regions);

        if ($opinionsCount>0){
            $randomIndexOpinion = rand(0, $opinionsCount - 1);
        }

        if ($peoplesCount>0){
            $randomIndexPeople = rand(0, $peoplesCount - 1);
        }

        if ($interviewsCount>0){
            $randomIndexInterview = rand(0, $interviewsCount - 1);
        }

        if ($regionsCount>0){
            $randomIndexRegion = rand(0, $regionsCount - 1);
        }

        $opinion = $opinions->get($randomIndexOpinion);
        $people = $peoples->get($randomIndexPeople);
        $interview = $interviews->get($randomIndexInterview);
        $region = $regions->get($randomIndexRegion);

        if (is_null($opinion) || is_null($people) || is_null($interview) || is_null($region)) {
            return response()->json(['message' => 'Not found'], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $result = [
            'opinion' => $opinion,
            'people' => $people,
            'interview' => $interview,
            'region' => $region,
        ];

        return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function allPriorityGrandNews(){
        $grandNews = GrandNews::whereHas('news', function ($query) {
            $query->whereHas('status', function ($statusQuery) {
                $statusQuery->where('status', 'Опубликовано');
            });
        })->with('news')->get();

        $topPriorityNews = $grandNews->sortByDesc('priority')->all();

        return response()->json($topPriorityNews, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function allListForDelete(Request $request){
        $search = $request->input('search');

        $news = News::with(['status', 'regionsAndPeoples'])->when($search, function ($query, $search) {
            $query->where('title', 'like', '%' . $search . '%');
        })->where('delete_mark',1)->get();

        $regionsAndPeoplesForRegions = RegionsAndPeoples::where('type','Region')->when($search, function ($query, $search) {
            $query->where('fio_or_name_region', 'like', '%' . $search . '%');
        })->where('delete_mark',1)->get();

        $regions = $regionsAndPeoplesForRegions->map(function ($region) {
            return MapperRegion::toRegion($region);
        });

        $regionsAndPeoplesForPeoples = RegionsAndPeoples::where('type','People')->when($search, function ($query, $search) {
            $query->where('fio_or_name_region', 'like', '%' . $search . '%');
        })->where('delete_mark',1)->get();

        $peoples = $regionsAndPeoplesForPeoples->map(function ($people) {
            return MapperPeople::toPeople($people);
        });

        $peopleContentForInterviews = PeopleContent::where('type','Interview')->when($search, function ($query, $search) {
            $query->where('title', 'like', '%' . $search . '%');
        })->where('delete_mark',1)->get();

        $interviews = $peopleContentForInterviews->map(function ($interview) {
            return MapperInterview::toInterview($interview);
        });

        $peopleContentForPointViews = PeopleContent::where('type','PointView')->when($search, function ($query, $search) {
            $query->where('title', 'like', '%' . $search . '%');
        })->where('delete_mark',1)->get();

        $pointViews = $peopleContentForPointViews->map(function ($pointView) {
            return MapperPointView::toPointView($pointView);
        });

        $peopleContentForOpinions = PeopleContent::where('type','Opinion')->when($search, function ($query, $search) {
            $query->where('title', 'like', '%' . $search . '%');
        })->where('delete_mark',1)->get();

        $opinions = $peopleContentForOpinions->map(function ($opinion) {
            return MapperOpinion::toOpinion($opinion);
        });

        $users = User::when($search, function ($query, $search) {
            $query->where(function($q) use ($search) {
                $q->where('login', 'like', '%' . $search . '%')
                    ->orWhere('fio', 'like', '%' . $search . '%');
            });
        })->where('delete_mark', 1)->get();

        $videos = Video::with('status')->when($search, function ($query, $search) {
            $query->where('title', 'like', '%' . $search . '%');
        })->where('delete_mark',1)->get();

        $result = [
            'Новости' => $news,
            'Регионы' => $regions,
            'Люди' => $peoples,
            'Интервью' => $interviews,
            'Точки зрения' => $pointViews,
            'Мнения' => $opinions,
            'Пользователи' => $users,
            'Видео' => $videos,
        ];

        return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function deleteAllForPanel(Request $request){
        $rules = [
            'news_ids' => 'nullable|array',
            'news_ids.*' => 'integer|exists:news,id',
            'regions_ids' => 'nullable|array',
            'regions_ids.*' => 'integer|exists:regions_and_peoples,id',
            'people_ids' => 'nullable|array',
            'people_ids.*' => 'integer|exists:regions_and_peoples,id',
            'interview_ids' => 'nullable|array',
            'interview_ids.*' => 'integer|exists:people_contents,id',
            'point_of_view_ids' => 'nullable|array',
            'point_of_view_ids.*' => 'integer|exists:people_contents,id',
            'opinion_ids' => 'nullable|array',
            'opinion_ids.*' => 'integer|exists:people_contents,id',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'integer|exists:users,id',
            'video_ids' => 'nullable|array',
            'video_ids.*' => 'integer|exists:videos,id',
        ];

        try {
            $request->validate($rules);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }

        if ($request->input('news_ids')!=null){
            News::whereIn('id', $request->input('news_ids'))
                ->where('delete_mark', 1)
                ->delete();
        }

        if ($request->input('regions_ids')!=null){
            RegionsAndPeoples::whereIn('id', $request->input('regions_ids'))
                ->where('type','Region')
                ->where('delete_mark', 1)
                ->delete();
        }

        if ($request->input('people_ids')!=null){
            RegionsAndPeoples::whereIn('id', $request->input('people_ids'))
                ->where('type','People')
                ->where('delete_mark', 1)
                ->delete();
        }

        if ($request->input('interview_ids')!=null){
            PeopleContent::whereIn('id', $request->input('interview_ids'))
                ->where('type','Interview')
                ->where('delete_mark', 1)
                ->delete();
        }

        if ($request->input('point_of_view_ids')!=null){
            PeopleContent::whereIn('id', $request->input('point_of_view_ids'))
                ->where('type','PointView')
                ->where('delete_mark', 1)
                ->delete();
        }

        if ($request->input('opinion_ids')!=null){
            PeopleContent::whereIn('id', $request->input('opinion_ids'))
                ->where('type','Opinion')
                ->where('delete_mark', 1)
                ->delete();
        }

        if ($request->input('user_ids')!=null){
            User::whereIn('id', $request->input('user_ids'))
                ->where('delete_mark', 1)
                ->delete();
        }

        if ($request->input('video_ids')!=null){
            Video::whereIn('id', $request->input('video_ids'))
                ->where('delete_mark', 1)
                ->delete();
        }

        return response()->json(['message' => 'Records deleted successfully'], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
