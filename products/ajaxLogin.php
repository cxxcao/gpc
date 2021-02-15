<?php
session_start();

$home = dirname(__FILE__) . "/../";
$lib = $home ."/lib/";

require_once($lib . 'database.php');
require_once($home . '/globals.php');
require_once($lib . 'functions.php');
require_once($lib . 'loginfunctions.php');

$userid = _checkIsSet("userid");
$password = _checkIsSet("password");
$isValidate = false;

if(!$userid || !$password)
{
   echo '{"success":false,"msg":"User ID, Password required"}';    // RETURN ARRAY WITH ERROR
   return;
}
else
{
   if(user_login($userid,$password, "user_name"))
   {
      $isValidate = true;
      $username = $_SESSION[_USER_NAME];
      $userid = $_SESSION[_USER_ID];
      $locationid = $_SESSION[_LOCATION_ID];
      $firstname = $_SESSION[_FIRST_NAME];
      $lastname = $_SESSION[_LAST_NAME];
      $email = $_SESSION[_EMAIL];
      $fullname = "$firstname $lastname";
		$daysworked = $_SESSION["daysworked"];
		$branch_id = $_SESSION["branch_id"];
		$rolename = $_SESSION["rolename"];
      $role_id =  $_SESSION["ROLE_ID"]; 
      
      $query = "select * from location where location_id = $locationid";
      $res = db_query($query);
      $locationname = db_result($res, 0, "sname");
      $business_name = db_result($res, 0, "business_name");
      $address = db_result($res, 0, "address");
      $suburb = db_result($res, 0, "suburb");
      $state = db_result($res, 0, "state");
      $postcode = db_result($res, 0, "postcode");
      $successMsg = '"roleid":"'.$role_id.'","costcentre":"'.$branch_id.'","daysworked":"'.$daysworked.'","email":"'.$email.'","username":"'.$username.'","userid":"'.$userid.'","locationid":"'.$locationid.'","locationname":"'.$business_name.'","fullname":"'.$fullname.'","address":"'.$address.'","state":"'.$state.'","postcode":"'.$postcode.'","suburb":"'.$suburb.'"';
   }
   else
   {
      $isValidate = false;
      $errMsg = $_SESSION['msg'];
   }
}
unset($_SESSION['msg']);

/* THIS NEED TO BE IN YOUR FILE NO MATTER WHAT */
if($isValidate == true)
{
   echo '{"success":true,'.$successMsg.'}';
	//echo "true";
//   echo "";
//header("Location:orderregister.php");
}else{
	echo '{"success":false,"msg":"'.$errMsg.'"}';		// RETURN ARRAY WITH ERROR
}
?>