<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Tag;

class Property extends Model
{
    //protected $guarded = ['id','created_at','updated_at'];
    protected $fillable = ['name','description'];
    protected $estimated_tag_id = null;
    public $estimation_source = null;
    
    public function items(){
        return $this->belongsToMany('App\Item');
    }
    
    public function tags(){
        return $this->belongsToMany('App\Tag');
    }
    
    public function getItemTags($item_id){
        $query = $this->prepareTagsQuery($item_id);
        $query->select('tag_id',DB::raw('count(tag_id) as count'));
        $query->groupBy('tag_id');
        $result = $query->get();
        $tagIds = [];
        $count = [];
        foreach($result as $line){
            $tagIds[] = $line->tag_id;
            $count[$line->tag_id] = $line->count;
        }
        
        $tags = Tag::whereIn('id', $tagIds)->OrderBy('name')->get();
        foreach($tags as $t){
            $t->count = $count[$t->id];
        }
        return $tags;
    }
    
    
    private function prepareTagsQuery($item_id){
        $query = DB::table('bindings');
        if (! $item_id){
            $item_id = $this->getItemId();
        }
        if ($item_id){
            $query->where('item_id',$item_id);
        }

        $query->where('property_id',$this->id);
        $query->whereNotNull('tag_id');
        $query->where('tag_id','<>',0);
        return $query;
    }
    
    
   public function getLastTagIds($count = 5, $item = null){
        $query = $this->prepareTagsQuery($item);
        $query->select('tag_id','updated_at');
        $query->whereNotNull('updated_at');
        $query->distinct('tag_id');
        $query->orderBy('updated_at','DESC');
        $query->take(100);
        $unique = array_unique($query->pluck('tag_id')->toArray());
        return array_slice($unique,0,$count);
    }
    
    public function getPopularTags($count = 5, $item = null){
        
        $last_count = intval($count/2);
        $last = $this->getLastTagIds($last_count,$item);
        
        
        $query = $this->prepareTagsQuery($item);
        $query->select('tag_id',DB::raw('count(feature_id) as fc'));
        $query->whereNotIn('tag_id',$last);
        $query->groupBy('tag_id');
        $query->orderBy('fc','DESC');
        $query->take($count-$last_count);
        $popular = $query->pluck('tag_id')->toArray();
        $tag_ids = array_merge($last,$popular);
        return Tag::whereIn('id', $tag_ids)->OrderBy('name')->get();
    }
    
    public function tagId(){
        if ($this->pivot && $this->pivot->tag_id){
            return $this->pivot->tag_id;
        }
        if ($this->estimated_tag_id){
            return $this->estimated_tag_id;
        }
        return null;
    }
    
    private function getItemId(){
        if ($this->pivot){
            return $this->pivot->item_id;
        }
        return null;
    }
    
    public function getTag(){
        if ($this->tagId()){
            return Tag::find($this->tagId());
        }
        return null;
    }
    
    public function getTagName(){
        $tag = $this->getTag();
        if ($tag){
            return $tag->name;
        }
        return null;
    }
    /*
    public function setTagForFeature($featureId,$itemId){
         $line =  DB::table('bindings')->
                where('feature_id', $featureId)->
                where('item_id', $itemId)->
                where('property_id', $this->id)->first();
         
        if ($line ){
            //$this->pivot->tag_id = Tag::findOrFail($line->tag_id);
            return true;
        }
        return false;
    }
    */
    
    /* If filled properties not exists
    * try find another properties belonged to this image
    * or it's siblings and copy its/ value 
    */
    public function setEstimatedTag($image,$item){
        if ($this->tagId()){
            // Tag already set
            return;
        }
        $images = $image->getSiblings();
        $images->add($image);
        foreach($images as $image){
            foreach($image->features as $f){
                $f_item = $f->getItem();
                if ($f_item && $f_item->canBeCopied()){
                    foreach($f->properties as $p){
                        if ( $p->id == $this->id && $f_item->id == $item->id && $p->tagId()){
                            $this->estimated_tag_id = $p->tagId();
                            $this->estimation_source = "From image #".$image->id.", ".$f_item->name;
                            return true;
                        } 
                    }
                }
            }
        }
        return false;
    }
    
    
    // Override
    public function delete(){
        $this->tags()->detach();
        $this->items()->detach();
        return parent::delete();
    }
    
    public function isSearchable(){
        return in_array($this->name,['Brand']);
    }
    
    public function isManualInput(){
        return in_array($this->name,['Model']);
    }
    
    public function lookupTag($tag_name){
        if ( empty($tag_name)){
            return null;
            //throw new \Exception("Invalid tag name");
        };
        $tag_name = trim($tag_name);
        $tag = Tag::where('name',$tag_name)
            ->orWhere(DB::raw("LOWER(name) LIKE '".mb_strtolower($tag_name)."'"))->first();
        if (! $tag){
            $tag = Tag::create(['name'=>$tag_name]);
        }
            
        if (! $this->tags()->where('tags.id',$tag->id)->exists()){
            $this->tags()->attach($tag->id);    
        }
        
        return $tag;
    }
    
    
    public function count(){
        return DB::table('bindings')
            ->where('property_id',$this->id)
            ->where('item_id',$this->getItemId())
            ->whereNotNull('tag_id')
            ->where('tag_id','<>',0)
            ->distinct('feature_id')
            ->count('feature_id');
    }
    
}
