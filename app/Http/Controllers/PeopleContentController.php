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

    public function allInterviews()
    {
        $interviews = FilterInterview::allInterviewsByFilter();

        return response()->json($interviews);
    }

    public function allOpinions()
    {
        $opinions = FilterOpinion::allOpinionsByFilter();

        return response()->json($opinions);
    }

    public function allPointViews()
    {
        $pointViews = FilterPointView::allPointViewsByFilter();

        return response()->json($pointViews);
    }
}
