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
			'Request',
			'View'
		]
	],
	'skip_generators' => [],
	'override' => true,
	'seed_rows' => 25,
	'user_model' => 'App\User'
];