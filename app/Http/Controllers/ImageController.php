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
    public function index(Request $request)
    {
        $user = Auth::user();
        $images = Image::orderBy('status', "ASC")
            ->orderBy('updated_at', "DESC");
        
        if ($request->has('new')){
            $images->whereNull('user_id');
            $active_tab = 1;
        }
        else{
            $active_tab = 0;
            if ($user->isAdmin()){
                $images->whereNotNull('user_id');
            }else{
                $images->where('user_id',$user->id);
            }    
        }
        
        $tabs = [
            route('images.index') => 'Labeled',
            route('images.index',['new' => true]) => 'New',
        ];
        
        return view('image.index')->with([
            'images'=>$images->paginate(self::ITEMS_PER_PAGE),
            'items'=>Item::all(),
            'tabs' => $tabs,
            'active_tab' => $active_tab,
            'count' => $user->getStat()
            
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
        //$image->user_id = 0; // New images don't has owner
        //Auth::user()->id;
        
        if ($request->url){
            // TODO select parser
            $parser = new Avito($request->url);
            $tmpImagePath = $parser->getImage(); 
            
            $duplicate = Image::findByHash($tmpImagePath);
            if ($duplicate){
                return redirect()->route('images.exists',[$duplicate->id]);
            }
                
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
    
    public function alreadyExists($id){
        $image = Image::find($id);
        return view('image.exists')->with([
            'image' => $image,
            'link' => $this->hasAccess($image),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $image = Image::find($id);
        
        return view('image.show')->with([
            'image' => $image,
            'link' => $this->hasAccess($image)
        ]);
        //return redirect()->route('images.edit',$id);
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
        $this->checkAccess($image);
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
        $this->checkAccess($image);
        $image->fill($request->all()); // Update tag id
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
        if (! Auth::user()->isAdmin()){
            abort(403, 'Unauthorized action.');
        }
        $image = Image::findOrFail($id);
        $image->delete();
        return redirect()->route('images.index');
    }
    
    public function deleteFeature($imageId,$featureId)
    {
        $image = Image::findOrFail($id);
        $this->checkAccess($image);
        
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
   
    
    public function take($id){
        $image = Image::findOrFail($id);
        $user = Auth::user();
        
        $image->user_id = $user->id;
        $image->save();
        
         
        return redirect()->route('images.edit',$image->id);
    }
    
}
