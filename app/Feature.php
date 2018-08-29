<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    protected $fillable = ['x1','y1','x2','y2','description'];

    public function tags(){
        return $this->belongsToMany('App\Tag');
    }
    
    public function getName(){
        return $this->tag->name;
    }
   
}
