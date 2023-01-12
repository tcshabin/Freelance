<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\User;
use App\Models\Facebook;
use App\Models\Youtube;
use App\Models\Google;
use Auth;

class DashboardController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
    public function Dashboard(){

        $title = 'Dashboard';
        return view('user.dashboard',compact('title'));
    }

     // Instagram Start
    public function redirectToInstagramProvider()
    {
        $appId = config('services.instagram.client_id');
        $redirectUri = urlencode(config('services.instagram.redirect'));
        return redirect()->to("https://api.instagram.com/oauth/authorize?app_id={$appId}&redirect_uri={$redirectUri}&scope=user_profile,user_media&response_type=code");
    }
    
    public function instagramProviderCallback(Request $request)
    {
        $code = $request->code;
        if (empty($code)) return redirect()->route('home')->with('error', 'Failed to login with Instagram.');
    
        $appId = config('services.instagram.client_id');
        $secret = config('services.instagram.client_secret');
        $redirectUri = config('services.instagram.redirect');
    
        $client = new Client();
        // Get access token
        $response = $client->request('POST', 'https://api.instagram.com/oauth/access_token', [
            'form_params' => [
                'app_id' => $appId,
                'app_secret' => $secret,
                'grant_type' => 'authorization_code',
                'redirect_uri' => $redirectUri,
                'code' => $code,
            ]
        ]);
    
        if ($response->getStatusCode() != 200) {
            return redirect()->route('home')->with('error', 'Unauthorized login to Instagram.');
        }
    
        $content = $response->getBody()->getContents();
        $content = json_decode($content);
    
        $accessToken = $content->access_token;
        $userId = $content->user_id;
    
        // Get user info
        $response = $client->request('GET', "https://graph.instagram.com/me?fields=id,username,account_type&access_token={$accessToken}");
    
        $content = $response->getBody()->getContents();
        $oAuth = json_decode($content);
    
        // Get instagram user name 
        $username = $oAuth->username;
    
        dd($oAuth);
        // do your code here
    }
    // Instagram End

    // Google Start

    public function redirectToGoogleProvider() // exactly google login
    {
        return Socialite::driver('google')->redirect();
    }


    public function GoogleProviderCallback(Request $request)
    {
        if(Auth::check()){
            return redirect('user/dashboard');
        }

        $google_user =  Socialite::driver('google')->stateless()->user();
        //$userSocial->getEmail()
        $name = 'fake'.User::max('id');
        
        $data = array();
        $google = array();
        // $data['username'] = $google_user->getName();
        // $data['email'] = $google_user->getEmail();
      
        $google['user_id'] = Auth::id();
        $google['google_id'] = '23444';
        $google['access_token'] = 'wqd233d3';
        Google::updateOrCreate(['user_id'=>$google['user_id']],$google);

        dd('needs of youtube api');

    } 
    // Google End

    // Facebook Start
    public function redirectToFacebookProvider(Request $request)
    {
        return Socialite::driver('facebook')->redirect();

    } 

    public function FacebookProviderCallback(Request $request)
    {
        try {
            $facebook_user = Socialite::driver('facebook')->user();
            $data = array();
            $facebook = array();
            //$data['username'] = $facebook_user->getName();
            //$data['email'] = $facebook_user->getEmail();
            $facebook['user_id'] = Auth::id();
            $facebook['facebook_id'] = $facebook_user->getId();

            Facebook::updateOrCreate(['user_id'=>$facebook['user_id']],$facebook);

            dd('needs of facebook');
            //return redirect('user/dashboard');
      
        } catch (\Throwable $th) {
               throw $th;
               return redirect('/user/dashboard');
        }
    }

    // Facebook End
  
   

}
