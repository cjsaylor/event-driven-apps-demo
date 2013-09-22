<?php

class Repository {

	public static $items = array(
		'item1' => array(
			'name' => 'Item 1',
			'role' => array(),
			'price' => 30,
			'available' => 3
		),
		'item2' => array(
			'name' => 'Item 2',
			'role' => array(),
			'price' => 40,
			'available' => 1
		),
		'item3' => array(
			'name' => 'Premium Item',
			'role' => array('premium', 'superpremium'),
			'price' => 50,
			'available' => 10
		),
		'item4' => array(
			'name' => 'Super Premium Item',
			'role' => array('superpremium'),
			'price' => 60,
			'available' => 10
		)
	);

}
