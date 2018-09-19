<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Property;
use App\Tag;
use Illuminate\Support\Facades\DB;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $properties = Property::all();
        return view('property.index')->with([
            'properties' => $properties
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('property.create')->with([
            'tags' => $this->getAllTags(),
            'selected_tags' => []
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
        $property = Property::create($request->all());
        $this->attachTags($request,$property);
        $this->createNewTag($property,$request->new_tag);
        return redirect()->route('properties.index'); 
    }

     protected function attachTags($request,$property){
        if (! $request->tags ){
            return;
        }
        $property->tags()->detach();
        foreach($request->tags as $tag_id ){
            $property->tags()->attach($tag_id);
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
    public function edit(Property $property)
    {
        $selected = [];
        foreach($property->tags as $t){
            $selected[] = $t->id;
        }
        return view('property.edit')->with([
            'property' => $property,
            'tags' => $this->getAllTags(),
            'selected_tags' => $selected
        ]);
    }

    private function getAllTags(){
        return Tag::select("*")->orderBy('name')->get();
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Property $property)
    {
        $property->fill($request->all());
        $property->save();
        $this->attachTags($request,$property);
        $this->createNewTag($property,$request->new_tag);
        return redirect()->route('properties.index'); 
    }

    private function createNewTag($property,$tag_name){
        if (! empty($tag_name)){
            $newTag = Tag::where(DB::raw("LOWER(name) LIKE '".mb_strtolower($tag_name)."'"))->first();
            if (! $newTag){
                $newTag = new Tag();
                $newTag->name = $tag_name;
                $newTag->save();
            }
            $property->tags()->attach($newTag->id);
        }
        
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
            $property = Property::findOrFail($id);
            $property->delete();
        }catch( \Exception $e){
            $result = $this->getAjaxResponse(1, $e->getMessage());
        }
        return response()->json($result);
    }
    
}
