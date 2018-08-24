<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Image;
use App\Feature;


class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $images = Image::all();
        return view('image.index')->with([
            'images'=>$images
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

        $request->validate([
            'file' => 'required|image',
        ]);
        
        $path = $request->file->store('public/images');        
        $image = new Image();
        $image->path = $path;
        $size = getimagesize(storage_path('app/'.$path));
        $image->width = $size[0];
        $image->height = $size[1];
        $image->save();
        return redirect()->route('images.index');
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
        $tags = TagController::getTagSelect();
        return view('image.edit')->with([
            'image' => $image,
            'tags' => $tags,
            'brands' => BrandController::getSelect()
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
        //dump($image);
        if ($request->feature_id){
            $feature = Feature::findOrFail($request->feature_id);    
        }else{
            $feature = new Feature();    
            $feature->image_id = $id;
        }
        //dd("Here");
        $feature->fill($request->all()); // Update tag id
        $feature->region = $this->extractRegion($request);
        
        $feature->save();
        //dump($request->all());
        //dd($feature);
        return redirect()->route('images.edit',$id);
        
    }

    private function extractRegion($request){
        $coords = [];
        $keys = ['x1','y1','x2','y2'];
        $coords[] = [$request->x1,$request->y1];
        $coords[] = [$request->x2,$request->y2];
        return json_encode($coords,true);
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
}
