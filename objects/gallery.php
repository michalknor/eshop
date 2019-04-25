<?php
class Gallery {

	// database connection and table name
	private $conn;
	private $table_name = "gallery";

	// object properties
	public $id;
	public $img;
	public $products_id;

	// constructor with $db as database connection
	public function __construct($db) {
		$this->conn = $db;
	}

	// read products
	function read(){

		// prepare while statements
		$products_id = isset($this->products_id) ? " AND g.products_id = ?" : "";
		
		// query 
		$query = "SELECT
					g.id, g.img, g.products_id
				FROM
					" . $this->table_name . " AS g
				WHERE
					1=1" . $products_id . "";
		//show query					
		//echo $query;

		// prepare query statement
		$stmt = $this->conn->prepare($query);
		// bind parameters in while statement
		if ($this->products_id != null) {
			$stmt->bindParam(1, $this->products_id);
		}

		// execute query
		$stmt->execute();

		return $stmt;
	}
}
?>