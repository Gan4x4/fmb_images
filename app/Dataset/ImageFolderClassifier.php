<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Dataset;

use Illuminate\Support\Facades\Storage;
use App\Item;
use App\Tag;

/**
 * Description of Dataset
 *
 * @author gan
 */
class ImageFolderClassifier extends Dataset{
    
    private $items = [];
    public $subdirs = true;
    public $dir = null;
    public $minimalPropertyCount = 9;
    public $tree = null;
    
    private $other = 'other';
    
    public function __construct($params) {
        $tmp = [];
        
        $this->minimalPropertyCount = intval($params['min_prop']);
        $this->test = floatval($params['validate']);
        \Log::debug($params);
        foreach($params['items'] as $item_id){
            $tmp[$item_id] = [];
            $propKey = $item_id.'_propertys';
            if (isset($params[$propKey]) ){
                foreach($params[$propKey] as $prop_id){
                    $tmp[$item_id][$prop_id] = [];
                    $tagKey =  $item_id.'_'.$prop_id.'_tags';
                    if (isset($params[$tagKey])){
                        foreach($params[$tagKey] as $tag_id){
                            $tmp[$item_id][$prop_id][] = $tag_id;
                        }
                    }
                }
            }
        }
        $this->items = $tmp;
        $this->tree = new Tree(array_keys($this->items),$this->test);//array_keys($this->items));
        
        
    }
    
    
    public function build($dir){
        $this->dir = $dir;
        Storage::makeDirectory($this->dir);
        
        $itemIds = array_keys($this->items);
        foreach($itemIds as $item_id){
            $this->extractAndSaveImages($item_id);
        }
        
        $target = $this->dir.DIRECTORY_SEPARATOR.'compressed.zip';
        $this->zip(storage_path('app'.DIRECTORY_SEPARATOR.$this->dir), storage_path('app'.DIRECTORY_SEPARATOR.$target));

        return $target;
    }
    
    private function extractAndSaveImages($item_id){
        $item = Item::findOrFail($item_id);
        $features = $item->features;
        if (! $features) {
            return;
        }
         
        foreach($features as $feature){
            $this->saveValuesInSubdirs($item,$feature);   
            
        }
    }
    
    private function hasSelectedProps($itemId){
        return count($this->items[$itemId]) != 0; 
    }
    
    private function lookupItemDir($item){
        $item_dir = $this->dir.DIRECTORY_SEPARATOR.self::name2dir($item->name);
        if (! Storage::exists($item_dir)){
            Storage::makeDirectory($item_dir);
        }
        return $item_dir;
    }
    
    /*
     *  Save images in folders as:
     *  bike/color/red/red1.jpg
     *  bike/color/blue/blue1.jpg
     *  ...
     *  bike/brand/gt/99.jpg
     *  bike/brand/gt/34.jpg
     * 
     *  fork/color/red/99.jpg
     * 
     */
    private function saveValuesInSubdirs($item,$feature){
        $item_dir = $this->lookupItemDir($item);//$this->dir.DIRECTORY_SEPARATOR.mb_strtolower($item->name);

        foreach($feature->properties as $property){
            if ( in_array($property->id, array_keys($this->items[$item->id]))){
                
                $prop_dir = $this->lookupPropDir($item,$property);
                $selectedTagIds = $this->items[$item->id][$property->id];
                $tag = $property->getTag();
                
                if ($tag && in_array($tag->id, $selectedTagIds)){
                    $tag_dir = $this->lookupTagDir($item,$property,$tag);
                    $filename = self::name2file($tag->name.'_'.$feature->image->id);
                    $feature->extract(storage_path('app'.DIRECTORY_SEPARATOR.$tag_dir),$filename);
                }
            }
        }
        
        $undefined = $feature->getUndefinedProperties();
        foreach($undefined as $property){
            if ( in_array($property->id, array_keys($this->items[$item->id])) && in_array(0,$this->items[$item->id][$property->id])){
                $prop_dir = $this->lookupPropDir($item,$property);
                $tag = new Tag();
                $tag->id = null;
                $tag->name = 'Undefined';
                $tag_dir = $this->lookupTagDir($item,$property,$tag);
                $filename = self::name2file($tag->name.'_'.$feature->image->id);
                $feature->extract(storage_path('app'.DIRECTORY_SEPARATOR.$tag_dir),$filename);
            }
        }
        
    }
    
    
    
    
    /*
    private function makeDir(){
        
        $this->dir.
        if( ! Storage::exists($prop_dir)) {
            Storage::makeDirectory($prop_dir);
        }
    }
    */
    private function lookupPropDir($item,$property){
        $item_dir = $this->lookupItemDir($item);
        $prop_dir = $item_dir.DIRECTORY_SEPARATOR.self::name2dir($property->name);
        if( ! Storage::exists($prop_dir)) {
            Storage::makeDirectory($prop_dir);
        }
        return $prop_dir;
    }
    
     private function lookupTagDir($item,$property,$tag){
        $prop_dir = $this->lookupPropDir($item,$property);
        
        if ($this->test > 0){
            if ($this->tree->getValidateCount($item->id,$property->id,$tag->id)){
                $this->tree->decValidateCount($item->id,$property->id,$tag->id);
                $prop_dir = $prop_dir.DIRECTORY_SEPARATOR.'val';
            }
            else{
                $prop_dir = $prop_dir.DIRECTORY_SEPARATOR.'train';
            }
        }
        
        if ($this->tree->count($item->id,$property->id,$tag->id) < $this->minimalPropertyCount){
            $tag_dir = $prop_dir.DIRECTORY_SEPARATOR.$this->other;
        }else{
            $tag_dir = $prop_dir.DIRECTORY_SEPARATOR.self::name2dir($tag->name);    
        }
        
        if( ! Storage::exists($tag_dir)) {
            Storage::makeDirectory($tag_dir);
        }
        return $tag_dir;
    }
    
    public function toValidate($item_id,$property_id,$tag_id){
        
    }
    
    public static function name2dir($str){
        return mb_strtolower(strtr($str,' ','_'));
    }
    
    public static function name2file($str){
        return uniqid(self::name2dir($str).'_');
    }
   
    
}
