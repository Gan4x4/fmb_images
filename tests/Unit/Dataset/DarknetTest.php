<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Dataset\Darknet;

class DarknetTest extends TestCase
{
    
    public $input = [
            "items" => [0 => "11"],
            "validate" => "0.1",
            "type" => "1",
            "path" => ""
        ];

    /**
     * A basic test example.
     *
     * @return void
     */
    
    
    public function testConstructor()
    {
        $darknet = new Darknet($this->input);
        
        $this->assertTrue(count($darknet->classes) == 1);
    }
    /*
    public function testConstructorWithTags()
    {
        $input = $this->input;
        $input["11_propertys"] = [ 0 => "2" ];
        $input["11_2_tags"] = [ 0 => "36", 1 => "45", ];
        $darknet = new Darknet($input);
        $this->assertTrue(true);
    }
    
    public function testBuild()
    {
        print "Build";
        $darknet = new Darknet($this->input);
        $images = $darknet->findImages();
        dump($images);
        $darknet->build('/tmp/1');
        $this->assertTrue(! empty($images));
    }
    */
}
