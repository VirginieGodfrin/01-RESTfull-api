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
use AppBundle\Api\ApiProblem;
use AppBundle\Api\ApiProblemException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use AppBundle\Pagination\PaginatedCollection;

class ProgrammerController extends  BaseController
{
	/**
	* @Route("/api/programmers") 
	* @Method("POST")
	*/
	public function newAction(Request $request)
	{
		// throw new \Exception();
		
		$body = $request->getContent();
		$data = json_decode($body, true);
		$programmer = new Programmer();
		// this work with php 5.6.3
		$form = $this->createForm(new ProgrammerType(), $programmer);
        $this->processForm($request, $form);

        // Handling Validation in the Controller
        if (!$form->isValid()) {
        	$this->throwApiProblemValidationException($form);
        }

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
	public function listAction(Request $request)
	{
		// make pagination : you need to tell the pagination library what page you're on and give it a query builder
		// The 1 is the default value in case there is no query parameter:
		$page = $request->query->get('page', 1);
		// create a query builder
		$qb = $this->getDoctrine() 
			->getRepository('AppBundle:Programmer') 
			->findAllQueryBuilder();
		// 1° adaptater
		$adapter = new DoctrineORMAdapter($qb);
		// 2° pagerfanta
		$pagerfanta = new Pagerfanta($adapter);
		// 3° max per page 
		$pagerfanta->setMaxPerPage(10); 
		// 4° current page 
		$pagerfanta->setCurrentPage($page);

		// Using Pagerfanta to Fetch Results
		// we need Pagerfanta to return the programmers based on whatever page is being requested
		// $pagerfanta->getCurrentPageResults() return a type of traversable object with those programmes inside
		// This confuses the serializer
		// loop over that traversable object from Pagerfanta and push each Programmer object into our simple array
		$programmers = [];
		foreach ($pagerfanta->getCurrentPageResults() as $result) {
			$programmers[] = $result;
		}
		// In createApiResponse , we still need to pass in the programmers key, but we also need to add count and total
		// Add the total key and set it to $pagerfanta->getNbResults()
		// $response = $this->createApiResponse([ 
		// 	'total' => $pagerfanta->getNbResults(), 
		// 	'count' => count($programmers), 
		// 	'programmers' => $programmers,
		// ], 200);
		
		// Using the new class is easy
		$paginatedCollection = new PaginatedCollection($programmers, $pagerfanta->getNbResults());
		// the response with collection
		$response = $this->createApiResponse($paginatedCollection, 200);

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

        if (!$form->isValid()) {
        	$this->throwApiProblemValidationException($form);
        }

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
        // If the $body has a bad format, then $data will be null, then return 400 status code
        if ($data === null) {
        	// creating an ApiProblem, so we can only throw an exception to stop the flow, and
        	// the response body that an HttpException generates is still an HTML error page
        	$apiProblem = new ApiProblem(400, ApiProblem::TYPE_INVALID_REQUEST_BODY_FORMAT);
        	throw new ApiProblemException($apiProblem);

        }
        $clearMissing = $request->getMethod() != 'PATCH';
        $form->submit($data, $clearMissing);
    }

    // getErrorsFromForm: collect errors form each fields
    private function getErrorsFromForm(FormInterface $form) 
    {
		$errors = array();
		foreach ($form->getErrors() as $error) {
			$errors[] = $error->getMessage(); 
		}
		foreach ($form->all() as $childForm) {
			if ($childForm instanceof FormInterface) {
				if ($childErrors = $this->getErrorsFromForm($childForm)) { 
					$errors[$childForm->getName()] = $childErrors;
				} 
			}
		}
		return $errors; 
	}

	private function throwApiProblemValidationException(FormInterface $form)
	{
        $errors = $this->getErrorsFromForm($form);
        // use api problem
        $apiProblem = new ApiProblem( 
        	400,
			ApiProblem::TYPE_VALIDATION_ERROR
		);

		$apiProblem->set('errors', $errors);

		throw new ApiProblemException($apiProblem);
	}

}