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
use App\Models\Google;
use Auth;
use Hash;
use Validator;

class LoginController extends Controller
{
  
    public function Login(Request $request){

        if(Auth::check()){
            return redirect('user/dashboard');
        }
        if ($request->isMethod('post')) {
            $user = User::whereEmail($request->email)->select('id','password')->first(); 
            if(isset($user->id)){
                if (!Hash::check($request->password, $user->password)){
                    return redirect()->back()->withErrors('These credentials do not match our records');
                }
                Auth::loginUsingId($user->id);
                return redirect('user/dashboard');
            }
            
        }
        $title = 'Login-Page';
        return view('auth.login',compact('title'));
    }
   
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
