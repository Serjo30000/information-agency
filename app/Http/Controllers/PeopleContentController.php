<?php

namespace App\Http\Controllers;

use App\Http\Services\Filters\FilterInterview;
use App\Http\Services\Filters\FilterOpinion;
use App\Http\Services\Filters\FilterPointView;
use App\Models\PeopleContent;
use Illuminate\Http\Request;

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
