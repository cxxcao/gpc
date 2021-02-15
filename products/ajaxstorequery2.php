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

$id = _checkIsSet("id");
//if (!$q) return;

$sql = "select * from location where location_id = $id order by sname";
//echo "$sql<BR>";
$res = db_query($sql);
$num = db_numrows($res);
//$json = json_encode($res);
$rows = array();
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

   if($_SESSION["CORPORATE"] == true)
   {
      $phone = "";
   }

   $tmpArr = array(
   "sname" => $sname,
   "location_id" => $location_id,
   "address" => $address,
   "suburb" => $suburb,
   "state" => $state,
   "postcode" => $postcode,
   "phone" => $phone,
   "fax" => $fax,
   "email" => $email);

   ///array_push($rows, $tmpArr);
}
echo json_encode($tmpArr);

?>

