<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Dataset;

use App\Image;
use Illuminate\Support\Facades\Storage;

/**
 *
 * Save images with full description to validate entire system
 */
class Validation extends Dataset {

    public $subkeys = true;
    
     public function __construct($params) {
        $this->subkeys = isset($params['subkeys']) && boolval($params['subkeys']);
    }
    
    public function build($dir) {
        $this->dir = $dir;
        Storage::makeDirectory($dir);
        $images = Image::where('validation',true)->get();
        $count = 0;
        foreach($images as $image){
            $text = $this->extractDescription($image);
            if ($text){
                $source = storage_path('app'.DIRECTORY_SEPARATOR.$image->path);
                $image_file_name = $image->id.".jpg";
                $target_dir = storage_path('app'.DIRECTORY_SEPARATOR.$dir);
                copy($source,$target_dir.DIRECTORY_SEPARATOR.$image_file_name);
                $json = json_encode($text);
                file_put_contents($target_dir.DIRECTORY_SEPARATOR.$image->id.".json",$json);
                $count++;
            }
        }

        if ($count){
            $target = $this->dir.DIRECTORY_SEPARATOR.'compressed.zip';
            $this->zip(storage_path('app'.DIRECTORY_SEPARATOR.$this->dir), storage_path('app'.DIRECTORY_SEPARATOR.$target));
            return $target;
        }
        
        return null;
       
    }
    
    private function extractDescription($image){
        $features = $image->features;
        $text = [];
        foreach($features as $feature){
            $name = mb_strtolower($feature->getName());
            $props = $feature->getDescription();
            $out = [];
            foreach($props as $key=>$p){
                $clear_name =  mb_strtolower(strtr($p,' ','_'));
                $clear_key = mb_strtolower(strtr($key,' ','_'));
                if ($this->subkeys){
                    $out[$clear_key] = $clear_name;
                }
                else{
                    $out[] = $clear_name; 
                }
            }
            $text[$name] = $out;
        }
        
        if (empty($text)){
            return null;
        }
        
        return $text;
        
    }

}
