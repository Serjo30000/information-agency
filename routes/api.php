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

//Обязательные параметры:
//Необязательные параметры:
//Возвращает:
//Токен:

//get

Route::get('grandNews', [GrandNewsController::class, 'allGrandNews']);
//Обязательные параметры: Нет
//Необязательные параметры: Нет
//Возвращает: Список опубликованных главных новостей
//Токен: Нет

Route::get('grandNewsForPanel', [GrandNewsController::class, 'allGrandNewsForPanel'])->middleware('auth:sanctum');
//Обязательные параметры: Нет
//Необязательные параметры: Нет
//Возвращает: Список главных новостей
//Токен: Да

Route::get('grandNewsTopFive', [GrandNewsController::class, 'listGrandNewsTopFive']);
//Обязательные параметры: Нет
//Необязательные параметры: Нет
//Возвращает: Список опубликованных главных новостей 5 записей
//Токен: Нет

Route::get('grandNewsTopSix', [GrandNewsController::class, 'listGrandNewsTopSix']);
//Обязательные параметры: Нет
//Необязательные параметры: Нет
//Возвращает: Список опубликованных главных новостей 6 записей
//Токен: Нет

Route::get('grandNewsTopTwenty', [GrandNewsController::class, 'listGrandNewsTopTwenty']);
//Обязательные параметры: Нет
//Необязательные параметры: Нет
//Возвращает: Список опубликованных главных новостей 12 записей
//Токен: Нет

Route::get('grandNewsPaginate', [GrandNewsController::class, 'allGrandNewsPaginate']);
//Обязательные параметры: Нет
//Необязательные параметры: per_page (10), start_date, end_date
//Возвращает: Список опубликованных гланых новостей с пагинацией
//Токен: Нет

Route::get('grandNewsBySearchAndFiltersAndStatusesAndSortAndIsActiveForPanel', [GrandNewsController::class, 'allGrandNewsBySearchAndFiltersAndStatusesAndSortAndIsActiveForPanel'])->middleware('auth:sanctum');
//Обязательные параметры: Нет
//Необязательные параметры: per_page (10), search, start_date, end_date, selected_statuses ([] статусы из БД), sort_field (поле для сортировки из БД), sort_direction (asc, desc)
//Возвращает: Список активных главных новостей у пользователя с поиском, фильтрацией, статусами, сортировкой и пагинацией
//Токен: Да

Route::get('grandNewsBySearchAndFiltersAndStatusesAndSortAndIsNotActiveForPanel', [GrandNewsController::class, 'allGrandNewsBySearchAndFiltersAndStatusesAndSortAndIsNotActiveForPanel'])->middleware('auth:sanctum');
//Обязательные параметры: Нет
//Необязательные параметры: per_page (10), search, start_date, end_date, selected_statuses ([] статусы из БД), sort_field (поле для сортировки из БД), sort_direction (asc, desc)
//Возвращает: Список неактивных главных новостей у пользователя с поиском, фильтрацией, статусами, сортировкой и пагинацией
//Токен: Да

Route::get('grandNewsOne/{id}', [GrandNewsController::class, 'findGrandNewsOne']);
//Обязательные параметры: id
//Необязательные параметры: Нет
//Возвращает: Опубликованную главную новость
//Токен: Нет

Route::get('grandNewsOneForPanel/{id}', [GrandNewsController::class, 'findGrandNewsOneForPanel'])->middleware('auth:sanctum');
//Обязательные параметры: id
//Необязательные параметры: Нет
//Возвращает: Главную новость
//Токен: Да

Route::get('news', [NewsController::class, 'allNews']);
//Обязательные параметры: Нет
//Необязательные параметры: Нет
//Возвращает: Список опубликованных новостей
//Токен: Нет

Route::get('newsForPanel', [NewsController::class, 'allNewsForPanel'])->middleware('auth:sanctum');
//Обязательные параметры: Нет
//Необязательные параметры: Нет
//Возвращает: Список новостей
//Токен: Да

Route::get('newsPaginateAndSearch', [NewsController::class, 'allNewsPaginateAndSearch']);
//Обязательные параметры: Нет
//Необязательные параметры: per_page (10), start_date, end_date, searchContent, selected_regions ([] id регионов)
//Возвращает: Список опубликованных новостей с пагинацией
//Токен: Нет

Route::get('newsPaginateByFilterFederalRegion', [NewsController::class, 'allNewsPaginateByFilterFederalRegion']);
//Обязательные параметры: Нет
//Необязательные параметры: per_page (10), start_date, end_date
//Возвращает: Список опубликованных новостей с фильтрацией по Федеральным регионам
//Токен: Нет

Route::get('newsPaginateByFilterRegion', [NewsController::class, 'allNewsPaginateByFilterRegion']);
//Обязательные параметры: Нет
//Необязательные параметры: per_page (10), start_date, end_date
//Возвращает: Список опубликованных новостей с фильтрацией по обычным регионам
//Токен: Нет

Route::get('newsTopTenByRegion/{id_region}', [NewsController::class, 'listNewsTopTenByRegion']);
//Обязательные параметры: id_region
//Необязательные параметры: Нет
//Возвращает: Список опубликованных новостей 10 записей
//Токен: Нет

Route::get('newsPaginate', [NewsController::class, 'allNewsPaginate']);
//Обязательные параметры: Нет
//Необязательные параметры: per_page (10), start_date, end_date
//Возвращает: Список опубликованных новостей с пагинацией
//Токен: Нет

Route::get('newsBySearchAndFiltersAndStatusesAndSortForPanel', [NewsController::class, 'allNewsBySearchAndFiltersAndStatusesAndSortForPanel'])->middleware('auth:sanctum');
//Обязательные параметры: Нет
//Необязательные параметры: per_page (10), search, start_date, end_date, selected_statuses ([] статусы из БД), sort_field (поле для сортировки из БД), sort_direction (asc, desc)
//Возвращает: Список новостей у пользователя с поиском, фильтрацией, статусами, сортировкой и пагинацией
//Токен: Да

Route::get('newsBySearchAndFiltersAndStatusesAndSortForPanelCensor', [NewsController::class, 'allNewsBySearchAndFiltersAndStatusesAndSortForPanelCensor'])->middleware(['auth:sanctum', 'role:censor']);
//Обязательные параметры: Нет
//Необязательные параметры: per_page (10), search, start_date, end_date, selected_statuses ([] статусы из БД), sort_field (поле для сортировки из БД), sort_direction (asc, desc)
//Возвращает: Список новостей с поиском, фильтрацией, статусами, сортировкой и пагинацией
//Токен: Да

Route::get('newsOne/{id}', [NewsController::class, 'findNewsOne']);
//Обязательные параметры: id
//Необязательные параметры: Нет
//Возвращает: Опубликованную новость
//Токен: Нет

Route::get('newsOneForPanel/{id}', [NewsController::class, 'findNewsOneForPanel'])->middleware('auth:sanctum');
//Обязательные параметры: id
//Необязательные параметры: Нет
//Возвращает: Новость
//Токен: Да

Route::get('peopleContents', [PeopleContentController::class, 'allPeopleContents']);
//Обязательные параметры: Нет
//Необязательные параметры: Нет
//Возвращает: Список опубликованных peopleContents
//Токен: Нет

