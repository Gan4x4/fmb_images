<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Property extends Model
{
    protected $guarded = ['id','created_at','updated_at'];
    //public $tag = null;
    
    public function items(){
        return $this->belongsToMany('App\Item');
    }
    
    public function tags(){
        return $this->belongsToMany('App\Tag');
    }
    
    public function tagId(){
        if ($this->pivot){
            return $this->pivot->tag_id;
        }
        return null;
    }
    
    
    public function setTagForFeature($featureId,$itemId){
         $line =  DB::table('bindings')->
                where('feature_id', $featureId)->
                where('item_id', $itemId)->
                where('property_id', $this->id)->first();
         
        if ($line ){
            $this->tag = Tag::findOrFail($line->tag_id);
        }
    }
    
    
}
