<?php
error_reporting(0);
session_start();

define('ROOT', dirname(__FILE__));
include ROOT . '/controllers/AutoLoader.class.php';
$autoloader = new AutoLoader();

$mod = trim($_REQUEST['mod']);
$act = trim($_REQUEST['act']);

$mod = strtolower($mod);
$class = ucfirst($mod);

$c  = new $class();
$data = $c->$act();
echo json_encode($data);
