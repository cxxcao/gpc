<?php
session_start();
//    error_reporting(E_ALL);
//    ini_set("display_errors", 1);
$home = dirname(__FILE__);
$lib = $home ."/lib/";

require_once($home . '/globals.php');
require_once($lib . 'functions.php');
require_once($lib . 'loginfunctions.php');
require_once($lib . 'htmlGenerator.php');

$action = _checkIsSet("action");
$action = urlencode($action);
$appID = _checkIsSet("appid");
if($_SESSION['order'])
   unset($_SESSION['order']);
//logout
if($action == "%C2%AB+%C2%A0Log+Out")
   user_logout();

?>
<!DOCTYPE html>
<html lang="en">
  
<head>
    <meta charset="utf-8">
    <title>Terry White Chemmart - Registration</title>

	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes"> 
    
<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="css/bootstrap-responsive.min.css" rel="stylesheet" type="text/css" />

<link href="css/font-awesome.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600" rel="stylesheet">
    
<link href="css/style.css" rel="stylesheet" type="text/css">
<link href="css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
<link href="css/pages/signin.css" rel="stylesheet" type="text/css">

</head>

<body>
	
	<div class="navbar navbar-fixed-top">
	
	<div class="navbar-inner">
		
		<div class="container">
			
			<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</a>
			
			<a class="brand" href="<?php echo _CUR_HOST_._OMSDIR;?>index.php">
			<img src="img/Lumina-logo_official-licensee.png" width="100" height="100" alt="Lumina Shop">
				SHOP	
			</a>		
			
			<div class="nav-collapse">
				<ul class="nav pull-right">
					<!-- 
					<li class="">						
						<a href="" class="">
							Don't have an account?
						</a>
						
					</li>
					 -->
					<li class="">						
						<a href="http://www.luminaactive.com.au" class="">
							Back to Lumina Active
						</a>
						
					</li>
				</ul>
				
			</div><!--/.nav-collapse -->	
	
		</div> <!-- /container -->
		
	</div> <!-- /navbar-inner -->
	
</div> <!-- /navbar -->



<div class="account-container">
	
	<div class="content clearfix">
		
		<?php 
		if($appID != "MYCRICKET")
		{
		?>
		<form action="" method="post" id="loginForm">
			<h1>RESELLER REGISTRATION</h1>		
			
				<hr/>
				<p><b>To became a reseller of Lumina Active products, please fill out the form below and we will contact you.</b></p>
				
				<hr/>			
			
			<div class="login-fields">
				
				<p>Please enter your details below</p>
								
				<div class="field">
					<label for="username">Company Name</label>
					<input type="text" id="company" name="company" value="" placeholder="Name of your business" class="login username-field validate[required]" />
				</div> 
				<div class="field">
					<label for="firstname">First Name</label>
					<input type="text" id="firstname" name="firstname" value="" placeholder="Your first name" class="login username-field validate[required]" />
				</div> 
				<div class="field">
					<label for="lastname">Last Name</label>
					<input type="text" id="lastname" name="lastname" value="" placeholder="Your last name" class="login username-field validate[required]" />
				</div> 				
				<div class="field">
					<label for="contact">Contact Number</label>
					<input type="text" id="contact" name="contact" value="" placeholder="Primary contact number" class="login username-field validate[required]" />
				</div> 				
				<div class="field">
					<label for="email">Email</label>
					<input type="text" id="email" name="email" value="" placeholder="Your email address" class="login username-field validate[required]" />
				</div> 				
				
				
				

				<div class="field">
				<div id="loader"></div>	&nbsp;
				</div>
				
			</div> 
			<div class="login-actions">
			   <button id="signin" class="button btn btn-success btn-large">Register</button>
			</div>
		</form>
		<?php 
		}
		else
		{
		?>
		<form action="" method="post" id="loginForm">
			<h1>Signing in...</h1>		
		</form>		
		<?php 
		}
		?>
	</div> <!-- /content -->
	
</div> <!-- /account-container -->

<div class="login-extra">
	2016 &copy; <a href="http://www.luminaactive.com.au">Lumina Active</a> - A Designs To You Company | SHOP
</div> <!-- /login-extra -->

<script src="js/jquery-1.10.2.js"></script>
<script src="https://code.jquery.com/jquery-migrate-1.2.1.min.js"></script> 
<script src="js/bootstrap.js"></script>
<script src="js/signin.js"></script>
<script src="js/languages/jquery.validationEngine-en.js"></script>
<script src="js/jquery.validationEngine.js"></script>

<script type="text/javascript">
$(document).ready(function()
{
	if($.browser.msie && $.browser.version < 9)
		alert('Unfortunately the SHOP is not compatible with your browser, only version 9+ of Internet Explorer is supported.  Please upgrade your browser, or use Google Chrome, Firefox or Safari');
	$("#loginForm").validationEngine();
	$("#loader").hide();
	
	$("#signin").click(function(e)
	{
		if(!$("#loginForm").validationEngine('validate'))
			return false;

		$("#loader").show();
		
		var vals = $("#loginForm").serialize();
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
					alert('Thank You for your interest in becoming a reseller, your details have been saved and we will be in touch with you shortly.');
				}
				else
				{
					$('#company').validationEngine('showPrompt', msg.msg, 'error');
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
<script>

</script>
</body>

</html>
