<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php';

// Static version
Cloudmanic\Database\DB::connection('HOSTNAME HERE', 'USERNAME HERE', 'PASS HERE', 'DATABASE HERE');
Cloudmanic\Database\DB::set_table('Users');

$query = array(
	'UsersFirstName' => 'Lady',
	'UsersLastName' => 'Gaga'
);

$id = Cloudmanic\Database\DB::insert($query);

echo '<pre>' . print_r($id, TRUE) . '</pre>';

// Instance version
$db = new Cloudmanic\Database\Instance('HOSTNAME HERE', 'USERNAME HERE', 'PASS HERE', 'DATABASE HERE');
$db->set_table('Users');

$query = array(
	'UsersFirstName' => 'Katie',
	'UsersLastName' => 'Perry'
);

$id = $db->insert($query);

echo '<pre>' . print_r($id, TRUE) . '</pre>';