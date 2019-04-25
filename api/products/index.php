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
include_once '../objects/gallery.php';

// database connection
$database = new Database();
$db = $database->getConnection();

// create product object
$product = new Product($db);

// set properties of record to read
$product->id = $_GET['id'];
$product->name = $_GET['name'];
$product->price = $_GET['price'];
$product->description = $_GET['description'];
$product->img = $_GET['img'];

$stmt = $product->read();

$num = $stmt->rowCount();

// check if more than 0 record found
if($num > 0) {

	// products array
	$products_arr = array("code" => 200);
	$products_arr["records"] = array();

	// retrieve our table contents
	// fetch() is faster than fetchAll()
	// http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		// extract row
		// this will make $row['name'] to
		// just $name only
		extract($row);

		$product_item=array(
			"id" => $id,
			"name" => $name,
			"price" => $price,
			"description" => html_entity_decode($description),
			"img" => $img
		);

		$product_item["gallery"] = array();

		$gallery = new Gallery($db);
		$gallery->products_id = $id;
		$stmt2 = $gallery->read();
		$num2 = $stmt->rowCount();
		while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)){
			extract($row2);
			array_push($product_item["gallery"], $img);
		}

		array_push($products_arr["records"], $product_item);
	}

	// set response code - 200 OK
	http_response_code(200);

	// show products data in json format
	echo json_encode($products_arr, JSON_NUMERIC_CHECK );
}
else {

	// set response code - 404 Not found
	http_response_code(404);

	// tell the user no products found
	echo json_encode(
		array(
			"code" => "404",
			"message" => "products not found."
		)
	);
}
// token generation
// https://stackoverflow.com/questions/18830839/generating-cryptographically-secure-tokens
/*$bytes = 32;
$token = bin2hex(openssl_random_pseudo_bytes($bytes));
echo $token;*/
?>