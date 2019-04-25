<?php
class User {

	// database connection and table name
	private $conn;
	private $table_name = "users";

	// object properties
	public $id;
	public $username;
	public $email;
	public $password;
	public $token;

	// constructor with $db as database connection
	public function __construct($db) {
		$this->conn = $db;
	}

	// login
	function read() {

		// prepare while statement
		$id = isset($this->id) ? " AND u.id = ?" : "";
		$username = isset($this->username) ? " AND u.username = ?" : "";
		$email = isset($this->email) ? " AND u.email = ?" : "";
		$token = isset($this->token) ? " AND u.token = ?" : "";
		
		// query 
		$query = "SELECT
					u.id, u.username, u.email, u.password, u.token
				FROM
					" . $this->table_name . " AS u
				WHERE
					1=1" . $id . $username . $email. $token. "";
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
		if ($this->username != null) {
			$stmt->bindParam($index, $this->username);
			$index++;
		}
		if ($this->email != null) {
			$stmt->bindParam($index, $this->email);
			$index++;
		}
		if ($this->token != null) {
			$stmt->bindParam($index, $this->token);
		}

		// execute query
		$stmt->execute();

		return $stmt;
	}

	function register() {
		$query = "INSERT INTO
				" . $this->table_name . " (username, email, password, token)
			VALUES
				(:username, :email, :password, :token);";

		$stmt = $this->conn->prepare($query);

		// bind values
		$stmt->bindParam(":username", $this->username);
		$stmt->bindParam(":email", $this->email);
		$stmt->bindParam(":password", $this->password);
		$stmt->bindParam(":token", $this->token);

		return $stmt->execute();
	}
}
?>