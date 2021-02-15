<?php
session_start();
$home = dirname(__FILE__) . "/../";
$lib = $home ."/lib/";

require_once($home . '/globals.php');
require_once($lib . 'functions.php');
require_once($lib . 'loginfunctions.php');
require_once($lib . 'htmlGenerator.php');
require_once('ordersclass.php');
require_once('productsclass.php');

$q = strtolower($_REQUEST["q"]);
$lid = $_SESSION["savestoresession"];

$jur = $_SESSION[_JURISDICTION];
$jurArr = explode(",", $jur);
$branchLocation_id = $_SESSION[_LOCATION_ID];
$branch_id = $_SESSION["branch_id"];
$uid = $_SESSION[_USER_ID];

// if(!$lid)
// 	$lid = $_SESSION[_LOCATION_ID];
// echo "$lid<BR>";
if (!$q)
{
   if(minAccessLevel(_ADMIN_LEVEL))
   {
       $sql = "SELECT * FROM `login` where concat_ws(' ', firstname, lastname) LIKE '%$q%' and status = 'ACTIVE' ";
      if($lid)
      {
      	$sql = "SELECT * FROM `login` l, `location` l1 where concat_ws(' ', firstname, lastname) LIKE '%$q%' and l.location_id = l1.location_id and l.status = 'ACTIVE' ";      	
      }
   }
   else if(minAccessLevel(_BRANCH_LEVEL))
   {
      $sql = "SELECT * FROM `login` l, `location` l1 where l.user_id != '' and (l1.location_id = $branchLocation_id) and l.location_id = l1.location_id and l.status = 'ACTIVE' ";
   }
}
else
{
   if(minAccessLevel(_ADMIN_LEVEL))
   {
      $sql = "SELECT * FROM `login` where concat_ws(' ', firstname, lastname) LIKE '%$q%' and status = 'ACTIVE' ";
      if($lid)
      {
      	$sql = "SELECT * FROM `login` l, `location` l1 where concat_ws(' ', firstname, lastname) LIKE '%$q%' and l.location_id = l1.location_id and l.status = 'ACTIVE' ";
      }
   }
   else if(minAccessLevel(_BRANCH_LEVEL))
   {
      $sql = "SELECT * FROM `login` l, `location` l1 where  concat_ws(' ', firstname, lastname) LIKE '%$q%' and l.user_id != '' and (l1.location_id = $branchLocation_id) and l.location_id = l1.location_id and l.status = 'ACTIVE' ";
   }
}
if($lid)
	$sql .= " and l1.location_id = $lid";

$sql .= " order by concat_ws(' ', firstname, lastname)";


// echo "$sql<BR>\n";

$res = db_query($sql);
$num = db_numrows($res);
for($i = 0; $i < $num; $i++)
{
   $user_name = db_result($res, $i, "user_name");
   $user_id = db_result($res, $i, "user_id");
   $firstname = db_result($res, $i, "firstname");
   $lastname = db_result($res, $i, "lastname");
   $costcentre = db_result($res, $i, "branch_id");
   $role_id = db_result($res, $i, "role_id");
   
   
   echo "$firstname $lastname - $user_name |$user_id|$user_name\n";
}

?>

