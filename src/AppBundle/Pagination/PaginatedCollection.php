<?php

namespace AppBundle\Pagination;

// Since we'll use pagination in a lot of places, we're going to need to duplicate this JSON structure
// Why not create an object with these properties, and then let the serializer turn that object into JSON?
class PaginatedCollection 
{
	// this object has an items property instead of programmers (re-use this class for other resources) 
	private $items;

	private $total;

	private $count;

	public function __construct(array $items, $totalItems) {
		$this->items = $items; 
		$this->total = $totalItems; 
		$this->count = count($items);
	}

}