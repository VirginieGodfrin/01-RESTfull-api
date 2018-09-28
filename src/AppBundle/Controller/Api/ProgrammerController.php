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
use AppBundle\Form\UpdateProgrammerType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\FormInterface;

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
		// this work with php 5.6.3
		$form = $this->createForm(new ProgrammerType(), $programmer);
        $this->processForm($request, $form);
		$programmer->setUser($this->findUserByUsername('weaverryan'));

		$em = $this->getDoctrine()->getManager();
		$em->persist($programmer);
		$em->flush();
		// use createApiResponse form baseController to return reponse with correct content-type
		$response = $this->createApiResponse($programmer, 201);
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
		$response = $this->createApiResponse($programmer, 200);
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
		$data = ['programmers' => $programmers];
		$response = $this->createApiResponse($data, 200);
		return $response;
	}

	/**
	 * @Route("/api/programmers/{nickname}", name="api_programmers_update") 
	 * @Method({"PUT", "PATCH"})
	 */
	public function updateAction (Request $request, $nickname)
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
		// this work with php 5.6.3
		// array is needed to pass 'is_edit' parameter to form
		// $form = $this->createForm(new ProgrammerType(), $programmer, array( 
		// 	'is_edit' => true,
		// ));
		$form = $this->createForm(new UpdateProgrammerType(), $programmer);
        $this->processForm($request, $form);

		$em = $this->getDoctrine()->getManager();
		$em->persist($programmer);
		$em->flush();

		$response = $this->createApiResponse($programmer, 200);
		return $response;

	}
	/**
	 * @Route("/api/programmers/{nickname}") * @Method("DELETE")
	 */
	public function deleteAction($nickname)
	{
		$programmer = $this->getDoctrine() 
			->getRepository('AppBundle:Programmer') 
			->findOneByNickname($nickname);

		if ($programmer) {
			$em = $this->getDoctrine()->getManager(); 
			$em->remove($programmer);
			$em->flush();
		}
		// statut code 204
		return new Response(null, 204);
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

	// // how to use jms_serializer 1
	// private function serialize($data) {
	// 	return $this->container->get('jms_serializer') 
	// 		->serialize($data, 'json');
	// }

	// processForm: do the work of passing the data to the form 
	private function processForm(Request $request, FormInterface $form)
    {
        $data = json_decode($request->getContent(), true);
        // the clearmissing : clear all the missing fields, unless the request method is PATCH
        // second argument of submit(), if it's true any missing fields are nullified
        // if it's false missing fields are ignored
        $clearMissing = $request->getMethod() != 'PATCH';
        $form->submit($data, $clearMissing);
    }

}