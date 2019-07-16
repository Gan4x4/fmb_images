<?php

namespace Tests\Unit;

use Tests\TestCase;

use App\Dataset\ImageFolderClassifier;

class ImageFolderClassifierTest extends TestCase
{
    
    public $input = [
            "items" => [ 0 => 7 ],
            "7_propertys" => [ 0 => 10],
            "7_10_tags" => [
                0 => 58,
                1 => 59
            ],
        
            "min_width" => 0,
            "max_width" => 0,
            "min_prop" => null,
            "validate" => 0.2,
            "crop_form" => 0,
            "type" => 2,
            "type" => "1",
            "path" => "tmp"
        ];

    /**
     * A basic test example.
     *
     * @return void
     */
    
     
    public function testConstructor()
    {
        $if = new ImageFolderClassifier($this->input);
        $if->build("tmp");
        
     //   $this->assertTrue(count($darknet->classes) == 1);
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