Route::get('peopleContentsForPanel', [PeopleContentController::class, 'allPeopleContentsForPanel'])->middleware('auth:sanctum');
//Обязательные параметры: Нет
//Необязательные параметры: Нет
//Возвращает: Список peopleContents
//Токен: Да

Route::get('peopleContentsPaginate', [PeopleContentController::class, 'allPeopleContentsPaginate']);
//Обязательные параметры: Нет
//Необязательные параметры: per_page (10), start_date, end_date
//Возвращает: Список опубликованных peopleContents с пагинацией
//Токен: Нет

Route::get('peopleContentsPaginateForPanel', [PeopleContentController::class, 'allPeopleContentsPaginateForPanel'])->middleware('auth:sanctum');
//Обязательные параметры: Нет
//Необязательные параметры: per_page (10), start_date, end_date
//Возвращает: Список peopleContents с пагинацией
//Токен: Да


Route::get('peopleContent/{id}', [PeopleContentController::class, 'findPeopleContent']);
//Обязательные параметры: id
//Необязательные параметры: Нет
//Возвращает: Опубликованный peopleContents
//Токен: Нет

Route::get('peopleContentForPanel/{id}', [PeopleContentController::class, 'findPeopleContentForPanel'])->middleware('auth:sanctum');
//Обязательные параметры: id
//Необязательные параметры: Нет
//Возвращает: peopleContents
//Токен: Да

Route::get('interviews', [PeopleContentController::class, 'allInterviews']);
//Обязательные параметры: Нет
//Необязательные параметры: Нет
//Возвращает: Список опубликованных интервью
//Токен: Нет

Route::get('interviewsForPanel', [PeopleContentController::class, 'allInterviewsForPanel'])->middleware('auth:sanctum');
//Обязательные параметры: Нет
//Необязательные параметры: Нет
//Возвращает: Список интервью
//Токен: Да

Route::get('interviewsTopThree', [PeopleContentController::class, 'listInterviewsTopThree']);
//Обязательные параметры: Нет
//Необязательные параметры: Нет
//Возвращает: Список опубликованных интервью 3 записи
//Токен: Нет

Route::get('interviewsTopFour', [PeopleContentController::class, 'listInterviewsTopFour']);
//Обязательные параметры: Нет
//Необязательные параметры: Нет
//Возвращает: Список опубликованных интервью 4 записи
//Токен: Нет

Route::get('interviewsTopFourteen', [PeopleContentController::class, 'listInterviewsTopFourteen']);
//Обязательные параметры: Нет
//Необязательные параметры: Нет
//Возвращает: Список опубликованных интервью 14 записей
//Токен: Нет

Route::get('interviewsPaginate', [PeopleContentController::class, 'allInterviewsPaginate']);
//Обязательные параметры: Нет
//Необязательные параметры: per_page (10), start_date, end_date
//Возвращает: Список опубликованных интервью с пагинацией
//Токен: Нет

Route::get('interviewsBySearchAndFiltersAndStatusesAndSortForPanel', [PeopleContentController::class, 'allInterviewsBySearchAndFiltersAndStatusesAndSortForPanel'])->middleware('auth:sanctum');
//Обязательные параметры: Нет
//Необязательные параметры: per_page (10), search, start_date, end_date, selected_statuses ([] статусы из БД), sort_field (поле для сортировки из БД), sort_direction (asc, desc)
//Возвращает: Список интервью у пользователя с поиском, фильтрацией, статусами, сортировкой и пагинацией
//Токен: Да

Route::get('interviewsBySearchAndFiltersAndStatusesAndSortForPanelCensor', [PeopleContentController::class, 'allInterviewsBySearchAndFiltersAndStatusesAndSortForPanelCensor'])->middleware(['auth:sanctum', 'role:censor']);
//Обязательные параметры: Нет
//Необязательные параметры: per_page (10), search, start_date, end_date, selected_statuses ([] статусы из БД), sort_field (поле для сортировки из БД), sort_direction (asc, desc)
//Возвращает: Список интервью с поиском, фильтрацией, статусами, сортировкой и пагинацией
//Токен: Да

Route::get('interview/{id}', [PeopleContentController::class, 'findInterview']);
//Обязательные параметры: id
//Необязательные параметры: Нет
//Возвращает: Опубликованный интервью
//Токен: Нет

Route::get('interviewForPanel/{id}', [PeopleContentController::class, 'findInterviewForPanel'])->middleware('auth:sanctum');
//Обязательные параметры: id
//Необязательные параметры: Нет
//Возвращает: Интервью
//Токен: Да

Route::get('opinions', [PeopleContentController::class, 'allOpinions']);
//Обязательные параметры: Нет
//Необязательные параметры: Нет
//Возвращает: Список опубликованных мнений
//Токен: Нет

Route::get('opinionsForPanel', [PeopleContentController::class, 'allOpinionsForPanel'])->middleware('auth:sanctum');
//Обязательные параметры: Нет
//Необязательные параметры: Нет
//Возвращает: Список мнений
//Токен: Да

Route::get('opinionsTopFour', [PeopleContentController::class, 'listOpinionsTopFour']);
//Обязательные параметры: Нет
//Необязательные параметры: Нет
//Возвращает: Список опубликованных мнений 4 записи
//Токен: Нет

Route::get('opinionsTopFourteen', [PeopleContentController::class, 'listOpinionsTopFourteen']);
//Обязательные параметры: Нет
//Необязательные параметры: Нет
//Возвращает: Список опубликованных мнений 14 записей
//Токен: Нет

Route::get('opinionsPaginate', [PeopleContentController::class, 'allOpinionsPaginate']);
//Обязательные параметры: Нет
//Необязательные параметры: per_page (10), start_date, end_date
//Возвращает: Список опубликованных мнений с пагинацией
//Токен: Нет

Route::get('opinionsBySearchAndFiltersAndStatusesAndSortForPanel', [PeopleContentController::class, 'allOpinionsBySearchAndFiltersAndStatusesAndSortForPanel'])->middleware('auth:sanctum');
//Обязательные параметры: Нет
//Необязательные параметры: per_page (10), search, start_date, end_date, selected_statuses ([] статусы из БД), sort_field (поле для сортировки из БД), sort_direction (asc, desc)
//Возвращает: Список мнений у пользователя с поиском, фильтрацией, статусами, сортировкой и пагинацией
//Токен: Да

Route::get('opinionsBySearchAndFiltersAndStatusesAndSortForPanelCensor', [PeopleContentController::class, 'allOpinionsBySearchAndFiltersAndStatusesAndSortForPanelCensor'])->middleware(['auth:sanctum', 'role:censor']);
//Обязательные параметры: Нет
//Необязательные параметры: per_page (10), search, start_date, end_date, selected_statuses ([] статусы из БД), sort_field (поле для сортировки из БД), sort_direction (asc, desc)
//Возвращает: Список мнений с поиском, фильтрацией, статусами, сортировкой и пагинацией
//Токен: Да

Route::get('opinion/{id}', [PeopleContentController::class, 'findOpinion']);
//Обязательные параметры: id
//Необязательные параметры: Нет
//Возвращает: Опубликованное мнение
//Токен: Нет

