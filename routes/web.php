<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

Route::get('/', function (Request $request) {
    return redirect('/register');
    //Log::info('sss');
  
});
Route::get('register','App\Http\Controllers\Auth\RegisterController@Register')->name('register');
Route::post('register','App\Http\Controllers\Auth\RegisterController@Register');

Route::get('login','App\Http\Controllers\Auth\LoginController@Login')->name('login');
Route::post('login','App\Http\Controllers\Auth\LoginController@Login');
Route::get('logout', 'App\Http\Controllers\Auth\LoginController@Logout')->name('logout');
// // youtube start
// Route::get('login/youtube','App\Http\Controllers\Auth\LoginController@redirectToYoutubeProvider')->name('youtube.login');
// Route::get('login/youtube/callback', 'App\Http\Controllers\Auth\LoginController@YoutubeProviderCallback')->name('youtube.login.callback');
// // youtube end

Route::get('update_profile/{encrypted_id}','App\Http\Controllers\Auth\LoginController@UpdateProfile');
Route::post('update_profile/{encrypted_id}','App\Http\Controllers\Auth\LoginController@UpdateProfile');

Route::group(['prefix' => 'user', 'middleware' => 'auth'], function () {

    Route::get('dashboard', 'App\Http\Controllers\User\DashboardController@Dashboard');

    // insta start
    Route::get('instagram','App\Http\Controllers\User\DashboardController@redirectToInstagramProvider')->name('instagram.user');
    Route::get('instagram/callback', 'App\Http\Controllers\User\DashboardController@instagramProviderCallback')->name('instagram.user.callback');
    Route::get('instagram/summary/{instagram_id}', 'App\Http\Controllers\User\DashboardController@InstagramSummary')->name('instagram.user.summary');
    // insta end
    
    // google start
    Route::get('google','App\Http\Controllers\User\DashboardController@redirectToGoogleProvider')->name('google.user');
    Route::get('google/callback', 'App\Http\Controllers\User\DashboardController@GoogleProviderCallback')->name('google.user.callback');
    // google end

    // youtube start
    Route::get('youtube/summary/{channel_id}', 'App\Http\Controllers\User\DashboardController@YoutubeSummary')->name('youtube.user.summary');
    Route::post('youtube/channel_call_back', 'App\Http\Controllers\User\DashboardController@ChannelCallback')->name('youtube.user.callback');
    // youtube end
    
    // facebook start
    Route::get('facebook','App\Http\Controllers\User\DashboardController@redirectToFacebookProvider')->name('facebook.user');
    Route::get('facebook/callback', 'App\Http\Controllers\User\DashboardController@FacebookProviderCallback')->name('facebook.user.callback');
    Route::get('facebook/summary/{facebook_id}', 'App\Http\Controllers\User\DashboardController@FacebookSummary')->name('facebook.user.summary');
    // facebook end

});     

// http://employee.demo/insta/callback

// pro 

// login influence

// phon
// login ,register