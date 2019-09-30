<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/chart/sample', function () {
    return view('charts.samples.index');
});

Route::get('/chart/sample/trend', function () {
    return view('charts.samples.sample-trend');
});

Route::get('/chart/sample/pie', function () {
    return view('charts.samples.sample-pie');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/logout', 'HomeController@logout')->name('bye');
Route::get('/users', 'UserController@index')->middleware('auth')->name('show_registered_users');
Route::get('/users/set_to_admin/{id}', 'UserController@set_to_admin')->middleware('auth');
Route::get('/users/set_to_normal/{id}', 'UserController@set_to_normal')->middleware('auth');
Route::get('/drugs', 'DrugController@index')->name('show_drug_list');
Route::post('/drugs', 'DrugController@show_specified');

Route::get('/medications/maint_master', 'MedicationController@maint_master');
Route::get('/medications/convert', 'MedicationController@convert');

Route::get('/dictionary', 'DrugDictionaryController@index')->name('dictionary_menu')->middleware('auth');
Route::get('/dictionary/upload', 'DrugDictionaryController@create')->middleware('auth');
Route::post('/dictionary/upload', 'DrugDictionaryController@store')->name('store_dictionary')->middleware('auth');
Route::post('/dictionary/search', 'DrugDictionaryController@search')->name('search_dictionary');
Route::get('/dictionary/list/{em_rank}', 'DrugDictionaryController@list')->middleware('auth');
Route::get('/dictionary/check_em_rank/{yj9}', 'DrugDictionaryController@check_kk');
Route::get('/dictionary/em_rank/update', 'DrugDictionaryController@update_userdrug_emrank');

Route::get('/user_info','UserInfoController@index')->name('user_info')->middleware('auth');
Route::get('/user_info/summary','UserInfoController@summary')->name('user_info_summary');
Route::get('/user_info/show/{user_id}','UserInfoController@show')->middleware('auth');
Route::get('/user_info/distribution', 'UserInfoController@distribution')->name('distribution')->middleware('auth');
Route::get('/user_info/list/{em_rank}', 'UserInfoController@list_by_emrank')->middleware('auth');
Route::get('/kisyo', 'UserInfoController@get_kisyo')->name('get_kisyo');
Route::post('/kisyo/detail', 'UserInfoController@show_kisyo_detail');

Route::get('/user_condition', 'UserConditionController@index');

Route::get('/applog', 'UserInfoController@applog')->name('applog')->middleware('auth');

Route::get('/survey', 'SurveyController@index');
Route::post('/survey', 'SurveyController@index');
Route::post('/survey/answer', 'SurveyController@store');
Route::get('/survey/stat', 'SurveyController@stat')->name('survey_stat')->middleware('auth');

Route::get('/phone', 'UserInfoController@phone');

Route::get('/user_condition/maptest', 'UserConditionController@maptest');

