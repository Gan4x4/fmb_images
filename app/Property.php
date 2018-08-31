<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $guarded = ['id','created_at','updated_at'];
    public function propertys(){
        return $this->belongsToMany('App\Item');
    }
    
}
