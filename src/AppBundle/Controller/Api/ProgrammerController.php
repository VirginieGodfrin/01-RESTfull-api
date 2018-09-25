<?php

namespace AppBundle\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route; 
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class ProgrammerController extends Controller
{
	// First end point 
	/**
	* @Route("/api/programmers") 
	* @Method("POST")
	*/
	public function newAction()
	{
		return new Response('Let\'s do this!');
	}

}