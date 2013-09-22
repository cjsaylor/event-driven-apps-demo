<?php
	session_start();

	require dirname(__FILE__) . '/lib/cart.php';

	if (empty($_SESSION['cart'])) {
		$cart = new Cart();
	} else {
		$cart = Cart::restore($_SESSION['cart']);
	}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf8">
	<title>Event Driven App Demo - Cart</title>
</head>
<body>
	<h2>Store</h2>
	<ul>
	<?php foreach (Repository::$items as $key => $item): ?>
		<li><?php echo $item['name'] ?> ($<?php echo $item['price']?>) - <a href="add_cart.php?item=<?php echo $key ?>">Add to Cart</a></li>
	<?php endforeach; ?>
	</ul>
	<hr>
	<?php
		$role = $cart->role();
	?>
	<h2>My Cart <?php if (!empty($role)) echo '(Role: ' . $role . ')' ?></h2>
	<ul>
	<?php
		$items = $cart->items();
		if (empty($items)) {
			echo '<li>No items in the cart.</li>';
		} else {
			foreach ($items as $key => $item) {
				echo '<li>' . $item['name'] . ' - ' . $item['quantity'] . ' <a href="remove_cart.php?item=' . $key . '">(Remove 1)</a></li>';
			}
		}
	?>
	</ul>
	<hr>
	<h2>Misc</h2>
	<ul>
		<li><a href="set_role.php?role=">Reset role</a></li>
		<li><a href="set_role.php?role=premium">Set role to &quot;premium&quot;</a></li>
		<li><a href="set_role.php?role=superpremium">Set role to &quot;superpremium&quot;</a></li>
	</ul>
</body>
</html>
