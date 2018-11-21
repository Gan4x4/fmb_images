<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Parser\Avito;

class TestController extends Controller
{

    public function index()
    {
        $parser = Avito::createByUrl("https://www.avito.ru/moskva/velosipedy/trek_remedy_27.5_2015_1410772984");
        dump($parser->getAllImages());
    }

    
    
}
