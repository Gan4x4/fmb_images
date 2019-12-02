<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Dataset;

use App\Item;
use App\Tag;
use App\Image;
use Illuminate\Support\Facades\Storage;

/**
 * Build Image list for YOLO
 * https://github.com/AlexeyAB/darknet#how-to-train-to-detect-your-custom-objects
 *
 * @author gan
 */
class Darknet extends Dataset{
    const IMAGES_SUBDIR = 'images'; 
    const CLASSES_FILENAME = 'classes.names';
    
    
    protected $cfg_templates = [
        0 =>'yolov3-tiny_fmb.cfg',
        1 =>'yolov3_fmb.cfg'
    ];
    
    protected $input_tree = null;
    public $classes = null;
    protected $path = '';
    protected $model_cfg = null;
    
    
    
    public function __construct($params) {
        $this->items = $params['items'];
        $this->path = $params['path'];
        $this->max_width = $params['max_width'];
        $this->free_only = boolval($params['free']);
        $this->test = floatval($params['validate']);
        $this->input_tree = self::tree2array($params);
        //dump($this->input_tree);
        $this->classes = $this->extractClasses();
        $this->setCfgTemplate($params);
    }
    
    private function extractClasses(){
        $out = [];
        foreach($this->input_tree as $item_id => $properties){

            $item = Item::find($item_id);
            if (empty($properties)){
                
                $out[] = $item->name;
            }elseif (count($properties) == 1 ){
                $keys = array_keys($properties);
                $key = $keys[0];
                foreach($properties[$key] as $tag_id){
                    $out[] = $this->tagIdToClassName($tag_id, $item->name);
                }
            }
            else{
                throw new Exception("Only one property can be selected for darknet.");
            }
        }
        
        array_walk($out,function(&$item,$key){
           return $item = mb_strtolower($item);
        });
        
       sort($out);
       return $out;
    }
    
    private function setCfgTemplate($params){
         if ( $params['model'] && in_array($params['model'],$this->cfg_templates) ){
            $this->model_cfg =$params['model'];
        }else{
            $this->model_cfg = $this->cfg_templates[0];
        }
    }
    
    private function tagIdToClassName($tag_id, $perfix = ''){
        if ($tag_id == 0){
            $name = 'undefined';
        }
        else{
            $tag = Tag::find($tag_id);
            $name = $tag->name;
        }
        if ($perfix){
            $name = $perfix.'_'.$name;
        }
        return mb_strtolower($name);
    }

    public function build($dir) {

        $this->dir = $dir;
        
        $images_dir = $this->getImagesSubdir();
        //Storage::makeDirectory($this->dir);
        Storage::makeDirectory($images_dir);
        $this->saveClasses($this->getFilePath(self::CLASSES_FILENAME));

        $class_to_key = array_flip($this->classes);

        $imageIds = $this->findImages();
        $img_files = [];
        $i = 0;
        
        $directory = $this->path ? $this->path.DIRECTORY_SEPARATOR : '';
        foreach($imageIds as $image_id){
            $image = Image::find($image_id);
            if (! $this->checkImage($image)){
                // Bypass images that not statisfy size condition
                continue;
            }
            
            $source = storage_path('app'.DIRECTORY_SEPARATOR.$image->path);
            $image_file_name = $image->id.".jpg";//.pathinfo($source,PATHINFO_EXTENSION);
            $img_files[] = $directory.self::IMAGES_SUBDIR.DIRECTORY_SEPARATOR.$image_file_name;
            $target = storage_path('app'.DIRECTORY_SEPARATOR.$images_dir.DIRECTORY_SEPARATOR.$image_file_name);
            copy($source,$target);
            
            $features = $image->features;
            $text = [];
            foreach($features as $feature){
                $class_name = $this->getFeatureClass($feature);
                if ($this->hasClass($class_name)){
                    $class_num = $class_to_key[$class_name];
                    $coords = $this->coord2darknet($feature,$image);
                    $text[] = array_merge([$class_num],$coords);
                }
            }
            if (empty($text)){
                throw new Exception("Image without expected features: ".$image->id);
            }

            $text_file_name = $image->id.".txt";
            $this->saveDescriptions(storage_path('app'.DIRECTORY_SEPARATOR.$images_dir.DIRECTORY_SEPARATOR.$text_file_name),$text);
            unset($image);
            $i++;
            if ($i % 1000 == 0){
                \Log::debug(" Images processed :".$i." of ".count($imageIds));
            }
        }
        
        \Log::debug("All Images processed.");
        $this->makeCfg($this->getFilePath('yolo3_fmb.cfg'),count($this->classes));
        $this->makeDataFile($this->getFilePath('fmb.data'),count($this->classes));
        
        $this->image_count = count($img_files);
        if ($this->image_count > 0){
            $this->divideToTranAndTest($img_files);
        }
        \Log::debug("Divide to train and test finished.");
        $target = $this->dir.DIRECTORY_SEPARATOR.'compressed.zip';
        //$this->zip(storage_path('app'.DIRECTORY_SEPARATOR.$this->dir), storage_path('app'.DIRECTORY_SEPARATOR.$target));
        self::zip2(storage_path('app'.DIRECTORY_SEPARATOR.$this->dir), 'compressed.zip');
        
        \Log::debug("Zip file created.");
        $this->fillDescription();
        return $target;

    }
    
