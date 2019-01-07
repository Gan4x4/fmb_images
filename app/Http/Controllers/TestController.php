<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Parser\Avito;
use App\Image;

class TestController extends Controller
{

    public function index()
    {
        $parser = Avito::createByUrl("https://www.avito.ru/moskva/velosipedy/trek_remedy_27.5_2015_1410772984");
        dump($parser->getAllImages());
    }
    
    
    public function bike2frame()
    {
        
        //$images = Image::orderBy('id')->take(3)->get();
        $images = Image::all();
        foreach($images as $image){
            print " Image <a href='".route('images.edit',$image->id)."'>".$image->id."</a><br>";    
            $bike = null;
            $frame = null;
            
            foreach($image->features as $feature){
                $item = $feature->getItem();
                if ($item && $item->name == 'Bike' ){
                    $bike = $feature;
                }
                elseif($item && $item->name == 'Frame' ){
                    $frame = $feature;
                }
            }    
            
            if ($bike && $frame){

                foreach($bike->properties as $bike_prop){
                    
                    if (! in_array($bike_prop->name,['Color','Second color'])){
                        print "Not a color ".$bike_prop->name."<br>";
                        continue;
                    }
                    
                    $frame_props = $frame->properties()->where('properties.id',$bike_prop->id)->get();
                    $frame_prop = $frame_props->first();
                    if ($bike_prop->tagId() &&  (! $frame_prop || (! $frame_prop->tagId()) ) ){
                        
                        
                        $frame->properties()->attach($bike_prop->id,[
                                'feature_id' => $frame->id,
                                'tag_id' => $bike_prop->tagId(),
                                'item_id' => $frame->getItem()->id
                            ]);
                        print "Attached prop ".$bike_prop->name." ".$bike_prop->getTagName()."<br>";
                        $bike->properties()->detach($bike_prop->id);
                    }else{
                       if ($bike_prop){
                           print "Bike pn ". $bike_prop->name;
                       }
                       
                       if ($frame_prop){
                           print " Frame pn ".$frame_prop->name." FP tn".$frame_prop->getTagName();
                       }
                       print "<br>"; 
                    }
                }
            }else{
                print "Bike ".boolval($bike)." frame ".boolval($frame)."<br>";
            }
            
            print "<hr>";
        }
        
    }
   
    public function withoutFrame()
    {
        
        $images = Image::whereNotNull('user_id')->get();
        foreach($images as $image){
            $frame = null;
            foreach($image->features as $feature){
                $item = $feature->getItem();
                if($item && $item->name == 'Frame' ){
                    $frame = $feature;
                }
            }    
            
            if (! $frame){
                print " Image <a href='".route('images.edit',$image->id)."'>".$image->id."</a><br>";    
            }
            

        }
    }


    
    
}
