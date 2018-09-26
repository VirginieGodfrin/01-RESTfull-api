<?php

namespace AppBundle\Tests\Controller\API;
use AppBundle\Test\ApiTestCase;

class ProgrammerControllerTest extends ApiTestCase
{
	public function testPOST()
	{
		$nickname = 'ObjectOrienter'.rand(0, 999); 
		$data = array(
            'nickname' => $nickname,
            'avatarNumber' => 5,
            'tagLine' => 'a test dev!'
        );
        $response = $this->client->post('/api/programmers', [ 
			'body' => json_encode($data)
		]);
        $statusCode = $response->getStatusCode();

		$this->assertEquals(201, $response->getStatusCode());
		$this->assertTrue($response->hasHeader('Location'));
		$finishedData = json_decode($response->getBody(true), true);
		$this->assertArrayHasKey('nickname', $finishedData);
	}
}