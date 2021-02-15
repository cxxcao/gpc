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

$item_id = _checkIsSet("item_id");
$item_size = _checkIsSet("item_size");

   if(user_isloggedin())
   {
      $orders = new orders();
      $qty = $orders->getOnHand($item_id, $item_size);
      $ordered = $orders->getOrdersNotProcessed($item_id, $item_size);
      $qty -= $ordered;
      if($qty < 0)
         $qty = 0;
//echo "ORDERED: $ordered onhand: $qty<BR>";
      echo '{"success":true,"qty":"'.$qty.'"}';
   }
   else
      echo "Please login";

?>

