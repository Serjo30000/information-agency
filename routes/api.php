<?php

use App\Http\Controllers\GrandNewsController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PeopleContentController;
use App\Http\Controllers\RegionsAndPeoplesController;
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
Route::get('news', [NewsController::class, 'allNews']);
Route::get('peopleContents', [PeopleContentController::class, 'allPeopleContents']);
Route::get('interviews', [PeopleContentController::class, 'allInterviews']);
Route::get('opinions', [PeopleContentController::class, 'allOpinions']);
Route::get('pointViews', [PeopleContentController::class, 'allPointViews']);
Route::get('regionsAndPeoples', [RegionsAndPeoplesController::class, 'allRegionsAndPeoples']);
Route::get('peoples', [RegionsAndPeoplesController::class, 'allPeoples']);
Route::get('regions', [RegionsAndPeoplesController::class, 'allRegions']);
Route::get('statuses', [StatusController::class, 'allStatuses']);
Route::get('users', [UserController::class, 'allUsers']);
Route::get('videos', [VideoController::class, 'allVideos']);
