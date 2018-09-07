<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    protected $fillable = ['image_id','x1','y1','x2','y2','description'];

    public function properties(){
        return $this->belongsToMany('App\Property','bindings','feature_id','property_id')->withPivot('item_id', 'tag_id');;
    }
    
    protected function items(){
        return $this->belongsToMany('App\Item','bindings','feature_id','item_id');
    }
    
    protected function image(){
        return $this->belongsTo('App\Image');
    }
    
    
/*    
    public function item(){
        return $this->items()->limit(1);
    }
  */ 
    
    public function getItemId(){
        $items = $this->items;
        if ($this->items->count()){
            return $items->first();
        }
        return null;
    }
    
    public function getName(){
        if ($this->items->count()){
            return $this->items->first()->name;    
        }else{
            return "--";
        }
        
    }
    /*
    public function getFilledProperties(){
        
        foreach ($this->properties as $property){
            
            
        }
        
    }
   */
}
