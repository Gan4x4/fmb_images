<?php

namespace App\Parser;

use GuzzleHttp\Client;

class Parser {
    //put your code here
    
    public static function loadHtml($url){
        $client = new Client();
        $response = $client->request('GET', $url);
        if ( $response->getStatusCode() != 200){
            throw new \Exception("Invalid request code ". $response->getStatusCode()." ".$response->getBody()->getContents());
        }
        return  $response->getBody()->getContents();
    }
    
}
