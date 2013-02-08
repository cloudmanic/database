<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php';

// Static version.
Cloudmanic\Database\DB::connection('HOSTNAME HERE', 'USERNAME HERE', 'PASS HERE', 'DATABASE HERE');
Cloudmanic\Database\DB::set_table('Users')->get();
$d = Cloudmanic\Database\DB::get();

echo '<pre>' . print_r($d, TRUE) . '</pre>';

// Instance version
$db = new Cloudmanic\Database\Instance('HOSTNAME HERE', 'USERNAME HERE', 'PASS HERE', 'DATABASE HERE');
$db->set_table('Users')->get();

echo '<pre>' . print_r($d, TRUE) . '</pre>';