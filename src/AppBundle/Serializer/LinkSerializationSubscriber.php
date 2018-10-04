<?php 

namespace AppBundle\Serializer;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\JsonSerializationVisitor;
use Symfony\Component\Routing\RouterInterface;

// we want to include the URL to the programmer in its JSON representation. To make that URL, you need the router service. 
// But it isn't possible to access services from within a method in entity
// One way to do that it's with an event subscriber on the serializer.

// To create a subscriber with the JMSSerializer, you need to implement EventSubscriberInterface
class LinkSerializationSubscriber implements EventSubscriberInterface
{
	// To generate the real URI, we need the router
	private $router;
	public function __construct(RouterInterface $router) {
		$this->router = $router; 
	}
	
	public function onPostSerialize(ObjectEvent $event)
	{
		// The visitor is kind of in charge of the serialization process.
		// this will be an instance of JsonSerializationVisitor
		/** @var JsonSerializationVisitor $visitor */
		$visitor = $event->getVisitor();

		/** @var Programmer $programmer */
		$programmer = $event->getObject();

		// that class has a method on it called addData() 
		// We can use it to add custom fields we want
		$visitor->addData( 'uri',
			$this->router->generate('api_programmers_show', [ 
				'nickname' => $programmer->getNickname()
			]) 
		);
	}

	// In this method, we'll tell the serializer exactly which events we want to hook into. 
	// One of those will allow us to add a new field... which will be the URL to whatever Programmer is being serialized.
	public static function getSubscribedEvents()
	{

		return array( 
			array(
				'event' => 'serializer.post_serialize',
				'method' => 'onPostSerialize',
				'format' => 'json',
				'class' => 'AppBundle\Entity\Programmer'
			)
		);
	}

}