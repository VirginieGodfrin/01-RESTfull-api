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
		$programmer->setNickname($data['nickname']);
		$programmer->setAvatarNumber($data['avatarNumber']);
		$programmer->setTagLine($data['tagLine']);
		$programmer->setUser($this->findUserByUsername('weaverryan'));

		$em = $this->getDoctrine()->getManager();
		$em->persist($programmer);
		$em->flush();
		// when you create a resource, the status code should be 201:
		$response = new Response('It worked. Believe me - I\'m an API', 201);
		// and best-practices say that you should set a Location header on the response
		// handle url
		$url = $this->generateUrl(
			'api_programmers_show',
			['nickname' => $programmer->getNickname()] 
		);
		$response->headers->set('Location', $url);
		return $response;
	}

	/**
	 * @Route("/api/programmers/{nickname}", name="api_programmers_show") 
	 * @Method("GET")
	 */
	public function showAction($nickname)
	{
		$programmer = $this->getDoctrine() 
			->getRepository('AppBundle:Programmer')
			->findOneByNickname($nickname);

		// error 404
		if (!$programmer) {
			throw $this->createNotFoundException(sprintf(
			'No programmer found with nickname "%s"',
			$nickname ));
		}

		$data = array(
			'nickname' => $programmer->getNickname(), 
			'avatarNumber' => $programmer->getAvatarNumber(), 
			'powerLevel' => $programmer->getPowerLevel(), 
			'tagLine' => $programmer->getTagLine(),
		);
		$response = new Response(json_encode($data), 200);
		// setting Content-Type: application/json
		$response->headers->set('Content-Type', 'application/json');
		return $response;
	}

}