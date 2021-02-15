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
require_once('staffclass.php');

if(!minAccessLevel(_BRANCH_LEVEL))
{
   user_logout();
   header("Location: " . _CUR_HOST. _DIR);
}
//if($action == "add")
{
   if(user_isloggedin())
   {
      if($_SESSION["staff"])
         $staff = unserialize($_SESSION["staff"]);
      else
         $staff = new staff();
      if($staff->save())
      {
         unset($_SESSION["staff"]);
         echo '{"success":true,"msg":"Staff details saved."}';
      }
      else
      {
         $msg = "Failed to saved.";
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

