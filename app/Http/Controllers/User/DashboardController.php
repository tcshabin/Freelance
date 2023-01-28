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
use App\Models\InstagramPost;
use App\Models\YoutubeReports;
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
    public function YoutubeAnalyse($token,$channel_id){

        if($channel_id == null){
            return false;
        }
        $api_key = config('services.google.api_key');
        $curl = curl_init();

        $start_date = date("Y-m-d",strtotime('-30 days'));
        $end_date = date("Y-m-d");

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://content-youtubeanalytics.googleapis.com/v2/reports?dimensions=day&startDate='.$start_date.'&ids=channel==MINE&metrics=views,subscribersGained,likes&endDate='.$end_date.'&key='.$api_key,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'x-origin: https://explorer.apis.google.com',
                'Authorization: Bearer '.$token
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $analyst = json_decode($response,true);
        
        if(isset($analyst['rows'])){
            foreach($analyst['rows'] as $report){
                $date = isset($report[0]) ? $report[0] : null;
                $views =  isset($report[1]) ? $report[1] : null;
                $sub_get = isset($report[2]) ? $report[2] : null;
                $likes = isset($report[3]) ? $report[3] : null;
                YoutubeReports::updateOrCreate(['channel_id'=>$channel_id,'date'=>$date],['views'=>$views,'likes'=>$likes,'sub_get'=>$sub_get,'date'=>$date,'channel_id'=>$channel_id,'response' =>$response ]);
            }
        }

        // average_engagement_rate start

        $curl = curl_init();

        $start_date = '2000-01-01';//date("Y-m-d",strtotime('-30 years'));
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://content-youtubeanalytics.googleapis.com/v2/reports?startDate='.$start_date.'&ids=channel==MINE&metrics=likes,dislikes,comments&endDate='.$end_date.'&key='.$api_key,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'x-origin: https://explorer.apis.google.com',
                'Authorization: Bearer '.$token
            ),
        ));

        $response2 = curl_exec($curl);
        curl_close($curl);
        $analyst2 = json_decode($response2,true);

       
        $total_likes = isset($analyst2['rows'][0][0]) ? $analyst2['rows'][0][0] : 0;
        $total_dislikes = isset($analyst2['rows'][0][1]) ? $analyst2['rows'][0][1] : 0;
        $total_comments = isset($analyst2['rows'][0][2]) ? $analyst2['rows'][0][2] : 0;
        $total_subscribers = Youtube::whereChannel_id($channel_id)->value('subscribers_count');
        $total_videos =  Youtube::whereChannel_id($channel_id)->value('video_count');
        if(!is_null($total_videos) && !is_null($total_subscribers) && $total_subscribers !=0){
            $total = ($total_likes+$total_dislikes+$total_comments)/$total_videos;
            $average = ($total/$total_subscribers)*100;
            Youtube::updateOrCreate(['channel_id'=>$channel_id],['average_engagement'=>$average]);
        }
        //average_engagement_end
        return true;
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

            self::InstagramPostUploads($oAuth->id,$accessToken);

            $url = 'user/instagram/summary/'.$oAuth->id;
            return redirect($url);
        }
        return redirect('user/dashboard')->with('error', 'Something Went Wrong');
    
    }
    public function InstagramPostUploads($instagram_id,$token){

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://graph.instagram.com/'.$instagram_id.'/media?fields=id,caption,media_type,media_url,thumbnail_url,permalink,timestamp&access_token='.$token,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $result2 = curl_exec($curl);

        curl_close($curl);
       
        if(!empty($result2)){
            $posts_data = json_decode($result2,true);

            if(!empty($posts_data)){
                foreach($posts_data as $data){
                    foreach($data as $post){
                        $media_id = isset($post['id']) ? $post['id'] : null;
                        $media_type = isset($post['media_type']) ? $post['media_type'] : null;
                        $link = isset($post['media_url']) ? $post['media_url'] : null; //permalink
                        $uploaded_at = isset($post['timestamp']) ? $post['timestamp'] : null;
                        if(!is_null($link)){
                            InstagramPost::updateOrCreate(['instagram_id'=>$instagram_id,'media_id'=>$media_id],['link'=>$link,'media_id'=>$media_id,'media_type'=>$media_type,'response'=>$result2,'uploaded_at'=>$uploaded_at]);
                        }
                    }
                }
            }
        }
        return true;
    }
    public function InstagramSummary($instagram_id){
        
        $instagram_posts = InstagramPost::whereInstagram_id($instagram_id)->latest()->take(7)->get();
        return view('user.instagram_summary',compact('instagram_id','instagram_posts'));
    }
    // Instagram End

    // Google Start

    public function redirectToGoogleProvider() // exactly google login
    {
       return Socialite::driver('google')->scopes(['https://www.googleapis.com/auth/youtube.readonly'])->redirect();
    }

    // public function ChannelCallback(Request $request)
    // {
    //     $data = array();
    //     $data['user_id'] = Auth::id();
    //     $data['channel_id'] = null;
    //     $data['channel_response'] = json_encode($request->all());
    //     if(!empty($data['channel_response'])){
    //         $result = json_decode($data['channel_response'],true);
    //         if(isset($result['body'])){
    //             $body =  json_decode($result['body'],true);
    //             $data['channel_id'] = isset($body['items'][0]['id']) ? $body['items'][0]['id'] : null;
    //             $data['subscribers_count'] = isset($body['items'][0]['statistics']['subscriberCount']) ? $body['items'][0]['statistics']['subscriberCount'] : null;
    //             $data['video_count'] = isset($body['items'][0]['statistics']['videoCount']) ? $body['items'][0]['statistics']['videoCount'] : null;
    //         }
            
    //         Youtube::updateOrCreate(['user_id'=>Auth::id(),'channel_id'=>$data['channel_id']],$data);
            
    //         return response()->json(['status'=>true,'channel_id'=>$data['channel_id'],'message' => 'Response Created Sucessfully..!']);
    //     }
    //     return response()->json(['status'=>false,'message' => 'No Response']);
    // } 


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
                $data['description'] =  isset($result['items'][0]['snippet']['description']) ? $result['items'][0]['snippet']['description'] : null;
            }
            
            Youtube::updateOrCreate(['user_id'=>Auth::id(),'channel_id'=>$data['channel_id']],$data);

            self::YoutubeAnalyse($google_user->token,$data['channel_id']);

            $url = 'user/youtube/summary/'.$data['channel_id'];
            return redirect($url);
        }
        return redirect()->back('/user/dashboard')->withErrors('No Channel Found/Something Went Wrong');
        
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
            $fields = urlencode('posts');
            curl_setopt($ch, CURLOPT_URL, 'https://graph.facebook.com/v15.0/me?fields=posts.limit(7)%7Blink%2Ctype%2Cobject_id%2Cshares%7D&access_token='.$accessToken);
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
                    $object_id = isset($post['object_id']) ? $post['object_id'] : null;
                    if(!is_null($link)){
                        FacebookPost::updateOrCreate(['facebook_id'=>$facebook['facebook_id'],'link'=>$link],['link'=>$link,'shares'=>$shares,'response'=>$result,'object_id'=>$object_id]);
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

    public function FacebookSummary($facebook_id){
        
        // $ch = curl_init();

        // curl_setopt($ch, CURLOPT_URL, 'https://graph.facebook.com/164483436331506/accounts?access_token=EAAKQwc9o9cMBAHLUosgbu4ZBKJkRAOAlx3PdZB9GPSeASkZBQvW5TNAybVBjFxGbR24hFHc48jqsCOsei51wTqNggikFOAWOTSVcZBsac37WZAkpGU4qjo9odQT6ZCxYkNAdqqDh7FpptCdYjjyGyZACcN6oJQsM1OgLBhlUR8XyvwaUZCixJkQFpZC4LnUzMI7aAQlpEMmyS4lIjmrYHGklqKieFnFZCpFpEZC7LYtx7ZBVAEtbwNDe4pANuavwRqZBreHgZD');
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $result = curl_exec($ch);
        // if (curl_errno($ch)) {
        //     echo 'Error:' . curl_error($ch);
        // }
        // $curl = curl_init();

        // curl_setopt_array($curl, array(
        //     CURLOPT_URL => 'https://graph.facebook.com/164483436331506/accounts?access_token=EAAKQwc9o9cMBAC2AwxPU4ZAoMCZC50BuMs88lZB8vbZBZA1yXJZCIM6htdBU7YHDdPs3XZAPCFeZByEcSNJdZCmjQTaBmy7tyIrmnZBaFg76sny9uYtg77ZAprmcZBmVzZC5ew6Fcwd3Cb7BYEDrG09rlUZAtWCLzZCgqFCHC6FPSeBEg7NUFbeTa4lz1O7YOauNCLzxyD0TiTDirr1Eljc4kzq4dBQr09NZAWsZCJkKF6TPavJpR4r6AGQJeBTbnZBXP21ZCGM2SsZD',
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_ENCODING => '',
        //     CURLOPT_MAXREDIRS => 10,
        //     CURLOPT_TIMEOUT => 0,
        //     CURLOPT_FOLLOWLOCATION => true,
        //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //     CURLOPT_CUSTOMREQUEST => 'GET',
        // ));

        // $response = curl_exec($curl);
        // dd($response);
        // curl_close($ch);

        $facebook_posts = FacebookPost::whereFacebook_id($facebook_id)->latest()->take(7)->get();

        $token = Facebook::whereFacebook_id($facebook_id)->latest()->value('access_token');

        // loops to get total likes
        foreach($facebook_posts as $posts){

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://graph.facebook.com/v15.0/'.$posts->object_id.'/likes?access_token='.$token.'&summary=true&limit=25&after=QVFIUmp6MXVRaEhOWWxGV2cwT3h3VE9KclpHejFvMF9zaU9hUFNYVGs5LWQxZAW5rNDRmZAHdpRmM2ZADdwbWtnbkY5SVpKME9ZAc1ZA5a3VGZAXF3bmh2bWhMUWV3',
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
            if(!empty($response)){
                $result = json_decode($response,true);
                $likes = isset($result['summary']['total_count']) ? $result['summary']['total_count'] : null;
                if(!is_null($likes)){
                    FacebookPost::updateOrCreate(['facebook_id'=>$facebook_id,'object_id'=>$posts->object_id],['likes'=>$likes]);
                }
            }
        }
        $facebook_posts = FacebookPost::whereFacebook_id($facebook_id)->latest()->take(7)->get();
        
        return view('user.facebook_summary',compact('facebook_posts'));
    }

    // Facebook End
  
   

}
