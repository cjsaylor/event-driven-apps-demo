<?php
session_start();

require dirname(__FILE__) . '/lib/cart.php';

if (empty($_SESSION['cart'])) {
	$cart = new Cart();
} else {
	$cart = Cart::restore($_SESSION['cart']);
}

$cart->role($_GET['role']);
header("Location: /event-driven-apps-demo/cart/index.php");
