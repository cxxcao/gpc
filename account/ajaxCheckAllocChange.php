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

      $user_id = $staff->user_id;
      $start = _checkIsSet("start");
      $end = _checkIsSet("end");
      $idx = _checkIsSet("idx");
      $new_alloc = _checkIsSet("new_alloc");
      $cat_type = _checkIsSet("cat_type");
      
      if(!$staff->allocationUsed($user_id, $start, $end, $idx, $new_alloc, $cat_type))
      {
	   	$staff->loadRules($user_id);
	
	   	if($idx < 6)
	   		$rule_type = 1;
	   	else 
	   	{
	   		$multi = ($idx - $idx%5)/5;
	   		if($multi > 0)
	   			$rule_type += $multi;
	   	}
	   	
			$rulesKey = $cat_type . "_" . $rule_type;
      	$curRule = $staff->rulesArr[$rulesKey];
      	$cat_id = $curRule->cat_type;
      	$alloc = $curRule->max_allowed;
      	$start = $curRule->start;
      	$end = $curRule->end;
      	
         echo '{"success":false,"msg":"This allocation has already been used on an order and cannot be changed.","cat_id":"'.$cat_id.'","alloc":"'.$alloc.'","start":"'.$start.'","end":"'.$end.'"}';
      }
      else
         echo '{"success":true}';


   }
   else
      echo "Please login";
}


?>

