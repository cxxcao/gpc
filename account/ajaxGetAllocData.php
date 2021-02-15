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
      $empStatus = _checkIsSet("empstatus");
      $role_id = _checkIsSet("role_id");
      
      $query = "select * from role_rules where role_id = $role_id and status = '$empStatus'";
      $res = db_query($query);
      $num = db_numrows($res);
      
      if($num > 0)
      {
         $upper = db_result($res, 0, "upper");
         $flame = db_result($res, 0, "flame");         
         $lower = db_result($res, 0, "lower");
         $outer = db_result($res, 0, "outer");
         $headwear = db_result($res, 0, "headwear");     
         $footwear = db_result($res, 0, "footwear");
         $role_name = db_result($res, 0, "role_name");
         
         echo '{"success":true,"msg":"'.$role_name.'/'.$empStatus.' allocation loaded.","flame":"'.$flame.'","outer":"'.$outer.'","upper":"'.$upper.'","lower":"'.$lower.'","headwear":"'.$headwear.'","footwear":"'.$footwear.'"}';
      }
      else
         echo '{"success":false}';


   }
   else
      echo "Please login";
}


?>

