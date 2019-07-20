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
			'View',
			'Notification',
			'Observer'
		]
	],
	'files_to_publish' => [
        'public',
        'app',
        'database',
        'resources',
	],
	'image_sizes' => [
		'sm' => '500x500',
		'md' => '800x800',
		'xs' => '200x200',
	],
	'events'	=> [
		'created','updated','deleted'
	],
	'seed_rows' => 25,
	'listing_per_page' => 20,
	'user_model' => 'App\User'
];