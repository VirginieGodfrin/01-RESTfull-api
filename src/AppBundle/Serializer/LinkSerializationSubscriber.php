<?php 

namespace AppBundle\Serializer;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;

// we want to include the URL to the programmer in its JSON representation. To make that URL, you need the router service. 
// But it isn't possible to access services from within a method in entity
// One way to do that it's with an event subscriber on the serializer.

// To create a subscriber with the JMSSerializer, you need to implement EventSubscriberInterface
class LinkSerializationSubscriber implements EventSubscriberInterface
{
	public function onPostSerialize(ObjectEvent $event)
	{
		
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