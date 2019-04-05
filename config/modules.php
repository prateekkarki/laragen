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
    'categories' => [
        'title'             => 'string|max:128',
        'slug'              => 'string|max:128|unique|required',
        'short_description' => 'string',
        'show_in_menu'      => 'boolean',
        'banners'           => 'gallery',
        'image'             => 'image|mimes:jpeg',
        'brochure'          => 'file|mimes:pdf|max:500000'
    ],

    'tags' => [
        'title'             => 'string:128'
    ],

    'posts' => [
        'author'            => 'parent:users',
        'title'             => 'string|max:128|required',
        'slug'              => 'string|max:128|unique',
        'short_description' => 'string|max:512',
        'full_description'  => 'text',
        'category'          => 'parent:categories',
        'posted_at'         => 'datetime',
        'tags'              => 'related:tags'
    ],
];
