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
    // insta end
    
    // google start
    Route::get('google','App\Http\Controllers\User\DashboardController@redirectToGoogleProvider')->name('google.user');
    Route::get('google/callback', 'App\Http\Controllers\User\DashboardController@GoogleProviderCallback')->name('google.user.callback');
    // google end
    
    // facebook start
    Route::get('facebook','App\Http\Controllers\User\DashboardController@redirectToFacebookProvider')->name('facebook.user');
    Route::get('facebook/callback', 'App\Http\Controllers\User\DashboardController@FacebookProviderCallback')->name('facebook.user.callback');
    // facebook end

});     

// http://employee.demo/insta/callback

// pro 

// login influence

// phon
// login ,register