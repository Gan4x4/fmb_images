<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Parser;


use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Carbon\Carbon;
use App\Helper\Utils;
//use \Monolog\Formatter\HtmlFormatter;


class AvitoScanner {
    
    // TODO move to common used library
    const ERROR_ALREADY_EXISTS = 50;
    const ERROR_NO_IMAGES = 1;
    
    protected $logger;
    protected $last_date = null;
    protected $params = [];
    public function __construct(){
        
        $this->logger = new Logger('avito-scanner');
        $path = storage_path('logs/avito-scanner.log');
        //dump($path);
        $this->logger->pushHandler(new StreamHandler($path, Logger::DEBUG));
        //$logger->info('This is a log! ^_^ ');
        //$logger->warning('This is a log warning! ^_^ ');
        //$logger->error('This is a log error! ^_^ ');
        
        $price_min = 15000; 
        $brands = [
            'gt',
            'merida',
            'cube',
            'trek',
            'norco',
            'orbea',
            'welt',
            //'stels'
            ];
        
        $this->params = [
                   'region' => 'moskva',
                   'type' => ['gornye','dorozhnye','bmx'],
                   'q' => $brands,
                   'pmin' => $price_min 
                ]; 
        
    }
            
    
    public static function combine($array){
        $combinations = [];
        foreach($array as $key=>$values){
            $new_comb = [] ;
            if (! is_array($values)){
                $values = [$values];
            }
            foreach($values as $val){
                if (! empty($combinations)){
                    foreach($combinations as $comb){
                        $comb[$key] = $val;
                        $new_comb[] = $comb;
                    }
                }
                else{
                    $new_comb[] = [$key => $val];
                }
            }
            $combinations = $new_comb; 
        }
        return $combinations;
    }
  
    
    protected function log($message){
        $this->logger->info($message);
        print $message."\n";
    }
    
    public function __invoke(){
        $t1 = time();
        try{
            
            $combinations = self::combine($this->params);
            $this->log("Start avito scan by ".count($combinations)." requests");
            foreach($combinations as $comb){
               $list = new AvitoList($comb);
               $query = $list->buildQuery();
               $this->log("Query :".$query);
               $advs = $list->parse();
               if (count($advs) == 0){
                   $this->log("No Ads found");
               }else{
                    $new_adv = $this->filterNew($advs);
                    $this->log("Found ".count($new_adv)." new ads");
                    if (count($new_adv) > 0){
                        $this->send2site($new_adv);
                    }
               }
               sleep(5);
           }
        }
        catch(\Exception $e){
           $this->log("Error ".$e->getMessage());
        }
        $time = (time() - $t1);
        $min = floor($time/60);
        $sec = $time%60;
        
        $this->log("Scan finished. total time $min m. $sec s.");
    }
    
    public function filterNew($advs){
        $map = $this->extractAdvIds($advs);
        //dump($map);
        //dump(array_keys($map));
        $id_of_new_adv = $this->findNewAdvs(array_keys($map));
        $out = [];
        foreach($id_of_new_adv as $id){
            $out[] = $map[$id];
        }
        return $out;
    }
    
    public function extractAdvIds($advs){
        $map = [];
        foreach($advs as $adv){
            $parts = explode('_',$adv['url']);
            $id = end($parts);
            $map[$id] = $adv; 
        }
        return $map;
    }
    
    
    public function getLastAdvList(){
        $list = new AvitoList();
        $list->region = 'moskva';
        $list->type = 'gornye';
        $list->query['q']='merida';
        $advs = $list->parse();
        return $advs;
    }
    
    
    public function getLastAdvDate(){
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', 'http://fmb.gan4x4.ru/api/sources/last_date');
        $res = json_decode(($response->getBody()->getContents()));
        //dump($res);
        return $res->time;
    }
    
    public function findNewAdvs($ids){
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', 'http://fmb.gan4x4.ru/api/sources/exists',[
            'form_params' => [
                'ids' => $ids
                ]
        ]);
        $res = json_decode(($response->getBody()->getContents()));
        //dump($res);
        return $res->new;
    }
    
    public function isNew($adv){
        /*
         * 
         * temp
         * 
         */
        return true;
        if (! $this->last_date){
            return true;
        }
            
        $carbon_a = Carbon::createFromFormat('Y-m-d H:i:s', $adv['time']);
        $carbon_l = Carbon::createFromFormat('Y-m-d H:i:s', $this->last_date);
        dump($this->last_date);
        dump($carbon_l->format('Y-m-d H:i:s'));
        return $carbon_a->gt($carbon_l);
    }
    
    public function send2site($advs){
        //$advs = array_reverse($this->getLastAdvList());
        //$this->log('Scan performed, found '.count($advs)." advs");
        //$this->last_date = $this->getLastAdvDate();
        //print ($this->last_date);
        //$this->logger->info('Last adv added at '.$this->last_date);
        //print "Found ".count($advs)." adv \n";
        $i=1;
        foreach($advs as $adv){
            $this->log("Adv from ". $adv['url']);
            $this->sendAdvToFmb($adv);
            $i++;
            sleep(rand(2,5));
        }
       
        $this->log("Imported $i adv");
    }
    
    
    public function sendAdvToFmb($adv){
        
        $parser = Avito::createByUrl($adv['url']);
        $allImgPaths = $parser->getAllImages(null,0.5);
        $tmpImagePaths = $this->excludeWhite($allImgPaths);
        $count = count($tmpImagePaths);
        $this->log("Get $count images");            
        if (count($tmpImagePaths) == 0){
            $this->log("Images not found");
            return;
        }
        
        $multipart = [];
        $i =1;
        foreach ($tmpImagePaths as $img){
            $multipart[] = [
                    'name'     => 'image[]',
                    'contents' => file_get_contents($img),
                    'filename' => pathinfo($img,PATHINFO_FILENAME)
                ];
            $i++;
        }
        
        $adv['description'] = $parser->getShortDescription();
        $adv['title'] = $parser->getTitle();
        $adv['provider_id'] = Avito::CODE;
        
        
        foreach ($adv as $key =>$val){
            $multipart[] = [
                    'name'     => $key,
                    'contents' => $val,
                ];
        }
        foreach ($tmpImagePaths as $img){
            unlink($img);
        }
        
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', 'http://fmb.gan4x4.ru/api/sources/import', [
                'multipart' => $multipart,
            ]);

        $result =json_decode(($response->getBody()->getContents()));
        
        
        switch ($result->error) {
            case 0:
                // All OK
                break;
            
            case self::ERROR_ALREADY_EXISTS:
                // All OK
                break;

            default:
                $this->log("Images not loaded");
                throw new \Exception($result->message);
            
        }
        
        $this->log($result->message);
        
        /*
        
        
         if ($result->error != 0 ){
                $this->log("Images not loaded");
                throw new \Exception($result->message);
            }
            else{
                $this->log($result->message);
                //$this->log("Loaded $count images");
            }
         * 
         */
        return $result; 
         
    }
    
    private function excludeWhite($paths){
        $result = [];
        foreach($paths as $path){
            if (! Utils::isImageWhite($path)){
               $result[] = $path; 
            }
        }
        
        $diff = count($paths) - count($result);
        if ($diff){
            $this->log("Exclude $diff white images");
        }
        
        return $result;
    }
    
}
