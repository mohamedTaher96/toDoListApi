<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use File;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','position_id',
        'address','api_auth','verf_code','tel','is_confirm','image'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function position()
    {
        return $this->belongsTo('App\position'); 
    }
    public function lists()
    {
        return $this->hasMany('App\lists'); 
    }
    public function getImageAttribute()
    {
        if (!isset($this->attributes['image'])) {
        return "https://bootdey.com/img/Content/avatar/avatar6.png";
        } 
        return url("uploads/users_profile/".$this->attributes['image']) ;
    }
        // this is a recommended way to declare event handlers
    public static function boot() {
        parent::boot();

        static::deleting(function($user) { // before delete() method call this
             foreach($user->lists as $list){
                $list->delete();
              }
             File::delete($user->image);
        });
    }

}
