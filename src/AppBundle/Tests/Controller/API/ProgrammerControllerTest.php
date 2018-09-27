<?php

namespace AppBundle\Tests\Controller\API;
use AppBundle\Test\ApiTestCase;

class ProgrammerControllerTest extends ApiTestCase
{
	// create user 
	protected function setUp()
	{
		parent::setUp(); 
		$this->createUser('weaverryan');
	}

	public function testPOST()
	{
		$nickname = 'ObjectOrienter'.rand(0, 999); 
		$data = array(
			'nickname' => 'ObjectOrienter', 
			'avatarNumber' => 5,
			'tagLine' => 'a test dev!'
		);
        // Extend the base class ApitestCase
        $response = $this->client->post('/api/programmers', [ 
			'body' => json_encode($data)
		]);
		
		// assert... test a value 
		// assertEquals: Reports an error identified by $message if the two parameters are not equal. 
		// if assertEquals don't work use assertSame() 
		// https://stackoverflow.com/questions/10254180/difference-between-assertequals-and-assertsame-in-phpunit/10254238
		$this->assertSame(201, $response->getStatusCode());
		$this->assertTrue($response->hasHeader('Location'));
		$finishedData = json_decode($response->getBody(true), true);
		$this->assertArrayHasKey('nickname', $finishedData);
		$this->assertSame('ObjectOrienter', $finishedData['nickname']);
	}

	public function testGETProgrammer()
	{
		// before we make a request to fetch a single programmer, we need to make sure there's one in the database !
		$this->createProgrammer(array( 
			'nickname' => 'UnitTester', 
			'avatarNumber' => 3,
		));

		$response = $this->client->get('/api/programmers/UnitTester');
		// assertequals & assertSame work both
		$this->assertSame(200, $response->getStatusCode());
		// Guzzle can decode the JSON for us if we call $response->json(), this gives us the decoded JSON
		$data = $response->json();
		// in assertEquals() put programmers property names as the first argument 
		// & the actual value in the second ( array_keys() on the json decoded response body - this give us the field names)
		$this->assertSame(
			array(
				'nickname', 
				'avatarNumber', 
				'powerLevel', 
				'tagLine'
			), 
			array_keys($data)
		);
	}
}