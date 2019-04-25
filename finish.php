<?php 
session_start();

// check if user is logged in
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
	// include database and object files
	include_once 'config/database.php';
	include_once 'objects/order_not_finished.php';
	include_once 'objects/order_product_not_finished.php';
	include_once 'objects/order.php';
	include_once 'objects/order_product.php';

	// database connection
	$database = new Database();
	$db = $database->getConnection();

	$order_not_finished = new Order_not_finished($db);
	$order_not_finished->user_id = $_SESSION["id"];

	$stmt = $order_not_finished->read();

	$num = $stmt->rowCount();
	if ($num == 0) {
		// user has no unfinished orders so redirect him to profile (this should never happen)
		header("location: /eshop/prihlasenie.php");
		exit;
	}
	$order = new Order($db);
	$row_order_not_finished = $stmt->fetch(PDO::FETCH_ASSOC);
	$order_not_finished_id = $row_order_not_finished['id'];
	$order->total_price = $row_order_not_finished['total_price'];
	$order->user_id = $row_order_not_finished['user_id'];
	$order_id = $order->insert();

	$order_product_not_finished = new Order_product_not_finished($db);
	$order_product_not_finished->order_id = $order_not_finished_id;

	$stmt = $order_product_not_finished->read();

	$order_product = new Order_product($db);
	$order_product->order_id = $order_id;

	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		extract($row);
		$order_product->product_id = $product_id;
		$order_product->value = $value;
		$order_product->insert();
	}
	$order_product_not_finished->delete();

	$order_not_finished->id = $order_not_finished_id;
	$order_not_finished->delete();
}
// redirect to profile
header("location: /eshop/prihlasenie.php");

?>