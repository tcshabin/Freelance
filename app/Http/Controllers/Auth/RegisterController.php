<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Validator;
use Hash;

class RegisterController extends Controller
{
    protected function validator(array $data)
    {
        return Validator::make($data, [  //Login information
            // 'file' => 'required|image',
            'username' => 'required|max:255|unique:users',
            'email' => 'required|email|max:255|unique:users',           
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/',
            'password' => 'min:6|required_with:password_confirmation|same:confirm_password',
            'confirm_password' => 'min:6',

        ]);
    }
  
    public function Register(Request $request){

        if(Auth::check()){
            return redirect('user/dashboard');
        }
        if ($request->isMethod('post')) {

            $validator = $this->validator($request->all());
      
            if ($validator->fails()) {           
                return redirect()->back()->withErrors($validator);         
            }
            
            $data = $request->only('username','phone','email');
            $data['password'] = Hash::make($request->password);
            $user = User::Create($data);
            if(isset($user->id)){
                Auth::loginUsingId($user->id);
                return redirect('user/dashboard');
            }
            
        }
        $title = 'Register-Page';
        return view('auth.register',compact('title'));
    }
   
}
