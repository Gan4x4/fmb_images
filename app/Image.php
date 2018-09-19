<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = ['description'];
    
    public function features(){
        return $this->HasMany('App\Feature');
    }
    
    public function getThumbUrl(){
        return $this->getUrl();
    }
    
    public function getUrl(){
        return "/storage/".substr($this->path,7);
    }
    
    /*
    public function size2region(){
        $region = [[0,0],[$this->width,$this->height]];
        return json_encode($region);
    }
    */
    
    public function getFeatureDescription(){
        $out = [];
        foreach($this->features as $feature){
            /*
            print $feature->id." ". $feature->getItem()->name;
            foreach($feature->properties as $p){
                $tag = $p->getTag();
                
                //print $p->name." ".$p->tagId()." ";
                if ($tag){
                   // print $tag->name;
                }
            }
            //print "<br>";
             * 
             */
            $out[$feature->getItem()->name] = $feature->getDescription();
        }
        return $out;
    }
    
    
}
