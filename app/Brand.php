<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $guarded = ['id','created_at','updated_at'];
    
    public function features(){
        return $this->hasMany('App/Feature');
    }
    
    // Override
    public function delete(){
        foreach($this->features as $feature){
            $feature->brand_id = null;
            $feature->save();
        }
        parent::delete();
    }
    
}
