<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Intervention\Image\ImageManager;

class Feature extends Model
{
    protected $fillable = ['image_id','x1','y1','x2','y2','description'];

    public function properties(){
        return $this->belongsToMany('App\Property','bindings','feature_id','property_id')->withPivot('item_id', 'tag_id','feature_id');
    }
    
    protected function items(){
        return $this->belongsToMany('App\Item','bindings','feature_id','item_id');
    }
    
    protected function image(){
        return $this->belongsTo('App\Image');
    }
    
    public function extract($dir,$name){
        $manager = new ImageManager();
        $file = storage_path('app'.DIRECTORY_SEPARATOR.$this->image->path);
        $image = $manager->make($file);
        $image->crop($this->width,$this->height,$this->x1, $this->y1);
        $full_name  = $name.".jpeg";
        $image->save($dir.DIRECTORY_SEPARATOR.$full_name);
        return $full_name;
       
    }
    
    
    public function getWidthAttribute(){
        return $this->x2 - $this->x1;
    }
    
    public function getHeightAttribute(){
        return $this->y2 - $this->y1;
    }
    
    public function getItem(){
        $items = $this->items;
        if ($this->items->count()){
            return $items->first();
        }
        return null;
    }
    
    public function getName(){
        if ($this->items->count()){
            return $this->items->first()->name;    
        }else{
            return "--";
        }
        
    }
    
    public function getDescription(){
        $out = [];
        foreach($this->properties as $property){
            $tag = $property->getTag();
            if ($tag){
                $out[$property->name] = $tag->name;
            }
        }
        return $out;
    }
    
    public function hasProperty($property_id){
        return $this->properties()->where('properties.id',$property_id)->exists();
    }
    
    public function delete(){
        parent::delete();
        $this->image->updateStatus();
    }
    
    // Override
    public function save(array $options = array()){
        $this->image->updateStatus();
        parent::save($options);
    }

}
