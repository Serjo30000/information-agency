<?php

use App\Http\Controllers\AuthorizationController;
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

//get
Route::get('grandNews', [GrandNewsController::class, 'allGrandNews']);
Route::get('grandNewsForPanel', [GrandNewsController::class, 'allGrandNewsForPanel'])->middleware(['auth:sanctum', 'role:guest']);
Route::get('grandNewsTopFive', [GrandNewsController::class, 'listGrandNewsTopFive']);
Route::get('grandNewsTopTwenty', [GrandNewsController::class, 'listGrandNewsTopTwenty']);
Route::get('grandNewsPaginate', [GrandNewsController::class, 'allGrandNewsPaginate']);
Route::get('grandNewsPaginateForPanel', [GrandNewsController::class, 'allGrandNewsPaginateForPanel'])->middleware(['auth:sanctum', 'role:guest']);
Route::get('grandNewsOne/{id}', [GrandNewsController::class, 'findGrandNewsOne']);
Route::get('grandNewsOneForPanel/{id}', [GrandNewsController::class, 'findGrandNewsOneForPanel'])->middleware(['auth:sanctum', 'role:guest']);
Route::get('news', [NewsController::class, 'allNews']);
Route::get('newsForPanel', [NewsController::class, 'allNewsForPanel'])->middleware(['auth:sanctum', 'role:guest']);
Route::get('newsPaginateAndSearch', [NewsController::class, 'allNewsPaginateAndSearch']);
Route::get('newsTopTenByRegion/{id_region}', [NewsController::class, 'listNewsTopTenByRegion']);
Route::get('newsPaginate', [NewsController::class, 'allNewsPaginate']);
Route::get('newsPaginateForPanel', [NewsController::class, 'allNewsPaginateForPanel'])->middleware(['auth:sanctum', 'role:guest']);
Route::get('newsOne/{id}', [NewsController::class, 'findNewsOne']);
Route::get('newsOneForPanel/{id}', [NewsController::class, 'findNewsOneForPanel'])->middleware(['auth:sanctum', 'role:guest']);
Route::get('peopleContents', [PeopleContentController::class, 'allPeopleContents']);
Route::get('peopleContentsForPanel', [PeopleContentController::class, 'allPeopleContentsForPanel'])->middleware(['auth:sanctum', 'role:guest']);
Route::get('peopleContentsPaginate', [PeopleContentController::class, 'allPeopleContentsPaginate']);
Route::get('peopleContentsPaginateForPanel', [PeopleContentController::class, 'allPeopleContentsPaginateForPanel'])->middleware(['auth:sanctum', 'role:guest']);
Route::get('peopleContent/{id}', [PeopleContentController::class, 'findPeopleContent']);
Route::get('peopleContentForPanel/{id}', [PeopleContentController::class, 'findPeopleContentForPanel'])->middleware(['auth:sanctum', 'role:guest']);
Route::get('interviews', [PeopleContentController::class, 'allInterviews']);
Route::get('interviewsForPanel', [PeopleContentController::class, 'allInterviewsForPanel'])->middleware(['auth:sanctum', 'role:guest']);
Route::get('interviewsTopThree', [PeopleContentController::class, 'listInterviewsTopThree']);
Route::get('interviewsTopFour', [PeopleContentController::class, 'listInterviewsTopFour']);
Route::get('interviewsTopFourteen', [PeopleContentController::class, 'listInterviewsTopFourteen']);
Route::get('interviewsPaginate', [PeopleContentController::class, 'allInterviewsPaginate']);
Route::get('interviewsPaginateForPanel', [PeopleContentController::class, 'allInterviewsPaginateForPanel'])->middleware(['auth:sanctum', 'role:guest']);
Route::get('interview/{id}', [PeopleContentController::class, 'findInterview']);
Route::get('interviewForPanel/{id}', [PeopleContentController::class, 'findInterviewForPanel'])->middleware(['auth:sanctum', 'role:guest']);
Route::get('opinions', [PeopleContentController::class, 'allOpinions']);
Route::get('opinionsForPanel', [PeopleContentController::class, 'allOpinionsForPanel'])->middleware(['auth:sanctum', 'role:guest']);
Route::get('opinionsTopFour', [PeopleContentController::class, 'listOpinionsTopFour']);
Route::get('opinionsTopFourteen', [PeopleContentController::class, 'listOpinionsTopFourteen']);
Route::get('opinionsPaginate', [PeopleContentController::class, 'allOpinionsPaginate']);
Route::get('opinionsPaginateForPanel', [PeopleContentController::class, 'allOpinionsPaginateForPanel'])->middleware(['auth:sanctum', 'role:guest']);
Route::get('opinion/{id}', [PeopleContentController::class, 'findOpinion']);
Route::get('opinionForPanel/{id}', [PeopleContentController::class, 'findOpinionForPanel'])->middleware(['auth:sanctum', 'role:guest']);
Route::get('pointViews', [PeopleContentController::class, 'allPointViews']);
Route::get('pointViewsForPanel', [PeopleContentController::class, 'allPointViewsForPanel'])->middleware(['auth:sanctum', 'role:guest']);
Route::get('pointViewsTopFour', [PeopleContentController::class, 'listPointViewsTopFour']);
Route::get('pointViewsPaginate', [PeopleContentController::class, 'allPointViewsPaginate']);
Route::get('pointViewsPaginateForPanel', [PeopleContentController::class, 'allPointViewsPaginateForPanel'])->middleware(['auth:sanctum', 'role:guest']);
Route::get('pointView/{id}', [PeopleContentController::class, 'findPointView']);
Route::get('pointViewForPanel/{id}', [PeopleContentController::class, 'findPointViewForPanel'])->middleware(['auth:sanctum', 'role:guest']);
Route::get('regionsAndPeoples', [RegionsAndPeoplesController::class, 'allRegionsAndPeoples']);
Route::get('regionsAndPeoplesPaginate', [RegionsAndPeoplesController::class, 'allRegionsAndPeoplesPaginate']);
Route::get('regionAndPeople/{id}', [RegionsAndPeoplesController::class, 'findRegionAndPeople']);
Route::get('peoples', [RegionsAndPeoplesController::class, 'allPeoples']);
Route::get('peoplesPaginate', [RegionsAndPeoplesController::class, 'allPeoplesPaginate']);
Route::get('people/{id}', [RegionsAndPeoplesController::class, 'findPeople']);
Route::get('regions', [RegionsAndPeoplesController::class, 'allRegions']);
Route::get('regionsPaginate', [RegionsAndPeoplesController::class, 'allRegionsPaginate']);
Route::get('regionsBySearch', [RegionsAndPeoplesController::class, 'listRegionsBySearch']);
Route::get('region/{id}', [RegionsAndPeoplesController::class, 'findRegion']);
Route::get('statuses', [StatusController::class, 'allStatuses'])->middleware(['auth:sanctum', 'role:guest']);
Route::get('statusesPaginate', [StatusController::class, 'allStatusesPaginate'])->middleware(['auth:sanctum', 'role:guest']);
Route::get('status/{id}', [StatusController::class, 'findStatus'])->middleware(['auth:sanctum', 'role:guest']);
Route::get('users', [UserController::class, 'allUsers'])->middleware(['auth:sanctum', 'role:guest']);
Route::get('usersPaginate', [UserController::class, 'allUsersPaginate'])->middleware(['auth:sanctum', 'role:guest']);
Route::get('user/{id}', [UserController::class, 'findUser'])->middleware(['auth:sanctum', 'role:guest']);
Route::get('videos', [VideoController::class, 'allVideos']);
Route::get('videosForPanel', [VideoController::class, 'allVideosForPanel'])->middleware(['auth:sanctum', 'role:guest']);
Route::get('videosTopFour', [VideoController::class, 'listVideosTopFour']);
Route::get('videosTopTen', [VideoController::class, 'listVideosTopTen']);
Route::get('videosPaginate', [VideoController::class, 'allVideosPaginate']);
Route::get('videosPaginateForPanel', [VideoController::class, 'allVideosPaginateForPanel'])->middleware(['auth:sanctum', 'role:guest']);
Route::get('video/{id}', [VideoController::class, 'findVideo']);
Route::get('videoForPanel/{id}', [VideoController::class, 'findVideoForPanel'])->middleware(['auth:sanctum', 'role:guest']);
Route::get('randomSections', [ReportController::class, 'allRandomSections']);
Route::get('priorityGrandNews', [ReportController::class, 'allPriorityGrandNews']);

