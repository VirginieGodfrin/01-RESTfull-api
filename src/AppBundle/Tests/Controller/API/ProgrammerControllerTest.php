<?php

namespace AppBundle\Tests\Controller\API;
use AppBundle\Test\ApiTestCase;
use AppBundle\Test\ResponseAsserter;

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

	// test one ressources
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
		$this->asserter()->assertResponsePropertiesExist($response, array(
				'nickname', 
				'avatarNumber', 
				'powerLevel', 
				'tagLine'
		));
		// $this->asserter()->assertResponsePropertyEquals($response, 'nickname', 'UnitTester');
		// because assertEquals do not work well I create assertResponsePropertySame that do the same work !
		$this->asserter()->assertResponsePropertySame($response, 'nickname', 'UnitTester');
	}

	// testing the GET collection 
	public function testGETProgrammersCollection() 
	{
		$this->createProgrammer(array( 
			'nickname' => 'UnitTester', 
			'avatarNumber' => 3,
		)); 
		$this->createProgrammer(array(
			'nickname' => 'CowboyCoder',
			'avatarNumber' => 5, 
		));
		// the request
		$response = $this->client->get('/api/programmers');
		// the assert
		$this->assertSame(200, $response->getStatusCode());
		// because listAction return an associative array with a programmers (the collection of programmers)
		// let's first assert that there's a programmers key in the response and that it's an array.
		$this->asserter()->assertResponsePropertyIsArray($response, 'programmers');
		// next, let's assert that there are two things on this array
		$this->asserter()->assertResponsePropertyCount($response, 'programmers', 2);
		$this->asserter()->assertResponsePropertySame($response, 'programmers[1].nickname', 'CowboyCoder');
		// to test it use this commande with --filter and fucntion name :
		// php bin/phpunit -c app --filter testGETProgrammersCollection src/AppBundle/Tests/Controller/Api/ProgrammerControllerTest.php
	}
}