Route::get('opinionForPanel/{id}', [PeopleContentController::class, 'findOpinionForPanel'])->middleware('auth:sanctum');
//Обязательные параметры: id
//Необязательные параметры: Нет
//Возвращает: Мнение
//Токен: Да

Route::get('pointViews', [PeopleContentController::class, 'allPointViews']);
//Устарел

Route::get('pointViewsForPanel', [PeopleContentController::class, 'allPointViewsForPanel'])->middleware('auth:sanctum');
//Устарел

Route::get('pointViewsTopFour', [PeopleContentController::class, 'listPointViewsTopFour']);
//Устарел

Route::get('pointViewsPaginate', [PeopleContentController::class, 'allPointViewsPaginate']);
//Устарел

Route::get('pointViewsBySearchAndFiltersAndStatusesAndSortForPanel', [PeopleContentController::class, 'allPointViewsBySearchAndFiltersAndStatusesAndSortForPanel'])->middleware('auth:sanctum');
//Устарел

Route::get('pointView/{id}', [PeopleContentController::class, 'findPointView']);
//Устарел

Route::get('pointViewForPanel/{id}', [PeopleContentController::class, 'findPointViewForPanel'])->middleware('auth:sanctum');
//Устарел

Route::get('regionsAndPeoples', [RegionsAndPeoplesController::class, 'allRegionsAndPeoples']);
//Обязательные параметры: Нет
//Необязательные параметры: Нет
//Возвращает: Список regionsAndPeoples
//Токен: Нет

Route::get('regionsAndPeoplesPaginate', [RegionsAndPeoplesController::class, 'allRegionsAndPeoplesPaginate']);
//Обязательные параметры: Нет
//Необязательные параметры: per_page (10), start_date, end_date
//Возвращает: Список regionsAndPeoples с пагинацией
//Токен: Нет

Route::get('regionsPaginateByFilterFederal', [RegionsAndPeoplesController::class, 'allRegionsPaginateByFilterFederal']);
//Обязательные параметры: Нет
//Необязательные параметры: per_page (10), start_date, end_date
//Возвращает: Список regionsAndPeoples с фильтрацией по федеральным регионам с пагинацией
//Токен: Нет

Route::get('regionsPaginateByFilterNotFederal', [RegionsAndPeoplesController::class, 'allRegionsPaginateByFilterNotFederal']);
//Обязательные параметры: Нет
//Необязательные параметры: per_page (10), start_date, end_date
//Возвращает: Список regionsAndPeoples с фильтрацией по обычным регионам с пагинацией
//Токен: Нет

Route::get('regionsBySearchAndFiltersAndSortForPanel', [RegionsAndPeoplesController::class, 'allRegionsBySearchAndFiltersAndSortForPanel'])->middleware('auth:sanctum');
//Обязательные параметры: Нет
//Необязательные параметры: per_page (10), start_date, end_date, search, sort_field, sort_direction
//Возвращает: Список регионов с фильтрацией, поиском, сортировкой по обычным регионам с пагинацией
//Токен: Да

Route::get('peoplesBySearchAndFiltersAndSortForPanel', [RegionsAndPeoplesController::class, 'allPeoplesBySearchAndFiltersAndSortForPanel'])->middleware('auth:sanctum');
//Обязательные параметры: Нет
//Необязательные параметры: per_page (10), start_date, end_date, search, sort_field, sort_direction
//Возвращает: Список людей с фильтрацией, поиском, сортировкой по обычным регионам с пагинацией
//Токен: Да

Route::get('regionAndPeople/{id}', [RegionsAndPeoplesController::class, 'findRegionAndPeople']);
//Обязательные параметры: id
//Необязательные параметры: Нет
//Возвращает: regionAndPeople
//Токен: Нет

Route::get('peoples', [RegionsAndPeoplesController::class, 'allPeoples']);
//Обязательные параметры: Нет
//Необязательные параметры: Нет
//Возвращает: Список людей
//Токен: Нет

Route::get('peoplesPaginate', [RegionsAndPeoplesController::class, 'allPeoplesPaginate']);
//Обязательные параметры: Нет
//Необязательные параметры: per_page (10), start_date, end_date
//Возвращает: Список людей с пагинацией
//Токен: Нет

Route::get('people/{id}', [RegionsAndPeoplesController::class, 'findPeople']);
//Обязательные параметры: id
//Необязательные параметры: Нет
//Возвращает: Человек
//Токен: Нет

Route::get('regions', [RegionsAndPeoplesController::class, 'allRegions']);
//Обязательные параметры: Нет
//Необязательные параметры: Нет
//Возвращает: Список регионов
//Токен: Нет

Route::get('regionsPaginate', [RegionsAndPeoplesController::class, 'allRegionsPaginate']);
//Обязательные параметры: Нет
//Необязательные параметры: per_page (10), start_date, end_date
//Возвращает: Список регионов с пагинацией
//Токен: Нет

Route::get('regionsBySearch', [RegionsAndPeoplesController::class, 'listRegionsBySearch']);
//Обязательные параметры: Нет
//Необязательные параметры: search, selected_regions ([] id регионов)
//Возвращает: Список выбранных регионов
//Токен: Нет

Route::get('region/{id}', [RegionsAndPeoplesController::class, 'findRegion']);
//Обязательные параметры: id
//Необязательные параметры: Нет
//Возвращает: Регион
//Токен: Нет

Route::get('statuses', [StatusController::class, 'allStatuses'])->middleware('auth:sanctum');
//Обязательные параметры: Нет
//Необязательные параметры: Нет
//Возвращает: Список статусов
//Токен: Да

Route::get('statusesPaginate', [StatusController::class, 'allStatusesPaginate'])->middleware('auth:sanctum');
//Обязательные параметры: Нет
//Необязательные параметры: per_page (10), start_date, end_date
//Возвращает: Список статусов с пагинацией
//Токен: Да

Route::get('status/{id}', [StatusController::class, 'findStatus'])->middleware('auth:sanctum');
//Обязательные параметры: id
//Необязательные параметры: Нет
//Возвращает: Статус
//Токен: Да

Route::get('users', [UserController::class, 'allUsers'])->middleware(['auth:sanctum', 'any.role:admin,super_admin']);
//Обязательные параметры: Нет
//Необязательные параметры: Нет
//Возвращает: Список пользователей
//Токен: Да

Route::get('usersBySearchAndSortForPanel', [UserController::class, 'allUsersBySearchAndSortForPanel'])->middleware(['auth:sanctum', 'any.role:admin,super_admin']);
//Обязательные параметры: Нет
//Необязательные параметры: per_page (10), search, sort_field, sort_direction
//Возвращает: Список пользователей с поиском, сортировкой с пагинацией
//Токен: Да

Route::get('usersListForPanel', [UserController::class, 'allUsersListForPanel'])->middleware('auth:sanctum');
//Обязательные параметры: Нет
//Необязательные параметры: Нет
//Возвращает: Список пользователей только с id, login
//Токен: Да

Route::get('usersPaginate', [UserController::class, 'allUsersPaginate'])->middleware('auth:sanctum');
//Обязательные параметры: Нет
//Необязательные параметры: per_page (10), start_date, end_date
//Возвращает: Список пользователей с пагинацией
//Токен: Да

