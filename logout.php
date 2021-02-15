<?php
session_start();
$home = dirname(__FILE__);
$lib = $home ."/lib/";

require_once($home . '/globals.php');
require_once($lib . 'functions.php');
require_once($lib . 'loginfunctions.php');
require_once($lib . 'htmlGenerator.php');

user_logout();
// header("location: " . _CUR_HOST . _DIR . "index2.php");
	echo "<meta http-equiv=\"refresh\" content=\"0; url="._DIR."index2.php\">";

?>
