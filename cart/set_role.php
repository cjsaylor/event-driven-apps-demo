<?php
session_start();

require 'cart.php';

if (empty($_SESSION['cart'])) {
	$cart = new Cart();
} else {
	$cart = Cart::restore($_SESSION['cart']);
}

$cart->role($_GET['role']);
$_SESSION['cart'] = $cart->export();
header("Location: /demo/cart/index.php");
