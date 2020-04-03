<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/clear_cache', function() {
    $exitCode = Artisan::call('config:clear');
    $exitCode = Artisan::call('cache:clear');
    $exitCode = Artisan::call('config:cache');
    echo "done";
});
//userController
Route::post('/toDoLists','UserController@toDoLists');
Route::get('/listTasks/{id}','UserController@listTasks');
Route::post('/listCheck','UserController@listCheck');
Route::post('/listDone','UserController@listDone');
Route::post('/toDoList/listSave','UserController@list_save');
Route::get('/user/{id}','UserController@userInfo');
Route::post('/user_info','UserController@user_info');
Route::post('/user/saveProfile','UserController@save_profile');


Route::post('/admin/users','AdminController@users');
Route::post('/admin/user/search','AdminController@user_search');
Route::post('/admin/user/tasks/done','AdminController@user_tasks_done');
Route::post('/admin/user/tasks/check','AdminController@user_tasks_check');
Route::post('/admin/user/info','AdminController@user_info');
Route::post('/admin/user/confirm','AdminController@user_confirm');
Route::post('/admin/user/delete','AdminController@user_delete');
Route::post('/admin/user/task/new','AdminController@new_task');
Route::post('/admin/checklist','AdminController@checkList');


//authController
Route::post('/login','AuthController@login');
Route::get('/positions','AuthController@positions');
Route::post('/submit','AuthController@submit');
Route::post('/verf_confirm','AuthController@verf_confirm');
Route::post('/resend_code','AuthController@resend_code');
Route::post('/checkEmail','AuthController@check_email');





