<?php

// Data options separated by '|'
// Arguments for options separated by ':'

// Regular data types: 
//      string, int, text, bool, date

// Data type options 
//      unique, required

//Must start with data type nd then followed by size, then by modifiers if required

// Special data types: 
//      parent: requires name of a module, creates a one-to-many relation with the current module
//      related: requires name of a module, creates many to many relation with current module

return [

    'departments' => [
        'structure' => [
            'title'             => 'string|max:128',
            'slug'              => 'string|max:128|unique|required',
            'image'             => 'image',
            'short_description' => 'string'            
        ]
    ],

    'skills' => [],

    'genders' => [
        'data'=>[
            'Male', 'Female', 
        ]
    ],

    'clients' => [
        'title'             => 'string|max:128',
        'image'             => 'image',
        'short_description' => 'string|max:512',
        'address'           => 'string|max:250',
        'phone'             => 'string|max:128',
        'mobile'            => 'string|max:128',
        'email'             => 'string|max:128',
    ],


    'employees' => [
        'structure' => [
            'name'              => 'string|max:128',
            'gender'            => 'parent:genders',
            'phone'             => 'string|max:256',
            'mobile'            => 'string|max:256',
            'permanent_address' => 'string|max:512',
            'temporary_address' => 'string|max:512',
            'description'       => 'string|max:512',
            'department'        => 'parent:departments',
            'date_joined'       => 'date',
            'date_of_birth'     => 'date',
            'salary'            => 'integer',
            'profile_image'     => 'image',
            'is_active'         => 'boolean',
            'skills'            => 'related:skills',
        ],
        'additional_fields' => [
            'generic' => false,
            'seo' => false
        ]
    ],

    'teams'  => [
        'structure' => [
            'title'     => 'string|max:128',
            'members'   => 'related:employees',
        ],
        'additional_fields' => [
            'seo' => false
        ]
    ],
    
    'projects'  => [
        'structure' => [
            'title'         => 'string|max:128',
            'description'   => 'string|max:512',
            'client'        => 'parent:clients',
            'team'          => 'parent:teams',
            'gallery'       => 'gallery',
        ],
        'data'=>[
            'Web project X', 'Web project Y', 'App project Z', 
        ],
        'additional_fields' => [
            'generic' => false,
            'seo' => true
        ]
    ],

];
