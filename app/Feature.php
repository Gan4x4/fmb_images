<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    //protected $guarded = ['id','created_at','updated_at'];
    protected $fillable = ['tag_id','color','brand_id','model_id'];
    //protected $casts = ['region'];

    public function tag(){
        return $this->belongsTo('App\Tag');
    }
    
    public function getName(){
        return $this->tag->name;
    }
   
}
