<?php

namespace App\Parser;
use Symfony\Component\DomCrawler\Crawler;

use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use GuzzleHttp\Client;

class AvitoList extends Parser{
    
    // resion = rossiya, moskva, sankt-peterburg
    public $base = "https://www.avito.ru";
    public $region = 'rossiya';
    public $type = null;
    public $query = [
        'pmax' => null,  // min price
        'pmin' => null,  // max price
        'p' => null,    // page 
        's' => 104,     // Sort by date
        'user' => 1,     // peoples only, not org
        'q' => null,
    ];
    protected $raw = null;
    protected $crawler = null;
    
    public function __construct($params){
        
        if ($params){
            
            if (isset($params['type'])){
                $this->type = $params['type'];
            }

            if (isset($params['region'])){
               $this->region = $params['region'];
            }

            foreach($this->query as $key=>$val){
                if (isset($params[$key])){
                    $this->query[$key]=$params[$key];
                }
            }
        }
    }
    
    
    public function loadFormString($html){
        $this->crawler = new Crawler($html);
    }
    
    public function parse(){
        $url = $this->buildQuery();
    
        $client = new Client();
        $response = $client->request('GET', $url,['http_errors' => false]);
        $code = $response->getStatusCode();
        $html = $response->getBody()->getContents();
        switch ($code) {
            case 404:
                // All OK but no ads found
                return [];
                
            case 200:
                $this->crawler = new Crawler($html);
                return $this->getAdvBlocks();
                
            default:
                //print substr($html,0,3000);
                print strip_tags($html)."\n";
                throw new \Exception("Error status code ".$code);            
        }
        
    }
    
    public function buildQuery(){
        $parts = [$this->base, $this->region, "velosipedy"];
        if ($this->type){
            $parts[] = $this->type;
        }
        $url = implode('/',$parts); 
        
        $params = [];
        foreach($this->query as $key=>$val){
            if ($val){
               $params[$key] = $val; 
            }
        }
        if (! empty($params)){
            $query = "?".http_build_query($params);
        }else{
            $query = "";
        }
        
        return $url.$query;
    }
    
    public function load(){
        print $this->buildQuery();
    }
    
    public function getAdvBlocks(){
        $advs = $this->crawler->filterXPath("//div[contains(@class,'description item_table-description')]");       
        $results = [];
        for($i = 0; $i< $advs->count(); $i++){
            $adv_crawler = $advs->eq($i);
            $item['url'] = $this->base.$this->extractLink($adv_crawler);
            $item['time'] = $this->extractTime($adv_crawler)->format("Y-m-d H:i:s");
            if ($this->region == 'rossiya'){
                $item['city'] = $this->extractAddress($adv_crawler);
            }else{
                $item['city'] = $this->region;
            }

            $results[] = $item;
        }
        return $results;        
    }
    
    private function extractLink($crawler){
        $link_crawler = $crawler->filterXPath("//a[contains(@class,'item-description-title-link')]");
        return $link_crawler->getNode(0)->getAttribute('href');
    }
    
    private function extractTime($crawler){
        $time_crawler = $crawler->filterXPath("//div[contains(@class,'js-item-date c-2')]");
        $time = trim($time_crawler->getNode(0)->getAttribute('data-absolute-date'));
        $parts = explode(chr(0xC2).chr(0xA0),$time);
        $str = implode(" ",$parts);
        return self::extractTimeFromString($str);  
    }
    
    private function extractAddress($crawler){
        $address_crawler = $crawler->filterXPath("//p");
        $text = '';
        if ($address_crawler->count()){
            $text = trim($address_crawler->text());
        }
        return $text;  
    }
    
    
    public static function extractTimeFromString($str){
        $parts = explode(' ',$str);
         if (is_numeric($parts[0])){

            $month = self::extractMonthName($parts[1]);
            $carbon = Carbon::now();
            $carbon->day = $parts[0];
            if ($month > $carbon->month){
                $carbon->year--;
            }
            $carbon->month = $month;
            $carbon = self::time2carbon($carbon);
           
        }else{ 
            $day_to_int = ['Сегодня'=>0,'Вчера'=>1 ];    
            if ( isset($day_to_int[$parts[0]]) ){
                
                $carbon = Carbon::now();
                $substract = $day_to_int[$parts[0]];
                if ($substract){
                    $carbon->subDays($substract);
                }
                $carbon = self::time2carbon($carbon,$parts[1]);
            }
            else{
                throw new \Exception("Invalid day name ".$parts[0]);
            }
        } 
        return $carbon;
    }
    
    public static function time2carbon($carbon,$timestr = "00:00"){
        $tm = explode(":",$timestr);
        $carbon->hour = $tm[0];
        $carbon->minute = $tm[1];
        $carbon->second = 0;
        return $carbon;
    }
    
    public static function extractMonthName($str){
         $months = [
            1=>'Январ',
            2=>'Феврал',
            3=>'Март',
            4=>'Апрел',
            5=>'Ма', 
            6=>'Июн',
            7=>'Июл', 
            8=>'Август',
            9=>'Сентябр',
            10=>'Октябр',
            11=>'Ноябр', 
            12=>'Декабр' ];
         
         foreach($months as $num=>$m){
             if (strpos(mb_strtolower($str),mb_strtolower($m)) !== false){
                 return $num;
             }
         }
         
        throw new \Exception("Invalid month name ".$str);
    }
    
    
    private function getAdvUrls(){
        try{
            $links = $this->crawler->filterXPath("//a[contains(@class,'item-description-title-link')]");
            $urls = [];
            foreach($links as $link){
                $urls[] = $this->base.$link->getAttribute('href');
            }
            return $urls;
        }
        catch(\InvalidArgumentException $e){
            print $e->getMessage();
            print substr($this->raw,0,2000);
        }
    }
    
        
}
