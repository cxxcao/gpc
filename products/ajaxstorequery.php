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
$uid = $_SESSION[_USER_ID];
//if (!$q) return;

if(minAccessLevel(_ADMIN_LEVEL))
	$sql = "select * from location where sname LIKE '%$q%' order by sname";
else 
{
	$sql = "select * from location l, login l1 where sname LIKE '%$q%' and l.location_id = l1.location_id and l1.user_id = $uid group by l.location_id order by sname ";
}

$res = db_query($sql);
$num = db_numrows($res);
for($i = 0; $i < $num; $i++)
{
   $sname = db_result($res, $i, "sname");
   $location_id = db_result($res, $i, "location_id");
   $address = db_result($res, $i, "address");
   $suburb = db_result($res, $i, "suburb");
   $state = db_result($res, $i, "state");
   $postcode = db_result($res, $i, "postcode");
   $phone = db_result($res, $i, "phone");
   $fax = db_result($res, $i, "fax");
   $email = db_result($res, $i, "email");
   $country = db_result($res, $i, "country");
   $costcentre = db_result($res, $i, "branch_id");
   echo "$sname|$location_id|$address|$suburb|$state|$postcode|$phone|$fax|$email|$country|$costcentre\n";
}

?>

