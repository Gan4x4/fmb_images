<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tag;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tags = Tag::all();
        $tags->sort(function($a,$b){
            
            if ($a->parent == $b->parent){
                return strcasecmp($a->name,$b->name);
            }
            else{
                return $a->parent - $b->parent;
            }
        });
        
        return view('tag.index')->with([
            'tags' => $tags
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('tag.create')->with([
            'groups' => GroupController::getGroupSelect()
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
        $tag = Tag::create($request->all());
        foreach($request->groups as $group_id){
            $tag->groups()->attach($group_id);
        }
        return redirect()->route('tags.index'); 
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
        $tag = Tag::findOrFail($id);
        return view('tag.edit')->with([
            'tag' => $tag,
            'tags' => $this->getTagSelect()
        ]);
    }

    public static function getTagSelect($exceptId = []){
        //$tags = Tag::whereNull('parent')->get();
        $tags = Tag::all();
        $filtered = $tags->filter(function ($value, $key) use ($exceptId) {
            return ! in_array($value->id,$exceptId);
        });
        
        return self::collection2select($tags,[null=>"No"]);
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
        $cat = Tag::findOrFail($id);
        $cat->fill($request->all());
        $cat->save();
        return redirect()->route('tags.index'); 
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
            $tag = Tag::findOrFail($id);
            $tag->delete();
        }catch( \Exception $e){
            $result = $this->getAjaxResponse(1, $e->getMessage());
        }
        return response()->json($result);
    }
}
