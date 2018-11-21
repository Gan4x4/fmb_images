<?php

namespace App\Parser;
use Symfony\Component\DomCrawler\Crawler;

use Illuminate\Support\Facades\Storage;

class Avito extends Parser{
    protected $raw = null;
    protected $cache = 'tmp_html';
    protected $crawler = null;
    
    public function __construct($html) {
       // if (! Storage::exists($this->cache)){
        /*
            $client = new Client();
            $response = $client->request('GET', $url);
            $this->raw = $response->getBody()->getContents();
         * 
         */
            //Storage::put($this->cache,$html);
        //}
        //$this->raw = Storage::get($this->cache);
        
        //print substr($this->raw,0,2000);
        //print $url;
        //dd($this->raw);
        $this->raw = $html;
        $this->crawler = new Crawler($this->raw);
          /*
        $nodeValues = $images->each(function (Crawler $node, $i) {
            return $node->attr('data-url');
        });
        
        dump($nodeValues);
        
      
        foreach ($crawler as $domElement) {
            var_dump($domElement->nodeName);
            print $domElement->attr('data-url');
            }
        
        */
        
        //print $this->getDescription();
    }
    
    public static function createByUrl($url){
        $html = self::loadHtml($url);
        $instance = new static($html);
        return $instance;
    }
    
    private function getImageUrls($pattern = "640x480"){
        try{
            $images = $this->crawler->filterXPath("//div[contains(@data-url,'$pattern')]");
            return $images;
        }
        catch(\InvalidArgumentException $e){
            print $e->getMessage();
            print substr($this->raw,0,2000);
        }
    }

    public function getAllImages(){
        $list = [];
        $images = $this->getImageUrls();
        foreach($images as $image){
            $url = $image->getAttribute('data-url');
            $list[] = $this->saveImage($url);
            //dump($image);
        }
        //dd($images);
        return $list;
    }

    /*
     * Download first image and return it'stmp file path
     */    
    public function getImage(){
            $images = $this->getImageUrls();
            //$imgUrl = "http:".$images->attr('data-url');
            return $this->saveImage($images->attr('data-url'));
            //$tempImage = tempnam(sys_get_temp_dir(),"avito_img");
            //copy($imgUrl, $tempImage);
            //return $tempImage;
        
    }
    
    private function saveImage($url){
        $imgUrl = "http:".$url;
        $tempImage = tempnam(sys_get_temp_dir(),"avito_img");
        copy($imgUrl, $tempImage);
        return $tempImage;
    }
    
    public function getDescription(){
        $title = $this->crawler->filterXPath('//span[contains(@class, "title-info-title-text")]');
        $body = $this->crawler->filterXPath('//div[contains(@class, "item-description-text")]');
        $price = $this->getPrice();
        return $title->text()."\n".$body->text()."\n Цена: ".$price;
    }
    
    
    public function getPrice(){
        $price = $this->crawler->filterXPath('//span[contains(@class, "js-item-price")]');
        return $price->text();
    }

    
        
}
