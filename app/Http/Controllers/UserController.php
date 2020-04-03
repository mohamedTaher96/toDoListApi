<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

use App\lists;
use App\task;
use App\user;
use App\position;
use Mail;
use File;

class UserController extends Controller
{

    public function toDoLists(Request $request)
    {
        $user = user::where(['api_auth'=>$request->api_auth,'is_confirm'=>1])->first();
        if($user)
        {
            $lists = lists::where('user_id',$user->id)->whereHas('task',function($query){
                return $query->where('is_done',0);
            })->orderBy('created_at','DESC')->get();
            return response(['status'=>1,'msg'=>'done','data'=>$lists]);
        }

    }

    public function listTasks($id)
    {
        
        $lists = task::where('list_id',$id)->orderBy('created_at','DESC')->get();
        return response(['status'=>1,'msg'=>'done','data'=>$lists]);
    }

    public function listCheck(Request $request)
    {
        $user = user::where(['api_auth'=>$request->api_auth,'is_confirm'=>1])->first();
        if($user)
        {
            $lists = lists::where(['user_id'=>$user->id,'is_confirm'=>0])->whereDoesntHave('task',function($query){
                return $query->where('is_done',0);
            })->orderBy('created_at','DESC')->simplePaginate(5);
            return response(['status'=>1,'msg'=>'done','data'=>$lists]);
        }

    }

    public function listDone(Request $request)
    {     
        $user = user::where(['api_auth'=>$request->api_auth,'is_confirm'=>1])->first();
        if($user)
        {
            $lists = lists::where(['user_id'=>$user->id,'is_confirm'=>1])->whereDoesntHave('task',function($query){
                return $query->where('is_done',0);
            })->orderBy('created_at','DESC')->simplePaginate(5);
            return response(['status'=>1,'msg'=>'done','data'=>$lists]);
        }

    }


    public function userInfo($id)
    {
        $lists = user::where('id',$id)->with('position')->first();
        return response(['status'=>1,'msg'=>'done','data'=>$lists]);
    }

    public function list_save(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     '*'=>'required'
        // ]);
        // if($validator->fails())
        // {
        //     return response(['status'=>0,'msg'=>$validator->errors()]);
        // }
        $user =user::where(['api_auth'=>$request->api_auth,'is_confirm'=>1])->first();
        if($user)
        {
            if(count($request->ids)>0)
            {
                foreach($request->ids as $id)
                {
                    $task = task::find($id);
                    $task->update(['is_done'=>!$task->is_done]);
                }
            }

            $list = lists::where('id',$request->list_id)->whereDoesntHave('task',function($query){
                return $query->where('is_done',0);
            })->first();

            if(!$list)
            {
                $list = lists::find($request->list_id);
                $list->update(['is_confirm'=>0]);
            }else{
                if($user->role=='admin')
                {
                    $list->update(['is_confirm'=>1]);
                }
            }

            
            return response(['status'=>1,'msg'=>'success']);
        }


        
        
    }

    public function user_info(Request $request)
    {

        $user = user::where(['api_auth'=>$request->api_auth,'is_confirm'=>1])->first();
        $positions = position::get()->toArray();
        $data = (object)array('user'=>$user,'positions'=>$positions);
        return response(['status'=>1,'msg'=>'success','data'=>$data]);
    
    }

    public function save_profile(Request $request)
    {
        
        $input = $request->all();
        
        $validator = Validator::make($input, [
            
            'position_id'=>'required',
            '*'=>'required',
            'email' => 'required|email|',
            'password' => 'required|confirmed|min:6',
            'tel'=>'numeric',
            'uploadfile'=>'image|mimes:jpg,jpeg,png,gif'

        ]);

        $user = user::where(['api_auth'=>$request->api_auth,'is_confirm'=>1])->first();

        if($validator->fails())
        {
            return response(['status'=>0,'msg'=>$validator->errors()]);
        }
        else if(user::where('id','<>',$user->id)->where('email',$request->email)->first())
        { 
            return response(['status'=>0,'msg'=>(['email'=>'this email is used'])]);
        }
        

        
        if (request()->hasFile('uploadfile')) {
            File::delete($user->image);
            $file = request()->file('uploadfile');
            $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
            $file->move('./uploads/users_profile/', $fileName);    
            $input['image']= $fileName;
            
        }   

        $user->update($input);
        return response(['status'=>1,'msg'=>'success']);
    
    }












    public function html_email($user) {

        $data = array('name'=>$user->name,'code'=>$user->verf_code);
        Mail::send('mail', $data, function($message) use($user) {
           $message->to($user->email, 'Verfication Code')->subject
              ('Verfication Code');
           $message->from('goman2013@gmail.com','app');
        });
     }
}
