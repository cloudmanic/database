<?php 
//
// Company: Cloudmanic Labs, LLC
// Website: http://cloudmanic.com
// Date: 1/07/2013
//

namespace Cloudmanic\Database;

class Instance
{
	private $_table = null;
	private $_db_conn = null;
	private $_host = 'localhost';
	private $_user = '';
	private $_pass = '';
	private $_db = '';
	private $_query = '';
	private $_query_log = array();

	
	//
	// Set db connection.
	//
	public function __construct($host, $user, $pass, $db)
	{
		$this->_host = $host;
		$this->_user = $user;
		$this->_pass = $pass;
		$this->_db = $db;
		$this->_db_conn = new \PDO("mysql:host=$this->_host;dbname=$this->_db", $this->_user, $this->_pass);
	}
	
	// ---------------- Setters ----------------- //
	
	// 
	// Set table.
	//
	public function set_table($table)
	{
		$this->_table = $table;
		return $this;
	}
	
	// ---------------- Getters ---------------- //
	
	// 
	// Get last query.
	//
	public function get_last_query()
	{
		return end($this->_query_log);
	}

	// ---------------- Queries ---------------- //	

	//
	// Get.... (SELECT).
	//
	public function get()
	{
		$this->_query = "SELECT * FROM $this->_table";
		$this->_query_log[] = $this->_query;

		if($stmt = $this->_db_conn->query($this->_query))
		{
			return $stmt->fetchAll(\PDO::FETCH_ASSOC);
		} else
		{
			return array();
		}
	}
	
	//
	// Insert.... 
	//
	public function insert($arr = array())
	{
		// Check to make sure we pass in the correct magic. 
		if((! is_array($arr)) || (! count($arr))) 
		{
			return false;
		}

		// Build & Make query.
		$bind = ':' . implode(',:', array_keys($arr));
		$this->_query = 'INSERT INTO ' . $this->_table . '(' . implode(',', array_keys($arr)) . ') ' . 'VALUES (' . $bind . ')';
		$this->_query_log[] = $this->_query;
		$stmt = $this->_db_conn->prepare($this->_query);
		$stmt->execute(array_combine(explode(',', $bind), array_values($arr)));
		
		// Get the insert id.
		$id = $this->_db_conn->lastInsertId();
		
		return ($id) ? $id : 0;
	}
}

/* End File */