<?php
session_start();
$home = dirname(__FILE__) . "/../";
$lib = $home ."/lib/";

require_once($home . '/globals.php');
require_once($lib . 'functions.php');
require_once($lib . 'loginfunctions.php');
require_once($lib . 'htmlGenerator.php');
require_once('ordersclass.php');
require_once('productsclass.php');

$loc = _checkIsSet("location_id");
if($loc == "0")
	unset($_SESSION["savestoresession"]);
else
	$_SESSION["savestoresession"] = $loc;

?>

