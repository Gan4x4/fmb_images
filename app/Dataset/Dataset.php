<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Dataset;

use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use \Exception;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\Parser\Source;

abstract class Dataset {
    
    public $test = .20;
    protected $description = null;
    protected $max_width = null;
    protected $free_only = false;
    protected $image_count = 0;
    
    abstract public function build($dir);
    
    
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
    
    
    /*
     * transform request with tree to array 
     * 
     */
    public static function tree2array($params){
        if (! isset($params['items'])){
            return null;
        }
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
        return $tmp;
    }
    
    public function getDescription(){
        return $this->description;
    }
    
    protected function fillDescription(){
        $parts[] = $this->image_count. " imgs";
        if ($this->max_width){
            $parts[] = "Max width: ".$this->max_width;
        }
        $this->description = implode("; ",$parts);
        return $this->description;
    }
    
    protected function checkImage($image){
        if (! $image->user){
            return false;
        }
        
        // If max_width set, bypass big images
        if ( $this->max_width > 0){
           return $image->width <= $this->max_width;    
        }
        
         // If max_width set, bypass big images
        if ( $this->free_only && $image->source){
           if ($image->source->type == Source::TYPE_FMB){
               return false;
           }    
        }
       
       
        return true;
    }
    
    
    public static function zip2($source,$target){
        //$target = 'compressed.zip';
        $process = new Process("zip -r $target .");
        $process->setWorkingDirectory($source);
        // use start() for async executing
        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        $raw_output = $process->getOutput();
        return $raw_output;
    }
}
