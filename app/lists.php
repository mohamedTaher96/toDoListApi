<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class lists extends Model
{
    protected $table="lists";
    protected $fillable = [
        'title','is_confirm','user_id'
    ];

    public function task()
    {
        return $this->hasMany('App\task','list_id'); 
    }
    public function user(){
        return $this->belongsTo('App\user'); 
    }

    public static function boot() {
        parent::boot();
        static::deleting(function($list) {
             foreach($list->task as $task){
                $task->delete();
              }
        });
    }
}