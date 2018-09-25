<?php

namespace AppBundle\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route; 
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;
use AppBundle\Entity\Programmer;


class ProgrammerController extends  BaseController
{
	/**
	* @Route("/api/programmers") 
	* @Method("POST")
	*/
	public function newAction(Request $request)
	{
		// json decode the body into an array
		$data = json_decode($request->getContent(), true);
		$programmer = new Programmer;
		// pass data to programmer
		$programmer->setNickname($data['nickname']);
		$programmer->setAvatarNumber($data['avatarNumber']);
		$programmer->setTagLine($data['tagLine']);
		$programmer->setUser($this->findUserByUsername('weaverryan'));

		$em = $this->getDoctrine()->getManager(); 
		$em->persist($programmer);
		$em->flush();

		return new Response('It worked. Believe me - I\'m an API');
	}

}