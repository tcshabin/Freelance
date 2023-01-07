<?php

use Illuminate\Support\Facades\Route;

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
    return redirect('/register');
});

Route::get('/tasks', 'App\Http\Controllers\TaskController@Index');
Route::get('/tasks_data', 'App\Http\Controllers\TaskController@Data');
Route::get('/create', 'App\Http\Controllers\TaskController@Create');
Route::post('/store', 'App\Http\Controllers\TaskController@Store');
Route::get('/tasks/{id}', 'App\Http\Controllers\TaskController@Show');
Route::put('/update-task/{id}', 'App\Http\Controllers\TaskController@Update');
Route::delete('/task/{id}', 'App\Http\Controllers\TaskController@Destroy')->name('task.destroy');
Route::get('priority_update', 'App\Http\Controllers\TaskController@PriorityUpdate');

Route::get('login','App\Http\Controllers\Auth\LoginController@Login')->name('login');
Route::get('logout', 'App\Http\Controllers\Auth\LoginController@Logout')->name('logout');
// insta start
Route::get('login/instagram','App\Http\Controllers\Auth\LoginController@redirectToInstagramProvider')->name('instagram.login');
Route::get('login/instagram/callback', 'App\Http\Controllers\Auth\LoginController@instagramProviderCallback')->name('instagram.login.callback');
// insta end

// facebook start
Route::get('login/facebook','App\Http\Controllers\Auth\LoginController@redirectToFacebookProvider')->name('facebook.login');
Route::get('login/facebook/callback', 'App\Http\Controllers\Auth\LoginController@FacebookProviderCallback')->name('facebook.login.callback');
// facebook end

Route::get('update_profile/{encrypted_id}','App\Http\Controllers\Auth\LoginController@UpdateProfile');
Route::post('update_profile/{encrypted_id}','App\Http\Controllers\Auth\LoginController@UpdateProfile');

Route::group(['prefix' => 'user', 'middleware' => 'auth'], function () {

    Route::get('dashboard', 'App\Http\Controllers\User\DashboardController@Dashboard');
});     

// http://employee.demo/insta/callback