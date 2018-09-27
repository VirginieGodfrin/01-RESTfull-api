<?php

namespace AppBundle\Test;
use GuzzleHttp\Client;

// ApiTestCase will be a base class for all our API tests. It extend the PHPUnit_Framework_TestCase.
class ApiTestCase extends \PHPUnit_Framework_TestCase 
{
	private static $staticClient;

	// we use $client in sub-classes
	/**
     * @var Client
     */
    protected $client;


    // 1) Guzzle Client 
    // static fct setUpBeforeClass:  PHPUnit calls this one time at the beginning of running your whole test suite.
    // make sure the Client is created just once
    // we always use the same Guzzle client
    // setter $staticClient
	public static function setUpBeforeClass() {

		self::$staticClient = new Client([ 
			'base_url' => 'http://127.0.0.1:8001/', 
			'defaults' => [
				'exceptions' => false ]
		]); 
	}
	//setter $client
	protected function setUp() 
	{
		$this->client = self::$staticClient; 
	}
}