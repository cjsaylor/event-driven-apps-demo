<?php

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Event;

class PermissionPlugin implements EventSubscriberInterface {

	public static function getSubscribedEvents() {
		return array(
			'cart.beforeAdd' => 'checkPermissions',
			'cart.refresh' => 'checkPermissions'
		);
	}

	public function checkPermissions(Event $e) {
		$cart = $e->getSubject();
		$item = $e->getArgument('item');
		if (!$this->isPermitted($cart, $item)) {
			if (!$e->hasArgument('reduce')) {
				throw new Exception('Role must be: ' . implode(', ', Repository::$items[$item]['role']));
			}
			$cart->remove($item, true);
		}
	}

	public function isPermitted(Cart $cart, $item) {
		$repositoryItem = Repository::$items[$item];
		if (empty($repositoryItem['role'])) {
			return true;
		}
		return in_array($cart->role(), $repositoryItem['role']);
	}

}
