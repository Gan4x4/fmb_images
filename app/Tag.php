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
    protected $fillable = ['name','description'];
    
    
     public function properties(){
        return $this->belongsToMany('App\Property');
    }
    
    //Override
    public function delete(){
        $this->properties()->detach();
        parent::delete();
    }
    
}
