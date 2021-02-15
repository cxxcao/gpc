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
$emb = _checkIsSet("emb");

if(!$_SESSION['order'])
{
   $orders = new orders();
}
else
{
   $orders = unserialize($_SESSION['order']);
}
//if($action == "add")
{
   if(user_isloggedin())
   {

      if($action == "delline")
      {
         $lineCost = $orders->removeAllItems($prod_id, $size, $emb);

         /*
         $GrandTotal = $orders->GrandTotal();
         $remaining = $orders->CalcRemaining();
         $payable = $orders->CalcPayable();
         $allowance = $_SESSION[_ALLOWANCE];
         if($payable < 0)
            $payable *= -1;
         $_SESSION["order"] = serialize($orders);
         echo '{"success":true,"grandtotal":"'.$GrandTotal.'","payable":"'.$payable.'","linetotal":"'.$linetotal.'","remaining":"'.$remaining.'","allowance":"'.$allowance.'"}';
         */
      }
      else if($action == "updateqty")
      {
         $newqty = _checkIsSet("qty");
         $linetotal = $orders->UpdateQty($prod_id, $size, $newqty, $emb);
         /*
         $remaining = $orders->CalcRemaining();
         $GrandTotal = $orders->GrandTotal();
         $payable = $orders->CalcPayable();
         $allowance = $_SESSION[_ALLOWANCE];
         if($payable < 0)
            $payable *= -1;
         $_SESSION["order"] = serialize($orders);
         echo '{"success":true,"grandtotal":"'.$GrandTotal.'","payable":"'.$payable.'","linetotal":"'.$linetotal.'","remaining":"'.$remaining.'","allowance":"'.$allowance.'"}';
         */
      }
         //$remaining = $orders->CalcRemainingExCart();
         $GrandTotal = $orders->GrandTotal();
         $payRemainArr = $orders->CalcPayable();
         $payable = $payRemainArr[0];
         $remaining = $payRemainArr[1];

         if($remaining > 0)
            $payable = 0;
         else
         {
            $payable = $remaining * -1;
            $remaining = 0;
         }
         $allowance = $_SESSION[_ALLOWANCE];
         $_SESSION["order"] = serialize($orders);
         echo '{"success":true,"grandtotal":"'.$GrandTotal.'","payable":"'.$payable.'","linetotal":"'.$linetotal.'","remaining":"'.$remaining.'","allowance":"'.$allowance.'"}';
   }
   else
    echo "Please login";
}


?>

