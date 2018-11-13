<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Interfaces\Owned;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    const ITEMS_PER_PAGE = 16;
    
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    public static function collection2select($collection,$add = []){
        $select = $add;
        foreach($collection as $item){
            $select[$item['id']] = $item->name;
        }
        return $select;
    }
    
    public function getAjaxResponse($errorCode = 0, $message = "OK"){
        return [
            'error' => $errorCode,
            'message' => $message
            ];
    }
    
    protected function attachProperties($request,$item){
        if (! $request->properties ){
            return;
        }
        foreach($request->properties as $property_id ){
            $item->properties()->attach($property_id);
        }
    }
    
    protected function checkAccess(Owned $object){
        $user = Auth::user();
        if ($user->isAdmin()){
            return true;
        }
        
        if ($user->id != $object->user->id){
            abort(403, 'Unauthorized action.');
        }
    }
    
}
