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
use AppBundle\Form\ProgrammerType;


class ProgrammerController extends  BaseController
{
	/**
	* @Route("/api/programmers") 
	* @Method("POST")
	*/
	public function newAction(Request $request)
	{

		$body = $request->getContent();
		$data = json_decode($body, true);
		$programmer = new Programmer();
		$form = $this->createForm(new ProgrammerType(), $programmer);
		// $form->submit($data);
		$form->isSubmitted($data);

		$programmer->setNickname($data['nickname']);
		$programmer->setAvatarNumber($data['avatarNumber']);
		$programmer->setTagLine($data['tagLine']);
		$programmer->setUser($this->findUserByUsername('weaverryan'));

		$em = $this->getDoctrine()->getManager();
		$em->persist($programmer);
		$em->flush();
		// when you create a resource, the status code should be 201:
		return new Response('It worked. Believe me - I\'m an API', 201);
		// and best-practices say that you should set a Location header on the response
		$response->headers->set('Location', '/some/programmer/url');
	}

}