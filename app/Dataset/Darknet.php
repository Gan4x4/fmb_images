<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Dataset;

use App\Item;
use App\Image;
use Illuminate\Support\Facades\Storage;

/**
 * Build Image list for YOLO
 * https://github.com/AlexeyAB/darknet#how-to-train-to-detect-your-custom-objects
 *
 * @author gan
 */
class Darknet extends Dataset{
    
    
    
    protected $cfg_template = 'template/yolov3_fmb.cfg';
    
    public function __construct($items) {
       $this->items = $items;
    }

    public function build($dir) {

        $this->dir = $dir;
        Storage::makeDirectory($this->dir);
        $classes = $this->saveClasses(storage_path('app'.DIRECTORY_SEPARATOR.$this->dir.DIRECTORY_SEPARATOR.'classes.names'));

        $class_to_key = array_flip($classes);
        // Properties ignored

        $itemIds = $this->getItemIds();
        $imageIds = $this->findImages($itemIds );
        $img_files = [];
        $directory = 'fmb_data';
        foreach($imageIds as $image_id){
            $image = Image::find($image_id);

            
            $source = storage_path('app'.DIRECTORY_SEPARATOR.$image->path);
            $image_file_name = $image->id.".jpg";//.pathinfo($source,PATHINFO_EXTENSION);
            $img_files[] = $directory.DIRECTORY_SEPARATOR.$image_file_name;
            
            $target = storage_path('app'.DIRECTORY_SEPARATOR.$this->dir.DIRECTORY_SEPARATOR.$image_file_name);
            copy($source,$target);
            
            $features = $image->features;
            $text = [];
            foreach($features as $feature){
                $f_item = $feature->getItem();
                if ($f_item && in_array($f_item->id,$itemIds )){
                    $class_num = $class_to_key[mb_strtolower($f_item->name)];
                    $coords = $this->coord2darknet($feature,$image);
                    $text[] = array_merge([$class_num],$coords);
                }
            }
            $text_file_name = $image->id.".txt";
            $this->saveDescriptions(storage_path('app'.DIRECTORY_SEPARATOR.$this->dir.DIRECTORY_SEPARATOR.$text_file_name),$text);
        }
        

        $this->makeCfg(storage_path('app'.DIRECTORY_SEPARATOR.$this->dir.DIRECTORY_SEPARATOR.'yolo3_fmb.cfg'),count($classes));
        $this->divideToTranAndTest($img_files);
        
        $target = $this->dir.DIRECTORY_SEPARATOR.'compressed.zip';
        $this->zip(storage_path('app'.DIRECTORY_SEPARATOR.$this->dir), storage_path('app'.DIRECTORY_SEPARATOR.$target));
        return $target;

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
        
        if ($this->test > 0){
            $key_count = round($this->test*count($img_files));
            $rand_keys = array_rand($img_files, $key_count);
            $test_images = array_intersect_key($img_files, array_flip($rand_keys));
            $train_images = array_diff($img_files,$test_images);
        }else{
            $train_images = $img_files;
        }
            
        if (! empty($test_images) ){
            $this->array2file(storage_path('app'.DIRECTORY_SEPARATOR.$this->dir.DIRECTORY_SEPARATOR.'test.txt'), $test_images);
        }
        
        $this->array2file(storage_path('app'.DIRECTORY_SEPARATOR.$this->dir.DIRECTORY_SEPARATOR.'train.txt'), $train_images);
        
    }
    
    
    private function makeCfg($filename,$classes_count){
        $text = file_get_contents(resource_path('assets'.DIRECTORY_SEPARATOR.$this->cfg_template));
        $changed = str_replace('{{$classes}}', $classes_count, $text);
        $filters = ( $classes_count + 5 ) * 3;
        $changed = str_replace('{{$filters}}', $filters , $changed);
        file_put_contents($filename, $changed);
        
    }
    
    
    private function getItemIds(){
        return $this->items;
    }
    
    private function saveClasses($file){
        $itemIds = $this->getItemIds();
        $handle = fopen($file, "w+");
        $out = [];
        foreach($itemIds as $item_id){
            $item = Item::findOrFail($item_id);
            $class = mb_strtolower($item->name);
            fwrite($handle,$class.PHP_EOL);
            $out[] = $class;
        }
        fclose($handle);
        return $out;
    }
    
    private function saveDescriptions($file,$lines){
        $handle = fopen($file, "w+");
        foreach($lines as $line){
            $str = implode(' ',$line);
            fwrite($handle,$str.PHP_EOL);
        }
        fclose($handle);
        //return file($file);
    }
    
    
    private function findImages($itemIds){
        $imageIds = [];
        foreach($itemIds as $item_id){
            $item = Item::findOrFail($item_id);
            $features = $item->features;
            foreach($features as $feature){
                $imageIds[] = $feature->image_id;
            }
        }
        return array_unique($imageIds);
    }
    
    private function lookupItemDir($item){
        $item_dir = $this->dir.DIRECTORY_SEPARATOR.mb_strtolower($item->name);
        if (! Storage::exists($item_dir)){
            Storage::makeDirectory($item_dir);
        }
        return $item_dir;
    }
    
    
    protected function array2file($file,$arr){
        $handle = fopen($file, "a");
        foreach($arr as $line){
            //$str = implode(' ',$line);
            fwrite($handle,$line.PHP_EOL);
        }
        fclose($handle);
    }
    

}
