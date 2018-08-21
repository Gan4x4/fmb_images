<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    public function features(){
        return $this->HasMany('App\Feature');
    }
    
    public function getThumbUrl(){
        return $this->getUrl();
    }
    
    public function getUrl(){
        return "/storage/".substr($this->path,7);
    }
    
    public function size2region(){
        $region = [[0,0],[$this->width,$this->height]];
        return json_encode($region);
    }
    
}
