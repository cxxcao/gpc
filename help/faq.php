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
            &nbsp;&raquo;&nbsp;<strong>FAQ</strong>
         </p>
      </div>
   </div> <!-- end breacrumbsSection -->

   <div class="cAlign cFloat">
      <!-- Blog Post List -->
      <div id="mainSection">
         <ul id="articles">
            <li>
               <div class="orderContent">
                  <h2>FAQ</h2>
                  <p>
                  <ol>
                     <li><b>1. How do I login?</b><br/>
a. To login, you will need to enter your User Name as the user ID and enter in the generic password.  If you are successful, you will be taken straight to the ordering page.
</li>

<li><b>2. What does the status APPROVED, PROCESSING, DESPATCHED mean, and why can't I modify my orders?</b><br/>

a. APPROVED: Are orders that have just been entered into the system, these orders can be modified or deleted at anytime.
<br/>
<br/>
PROCESSING: Orders in this state indicate that they are currently being picked and are being prepared for despatch. Orders in this state may not be modified, however you may contact DTY on (03) 9753 2555 to verify this.
<br/>
<br/>
DESPATCHED: Indicates that your order has been despatched and cannot be changed.  All orders are sent using Australia Post and may take up to 10 working days for embroidery plus delivery.
</li>
<li><b>3. Can I track my orders online?</b><br/>
a. Yes, orders can be tracked online. Simply click on the DESPATCH link on the order lists page, this will automatically take to your Australia Post's tracking site and display tracking information.  Only DESPATCHED orders can be tracked.
</li>
<li><b>4. The size calculator is suggesting a size that is different to what I normally wear.</b><br/>
a. Particularly with women's clothing, there are no set standard in Australia for sizes and vary from brand to brand and as such, DTY sizes are also different.  The size calculator should only be used as a guide to help you determine your size.
</li>
<li><b>5. Currency</b><br/>
Unless otherwise indicated, all prices shown are in Australian Dollars (AUD) and includes GST.
</li>

                  </ol>
                  </p>

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
      //echo md5("anzdtytest") . "<BR>";
   ?>

</body>
</html>