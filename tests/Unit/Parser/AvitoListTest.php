<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Parser\AvitoList;
use Carbon\Carbon;

class AvitoListTest extends TestCase
{
    
    /*
    public function testBuildQuery()
    {
        $list = new AvitoList();
        $simple = $list->buildQuery();
        $this->assertEquals($simple,'https://www.avito.ru/rossiya/velosipedy');
        
        $list->query['p'] = 2;
        $page2 = $list->buildQuery();
        $this->assertEquals($page2,'https://www.avito.ru/rossiya/velosipedy?p=2');
        
    }
    */
    /*
    public function testParseList(){
        $content = file_get_contents(__DIR__.'/data/avito_list.html');
        $list = new AvitoList();
        $list->loadFormString($content);
        $adv = $list->getAdvBlocks();
        dump($adv);
    }
    
    
    public function testextractTimeFromString(){
        $format = 'Y-m-d H:i';
        $in = "Сегодня 18:30";
        $out = AvitoList::extractTimeFromString($in)->format($format);
        $this->assertEquals(date('Y-m-d')." 18:30",$out);
        
        $yesterday = AvitoList::extractTimeFromString("Вчера 12:45")->format($format);
        $carbon = Carbon::now()->subDay()->setTime(12,45);
        $this->assertEquals($carbon->format($format),$yesterday);
     
        $out = AvitoList::extractTimeFromString("23 февраля")->format('Y-m-d');
        $carbon_past = Carbon::create(date('Y'),2,23);
        $this->assertEquals($carbon_past->format('Y-m-d'),$out);
       
    }
    
   
    
    public function testCombine(){
        $price_min = [15000]; 
        $brands = ['gt', 'cube'];
        $params = ['type' => ['gornye','road'],
                   'q' => $brands,
                   'pmin' => $price_min 
                ]; 
        
        $result = AvitoScanner::combine($params);
        
      
        
        dump($result);
    }
     * 
     */
}
