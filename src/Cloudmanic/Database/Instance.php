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
	private $_wheres = array();
	private $_wheres_not = array();
	private $_selects = array();

	
	//
	// Set db connection.
	//
	public function __construct($host, $user, $pass, $db)
	{
		$this->_host = $host;
		$this->_user = $user;
		$this->_pass = $pass;
		$this->_db = $db;
		
		try {
			$this->_db_conn = new \PDO("mysql:host=$this->_host;dbname=$this->_db", $this->_user, $this->_pass);
			$this->_db_conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING);
		} catch(\PDOException $e) {
			echo 'ERROR: ' . $e->getMessage();
		}
	}
	
	// ---------------- Setters ----------------- //
	
	//
	// Set selects.
	//
	public function set_select($select)
	{
		$this->_selects[] = $select;
	}
	
	// 
	// Set table.
	//
	public function set_table($table)
	{
		$this->_table = $table;
		return $this;
	}
	
	//
	// Set col.
	//
	public function set_col($key, $val)
	{
		$this->_wheres[$key] = $val;
		return $this;
	}
	
	//
	// Set not col.
	//
	public function set_not_col($key, $val)
	{
		$this->_wheres_not[$key] = $val;
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
	// Get by id (SELECT).
	//
	public function get_by_id($id)
	{
		static::set_col($this->_table . 'Id', $id);
		$data = static::get();
		return (isset($data[0])) ? $data[0] : false;
	}

	//
	// Get.... (SELECT).
	//
	public function get()
	{
		// Set the selects.
		if(count($this->_selects) > 0)
		{
			$selects = implode(',', $this->_selects);
		} else
		{
			$selects = '*';
		}
	
		$this->_query = "SELECT $selects FROM $this->_table";
		
		// Add Where to the query.		
		if(count($this->_wheres) || count($this->_wheres_not))
		{
			$this->_query .= ' WHERE ';
		}
		
		// Add Wheres.
		if(count($this->_wheres))
		{
			foreach($this->_wheres AS $key => $row)
			{
				$this->_query .= "$key = '$row' ";
			}
		}
		
		// Add Where nots.
		if(count($this->_wheres_not))
		{
			foreach($this->_wheres_not AS $key => $row)
			{
				$this->_query .= "$key != '$row' ";
			}
		}
		
		// Set query log.
		$this->_query_log[] = $this->_query;

		// Make query.
		if($stmt = $this->_db_conn->query($this->_query))
		{
			$data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
		} else
		{
			$data = array();
		}
		
		// Clear.
		$this->clear();
		
		return $data;
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
		
		// Clear.
		$this->clear();
		
		return ($id) ? $id : 0;
	}
	
	//
	// Delete....
	//
	public function delete()
	{
		$this->_query = "DELETE FROM $this->_table";

		// Add Where to the query.		
		if(count($this->_wheres) || count($this->_wheres_not))
		{
			$this->_query .= ' WHERE ';
		}
		
		// Add Wheres.
		if(count($this->_wheres))
		{
			foreach($this->_wheres AS $key => $row)
			{
				$this->_query .= "$key = '$row' ";
			}
		}
		
		// Add Where nots.
		if(count($this->_wheres_not))
		{
			foreach($this->_wheres_not AS $key => $row)
			{
				$this->_query .= "$key != '$row' ";
			}
		}
		
		// Set query log.
		$this->_query_log[] = $this->_query;

		// Make query.
		try {
			$this->_db_conn->exec($this->_query);
		} catch(\PDOException $e) {
			echo 'ERROR: ' . $e->getMessage();
		}
		
		// Clear.
		$this->clear();
	}
	
	//
	// Clear query variables.
	//
	public function clear()
	{
		$this->_wheres = array();
		$this->_wheres_not = array();
		$this->_selects = array();
	}
	
	// ---------------- Helper Queries ---------------- //	
	
	//
	// Return the database connection.
	//
	public function get_connection()
	{
		return $this->_db_conn;
	}
	
	//
	// Make a raw query.
	//
	public function query($sql)
	{
		return $this->_db_conn->query($sql);
	}
	
	//
	// Get list of all tables in the database.
	//
	public function list_tables()
	{	
		$tables = array();
		$stmt = $this->_db_conn->query("SHOW TABLES");
		$all = $stmt->fetchAll(\PDO::FETCH_ASSOC);
		foreach($all AS $key => $row)
		{
			$tables[] = current($row);
		}
		return $tables;
	}

}

/* End File */