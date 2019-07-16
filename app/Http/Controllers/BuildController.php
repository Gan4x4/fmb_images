<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Dataset\Build;
use App\Item;
use App\Jobs\DatasetBuilder;

class BuildController extends Controller
{
    
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $builds = Build::all();
        return view('build.index')->with([
                'builds' => $builds,
                'menu' => $this->getMenu()
                ]); 
    }

    
    private function getMenu(){
        $key = route('builds.index');
        $menu[$key] = 'Result';
        $data = __('common.build_type');
        unset($data[3]);
        foreach($data as $num => $name){
            $link = route('builds.create',['type'=>$num]);
            $menu[$link] = $name;    
        }
        return $menu;
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $view =  $this->getView($request->type);
        return view($view)->with([
                'items' => Item::all(),
                'menu' => $this->getMenu()
            ]); 
    }
    
    
    public function getView($type){
        $views = [
            Build::DARKNET => 'darknet',
            Build::CLASSIFIER => 'classifier',
            Build::VALIDATION => 'validation'
        ];
        
        return 'build.'.$views[$type];
    }
    

    /**
     Old build from ItemController
     */
    public function store(Request $request)
    {
        $dir = 'public/features/'.uniqid("build_");
        //dd($request->all());
        $build = new Build();
        $build->params = $request->all();
        dd($build->params);
        $build->dir = $dir;
        $build->save();
        
        DatasetBuilder::dispatch($build);
       
        return redirect()->route('builds.index');
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
        //
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
        //
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
            $build = Build::findOrFail($id);
            $build->delete();
        }catch( \Exception $e){
            $result = $this->getAjaxResponse(1, $e->getMessage());
        }
        return response()->json($result);
    }
}
