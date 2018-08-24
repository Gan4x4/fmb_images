<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
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
    
}
