<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Helper\Utils;

class UtilsTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testWhiteCorners()
    {
        $path = __DIR__.'/images/white.jpeg';
        $white = Utils::isImageWhite($path);
        $this->assertTrue($white);
        
        $path = __DIR__.'/images/not_white.jpg';
        $not_white = Utils::isImageWhite($path);
        $this->assertFalse($not_white);
    }
}
