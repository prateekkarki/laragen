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
	'seed_rows' => 25,
	'listing_per_page' => 20,
	'user_model' => 'App\User'
];