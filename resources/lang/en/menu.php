<?php


    return [
        'common'=>[
            route('images.index') => 'Images',
            
        ],
        
        'admin' => [
            route('items.index') => 'Items',
            route('properties.index') => 'Properties',
            route('tags.index' ) => 'Tags',
            route('users.index' ) => 'Users',
            route('builds.index' ) => 'Builds',
        ],
        
        'logged' => [
            route('images.index') => 'Images',
        ],
        
        'unlogged' => [
            route('login') => 'Login',
            route('register') => 'Register'
        ], 
        
    ];


?>