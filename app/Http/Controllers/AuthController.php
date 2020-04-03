<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

use App\user;
use App\position;
use Mail;
use Hash;


class AuthController extends Controller
{

    public function login(Request $request)
    {
   
        $validator = Validator::make($request->all(), [
            // 'type' => 'in:DEFAULT,SOCIAL', // DEFAULT or SOCIAL values
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if($validator->fails())
        {
            return response(['status'=>0,'msg'=>$validator->errors()]);
        }
        $user = user::where(['email'=>$request->email])->first();
        if($user&& Hash::check($request->password, $user->password) &&$user->is_confirm) 
        {
            return response(['status'=>2,'msg'=>'done','data'=>$user]);
        }
        return response(['status'=>1,'msg'=>"record doesn't match to our records"]);
        
    }
    public function positions()
    {
        $positions = position::get()->toArray();
        return response(['status'=>1,'msg'=>'done','data'=>$positions]);
    }
    public function submit(Request $request)
    {


        $validator = Validator::make($request->all(), [

            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:6',
            'tel'=>'numeric',
            '*'=>'required'

        ]);
        if($validator->fails())
        {
            return response(['status'=>0,'msg'=>$validator->errors()]);
        }
        $input = $request->all();

        $code = rand(999,10000);
        $api_auth = str_random(12);

        $input['verf_code']=$code;
        $input['api_auth']=$api_auth;
        $input['password'] = Hash::make($request->password);
        // return['sd'=>$input];
        $user = user::create($input);
        self::html_email($user);
        return response(['status'=>1,'msg'=>'done','data'=>$user->id]);
    }

    public function verf_confirm(Request $request)
    {
        $validator = Validator::make($request->all(), [

            '*'=>'required'

        ]);
        if($validator->fails())
        {
            return response(['status'=>0,'msg'=>$validator->errors()]);
        }
        

        $user = user::find($request->id);

        if($request->code == $user->verf_code)
        {
            $user->update(['is_confirm'=>1]);
            return response(['status'=>2,'msg'=>'done','data'=>$user]);
        }else
        {
            return response(['status'=>1,'msg'=>'invalid code !']);
        }
        
        
    }
    public function resend_code(Request $request)
    {
        $user = user::find($request->id);
        $code = rand(999,10000);
        $user->update(['verf_code'=>$code]);
        self::html_email(user::find($request->id));
        return response(['status'=>1,'msg'=>'done']);
    }
    public function check_email(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'=>'required|email|exists:users'
        ]);
        if($validator->fails())
        {
            return response(['status'=>0,'msg'=>$validator->errors()]);
        }
        
        $user = user::where(['email'=>$request->email,'is_confirm'=>1,'role'=>'user'])->first();
        
        if($user)
        {
            $code = rand(999,10000);
            $user->update(['verf_code'=>$code]);
            self::html_email($user);
            return response(['status'=>1,'msg'=>'done','data'=>$user->id]);
        }else
        {
            return response(['status'=>0,'msg'=>[email=>"This Email is not confirmed"]]);
        }

    }





    public function html_email($user) {

        $data = array('name'=>$user->name,'code'=>$user->verf_code);
        Mail::send('mail', $data, function($message) {
           $message->to('mohamed.taher254@icloud.com', 'Verfication Code')->subject
              ('Verfication Code');
           $message->from('goman2013@gmail.com','app');
        });
     }
}
