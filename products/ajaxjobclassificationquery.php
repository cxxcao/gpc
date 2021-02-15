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
$jur = $_SESSION[_JURISDICTION];
$jurArr = explode(",", $jur);

if (!$q)
{
      $sql = "SELECT * FROM `role_classifications`";
}
else
{
      $sql = "SELECT * FROM `role_classifications` where role  LIKE '%$q%' ";
}


//echo "$sql<BR>";

$res = db_query($sql);
$num = db_numrows($res);
for($i = 0; $i < $num; $i++)
{
   $role = db_result($res, $i, "role");
   $allowance_first = db_result($res, $i, "allowance_first");
   echo "$role|$allowance_first\n";
   //echo "$firstname $lastname|$user_id|$user_name\n";
}

?>

