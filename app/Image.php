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
            $out[$feature->getItem()->name] = $feature->getDescription();
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
    
}
