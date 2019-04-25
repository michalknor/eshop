<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

// include database and object files
include_once '../config/database.php';
include_once '../objects/user.php';

// database connection
$database = new Database();
$db = $database->getConnection();

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
	$users_arr = array("code" => 200);
	$users_arr["records"] = array();

	// retrieve our table contents
	// fetch() is faster than fetchAll()
	// http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
		// extract row
		// this will make $row['name'] to
		// just $name only
		extract($row);

		$user_item=array(
			"id" => $id,
			"username" => $username,
			"email" => $email
		);

		array_push($users_arr["records"], $user_item);
	}

	// set response code - 200 OK
	http_response_code(200);

	// show users data in json format
	echo json_encode($users_arr, JSON_NUMERIC_CHECK );
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