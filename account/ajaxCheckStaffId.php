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

if(!minAccessLevel(_USER_LEVEL))
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

      $id = _checkIsSet("user_id");
      if($staff->checkExistingUserID($id))
         echo '{"success":false,"msg":"Employee No already used."}';
      else
         echo '{"success":true}';


   }
   else
      echo "Please login";
}


?>

