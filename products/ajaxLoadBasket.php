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

$action = _checkIsSet('action');

if(!$_SESSION['order'])
{
   $orders = new orders();
}
else
{
   $orders = unserialize($_SESSION['order']);
}

if($action == "load")
{
	$numitems = $orders->PopulateBasket($_SESSION[_USER_ID]);
	$_SESSION['order'] = serialize($orders);
	echo '{"success":true,"numitems":"'.$numitems.'"}';
}
else if($action == "delete")
{
	$orders->DeleteBasket($_SESSION[_USER_ID]);
	$_SESSION['order'] = serialize($orders);
	echo '{"success":true,"numitems":"0"}';
}
?>

