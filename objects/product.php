<?php
class Product {

	// database connection and table name
	private $conn;
	private $table_name = "products";

	// object properties
	public $id;
	public $name;
	public $price;
	public $description;
	public $img;
	public $order;

	// constructor with $db as database connection
	public function __construct($db) {
		$this->conn = $db;
	}

	// read products
	function read(){

		// prepare while statements
		$id = isset($this->id) ? " AND p.id = ?" : "";
		$name = isset($this->name) ? " AND p.name = ?" : "";
		$price = isset($this->price) ? " AND p.price = ?" : "";
		$description = isset($this->description) ? " AND p.description = ?" : "";
		$img = isset($this->img) ? " AND p.img = ?" : "";
		$order = isset($this->order) ? " ORDER BY p.price " . $this->order : "";
		
		// query 
		$query = "SELECT
					p.id, p.name, p.price, p.description, p.img
				FROM
					" . $this->table_name . " AS p
				WHERE
					1=1" . $id . $name . $price . $description . $img . $a . $order;
		//show query					
		//echo $query;

		// prepare query statement
		$stmt = $this->conn->prepare($query);

		// bind parameters in while statement
		$index = 1;
		if ($this->id != null) {
			$stmt->bindParam(1, $this->id);
			$index++;
		}
		if ($this->name != null) {
			$stmt->bindParam($index, $this->name);
			$index++;
		}
		if ($this->price != null) {
			$stmt->bindParam($index, $this->price);
			$index++;
		}
		if ($this->description != null) {
			$stmt->bindParam($index, $this->description);
			$index++;
		}
		if ($this->img != null) {
			$stmt->bindParam($index, $this->img);
		}

		// execute query
		$stmt->execute();

		return $stmt;
	}
}
?>