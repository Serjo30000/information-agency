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

        $opinionsCount = count(FilterOpinion::allOpinionsByFilter());
        $peoplesCount = count(FilterPeople::allPeoplesByFilter());
        $interviewsCount = count(FilterInterview::allInterviewsByFilter());
        $regionsCount = count(FilterRegion::allRegionsByFilter());

        if ($opinionsCount>0){
            $randomIndexOpinion = rand(1, $opinionsCount);
        }

        if ($peoplesCount>0){
            $randomIndexPeople = rand(1, $randomIndexPeople);
        }

        if ($interviewsCount>0){
            $randomIndexInterview = rand(1, $randomIndexInterview);
        }

        if ($regionsCount>0){
            $randomIndexRegion = rand(1, $randomIndexRegion);
        }

        $opinion = FilterOpinion::findOpinionByFilter($randomIndexOpinion);
        $people = FilterPeople::findPeopleByFilter($randomIndexPeople);
        $interview = FilterInterview::findInterviewByFilter($randomIndexInterview);
        $region = FilterRegion::findRegionByFilter($randomIndexRegion);

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
        $grandNews = GrandNews::all();

        $topPriorityNews = $grandNews->sortByDesc('priority')->all();

        return response()->json($topPriorityNews, 200, [], JSON_UNESCAPED_UNICODE);
    }
}
