<?php

namespace App\Helper;
use Intervention\Image\ImageManagerStatic;

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
    
    
    
    public static function isImageWhite($im_path){
        $img = ImageManagerStatic::make($im_path);

        // pick a color at position as array
        $lt = $img->pickColor(0, 0);
        $lb = $img->pickColor(0, $img->height() - 1);
        $rt = $img->pickColor($img->width() - 1, 0);
        $rb = $img->pickColor($img->width() - 1, $img->height() - 1);
        return self::isPixelWhite($lt) && self::isPixelWhite($lb) && self::isPixelWhite($rt) && self::isPixelWhite($rb);
    }
    
    public static function isPixelWhite($rgb_array){
        $th = 250;
        return $rgb_array[0] > $th && $rgb_array[1] > $th && $rgb_array[2] > $th;
    }
    
    // Save Image from URL to local temp dir
    public static function saveImage($url){
        
        if (strpos($url, "http:") === false){
            $imgUrl = "http:";
        }else{
            $imgUrl = $url;    
        }
        $tempImage = tempnam(sys_get_temp_dir(),"ext_img");
        copy($imgUrl, $tempImage);
        return $tempImage;
    }
    
}
