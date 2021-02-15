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
require_once('locationclass.php');

$action = _checkIsSet("action");

if(!user_isloggedin())
{
   header("Location: " . _CUR_HOST. _DIR);
}
else if(!minAccessLevel(_USER_LEVEL))
{
   user_logout();
   header("Location: " . _CUR_HOST. _DIR);
}

if($action != "new")
{
   //if(!$_SESSION["location"])
   {
      $lid = _checkIsSet("location_id");
      $location = new location();
      $location->LoadLocationId($lid);
      $_SESSION["location"] = serialize($location);
   }
  // else
   {
      $location = unserialize($_SESSION["location"]);
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

   $("#query").autocomplete("../products/ajaxstorequery.php", {
      width: 260,
      matchContains: true,
      selectFirst: false
   });

   $("#query").result(function(event, data, formatted) {
      var location_id = data[1];
      $("#query_val").val(data[1]);
   });

   $("#save").click(function(e)
   {
      if(!$("#checkoutform").validationEngine('validate'))
         return false;

      $("#loadercheckout").show();

      var vals = $("#checkoutform").serialize();
      $.ajax(
      {
         type: "POST",
         url: "ajaxSaveLocation.php",
         data: vals,
         dataType: 'json', // expecting json
         success: function(msg)
         {
            if(msg.success == true)
            {
               alert(msg.msg);
               $("#sname").val("");
               $("#branch_id").val(""); //costcentre
               $("#stype").val("");
               $("#address").val("");
               $("#suburb").val("");
               $("#state").val("");
               $("#postcode").val("");
               $("#stype").val("");
               $("#phone").val("");
               $("#fax").val("");
               $("#email").val("");
               $("#business_name").val("");
               $("#entity").val("");
               $("#hospital").val("");               
               $("#business_unit").val("");               
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
            &nbsp;&raquo;&nbsp;Location Management
            &nbsp;&raquo;&nbsp;<strong>Add Location</strong>
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
                     $branch_id = $location->branch_id;
                     $sname = $location->sname;
                     $stype = $location->stype;
                     $address = $location->address;
                     $suburb = $location->suburb;
                     $postcode = $location->postcode;
                     $state = $location->state;
                     $phone = $location->phone;
                     $fax = $location->fax;
                     $email = $location->email;
                     $country = $location->country;

                     $defstatus = $location->status;

                     $defbusiness_name = $location->business_name;
                     $defbusiness_unit = $location->business_unit;
                     $entity = $location->entity;
                     $hospital = $location->hospital;
                  ?>
						<h2>Edit Location</h2>
                  <?php
                  }
                  else
                  {
                  ?>
                  <h2>Add Location</h2>
                  <?php

                  }
                  ?>
                  <p>
                     <form action="" method="post" id="checkoutform">
                     <div class="formrow">
                        <label>Status</label>
                        <span class="formwrap">
                          <?php
                           $status = _checkIsSet("status");
                           if(!$status)
                              $status = $defstatus;
                           generateStaticCombo($staffStatusArr, $status, "status", true);?>
                        </span>
                     </div>   
                     <div class="formrow">
                        <label>Location Name</label>
                        <span class="formwrap">
                           <input class="validate[required]" type="text" name="sname" id="sname" value="<?php echo $sname;?>"/>
                        </span>
                     </div>                       
                                                        
                     <div class="formrow">
                        <label>Cost Centre</label>
                        <span class="formwrap">
                           <input class="validate[required]" type="text" name="branch_id" id="branch_id" value="<?php echo $branch_id;?>"/>
                        </span>
                     </div>
                      
<!-- 
                     <div class="formrow">
                        <label>Type</label>
                        <span class="formwrap">
                           <input class="validate[required]" type="text" name="stype" id="stype" value="<?php echo $stype;?>"/>
                        </span>
                     </div>
 -->                     
                 
                     <div class="formrow">
                        <label>Address</label>
                        <span class="formwrap">
                           <input class="validate[required]" type="text" name="address" id="address" value="<?php echo $address;?>"/>
                        </span>
                     </div>
                     <div class="formrow">
                        <label>Suburb</label>
                        <span class="formwrap">
                           <input class="validate[required]" type="text" name="suburb" id="suburb" value="<?php echo $suburb;?>"/>
                        </span>
                     </div>
                     <div class="formrow">
                        <label>State</label>
                        <span class="formwrap">
                           <?php generateStaticCombo($statesArr, $state, "state", true);?>
                        </span>
                     </div>
                     <div class="formrow">
                        <label>Postcode</label>
                        <span class="formwrap">
                           <input class="validate[required]" type="text" name="postcode" id="postcode" value="<?php echo $postcode;?>" />
                        </span>
                     </div>
                     <div class="formrow">
                        <label>Country</label>
                        <span class="formwrap">
                           <?php generateStaticCombo($countryArr, $country, "country", true);?>
                        </span>
                     </div>
                     <div class="formrow">
                        <label>Phone</label>
                        <span class="formwrap">
                           <input class="validate[required,custom[phone]]" type="text" name="phone" id="phone" value="<?php echo $phone;?>" />
                        </span>
                     </div>
                     <div class="formrow">
                        <label>Fax</label>
                        <span class="formwrap">
                           <input class="validate[optional,custom[phone]]" type="text" name="fax" id="fax" value="<?php echo $fax;?>" />
                        </span>
                     </div>
                     <div class="formrow">
                        <label>Email</label>
                        <span class="formwrap">
                           <input class="validate[optional,custom[email]]" type="text" name="email" id="email" value="<?php echo $email;?>" />
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