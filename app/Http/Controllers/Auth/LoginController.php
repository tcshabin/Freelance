<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\User;
use App\Models\Facebook;
use App\Models\Youtube;
use Auth;
use Hash;
use Validator;

class LoginController extends Controller
{
  
    public function Login(){

        if(Auth::check()){
            return redirect('user/dashboard');
        }

        $title = 'Login-Page';
        return view('auth.login',compact('title'));
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

    // Youtube Start

    public function redirectToYoutubeProvider(Request $request)
    {
        if(Auth::check()){
            return redirect('user/dashboard');
        }
        $name = 'fake'.User::max('id');
        
        $data = array();
        $youtube = array();
        $data['username'] = $name;
        $data['password'] = Hash::make('123456');
        $data['email'] = $name.'@gmail.com';
        User::updateOrCreate(['email'   => $data['email']],$data);

        $user = User::whereEmail($data['email'])->select('id','phone')->first();

        $youtube['user_id'] = $user->id;
        $youtube['youtube_id'] = '23444';
        $youtube['access_token'] = 'wqd233d3';
        Youtube::updateOrCreate(['user_id'=>$youtube['user_id']],$youtube);

        if(is_null($user->phone)){
            $redirect_url = '/update_profile/'.encrypt($user->id);
            return redirect($redirect_url);
        }
        Auth::loginUsingId($user->id);
        return redirect('user/dashboard');

    } 
    // Youtube End

    // Facebook Start
    public function redirectToFacebookProvider(Request $request)
    {
        if(Auth::check()){
            return redirect('user/dashboard');
        }
        return Socialite::driver('facebook')->redirect();

    } 

    public function FacebookProviderCallback(Request $request)
    {
        if(Auth::check()){
            return redirect('user/dashboard');
        }

        try {
            $facebook_user = Socialite::driver('facebook')->user();
            $data = array();
            $facebook = array();
            $data['username'] = $facebook_user->getName();
            $data['password'] = Hash::make('123456');
            $data['email'] = $facebook_user->getEmail();

            User::updateOrCreate(['email'   => $data['email']],$data);
            $user = User::whereEmail($data['email'])->select('id','phone')->first();
            
            $facebook['user_id'] = $user->id;
            $facebook['facebook_id'] = $facebook_user->getId();

            Facebook::updateOrCreate(['user_id'=>$facebook['user_id']],$facebook);

            if(is_null($user->phone)){
                $redirect_url = '/update_profile/'.encrypt($user->id);
                return redirect($redirect_url);
            }
            Auth::loginUsingId($user->id);
            return redirect('user/dashboard');
      
        } catch (\Throwable $th) {
               throw $th;
               return redirect('/login');
        }
    }

    // Facebook End
    public function UpdateProfile(Request $request,$encrypted_id){

        if(Auth::check()){
            return redirect('user/dashboard');
        }
        $id = decrypt($encrypted_id);
        $user = User::whereId($id)->first();

        if(is_null($user)){
            abort(404);
        }

        if ($request->isMethod('get')) {
            return view('auth.update_profile',compact('encrypted_id'));
        }
        
        $validator = $this->validator($request->all());
      
        if ($validator->fails()) {        
           return redirect()->back()->withErrors($validator);         
        }
        if($request->has('file')){
            $file = request()->file('file'); 
            $destinationPath = storage_path("app/public/profile_image"); 
            $filename = time() . '.' . $file->getClientOriginalName();
            $upload_success = $file->move($destinationPath, $filename);
        }
        $data['phone'] = $request->phone;
        $data['profile_image'] = isset($filename) ? $filename : null;
        User::whereId($id)->update($data);

        Auth::loginUsingId($id);
        return redirect('user/dashboard');
    }  

    protected function validator(array $data)
    {
        return Validator::make($data, [  //Login information
            'file' => 'required|image',
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/',

        ]);

    }
    public function Logout(){
        
        if(Auth::check()){
            Auth::logout();
        }
        return redirect('/login');
    }
   

}