Route::get('user/{id}', [UserController::class, 'findUser'])->middleware(['auth:sanctum', 'any.role:admin,super_admin']);
//Обязательные параметры: id
//Необязательные параметры: Нет
//Возвращает: Пользователя
//Токен: Да

Route::get('userForPanel/{id}', [UserController::class, 'findUserForPanel'])->middleware('auth:sanctum');
//Обязательные параметры: id
//Необязательные параметры: Нет
//Возвращает: Пользователя только с id, login
//Токен: Да

Route::get('rolesForUser/{id}', [UserController::class, 'findRoleForUser'])->middleware(['auth:sanctum', 'any.role:admin,super_admin']);
//Обязательные параметры: id
//Необязательные параметры: Нет
//Возвращает: Роли другого пользователя
//Токен: Да

Route::get('videos', [VideoController::class, 'allVideos']);
//Обязательные параметры: Нет
//Необязательные параметры: Нет
//Возвращает: Список опубликованных видео
//Токен: Нет

Route::get('videosForPanel', [VideoController::class, 'allVideosForPanel'])->middleware('auth:sanctum');
//Обязательные параметры: Нет
//Необязательные параметры: Нет
//Возвращает: Список видео
//Токен: Да

Route::get('videosTopFour', [VideoController::class, 'listVideosTopFour']);
//Обязательные параметры: Нет
//Необязательные параметры: Нет
//Возвращает: Список видео 4 записи
//Токен: Да

Route::get('videosTopTen', [VideoController::class, 'listVideosTopTen']);
//Обязательные параметры: Нет
//Необязательные параметры: Нет
//Возвращает: Список видео 10 записей
//Токен: Да

Route::get('videosPaginate', [VideoController::class, 'allVideosPaginate']);
//Обязательные параметры: Нет
//Необязательные параметры: per_page (10), start_date, end_date
//Возвращает: Список опубликованных видео с пагинацией
//Токен: Нет

Route::get('videosPaginateForPanel', [VideoController::class, 'allVideosPaginateForPanel'])->middleware('auth:sanctum');
//Обязательные параметры: Нет
//Необязательные параметры: per_page (10), start_date, end_date
//Возвращает: Список видео с пагинацией
//Токен: Да

Route::get('videosBySearchAndFiltersAndStatusesAndSortForPanel', [VideoController::class, 'allVideosBySearchAndFiltersAndStatusesAndSortForPanel'])->middleware('auth:sanctum');
//Обязательные параметры: Нет
//Необязательные параметры: per_page (10), search, start_date, end_date, selected_statuses ([] статусы из БД), sort_field (поле для сортировки из БД), sort_direction (asc, desc)
//Возвращает: Список видео с поиском, фильтрацией, статусами, сортировкой и пагинацией
//Токен: Да

Route::get('video/{id}', [VideoController::class, 'findVideo']);
//Обязательные параметры: id
//Необязательные параметры: Нет
//Возвращает: Опубликованное видео
//Токен: Нет

Route::get('videoForPanel/{id}', [VideoController::class, 'findVideoForPanel'])->middleware('auth:sanctum');
//Обязательные параметры: id
//Необязательные параметры: Нет
//Возвращает: Видео
//Токен: Да

Route::get('randomSections', [ReportController::class, 'allRandomSections']);
//Обязательные параметры: Нет
//Необязательные параметры: Нет
//Возвращает: Рандомный список состоящий из мнения, человека, интервью, региона
//Токен: Нет

Route::get('priorityGrandNews', [ReportController::class, 'allPriorityGrandNews']);
//Обязательные параметры: Нет
//Необязательные параметры: Нет
//Возвращает: Список активных главных новостей по приоритету
//Токен: Нет

Route::get('allListForDelete', [ReportController::class, 'allListForDelete'])->middleware('auth:sanctum');
//Обязательные параметры: Нет
//Необязательные параметры: search
//Возвращает: Список всех записей с активной пометкой для удаления
//Токен: Да

//auth

Route::post('register', [AuthorizationController::class, 'register'])->middleware(['auth:sanctum', 'role:super_admin']);
//Параметры:
//$rules = [
//    'login' => 'required|string|max:255|unique:users',
//    'password' => 'required|string|min:8|confirmed',
//    'fio' => ['required','string','max:255',new FullName()],
//    'phone' => ['required', 'string', 'max:15', 'unique:users', new PhoneNumber],
//    'sys_Comment' => 'nullable|string',
//    'roles' => 'required|array',
//    'roles.*' => 'string|exists:roles,name',
//];
//Возвращает и делает: Токен нового зарегистрированного пользователя
//Токен: Да

Route::post('logout', [AuthorizationController::class, 'logout'])->middleware('auth:sanctum');
//Параметры: Нет
//Возвращает и делает: Удаляет все токены пользователя
//Токен: Да

Route::post('login', [AuthorizationController::class, 'login'])->middleware('not.auth.user');
//Параметры:
//$rules = [
//    'login' => 'required|string',
//    'password' => 'required|string',
//];
//Возвращает и делает: Создание токена
//Токен: Должен быть обязательно пустым, например:  ''

Route::post('checkAuth', [AuthorizationController::class, 'checkAuth'])->middleware('auth:sanctum');
//Параметры: Нет
//Возвращает и делает: Проверяет есть ли пользователь в системе
//Токен: Да

Route::post('checkRole', [AuthorizationController::class, 'checkRole'])->middleware('auth:sanctum');
//Параметры:
//$rules = [
//    'roles' => 'required|array',
//    'roles.*' => 'string|exists:roles,name',
//];
//Возвращает и делает: Проверяет есть ли роли у пользователя
//Токен: Да

Route::get('account', [AuthorizationController::class, 'findAccount'])->middleware('auth:sanctum');
//Параметры: Нет
//Возвращает и делает: Пользователя системы
//Токен: Да

Route::get('myRoles', [AuthorizationController::class, 'findRole'])->middleware('auth:sanctum');
//Параметры: Нет
//Возвращает и делает: Роли пользователя системы
//Токен: Да

Route::put('editAccount', [AuthorizationController::class, 'editAccount'])->middleware('auth:sanctum');
//Параметры:
//$rules = [
//    'login' => [
//        'required',
//        'string',
//        'max:255',
//        Rule::unique('users', 'login')->ignore($user->id),
//    ],
//    'password' => 'required|string|min:8|confirmed',
//    'fio' => ['required','string','max:255',new FullName()],
//    'phone' => ['required', 'string', 'max:15', Rule::unique('users', 'phone')->ignore($user->id), new PhoneNumber],
//    'sys_Comment' => 'nullable|string',
//];
//Возвращает и делает: Изменение полей пользователя системы
//Токен: Да

//create

Route::post('createVideo', [VideoController::class, 'createVideo'])->middleware(['auth:sanctum', 'any.role:editor,admin,super_admin']);
//Параметры:
//$rules = [
//    'path_to_video' => 'required|string',
//    'title' => 'required|string',
//    'source' => 'required|string',
//    'publication_date' => [
//        'required',
//        'string',
//        'date_format:"Y-m-d H:i:s"',
//    ],
//    'sys_Comment' => 'nullable|string',
//    'user_id' => 'nullable|integer|exists:users,id',
//];
//Возвращает и делает: Новое видео со статусом редактируется
//Токен: Да

