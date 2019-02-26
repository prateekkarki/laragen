<?php

return [
    'options' => [
        'items_to_generate' => [
            'Common' => [
                'Migration',
                'Model',
                'Seeder',
                'Route'
            ],
            'Frontend' => [
                'Controller',
                'View'
            ],
            'Backend' => [
                'Controller',
                'Request'
            ]
        ],
        'skip_generators' => [],
        'override' => true
    ],
    'modules' => [
        
        // optional parameters : 
        //      'multiple'=>[]
        //      'images'=>'single'      //Images type: single, multiple

        'categories' => [
            'data'=> [
                // Separated by ':', numeric value represents size of the field its 192 by default and is optional
                
                // Regular data types: 
                //      string, int, text, bool, date
                
                // Data type modifiers 
                //      unique

                //Must start with data type nd then followed by size, then by modifiers if required

                // Special data types: 
                //      parent: requires name of a module, creates a one-to-many relation with the current module
                //      related: requires name of a module, creates many to many relation with current module

                'title'             => 'string:128',
                'slug'              => 'string:128:unique',
                'short_description' => 'string',
            ],
            'images'=>'single'
        ],

        'tags' => [
            'data'=> [
                'title'             => 'string:128'
            ]
        ],

        'posts' => [
            'data'  => [
                'author'            => 'parent:users',
                'title'             => 'string:256',
                'slug'              => 'string:128:unique',
                'short_description' => 'string',
                'full_description'  => 'text',
                'category'          => 'parent:categories',
                'posted_at'         => 'datetime',
                'tags'              => 'related:tags'
            ],
            'images'=>'multiple'
        ],
    ]
];
