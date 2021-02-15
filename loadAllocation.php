<?php
   error_reporting(E_ALL);
   ini_set("display_errors", 1);
session_start();
$home = dirname(__FILE__);
$lib = $home ."/lib/";

echo "home: $home<BR>";
echo "lib: $lib<BR>";


require_once($lib . 'database.php');
require_once($lib . 'functions.php');
require_once($lib . 'dbfunctions.php');
require_once($home . '/globals.php');


$action = _checkIsSet("action");

if($action == "submit")
{
   echo "<table><tr><td>userid</td><td>order_id</td><td>num rules</td><tr>";

   $hiredate = "2014-11-28";
   $hiredatePlusMonths = strtotime(date("Y-m-d", strtotime($hiredate)) . "+1 year");
   $endTime = date('Y-m-d', $hiredatePlusMonths);
   
   //$query = "select * from allowance where end = '2013-03-20'";
   $query = "select * from login";
   $res = db_query($query);
   $num = db_numrows($res);

   if($num > 0)
   {
   
      for($i = 0; $i < $num; $i++)
      {
      	$user_id = db_result($res, $i, "user_id");
      	$role_id = db_result($res, $i, "role_id");
      	$employment_status = db_result($res, $i, "employment_status");
      	$firstname = db_result($res, $i, "firstname");
      	$lastname = db_result($res, $i, "lastname");
      	$username = db_result($res, $i, "user_name");
      	$fullname = "$firstname $lastname";
      	
      	$rule_type = 1;
      	
      	$jacket = 0;
      	$upper = 0;
      	$lower = 0;
      	$pharmacyJkt = 0;
      	$knit1 = 0;
      	$softshell = 0;
      	$belt = 1; //all staff allowed belt
      	//$acc = 0;
      	//$polo = 0;
      	
      	if($role_id == 7) //pharmacist
      	{
      		if($employment_status == "Full-time Var Hrs" || $employment_status == "Full-time Set Hrs")
      		{
      			$jacket = 0;
      			$upper = 0;
      			$lower = 3;
      			$pharmacyJkt = 2;
      		}
      		else if($employment_status == "Part-time Var Hrs" || $employment_status == "Part-time Set Hrs")
      		{
      			$jacket = 0;
      			$upper = 0;
      			$lower = 2;
      			$pharmacyJkt = 1;
      		}
      	
      		else if($employment_status == "Casual")
      		{
      			$jacket = 0;
      			$upper = 0;
      			$lower = 1;
      			$pharmacyJkt = 1;
      		}
      	}
      	else if($role_id == 8) //pharmacist assistant
      	{
      		if($employment_status == "Full-time Var Hrs" || $employment_status == "Full-time Set Hrs")
      		{
      			$jacket = 1;
      			$upper = 4;
      			$lower = 3;
      			$pharmacyJkt = 0;
      		}
      		else if($employment_status == "Part-time Var Hrs" || $employment_status == "Part-time Set Hrs")
      		{
      			$jacket = 1;
      			$upper = 2;
      			$lower = 2;
      			$pharmacyJkt = 0;
      		}
      		 
      		else if($employment_status == "Casual")
      		{
      			$jacket = 1;
      			$upper = 1;
      			$lower = 1;
      			$pharmacyJkt = 0;
      		}
      	}
      	else if($role_id == 9 || $role_id == 10 || $role_id == 11) //technician
      	{
      		if($employment_status == "Full-time Var Hrs" || $employment_status == "Full-time Set Hrs")
      		{
      			$knit1 = 1;
      			$upper = 4;
      			$lower = 3;
      			$pharmacyJkt = 0;
      		}
      		else if($employment_status == "Part-time Var Hrs" || $employment_status == "Part-time Set Hrs")
      		{
      			$knit1 = 1;
      			$upper = 2;
      			$lower = 2;
      			$pharmacyJkt = 0;
      		}
      		 
      		else if($employment_status == "Casual")
      		{
      			$knit1 = 1;
      			$upper = 1;
      			$lower = 1;
      			$pharmacyJkt = 0;
      		}
      	}
      	else if($role_id == 12)
      	{
      		if($employment_status == "Full-time Var Hrs" || $employment_status == "Full-time Set Hrs")
      		{
      			$softshell = 1;
      			$upper = 4;
      			$lower = 3;
      			$pharmacyJkt = 0;
      		}
      		else if($employment_status == "Part-time Var Hrs" || $employment_status == "Part-time Set Hrs")
      		{
      			$softshell = 1;
      			$upper = 2;
      			$lower = 2;
      			$pharmacyJkt = 0;
      		}
      		 
      		else if($employment_status == "Casual")
      		{
      			$softshell = 1;
      			$upper = 1;
      			$lower = 1;
      			$pharmacyJkt = 0;
      		}
      	}
      	 
      	 
      	$jacket_type = _JACKET_TYPE;
      	$upper_type = _UPPER_TYPE;
      	$lower_type = _LOWER_TYPE;
      	$pharmacy_type = _PHJK_TYPE;
      	$knit_type1 = _KNIT_TYPE;
      	$softshell_type = _SOFTSHELL_TYPE;
      	//$belt_type = _BELT_TYPE;
      	$acc_type = _ACC_TYPE;
      	//$polo_type = _POLO_TYPE;
      	$query = "insert into rules (user_id, full_name, user_name, cat_type, max_allowed, rule_type, start, end) values
      	($user_id, \"$fullname\", \"$username\", $jacket_type, $jacket, $rule_type, '$hiredate','$endTime'),
      	($user_id, \"$fullname\", \"$username\", $upper_type, $upper, $rule_type, '$hiredate','$endTime'),
      	($user_id, \"$fullname\", \"$username\", $lower_type, $lower, $rule_type, '$hiredate','$endTime'),
      	($user_id, \"$fullname\", \"$username\", $acc_type, $belt, $rule_type, '$hiredate','$endTime'),
      	($user_id, \"$fullname\", \"$username\", $knit_type1, $knit1, $rule_type, '$hiredate','$endTime'),
      	($user_id, \"$fullname\", \"$username\", $softshell_type, $softshell, $rule_type, '$hiredate','$endTime'),
      	($user_id, \"$fullname\", \"$username\", $pharmacy_type, $pharmacyJkt, $rule_type, '$hiredate','$endTime')";
      	 
      		   	echo "$query<BR>";
      db_query($query);      	
      	
      }
   }

   echo "</table>";
}

?>

<html>
<body>

<form enctype="multipart/form-data" name="list" method="post">
<br>
File:<input type="file" size="50" name="file"><br>

<input type="submit" name="action" value="submit">

</form>


</body>
</html>
