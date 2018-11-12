<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Image;
use App\Feature;
use App\Item;
use App\Parser\Avito;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Auth;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $images = Image::orderBy('updated_at',"DESC")->paginate(15);
        
        return view('image.index')->with([
            'images'=>$images,
            'items'=>Item::all()
        ]);
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         return view('image.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $image = new Image();
        $image->user_id = Auth::user()->id;
        
        if ($request->url){
            // TODO select parser
            $parser = new Avito($request->url);
            $tmpImagePath = $parser->getImage(); 
            $image->path = Storage::putFile('public/images', new File($tmpImagePath));
            $image->description = $parser->getDescription();
            $image->source_id = 1;
        }else{
            $request->validate([
                'file' => 'required|image',
            ]);
            $image->path = $request->file->store('public/images');    
            $image->description = $request->description;
        }

        $image->save();
        return redirect()->route('images.edit',$image->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->route('images.edit',$id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $image = Image::findOrFail($id);
        return view('image.edit')->with([
            'image' => $image,
            'items' => Item::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $image = Image::findOrFail($id);
        $image->fill($request->all()); // Update tag id
        $image->status = Image::STATUS_EDITED;
        $image->save();
        return redirect()->route('images.edit',$id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    
    public function deleteFeature($imageId,$featureId)
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
    /*
    public function updateOrCreateFeature(Request $request, $id){
        $image = Image::findOrFail($id);
        if ($request->feature_id){
            $feature = Feature::findOrFail($request->feature_id);    
        }else{
            $feature = new Feature();    
            $feature->image_id = $id;
        }

        $item = Item::findOrFail($request->item_id);
        
        $feature->fill($request->all()); // Update only coords and description
        $feature->save(); // To obtain id
        $feature->properties()->detach();
        $prop_data = $this->extractProperties($request);
        foreach($prop_data as $property_id=>$tag_id){
            $feature->properties()->attach($property_id,[
                'feature_id' => $feature->id,
                'tag_id' => $tag_id,
                'item_id' => $item->id
                    ]);
        }
        
        $feature->save();
        return redirect()->route('images.edit',$id);
    }
    */
    
    
    
    /*
    public function feature($featureId){
        //return "Here";
        //$image = Image::findOrFail($imageId);
        $feature = Feature::findOrFail($featureId);
        $properties = $feature->properties;
        //dd($properties);
        
        return view('image.feature')->with([
                    'feature' => $feature,
                    'item_id' => $feature->getItemId(),
                    'items' => Item::all(),
                    'properties' => $feature->properties,
                    'image' => $feature->image
                ]);
    }
    */
    
}
