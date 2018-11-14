<?php

namespace App\Helper;

class Utils {
    //put your code here
    
    public static function mergeHtmlAttr($a,$b){
        if (! is_array($a)){
            throw new Exception("First param must be an array");
        }
        $res = $a;
        if (! empty($b)){
            foreach($b as $key=>$val){
                if (isset($res[$key]) && $key == 'class' ){
                    $res[$key] .= ' '.$val;
                }else{
                    $res[$key] = $val;
                }
            }
        }
        return $res;
    }
    
}
