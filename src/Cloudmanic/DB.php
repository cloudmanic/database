<?php 
//
// Company: Cloudmanic Labs, LLC
// Website: http://cloudmanic.com
// Date: 1/07/2013
//

namespace Cloudmanic;

class DB
{
	private static $i = null;
	private static $_host = 'localhost';
	private static $_user = '';
	private static $_pass = '';
	private static $_db = '';
	
	//
	// Set the connection information.
	//
	public static function connection($host, $user, $pass, $db)
	{
		self::$_host = $host;
		self::$_user = $user;
		self::$_pass = $pass;
		self::$_db = $db;
	}
	
	//
	// Instance ...
	//
	public static function instance()
	{
		if(! self::$i)
		{
			self::$i = new Database(self::$_host, self::$_user, self::$_pass, self::$_db);
		}
        
		return self::$i;
	}

	//
	// Call static......
	//
	public static function __callStatic($method, $args)
	{
		return call_user_func_array(array(self::instance(), $method), $args);
	}
}

/* End File */