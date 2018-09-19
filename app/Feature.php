<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Intervention\Image\ImageManager;

class Feature extends Model
{
    protected $fillable = ['image_id','x1','y1','x2','y2','description'];

    public function properties(){
        return $this->belongsToMany('App\Property','bindings','feature_id','property_id')->withPivot('item_id', 'tag_id');;
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
       // print $this->width." ".$this->height." ".$this->x1." ".$this->y1;
        $image->crop($this->width,$this->height,$this->x1, $this->y1);
        //$dir = storage_path('app/public/features');
        //$core = tempnam($dir, 'feature_');
        $full_name  = $name.".jpeg";
        $image->save($dir.DIRECTORY_SEPARATOR.$full_name);
        //unlink($core);
        //dump( $new_file);
        return $full_name;
        /*
       // $size=getimagesize($this->image->path);
        ini_set("gd.jpeg_ignore_warning", 1);
        dump($this->id);
        dump($this->image);
        $image=imagecreatefromgd2part (storage_path('app/'.$this->image->path) , $this->x1, $this->y1, $this->x2, $this->y2);
        $dir = storage_path('public/features');
        $new_file = tempnam($dir, 'feature');
        imagejpeg($image,$new_file);
        return $new_file;
         
         */
    }
    
    
/*    
    public function item(){
        return $this->items()->limit(1);
    }
  */ 
    
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
    
    /*
    public function getFilledProperties(){
        
        foreach ($this->properties as $property){
            
            
        }
        
    }
   */
}
