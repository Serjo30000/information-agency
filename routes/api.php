<?php

use App\Http\Controllers\GrandNewsController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PeopleContentController;
use App\Http\Controllers\RegionsAndPeoplesController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VideoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('grandNews', [GrandNewsController::class, 'allGrandNews']);
Route::get('grandNewsTopFive', [GrandNewsController::class, 'listGrandNewsTopFive']);
Route::get('grandNewsTopTwenty', [GrandNewsController::class, 'listGrandNewsTopTwenty']);
Route::get('grandNewsPaginate', [GrandNewsController::class, 'allGrandNewsPaginate']);
Route::get('grandNewsOne/{id}', [GrandNewsController::class, 'findGrandNewsOne']);
Route::get('news', [NewsController::class, 'allNews']);
Route::get('newsTopTenByRegion/{id_region}', [NewsController::class, 'listNewsTopTenByRegion']);
Route::get('newsPaginate', [NewsController::class, 'allNewsPaginate']);
Route::get('newsOne/{id}', [NewsController::class, 'findNewsOne']);
Route::get('peopleContents', [PeopleContentController::class, 'allPeopleContents']);
Route::get('peopleContentsPaginate', [PeopleContentController::class, 'allPeopleContentsPaginate']);
Route::get('peopleContent/{id}', [PeopleContentController::class, 'findPeopleContent']);
Route::get('interviews', [PeopleContentController::class, 'allInterviews']);
Route::get('interviewsTopThree', [PeopleContentController::class, 'listInterviewsTopThree']);
Route::get('interviewsTopFour', [PeopleContentController::class, 'listInterviewsTopFour']);
Route::get('interviewsTopFourteen', [PeopleContentController::class, 'listInterviewsTopFourteen']);
Route::get('interviewsPaginate', [PeopleContentController::class, 'allInterviewsPaginate']);
Route::get('interview/{id}', [PeopleContentController::class, 'findInterview']);
Route::get('opinions', [PeopleContentController::class, 'allOpinions']);
Route::get('opinionsTopFour', [PeopleContentController::class, 'listOpinionsTopFour']);
Route::get('opinionsTopFourteen', [PeopleContentController::class, 'listOpinionsTopFourteen']);
Route::get('opinionsPaginate', [PeopleContentController::class, 'allOpinionsPaginate']);
Route::get('opinion/{id}', [PeopleContentController::class, 'findOpinion']);
Route::get('pointViews', [PeopleContentController::class, 'allPointViews']);
Route::get('pointViewsTopFour', [PeopleContentController::class, 'listPointViewsTopFour']);
Route::get('pointViewsPaginate', [PeopleContentController::class, 'allPointViewsPaginate']);
Route::get('pointView/{id}', [PeopleContentController::class, 'findPointView']);
Route::get('regionsAndPeoples', [RegionsAndPeoplesController::class, 'allRegionsAndPeoples']);
Route::get('regionsAndPeoplesPaginate', [RegionsAndPeoplesController::class, 'allRegionsAndPeoplesPaginate']);
Route::get('regionAndPeople/{id}', [RegionsAndPeoplesController::class, 'findRegionAndPeople']);
Route::get('peoples', [RegionsAndPeoplesController::class, 'allPeoples']);
Route::get('peoplesPaginate', [RegionsAndPeoplesController::class, 'allPeoplesPaginate']);
Route::get('people/{id}', [RegionsAndPeoplesController::class, 'findPeople']);
Route::get('regions', [RegionsAndPeoplesController::class, 'allRegions']);
Route::get('region/{id}', [RegionsAndPeoplesController::class, 'findRegion']);
Route::get('statuses', [StatusController::class, 'allStatuses']);
Route::get('statusesPaginate', [StatusController::class, 'allStatusesPaginate']);
Route::get('status/{id}', [StatusController::class, 'findStatus']);
Route::get('users', [UserController::class, 'allUsers']);
Route::get('usersPaginate', [UserController::class, 'allUsersPaginate']);
Route::get('user/{id}', [UserController::class, 'findUser']);
Route::get('videos', [VideoController::class, 'allVideos']);
Route::get('videosTopFour', [VideoController::class, 'listVideosTopFour']);
Route::get('videosTopTen', [VideoController::class, 'listVideosTopTen']);
Route::get('videosPaginate', [VideoController::class, 'allVideosPaginate']);
Route::get('video/{id}', [VideoController::class, 'findVideo']);
Route::get('randomSections', [ReportController::class, 'allRandomSections']);
Route::get('priorityGrandNews', [ReportController::class, 'allPriorityGrandNews']);
