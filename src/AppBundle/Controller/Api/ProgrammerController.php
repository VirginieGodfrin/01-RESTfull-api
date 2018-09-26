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
use Symfony\Component\HttpFoundation\JsonResponse;

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
		$data = $this->serializeProgrammer($programmer);
		// $response = new Response(json_encode($data), 201);
		$response = new JsonResponse($data, 201);
		// and best-practices say that you should set a Location header on the response
		// handle url
		$url = $this->generateUrl(
			'api_programmers_show',
			['nickname' => $programmer->getNickname()] 
		);
		$response->headers->set('Location', $url);
		$response->headers->set('Content-Type', 'application/json');
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

		$data = $this->serializeProgrammer($programmer);

		// $response = new Response(json_encode($data), 200);
		$response = new JsonResponse($data, 201);
		// setting Content-Type: application/json
		$response->headers->set('Content-Type', 'application/json');
		return $response;
	}

	/**
	 * @Route("/api/programmers") 
	 * @Method("GET")
	 */
	public function listAction()
	{
		$programmers = $this->getDoctrine() 
			->getRepository('AppBundle:Programmer') 
			->findAll();
		// we need to loop over the Programmers and serialize them one-by-one
		// by putting the collection inside a key, we have room for more root keys later like maybe count or offset for pagination
		// your outer JSON should always be an object, not an array (JSON Hijacking)
		$data = ['programmers' => []];
		foreach ($programmers as $programmer) {
			$data['programmers'][] = $this->serializeProgrammer($programmer); 
		}

		// $response = new Response(json_encode($data), 200);
		$response = new JsonResponse($data, 201);
		$response->headers->set('Content-Type', 'application/json');
		return $response;
	}

	// private function that return an array 
	private function serializeProgrammer(Programmer $programmer) {
		return array(
			'nickname' => $programmer->getNickname(), 
			'avatarNumber' => $programmer->getAvatarNumber(), 
			'powerLevel' => $programmer->getPowerLevel(), 
			'tagLine' => $programmer->getTagLine(),
		);
	}

}