<?php

namespace App\Dataset;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Build extends Model
{
    
    const STATE_NEW =0;
    const STATE_WORK = 1;
    const STATE_FINISH = 2;
    const STATE_ERROR = 3;
    
    const DARKNET = 1;
    const CLASSIFIER = 2;
    
     protected $casts = [
        'params' => 'array',
    ];
     
     
    // Override
    public function save(array $options = array()){
        $this->storeDatasetId();
        
        if ($this->state === null){
            $this->state = self::STATE_NEW;
        }
        parent::save($options);
    }
    
    public function delete(){
        $directory = $this->dir;
        parent::delete();
        Storage::deleteDirectory($directory);
    }
    
    
    protected function getTypeName(){
        $types = __('common.build_type');
        return $types[$this->params['type']];
    }
    
    protected function fillDescription(){
        $this->description = $this->getTypeName();
    }
    
    protected function storeDatasetId(){
        $this->dataset_id = $this->params['type'];
    }
    
    protected function getItems(){
        return $this->params['items'];
    }
    
    public function make(){
        $dataset = $this->getDataset();
        $this->state = self::STATE_WORK;
        $this->save();
        try{
            //\Log::debug("ST: ".$this->dir);
            //\Log::debug(var_export($this->params,true));
            //\Log::debug(var_export($dataset,true));
            $this->file = $dataset->build($this->dir);
            $this->state = self::STATE_FINISH;
            $this->fillDescription();
        }
        catch(\Exception $e){
            $this->description = $e->getMessage();
            $this->state = self::STATE_ERROR;
            //\Log::debug($e->getMessage());
            //\Log::debug($e->getMessage());
            throw $e;
        }
        finally{
            $this->save();    
        }
        
    }
    
    
    protected function getDataset(){
        //$items = $this->getItems();
        switch ($this->params['type']) {
            case self::DARKNET:
                $dataset = new Darknet($this->params['items']);
                break;
            
            case self::CLASSIFIER:
                $dataset = new ImageFolderClassifier($this->params);
                break;

            default:
                $dataset = new ImageFolder($this->params);
                $dataset->subdirs = isset($this->params['subdirs']);
        } 
        return $dataset;
    }
    
    
    
    public function getLink(){
        if ($this->state != self::STATE_FINISH){
            return null;
        }
        return Storage::url($this->file);
    }
    
    public function getStateName(){
        $names = __('common.build_name');
        
        if (isset($names[intval($this->state)])){
            return $names[intval($this->state)];
        }
        return "Invalid state";
    }
    
    public function isActive(){
        return in_array($this->state,[self::STATE_WORK]);
    }
    
}
