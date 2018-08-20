<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
/*
 * Class for tag or word for descripe image or it part
 * Can be nested.
 * Examble: Hardtail, BMX, 
 * Brake -> disk
 * ...
 * 
 */
class Category extends Model
{
    protected $table = 'categorys';
    protected $guarded = ['id','created_at','updated_at'];
    
}
