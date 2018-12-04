<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Item;
use App\Interfaces\Owned;

class Image extends Model implements Owned
{
    const STATUS_NEW = null;
    const STATUS_EDITED = 1;
    
    protected $fillable = ['description'];
    
    public static function findByHash($path){
        $hash =  self::hashFunction($path);
        return Image::where('hash',$hash)->first();
    }
    
    public static function hashFunction($path){
        return md5_file($path);
    }
    
    public function features(){
        return $this->HasMany('App\Feature');
    }
    
    
    
    public function user(){
        return $this->BelongsTo('App\User');
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
    
    public function getFeatureDescription(){
        $out = [];
        foreach($this->features as $feature){
            $item = $feature->getItem();
            if ($item){
                $out[$feature->getItem()->name] = $feature->getDescription();
            }else{
                $out[] = ["Invalid feature #".$feature->id];
            }
        }
        return $out;
    }
    
    
    public function getPrposedItem(){
        $features = $this->features;
        $existingItemIds = [];
        foreach($features as $feature){
            $f_item = $feature->getItem();
            if ($f_item){
                $existingItemIds[] = $f_item->id;
            }
        }
        $items = Item::all();
        foreach($items as $item){ 
            if (! in_array($item->id, $existingItemIds)){
                return $item;
            }
        }
        return $items->first(); 
    }
    
    
    public function hasItem($item_id){
        $features = $this->features;
        foreach($features as $feature){
            $item = $feature->getItem();
            if ($item && $item->id == $item_id){
                return true;
            }
        }
        return false;
    }
    
    // Override
    public function save(array $options = array()){
        $size = getimagesize($this->getFullPath());
        $this->width = $size[0];
        $this->height = $size[1];
        $this->hash = self::hashFunction($this->getFullPath());
        parent::save($options);
    }
    
    public function updateStatus(){
        if ($this->features->count() == 0){
            $this->status = self::STATUS_NEW;    
        }
        else{
            $this->status = self::STATUS_EDITED;
        }
        parent::save();
    }
    
    // Override
    public function delete(){
        foreach($this->features as $feature){
            $feature->delete();
        }
        parent::delete();
    }
    
    /*
     * Another images of the object
     */
    public function getSiblings(){
        return Image::where('source_id',$this->source_id)->
                where('id','<>',$this->id)->
                whereNotNull('source_id')->
                get();
    }
    
    
}
