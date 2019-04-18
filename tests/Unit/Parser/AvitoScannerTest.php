<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Parser\AvitoScanner;


class AvitoScannerTest extends TestCase
{
    
    public function testCombine(){
        $price_min = [15000]; 
        $brands = ['gt', 'cube'];
        $params = ['type' => ['gornye','road'],
                   'q' => $brands,
                   'pmin' => $price_min 
                ]; 
        
        $result = AvitoScanner::combine($params);
        //dump($result);
        $this->assertEquals(count($result),4);
    }
}
