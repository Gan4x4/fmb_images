<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App;

use Illuminate\Support\Facades\Storage;
use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;


/**
 * Description of Dataset
 *
 * @author gan
 */
class Dataset {
    
    private $items = [];
    public $subdirs = true;
    public $dir = null;
    
    public function __construct($items) {
       $this->items = $items;
    }
    
    
    public function build($dir){
        $this->dir = $dir;
        Storage::makeDirectory($this->dir);
        
        foreach($this->items as $item_id => $propIds){
            $item = Item::findOrFail($item_id);
            $features = $item->features;
            if (! $features){
                // No images found
                continue;
            }
            
            foreach($features as $feature){
                if (! $this->hasSelectedProps($item_id)){
                    $this->saveWithoutProps($item,$feature);
                }elseif($this->subdirs){
                //$this->savePropsInSubdirs($item);
                    $this->savePropsInSubdirs($item,$feature);
                }else{
                    //$this->savePropsAsNewItems($item);
                    $this->savePropsAsNewItems($item,$feature);
                }
                
            }
          
        }
        
        $target = $this->dir.DIRECTORY_SEPARATOR.'compressed.zip';;
        $this->zip(storage_path('app'.DIRECTORY_SEPARATOR.$this->dir), storage_path('app'.DIRECTORY_SEPARATOR.$target));

        return $target;
    }
    
    private function hasSelectedProps($itemId){
        return count($this->items[$itemId]) != 0; 
    }
    
    private function lookupItemDir($item){
        $item_dir = $this->dir.DIRECTORY_SEPARATOR.mb_strtolower($item->name);
        if (! Storage::exists($item_dir)){
            Storage::makeDirectory($item_dir);
        }
        return $item_dir;
    }
    
    /*
     *  Save images in folders as:
     *  bike/color/red1.jpg
     *  bike/color/blue1.jpg
     *  ...
     *  bike/brand/99.jpg
     *  bike/brand/34.jpg
     * 
     *  fork/color/99.jpg
     * 
     */
    private function savePropsInSubdirs($item,$feature){
        $item_dir = $this->lookupItemDir($item);//$this->dir.DIRECTORY_SEPARATOR.mb_strtolower($item->name);
        foreach($feature->properties as $property){
            if ( in_array($property->id, $this->items[$item->id])){
                $prop_dir = $item_dir.DIRECTORY_SEPARATOR.mb_strtolower($property->name);
                Storage::makeDirectory($prop_dir);
                $filename = uniqid($this->genNewItemName($item,$property).'_');
                $feature->extract(storage_path('app'.DIRECTORY_SEPARATOR.$prop_dir),$filename);
            }
        }
    }
    
    
    
    private function saveWithoutProps($item,$feature){
        $item_dir = $this->lookupItemDir($item);
        $feature->extract(storage_path('app'.DIRECTORY_SEPARATOR.$item_dir),$feature->id);
    }
    
    
    
    
    /*
     *  Save images in folders as:
     * 
     *  bike/Bike_type_BMX/1.jpg
     *  bike/Bike_type_BMX/2.jpg
     *  bike/Bike_type_MTB/2.jpg
     *  ...
     *  Brake/Brake_type_V/99.jpg
     * 
     */
    
    private function savePropsAsNewItems($item,$feature){
        $item_dir = $this->lookupItemDir($item);
            foreach($feature->properties as $property){
                if (in_array($property->id,$this->items[$item->id]) && $property->getTag()){
                    $prop_dir =  $this->lookupPropDir($item,$property);
                    $filename = uniqid($property->getTag()->name."_");
                    $feature->extract(storage_path('app'.DIRECTORY_SEPARATOR.$prop_dir),$filename);
                }
            }
    }
    
    private function lookupPropDir($item,$property){
        $item_dir = $this->lookupItemDir($item);
        $prop_dir = $item_dir.DIRECTORY_SEPARATOR.$this->genNewItemName($item,$property);
        if( ! Storage::exists($prop_dir)) {
            Storage::makeDirectory($prop_dir);
        }
        return $prop_dir;
    }
    
    private function genNewItemName($item,$property){
        if ($property->getTag()){
            return $property->name.'_'.$property->getTag()->name;
        }
        else{
            $property->name;
        }
    }
    
    
   
    // Helper 
     /* https://stackoverflow.com/questions/45450209/how-to-zip-folder-in-laravel-5
     * 
     */
    
    public function zip($source, $destination){
        if (!extension_loaded('zip') || !file_exists($source)) {
            return false;
        }

        $zip = new ZipArchive();
        if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
            return false;
        }

        $source = str_replace('\\', '/', realpath($source));

        if (is_dir($source) === true)
        {
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

            foreach ($files as $file)
            {
                $file = str_replace('\\', '/', $file);

                // Ignore "." and ".." folders
                if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
                    continue;

                $file = realpath($file);

                if (is_dir($file) === true)
                {
                    $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
                }
                else if (is_file($file) === true)
                {
                    $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
                }
            }
        }
        else if (is_file($source) === true)
        {
            $zip->addFromString(basename($source), file_get_contents($source));
        }

        return $zip->close();
    }
    
    
}
