<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Item;
use App\Property;

use App\Image;
use App\Jobs\DatasetBuilder;
use App\Dataset\Build;


class ItemController extends Controller
{
    const DARKNET = 1;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = Item::all();
        $items->sort(function($a,$b){
            
            if ($a->parent == $b->parent){
                return strcasecmp($a->name,$b->name);
            }
            else{
                return $a->parent - $b->parent;
            }
        });
        
        return view('item.index')->with([
            'items' => $items
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $properties = Property::all();
         return view('item.create')->with([
            'items' => $this->getItemSelect(),
            'properties' => $properties,
            'selected_properties' => []
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $item = Item::create($request->all());
        $this->attachProperties($request,$item);
        return redirect()->route('items.index'); 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = Item::findOrFail($id);
        $properties = Property::all();
        $selected = [];
        foreach($item->properties as $p){
            $selected[] = $p->id;
        }
        return view('item.edit')->with([
            'item' => $item,
            'items' => $this->getitemSelect(),
            'properties' => $properties,
            'selected_properties' => $selected
        ]);
    }
    
    public static function getItemSelect($exceptId = []){
        $items = Item::all();
        $filtered = $items->filter(function ($value, $key) use ($exceptId) {
            return ! in_array($value->id,$exceptId);
        });
        
        return self::collection2select($items,[null=>"No"]);
    }
    /*
    public static function getPropertySelect($exceptId = []){
        $props = Property::all();
        $filtered = $props->filter(function ($value, $key) use ($exceptId) {
            return ! in_array($value->id,$exceptId);
        });
        return self::collection2select($props);
    }
    */

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $item = Item::findOrFail($id);
        $item->fill($request->all());
        $item->save();
        $item->properties()->detach();
        $this->attachProperties($request,$item);
        return redirect()->route('items.index'); 
    }

    
    
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = $this->getAjaxResponse();
        try{
            $item = Item::findOrFail($id);
            $item->delete();
        }catch( \Exception $e){
            $result = $this->getAjaxResponse(1, $e->getMessage());
        }
        return response()->json($result);
    }
    
    public function properties(Request $request,$itemId){
        $item = Item::findOrFail($itemId);
        $image = Image::find($request->image_id);
        $properties = $item->getPrefilledProperties($image);
        return view('feature.properties',[
            'properties' => $properties,
                'image' =>$image,
            ]);
    }
    
/*
   
    public function build(Request $request){
        
        $dir = 'public/features/'.uniqid("build_");
        if ($request->type == self::DARKNET){
            $dataset = new Darknet($request->items);
        }else{
        
            $items = [];
            foreach($request->items as $item_id){
                $items[$item_id]=[];
                $propKey = $item_id.'_propertys';
                if ($request->has($propKey)){
                    //dump($request->$propKey);
                    foreach($request->$propKey as $prop_id){
                        $items[$item_id][] = $prop_id;
                    }
                }
            }
            $dataset = new ImageFolder($items);
            $dataset->subdirs = $request->has('subdirs');
        }
        
        $target = $dataset->build($dir);
        return view('item.build')->with([
                'zip' => Storage::url($target)
                ]); 
        
    }
  */  
    public function build(Request $request){
        
        $dir = 'public/features/'.uniqid("build_");

        $build = new Build();
        $build->params = $request->all();
        //dd($build->params);
        $build->dir = $dir;
        //$build->minimalPropertyCount = intval($request->min_prop);
        //$build->test = floatva($request->validate);
        $build->save();
        
        DatasetBuilder::dispatch($build);
       
        return redirect()->route('builds.index');
        
       
        
    }
    
}
