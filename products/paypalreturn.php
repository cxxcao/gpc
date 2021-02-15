<?php
session_start();
   error_reporting(E_ALL);
   ini_set("display_errors", 1);
$home = dirname(__FILE__) . "/../";
$lib = $home ."/lib/";

require_once($home . '/globals.php');
require_once($lib . 'functions.php');
require_once($lib . 'loginfunctions.php');
require_once($lib . 'htmlGenerator.php');
require_once('ordersclass.php');
require_once('productsclass.php');
require_once('../account/locationclass.php');

$action = _checkIsSet("action");

if(!user_isloggedin())
{
   header("Location: " . _CUR_HOST. _DIR);
}


$pp_hostname = "www.paypal.com"; // Change to www.sandbox.paypal.com to test against sandbox
$pp_hostname = "www.sandbox.paypal.com";

// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-synch';

$tx_token = $_GET['tx'];
$auth_token = "5jW-AwFuhAXa-2MGfky97EUKYbm7J4hR7_mo-HLjteTt59KG0SqklqwTdhK";
$req .= "&tx=$tx_token&at=$auth_token";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://$pp_hostname/cgi-bin/webscr");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
//set cacert.pem verisign certificate path in curl using 'CURLOPT_CAINFO' field here,
//if your server does not bundled with default verisign certificates.
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Host: $pp_hostname"));
$res = curl_exec($ch);
curl_close($ch);
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="utf-8">
   <title>Designs To You - DTYLink Online Ordering</title>
   <?php include('../_inc/js_css.php');?>
</head>
<body>

   <div id="topHeader" class="cAlign">

      <!-- Logo -->
      <a href="<?php  echo _CUR_HOST . _DIR ; ?>index.php" id="logo"><img src="<?php  echo _CUR_HOST . _DIR ; ?>_img/dtylink_logo.png" alt="DTY Link - Online Ordering System" /></a>
      <div style="float:right">
      <img src="<?php  echo _CUR_HOST . _DIR ; ?>_img/<?php  echo _CLIENT_LOGO; ?>" alt="<?php  echo _CLIENT_ALT; ?>" />
      </div>
      <?php
      //   include('_inc/mainnav.php');
      ?>

      <div class="cBoth"><!-- --></div>
   </div> <!-- end topheader -->

   <!-- Category Section -->
   <div id="categorySection">
      <div class="cAlign">
         <!-- Categories -->
         <?php
            include('../_inc/middlenav.php');
         ?>

         <!-- Toggle Button
         <img src="_img/collapseButton.png" alt="Click here to collapse the panel" class="toggleButton" />
         <img src="_img/expandButton.png" alt="Click here to expand the panel" class="toggleButton" id="expandButton" />
-->
         <div style="clear: both"><!-- --></div>
      </div>
   </div> <!-- end categorySection -->

   <!-- Breadcrumbs -->
   <div id="breadcrumbsSection">
      <div class="cAlign cFloat">
         <p>
            You are here:&nbsp;&nbsp;
            Home
            &nbsp;&raquo;&nbsp;Order Management
            &nbsp;&raquo;&nbsp;<strong>Check Out</strong>
         </p>
      </div>
   </div> <!-- end breacrumbsSection -->
   <div class="cAlign cFloat">
      <!-- Blog Post List -->
      <div id="mainSection">
         <ul id="articles">
            <li>
               <div class="articleContent">

                 <div id="results">
                  <h3>Order submitted.  Your order will be processed within 24 hours.</h3>
                  <h3>Please allow upto 10 working days for embroidery plus delivery to your location.</h3>
                 </div>


                  <p>
                  DTYLink v2.0
                  </p>

               </div>
            </li>

         </ul>



      </div> <!-- end mainSection -->

      <!-- Sidebar -->
      <div id="sidebar">

      </div> <!-- end sidebar -->

   </div>

   <?php
      include('../_inc/footer.php');
   ?>



</body>
</html>
