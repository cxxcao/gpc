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
require_once('requestclass.php');
require_once('staffclass.php');

$action = _checkIsSet("action");

if(!user_isloggedin())
{
   header("Location: " . _CUR_HOST. _DIR);
}


if($action != "new")
{
   $rid = _checkIsSet("request_id");
   $request = new request();
   $request->LoadRequestId($rid);
   $_SESSION["request"] = serialize($request);
}
else
{
   $request = new request();
   $_SESSION["request"] = serialize($request);
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

   $("#amount").change(function(e){
      var amount = $(this).val();
      amount = Number(amount.replace(/[^0-9\.]+/g,""));
      if(amount > 100)
      {
         alert("The maxmium top up amount you can request is $100, please update your request.");
         $(this).val("0.00");
      }
   });

   $("#save").click(function(e)
   {
      if(!$("#checkoutform").validationEngine('validate'))
         return false;

      $("#loadercheckout").show();

      var vals = $("#checkoutform").serialize();
      //alert(vals);
      $.ajax(
      {
         type: "POST",
         url: "ajaxSaveRequest.php",
         data: vals,
         dataType: 'json', // expecting json
         success: function(msg)
         {
            if(msg.success == true)
            {
               alert(msg.msg);
               $("#fullname").val("");
               $("#phone").val("");
               $("#email").val("");
               $("#comments").val("");
               $("#amount").val("");

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
            &nbsp;&raquo;&nbsp;Allowance
            &nbsp;&raquo;&nbsp;<strong>Top Up Request</strong>
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

                  if($action == "edit" || $action == _UPDATE)
                  {
                     $contact_number = $request->contact_number;

                  ?>
                  <h2>Edit Top Up Request</h2>
                  <?php
                  }
                  else
                  {
                  ?>
                  <h2>Top Up Request</h2>
                  <?php
                  }


                  $request_date = $request->request_date;
                  $user_id = $request->user_id;
                  $staff = new staff();
                  $staff->LoadStaffId($user_id);
                  $fullname = $staff->firstname . " " . $staff->lastname;
                  $status = $request->status;
                  $amount = $request->amount;
                  $comments = $request->comments;
                  $email = $request->email;
                  $approval_date = $request->approval_date;
                  $approved_by = $request->approved_by;
                  $requests_id = $request->requests_id;
                  if(!$requests_id)
                     $requests_id = "Automatically Generated";

                  ?>
                  <p>
                     <form action="" method="post" id="checkoutform">
                     <div class="formrow">
                        <label>Request ID:</label>
                        <span>
                           <input type="text" name="requests_id" value="<?php echo $requests_id;?>" readonly/>
                        </span>
                     </div>
                     <div class="formrow">
                        <label>Date:</label>
                        <span>
                           <input type="text" name="request_date" value="<?php echo $request_date;?>" readonly/>
                        </span>
                     </div>
                     <div class="formrow">
                        <input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id;?>"/>
                        <label>Contact Name:</label>
                        <span>
                           <input class="validate[required,length[0,50]] text-input" type="text" class="big" name="fullname" id="fullname" value="<?php echo $fullname;?>" size="50"/>
                        </span>
                     </div>
                     <div class="formrow">
                        <label>Contact Number:</label>
                        <span>
                           <input class="validate[required,length[0,50]] text-input" type="text" class="big" name="phone" id="phone" value="<?php echo $contact_number;?>"/>
                        </span>
                     </div>
                     <div class="formrow">
                        <label>Email (optional):</label>
                        <span>
                           <input class="validate[optional,custom[email]] text-input" type="text" class="big" name="email" id="email" value="<?php echo $email;?>" size="50"/>
                        </span>
                     </div>
                     <div class="formrow">
                        <label>Amount:</label>
                        <span>
                           <input class="validate[required,custom[number]] text-input" type="text" name="amount" id="amount" value="<?php echo $amount;?>" />
                        </span>
                     </div>

                     <div class="formrow">
                        <label>Comments:</label>
                        <span class="formwrap">
                           <textarea name="comments" id="comments" rows="10" cols="40"><?php echo $comments;?></textarea>
                        </span>
                     </div>

                     <div class="formrow">
                        <label>Status:</label>
                        <span class="formwrap">
                           <input type="text" name="status" value="<?php echo $status;?>" readonly/>
                        </span>
                     </div>

                     <div class="formrow">
                        <label>Approval Date:</label>
                        <span>
                           <input type="text" name="approval_date" value="<?php echo $approval_date;?>" readonly/>
                        </span>
                     </div>

                     <div class="formrow">
                        <label>Approved By:</label>
                        <span>
                           <input type="text" name="approved_by" value="<?php echo $approved_by;?>" readonly/>
                        </span>
                     </div>

                     <div class="formrow">
                        <label></label>
                        <span class="formwrap">
                          <input type="submit" id="save" name="save" value="Submit"/>
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


</body>
</html>