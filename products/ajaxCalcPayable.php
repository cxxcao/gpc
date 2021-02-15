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
$action = _checkIsSet("action");
$prod_id = _checkIsSet("prod_id");
$size = _checkIsSet("size");

if(!$_SESSION['order'])
{
   $orders = new orders();
}
else
{
   $orders = unserialize($_SESSION['order']);
}
         $GrandTotal = $orders->GrandTotal();
         $payRemainArr = $orders->CalcPayable();
         $payable = $payRemainArr[0];
         $remaining = $payRemainArr[1];

         if($remaining > 0)
            $payable = 0;
         else
            $payable = $remaining * -1;
         $allowance = $_SESSION[_ALLOWANCE];

//         echo "pay: $payable remain: $remaining<BR>";

         echo '{"success":true,"grandtotal":"'.$GrandTotal.'","payable":"'.$payable.'","linetotal":"'.$linetotal.'","remaining":"'.$remaining.'","allowance":"'.$allowance.'"}';

?>
