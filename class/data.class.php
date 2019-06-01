<?php
// use database class

// check if called from an allowed page
if (!defined('ESRC'))
{
	echo "Do not call the script direct!";
	exit ( 1 );
}

require_once '../class/db.class.php';

/**
 * Class to check (and later manage) permissions of a user. 
 *
 * Current implementation bases on a singl table.
 * 
 * Implementation should be improved if a user permissions handling UI exists.
 */
class Data {
	var $db = null;
	
	public function __construct($database = NULL)
	{
		if (isset($database))
		{
			$this->db = $database;
		}
		else
		{
			// create a new database class instace
			$this->connectDatabase ();
		}
	}
	
	/**
	 * Creates a new DB connection.
	 */
	private function connectDatabase() {
		$this->db = new Database ();
	}
	
	/**
	 * Save a key, value pair to the database with the current timestamp.
	 * @param unknown $key
	 * @param unknown $value
	 */
	public function saveData($key, $value)
	{
		if (!isset($key) || !isset($value))
		{
			print_r('Something undefined: key:'.isset($key) .' or value:'.isset($value));
			return;
		}
		
		// sql to use to save data
		$sql = 'insert into datavalues(name, data) values(:name, :data)';
		// create the query
		$this->db->query ( $sql );
		// bind the data to save
		$this->db->bind(':name', $key);
		$this->db->bind(':data', $value);
		
		// execute the query and get result
		$this->db->execute();
		
		// close the db cursor
		$this->db->closeQuery();
	}
	
	public function getLatestValue($key, $default = '')
	{
		$sql = 'select data from datavalues where name = :name order by ID desc limit 1';
		
		// create the query
		$this->db->query ( $sql );
		// bind the data to save
		$this->db->bind(':name', $key);
		
		$this->db->execute();
		
		$row = $this->db->single();

		if (isset($row))
		{
			$result =$row['data'];
		}
		
		if (!isset($result))
		{
			$result = $default;
		}
		return $result;
	}
}

?>
