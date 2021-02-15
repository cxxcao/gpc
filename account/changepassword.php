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
require_once('staffclass.php');
require_once('locationclass.php');

$action = _checkIsSet("action");

if(!user_isloggedin())
{
   header("Location: " . _CUR_HOST. _DIR);
}
//else if(!minAccessLevel(_USER_LEVEL))
//{
//   user_logout();
//   header("Location: " . _CUR_HOST. _DIR);
//}

if($action != "new")
{
  // if(!$_SESSION["staff"])
   {
      $uid = _checkIsSet("user_id");
      $staff = new staff();
      $staff->LoadStaffId($uid);
      $_SESSION["staff"] = serialize($staff);
   }
   //else
   {
      $staff = unserialize($_SESSION["staff"]);
   }

}



?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Designs To You - DTYLink Online Ordering</title>
   <?php include('../_inc/js_css.php');?>
</head>

<script type="text/javascript">
$(document).ready(function()
{
   $("#loadercheckout").hide();
   $("#checkoutform").validationEngine();


   $("#change").click(function(e)
   {
      if(!$("#checkoutform").validationEngine('validate'))
         return false;

      $("#loadercheckout").show();

      var vals = $("#checkoutform").serialize();
      $.ajax(
      {
         type: "POST",
         url: "ajaxChangePassword.php",
         data: vals,
         dataType: 'json', // expecting json
         success: function(msg)
         {
            if(msg.success == true)
            {
               alert(msg.msg);
               $("#curpassword").val("");
               $("#newpassword").val("");
               $("#conpassword").val("");
               $("#loadercheckout").hide();
            }
            else
            {
               alert(msg.msg);
               $("#loadercheckout").hide();
               return false;
            }
         },
         failure: function(msg)
         {
            alert('Error!');
            return false;
         }
      });
        e.preventDefault();
   });

});


</script>

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
            &nbsp;&raquo;&nbsp;Account Details
            &nbsp;&raquo;&nbsp;Staff Management
            &nbsp;&raquo;&nbsp;<strong>Change Password</strong>
			</p>
		</div>
	</div> <!-- end breacrumbsSection -->

	<div class="cAlign cFloat">
		<!-- Blog Post List -->
		<div id="mainSection">
			<ul id="articles">
				<li>
					<div class="articleContent">
                  <h2>Change My Password</h2>
                  <p>
                     <form action="" method="post" id="checkoutform">
                     <div class="formrow">
                        <label>Current Password</label>
                        <span class="formwrap">
                           <input class="validate[required]" type="password" name="curpassword" id="curpassword" value=""/>
                        </span>
                     </div>
                     <div class="formrow">
                        <label>New Pasword</label>
                        <span class="formwrap">
                           <input class="validate[required]" type="password" name="newpassword" id="newpassword" value=""/>
                        </span>
                     </div>
                     <div class="formrow">
                        <label>Confirm Password</label>
                        <span class="formwrap">
                           <input class="validate[required]" type="password" name="conpassword" id="conpassword" value=""/>
                        </span>
                     </div>

                     <div class="formrow">
                        <label></label>
                        <span class="formwrap">
                          <input type="submit" id="change" name="change" value="Submit"/>
<div id="loadercheckout"><img src="../_img/fbloader.gif" alt="loading..."/></div>
                        </span>
                     </div>

                     </form>
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
   ?>



	<!-- Copyright info -->
	<div id="copyrightSection">

		<!--<img src="_img/basicsSmallLogo.png" alt="Designs To You" />-->

		<p>&copy; Copyright 2011 Designs To You Pty Ltd. All rights reserved.</p>

	</div> <!-- end copyrightSection -->

</body>
</html>