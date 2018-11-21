<?php

namespace App\Parser;

use GuzzleHttp\Client;

class Parser {
    //put your code here
    
    public static function loadHtml($url){
        $client = new Client();
        $response = $client->request('GET', $url);
        return  $response->getBody()->getContents();
    }
    
}