Route::post('createVideoForCheck', [VideoController::class, 'createVideoForCheck'])->middleware(['auth:sanctum', 'any.role:editor,admin,super_admin']);
//Параметры:
//$rules = [
//    'path_to_video' => 'required|string',
//    'title' => 'required|string',
//    'source' => 'required|string',
//    'publication_date' => [
//        'required',
//        'string',
//        'date_format:"Y-m-d H:i:s"',
//    ],
//    'sys_Comment' => 'nullable|string',
//    'user_id' => 'nullable|integer|exists:users,id',
//];
//Возвращает и делает: Новое видео со статусом ожидает подтверждения
//Токен: Да

Route::post('createNews', [NewsController::class, 'createNews'])->middleware(['auth:sanctum', 'any.role:editor,admin,super_admin']);
//Параметры:
//$rules = [
//    'path_to_image_or_video' => 'required|string',
//    'title' => 'required|string',
//    'content' => 'required|string',
//    'source' => 'nullable|string',
//    'sys_Comment' => 'nullable|string',
//    'publication_date' => [
//        'required',
//        'string',
//        'date_format:"Y-m-d H:i:s"',
//    ],
//    'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
//    'user_id' => 'nullable|integer|exists:users,id',
//];
//Возвращает и делает: Новую новость со статусом редактируется
//Токен: Да

Route::post('createNewsForCheck', [NewsController::class, 'createNewsForCheck'])->middleware(['auth:sanctum', 'any.role:editor,admin,super_admin']);
//Параметры:
//$rules = [
//    'path_to_image_or_video' => 'required|string',
//    'title' => 'required|string',
//    'content' => 'required|string',
//    'source' => 'nullable|string',
//    'sys_Comment' => 'nullable|string',
//    'publication_date' => [
//        'required',
//        'string',
//        'date_format:"Y-m-d H:i:s"',
//    ],
//    'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
//    'user_id' => 'nullable|integer|exists:users,id',
//];
//Возвращает и делает: Новую новость со статусом ожидает подтверждения
//Токен: Да

Route::post('createGrandNews', [GrandNewsController::class, 'createGrandNews'])->middleware(['auth:sanctum', 'any.role:editor,admin,super_admin']);
//Параметры:
//$rules = [
//    'start_publication_date' => [
//        'required',
//        'string',
//        'date_format:"Y-m-d H:i:s"',
//    ],
//    'end_publication_date' => [
//        'required',
//        'string',
//        'date_format:"Y-m-d H:i:s"',
//    ],
//    'priority' => 'required|integer|min:0',
//    'sys_Comment' => 'nullable|string',
//    'isActivate' => 'required|boolean',
//    'news_id' => 'required|integer|exists:news,id',
//];
//Возвращает и делает: Новую главную новость
//Токен: Да

Route::post('createNewsWithGrandNews', [GrandNewsController::class, 'createNewsWithGrandNews'])->middleware(['auth:sanctum', 'role:first_page']);
//Параметры:
//$rules = [
//    'path_to_image_or_video' => 'required|string',
//    'title' => 'required|string',
//    'content' => 'required|string',
//    'source' => 'nullable|string',
//    'publication_date' => [
//        'required',
//        'string',
//        'date_format:"Y-m-d H:i:s"',
//    ],
//    'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
//    'user_id' => 'nullable|integer|exists:users,id',
//    'start_publication_date' => [
//        'required',
//        'string',
//        'date_format:"Y-m-d H:i:s"',
//    ],
//    'end_publication_date' => [
//        'required',
//        'string',
//        'date_format:"Y-m-d H:i:s"',
//    ],
//    'priority' => 'required|integer|min:0',
//    'sys_Comment' => 'nullable|string',
//    'isActivate' => 'required|boolean',
//];
//Возвращает и делает: Новую новость с главной новостью
//Токен: Да

Route::post('createRegion', [RegionsAndPeoplesController::class, 'createRegion'])->middleware(['auth:sanctum', 'any.role:editor,admin,super_admin']);
//Параметры:
//$rules = [
//    'path_to_image' => 'required|string',
//    'type_region' => 'required|string',
//    'name_region' => 'required|string',
//    'content' => 'required|string',
//    'date_foundation' => 'required|date_format:Y-m-d',
//    'sys_Comment' => 'nullable|string',
//];
//Возвращает и делает: Новый регион
//Токен: Да

Route::post('createPeople', [RegionsAndPeoplesController::class, 'createPeople'])->middleware(['auth:sanctum', 'any.role:editor,admin,super_admin']);
//Параметры:
//$rules = [
//    'path_to_image' => 'required|string',
//    'position' => 'required|string',
//    'fio' => ['required','string','max:255',new FullName()],
//    'place_work' => 'required|string',
//    'content' => 'required|string',
//    'date_birth' => 'required|date_format:Y-m-d',
//    'sys_Comment' => 'nullable|string',
//];
//Возвращает и делает: Нового человека
//Токен: Да

Route::post('createInterview', [PeopleContentController::class, 'createInterview'])->middleware(['auth:sanctum', 'any.role:editor,admin,super_admin']);
//Параметры:
//$rules = [
//    'path_to_image' => 'required|string',
//    'title' => 'required|string',
//    'content' => 'required|string',
//    'source' => 'required|string',
//    'publication_date' => 'required|date_format:Y-m-d',
//    'sys_Comment' => 'nullable|string',
//    'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
//    'user_id' => 'nullable|integer|exists:users,id',
//];
//Возвращает и делает: Новое интервью со статусом редактируется
//Токен: Да

Route::post('createInterviewForCheck', [PeopleContentController::class, 'createInterviewForCheck'])->middleware(['auth:sanctum', 'any.role:editor,admin,super_admin']);
//Параметры:
//$rules = [
//    'path_to_image' => 'required|string',
//    'title' => 'required|string',
//    'content' => 'required|string',
//    'source' => 'required|string',
//    'publication_date' => 'required|date_format:Y-m-d',
//    'sys_Comment' => 'nullable|string',
//    'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
//    'user_id' => 'nullable|integer|exists:users,id',
//];
//Возвращает и делает: Новое интервью со статусом ожидает подтверждения
//Токен: Да

Route::post('createOpinion', [PeopleContentController::class, 'createOpinion'])->middleware(['auth:sanctum', 'any.role:editor,admin,super_admin']);
//Параметры:
//$rules = [
//    'path_to_image' => 'required|string',
//    'title' => 'required|string',
//    'content' => 'required|string',
//    'publication_date' => 'required|date_format:Y-m-d',
//    'sys_Comment' => 'nullable|string',
//    'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
//    'user_id' => 'nullable|integer|exists:users,id',
//];
//Возвращает и делает: Новое мнение со статусом редактируется
//Токен: Да

Route::post('createOpinionForCheck', [PeopleContentController::class, 'createOpinionForCheck'])->middleware(['auth:sanctum', 'any.role:editor,admin,super_admin']);
//Параметры:
//$rules = [
//    'path_to_image' => 'required|string',
//    'title' => 'required|string',
//    'content' => 'required|string',
//    'publication_date' => 'required|date_format:Y-m-d',
//    'sys_Comment' => 'nullable|string',
//    'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
//    'user_id' => 'nullable|integer|exists:users,id',
//];
//Возвращает и делает: Новое мнение со статусом ожидает подтверждения
//Токен: Да

