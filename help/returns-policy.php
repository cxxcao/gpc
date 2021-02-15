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
require_once('../products/ordersclass.php');
require_once('../products/productsclass.php');

$action = _checkIsSet("action");
//
//if(!user_isloggedin())
//{
//   header("Location: " . _CUR_HOST. _DIR);
//}



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
            &nbsp;&raquo;&nbsp;Help
            &nbsp;&raquo;&nbsp;<strong>Returns Policy</strong>
         </p>
      </div>
   </div> <!-- end breacrumbsSection -->

   <div class="cAlign cFloat">
      <!-- Blog Post List -->
      <div id="mainSection">
         <ul id="articles">
            <li>
               <div class="orderContent">
                  <h2>Returns Policy</h2>
                  <h3>Making A Claim</h3>
                  <p>
                  <ol>
                     <li>
                     To return your garments, please use the online returns form which can be found by clicking on "MAKE A CLAIM" in the order details.
                        <br/>
                        <br/>
                        <b>All items returned must be in their original condition.</b>
                        <br/>
                        
                     </li>
                  </ol>
                  </p>

                  <h3>Exchanging an Item</h3>
                  <p>
                  <ol>
                     <li>
                        If you're not completely satisfied with your purchase and the garments are not embroidered, you can return the item(s) to us in their original condition* within 30 days of receipt.  

                        <br/>
                        Please note
                        <br/>
                        <br>
                        <br>&nbsp;&nbsp;&nbsp;1. You must send the item(s) back with a copy of the Approval email - your return may be rejected if the items are received without the approval email.
                        <br>&nbsp;&nbsp;&nbsp;2. Returned items must reach us within 14 days of receipt of the email/approval.
                        <br>&nbsp;&nbsp;&nbsp;3. Returned items must be sent back in their original condition, preferably in the orginial packaging or suitable replacement.
                        <br>&nbsp;&nbsp;&nbsp;4. Your parcel is your responsibility until it reaches us.
                     </li>
                  </ol>
                  </p>

                  <h3>Faulty/Incorrectly Supplied</h3>
                  <p>
                  <ol>
                     <li>
                        For faulty or incorrectly supplied items, DTY will provide you with a reply paid number to use when returning the items back to us.
                     </li>
                  </ol>
                  </p>
                  <p>
                  *Original Condition means items must still have all tags attached, free from any marks or scent arising from perfume, aftershave or deodorant and preferably returned in the original packaging or suitable replacement. We are unable to accept returns
                  where there is evidence that instructions have not been followed.
                  </p>
                  <p>
                  <sup>##</sup>
                  Special make garments, that is garments that have been made specifically for you according to your measurements are not eligible for exchanges/returns unless it is faulty or incorrectly supplied.
                  </p>                  
                  
                  <br/>
                  <br/>
                  <br/>
                  <p>
                  DTYLink v2.0
                  </p>

               </div>
            </li>

         </ul>

         <!-- Pagination --
         <ul id="pagination">
            <li><a href="#" class="active">1</a></li>
            <!--
            <li><a href="#">2</a></li>
            <li><a href="#">3</a></li>

         </ul>-->

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