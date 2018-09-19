<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Parser\Avito;

class TestController extends Controller
{

    public function index()
    {
        $parser = new Avito("https://www.avito.ru/moskva/velosipedy/velosiped_stels_navigator_660_md_27_5_v020_2018_1_1544486842");
    }

    
    
}
