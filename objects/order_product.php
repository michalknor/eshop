<?php
class Order_product {

	// database connection and table name
	private $conn;
	private $table_name = "orders_products";

	// object properties
	public $product_id;	
	public $value;
	public $order_id;

	// constructor with $db as database connection
	public function __construct($db) {
		$this->conn = $db;
	}

	// login
	function read() {

		// prepare while statement
		$product_id = isset($this->product_id) ? " AND op.product_id = ?" : "";
		$value = isset($this->value) ? " AND op.value = ?" : "";
		$order_id = isset($this->order_id) ? " AND op.order_id = ?" : "";
		
		// query 
		$query = "SELECT
					op.product_id, op.value, op.order_id
				FROM
					" . $this->table_name . " AS op
				WHERE
					1=1" . $product_id . $value . $order_id . "";
		//show query					
		//echo $query;

		// prepare query statement
		$stmt = $this->conn->prepare($query);
		// bind parameters in while statement
		$index = 1;
		if ($this->product_id != null) {
			$stmt->bindParam($index, $this->product_id);
			$index++;
		}
		if ($this->value != null) {
			$stmt->bindParam($index, $this->value);
			$index++;
		}
		if ($this->order_id != null) {
			$stmt->bindParam($index, $this->order_id);
		}

		// execute query
		$stmt->execute();

		return $stmt;
	}

	function insert() {
		// query to insert record and create new order
		$query = "INSERT INTO
				" . $this->table_name . " (product_id, value, order_id)
			VALUES
				(:product_id, :value, :order_id);";

		// prepare query
		$stmt = $this->conn->prepare($query);

		// bind values
		$stmt->bindParam(":product_id", $this->product_id);
		$stmt->bindParam(":value", $this->value);
		$stmt->bindParam(":order_id", $this->order_id);

		$stmt->execute();
	}

	function update() {
		$query = "UPDATE ". $this->table_name . " SET value = value + 1 WHERE product_id = :product_id AND order_id = :order_id";

		// prepare query
		$stmt = $this->conn->prepare($query);

		// bind values
		$stmt->bindParam(":product_id", $this->product_id);
		$stmt->bindParam(":order_id", $this->order_id);

		// execute query
		$stmt->execute();
	}
}
?>