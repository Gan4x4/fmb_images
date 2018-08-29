<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $guarded = ['id','created_at','updated_at'];
    
    
    public static function getDefault(){
        return Group::whereNull('parent_id')->first();
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
        return Group::where('parent_id',$this->id)->get();
    }
    
    public function getParent(){
        return Group::find($this->parent_id);
    }
    
    // Override
    public function delete(){
        $childs = $this->getChilds();
        foreach($childs as $child){
            $child->delete();
        }
        parent::delete();
    }
    
    
}
