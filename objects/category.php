<?php class Category{
	
	private $conn;
	private $tablename = 'categories';

	public function __construct($db){
			$this->conn = $db;
	}

	// used to read category name and ID for category dropdown
	function read_categories(){
		$query = "SELECT id,name from ".$this->tablename." Order by name";
		$result = $this->conn->prepare($query);
		$result->execute();

		return $result;

	}

	// used to read category name by its ID
	function readName(){
		$query = "SELECT name from ".$this->tablename." WHERE id = ? limit 0,1";
		$result = $this->conn->prepare($query);
		$result->bindParam(1,$this->id);
		$result->execute();

		$row = $result->fetch(PDO::FETCH_ASSOC);
		$this->name = $row['name'];
	}

	}
?>