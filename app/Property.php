<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Tag;

class Property extends Model
{
    //protected $guarded = ['id','created_at','updated_at'];
    protected $fillable = ['name','description'];
    
    /*
    public static function getModel(){
        return Property::where('name','Model')->first();
    }
    */
    
    public function items(){
        return $this->belongsToMany('App\Item');
    }
    
    public function tags(){
        return $this->belongsToMany('App\Tag');
    }
    
    public function tagId(){
        if ($this->pivot){
            return $this->pivot->tag_id;
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
    
    public function setTagForFeature($featureId,$itemId){
         $line =  DB::table('bindings')->
                where('feature_id', $featureId)->
                where('item_id', $itemId)->
                where('property_id', $this->id)->first();
         
        if ($line ){
            $this->tag = Tag::findOrFail($line->tag_id);
        }
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
    
    
}
