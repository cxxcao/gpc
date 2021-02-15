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
            &nbsp;&raquo;&nbsp;<strong>Terms Of Use</strong>
         </p>
      </div>
   </div> <!-- end breacrumbsSection -->

   <div class="cAlign cFloat">
      <!-- Blog Post List -->
      <div id="mainSection">
         <ul id="articles">
            <li>
               <div class="orderContent">
                  <h2>Terms of Use</h2>
                  <h3>Privacy Policy</h3>
                  <p>
                  <ol>
                     <li>
Design To You understands that your privacy is very important. We are committed to taking reasonable steps, in so far as it is in our power to do so, to protecting the personal information you provide to us. At no time will we sell your information to any marketing third-party, but use it only for its intended use; to provide you with our services.
                     </li>
                  </ol>
                  </p>

                  <h3>Personal Information</h3>
                  <p>
                  <ol>
                     <li>
When accessing, ordering or registering for a service on our site, www.designstoyou.com.au, will collect certain personal information to be able to provide you with our services. By registering, you are consenting to the collection of your personal data. These may include the following; name, email address, billing and delivery addresses and your contact number.
<br/><br/>
Your personal information is gathered to allow us to provide you with our services, maximize the effectiveness of our services and communication and fulfill your order. Your personal details, such as address and contact number may be provided to a third party business partner for purposes of fulfilling your order. Designs To You may from time to time use your email address to provide you with direct marketing material. You can chose to opt-out from this service at any time by emailing sales@designstoyou.com.au with the words 'opt-out' from your listed email address.
<br/><br/>
Designs To You collates information about site traffic, sales, wish lists and other commercial information which we may pass on to a third party, but this information does not include any information which can identify you personally.
<br/><br/>
We will not under any circumstance, except if required by law, disclose any information about you without your consent to any marketing third party.
                     </li>
                  </ol>
                  </p>

                  <h3>Security</h3>
                  <p>
                  <ol>
                     <li>
Your information is stored on secure servers, using Secure Sockets Layer (SSL) which encrypts the information you send through our website.
<br/><br/>
Designs To You makes no warranty of the effectiveness or strength of the encryption and will accept no responsibility for any events that may arise from unauthorized access to the personal information you provide to us.
<br/><br/>
Designs To You will endeavor to take reasonable steps to secure any information we may hold about you and your purchases, but we will not ensure or warrant the security of any information you transmit to us, you do so at your own risk.
<br/><br/>
You are solely responsible for maintaining the secrecy of your personal information. When using a public or shared computer more caution should always be taken.

                     </li>
                  </ol
                  </p>

                  <h3>Cookies</h3>
                  <p>
                  <ol>
                     <li>
By using our website you agree that we can place these types of cookies on your device and access them when you visit the site in future.
                     </li>
                  </ol
                  </p>
                  <h3>Alterations to our policies</h3>
                  <p>
                  <ol>
                     <li>
Designs To You reserves the right to review and revise The Privacy Policy, at any time without informing you.
<br/><br/>
We encourage you to check our website regularly for the most updated Privacy Policy.
                     </li>
                  </ol
                  </p>

                  <h3>Disclaimer</h3>
                  <p>
                  <ol>
                     <li>
Designs To You to you will take reasonable care to maintain appropriate safeguards to ensure the security, integrity and privacy of the information you have provided us with. In addition, we will take reasonable steps to ensure that any third party business partners to whom we transfer any data to provide your with our services, will provide sufficient protection of that personal information. However, Designs To You will not liable for any loss or damage arising from the use of this website and/or any other websites with which our website may be linked to or with.
                     </li>
                  </ol
                  </p>

                  <h3>Questions</h3>
                  <p>
                  <ol>
                     <li>
We take your comments very seriously, if a problem has occurred or you are having difficulties with our website, please contact us and we will work to address your concerns.
<br/><br/>
If you would like to contact us with any queries or comments in regards to our Terms and Conditions or feedback, please contact us at sales@designstoyou.com.au and we will endeavor to respond to you within one business day.

                     </li>
                  </ol
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