<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
/*
 * Class for tag or word for descripe image or it part
 * Can be nested.
 * Examble: Hardtail, BMX, 
 * Brake -> disk
 * ...
 * 
 */
class Tag extends Model
{
    protected $guarded = ['id','created_at','updated_at'];
    
    public function getChilds(){
        return Tag::where('parent_id',$this->id)->get();
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
