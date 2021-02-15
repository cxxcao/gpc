<?php
// session_start();
// //    error_reporting(0);
// //    ini_set("display_errors", 0);
$home = dirname(__FILE__);
// $lib = $home ."/lib/";

require_once($home . '/globals.php');
// require_once($lib . 'functions.php');
// require_once($lib . 'loginfunctions.php');
// require_once($lib . 'htmlGenerator.php');
header("Location: " . _CUR_HOST . _DIR . "index2.php");
?>