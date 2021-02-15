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
//if($action == "add")
{
   if(user_isloggedin())
   {
      if($orders->saveOrder())
      {
         $orders = new orders();
         $_SESSION["order"] = serialize($orders);
         echo '{"success":true,"msg":"Order Submitted."}';
      }
      else
      {
         $msg = "Submit Failed";
         if($_SESSION['msg'] )
         {
            $msg = $_SESSION['msg'] ;
            unset($_SESSION['msg'] );
         }
         echo '{"success":false,"msg":"'.$msg.'"}';
      }
   }
   else
    echo "Please login";
}


?>

