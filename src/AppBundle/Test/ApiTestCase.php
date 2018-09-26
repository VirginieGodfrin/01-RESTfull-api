<?php

namespace AppBundle\Test;
use GuzzleHttp\Client;

class ApiTestCase extends \PHPUnit_Framework_TestCase 
{
	private static $staticClient;

	/**
     * @var Client
     */
    protected $client;

	public static function setUpBeforeClass() {
		self::$staticClient = new Client([ 
			'base_url' => 'http://127.0.0.1:8001/app_dev.php', 
			'defaults' => [
				'exceptions' => false ]
		]); 
	}

	protected function setUp() 
	{
		$this->client = self::$staticClient; 
	}
}