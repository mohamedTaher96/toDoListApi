<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

use App\lists;
use App\task;
use App\user;
class AdminController extends Controller
{
    public function users()
    {
        $users = user::get();
        return response(['status'=>1,'msg'=>'done','data'=>$users]);
    }

    public function tasks($id)
    {
        $lists = task::where('list_id',$id)->get();
        return response(['status'=>1,'msg'=>'done','data'=>$lists]);
    }

    public function userInfo($id)
    {
        $lists = user::find($id);
        return response(['status'=>1,'msg'=>'done','data'=>$lists]);
    }
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
        $user = user::where(['email'=>$request->email,'password'=>$request->password])->with('position')->first();
        if(!$user)
        {
            return response(['status'=>1,'msg'=>"record doesn't match"]);
        }

        return response(['status'=>2,'msg'=>'done','data'=>$user]);
    }
    public function submit(Request $request)
    {
        return['xs'=>'sd'];
        $validator = Validator::make($request->all(), [
            // 'type' => 'in:DEFAULT,SOCIAL', // DEFAULT or SOCIAL values
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:6',
            'tel'=>'numeric',
            '.*'=>'required'
        ]);
        if($validator->fails())
        {
            return response(['status'=>0,'msg'=>$validator->errors()]);
        }
        // $user = user::where(['email'=>$request->email,'password'=>$request->password])->with('position')->first();
        // if(!$user)
        // {
        //     return response(['status'=>1,'msg'=>"record doesn't match"]);
        // }

        // return response(['status'=>2,'msg'=>'done','data'=>$user]);
    }
}
