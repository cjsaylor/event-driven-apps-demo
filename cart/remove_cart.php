<?php
session_start();

require 'cart.php';

if (empty($_SESSION['cart'])) {
	$cart = new Cart();
} else {
	$cart = Cart::restore($_SESSION['cart']);
}

try {
	$cart->remove($_GET['item']);
	$_SESSION['cart'] = $cart->export();
	header("Location: /demo/cart/index.php");
} catch (Exception $e) {
	echo $e->getMessage();
}
