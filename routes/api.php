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
Route::get('grandNewsOne/{id}', [GrandNewsController::class, 'findGrandNewsOne']);
Route::get('news', [NewsController::class, 'allNews']);
Route::get('newsOne/{id}', [NewsController::class, 'findNewsOne']);
Route::get('peopleContents', [PeopleContentController::class, 'allPeopleContents']);
Route::get('peopleContent/{id}', [PeopleContentController::class, 'findPeopleContent']);
Route::get('interviews', [PeopleContentController::class, 'allInterviews']);
Route::get('interview/{id}', [PeopleContentController::class, 'findInterview']);
Route::get('opinions', [PeopleContentController::class, 'allOpinions']);
Route::get('opinion/{id}', [PeopleContentController::class, 'findOpinion']);
Route::get('pointViews', [PeopleContentController::class, 'allPointViews']);
Route::get('pointView/{id}', [PeopleContentController::class, 'findPointView']);
Route::get('regionsAndPeoples', [RegionsAndPeoplesController::class, 'allRegionsAndPeoples']);
Route::get('regionAndPeople/{id}', [RegionsAndPeoplesController::class, 'findRegionAndPeople']);
Route::get('peoples', [RegionsAndPeoplesController::class, 'allPeoples']);
Route::get('people/{id}', [RegionsAndPeoplesController::class, 'findPeople']);
Route::get('regions', [RegionsAndPeoplesController::class, 'allRegions']);
Route::get('region/{id}', [RegionsAndPeoplesController::class, 'findRegion']);
Route::get('statuses', [StatusController::class, 'allStatuses']);
Route::get('status/{id}', [StatusController::class, 'findStatus']);
Route::get('users', [UserController::class, 'allUsers']);
Route::get('user/{id}', [UserController::class, 'findUser']);
Route::get('videos', [VideoController::class, 'allVideos']);
Route::get('video/{id}', [VideoController::class, 'findVideo']);
Route::get('randomSections', [ReportController::class, 'allRandomSections']);
Route::get('priorityGrandNews', [ReportController::class, 'allPriorityGrandNews']);