//auth
Route::post('register', [AuthorizationController::class, 'register'])->middleware(['auth:sanctum', 'role:super_admin']);
Route::post('logout', [AuthorizationController::class, 'logout'])->middleware('auth:sanctum');
Route::post('login', [AuthorizationController::class, 'login'])->middleware('not.auth.user');
Route::post('checkAuth', [AuthorizationController::class, 'checkAuth']);

//create
Route::post('createVideo', [VideoController::class, 'createVideo'])->middleware(['auth:sanctum', 'role:editor']);
Route::post('createNews', [NewsController::class, 'createNews'])->middleware(['auth:sanctum', 'role:editor']);
Route::post('createGrandNews', [GrandNewsController::class, 'createGrandNews'])->middleware(['auth:sanctum', 'role:editor']);
Route::post('createRegion', [RegionsAndPeoplesController::class, 'createRegion'])->middleware(['auth:sanctum', 'role:editor']);
Route::post('createPeople', [RegionsAndPeoplesController::class, 'createPeople'])->middleware(['auth:sanctum', 'role:editor']);
Route::post('createInterview', [PeopleContentController::class, 'createInterview'])->middleware(['auth:sanctum', 'role:editor']);
Route::post('createOpinion', [PeopleContentController::class, 'createOpinion'])->middleware(['auth:sanctum', 'role:editor']);
Route::post('createPointView', [PeopleContentController::class, 'createPointView'])->middleware(['auth:sanctum', 'role:editor']);
