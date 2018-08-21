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
        
        return view('category.index')->with([
            'categorys' => $tags
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('category.create')->with([
            'categorys' => $this->getTagSelect()
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
        $cat = Tag::create($request->all());
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
        return view('category.edit')->with([
            'cat' => $cat,
            'categorys' => $this->getTagSelect()
        ]);
    }

    public static function getTagSelect(){
        $tags = Tag::whereNull('parent')->get();
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
        $result = ['error'=>0, 'message'=>'OK'];
        try{
            $cat = Tag::findOrFail($id);
            $cat->delete();
        }catch( \Exception $e){
            $result = ['error' =>1, 'message'=>$e->getMessage()];
        }
        return response()->json($result);
    }
}
