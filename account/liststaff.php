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
require_once('../products/ordersclass.php');

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

//unset staff in case user moves back and forth
unset($_SESSION["staff"]);

?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="utf-8">
   <title>Designs To You - DTYLink Online Ordering</title>
   <?php include('../_inc/js_css.php');?>
   <link rel="stylesheet" type="text/css" href="<?php  echo _CUR_HOST . _DIR ; ?>_css/page.css" media="screen" />
   <link rel="stylesheet" type="text/css" href="<?php  echo _CUR_HOST . _DIR ; ?>_css/table.css" media="screen" />
</head>

<script type="text/javascript">
$(document).ready(function()
{
   $("#articleCommentForm").validationEngine();
   $('#box-table-a').dataTable({
      "aLengthMenu": [[-1,10, 25, 50, 100], ["All", 10, 25, 50, 100]],
      "iDisplayLength":10,
      "aoColumns": [
      { "bSortable": false },
      null,
      null,
      null,
      null,      
      null
      ]
   });

   $("#loadercheckout").hide();
   /* IE7 z-index fix*/
   $(function() {
   var zIndexNumber = 1000;
   $('div').each(function() {
       $(this).css('zIndex', zIndexNumber);
       zIndexNumber -= 10;
   });
   });

   $("#approve").click(function(e)
		   {
		      $("#loadercheckout").show();

		      var appItems = [];
		      var param = ''
		      $("input[name='registerBox']:checked").each(function(){
		         param += 'itemArr[]=' + $(this).val() + '&';
		      });
		//alert(param);
		      $.ajax(
		      {
		         type: "POST",
		         url: "ajaxApproveLogin.php",
		         data: param,
		         dataType: 'json', // expecting json
		         success: function(msg)
		         {
		            if(msg.success == true)
		            {
		               $("input[name='registerBox']:checked").each(function()
		               {
		                  var cur = $(this).val();
		                  $("#td_" + cur).html("APPROVED");
		               });

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

   $("#reset").click(function(e)
		   {
		      $("#loadercheckout").show();

		      var appItems = [];
		      var param = ''
		      $("input[name='registerBox']:checked").each(function(){
		         param += 'itemArr[]=' + $(this).val() + '&';
		      });
		//alert(param);
		      $.ajax(
		      {
		         type: "POST",
		         url: "ajaxResetPassword.php",
		         data: param,
		         dataType: 'json', // expecting json
		         success: function(msg)
		         {
		            if(msg.success == true)
		            {
		               $("input[name='registerBox']:checked").each(function()
		               {
		                  var cur = $(this).val();
		               });

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
   

   $("#delete").click(function(e)
   {
      $("#loadercheckout").show();
      if(!confirm('Are you sure you want to deactivate the selected staff?'))
      {
         $("#loadercheckout").hide();
         return false;
      }

      var delItems = [];
      var param = ''
      $("input[name='registerBox']:checked").each(function(){
         param += 'itemArr[]=' + $(this).val() + '&';
      });

      $.ajax(
      {
         type: "POST",
         url: "ajaxDeleteStaff.php",
         data: param,
         dataType: 'json', // expecting json
         success: function(msg)
         {
            if(msg.success == true)
            {
               $("input[name='registerBox']:checked").each(function()
               {
                  var cur = $(this).val();
                  $("#tr" + cur).remove();
               });

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
            &nbsp;&raquo;&nbsp;<strong>List Staff</strong>
         </p>
      </div>
   </div> <!-- end breacrumbsSection -->

   <div class="cAlign cFloat">
      <!-- Blog Post List -->
      <div id="mainSection">
         <ul id="articles">
            <li>
               <div class="orderContent">
                  <h2>Staff List</h2>
                  <p>
                     <form action="" method="post" id="checkoutform">
Select Last Name:&nbsp;<a href="<?php echo _CUR_HOST ._DIR . "account/liststaff.php?lastname=ALL"?>">ALL</a>&nbsp;|&nbsp;
                        <?php
                           $alphabetArr = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");

                           for($i = 0; $i < count($alphabetArr); $i++)
                           {
                              $curAlpha = $alphabetArr[$i];
                        ?>
                              <a href="<?php echo _CUR_HOST ._DIR . "account/liststaff.php?lastname=$curAlpha"?>"><?php echo $curAlpha;?></a>&nbsp;|&nbsp;
                        <?php
                           }

                        ?>
                        <table id="box-table-a" summary="Employee Pay Sheet">
                           <thead>
                           <tr>
                              <th><input type="checkbox" name="checkAll" id="checkAll" onclick="jqCheckAll(this.id, 'registerBox');"></th>
                              <th>EID/Username</th>
                              <th>Location</th>
                              <th>Name</th>
                              <th width="200px">Email</th>
                              <th>Status</th>                              
                           </tr>
                           </thead>
                           <tbody>
                           <?php
                              $jurisdiction = $_SESSION[_JURISDICTION];
                              $uid = $_SESSION[_USER_ID];
                              $lid = $_SESSION[_LOCATION_ID];

                              if(minAccessLevel(_ADMIN_LEVEL))
                                 $query = "select *,l.status as lstatus from login l where user_id !=''";
                              else if(minAccessLevel(_BRANCH_LEVEL))
                                  $query = "select *,l.status as lstatus from login l, location l1 where l.location_id = l1.location_id and l1.location_id = $lid ";
                              else
                              	$query = "select *,l.status as lstatus from login l, location l1 where l.location_id = l1.location_id and l.user_id = $uid";
                                 //$query = "select * from login where jurisdiction = '$jurisdiction'";
//                                  echo "$query<BR>";
                              $query .= " and l.status = 'ACTIVE'";

                              $lastname = _checkIsSet("lastname");
                              if($lastname)
                              {
                                 if($lastname != "ALL")
                                 {
                                    $query .= " and lastname like '$lastname%'";
                                 }
                                 else
                                 {

                                 }
                     
                                 $res = db_query($query);
                                 $num = db_numrows($res);
                              }

//   echo "$query<BR>";
                             if($num > 0)
                              {
                                 for($i = 0; $i < $num; $i++)
                                 {
                                    $user_id = db_result($res, $i, "user_id");//emp id
                                    $employee_id = db_result($res, $i, "user_name");
                                    $firstname = db_result($res, $i, "firstname");
                                    $lastname = db_result($res, $i, "lastname");
                                    $fullname = "$firstname $lastname";
                                    $business_name = db_result($res, $i, "job_classification");
                                    $location_id = db_result($res, $i, "location_id");
                                    $uniform = db_result($res, $i, "role_id");
                                    $email = db_result($res, $i, "email");
                                    $status = db_result($res, $i, "lstatus");
                                    
                                    $locationObj = new location();
                                    $locationObj->LoadLocationId($location_id);
                                    $locationName = $locationObj->sname;
                                    $costcentre = $locationObj->branch_id;
                                    $entity = $locationObj->entity;
												$tdid = "td_" . $user_id;

                                    $userlink = "<a href='addstaff.php?action=edit&user_id=$user_id'>$employee_id</a>";

                           ?>
                                    <tr id="tr<?php echo $user_id;?>">
                                       <td><input type="checkbox" name="registerBox" value="<?php echo $user_id;?>">&nbsp;</td>
                                       <td><?php echo $userlink;?></td>
                                       <td><?php echo $locationName;?></td>
                                       <td><?php echo $fullname;?></td>
                                       <td><?php echo $email;?></td>
                                       <td id="<?php echo $tdid;?>"><?php echo $status;?></td>                                       
                                    </tr>
                           <?php
                                 }
                              }
                           ?>
                           </tbody>
                        </table>
<!-- 
<div id="approveloc"><input type="submit" id="approve" name="approve" value="Approve"/> &nbsp;&nbsp;&nbsp;
<input type="submit" id="reset" name="reset" value="Reset Password"/>
</div>                   
 -->
 
<div id="delloc"><input type="submit" id="delete" name="delete" value="DEACTIVATE"/></div>
<div id="loadercheckout"><img src="../_img/fbloader.gif" alt="loading..."/></div>
                     </form>
                  </p>
               </div>
            </li>
            <li>

                  <p>
                  DTYLink v2.0
                  </p>
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