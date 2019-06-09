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
            4 => 'Validation',
        ],
        
        'crop_from' => [
            App\Dataset\ImageFolderClassifier::CROP_FORM_ORIGINAL => 'Original',
            App\Dataset\ImageFolderClassifier::CROP_FORM_SQUARE => 'Square',
            App\Dataset\ImageFolderClassifier::CROP_FORM_SQUARE_CANVAS => 'Square canvas'
        ],
        
        'darknet_templates' =>
            [
                'yolov3-tiny_fmb.cfg' => 'YOLOv3 tiny',
                'yolov3_fmb.cfg' => 'YOLOv3 full'
            ]
            
    ];

?>