    public function getImagesSubdir(){
        if (self::IMAGES_SUBDIR){
            return $this->dir.DIRECTORY_SEPARATOR.self::IMAGES_SUBDIR;
        }
        return $this->dir;
    }
    
    private function getFilePath($file){
        return storage_path('app'.DIRECTORY_SEPARATOR.$this->dir.DIRECTORY_SEPARATOR.$file);
    }
    
    private function saveClasses($file){
        $handle = fopen($file, "w+");
        foreach($this->classes as $class_name){
            fwrite($handle,$class_name.PHP_EOL);
        }
        fclose($handle);
        return true;
    }
    
    
   
    
    private function coord2darknet($feature,$image){
        
        $x = ($feature->x1 + ($feature->width / 2)) / $image->width;
        $y = ($feature->y1 +  ($feature->height /2 )) / $image->height;
        $w = $feature->width/$image->width;
        $h = $feature->height/$image->height;
        
        $out = [$x, $y, $w, $h];
        
        
        array_walk($out, function(&$item,$key){
            $item = number_format($item,6);
        });
        
        return $out;
    }
    
    private function divideToTranAndTest($img_files){
        
        $test_images = [];
        $train_images = [];
        $key_count = round($this->test*count($img_files));
        if ($this->test > 0 && $key_count > 0){
            $rand_keys = array_rand($img_files, $key_count);
            $test_images = array_intersect_key($img_files, array_flip($rand_keys));
            $train_images = array_diff($img_files,$test_images);
        }else{
            $train_images = $img_files;
        }
        

        if (! empty($test_images) ){
            $this->array2file($this->getFilePath('test.txt'), $test_images);
        }
        
        $this->array2file($this->getFilePath('train.txt'), $train_images);
        
    }
    
    
    private function makeCfg($filename,$classes_count){
        $text = file_get_contents(resource_path('assets'.DIRECTORY_SEPARATOR.'template'.DIRECTORY_SEPARATOR.$this->model_cfg));
        $changed = str_replace('{{$classes}}', $classes_count, $text);
        $filters = ( $classes_count + 5 ) * 3;
        $changed = str_replace('{{$filters}}', $filters , $changed);
        file_put_contents($filename, $changed);
    }
    
    
     private function makeDataFile($filename,$classes_count){
        $text = file_get_contents(resource_path('assets'.DIRECTORY_SEPARATOR.'template'.DIRECTORY_SEPARATOR.'fmb.data'));
        $changed = str_replace('{{$classes}}', $classes_count, $text);
        if ($this->path){
            $full_path = $this->path.DIRECTORY_SEPARATOR;
        }else{
            $full_path = '';
        }
        $changed = str_replace('{{$path}}', $full_path, $changed);
        file_put_contents($filename, $changed);
    }
    
    private function getItemIds(){
        return $this->items;
    }
   
    
    private function saveDescriptions($file,$lines){
        $handle = fopen($file, "w+");
        foreach($lines as $line){
            $str = implode(' ',$line);
            fwrite($handle,$str.PHP_EOL);
        }
        fclose($handle);
    }
    
    
    public function findImages(){
        $itemIds = array_keys($this->input_tree);
        $imageIds = [];
        foreach($itemIds as $item_id){
            $item = Item::findOrFail($item_id);
            $features = $item->features()->inRandomOrder()->get();
            foreach($features as $feature){
                if ( $this->checkImage($feature->image) && $this->getFeatureClass($feature)){
                    $imageIds[] = $feature->image_id;
                }
            }
        }
        return array_unique($imageIds);
    }
    
    protected function checkImage($image){
        
        if ( parent::checkImage($image) ){
            
            // Bypass images from validation set
            if ($image->validation){
                return false;
            }
            return true;
        }
        else{
            return false;
        }
    }
    
    
    private function getFeatureClass($feature){
        $item = $feature->getItem(); 
        if (! $item){
            throw new \Exception("Feature(".$feature->id.") has't item");
        }
        if ($this->hasClass($item->name)){
            return mb_strtolower($item->name);
        }

        foreach($feature->properties as $prop){
            $class = $this->tagIdToClassName($prop->tagId(), $item->name);
            if ($this->hasClass($class)){
                return $class;
            }
        }
        return false;
    }
    
    private function hasClass($name){
        $class_name = mb_strtolower($name);
        return in_array($class_name,$this->classes);
    }
      
    protected function array2file($file,$arr){
        $handle = fopen($file, "a");
        foreach($arr as $line){
            fwrite($handle,$line.PHP_EOL);
        }
        fclose($handle);
    }
    
    
    public function fillDescription(){
        $parts = [parent::fillDescription()];
        $parts[] = "Classes : ".implode(", ",$this->classes);
        $this->description = implode("; ",$parts);
        return $this->description;
    }
    

}
