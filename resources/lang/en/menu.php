<?php


    return [
        'common'=>[
            route('images.index') => 'Images',
            route('items.index') => 'Items',
            route('properties.index') => 'Properties',
            route('tags.index' ) => 'Tags',
        ],
        'logged' => [
            route('images.index') => 'Images',
        ],
        
        'unlogged' => [
            route('login') => 'Login',
            route('register') => 'Register'
        ], 
        'admin' => [
            
        ]
    ];


?>