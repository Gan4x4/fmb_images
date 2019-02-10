<?php

    return [
        'build_name' => [
            0 => 'New',
            1 => 'Generation',
            2 => 'Completed',
            3 => 'Error',
        ],
        
        'build_type' => [
            1 => 'Darknet',
            2 => 'Classifier',
            3 => 'FMB',
        ],
        
        'crop_from' => [
            App\Dataset\ImageFolderClassifier::CROP_FORM_ORIGINAL => 'Original',
            App\Dataset\ImageFolderClassifier::CROP_FORM_SQUARE => 'Square',
            App\Dataset\ImageFolderClassifier::CROP_FORM_SQUARE_CANVAS => 'Square canvas'
        ]
        
    ];

?>
