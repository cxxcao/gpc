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

$query = "select * from login";
$res = db_query($query);
$num = db_numrows($res);

if($num > 0)
{
   for($i = 0; $i < $num; $i++)
   {
      $user_id = db_result($res, $i, "user_id");
      $query = "update login set user_name = '$user_id' where user_id = $user_id";
      db_query($query);

   }
}

?>
