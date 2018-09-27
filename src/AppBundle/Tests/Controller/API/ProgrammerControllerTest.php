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
        // Extend the base class ApitestCase
        $response = $this->client->post('/api/programmers', [ 
			'body' => json_encode($data)
		]);
		
		// assert... test a value 
		// assertEquals: Reports an error identified by $message if the two parameters are not equal.
		$this->assertEquals(201, $response->getStatusCode());
		$this->assertTrue($response->hasHeader('Location'));
		$finishedData = json_decode($response->getBody(true), true);
		$this->assertArrayHasKey('nickname', $finishedData);
	}
}