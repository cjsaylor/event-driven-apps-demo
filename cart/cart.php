<?php

require 'repository.php';

class Cart {

	protected $items = array();

	protected $role;

	public function items($items = null) {
		if ($items !== null) {
			$this->items = $items;
		}
		return $this->items;
	}

	public function add($item, $quantity = 1) {
		// Item existence check
		if (!in_array($item, array_keys(Repository::$items))) {
			throw new Exception('Item not found.');
		}
		// Permission check
		if (!empty(Repository::$items[$item]['role']) && !in_array($this->role, Repository::$items[$item]['role'])) {
			throw new Exception('Role must be: ' . implode(', ', Repository::$items[$item]['role']));
		}
		// Availability check
		if (isset($this->items[$item]) && $this->items[$item]['quantity'] + $quantity > Repository::$items[$item]['available']) {
			throw new Exception('Item does not have sufficient availability.');
		} elseif (Repository::$items[$item]['available'] < 1) {
			throw new Exception('Item does not have sufficient availability.');
		}
		// Do the addition
		if (!empty($this->items[$item])) {
			$this->items[$item]['quantity']++;
		} else {
			$this->items[$item] = array(
				'name' => Repository::$items[$item]['name'],
				'quantity' => 1
			);
		}
		$this->refresh();
	}

	public function update($item, $quantity) {
		// Item existence check
		if (!in_array($item, array_keys(Repository::$items))) {
			throw new Exception('Item not found.');
		}
		// Permission check
		if (!empty(Repository::$items[$item]['role']) && !in_array($this->role, Repository::$items[$item]['role'])) {
			throw new Exception('Role must be: ' . implode(', ', Repository::$items[$item]['role']));
		}
		// Availability check
		if ($quantity > Repository::$items[$item]['available']) {
			throw new Exception('Item does not have sufficient availability.');
		}
		// Do the update
		$this->items[$item]['quantity'] = $quantity;
		
	}

	public function remove($item, $clear = false) {
		// Item existence check
		if (!in_array($item, array_keys(Repository::$items))) {
			throw new Exception('Item not found.');
		}
		// Do the removal
		if (!$clear && !empty($this->items[$item]) && $this->items[$item]['quantity'] > 1) {
			$this->items[$item]['quantity']--;
		} else {
			unset($this->items[$item]);
		}
	}

	public function refresh() {
		foreach ($this->items as $key => $item) {
			// Item existence check
			if (!in_array($key, array_keys(Repository::$items))) {
				$this->remove($key, true);
			}
			// Permission check
			if (!empty(Repository::$items[$key]['role']) && !in_array($this->role, Repository::$items[$key]['role'])) {
				$this->remove($key, true);
			}
			// Availability check
			if ($item['quantity'] > Repository::$items[$key]['available']) {
				$this->remove($key, true);
			}
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
