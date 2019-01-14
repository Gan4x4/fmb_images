<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Dataset;

use Illuminate\Support\Facades\DB;
use App\Item;

class Tree {
    
    public $data = [];
    
    
    public function __construct($items = null,$test = 0 ) {
        $query = DB::table('bindings');
        if ($items != null){
            $query->whereIn('item_id',$items);
        }
        $query->select('item_id','property_id','tag_id',DB::raw('count(tag_id) as count'))
              ->groupBy('item_id','property_id','tag_id')
              ->orderBy('item_id','property_id','tag_id');
        
        //dump($query->toSql());
        $lines = $query->get();
        
        foreach($lines as $line){
            if (! $line->tag_id){
                $this->data[$line->item_id][$line->property_id][$line->tag_id] = [
                    'count' => 0,
                    'validate' => 0
                ];
            }
            $this->data[$line->item_id][$line->property_id][$line->tag_id] = [
                'count' => $line->count,
        //        'validate' => intval(round($line->count * $test))
            ];
            
        }
        
        $this->addUndefined();
        
        $this->setValidation($test);
        //\Log::debug("Data");
        //\Log::debug($this->data);
    }
    
    public function addUndefined(){
        foreach($this->data as $item_id => $data){
            $item = Item::findOrFail($item_id);
            //dump($item);
            $features = $item->features;
            foreach($features as $feature){
                $undefined = $feature->getUndefinedProperties();
                foreach($undefined as $p){
                    if (! isset($this->data[$item->id][$p->id][0])){
                        $this->data[$item->id][$p->id][0] = [
                            'count' => 0,
                            'validate' => 0
                            ];
                    }
                    $this->data[$item->id][$p->id][0]['count'] += 1;
                    //$this->data[$item->id][$p->id][0]['count'] += 1;
                }
            }
        }
    }
    
    
    public function setValidation($test){
        foreach($this->data as $item_id => $properties){
            foreach($properties as $property_id => $tags){
                foreach($tags as $tag_id => $info){
                    $count = $info['count'];
                    $this->data[$item_id][$property_id][$tag_id]['validate'] = intval(round($count * $test));
                }
            }
        }
    }
    
    public function dump(){
        dump($this->data);
    }
    
    public function count($item_id,$property_id,$tag_id){
        $tag_id = intval($tag_id);
        return $this->data[$item_id][$property_id][$tag_id]['count'];
    }
    
    public function getValidateCount($item_id,$property_id,$tag_id){
        $tag_id = intval($tag_id);
        return $this->data[$item_id][$property_id][$tag_id]['validate'];
    }
    
    public function decValidateCount($item_id,$property_id,$tag_id){
        $tag_id = intval($tag_id);
        $val = $this->getValidateCount($item_id,$property_id,$tag_id);
        if ($val > 0){
            $val = $val -1;
        }
        $this->data[$item_id][$property_id][$tag_id]['validate'] = $val;
    }
    
    /*
    public function divide($validate = 0.15){
        foreach($this->data as $item_id => $propertys){
            foreach($propertys as $property_id => $tags){
                foreach($tags as $tag_id=>$tag_info){
                    $key_count = round($this->test*count($selectedTagIds));
                }
                
            }
            
        }
        
        $key_count = round($this->test*count($selectedTagIds));
                    $rand_keys = array_rand($selectedTagIds, $key_count);
                    $validate_tag_ids = array_intersect_key($selectedTagIds, array_flip($rand_keys));
                    $train_tag_ids = array_diff($selectedTagIds,$validate_tag_ids);
        
    }
     * 
     */
}
