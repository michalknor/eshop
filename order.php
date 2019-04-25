<?php 
session_start();

// check if user is logged in
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
	if (isset($_GET['id'])) {
		// include database and object files
		include_once 'config/database.php';
		include_once 'objects/product.php';
		include_once 'objects/order_not_finished.php';
		include_once 'objects/order_product_not_finished.php';

		// database connection
		$database = new Database();
		$db = $database->getConnection();

		// create product object
		$product = new Product($db);

		// set properties of record to read
		$product->id = $_GET['id'];

		$stmt = $product->read();

		$num = $stmt->rowCount();

		// found product with such id
		if($num == 1) {
			$row_product = $stmt->fetch(PDO::FETCH_ASSOC);
			extract($row_product);

			$order_not_finished = new Order_not_finished($db);
			$order_not_finished->user_id = $_SESSION["id"];

			$stmt = $order_not_finished->read();

			$num = $stmt->rowCount();
			$order_not_finished_id = 0;
			if ($num == 0) {
				// user has no unfinished orders so create new and save order id
				$order_not_finished_id = $order_not_finished->insert();
			}
			else {
				// get id of user order
				$row_order_not_finished = $stmt->fetch(PDO::FETCH_ASSOC);
				extract($row_order_not_finished);
				$order_not_finished_id = $row_order_not_finished['id'];
			}
			// add price 
			$order_not_finished->price = $row_product['price'];
			$order_not_finished->add_price();

			// add product to order
			$order_product_not_finished = new Order_product_not_finished($db);
			$order_product_not_finished->product_id = $product->id;
			$order_product_not_finished->order_id = $order_not_finished_id;
			$stmt = $order_product_not_finished->read();
			$num = $stmt->rowCount();
			if ($num == 0) {
				$order_product_not_finished->insert();
			}
			else {
				$order_product_not_finished->update();
			}
			



			// redirect back to product
			header("location: /eshop?id=" . $_GET['id']);
		}
		// no product with such id
		else {
			header("location: /eshop");
		}
	}
	// if id from GET is null
	else {
		header("location: /eshop");
	}
}
// user is not logged in
else {
	header("location: /eshop/prihlasenie.php");
}

?>