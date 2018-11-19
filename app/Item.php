<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    //protected $guarded = ['id','created_at','updated_at'];
    protected $fillable = ['name','description','parent_id'];
    
    
    public function properties(){
        return $this->belongsToMany('App\Property');
    }
    
    /*
     * Return all features containing this item
     */
    public function features(){
        return $this->belongsToMany('App\Feature','bindings','item_id','feature_id')->distinct('feature_id');
    }
    
    
    public static function getDefault(){
        return Item::whereNull('parent_id')->first();
    }
    
    public function getNameAttribute($val){
        $name = $val;
        $parent = $this->getParent();
        if ($parent){
            $name = $parent->name." -> ".$name;
        }
        return $name;
    }
    
    public function getChilds(){
        return Item::where('parent_id',$this->id)->get();
    }
    
    public function getParent(){
        return Item::find($this->parent_id);
    }
    
    // Override
    public function delete(){
        $childs = $this->getChilds();
        foreach($childs as $child){
            $child->delete();
        }
        parent::delete();
    }
    
    public function getDescription(){
        $out = [];
        $out = $this->name;
        foreach($this->properties as $property){
            
        }
    }
    
    public function isFullImage(){
        return $this->name == 'Bike';
    }
    
}
