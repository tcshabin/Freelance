<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\User;
use App\Models\Facebook;
use App\Models\FacebookPost;
use App\Models\Youtube;
use App\Models\Google;
use App\Models\Videos;
use App\Models\Instagram;
use Google\Service\YouTube as YouTubeClient;
use Amirsarhang\Instagram as InstagramPkg;
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
    public function YoutubeSummary($channel_id){

        $api_key = config('services.google.api_key');
        // $channel_id = Youtube::whereUser_id(AUth::id())->latest()->value('channel_id');
        if(!is_null($channel_id)){
            $latest_videos_ids = self::VideosId($api_key,$channel_id,7); //get latest 7 videos_id
            $latest_videos_details = self::VideosDetails($channel_id,$api_key,$latest_videos_ids);
            $new_videos = Videos::whereChannel_id($channel_id)->latest()->take(7)->get();
        }
        if(!isset($new_videos)){
            $new_videos = array();
        }
        $channels = Youtube::whereChannel_id($channel_id)->first();
        return view('user.youtube_summary',compact('new_videos','channels'));
       
    }
    public function FacebookSummary($facebook_id){

        $facebook_posts = FacebookPost::whereFacebook_id($facebook_id)->latest()->take(7)->get();
        return view('user.facebook_summary',compact('facebook_posts'));
    }
    public function VideosId($api_key,$channel_id,$maxresult=1000){

        $videoids = array();
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://www.googleapis.com/youtube/v3/search?key='.$api_key.'&channelId='.$channel_id.'&maxResults='.$maxresult.'&part=snippet,id&order=date&maxResults=7',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        
        $responce_card = json_decode($response,true);
        if(isset($responce_card['items'])){
            foreach($responce_card['items'] as $video){
                if(isset($video['id']['videoId'])){
                    $videoids[] = $video['id']['videoId'];
                }
            }
        }
        $list = implode(',', $videoids);
        return $list;
    }
    public function VideosDetails($channel_id,$api_key,$videos_ids){

        $statistics = array();
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://youtube.googleapis.com/youtube/v3/videos?part=statistics&key='.$api_key.'&id='.$videos_ids,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        
        $responce_card = json_decode($response,true);
        if(isset($responce_card['items'])){
            foreach($responce_card['items'] as $videodetails){
                if(isset($videodetails['id']) && isset($videodetails['statistics'])){//['statistics']
                    $statistics[$videodetails['id']] = $videodetails['statistics'];
                    
                }
            }
        }
       
        if(!empty($statistics)){
            foreach ($statistics as $key => $value) {
                Videos::updateOrCreate(['video_id'=>$key],['type'=>'youtube','view_count'=>$value['viewCount'],'like_count'=>$value['likeCount'],'video_id'=>$key,'channel_id'=>$channel_id]);
            }
        }
       return $statistics;
    }

     // Instagram Start
    public function redirectToInstagramProvider()
    {
        //return Socialite::driver('instagram')->scopes(['user_profile','user_media'])->redirect();
        
        $appId = config('services.instagram.client_id');
        $redirectUri = urlencode(config('services.instagram.redirect'));
        
        return redirect()->to("https://api.instagram.com/oauth/authorize?app_id={$appId}&redirect_uri={$redirectUri}&scope=user_profile,user_media&response_type=code");
    }
    
    public function instagramProviderCallback(Request $request)
    {
        $code = $request->code;
        if (empty($code)) return redirect('user/dashboard')->with('error', 'Failed to login with Instagram.');
    
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
            return redirect('user/dashboard')->with('error', 'Unauthorized login to Instagram.');
        }
    
        $content = $response->getBody()->getContents();
        $content = json_decode($content);
    
        $accessToken = $content->access_token;
        $userId = $content->user_id;
    
        // Get user info
        $response = $client->request('GET', "https://graph.instagram.com/me?fields=id,username,account_type&access_token={$accessToken}");
    
        $content = $response->getBody()->getContents();
        $oAuth = json_decode($content);

        if(isset($oAuth->username)){
            $data = array();
            $data['access_token'] = $accessToken;
            $data['insta_id'] = $oAuth->id;
            $data['user_id'] = Auth::id();
            Instagram::updateOrCreate(['user_id'=>Auth::id(),'insta_id'=>$oAuth->id],$data);
            $url = 'user/instagram/summary/'.$data['insta_id'];
            return redirect($url);
        }
        return redirect('user/dashboard')->with('error', 'Something Went Wrong');
    
    }
    public function InstagramSummary($instagram_id){

        // $ch = curl_init();

        // curl_setopt($ch, CURLOPT_URL, 'https://graph.facebook.com/v15.0/2130038143865586?fields=biography%2Cid%2Cusername%2Cwebsite&access_token=EAAKQwc9o9cMBAPZB1RGH8dPVwcTikhpZAuHnBHl9zrxKz271Aa4st69iEihRVsrpZCzGn9B3ucPe8Du93KHIYRz3KWWZCr1fzvSGu2H2Hp8lYN4woOIO6xTnZB6hRoCR9n9a98noobQWvoZB8jpIKLh1t3kl0ZBj1GumZBhCVMoBul9RdQhLefxhBoNZBIRxK7M230eZB8OIM0QAWXymcZBXSfvMzttb0ZCdNOewzNVZCF8ZBZBkZBmLv3ZAa8BY4');
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $result = curl_exec($ch);
        // if (curl_errno($ch)) {
        //     echo 'Error:' . curl_error($ch);
        // }
        // curl_close($ch);
        // dd($result);
        
        return view('user.instagram_summary',compact('instagram_id'));
    }
    // Instagram End

    // Google Start

    public function redirectToGoogleProvider() // exactly google login
    {
       return Socialite::driver('google')->scopes(['https://www.googleapis.com/auth/youtube.readonly'])->redirect();
    }

    public function ChannelCallback(Request $request)
    {
        $data = array();
        $data['user_id'] = Auth::id();
        $data['channel_id'] = null;
        $data['channel_response'] = json_encode($request->all());
        if(!empty($data['channel_response'])){
            $result = json_decode($data['channel_response'],true);
            if(isset($result['body'])){
                $body =  json_decode($result['body'],true);
                $data['channel_id'] = isset($body['items'][0]['id']) ? $body['items'][0]['id'] : null;
                $data['subscribers_count'] = isset($body['items'][0]['statistics']['subscriberCount']) ? $body['items'][0]['statistics']['subscriberCount'] : null;
                $data['video_count'] = isset($body['items'][0]['statistics']['videoCount']) ? $body['items'][0]['statistics']['videoCount'] : null;
            }
            
            Youtube::updateOrCreate(['user_id'=>Auth::id(),'channel_id'=>$data['channel_id']],$data);
            
            return response()->json(['status'=>true,'channel_id'=>$data['channel_id'],'message' => 'Response Created Sucessfully..!']);
        }
        return response()->json(['status'=>false,'message' => 'No Response']);
    } 


    public function GoogleProviderCallback(Request $request) //youtube response geting function
    {
        $google_user =  Socialite::driver('google')->stateless()->user();

        if(!isset($google_user->token)){
            return redirect()->back('/user/dashboard')->withErrors('Token Mismatch');
        }
        // $google_data['username'] = $google_user->getName();
        // $google_data['email'] = $google_user->getEmail();
        $google['user_id'] = Auth::id();
        $google['google_id'] = $google_user->getId();
        $google['access_token'] = $google_user->token;

        Google::updateOrCreate(['user_id'=>$google['user_id']],$google);
      
        $api_key = config('services.google.api_key');
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://content-youtube.googleapis.com/youtube/v3/channels?mine=true&part=snippet,statistics&key='.$api_key,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '.$google_user->token,
            'x-origin: https://explorer.apis.google.com'
        ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
       
        $data = array();
        $data['user_id'] = Auth::id();
        $data['channel_id'] = null;
        $data['channel_response'] = $response;
        if(!empty($data['channel_response'])){
            $result = json_decode($data['channel_response'],true);
           
            if(isset($result['items'])){
                $data['channel_id'] = isset($result['items'][0]['id']) ? $result['items'][0]['id'] : null;
                $data['subscribers_count'] = isset($result['items'][0]['statistics']['subscriberCount']) ? $result['items'][0]['statistics']['subscriberCount'] : null;
                $data['video_count'] = isset($result['items'][0]['statistics']['videoCount']) ? $result['items'][0]['statistics']['videoCount'] : null;
            }
            
            Youtube::updateOrCreate(['user_id'=>Auth::id(),'channel_id'=>$data['channel_id']],$data);

            $url = 'user/youtube/summary/'.$data['channel_id'];
            return redirect($url);
        }
        return redirect()->back('/user/dashboard')->withErrors('No Channel Found/Something Went Wrong');
        
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
            
            $facebook['login_response'] = json_encode($facebook_user);

            $ch = curl_init();
            $encode = json_encode($facebook_user,true);
            $decode = json_decode($encode,true);
          
            $accessToken = $decode['token'];//'EAAKQwc9o9cMBABwPb62HTjwqb8ZArnnWSXSA6ctW1wOT3f9oPfqODPVrm6nMbEH0WChFQEZBq2qzEsToa8IJIss78PCx09WcdAiJg5kOTZC3FBqj99WseiU0FWZBqpAVGWp9ZB16HB2pViZBC53Ko68ZCoSai5kJoHyXkiqvNAKpKtoyBvQHVQPVAlY3gXCiUJNhPRpqMFBADzmOucSqsK0';
            $facebook['access_token'] =$accessToken;
            
            curl_setopt($ch, CURLOPT_URL, 'https://graph.facebook.com/v15.0/me?fields=posts.limit(7)%7Blink%2Cshares%7D&access_token='.$accessToken);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }
            curl_close($ch);

            Facebook::updateOrCreate(['user_id'=>$facebook['user_id']],$facebook);

            if(!empty($result)){
                $response = json_decode($result,true);
                $posts_data = isset($response['posts']['data']) ? $response['posts']['data'] : [];
                
                foreach($posts_data as $post){
                    $link = isset($post['link']) ? $post['link'] : null;
                    $shares = isset($post['shares']['count']) ? $post['shares']['count'] : null;
                    if(!is_null($link)){
                        FacebookPost::updateOrCreate(['facebook_id'=>$facebook['facebook_id'],'link'=>$link],['link'=>$link,'shares'=>$shares,'response'=>$result]);
                    }
                }
            }
            $url = 'user/facebook/summary/'.$facebook['facebook_id'];
            return redirect($url);
        } catch (\Throwable $th) {
               throw $th;
               return redirect()->back('/user/dashboard')->withErrors('Something Went Wrong');
        }
    }

    // Facebook End
  
   

}
