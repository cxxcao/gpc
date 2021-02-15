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

if($appID == "DTYLINK")
{
	if(decryptIgnoreTime())
	{
		$entityid = $_SESSION["userID"];
		$isValidate = true;
		$entityname = $_SESSION["entityName"];
		$accesslevel = strlen($_SESSION[_ACCESS_LEVEL]);	
		
		echo "<meta http-equiv=\"refresh\" content=\"0; url="._CUR_HOST._DIR."products/neworder.php?action=new&range=1&role=4\">";
	}
}



?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Designs To You - DTYLink Online Ordering</title>
   <?php include('_inc/js_css.php');?>
   
    <script src="./_img/sorbtek/CSSPlugin.min.js"></script>
    <script src="./_img/sorbtek/EasePack.min.js"></script>
    <script src="./_img/sorbtek/TweenLite.min.js"></script>
    <script src="./_img/sorbtek/TimelineLite.min.js"></script>
    <script src="./_img/sorbtek/animation.js"></script>
    <link href="./_img/sorbtek/sorbtek.css" rel="stylesheet" type="text/css">   
   
</head>

<script type="text/javascript">
$(document).ready(function()
{
   $("#articleCommentForm").validationEngine();

   $("#loader").hide();
   $("#resmsg").hide();
   $("#results").hide();
   

   $("#continue").click(function(e)
   {
      e.preventDefault();
      window.location = '<?php echo _CUR_HOST . _DIR . "products/neworder.php?action=new"?>';
   });

   $("#signin").click(function(e)
   {
      if(!$("#articleCommentForm").validationEngine('validate'))
         return false;

      $("#loader").show();
      $("#resmsg").hide();

      var vals = $("#articleCommentForm").serialize();
      $.ajax(
      {
         type: "POST",
         url: "products/ajaxLogin.php",
         data: vals,
         dataType: 'json', // expecting json
         success: function(msg)
         {
            if(msg.success == true)
            {
               username = msg.username;
               userid = msg.userid;
               locationid = msg.locationid;
               locationname = msg.locationname;
               fullname = msg.fullname;
               //costcentre = msg.costcentre;
               email = msg.email;
               //rolename = msg.rolename;
               address = msg.address;
               suburb = msg.suburb;
               state = msg.state;
               postcode = msg.postcode;
               //daysworked = msg.daysworked;

               address = address + " " + suburb + " " + state + " " + postcode;

               $("#loader").hide();
               //login straight away
              // window.location = '<?php echo _CUR_HOST . _DIR . "products/neworder.php"?>';
					$(".createlogin").hide();
               $("#logindiv").fadeOut(1100, function()
               {
                  $("#results").append('<p>Please check your details below. If this is incorrect, please logout and contact your uniform coordinator to have this rectified. Otherwise, click <b>Continue</b></p>Logged in as <strong>'+fullname+'</strong><br/>User ID: '+username+'<br/>Address: '+address+'<br/>Email: '+email+'<br/>');
                  $("#results").fadeIn(1100);
               });
            }
            else
            {
               $("#loader").hide();
               $(".createlogin").hide();
               $("#resmsg").html('<font color="red">'+msg.msg+'</font>');
               $("#resmsg").show();
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
   <!--
   <div class="row promoWrapper headerBarBanner" style="background-color:#FFF9DE; color:#c09853">
         <div class="flexslider promoSliderHome container" style="width:100%;">
               <ul class="slides">
                           <!-- Begin Two Link Banner--
                           <li class="slide2 promoSlide twoLinkBanner largePercentOff htmlSlider">
                           <div class="contentWrapper">
                              <h3>
                                 <span class="extraBoldText shippingText">
                                <p style="font-size:12px; line-height:20px;"></p>
                                <p></p>
                                 </span>
                    
                              </h3>
                                 
                           </div>
                        </li>
                        <!-- 
                     <li class="slide2 promoSlide twoLinkBanner largePercentOff htmlSlider">
                           <div class="contentWrapper">
                              <h3>
                                          <span class="extraBoldText shippingText"><a href="<?php echo _CUR_HOST ._DIR . "help/returns-policy.php"?>">RETURNS ALLOWED FOR EXCHANGE OF GOODS ONLY<sup>#</sup></a></span>
                                          <span class="extraBoldText smallText">WE'RE ACCEPTING ALL EXCHANGES</span>                                        
                                          <span class="extraBoldText smallText">Even for embroidered garments!<sup>##</sup></span>
                              </h3>
                           </div>
                        </li>
                                 
                        
               </ul>
         </div>
      </div>   
      -->
      <!--               
<div class="row promoWrapper headerBarBanner">
        
    <div class="flexslider promoSliderHome container">
            <ul class="slides">
                        <!-- Begin Two Link Banner--

<li class="slide2 promoSlide twoLinkBanner largePercentOff htmlSlider">
			                <div class="contentWrapper">
			                    <h3>
                                        <span class="extraBoldText shippingText">GPC ASIA PACIFIC - ONLINE ORDERING</span>
                                        <span class="extraBoldText smallText">Uniforms &amp; Workwear</span>
			                    </h3>
                                <span class="divider"><img src="https://www.designstoyou.com.au/carlisle/_img/divider.png" alt=""></span>			                    
			                </div>
		                </li>
		                <!-- 
					    <li class="slide2 promoSlide twoLinkBanner largePercentOff htmlSlider">
			                <div class="contentWrapper">
			                    <h3>
                                        <span class="extraBoldText shippingText"><a href="<?php echo _CUR_HOST ._DIR . "help/returns-policy.php"?>">RETURNS ALLOWED FOR EXCHANGE OF GOODS ONLY<sup>#</sup></a></span>
                                        <span class="extraBoldText smallText">WE'RE ACCEPTING ALL EXCHANGES</span>                                        
                                        <span class="extraBoldText smallText">Even for embroidered garments!<sup>##</sup></span>
			                    </h3>
			                </div>
		                </li>
		                 --	                
		                
            </ul>
        </div>
    </div>			
		-->
	</div> <!-- end breacrumbsSection -->

	<div class="cAlign cFloat">
		<!-- Blog Post List -->
		<div id="mainSection">
		
		
			<ul id="articles">
				<li>
					<div class="loginContent">
				
					
               <?php
                  if(!user_isloggedin())
                  {
             			if($appID != "DTYLINK")
							{
               ?>
					
                  <p>
                     <form action="" method="post" id="articleCommentForm">
                     <div id="logindiv" class="logincls">
                     
                    		<h2>SIGN-IN TO YOUR ACCOUNT</h2>
   							  
                        
                        <div class="formrow">
                           <label>User ID</label>
                           <span class="formwrap">
                              <input type="text" id="userid" name="userid" class="validate[required]" value="Enter User ID"  onfocus="if(this.value=='Enter User ID')this.value='';" onblur="if(this.value=='')this.value='Enter User ID';" />
                           </span>
                        </div>

                        <div class="formrow">
                           <label>Password</label>
                           <span class="formwrap">
                              <input type="password" id="password" name="password" value="" class="validate[required]"/>
                           </span>
                        </div>
                        <div class="formrow">
                           <label>&nbsp;</label>
                           <span class="formwrap">
                              <a href="<?php  echo _CUR_HOST ."/forgotpassword/resetpassword.php?r=" ._REALM . "&d="._DIR; ?>">Forgot Password</a>
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
                              <input type="submit" name="signin" id="signin" value="Sign In &nbsp;&raquo;" name="action"/>
                           </span>
                        </div>
                        
                        <div class="formrow">
                           <label>&nbsp;</label>
                           <span class="formwrap">
                              <div id="loader"><img src="_img/fbloader.gif" alt="loading..."/></div>
                           </span>
                        </div>                              
                        
                     </div>
                     
                     <div class="createlogin">
                     <?php
                     include("../genericmsg.php");
                     ?>
                     </div>

                     <div id="results">
                     <p>
                  <input type="submit" id="logout" value="&laquo; &nbsp;Log Out" name="action"/>&nbsp;&nbsp;
                  <input type="submit" id="continue" value="Continue &nbsp;&raquo;" name="action"/>
                  </p>
                     </div>
                     </form>
                  </p>
                  <?php
							}//not appid DTYLINK
							else 
							{
						?>
								<form action="" method="post" id="articleCommentForm">
									<h1>Signing in...</h1>	
								</form>
						<?php 
								
							}	
                  }
                  else
                  {
                     $firstname = $_SESSION[_FIRST_NAME];
                     $lastname = $_SESSION[_LAST_NAME];
                  ?>

                  <h2>Logged In</h2>
                  <p>
                     Welcome <?php echo "$firstname $lastname";?>
                  </p>

                  <?php
                  }
                  ?>
                  <br/>
                  <br/>
                  <br/>
                  <br/>

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



	</div>

   <?php
      include('_inc/footer.php');
   ?>



</body>
</html>