Route::post('createPointView', [PeopleContentController::class, 'createPointView'])->middleware(['auth:sanctum', 'any.role:editor,admin,super_admin']);
//Устарел

Route::post('createPointViewForCheck', [PeopleContentController::class, 'createPointViewForCheck'])->middleware(['auth:sanctum', 'any.role:editor,admin,super_admin']);
//Устарел

//delete_mark

Route::put('deleteVideoMark', [VideoController::class, 'deleteVideoMark'])->middleware(['auth:sanctum', 'any.role:editor,admin,super_admin']);
//Параметры:
//$rules = [
//    'video_ids' => 'required|array',
//    'video_ids.*' => 'integer|exists:videos,id',
//];
//Возвращает и делает: Помеченные на удаление видео
//Токен: Да

Route::put('deleteNewsMark', [NewsController::class, 'deleteNewsMark'])->middleware(['auth:sanctum', 'any.role:editor,admin,super_admin']);
//Параметры:
//$rules = [
//    'news_ids' => 'required|array',
//    'news_ids.*' => 'integer|exists:news,id',
//];
//Возвращает и делает: Помеченные на удаление новости
//Токен: Да

Route::put('deleteUserMark', [UserController::class, 'deleteUserMark'])->middleware(['auth:sanctum', 'any.role:admin,super_admin']);
//Параметры:
//$rules = [
//    'user_ids' => 'required|array',
//    'user_ids.*' => 'integer|exists:users,id',
//];
//Возвращает и делает: Помеченные на удаление пользователи
//Токен: Да

Route::put('deleteInterviewMark', [PeopleContentController::class, 'deleteInterviewMark'])->middleware(['auth:sanctum', 'any.role:editor,admin,super_admin']);
//Параметры:
//$rules = [
//    'interview_ids' => 'required|array',
//    'interview_ids.*' => 'integer|exists:people_contents,id',
//];
//Возвращает и делает: Помеченные на удаление интервью
//Токен: Да

Route::put('deleteOpinionMark', [PeopleContentController::class, 'deleteOpinionMark'])->middleware(['auth:sanctum', 'any.role:editor,admin,super_admin']);
//Параметры:
//$rules = [
//    'opinion_ids' => 'required|array',
//    'opinion_ids.*' => 'integer|exists:people_contents,id',
//];
//Возвращает и делает: Помеченные на удаление мнения
//Токен: Да

Route::put('deletePointViewMark', [PeopleContentController::class, 'deletePointViewMark'])->middleware(['auth:sanctum', 'any.role:editor,admin,super_admin']);
//Устарел

Route::put('deleteRegionMark', [RegionsAndPeoplesController::class, 'deleteRegionMark'])->middleware(['auth:sanctum', 'any.role:editor,admin,super_admin']);
//Параметры:
//$rules = [
//    'region_ids' => 'required|array',
//    'region_ids.*' => 'integer|exists:regions_and_peoples,id',
//];
//Возвращает и делает: Помеченные на удаление регионы
//Токен: Да

Route::put('deletePeopleMark', [RegionsAndPeoplesController::class, 'deletePeopleMark'])->middleware(['auth:sanctum', 'any.role:editor,admin,super_admin']);
//Параметры:
//$rules = [
//    'people_ids' => 'required|array',
//    'people_ids.*' => 'integer|exists:regions_and_peoples,id',
//];
//Возвращает и делает: Помеченные на удаление люди
//Токен: Да

//delete

Route::delete('deleteVideo/{id}', [VideoController::class, 'deleteVideo'])->middleware(['auth:sanctum', 'role:deleter']);
//Устарел

Route::delete('deleteNews/{id}', [NewsController::class, 'deleteNews'])->middleware(['auth:sanctum', 'role:deleter']);
//Устарел

Route::delete('deleteGrandNews/{id}', [GrandNewsController::class, 'deleteGrandNews'])->middleware(['auth:sanctum', 'role:deleter']);
//Устарел

Route::delete('deleteRegion/{id}', [RegionsAndPeoplesController::class, 'deleteRegion'])->middleware(['auth:sanctum', 'role:deleter']);
//Устарел

Route::delete('deletePeople/{id}', [RegionsAndPeoplesController::class, 'deletePeople'])->middleware(['auth:sanctum', 'role:deleter']);
//Устарел

Route::delete('deleteInterview/{id}', [PeopleContentController::class, 'deleteInterview'])->middleware(['auth:sanctum', 'role:deleter']);
//Устарел

Route::delete('deleteOpinion/{id}', [PeopleContentController::class, 'deleteOpinion'])->middleware(['auth:sanctum', 'role:deleter']);
//Устарел

Route::delete('deletePointView/{id}', [PeopleContentController::class, 'deletePointView'])->middleware(['auth:sanctum', 'role:deleter']);
//Устарел

Route::delete('deleteUser/{id}', [UserController::class, 'deleteUser'])->middleware(['auth:sanctum', 'role:deleter']);
//Устарел

Route::delete('deleteAllForPanel', [ReportController::class, 'deleteAllForPanel'])->middleware(['auth:sanctum', 'role:deleter']);
//Параметры:
//$rules = [
//    'news_ids' => 'nullable|array',
//    'news_ids.*' => 'integer|exists:news,id',
//    'regions_ids' => 'nullable|array',
//    'regions_ids.*' => 'integer|exists:regions_and_peoples,id',
//    'people_ids' => 'nullable|array',
//    'people_ids.*' => 'integer|exists:regions_and_peoples,id',
//    'interview_ids' => 'nullable|array',
//    'interview_ids.*' => 'integer|exists:people_contents,id',
//    'opinion_ids' => 'nullable|array',
//    'opinion_ids.*' => 'integer|exists:people_contents,id',
//    'user_ids' => 'nullable|array',
//    'user_ids.*' => 'integer|exists:users,id',
//    'video_ids' => 'nullable|array',
//    'video_ids.*' => 'integer|exists:videos,id',
//];
//Возвращает и делает: Удаляет все помеченные на удаление записи
//Токен: Да

//edit

Route::put('editVideo/{id}', [VideoController::class, 'editVideo'])->middleware(['auth:sanctum', 'any.role:editor,admin,super_admin']);
//Параметры:
//id,
//$rules = [
//    'path_to_video' => 'required|string',
//    'title' => 'required|string',
//    'source' => 'required|string',
//    'publication_date' => [
//        'required',
//        'string',
//        'date_format:"Y-m-d H:i:s"',
//    ],
//    'sys_Comment' => 'nullable|string',
//    'user_id' => 'nullable|integer|exists:users,id',
//];
//Возвращает и делает: Измененное видео без изменения статуса
//Токен: Да

Route::put('editVideoForCheck/{id}', [VideoController::class, 'editVideoForCheck'])->middleware(['auth:sanctum', 'any.role:editor,admin,super_admin']);
//Параметры:
//id,
//$rules = [
//    'path_to_video' => 'required|string',
//    'title' => 'required|string',
//    'source' => 'required|string',
//    'publication_date' => [
//        'required',
//        'string',
//        'date_format:"Y-m-d H:i:s"',
//    ],
//    'sys_Comment' => 'nullable|string',
//    'user_id' => 'nullable|integer|exists:users,id',
//];
//Возвращает и делает: Измененное видео со статусом ожидает подтверждения
//Токен: Да

