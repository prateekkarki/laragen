<?php
return [
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
			'Api',
			'Request',
			'View'
		]
	],
	'image_sizes' => [
		'sm' => '500x500',
		'md' => '800x800',
		'xs' => '200x200',
	],
	'seed_rows' => 25,
	'listing_per_page' => 20,
	'user_model' => 'App\User'
];