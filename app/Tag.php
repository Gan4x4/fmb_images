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
    
    public function features()
    {
        return $this->belongsToMany('App\Feature');
    }
    
    public function groups()
    {
        return $this->belongsToMany('App\Group');
    }
    
    
    
}
