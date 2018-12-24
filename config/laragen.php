<?php

return [
	'options' => [
		'generated_by_default' => ['migration', 'controller', 'model'],
		'override' => true
	],
	'modules' => [
		
		// optional parameters : 
		// 		'multiple'=>[]
		// 		'images'=>'single'   	//Images type: single, multiple

		'categories'	=> [
			'data'=>[
				// Separated by ':', numeric value represents size of the field its 192 by default and is optional
				
				// Regular data types: 
				// 		string, int, text, bool
				
				// Data type modifiers 
				// 		required, unique, <numeric-values-for-size>

				// Special data types: 
				// 		parent: requires name of a module, creates a one-to-many relation with the current module
				// 		related: requires name of a module, creates many to many relation with current module

				'title' 			=> 'string:128:required',
				'slug' 				=> 'string:128:unique|required',
				'short_description' => 'string',
			],
			'images'=>'single'
		],

		'tags'	=> [
			'data'=>[
				'title' 			=> 'string:128:required'
			]
		],

		'posts'	=> [
			'data'	=> [
				'author' 			=> 'parent:users',
				'title' 			=> 'string:256:required',
				'slug' 				=> 'string:128:unique|required',
				'short_description' => 'string',
				'full_description' 	=> 'text',
				'category'			=>	'parent:categories',
				'tags'				=>	'related:tags'
			],
			'images'=>'multiple'
		],
	]
];
