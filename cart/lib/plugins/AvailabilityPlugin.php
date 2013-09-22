<?php

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Event;

class AvailabilityPlugin implements EventSubscriberInterface {

	public static function getSubscribedEvents() {
		return array(
			'cart.beforeAdd' => 'checkAvailability',
			'cart.beforeUpdate' => 'checkAvailability',
			'cart.refresh' => 'checkAvailability'
		);
	}

	public function checkAvailability(Event $e) {
		$cart = $e->getSubject();
		$item = $e->getArgument('item');
		$cartItems = $cart->items();
		$offset = $e->hasArgument('quantity') ? $e->getArgument('quantity') : 0;
		$quantity = isset($cartItems[$item]) ? 
			$cartItems[$item]['quantity'] : 
			($offset > 0 ? 0 : 1);
		
		if (!$this->isAvailable($cart, $item, $quantity, $offset)) {
			if (!$e->hasArgument('reduce')) {
				throw new Exception('Item does not have sufficient availability.');
			}
			$cartItems[$item]['quantity'] = Repository::$items[$item]['available'];
			$cart->items($cartItems);
		}
	}

	protected function isAvailable(Cart $cart, $item, $quantity, $offset = 0) {
		$cartItems = $cart->items();
		return $quantity + $offset <= Repository::$items[$item]['available'];
		
	}

}
