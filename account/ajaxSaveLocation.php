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
require_once('locationclass.php');

if(!minAccessLevel(_USER_LEVEL))
{
   user_logout();
   header("Location: " . _CUR_HOST. _DIR);
}
//if($action == "add")
{
   if(user_isloggedin())
   {
      if($_SESSION["location"])
         $location = unserialize($_SESSION["location"]);
      else
         $location = new location();
      if($location->save())
      {
         unset($_SESSION["location"]);
         echo '{"success":true,"msg":"Location details saved."}';
      }
      else
         echo '{"success":false,"msg":"Failed to saved."}';
   }
   else
      echo "Please login";
}


?>

