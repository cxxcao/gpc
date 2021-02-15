<?php
session_start();
header('Set-Cookie: ' . session_name() . '=' . session_id() . '; SameSite=None; Secure');
$home = dirname(__FILE__) . "/";
$lib = $home ."/lib/";

require_once($lib . 'database.php');
require_once($home . '/globals.php');
require_once($lib . 'functions.php');
require_once($lib . 'loginfunctions.php');

//http://www.designstoyou.com.au/national/autologin.php?date=Wed%20Feb%2022%2017:06:53%20EST%202012&key=f6aeee6ceb4d87003a95bd4488404edf
$key = _checkIsSet("key");
$user_id = _checkIsSet("user_name");
$field = _checkIsSet("field");
$enc = urldecode(_checkIsSet("date"));

$pass = md5($enc . "next3cDty768");

//login
if($pass == $key)
{
   if(user_login($user_id,"leica", $field))
       	echo "<meta http-equiv=\"refresh\" content=\"0; url="._DIR."index2.php\">";
//      header("Location: " . _CUR_HOST . _DIR . "products/listorders.php");
   else
      header("Location: " . _CUR_HOST . _DIR . "logout.php");
}
else
   header("Location: " . _CUR_HOST . _DIR . "logout.php");

//failed
//header("Location: " . _CUR_HOST . _DIR . "logout.php");

//echo "PASS: $pass<BR>";
//echo "KEYS: $key<BR>";
?>