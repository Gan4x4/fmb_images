<?php

namespace App\Parser;
use Symfony\Component\DomCrawler\Crawler;

use Illuminate\Support\Facades\Storage;
use App\Helper\Utils;

class Avito extends Parser{
    const CODE = 2;
    protected $raw = null;
    protected $cache = 'tmp_html';
    protected $crawler = null;
    public $image_size = '1280x960'; // '640x480'
    
    public function __construct($html) {
        $this->raw = $html;
        $this->crawler = new Crawler($this->raw);
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

    public function getAllImages($selected = null,$pause = 0.1){
        $list = [];
        $images = $this->getImageUrls($this->image_size);
        $i = 0;
        foreach($images as $image){
            $i++;
            if ($selected && count($selected) > 0 && (! in_array($i,$selected))){
                continue;
            }
            $url = $image->getAttribute('data-url');
            $list[] = Utils::saveImage($url);
            sleep($pause);
        }
        return $list;
    }

    /*
     * Download first image and return it'stmp file path
     */    
    public function getImage(){
        $images = $this->getImageUrls($this->image_size);
        return Utils::saveImage($images->attr('data-url'));
    }

    public function getDescription(){
        //$title = $this->crawler->filterXPath('//span[contains(@class, "title-info-title-text")]');
        //$body = $this->crawler->filterXPath('//div[contains(@class, "item-description-text")]');
        $price = $this->getPrice();
        return $this->getTitle()."\n".$this->getShortDescription()."\n Цена: ".$price;
    }
    
    public function getShortDescription(){
        $body = $this->crawler->filterXPath('//div[contains(@class, "item-description-text") or contains(@class, "item-description-html")]');
        if (! $body->count()){
            return null;
        }
        return $body->text();
    }
    
    public function getTitle(){
        $title = $this->crawler->filterXPath('//span[contains(@class, "title-info-title-text")]');
        return $title->text();
    }
    
    
    public function getPrice(){
        $price = $this->crawler->filterXPath('//span[contains(@class, "js-item-price")]');
        return $price->text();
    }

    
        
}
