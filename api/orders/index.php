<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

// include database and object files
include_once '../config/database.php';
include_once '../objects/product.php';
include_once '../objects/user.php';
include_once '../objects/order.php';
include_once '../objects/order_product.php';

// database connection
$database = new Database();
$db = $database->getConnection();

// create product object
$product = new Product($db);

// set properties of record to read
$product->id = $_GET['id'];
$product->total_price = $_GET['total_price'];
$product->token = $_GET['token'];

$stmt = $product->read();

$num = $stmt->rowCount();

if ($_GET['token'] == null) {
	// set response code - 404 Not found
	http_response_code(403);

	// tell the user no users found
	echo json_encode(
		array(
			"code" => "403",
			"message" => "forbidden"
		)
	);
	exit;
}

if (strlen($_GET['token']) != 64) {
	// set response code - 404 Not found
	http_response_code(403);

	// tell the user no users found
	echo json_encode(
		array(
			"code" => "401",
			"message" => "invalid api key"
		)
	);
	exit;
}


// create user object
$user = new User($db);

// set properties of record to read
$user->token = $_GET['token'];

$stmt = $user->read();

$num = $stmt->rowCount();

// check if more than 0 record found
if($num > 0) {

	// users array
	$orders_arr = array("code" => 200);
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$orders_arr = array("user" => $row['username']);
	$orders_arr["records"] = array();
	$order = new Order($db);

	$order->user_id = $row['id'];

	$stmt = $order->read();
	// retrieve our table contents
	// fetch() is faster than fetchAll()
	// http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		// extract row
		// this will make $row['name'] to
		// just $name only
		extract($row);

		$order_item = array(
			"id" => $id,
			"total_price" => $total_price
		);

		$order_item["items"] = array();

		$order_product = new Order_product($db);

		$order_product->order_id = $id;

		$stmt = $order_product->read();

		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			extract($row);

			$product = new Product($db);
			$product->id = $product_id;
			$stmt = $product->read();
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			extract($row);

			$items_item = array(
				"name" => $name,
				"value" => $value
			);
			array_push($order_item["items"], $items_item);
		}
		array_push($orders_arr["records"], $order_item);
	}

	// set response code - 200 OK
	http_response_code(200);

	// show users data in json format
	echo json_encode($orders_arr, JSON_NUMERIC_CHECK );
}
else {

	// set response code - 404 Not found
	http_response_code(404);

	// tell the user no users found
	echo json_encode(
		array(
			"code" => "404",
			"message" => "user not found"
		)
	);
}
?>