<?php

session_start();

require dirname(dirname(__DIR__)) . '/vendor/autoload.php';
require dirname(__FILE__) . '/repository.php';

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\GenericEvent;

class Cart {

	private $plugins = array(
		'AvailabilityPlugin',
		'PermissionPlugin',
	);

	protected $items = array();

	protected $role;

	public function __construct() {
		$this->dispatcher = new EventDispatcher();
		foreach ($this->plugins as $plugin) {
			$path = dirname(__FILE__) . '/plugins/' . $plugin . '.php';
			if (file_exists($path)) {
				include $path;
				$this->dispatcher->addSubscriber(new $plugin);
			}
		}
	}

	public function __destruct() {
		$_SESSION['cart'] = $this->export();
	}

	public function items($items = null) {
		if ($items !== null) {
			$this->items = $items;
		}
		return $this->items;
	}

	public function add($item, $quantity = 1) {
		if (!in_array($item, array_keys(Repository::$items))) {
			throw new Exception('Item not found.');
		}
		$this->dispatcher->dispatch('cart.beforeAdd', new GenericEvent($this, array(
			'item' => $item,
			'quantity' => $quantity
		)));
		// Do the addition
		if (!empty($this->items[$item])) {
			$this->items[$item]['quantity']++;
		} else {
			$this->items[$item] = array(
				'name' => Repository::$items[$item]['name'],
				'quantity' => 1
			);
		}
	}

	public function update($item, $quantity) {
		// Item existence check
		if (!in_array($item, array_keys(Repository::$items))) {
			throw new Exception('Item not found.');
		}
		$this->dispatcher->dispatch('cart.beforeUpdate', new GenericEvent($this, array(
			'item' => $item,
			'quantity' => $quantity
		)));
		
		// Do the update
		$this->items[$item]['quantity'] = $quantity;
		
	}

	public function remove($item, $clear = false) {
		// Item existence check
		if (!array_key_exists($item, $this->items)) {
			return;
		}
		// Do the removal
		if (!$clear && !empty($this->items[$item]) && $this->items[$item]['quantity'] > 1) {
			$this->items[$item]['quantity']--;
		} else {
			unset($this->items[$item]);
		}
	}

	public function refresh() {
		$refreshEvent = new GenericEvent($this);
		foreach ($this->items as $key => $item) {
			// Item existence check
			if (!in_array($key, array_keys(Repository::$items))) {
				$this->remove($key, true);
				continue;
			}
			$refreshEvent->setArguments(array(
				'item' => $key,
				'reduce' => true
			));
			$this->dispatcher->dispatch('cart.refresh', $refreshEvent);
		}
		
	}

	public function role($role = null) {
		if ($role !== null) {
			$this->role = $role;
		}
		return $this->role;
	}

	public function export() {
		return json_encode(array(
			'items' => $this->items,
			'role' => $this->role
		));
	}

	public static function restore($cartJson) {
		$cart = json_decode($cartJson, true);
		$instance = new static();
		$instance->items($cart['items']);
		$instance->role($cart['role']);
		$instance->refresh();
		return $instance;
	}

}
