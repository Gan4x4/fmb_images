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
    
    const CROP_FORM_ORIGINAL = 0;
    const CROP_FORM_SQUARE = 1;
    const CROP_FORM_SQUARE_CANVAS = 2;
    
    private $items = [];
    public $subdirs = true;
    public $dir = null;
    public $minimalPropertyCount = 9;
    public $tree = null;
    public $crop_form = null;
    protected $fragments_count = 0;
    private $img_ids = [];
    protected $min_width = 0;
    protected $sources = [ ];
    protected $f_count = [ ];
    protected $sizes = [ 
        'w' => 0,
        'h' => 0
    ];
    protected $user_id = null;
    
    private $other = 'other';
    
    public function __construct($params) {
        $tmp = [];
        
        $this->minimalPropertyCount = intval($params['min_prop']);
        $this->test = floatval($params['validate']);
        $this->crop_form = intval($params['crop_form']);
        $this->max_width = $params['max_width'];
        $this->min_width = $params['min_width'];
        $this->user_id = $params['user_id'] > 0 ? $params['user_id'] : null;
        $this->items = self::tree2array($params);
        $this->tree = new Tree(array_keys($this->items),$this->test);//array_keys($this->items));

    }
    
    
    public function build($dir){
        $this->dir = $dir;
        Storage::makeDirectory($this->dir);
        
        $itemIds = array_keys($this->items);
        foreach($itemIds as $item_id){
            $this->extractAndSaveImages($item_id);
        }
        $this->image_count = count(array_unique($this->img_ids));
        $target = $this->dir.DIRECTORY_SEPARATOR.'compressed.zip';
        $this->zip(storage_path('app'.DIRECTORY_SEPARATOR.$this->dir), storage_path('app'.DIRECTORY_SEPARATOR.$target));
        $this->fillDescription();
        return $target;
    }
    
    private function extractAndSaveImages($item_id){
        $item = Item::findOrFail($item_id);
       
        $features = $item->features()->inRandomOrder()->get();//shuffle();//()->inRandomOrder()->get();
        if (! $features) {
            return;
        }
        //dump($features->count());
        $shuffled = $features->shuffle();
        foreach($shuffled as $feature){
            if ($this->checkImage($feature->image)){
                //dump("IM: ".$feature->image->id);
                $this->saveValuesInSubdirs($item,$feature);   
                $this->img_ids[] = $feature->image->id;
            }
        }
       
    }
    
    protected function checkImage($image){
        
        if ( parent::checkImage($image) ){
            
            // Bypass images from validation set
            if ($image->validation){
                return false;
            }
            
            if ($this->min_width > 0 && $image->width < $this->min_width){
                //print "MW";
                return false;
            }
            
            if ($this->user_id > 0 && $image->user_id != $this->user_id){
                return false;
            }
            
            return true;
            
        }else{
            //print "PT";
            return false;
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
        $image = $feature->image;
        foreach($feature->properties as $property){
            if ( in_array($property->id, array_keys($this->items[$item->id]))){
                
                $prop_dir = $this->lookupPropDir($item,$property);
                $selectedTagIds = $this->items[$item->id][$property->id];
                $tag = $property->getTag();
                
                if ($tag && in_array($tag->id, $selectedTagIds)){
                    $tag_dir = $this->lookupTagDir($item,$property,$tag,$image);
                    $filename = self::name2file($tag->name.'_'.$image->id);
                    $this->extractRegion($feature,storage_path('app'.DIRECTORY_SEPARATOR.$tag_dir),$filename);
                    //$feature->extractSquare(storage_path('app'.DIRECTORY_SEPARATOR.$tag_dir),$filename);
                    
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
                $tag_dir = $this->lookupTagDir($item,$property,$tag,$feature->image);
                $filename = self::name2file($tag->name.'_'.$feature->image->id);
                $this->extractRegion($feature,storage_path('app'.DIRECTORY_SEPARATOR.$tag_dir),$filename);
                //$feature->extractSquare();
            }
        }
        
    }
    
    private function extractRegion($feature,$dir,$filename){
        
        $this->fragments_count++;
        $this->sizes['w'] += $feature->width;
        $this->sizes['h'] += $feature->height;
        
        switch ($this->crop_form) {
            case self::CROP_FORM_SQUARE:
                    $feature->extractSquare($dir,$filename);
                break;

            case self::CROP_FORM_SQUARE_CANVAS:
                    $feature->extractSquareCanvas($dir,$filename);
                break;
            
            default:
                // Original
                $feature->extract($dir,$filename);
                break;
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
    
     private function lookupTagDir($item,$property,$tag, $image){
        $prop_dir = $this->lookupPropDir($item,$property);
        $base = $prop_dir;
        if (! isset($this->sources[$base])){
            $this->sources[$base] = [
                'val' => [],
                'train' => []
                ];
        }
        
        if (! isset($this->f_count[$base])){
            $this->f_count[$base] =  [
                'val' => 0,
                'train' => 0
                ];
        }
        
        if ( $this->toValidate($image,$base)){
            
            //$random = mt_rand ( 0, 99 ) / 100; 
            //if ($random < $this->test){
            //if ($this->tree->getValidateCount($item->id,$property->id,$tag->id)){
            //    $this->tree->decValidateCount($item->id,$property->id,$tag->id);
            $prop_dir = $prop_dir.DIRECTORY_SEPARATOR.'val';
            $this->f_count[$base]['val'] ++;
            if ($image->source){
                $this->sources[$base]['val'][] = $image->source->id;
            }
        }
        else{
            $prop_dir = $prop_dir.DIRECTORY_SEPARATOR.'train';
            $this->f_count[$base]['train'] ++;
            if ($image->source){
                $this->sources[$base]['train'][] = $image->source->id;
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
    
    public function toValidate($image,$base){
        if (! $this->test){
            return false;
        }
        
        if ($image->source){
            if ( in_array($image->source->id,$this->sources[$base]['val'])){
                return true;                
            }
            if ( in_array($image->source->id,$this->sources[$base]['train'])){
                return false;                
            }
        }
        
        if ( isset($this->f_count[$base]['train']) && 
            isset($this->f_count[$base]['val']) &&
                $this->f_count[$base]['train'] > 0 && 
                $this->f_count[$base]['val']/$this->f_count[$base]['train'] < $this->test){
            // Images selected from DB in random order
            return true;
        }
        
        return false;
        //$random = mt_rand ( 0, 99 ) / 100;
        
    }
    
    
    
    /*
    public function toValidate($item_id,$property_id,$tag_id){
        
    }
    */
    public static function name2dir($str){
        return mb_strtolower(strtr($str,' ','_'));
    }
    
    public static function name2file($str){
        return uniqid(self::name2dir($str).'_');
    }
   
    
    public function fillDescription(){
        $parts = [parent::fillDescription()];
        $parts[] = $this->fragments_count. " fragments ";
        if ($this->min_width){
            $parts[] = " Min img width:  ".$this->min_width;
        }
        
        $mw = round($this->sizes['w'] / $this->fragments_count);
        $mh = round($this->sizes['h'] / $this->fragments_count);
        
        $parts[] = " Mean fragment size $mw x $mh";
        $this->description = implode("; ",$parts);
        return $this->description;
    }
    
}
