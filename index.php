<!DOCTYPE html>
<?php 

session_start();
// include database and object files
include_once 'config/database.php';
include_once 'objects/product.php';
include_once 'objects/gallery.php';
include_once 'objects/order_not_finished.php';

// database connection
$database = new Database();
$db = $database->getConnection();

// create product object
$product = new Product($db);

// set properties of record to read
$product->id = $_GET['id'];
if (isset($_GET['sort'])) {
	if ($_GET['sort'] == "max") {
		$product->order = "DESC";
	}
	else if ($_GET['sort'] == "min") {
		$product->order = "ASC";
	}
}



$stmt = $product->read();

$num = $stmt->rowCount();

?>
<html>
<?php

//404
if ($num == 0) {
	http_response_code(404);
	?>
 	<head>
	  <title>eshop - <?php echo $name ?></title>
	</head>
 	<body>
	<?php
	header("HTTP/1.0 404 Not Found");
	echo 'No such product';
}
//product detal
else if ($_GET['id'] != null) {
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	extract($row);
	?>
	<head>
	 <title>eshop - <?php echo $name ?></title>
	</head>
	<body>
	<?php
	$order_not_finished = new Order_not_finished($db);
	$order_not_finished->user_id = $_SESSION["id"];

	$stmt1 = $order_not_finished->read();

	$num1 = $stmt1->rowCount();
	$order_not_finished_id = 0;
	if ($num1 == 1) {
		$row1 = $stmt1->fetch(PDO::FETCH_ASSOC);
		echo $row1["total_price"] . "€";
		?>
		<form action="finish.php" name="frm" method="post">

       		<input type="submit" name="finish" value="finish" />

    	</form>
    	<?php
	}
	echo '<h1>' . $name . '</h1>';
	echo '<img src="assets/images/' .  $img . '" alt="' . $name . '" height="200">';

	$gallery = new Gallery($db);
	$gallery->products_id = $_GET['id'];
	$stmt2 = $gallery->read();
	$num2 = $stmt->rowCount();
	while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)){
		extract($row2);
		echo '<img src="assets/images/' .  $img . '" alt="' . $name . '" height="200">';
	}

	echo '<br>' . $description . ' <br>';
	echo 'price: ' . $price . '€ <br>';
	?>
	<form action="order.php/?id=<?php echo $_GET['id']; ?>" name="frm" method="post">

       <input type="submit" name="order" value="order" />

    </form>
    <?php
}
//main page
else {
	?>
	<head>
	<title>eshop - dragon models</title>
	</head>
	<body>
	<?php
	$order_not_finished = new Order_not_finished($db);
	$order_not_finished->user_id = $_SESSION["id"];

	$stmt1 = $order_not_finished->read();

	$num1 = $stmt1->rowCount();
	$order_not_finished_id = 0;
	if ($num1 == 1) {
		$row = $stmt1->fetch(PDO::FETCH_ASSOC);
		extract($row);
		echo $row["total_price"] . "€";
		?>
		<form action="finish.php" name="frm" method="post">

       		<input type="submit" name="finish" value="finish" />

    	</form>
    	<?php
	}
	?>
	<h1>Dragon models</h1>
	<form action="/eshop" name="frm" method="post">

       <input type="submit" name="default" value="default" />

    </form>

	<form action="?sort=max" name="frm" method="post">

       <input type="submit" name="max" value="max" />

    </form>

    <form action="?sort=min" name="frm" method="post">

       <input type="submit" name="min" value="min" />

    </form>
 	<?php
 	

 	if($num > 0) {
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			extract($row);
			echo '<h2>' . $name . '</h2>';
			echo '<a href="?id='. $id .'"><img src="assets/images/' .  $img . '" alt="' . $img . '" height="200"></a><br>';
			echo 'price: ' . $price . '€ <br>';
		}
	}
	?>
 </body>
<?php
}
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
	?><a href="prihlasenie.php"><?php echo $_SESSION["username"]; ?> </a><?php
}
else {
	?>
	<a href="prihlasenie.php">Log in</a>
	<a href="registracia.php">Sign up</a>
	<?php
}
?>
</html>