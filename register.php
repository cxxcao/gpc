<?php
session_start();
   error_reporting(E_ALL);
   ini_set("display_errors", 1);
$home = dirname(__FILE__);
$lib = $home ."/lib/";

require_once($home . '/globals.php');
require_once($lib . 'functions.php');
require_once($lib . 'loginfunctions.php');
require_once($lib . 'htmlGenerator.php');

$action = _checkIsSet("action");
$action = urlencode($action);
$appID = _checkIsSet("appID");
if($_SESSION['order'])
   unset($_SESSION['order']);
//logout
if($action == "%C2%AB+%C2%A0Log+Out")
   user_logout();

echo md5("841934");

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Designs To You - DTYLink Online Ordering</title>
   <?php include('_inc/js_css.php');?>
</head>

<script type="text/javascript">
$(document).ready(function()
{
   $("#articleCommentForm").validationEngine();

   $("#loader").hide();

	$("#signin").click(function(e)
			{
				if(!$("#articleCommentForm").validationEngine('validate'))
					return false;

				$("#loader").show();
				
				var vals = $("#articleCommentForm").serialize();
				$.ajax(
				{
					type: "POST",
					url: "products/ajaxRegister.php",
					data: vals,
					dataType: 'json', // expecting json
					success: function(msg)
					{
						if(msg.success == true)
						{
							$("#loader").hide();
							alert('Thank You for registering, your details have been saved and we will be in touch with you shortly.');
						}
						else
						{
							$('#storeno').validationEngine('showPrompt', msg.msg, 'error');
							$("#loader").hide();
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
		<a href="index.php" id="logo"><img src="<?php  echo _CUR_HOST . _DIR ; ?>_img/dtylink_logo.png" alt="DTY Link - Online Ordering System" /></a>
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
            include('_inc/middlenav.php');
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
				You are here:
				<strong>Home</strong>
				&nbsp;&raquo;&nbsp;
			</p>
		</div>
	</div> <!-- end breacrumbsSection -->

	<div class="cAlign cFloat">
		<!-- Blog Post List -->
		<div id="mainSection">
			<ul id="articles">
				<li>
					<div class="articleContent">
               <?php
                  if(!user_isloggedin())
                  {
               ?>
						<h2>Register to gain access to our online uniform store</h2>
                  <p>
                     <form action="" method="post" id="articleCommentForm">
                     <div id="logindiv">
                        <div class="formrow">
                           <label>&nbsp;</label>
                           <span class="formwrap">
                              <div id="loader"><img src="_img/fbloader.gif" alt="loading..."/></div>
                           </span>
                        </div>
                        <div class="formrow">
                           <label>Store #</label>
                           <span class="formwrap">
                              <input type="text" id="storeno" name="storeno" value="" placeholder="Enter Your Store Number (if known)" />
                           </span>
                        </div>
                        
                        <div class="formrow">
                           <label>Store Name</label>
                           <span class="formwrap">
                              <input type="text" id="storename" name="storename" class="validate[required]" value=""  placeholder="Enter Your Store Name" />
                           </span>
                        </div>    
                        
                        <div class="formrow">
                           <label>Store Address</label>
                           <span class="formwrap">
                              <input type="text" id="address" name="address" class="validate[required]" value=""  placeholder="Address" />
                           </span>
                        </div>    
                        
                        <div class="formrow">
                           <label>Suburb</label>
                           <span class="formwrap">
                              <input type="text" id="suburb" name="suburb" class="validate[required]" value=""  placeholder="Suburb" />
                           </span>
                        </div>       
                        
                     <div class="formrow">
                        <label>State</label>
                        <span class="formwrap">
                        <?php 
	                           generateStaticCombo($statesArr, $state, "state", true);
	                           ?>
                        </span>
                     </div>
                     
                        <div class="formrow">
                           <label>Postcode</label>
                           <span class="formwrap">
                              <input type="text" id="postcode" name="postcode" class="validate[required]" value=""  placeholder="Postcode" />
                           </span>
                        </div>     
                        
                        <div class="formrow">
                           <label>First Name</label>
                           <span class="formwrap">
                              <input type="text" id="firstname" name="firstname" class="validate[required]" value=""  placeholder="First Name" />
                           </span>
                        </div>  
                        
                        <div class="formrow">
                           <label>Last Name</label>
                           <span class="formwrap">
                              <input type="text" id="lastname" name="lastname" class="validate[required]" value=""  placeholder="Last Name" />
                           </span>
                        </div>  
                        
                        <div class="formrow">
                           <label>Work Phone</label>
                           <span class="formwrap">
                              <input type="text" id="phone" name="phone" class="validate[required]" value=""  placeholder="Phone" />
                           </span>
                        </div>                          
                        
                        <div class="formrow">
                           <label>Email</label>
                           <span class="formwrap">
                              <input type="text" id="email" name="email" class="validate[required]" value=""  placeholder="Email" />
                           </span>
                        </div>                                                                                                                                                                                             

                        <div class="formrow">
                          <label>&nbsp;</label>
                           <span id="resmsg">

                           </span>
                        </div>
                        <div class="formrow">
                          <label>&nbsp;</label>
                           <span class="formwrap">
                              <input type="submit" name="signin" id="signin" value="Submit" name="action"/>
                           </span>
                        </div>
                     </div>

                     </form>
                  </p>
						<?php 
								
							}	
                  ?>
                  <br/>
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
      include('_inc/footer.php');
   ?>



</body>
</html>