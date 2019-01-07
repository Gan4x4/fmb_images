<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Dataset;

use Illuminate\Support\Facades\DB;

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
            $this->data[$line->item_id][$line->property_id][$line->tag_id] = [
                'count' => $line->count,
                'validate' => intval(round($line->count * $test))
            ];
            
        }
    }
    
    public function dump(){
        dump($this->data);
    }
    
    public function count($item_id,$property_id,$tag_id){
        return $this->data[$item_id][$property_id][$tag_id]['count'];
    }
    
    public function getValidateCount($item_id,$property_id,$tag_id){
        return $this->data[$item_id][$property_id][$tag_id]['validate'];
    }
    
    public function decValidateCount($item_id,$property_id,$tag_id){
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
