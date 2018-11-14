<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Feature;
use App\Item;
use App\Image;

class FeatureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($imageId)
    {
        $image = Image::findOrFail($imageId);
        return view('feature.index',[
            'features' => $image->features
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($imageId)
    {
        $image= Image::findOrFail($imageId);
        $feature = new Feature();
        $feature->image_id = $image->id;
        $items = Item::all();
        $item = $image->getPrposedItem();
                
        return view('feature.edit')->with([
                    'feature' => $feature,
                    'items' => $items,
                    'item_id' => $item->id,
                    'properties' => $item->properties,
                    'image' => $image
                ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$imageId)
    {
        try{
            $request->request->add(['image_id'=>$imageId]);
            $feature = Feature::create($request->all());
            $this->updateProperties($request,$feature);
            return $this->getAjaxResponse();
        } catch (\Exception $e){
            return $this->getAjaxResponse(1,$e->getMessage());
        }
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
    public function edit($imageId,$featureId)
    {
        $feature = Feature::findOrFail($featureId);
        $item = $feature->getItem();
        return view('feature.edit')->with([
                    'feature' => $feature,
                    'item_id' => $item->id,
                    'items' => Item::all(),
                    'properties' => $feature->properties,
                    'image' => $feature->image
                ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $imageId, $featureId)
    {
        $feature = Feature::findOrFail($featureId);
        $feature->fill($request->all()); // Update only coords and description
        $feature->save(); // To obtain id
        $this->updateProperties($request,$feature);
        return $this->getAjaxResponse();
    }
    
    private function updateProperties($request,$feature){
        
        
        $feature->properties()->detach();
        $prop_data = $this->extractProperties($request);
        //dd($prop_data);
        $item = Item::findOrFail($request->item_id);
        foreach($prop_data as $property_id=>$tag_id){
            $feature->properties()->attach($property_id,[
                'feature_id' => $feature->id,
                'tag_id' => $tag_id,
                'item_id' => $item->id
                    ]);
        }
        
        $feature->save();
    }
    
    
    private function extractProperties($request){
        $perfix = 'property_';
        $out = [];
        foreach($request->all() as $key=>$val){
            if (strpos($key, $perfix) === 0){
                $id = substr($key,strlen($perfix));
                if ($val == null){
                    $val = 0;
                }
                $out[$id] = $val;
            }
        }
        return $out;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($imageId,$featureId)
    {
        $result = $this->getAjaxResponse();
        try {
            $feature = Feature::findOrFail($featureId); 
            if ($imageId != $feature->image_id){
                throw new \Exception('Invalid image id');
            }
            $feature->delete();
        } catch (Exception $exc) {
            $result = $this->getAjaxResponse(1,$exc->getMessage);
        }
        return response()->json($result);
    }
    
    
    
}