Route::put('editNews/{id}', [NewsController::class, 'editNews'])->middleware(['auth:sanctum', 'any.role:editor,admin,super_admin']);
//Параметры:
//id,
//$rules = [
//    'path_to_image_or_video' => 'required|string',
//    'title' => 'required|string',
//    'content' => 'required|string',
//    'source' => 'nullable|string',
//    'sys_Comment' => 'nullable|string',
//    'publication_date' => [
//        'required',
//        'string',
//        'date_format:"Y-m-d H:i:s"',
//    ],
//    'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
//    'user_id' => 'nullable|integer|exists:users,id',
//];
//Возвращает и делает: Измененная новость без изменения статуса
//Токен: Да

Route::put('editNewsCensor/{id}', [NewsController::class, 'editNewsCensor'])->middleware(['auth:sanctum', 'role:censor']);
//Параметры:
//id,
//$rules = [
//    'path_to_image_or_video' => 'required|string',
//    'title' => 'required|string',
//    'content' => 'required|string',
//    'source' => 'nullable|string',
//    'sys_Comment' => 'nullable|string',
//    'publication_date' => [
//        'required',
//        'string',
//        'date_format:"Y-m-d H:i:s"',
//    ],
//    'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
//    'user_id' => 'nullable|integer|exists:users,id',
//    'status' => 'required|string|exists:statuses,status',
//];
//Возвращает и делает: Измененная новость цензором с любым допустимым статусом
//Токен: Да

Route::put('editNewsForCheck/{id}', [NewsController::class, 'editNewsForCheck'])->middleware(['auth:sanctum', 'any.role:editor,admin,super_admin']);
//Параметры:
//id,
//$rules = [
//    'path_to_image_or_video' => 'required|string',
//    'title' => 'required|string',
//    'content' => 'required|string',
//    'source' => 'nullable|string',
//    'sys_Comment' => 'nullable|string',
//    'publication_date' => [
//        'required',
//        'string',
//        'date_format:"Y-m-d H:i:s"',
//    ],
//    'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
//    'user_id' => 'nullable|integer|exists:users,id',
//];
//Возвращает и делает: Измененная новость со статусом ожидает подтверждения
//Токен: Да

Route::put('editNewsForCheckCensor/{id}', [NewsController::class, 'editNewsForCheckCensor'])->middleware(['auth:sanctum', 'role:censor']);
//Параметры:
//id,
//$rules = [
//    'path_to_image_or_video' => 'required|string',
//    'title' => 'required|string',
//    'content' => 'required|string',
//    'source' => 'nullable|string',
//    'sys_Comment' => 'nullable|string',
//    'publication_date' => [
//        'required',
//        'string',
//        'date_format:"Y-m-d H:i:s"',
//    ],
//    'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
//    'user_id' => 'nullable|integer|exists:users,id',
//];
//Возвращает и делает: Измененная новость цензором со статусом ожидает подтверждения
//Токен: Да

Route::put('editGrandNews/{id}', [GrandNewsController::class, 'editGrandNews'])->middleware(['auth:sanctum', 'any.role:editor,admin,super_admin']);
//Параметры:
//id,
//$rules = [
//    'start_publication_date' => [
//        'required',
//        'string',
//        'date_format:"Y-m-d H:i:s"',
//    ],
//    'end_publication_date' => [
//        'required',
//        'string',
//        'date_format:"Y-m-d H:i:s"',
//    ],
//    'priority' => 'required|integer|min:0',
//    'sys_Comment' => 'nullable|string',
//    'isActivate' => 'required|boolean',
//    'news_id' => 'required|integer|exists:news,id',
//];
//Возвращает и делает: Измененная главная новость
//Токен: Да

Route::put('editNewsWithGrandNews/{id}', [GrandNewsController::class, 'editNewsWithGrandNews'])->middleware(['auth:sanctum', 'role:first_page']);
//Параметры:
//id,
//$rules = [
//    'start_publication_date' => [
//        'required',
//        'string',
//        'date_format:"Y-m-d H:i:s"',
//    ],
//    'end_publication_date' => [
//        'required',
//        'string',
//        'date_format:"Y-m-d H:i:s"',
//    ],
//    'priority' => 'required|integer|min:0',
//    'sys_Comment' => 'nullable|string',
//    'isActivate' => 'required|boolean',
//    'news_id' => 'required|integer|exists:news,id',
//    'path_to_image_or_video' => 'required|string',
//    'title' => 'required|string',
//    'content' => 'required|string',
//    'source' => 'nullable|string',
//    'publication_date' => [
//        'required',
//        'string',
//        'date_format:"Y-m-d H:i:s"',
//    ],
//    'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
//    'user_id' => 'nullable|integer|exists:users,id',
//];
//Возвращает и делает: Измененная главная новость со связанной новостью
//Токен: Да

Route::put('editRegion/{id}', [RegionsAndPeoplesController::class, 'editRegion'])->middleware(['auth:sanctum', 'any.role:editor,admin,super_admin']);
//Параметры:
//id,
//$rules = [
//    'path_to_image' => 'required|string',
//    'type_region' => 'required|string',
//    'name_region' => 'required|string',
//    'content' => 'required|string',
//    'date_foundation' => 'required|date_format:Y-m-d',
//    'sys_Comment' => 'nullable|string',
//];
//Возвращает и делает: Измененный регион
//Токен: Да

Route::put('editPeople/{id}', [RegionsAndPeoplesController::class, 'editPeople'])->middleware(['auth:sanctum', 'any.role:editor,admin,super_admin']);
//Параметры:
//id,
//$rules = [
//    'path_to_image' => 'required|string',
//    'position' => 'required|string',
//    'fio' => ['required','string','max:255',new FullName()],
//    'place_work' => 'required|string',
//    'content' => 'required|string',
//    'date_birth' => 'required|date_format:Y-m-d',
//    'sys_Comment' => 'nullable|string',
//];
//Возвращает и делает: Измененный xtkjdtr
//Токен: Да

Route::put('editInterview/{id}', [PeopleContentController::class, 'editInterview'])->middleware(['auth:sanctum', 'any.role:editor,admin,super_admin']);
//Параметры:
//id,
//$rules = [
//    'path_to_image' => 'required|string',
//    'title' => 'required|string',
//    'content' => 'required|string',
//    'source' => 'required|string',
//    'publication_date' => 'required|date_format:Y-m-d',
//    'sys_Comment' => 'nullable|string',
//    'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
//    'user_id' => 'nullable|integer|exists:users,id',
//];
//Возвращает и делает: Измененное интервью без изменения статуса
//Токен: Да

Route::put('editInterviewCensor/{id}', [PeopleContentController::class, 'editInterviewCensor'])->middleware(['auth:sanctum', 'role:censor']);
//Параметры:
//id,
//$rules = [
//    'path_to_image' => 'required|string',
//    'title' => 'required|string',
//    'content' => 'required|string',
//    'source' => 'required|string',
//    'publication_date' => 'required|date_format:Y-m-d',
//    'sys_Comment' => 'nullable|string',
//    'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
//    'user_id' => 'nullable|integer|exists:users,id',
//    'status' => 'required|string|exists:statuses,status',
//];
//Возвращает и делает: Измененное интервью цензором с любым допустимым статусом
//Токен: Да

