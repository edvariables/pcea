<?php

class DbConnection extends PDO {
	function __construct() {
		parent::__construct('mysql:host='.db_server.';dbname='.db_name, db_user, db_password);
		$this->exec("SET CHARACTER SET utf8");
	}
	
	public function select($sql, $parameters = null){
		return new DbRows($sql, $parameters, $this);
	}
}

class DbRows extends PDO {
	function __construct($sql, $parameters = null, $db=null){


		if (is_a($db,"DbConnection")) {
			$this->db = $db;
		}
	
		if(!isset($this -> db))
			$this -> db = new DbConnection();
			
		$this -> query = $this -> db -> prepare($sql);
		$this -> execute($parameters);
	}
	function execute($parameters = null){
		// if(!$parameters)
			// $parameters = 
		return $this -> query -> execute($parameters);
	}
	function fetch(){
		return $this -> query -> fetch(PDO::FETCH_ASSOC);
	}
}
?>