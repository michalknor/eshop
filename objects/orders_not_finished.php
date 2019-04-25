<?php
class Order_not_finished {

	// database connection and table name
	private $conn;
	private $table_name = "orders_not_finished";

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
		$id = isset($this->id) ? " AND onf.id = ?" : "";
		$user_id = isset($this->user_id) ? " AND onf.user_id = ?" : "";
		$total_price = isset($this->total_price) ? " AND onf.total_price = ?" : "";
		
		// query 
		$query = "SELECT
					onf.id, onf.user_id, onf.total_price
				FROM
					" . $this->table_name . " AS onf
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
				(:user_id, 0);";

		// prepare query
		$stmt = $this->conn->prepare($query);
		// sanitize
		$this->user_id = htmlspecialchars(strip_tags($this->user_id));

		// bind values
		$stmt->bindParam(":user_id", $this->user_id);

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