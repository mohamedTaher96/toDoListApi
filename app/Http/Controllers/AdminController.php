<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

use App\lists;
use App\task;
use App\user;
class AdminController extends Controller
{
    public function users(Request $request)
    {
        $user =user::where('api_auth',$request->api_auth)->first();
        if($user)
        {
            $users = user::where('role','user')->with('position')->orderBy('created_at','DESC')->simplePaginate(5);
            return response(['status'=>1,'msg'=>'done','data'=>$users]);
        }

    }

    public function user_search(Request $request)
    {
        $user =user::where('api_auth',$request->api_auth)->first();
        if($user)
        {
            $users = user::where('name','like', '%' . $request->search . '%')->orWhereHas('position',function($q) use($request){
                $q->where('title','like', '%' . $request->search . '%');
            })->with('position')->get();
            return response(['status'=>1,'msg'=>'done','data'=>$users]);
        }

    }
    public function user_tasks_done(Request $request)
    {
        $user =user::where('api_auth',$request->api_auth)->first();
        if($user)
        {
            $lists = lists::where(['user_id'=>$request->user_id,'is_confirm'=>1])->whereDoesntHave('task',function($query){
                return $query->where('is_done',0);
            })->orderBy('created_at','DESC')->simplePaginate(5);
            return response(['status'=>1,'msg'=>'done','data'=>$lists]);
        }

    }
    public function user_tasks_check(Request $request)
    {
        $user =user::where('api_auth',$request->api_auth)->first();
        if($user)
        {
            $lists = lists::where(['user_id'=>$request->user_id,'is_confirm'=>0])->orderBy('created_at','DESC')->simplePaginate(5);
            return response(['status'=>1,'msg'=>'done','data'=>$lists]);
        }
    }
    public function user_info(Request $request)
    {
        $user =user::where('api_auth',$request->api_auth)->first();
        if($user)
        {
            $user = user::where('id',$request->user_id)->with('position')->first();
            return response(['status'=>1,'msg'=>'done','data'=>$user]);
        }
    }
    public function user_confirm(Request $request)
    {
        $user =user::where('api_auth',$request->api_auth)->first();
        if($user)
        {
            $user = user::where('id',$request->user_id)->first();
            $user->update(['is_confirm'=>!$user->is_confirm]);
            return response(['status'=>1,'msg'=>'done']);
        }
    }
    public function user_delete(Request $request)
    {
        $user =user::where('api_auth',$request->api_auth)->first();
        if($user)
        {
            $user = $user::find($request->user_id);
            $user->delete();
            return response(['status'=>1,'msg'=>'done']);
        }
    }
    public function new_task(Request $request)
    {
        $validator = Validator::make($request->all(), [
            '*'=>'required'
        ]);
        if($validator->fails())
        {
            return response(['status'=>0,'msg'=>$validator->errors()]);
        }
        $user =user::where('api_auth',$request->api_auth)->first();
        if($user)
        {
            $list = lists::create(['user_id'=>$request->user_id,'title'=>$request->title]);
            foreach($request->tasks as $task)
            {
                task::create(['title'=>$task,'list_id'=>$list->id]);
            }
            return response(['status'=>1,'msg'=>'done']);
        }

    }
    public function checkList(Request $request)
    {
        $user =user::where('api_auth',$request->api_auth)->first();
        if($user)
        {
            $lists = lists::where(['is_confirm'=>0])->whereDoesntHave('task',function($query){
                return $query->where('is_done',0);
            })->with('user')->orderBy('created_at','DESC')->simplePaginate(5);
            return response(['status'=>1,'msg'=>'done','data'=>$lists]);
        }

    }


}
