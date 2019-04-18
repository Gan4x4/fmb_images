<?php

namespace App\Parser;

use Illuminate\Database\Eloquent\Model;


class Source extends Model
{
    const TYPE_AVITO = 2;
    const TYPE_USER = 1;
    const TYPE_FMB = 3;
    protected $guarded = ['id','created_at','updated_at'];
    
     public function images(){
        return $this->hasMany('App\Image');
    }

}
