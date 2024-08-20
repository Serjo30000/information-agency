<?php

namespace App\Http\Controllers;

use App\Http\Services\Filters\FilterInterview;
use App\Http\Services\Filters\FilterOpinion;
use App\Http\Services\Filters\FilterPeople;
use App\Http\Services\Filters\FilterRegion;
use App\Models\GrandNews;
use Illuminate\Http\Request;

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
        })->get();

        $topPriorityNews = $grandNews->sortByDesc('priority')->all();

        return response()->json($topPriorityNews, 200, [], JSON_UNESCAPED_UNICODE);
    }
}
