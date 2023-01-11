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

Route::get('login','App\Http\Controllers\Auth\LoginController@Login')->name('login');
Route::get('logout', 'App\Http\Controllers\Auth\LoginController@Logout')->name('logout');
// insta start
Route::get('login/instagram','App\Http\Controllers\Auth\LoginController@redirectToInstagramProvider')->name('instagram.login');
Route::get('login/instagram/callback', 'App\Http\Controllers\Auth\LoginController@instagramProviderCallback')->name('instagram.login.callback');
// insta end

// google start
Route::get('login/google','App\Http\Controllers\Auth\LoginController@redirectToGoogleProvider')->name('google.login');
Route::get('login/google/callback', 'App\Http\Controllers\Auth\LoginController@GoogleProviderCallback')->name('google.login.callback');
// google end

// // youtube start
// Route::get('login/youtube','App\Http\Controllers\Auth\LoginController@redirectToYoutubeProvider')->name('youtube.login');
// Route::get('login/youtube/callback', 'App\Http\Controllers\Auth\LoginController@YoutubeProviderCallback')->name('youtube.login.callback');
// // youtube end

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