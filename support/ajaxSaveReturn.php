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
require_once('warrantyclass.php');

//if(!minAccessLevel(_ADMIN_LEVEL))
//{
//   user_logout();
//   header("Location: " . _CUR_HOST. _DIR);
//}
//if($action == "add")
{
   if(user_isloggedin())
   {
      $warranty = new warranty();
      if($warranty->saveWarranty())
         echo '{"success":true,"msg":"Returns Saved.","redirect":"'. _CUR_HOST. _DIR .'support/listreturns.php"}';
      else
      {
         $msg = "Save failed.";
         if($_SESSION['msg'])
         {
            $msg = $_SESSION['msg'];
            unset($_SESSION['msg']);
         }

         echo '{"success":false,"msg":"'.$msg.'"}';
      }
   }
   else
      echo "Please login";
}


?>

