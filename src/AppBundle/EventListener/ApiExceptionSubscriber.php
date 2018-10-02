<?php

namespace AppBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Api\ApiProblem;
use AppBundle\Api\ApiProblemException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

// When we throw an ApiProblemException , we need our app to automatically turn that into a nicely-formatted API Problem JSON 
// response and return it.  That code needs to live in a global spot.
// Whenever an exception is thrown in Symfony, it dispatches an event called kernel.exception . 
// If we attach a listener function to that event, we can take full control of how exceptions are handled.

class ApiExceptionSubscriber implements EventSubscriberInterface
{
	// whenever an exception is thrown, Symfony will call this method, with a GetResponseForExceptionEvent object
	public function onKernelException(GetResponseForExceptionEvent $event)
	{
		// onKernelException() try to understand what went wrong and return a Response. 
		// to do, detect if an ApiProblemException was thrown and create a nice Api Problem JSON response if it was
		// 1° get access to the exception 
		$e = $event->getException();
		if ($e instanceof ApiProblemException) { 
			$apiProblem = $e->getApiProblem();
		} else {
			$statusCode = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;
			$apiProblem = new ApiProblem($statusCode);
		}
		$response = new JsonResponse(
		 	$apiProblem->toArray(), 
		 	$apiProblem->getStatusCode()
		 );
        $response->headers->set('Content-Type', 'application/problem+json');
        // tell Symfony to use this
		$event->setResponse($response);

	}
	// one method from EventSubscriberInterface
	public static function getSubscribedEvents() 
	{
		return array(
			KernelEvents::EXCEPTION => 'onKernelException'
		);
	}
}