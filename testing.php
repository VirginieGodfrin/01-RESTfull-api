<?php

require __DIR__.'/vendor/autoload.php';

$client = new GuzzleHttp\Client([ 
	'base_url' => 'http://127.0.0.1:8001', 
	'defaults' => [
		'exceptions' => false
	]
]);

// First end point
$nickname = 'ObjectOrienter'.rand(0, 999); 
$data = array(
	'nickname' => $nickname, 
	'avatarNumber' => 5, 
	'tagLine' => 'a test dev!'
);
// send json data in a array with key: body 
$response = $client->post('/api/programmers', [ 
	'body' => json_encode($data)
]);


echo $response; 
echo "\n\n";