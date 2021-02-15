<?php
session_start();
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
   echo "Redirecting to the login page.  Please click <a href=". _CUR_HOST. _DIR.">here</a> if you are not redirected within a few seconds.";
   exit;
}
else if(!minAccessLevel(_USER_LEVEL))
{
   user_logout();
   header("Location: " . _CUR_HOST. _DIR);
   echo "Redirecting to the login page.  Please click <a href=". _CUR_HOST. _DIR.">here</a> if you are not redirected within a few seconds.";
   exit;
}

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
<html>
<head>
<meta http-equiv="x-ua-compatible" content="IE=8">
	<meta charset="utf-8">
	<title>Designs To You - DTYLink Online Ordering</title>
   <?php include('../_inc/js_css.php');?>
</head>

<script type="text/javascript">
$(document).ready(function()
{
   $("#loadercheckout").hide();
   $("#checkoutform").validationEngine();
   var olduserId = $("#user_name").val();

   $("#query").autocomplete("../products/ajaxstorequery.php", {
      width: 260,
//      matchContains: true,
//      selectFirst: false,
      minChars:0,
      max:3000
   });

   $("#query").result(function(event, data, formatted) {
      var location_id = data[1];
      $("#query_val").val(data[1]);
      var address = data[2] + " " + data[3] + ", " + data[4] + " " + data[5];

      $("#addressinfo").html(address);
        //echo "$sname|$location_id|$address|$suburb|$state|$postcode|$phone|$fax|$email\n";
   });

   /*
   $("#job_classification").autocomplete("../products/ajaxjobclassificationquery.php", {
      width: 260,
      matchContains: true,
      selectFirst: false,
      max:3000
   });

   $("#job_classification").result(function(event, data, formatted) {
      var title = data[0];
      var allowance_first = data[1];
      showAllowanceFields();
      $("#job_classification").val(title);
      $("#allowance1").val(allowance_first);

   });
   */

   $("#hire_date").datepicker({
      showButtonPanel: true,
      dateFormat: "yy-mm-dd"
   });

   $("#user_name").bind('blur', function(e){

      var userId = $("#user_name").val();

      $.ajax(
      {
         type: "POST",
         url: "ajaxCheckStaffUsername.php",
         data: "user_name=" + userId,
         dataType: 'json', // expecting json
         success: function(msg)
         {
            if(msg.success == true)
            {

            }
            else
            {
               alert(msg.msg);
               $('#user_name').val(olduserId);
               $('#user_name').focus();
               return false;
            }
         },
         failure: function(msg)
         {
            alert('Error!');
            return false;
         }
      });
   });

   $("#user_id").bind('blur', function(e){

      var userId = $("#user_id").val();

      $.ajax(
      {
         type: "POST",
         url: "ajaxCheckStaffId.php",
         data: "user_id=" + userId,
         dataType: 'json', // expecting json 
         success: function(msg)
         {
            if(msg.success == true)
            {

            }
            else
            {
               alert(msg.msg);
               $('#user_id').val(olduserId);
               $('#user_id').focus();
               return false;
            }
         },
         failure: function(msg)
         {
            alert('Error!');
            return false;
         }
      });
   });

   $("#reset").click(function(e){
      var eid = $("#user_id").val();
      $("#loadercheckout").show();

      if(!confirm('Are you sure you want to reset the password?'))
      {
         $("#loadercheckout").hide();
         return false;
      }
      else
      {
         $.ajax(
         {
            type: "POST",
            url: "ajaxResetPassword.php?user_id=" + eid,
            dataType: 'json', // expecting json
            success: function(msg)
            {
               if(msg.success == true)
               {
                  alert(msg.msg);
                  $("#user_id").val("");
                  $("#firstname").val("");
                  $("#lastname").val("");
                  $("#query").val("");
                  $("#allowance").val("");

                  var num = $('.clonedInput').length - 1;
                  for(i = 1; i <= num; i++)
                  {
                     $("#allowance" +  i).val("");
                     $("#start" + i).val("");
                     $("#end" + i).val("");
                  }

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
      }
   });

   $(".datepickerclass").datepicker({
      showButtonPanel: true,
      dateFormat: "yy-mm-dd"
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
         url: "ajaxSaveStaff.php",
         data: vals,
         dataType: 'json', // expecting json
         success: function(msg)
         {
            if(msg.success == true)
            {
               alert(msg.msg);
               $("#user_id").val("");
               $("#user_name").val("");
               $("#firstname").val("");
               $("#lastname").val("");
               $("#query").val("");
               $("#allowance").val("");
               $("#email").val("");
               $("#job_classification").val("");
               $("#position").val("");
               $("#role_id").val("");
               $("#range").val("");

               var num = $('.clonedInput').length - 1;
               for(i = 1; i <= num; i++)
               {
                  $("#allowance" +  i).val("");
                  $("#start" + i).val("");
                  $("#end" + i).val("");
               }

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

   function showAllowanceFields()
   {
                var num = $('.clonedInput').length - 1;
                var newNum  = new Number(num + 1);
                $('#numallowance').val(newNum);

                var newElem = $('#input').clone().attr('id', 'input' + newNum);

                /* change name first */
                newElem.find("#allowance").attr('name','allowance' + newNum);
                newElem.find("#start").attr('name', 'start' + newNum);
                newElem.find("#end").attr('name', 'end' + newNum);
                newElem.find("#allowance").attr('id','allowance' + newNum);
                newElem.find("#start").attr('id', 'start' + newNum);
                newElem.find("#end").attr('id', 'end' + newNum);

                if(num != 0)
                {
                  $('#input' + num).after(newElem);
                }
                else
                {
                   $('#input').after(newElem);
                }
                $('#input' + newNum).show();


               $("#" + 'start' + newNum).datepicker({
                  showButtonPanel: true,
                  dateFormat: "yy-mm-dd"
               });


               $("#" + 'end' + newNum).datepicker({
                  showButtonPanel: true,
                  dateFormat: "yy-mm-dd"
               });

                $('#btnDel').removeAttr('disabled');
   }
            $('#btnAdd').click(function(e) {
               showAllowanceFields();
               e.preventDefault();
            });

            $('#btnDel').click(function(e) {
                var num = $('.clonedInput').length - 1; // how many "duplicatable" input fields we currently have
                $('#input' + num).remove();     // remove the last element

                $('#numallowance').val(num-1);
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
            &nbsp;&raquo;&nbsp;<strong>Add Staff</strong>
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
                     $user_name = $staff->user_name;
                     $user_id = $staff->user_id;
                     $firstname = $staff->firstname;
                     $lastname = $staff->lastname;
                     $location_id = $staff->location_id;
                     $location = new location();
                     $location->LoadLocationId($location_id);
                     $sname = $location->sname;
                     $allowance = $staff->allowance;
                     $role_id = $staff->role_id;
                     $defstatus = $staff->status;
                     $defAccessLevel = $staff->access_level;
                     $email = $staff->email;
                     $job_classification = $staff->job_classification;
                     $hire_date = $staff->hire_date;
                  ?>
                  <h2>Edit Staff</h2>
                  <?php
                  }
                  else
                  {
                     $user_name = "";
                     $user_id = "";
                  ?>
                  <h2>Add Staff</h2>
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
                        <label>Access Level</label>
                        <span class="formwrap">
                          <?php

                           $accessLevel = _checkIsSet("access_level");
                           if(!$accessLevel)
                              $accessLevel = $defAccessLevel;

                           generateStaticCombo($accessArr, $accessLevel, "access_level", true);?>
                        </span>
                     </div>
                     <div class="formrow">
                        <label>Employee ID</label>
                        <span class="formwrap">
                           <input class="validate[required]" type="text" name="user_id" id="user_id" value="<?php echo $user_id;?>"/>
                        </span>
                     </div>
                     <div class="formrow">
                        <label>Username</label>
                        <span class="formwrap">
                           <input class="validate[required]" type="text" name="user_name" id="user_name" value="<?php echo $user_name;?>"/>
                        </span>
                     </div>
                     <div class="formrow">
                        <label>First Name</label>
                        <span class="formwrap">
                           <input class="validate[required]" type="text" name="firstname" id="firstname" value="<?php echo $firstname;?>"/>
                        </span>
                     </div>
                     <div class="formrow">
                        <label>Last Name</label>
                        <span class="formwrap">
                           <input class="validate[required]" type="text" name="lastname" id="lastname" value="<?php echo $lastname;?>"/>
                        </span>
                     </div>
                     <div class="formrow">
                        <label>Email</label>
                        <span class="formwrap">
                           <input class="validate[optional,custom[email]]" type="text" name="email" id="email" value="<?php echo $email;?>"/>
                        </span>
                     </div>
                     <div class="formrow">
                        <label>Hire Date</label>
                        <span class="formwrap">
                           <input class="validate[required,custom[date]]" type="text" name="hire_date" id="hire_date" class="startdate" value="<?php echo $hire_date;?>"/>
                        </span>
                     </div>
                     <div class="formrow">
                        <label>Location</label>
                        <span class="formwrap">
                           
                           
                           <?php
                           $default = $location_id;
                           if(!$default)
                              $default = _checkIsSet("query_val");
                           generateComboQuery("query_val", "select sname,location_id from location", $default, "required");
                        ?>                           

                           <span id="addressinfo"></span>
                           
                        </span>
                     </div>                      
                     <div class="formrow">
                        <label>Range</label>
                        <span class="formwrap">
                           <?php
                           $rangeArr = array(1=>"Womenswear", 2=> "Menswear");
                           $range = _checkIsSet("range");
                           if(!$range)
                              $range = $staff->crange;

                           generateStaticComboSimple($rangeArr, $range, "range", true);?>
                        </span>
                     </div>
                           <?php
                           if(minAccessLevel(_BRANCH_LEVEL)) 
                           {
                              ?>
                     <div class="formrow">
                        <label>Uniform Type</label>
                        <span class="formwrap">
                        <?php 
                              $role_id = _checkIsSet("role_id");
                              if(!$role_id)
                              {
                                 $role_id = $staff->role_id;

                              }
                              
                             // generateDropDownRoleNZ(role_id, "select name, employeerole_id from employee_role", $role_id, true, $staff->isAUS );
                                 
                              
                              //generateStaticComboSimple($employeeRoleIDArr, $role_id, "role_id", true);
                              generateComboQuery("role_id", "select name,employeerole_id from employee_role", $role_id, "required");
//                            generateStaticComboSimple($divisionArr, $role_id, "role_id", true);
                        ?>
                        </span>
                     </div>                              
                     <?php 
                           }
                           else 
                           {
                           ?>
                             <input type="hidden" name="role_id" id="role_id" value="<?php echo $role_id;?>"/> <!--  default ID -->   
                           <?php 
                           }
                           ?>

                                          
                     <div class="formrow">
                        <label>Australian Based</label>
                        <span class="formwrap">
                           <?php
                           $isAUS = _checkIsSet("isAUS");
                           if(!$isAUS)
                              $isAUS = $staff->isAUS;

                           generateStaticCombo($yesNoArr, $isAUS, "isAUS", true);?>
                        </span>
                     </div>
                     
                     <div class="formrow">
                        <label>Days Worked</label>
                        <span class="formwrap">
                           <?php
                           $daysworked = _checkIsSet("daysworked");
                           if(!$daysworked)
                              $daysworked = $staff->daysworked;

                           generateStaticComboSimple($daysWorkedArr, $daysworked, "daysworked", true);?>
                        </span>
                     </div>                     

                     <!--
                     <div class="formrow">
                        <label>Allowance</label>
                        <span class="formwrap">
                           <input class="validate[required,custom[number]]" type="text" name="allowance" id="allowance" value="<?php echo $allowance;?>" />
                        </span>
                     </div>
                     -->
                     <div class="staffformrow">
                        <label>Allowance</label>
                           <span class="formwrap">
                        <?php
                           $len = count($staff->allowanceArr);
                           for($i = 1; $i <= $len; $i++)
                           {
                              $allowance = $staff->allowanceArr[$i-1]->allowance;
                              $startdate = $staff->allowanceArr[$i-1]->startdate;
                              $enddate = $staff->allowanceArr[$i-1]->enddate;
                        ?>

                              <div id="input<?php echo $i;?>" class="clonedInput">
                                 <input class="validate[required,custom[number]]" type="text" name="allowance<?php echo $i;?>" id="allowance<?php echo $i;?>" value="<?php echo $allowance;?>" class="allowanceamount"/>
                                 &nbsp;Start:&nbsp;<input class="validate[required,custom[date]], datepickerclass" type="text" name="start<?php echo $i;?>" id="start<?php echo $i;?>" value="<?php echo $startdate;?>" class="startdate" />
                                 &nbsp;Expiry:&nbsp;<input class="validate[required,custom[date]], datepickerclass" type="text" name="end<?php echo $i;?>" id="end<?php echo $i;?>" value="<?php echo $enddate;?>" class="enddate" />
                              </div>
                        <?php
                           }
                           if($len == 0)
                              $i = 1;
                        ?>

                           <div style="display:none;" id="input" class="clonedInput">
                              <input class="validate[required,custom[number]]" type="text" name="allowance" id="allowance" />
                              &nbsp;Start:&nbsp;<input class="validate[required,custom[date]]" type="text" name="start" id="start" class="startdate" />
                              &nbsp;Expiry:&nbsp;<input class="validate[required,custom[date]]" type="text" name="end" id="end" class="enddate" />
                           </div>
                        </span>
                     </div>

                     <?php
                     if(minAccessLevel(_BRANCH_LEVEL))//only admins can add staff, branch managers can view
                     {
                     ?>
                     <div class="staffformrow">
                        <label></label>
                        <span class="formwrap">
                           <div>
                              <input type="hidden" name="numallowance" id="numallowance" value="<?php echo $i-1;?>">
                              <input type="submit" id="btnAdd" value="Add Allowance" />
                              <input type="submit" id="btnDel" value="Remove Allowance" />
                           </div>
                        </span>
                     </div>
                     <div class="formrow">
                        <label></label>
                        <span class="formwrap">
                          <input type="submit" id="save" name="save" value="Submit"/>
                          <input type="submit" id="reset" name="reset" value="Reset Password"/>
                          <div id="loadercheckout"><img src="../_img/fbloader.gif" alt="loading..."/></div>
                        </span>
                     </div>
                     <?php
                     }
                     ?>
                     </form>
                  </p>

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