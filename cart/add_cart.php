<?php
session_start();

require dirname(__FILE__) . '/lib/cart.php';

if (empty($_SESSION['cart'])) {
	$cart = new Cart();
} else {
	$cart = Cart::restore($_SESSION['cart']);
}

try {
	$cart->add($_GET['item']);
	$_SESSION['cart'] = $cart->export();
	header("Location: /event-driven-apps-demo/cart/index.php");
} catch (Exception $e) {
	echo $e->getMessage();
}
