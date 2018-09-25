<?php

require __DIR__.'/vendor/autoload.php';

$client = new GuzzleHttp\Client([ 
	'base_url' => 'http://127.0.0.1:8001', 
	'defaults' => [
		'exceptions' => false
	]
]);

// First end point 
$response = $client->post('/api/programmers');

echo $response; 
echo "\n\n";