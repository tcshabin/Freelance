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
use File;
use Http;
use Google_Client;

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
        $google_user =  Socialite::driver('google')->stateless()->user();
       
        //dd($google_user);
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

        $api_key = config('services.google.api_key');
        $client_id = config('services.google.client_id');

        $apiKey = $api_key;
        $channelId = 'UCsUMdB77PBRR0bjWjOyrAVA';
        $resultsNumber = 10;

        $channel_id = $channelId;
        $api_key = $api_key;
        $api_response = file_get_contents('https://www.googleapis.com/youtube/v3/channels?part=statistics&id='.$channel_id.'&fields=items/statistics/subscriberCount&key='.$api_key);
        $api_response_decoded = json_decode($api_response, true);
        echo $api_response_decoded['items'][0]['statistics']['subscriberCount'];
           dd(11,$api_response_decoded);     
        $scope = urlencode('https://www.googleapis.com/auth/youtube.readonly');
        //dd($scope)
        $requestUrl = 'https://youtube.googleapis.com/youtube/v3/channels?part=snippet&mine=true&key=AIzaSyA2E5FFRuCCtrpEhDnd4UajpqGhW6-RAi0&SCOPES='.$scope;
        // if( function_exists( file_get_contents ) ) {
        //     $response = file_get_contents( $requestUrl );
        //     $json_response = json_decode( $response, TRUE );
             
        // } else {
            // No file_get_contents? Use cURL then...
            $authorization = 'Authorization: Bearer '.$google_user->token;
            if( function_exists( 'curl_version' ) ) {
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
                curl_setopt( $curl, CURLOPT_URL, $requestUrl );
                curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
                curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, TRUE );
                $response = curl_exec( $curl );
                curl_close( $curl );
                $json_response = json_decode( $response, TRUE );
                 
            } else {
                // Unable to get response if both file_get_contents and cURL fail
                $json_response = NULL;
            }
        //}
        dd($json_response);
        $curl = curl_init();
        $scope = urlencode('https://www.googleapis.com/auth/youtube.readonly');
        //dd($scope);

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://youtube.googleapis.com/youtube/v3/channels?part=snippet&mine=true&key=AIzaSyA2E5FFRuCCtrpEhDnd4UajpqGhW6-RAi0',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            'Authorization: Bearer '.$google_user->token,
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
         dd($response);

        $ch = curl_init();

        // curl_setopt($ch, CURLOPT_URL, 'https://youtube.googleapis.com/youtube/v3/search?part=snippet&key='.$api_key);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        // curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

        // $headers = array();
        // $headers[] = 'Accept: application/json';
        // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // $result = curl_exec($ch);
        
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://youtube.googleapis.com/youtube/v3/subscriptions?part=snippet&channelId=UCsUMdB77PBRR0bjWjOyrAVA&key='.$api_key);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

        $headers = array();
        $headers[] = 'Authorization: Bearer '.$client_id;
        $headers[] = 'Accept: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        dd($result);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

      
        // $part = 'snippet';
        // $country = 'BD';
        // $apiKey = config('services.google.api_key');
        // $maxResults = 12;
        // $youTubeEndPoint = config('services.google.search_endpoint');
        // $type = 'video'; // You can select any one or all, we are getting only videos
        // $keywords='';
        // $url = "$youTubeEndPoint?part=$part&maxResults=$maxResults&regionCode=$country&type=$type&key=$apiKey&q=$keywords";
        // $response = Http::get($url);
        // $results = json_decode($response);
        // // We will create a json file to see our response
        // File::put(storage_path() . '/app/public/results.json', $response->body());
        // return $results;

        // dd('needs of youtube api');

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
