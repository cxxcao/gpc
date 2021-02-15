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
      if($_SESSION["staff"])
         $staff = unserialize($_SESSION["staff"]);
      else
         $staff = new staff();

      $user_id = $staff->user_id;
      
      $loc_id = _checkIsSet("location_id");
      $role_id = _checkIsSet("role_id");
      $crange = _checkIsSet("range");
      $daysworked = _checkIsSet("daysworked");
      
      $loc = new location();
      $loc->LoadLocationId($loc_id);
      $loc_bu = $loc->branch_id; //business_unit for below query
      
      $query = "select * from rulesAlloc where business_unit = '$loc_bu' and daysworked = $daysworked and gender = $crange";
//      echo "$query<BR>";
      $res = db_query($query);
      $num = db_numrows($res);      

      $rulesMaxArr = array();
      
      for($i = 0; $i < $num; $i++)
      {
      	$cat_id = db_result($res, $i, "category");
      	$expiry = db_result($res, $i, "end");
      	$maxallowed = db_result($res, $i, "maxallowed");
      	if(!$maxallowed)
      		$maxallowed = 0;
      	$rulesMaxArr[$cat_id] = $maxallowed;
      }
      
      $jacket = $rulesMaxArr[_JACKET_TYPE];
      $upper = $rulesMaxArr[_POLO_TYPE];
      $lower = $rulesMaxArr[_LOWER_TYPE];
      $knit = $rulesMaxArr[_KNIT_TYPE];//outer
      $belt = $rulesMaxArr[_BELT_TYPE];
      $acc = $rulesMaxArr[_ACC_TYPE];
      
      if(!$jacket)
      	$jacket = 0;
      if(!$upper)
      	$upper = 0;
      if(!$lower)
      	$lower = 0;
      if(!$knit)
      	$knit = 0;
      if(!$belt)
      	$belt = 0;
      if(!$acc)
      	$acc = 0;
      
      echo '{"success":true,"a":"'.$jacket.'","b":"'.$upper.'","c":"'.$lower.'","d":"'.$knit.'","e":"'.$belt.'","f":"'.$acc.'"}';


   }
   else
      echo "Please login";
}


?>

