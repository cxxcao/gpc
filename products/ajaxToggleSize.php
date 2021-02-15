<?php
session_start();
   error_reporting(E_ALL);
   ini_set("display_errors", 1);
$home = dirname(__FILE__) . "/../";
$lib = $home ."/lib/";

require_once($home . '/globals.php');
require_once($lib . 'functions.php');
require_once($lib . 'loginfunctions.php');
require_once($lib . 'htmlGenerator.php');
require_once('ordersclass.php');
require_once('productsclass.php');

if(!$_SESSION['order'])
{
	$orders = new orders();
}
else
{
	$orders = unserialize($_SESSION['order']);
}

if(!minAccessLevel(_ADMIN_LEVEL))
{
   user_logout();
   header("Location: " . _CUR_HOST. _DIR);
}
//if($action == "add")
{
   if(user_isloggedin())
   {
      $staff = new staff();
      $pid = _checkIsSet("prod_id");
      $size = _checkIsSet("size");
      $oid = _checkIsSet("order_id");
      
      /* this will update all products with the same size in the same order*/
      if($orders->toggleSize($pid, $size, $oid))
         echo '{"success":true,"msg":"Update Successful"}';
      else
         echo '{"success":false,"msg":"Update Failed."}';
   }
   else
      echo "Please login";
}


?>

