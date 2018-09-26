<?php

namespace AppBundle\Tests\Controller\API;

class ProgrammerControllerTest extends \PHPUnit_Framework_TestCase
{
	public function testPOST()
	{
		$client = new \GuzzleHttp\Client([
			'base_url' => 'http://127.0.0.1:8001', 
			'defaults' => [
				'exceptions' => false
			]
		]);

		// $nickname = 'ObjectOrienter'.rand(0, 999); 
		$data = array(
            'nickname' => 'ObjectOrienter',
            'avatarNumber' => 5,
            'tagLine' => 'a test dev!'
        );

        // 1) Create a programmer resource
        $response = $client->post('/api/programmers', [ 
			'body' => json_encode($data)
		]);

        $this->assertEquals(201, $response->getStatusCode());
		$this->assertTrue($response->hasHeader('Location'));
		$finishedData = json_decode($response->getBody(true), true);
		$this->assertArrayHasKey('nickname', $finishedData);

	}
}