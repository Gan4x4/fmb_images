<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class User extends Authenticatable
{
    const ADMIN = 1;
    
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    
    public function images(){
        return $this->hasMany('App\Image');
    }
    
    public function features(){
        return $this->hasManyThrough('App\Feature', 'App\Image');
    }
    
    public function tags(){
        return $this->hasManyThrough('App\Tag', 'App\Image');
    }
    
    public function isAdmin(){
        return in_array($this->role,[self::ADMIN]);
    }
    
    public function getStat(){
        
        $props = DB::table('images')
            ->where('images.user_id',$this->id)
            ->join('features', 'features.image_id', '=', 'images.id')
            ->join('bindings', 'bindings.feature_id', '=', 'features.id')
            ->select('bindings.tag_id')
            ->where('bindings.tag_id','<>',0);

        
        $now = Carbon::now();
        $yesterday = $now->subDay();
        
        return [
            'all' =>[
                'images' => $this->images()->count(),
                'features' => $this->features()->count(),
                'properties' => $props->count()
            ],
            'today' => [
                'images' => $this->images()->whereDate('updated_at',Date('Y-m-d'))->count(),
                'features' => $this->features()->whereDate('images.updated_at',Date('Y-m-d'))->count(),
                'properties' => $props->whereDate('images.updated_at',Date('Y-m-d'))->count()
            ],
            
            'yesterday' => [
                'images' => $this->images()->whereDate('updated_at',$yesterday->format('Y-m-d'))->count(),
                'features' => $this->features()->whereDate('images.updated_at',$yesterday->format('Y-m-d'))->count(),
                'properties' => $props->whereDate('images.updated_at',$yesterday->format('Y-m-d'))->count()
            ]
             
        ];
        
    }
    
}
