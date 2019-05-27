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
use App\Parser\Source;
use App\Http\Controllers\FeatureController;
use Illuminate\Support\Facades\DB;
use App\Dataset\Dataset;
use App\Helper\Utils;
use GuzzleHttp\Client;
use App\Tag;


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
        $images = Image::orderBy('updated_at', "DESC");//orderBy('status', "ASC")
        $ids = $this->filter($request);
        if (! empty($ids)){
            $images->whereIn('id',$ids);
        }
                    
        if ($request->new){
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
        
        return view('image.index')->with([
            'images' => $images->paginate(self::ITEMS_PER_PAGE)->appends($request->all()),
            'items' => Item::all(),
            'tabs' => $this->getTabs($request->all()),
            'active_tab' => $active_tab,
            'count' => $user->getStat(),
            'tree' => Dataset::tree2array($request->all()),
            'enable_filter' => $request->filter
        ]);
    }
    
    private function filter($request){
        $selected = Dataset::tree2array($request->all());
        if (! $selected){
            return null;
        }
 
        $featureIds = [];
        foreach($selected as $item_id=>$props){
            $un_props = [];
            foreach($props as $prop_id=>$tags){
                $query =  DB::table('bindings')->distinct('feature_id');
                $query->where('item_id',$item_id);
                if (! empty($tags)){
                    $query->where('property_id',$prop_id);
                    $query->whereIn('tag_id',$tags);   
                    $featureIds = array_merge($featureIds,$query->pluck('feature_id')->toArray());
                }
                
                if (in_array(0,$tags)){
                    $un_props[] = $prop_id;
                }
                
                
            }
            
            if (! empty($un_props)){
                $item = Item::find($item_id);
                $features = $item->features;
                foreach($features as $feature){
                    $undefined = $feature->getUndefinedProperties();
                    foreach($undefined as $p){
                        if (in_array($p->id,$un_props)){
                            $featureIds[] = $feature->id;
                        }
                    }
                }
            }
            
        }
        
        $featureIds = array_unique($featureIds);
        $ImageIds =  DB::table('features')
                ->distinct('image_id')
                ->whereIn('id',$featureIds)
                ->pluck('image_id')->toArray();
        return $ImageIds;
        
    }
    
    /*
    private function tree2array($params){
        if (! isset($params['items'])){
            return null;
        }
        foreach($params['items'] as $item_id){
            $tmp[$item_id] = [];
            $propKey = $item_id.'_propertys';
            if (isset($params[$propKey]) ){
                foreach($params[$propKey] as $prop_id){
                    $tmp[$item_id][$prop_id] = [];
                    $tagKey =  $item_id.'_'.$prop_id.'_tags';
                    if (isset($params[$tagKey])){
                        foreach($params[$tagKey] as $tag_id){
                            $tmp[$item_id][$prop_id][] = $tag_id;
                        }
                    }
                }
            }
        }
        return $tmp;
    }
    */
    
    
    private function getTabs($params = []){
        $user = Auth::user();
        $images = Image::orderBy('status', "ASC")
            ->orderBy('updated_at', "DESC");
        
        if ($user->isAdmin()){
            $labeled_count = $images->whereNotNull('user_id')->count();;
        }else{
            $labeled_count = $images->where('user_id',$user->id)->count();
        }
        
        $images = Image::orderBy('status', "ASC")
            ->orderBy('updated_at', "DESC");
        
        $new_count = $images->whereNull('user_id')->count();
        $params1 = $params2 = $params;
        unset($params1['new']);
        $params2['new'] = true;
        
        $tabs = [
            route('images.index',$params1) => "Labeled <span class='badge badge-light'>$labeled_count</span>",
            route('images.index',$params2) => "New <span class='badge badge-light'>$new_count</span>",
        ];
        return $tabs;
        
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
            
            $result = $this->saveAllImages($request->url,$request->image_nums);
            if (is_object($result)){
                // Images found
                $image = $result;
            }else{
                // Result is id of duplicate image
                 return redirect()->route('images.exists',[$result]);
            }
            
        }else{
            $request->validate([
                'file' => 'required|image',
            ]);
            $image->path = $request->file->store(env('IMAGES_DIR'));    
            $image->description = $request->description;
        }
        //dd($image);
        $image->save();
        return redirect()->route('images.edit',$image->id);
    }
    
    private function saveAllImages($url,$selected){
        $parser = Avito::createByUrl($url);
        $tmpImagePaths = $parser->getAllImages($selected);
        $duplicates = [];
        $images = [];

        if (count($tmpImagePaths) > 0){
            $source = Source::create(['link'=>$url,'type' => Source::TYPE_AVITO]);
            
            foreach($tmpImagePaths as $newImagePath){
                $duplicate = Image::findByHash($newImagePath);
                if ($duplicate){
                    $duplicates[] = $duplicate->id;
                }else{
                    $image = new Image();
                    $image->path = Storage::putFile(env('IMAGES_DIR'), new File($newImagePath));
                    $image->description = $parser->getDescription();
                    $image->source_id = $source->id;
                    $image->user_id = null;
                    $image->save();
                    $images[] = $image;
                }
            }
        }

        if (empty($images) && empty($duplicates)){
            throw new \Exception("No images saved");
        }elseif(empty($images)){
            return $duplicates[0];
        }else{
            return $images[0];
        }
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
        
        //$model = Property::getModel();
        $features = $image->features->sort(function($a,$b ){
            return strcmp($a->getName(),$b->getName());
        });
        
        return view('image.edit')->with([
            'image' => $image,
            'items' => Item::orderBy('name')->get(),
            'features' => FeatureController::getSortedFeatures($image)
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
        $this->delete($id);
        return redirect()->route('images.index');
    }
    
    public function destroyAjax($id)
    {
        $result = $this->getAjaxResponse();
        try{
            $this->delete($id);
        }catch( \Exception $e){
            $result = $this->getAjaxResponse(1, $e->getMessage());
        }
        return response()->json($result);
    }
    
    private function delete($id){
        $image = Image::findOrFail($id);
        if (! $this->owned($image) ){
            abort(403, 'Unauthorized action.');
        }
        $image->delete();
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
        
        foreach($image->getSiblings() as $s){
            if (! $s->user_id){
                $s->user_id = $user->id;
                $s->save();
            }
        }
        
        $image->save();
        return redirect()->route('images.edit',$image->id);
    }
    
    
    public function suspicious(){
        $query = DB::table('images')
            ->distinct()
            ->join('features',function($join) {
                $join->on('images.id', '=', 'features.image_id');
                //->on('images.width','=','features.x2' )
                //->on('images.height','=','features.y2' );
            })
            ->where('features.x1','=',0)
            ->where('features.y1','=',0)
            ->select('features.*');
        
        $data = $query->get();
        $features = Feature::hydrate($data->toArray());
        return view('image.suspicious')->with([
            'features' => $features
        ]);
    }
    
    public function editFirstNewImage(){
        $image = Image::whereNull('user_id')->orderBy('updated_at', "DESC")->first();
        if (! $image){
            return redirect()->route('images.index',['new'=>1]);
        }
        
        return redirect()->route('images.edit',$image->id);
    }
    
    public function importFromFmb($bundle){
        $url = $bundle->url;
        $newImagePath = Utils::saveImage($url);
        
        $duplicate = Image::findByHash($newImagePath);
        if ($duplicate){
            //return $duplicate;
            return route('images.exists',[$duplicate->id]);
        }

        $image = new Image();

        $image->path = Storage::putFile(env('IMAGES_DIR'), new File($newImagePath));
        unlink($newImagePath);
        $image->user_id = null;
       // $image->source_id = $source->id;
        $image->description = "Complants: ".$bundle->complaints."--------------\n".$bundle->title."\n".$bundle->description;
        $image->save();
        
        
        $regions = json_decode($bundle->regions);
        $this->addFeatures($image,$regions);
        
        return $image;
        //return redirect()->route('images.show',[$image->id]);
    }
    
    private function addFeatures($image,$regions){
        foreach($regions as $region){
            $item = Item::whereRaw('lower(name) = (?)',["{$region->item_name}"])->first();
            if ($item){
                $coords = $region->coords;
                $feature = Feature::create([
                    'image_id'=>$image->id,
                    'x1' => $coords[0],
                    'y1' => $coords[1],
                    'x2' => $coords[2],
                    'y2' => $coords[3],
                    'description' => 'Created by NN'
                    ]);
                
                $props = $region->properties;
                //dump($props);
                foreach($item->properties as $property){
                    $key = strtr(mb_strtolower($item->name).'/'.mb_strtolower($property->name),' ','_');
                    dump($key);
                    $tag_id = 0;
                    
                    if ( is_object($props) && property_exists($props, $key)){
                        
                        $name = strtr(mb_strtolower($props->$key),'_',' ');
                        dump($name);
                        $query = Tag::whereRaw('LOWER(name) = (?)',["{$name}"]);
                        dump($query->toSql());
                        $tag = $query->first();
                        
                        if ($tag){
                            $tag_id = $tag->id;
                        }
                    }
                    
                    $feature->properties()->attach($property->id,[
                        'feature_id' => $feature->id,
                        'tag_id' => $tag_id,
                        'item_id' => $item->id
                        ]);    
                }
            }
        }
       
    }
    
    public function loadComplaints(){
        //dd("Here");
        
        //$url = 'http://fmb.gan4x4.ru/api/images/2199/export';
        $url = 'http://fmb.gan4x4.ru/api/images/complaints';
        $list = $this->readUrl($url);
//        dump($json_obj);
        if ($list->error != 0){
            dd($list->message);
        }
        
        $out = [];
                
        foreach($list->images as $image_url){
            $source = Source::where('link',$image_url)->first();
            $line = [
                'url' =>$image_url
            ];
            if ($source){
                // already loaded
                $img = $source->images()->first();
                if ($img){
                    $line['image'] = route('images.edit',$img->id);
                    $line['info'] = "Image already imported";
                }
                else{
                    $line['image'] = '--';
                    $line['info'] = "Source already exists but image not found";
                }
            }
            else{
                $source = Source::create(['link'=>$image_url,'type' => Source::TYPE_FMB]);
                $bundle = $this->readUrl($image_url);
                $img = $this->importFromFmb($bundle);
                if (is_object($img)){
                    $img->source_id = $source->id;
                    $img->save();
                    $line['image'] = route('images.edit',$img->id);    
                    $line['info'] = "Created new image";
                }else{
                    // Duplicate
                    $line['image'] = $img;    
                    $line['info'] = "Image already in db";
                }
                
                
            }
            
            $out[] = $line;
            //print $image_url."<br>";
        }
        //dd("HH");
        
        //$wheel = 'wheel';
        //dump(Item::whereRaw('lower(name) = (?)',["{$wheel}"])->toSql());
        //dd(Item::whereRaw('lower(name) = (?)',["{$wheel}"])->first());
        
        
        //dd(json_decode($json_obj->regions));
        return view('image.import_complaints')->with([
            'lines'=>$out
        ]);
    }
    
    private function readUrl($url){
        $client = new Client();
        $response = $client->request('GET', $url);
        if ( $response->getStatusCode() != 200){
            throw new \Exception("Invalid request code ". $response->getStatusCode()." ".$response->getBody()->getContents());
        }
        $data = $response->getBody()->getContents();
        return json_decode($data);
    }
    
}
