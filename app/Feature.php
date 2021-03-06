<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Intervention\Image\ImageManager;

class Feature extends Model
{
    protected $fillable = ['image_id','x1','y1','x2','y2','description'];

     public static function getIdsOfValidationFeatures(){
        $features = [];
        $images = Image::where('validation',true)->get();
        foreach($images as $image){
            $features = array_merge($features,$image->features()->pluck('id')->toArray());
        }
        return array_unique($features);
    }
    
    public function properties(){
        return $this->belongsToMany('App\Property','bindings','feature_id','property_id')
                ->withPivot('item_id', 'tag_id','feature_id')->withTimestamps();
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
    
    
    public function extractSquareCanvas($dir,$name){
        $manager = new ImageManager();
        $file = storage_path('app'.DIRECTORY_SEPARATOR.$this->image->path);
        $image = $manager->make($file);
        
        $image->crop($this->width,$this->height,$this->x1, $this->y1);
        
        if ($this->width != $this->height){
            $canvas = $this->width > $this->height ? $this->width : $this->height;
            $image->resizeCanvas($canvas,$canvas);
        }
        $full_name  = $name.".jpeg";
        $image->save($dir.DIRECTORY_SEPARATOR.$full_name);
        return $full_name;
    }
    
    
    public function extractSquare($dir,$name){
        $x1 = $this->x1;
        $y1 = $this->y1;
        $h = $this->height;
        $w = $this->width;
        $image = $this->image;
        if ($h > $w){
            $x1 = $x1 - intval(($h - $w) / 2);
            if ($x1 < 0 ){
                $x1 = 0;
            }
            $w = $h;
            
            if (($x1 + $w)  > $image->width){
                $x1 =  $this->image->width -$w;
                if ($x1 < 0 ){
                    $x1 = 0;
                }
            }
        }
        
        if ($h < $w){
            $y1 = $y1 - intval(($w - $h) / 2);
            if ($y1 < 0 ){
                $y1 = 0;
            }
            $h = $w;
            if (($y1 + $h)  > $image->height){
                $y1 =  $this->image->height -$h;
                if ($y1 < 0 ){
                    $y1 = 0;
                }
            }
        }
        
        $manager = new ImageManager();
        $file = storage_path('app'.DIRECTORY_SEPARATOR.$image->path);
        $image = $manager->make($file);
        $image->crop($w,$h,$x1, $y1);
        $full_name  = $name.".jpeg";
        $image->save($dir.DIRECTORY_SEPARATOR.$full_name);
        return $full_name;
    }
    
    
    
    /*
     * Get all item props ant replace not empty 
     * by filled from bindings page
     * 
     */
    public function getFilledProperties(){
        $item = $this->getItem();
        $current = $this->properties;
        if (! $item){
            return $current;
        }
       // dump($current);
        $out = [];
        //dd($out);
        $all = $item->getPrefilledProperties($this->image);
       // dd($all);
        foreach($all as $p){
            
            $filled = $current->first(function ($value, $key) use ($p){
                return $value->id == $p->id && $value->getTag();
            });
            
            if ($filled){
                $out[] = $filled;
            }else{
                $out[] = $p;
            }
        }
        
        return collect($out);
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
        //ksort($out);
        return $out;
    }
    
    public function hasProperty($property_id){
        return $this->properties()->where('properties.id',$property_id)->exists();
    }
    
    public function getUndefinedProperties(){
        $item = $this->getItem();
        if (! $item){
            return null;
        }
        $filled_ids = $this->properties()
                ->whereNotNull('tag_id')
                ->where('tag_id','>',0)
                ->pluck('property_id')
                ->toArray();
        
        $all = $this->getItem()->properties()->whereNotIn('properties.id',$filled_ids)->get();
        return $all;
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
    
    
    public function cloneToSiblings(){
        $item = $this->getItem();
        if (! $item){
            return;
        }
        
        $image = $this->image;
        if (! $image){
            return;
        }
        
        $siblings = $image->getSiblings();
        if ($siblings->count() == 0 ){
            return;
        }
        
        foreach($siblings as $img){
            foreach($img->features as $f){
                $ai = $f->getItem();
                if ($ai){ 
              //      dump($ai->name);
                    $this->copyPropertiesTo($f);
                }
            }
        }
        
    }
    
    public function copyPropertiesTo($anotherFeature){
        $item = $this->getItem();
        
        if (! $item || ! $item->canBeCopied()){
            return;
        }
        
        if ($anotherFeature->getItem()->id != $item->id){
            //dump("Bad item ".$anotherFeature->getItem());
            return;
        }
        
        //$anotherFeature->properties()->detach();
        //dump($this->properties);
        foreach($this->properties as $property){
            if (! $property->canBeCopied()){
                continue;
            }
            
            $tag = $property->getTag(); 
            if (! $tag ){
                continue;
            }
            //dump($tag->name);
            
            foreach($anotherFeature->properties as $ap){
                if ($ap->id == $property->id && $ap->getTag()){
                    //dump("Want to change ".$ap->name." tag ".$ap->getTag()->name." to ".$tag->name);
                    
                    if ($ap->pivot && $ap->pivot->tag_id){
                        //dump("Change");
                        $ap->pivot->tag_id = $tag->id;
                        $ap->pivot->save();
                    }
                }
            }
        }
        
        $anotherFeature->save();
    }

   
}
