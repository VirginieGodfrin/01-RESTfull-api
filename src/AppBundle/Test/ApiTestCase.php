<?php

namespace AppBundle\Test;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

// ApiTestCase will be a base class for all our API tests.
// to get the get container and use our services. 
// Symfony has a helpful way to do this - it's a base class called KernelTestCase :
class ApiTestCase extends KernelTestCase
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
		// 2) BootKernel
		// we need to clearing everything out before each test, for that we need the EntityManager object
		// The kernel is the heart of Symfony, and booting it basically just makes the service container available.
		self::bootKernel();
	}

	//setter $client
	protected function setUp() 
	{
		$this->client = self::$staticClient;

		$this->purgeDatabase(); 
	}

	/**
	 * Clean up Kernel usage in this test.
	 */
	protected function tearDown() 
	{
		// purposefully not calling parent class, which shuts down the kernel
		
	}

	// this method let our test classes fetch services from the container
	protected function getService($id) 
	{
		return self::$kernel->getContainer() ->get($id);
	}

	// clearing data
	// we can call this before every test
	private function purgeDatabase() 
	{
		// get entity manager
		// because we have the Doctrine DataFixtures library installed, we can use a great class called ORMPurger
		$purger = new ORMPurger($this->getService('doctrine')->getManager());
		$purger->purge(); 
	}
}