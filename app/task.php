<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class task extends Model
{
    protected $fillable = [
        'is_done','list_id','title'
    ];
    public function list()
    {
        return $this->belongsTo('App\lists'); 
    }
}
