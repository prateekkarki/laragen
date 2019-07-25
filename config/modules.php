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
        'title'             => 'string|max:128',
        'slug'              => 'string|max:128|unique|required',
        'image'             => 'image',
        'short_description' => 'string'
    ],

    // 'categories' => [
    //     'title'             => 'string|max:128',
    //     'slug'              => 'string|max:128|unique|required',
    //     'short_description' => 'string',
    //     'show_in_menu'      => 'boolean',
    //     'image'             => 'image|mimes:jpeg',
    //     'banner'            => 'image|mimes:jpeg,png,gif',
    //     'brochure'          => 'file|mimes:pdf|max:333',
    //     'images'            =>  [
    //         'caption' =>'string|max:123',
    //         'file'    =>'image'  
    //     ],
    //     'reviews'          => [
    //         'name'      => 'string|max:128',
    //         'reviews'   => 'text|max:5000',
    //         'rating'    => 'date',
    //         'show_in_home'      => 'boolean',
    //     ]
    // ],

    'designations' => [
    ],

    'leave_types' => [
        'data'=>[
            'Male', 'Female', 
        ]
    ],

    'skills' => [
    ],

    'genders' => [
        'data'=>[
            'Male', 'Female', 
        ]
    ],

    'leaves' => [
        'title' => 'string|max:128',
        'start_date' => 'date',
        'end_date' => 'date',
        'reason' => 'string|max:128',
        'leave_type' => 'parent:leave_types|required',
        'title' => 'string|max:128'
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

    'projects'  => [
        'structure' => [
            'title'         => 'string|max:128',
            'description'   => 'string|max:512',
            'client'        => 'parent:clients',
            'gallery'        => 'gallery',
        ],
        'data'=>[
            'Web project X', 'Web project Y', 'App project Z', 
        ],
        'seo_enabled' => true;
    ],

    'employees' => [
        'structure' => [
            'name'              => 'string|max:128',
            'gender'            => 'parent:genders',
            'phone'             => 'string|max:256',
            'mobile'            => 'string|max:256',
            'email'             => 'string|max:256',
            'permanent_address' => 'string|max:512',
            'temporary_address' => 'string|max:512',
            'description'       => 'string|max:512',
            'position'          => 'parent:designations',
            'department'        => 'parent:departments',
            'date_joined'       => 'date',
            'date_of_birth'     => 'date',
            'salary'            => 'integer',
            'profile_image'     => 'image',
            'leaves'            => 'related:leaves',
            'is_active'         => 'boolean',
            'skills'            => 'related:skills',
            'password'          => 'string|max:512',
            'remember_token'    => 'string|max:128',
            'projects'          => 'related:projects'
        ],
        'seo_enabled' => true;
    ],

];