Route::put('editInterviewForCheck/{id}', [PeopleContentController::class, 'editInterviewForCheck'])->middleware(['auth:sanctum', 'any.role:editor,admin,super_admin']);
//Параметры:
//id,
//$rules = [
//    'path_to_image' => 'required|string',
//    'title' => 'required|string',
//    'content' => 'required|string',
//    'source' => 'required|string',
//    'publication_date' => 'required|date_format:Y-m-d',
//    'sys_Comment' => 'nullable|string',
//    'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
//    'user_id' => 'nullable|integer|exists:users,id',
//];
//Возвращает и делает: Измененное интервью со статусом ожидает подтверждения
//Токен: Да

Route::put('editInterviewForCheckCensor/{id}', [PeopleContentController::class, 'editInterviewForCheckCensor'])->middleware(['auth:sanctum', 'role:censor']);
//Параметры:
//id,
//$rules = [
//    'path_to_image' => 'required|string',
//    'title' => 'required|string',
//    'content' => 'required|string',
//    'source' => 'required|string',
//    'publication_date' => 'required|date_format:Y-m-d',
//    'sys_Comment' => 'nullable|string',
//    'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
//    'user_id' => 'nullable|integer|exists:users,id',
//];
//Возвращает и делает: Измененное интервью цензором со статусом ожидает подтверждения
//Токен: Да

Route::put('editOpinion/{id}', [PeopleContentController::class, 'editOpinion'])->middleware(['auth:sanctum', 'any.role:editor,admin,super_admin']);
//Параметры:
//id,
//$rules = [
//    'path_to_image' => 'required|string',
//    'title' => 'required|string',
//    'content' => 'required|string',
//    'publication_date' => 'required|date_format:Y-m-d',
//    'sys_Comment' => 'nullable|string',
//    'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
//    'user_id' => 'nullable|integer|exists:users,id',
//];
//Возвращает и делает: Измененное мнение без изменения статуса
//Токен: Да

Route::put('editOpinionCensor/{id}', [PeopleContentController::class, 'editOpinionCensor'])->middleware(['auth:sanctum', 'role:censor']);
//Параметры:
//id,
//$rules = [
//    'path_to_image' => 'required|string',
//    'title' => 'required|string',
//    'content' => 'required|string',
//    'publication_date' => 'required|date_format:Y-m-d',
//    'sys_Comment' => 'nullable|string',
//    'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
//    'user_id' => 'nullable|integer|exists:users,id',
//    'status' => 'required|string|exists:statuses,status',
//];
//Возвращает и делает: Измененное мнение цензором с любым допустимым статусом
//Токен: Да

Route::put('editOpinionForCheck/{id}', [PeopleContentController::class, 'editOpinionForCheck'])->middleware(['auth:sanctum', 'any.role:editor,admin,super_admin']);
//Параметры:
//id,
//$rules = [
//    'path_to_image' => 'required|string',
//    'title' => 'required|string',
//    'content' => 'required|string',
//    'publication_date' => 'required|date_format:Y-m-d',
//    'sys_Comment' => 'nullable|string',
//    'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
//    'user_id' => 'nullable|integer|exists:users,id',
//];
//Возвращает и делает: Измененное мнение со статусом ожидает подтверждения
//Токен: Да

Route::put('editOpinionForCheckCensor/{id}', [PeopleContentController::class, 'editOpinionForCheckCensor'])->middleware(['auth:sanctum', 'role:censor']);
//Параметры:
//id,
//$rules = [
//    'path_to_image' => 'required|string',
//    'title' => 'required|string',
//    'content' => 'required|string',
//    'publication_date' => 'required|date_format:Y-m-d',
//    'sys_Comment' => 'nullable|string',
//    'regions_and_peoples_id' => 'required|integer|exists:regions_and_peoples,id',
//    'user_id' => 'nullable|integer|exists:users,id',
//];
//Возвращает и делает: Измененное мнение цензором со статусом ожидает подтверждения
//Токен: Да

Route::put('editPointView/{id}', [PeopleContentController::class, 'editPointView'])->middleware(['auth:sanctum', 'any.role:editor,admin,super_admin']);
//Устарел

Route::put('editPointViewForCheck/{id}', [PeopleContentController::class, 'editPointViewForCheck'])->middleware(['auth:sanctum', 'any.role:editor,admin,super_admin']);
//Устарел

Route::put('editUserForFields/{id}', [UserController::class, 'editUserForFields'])->middleware(['auth:sanctum', 'any.role:admin,super_admin']);
//Устарел

Route::put('editUserForRole/{id}', [UserController::class, 'editUserForRole'])->middleware(['auth:sanctum', 'any.role:admin,super_admin']);
//Устарел

Route::put('editUserForFull/{id}', [UserController::class, 'editUserForFull'])->middleware(['auth:sanctum', 'any.role:admin,super_admin']);
//Параметры:
//id,
//$rules = [
//    'login' => [
//        'required',
//        'string',
//        'max:255',
//        Rule::unique('users', 'login')->ignore($user->id),
//    ],
//    'password' => 'required|string|min:8|confirmed',
//    'fio' => ['required','string','max:255',new FullName()],
//    'phone' => ['required', 'string', 'max:15', Rule::unique('users', 'phone')->ignore($user->id), new PhoneNumber],
//    'sys_Comment' => 'nullable|string',
//    'roles' => 'required|array',
//    'roles.*' => 'string|exists:roles,name',
//    'delete_mark' => 'required|boolean',
//];
//Возвращает и делает: Измененный другой пользователь
//Токен: Да

//edit statuses

Route::put('editVideoByStatus/{id}', [VideoController::class, 'editVideoByStatus'])->middleware(['auth:sanctum', 'any.role:editor,admin,super_admin']);
//Параметры:
//id,
//$rules = [
//    'status' => 'required|string|exists:statuses,status',
//];
//Возвращает и делает: Изменение статуса у видео
//Токен: Да

Route::put('editNewsByStatus/{id}', [NewsController::class, 'editNewsByStatus'])->middleware(['auth:sanctum', 'any.role:editor,admin,super_admin']);
//Параметры:
//id,
//$rules = [
//    'status' => 'required|string|exists:statuses,status',
//];
//Возвращает и делает: Изменение статуса у новости
//Токен: Да

Route::put('editInterviewByStatus/{id}', [PeopleContentController::class, 'editInterviewByStatus'])->middleware(['auth:sanctum', 'any.role:editor,admin,super_admin']);
//Параметры:
//id,
//$rules = [
//    'status' => 'required|string|exists:statuses,status',
//];
//Возвращает и делает: Изменение статуса у интервью
//Токен: Да

Route::put('editOpinionByStatus/{id}', [PeopleContentController::class, 'editOpinionByStatus'])->middleware(['auth:sanctum', 'any.role:editor,admin,super_admin']);
//Параметры:
//id,
//$rules = [
//    'status' => 'required|string|exists:statuses,status',
//];
//Возвращает и делает: Изменение статуса у мнения
//Токен: Да

Route::put('editPointViewByStatus/{id}', [PeopleContentController::class, 'editPointViewByStatus'])->middleware(['auth:sanctum', 'any.role:editor,admin,super_admin']);
//Устарел
