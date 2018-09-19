<?php

namespace App\Parser;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;

class Avito extends Parser{
    protected $raw = null;
    protected $cache = 'tmp_html';
    protected $crawler = null;
    
    public function __construct($url) {
       // if (! Storage::exists($this->cache)){
            $client = new Client();
            $response = $client->request('GET', $url);
            $this->raw = $response->getBody()->getContents();
            //Storage::put($this->cache,$html);
        //}
        //$this->raw = Storage::get($this->cache);
        
        //print substr($this->raw,0,2000);
        //print $url;
        //dd($this->raw);
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
    
    public function getImage(){
        $images = $this->crawler->filterXPath('//div[@data-url]');
        $imgUrl = "http:".$images->attr('data-url');
        $tempImage = tempnam(sys_get_temp_dir(),"avito_img");
        copy($imgUrl, $tempImage);
        return $tempImage;
    }
    
    
    public function getDescription(){
        
        $title = $this->crawler->filterXPath('//span[contains(@class, "title-info-title-text")]');
        $body = $this->crawler->filterXPath('//div[contains(@class, "item-description-text")]');
        
        
        return $title->text()."\n".$body->text();
        
        
    }
    
        
}
