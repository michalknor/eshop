<?php
class Order {

	// database connection and table name
	private $conn;
	private $table_name = "orders";

	// object properties
	public $id;
	public $user_id;
	public $total_price;
	public $price;

	// constructor with $db as database connection
	public function __construct($db) {
		$this->conn = $db;
	}

	// login
	function read() {

		// prepare while statement
		$id = isset($this->id) ? " AND o.id = ?" : "";
		$user_id = isset($this->user_id) ? " AND o.user_id = ?" : "";
		$total_price = isset($this->total_price) ? " AND o.total_price = ?" : "";
		
		// query 
		$query = "SELECT
					o.id, o.user_id, o.total_price
				FROM
					" . $this->table_name . " AS o
				WHERE
					1=1" . $id . $user_id . $total_price . "";
		//show query					
		//echo $query;

		// prepare query statement
		$stmt = $this->conn->prepare($query);
		// bind parameters in while statement
		$index = 1;
		if ($this->id != null) {
			$stmt->bindParam($index, $this->id);
			$index++;
		}
		if ($this->user_id != null) {
			$stmt->bindParam($index, $this->user_id);
			$index++;
		}
		if ($this->total_price != null) {
			$stmt->bindParam($index, $this->total_price);
		}

		// execute query
		$stmt->execute();

		return $stmt;
	}

	function insert() {
		// query to insert record and create new order
		$query = "INSERT INTO
				" . $this->table_name . " (user_id, total_price)
			VALUES
				(:user_id, :total_price);";

		// prepare query
		$stmt = $this->conn->prepare($query);

		// bind values
		$stmt->bindParam(":user_id", $this->user_id);
		$stmt->bindParam(":total_price", $this->total_price);

		// execute query and get id of the order
		$stmt->execute();

		return $this->conn->lastInsertId();
	}

	function add_price() {
		$query = "UPDATE ". $this->table_name . " SET total_price = total_price + :price WHERE user_id = :user_id";
		// prepare query
		$stmt = $this->conn->prepare($query);

		// bind values
		$stmt->bindParam(":price", $this->price);
		$stmt->bindParam(":user_id", $this->user_id);

		// execute query
		$stmt->execute();
	}
}
?>