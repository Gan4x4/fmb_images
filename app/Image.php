<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Item;

class Image extends Model
{
    const STATUS_NEW = null;
    const STATUS_EDITED = 1;
    
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
    
    public function getFullPath(){
        return storage_path('app/'.$this->path);
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
    
    
    public function getPrposedItem(){
        
        $features = $this->features;
        $existingItemIds = [];
        //dump($features);
        foreach($features as $feature){
            $f_item = $feature->getItem();
            if ($f_item){
                $existingItemIds[] = $f_item->id;
            }
        }
        //dd($existingItemIds);
        $items = Item::all();
        foreach($items as $item){ 
            if (! in_array($item->id, $existingItemIds)){
                return $item;
            }
        }
        return $items->first(); 
        
    }
    
    // Override
    public function save(array $options = array()){
        $size = getimagesize($this->getFullPath());
        $this->width = $size[0];
        $this->height = $size[1];
        $this->hash = md5_file($this->getFullPath());
        parent::save($options);
    }
    
}
