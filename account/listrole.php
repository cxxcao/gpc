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
   <link rel="stylesheet" type="text/css" href="<?php  echo _CUR_HOST . _DIR ; ?>_css/table-jui.css" media="screen" />
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

   $("#delete").click(function(e)
   {
      $("#loadercheckout").show();
      if(!confirm('Are you sure you want to delete the selected items?'))
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
            &nbsp;&raquo;&nbsp;<strong>List Role</strong>
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
                        <table id="box-table-a" summary="Employee Pay Sheet">
                           <thead>
                           <tr>
                              <th><input type="checkbox" name="checkAll" id="checkAll" onclick="jqCheckAll(this.id, 'registerBox');"></th>
                              <th>Employee ID</th>
                              <th>Name</th>
                              <th width="200px">Location</th>
                              <th align="right">Allowance</th>
                              <th align="right">Purchased Amt</th>
                              <th align="right">Paid Amt</th>
                              <th align="right">Credit Amt</th>
                              <th align="right">Balance</th>
                              <th align="right">Status</th>
                           </tr>
                           </thead>
                           <tbody>
                           <?php
                              $jurisdiction = $_SESSION[_JURISDICTION];

                              if(minAccessLevel(_ADMIN_LEVEL))
                                 $query = "select * from login";
                              else
                                 $query = "select * from login where jurisdiction = '$jurisdiction'";
                              $res = db_query($query);
                              $num = db_numrows($res);
                             if($num > 0)
                              {
                                 for($i = 0; $i < $num; $i++)
                                 {
                                    $user_id = db_result($res, $i, "user_id");//emp id

                                    $user_name = db_result($res, $i, "user_name");
                                    $firstname = db_result($res, $i, "firstname");
                                    $lastname = db_result($res, $i, "lastname");
                                    $status = db_result($res, $i, "status");
                                    $fullname = "$firstname $lastname";
                                    /** JUST SHOW THE LATEST ALLOWANCE?? **/
                                    $allowanceObj = new allowance();
                                    $allowanceObj->getLatestAllowance($user_id);
                                    $allowStart = $allowanceObj->start;
                                    $allowEnd = $allowanceObj->end;
                                    $allowance = $allowanceObj->amount;
                                    if(!$allowance)
                                       $allowance = 0;

                                    /*
                                    $allowanceQuery = "select sum(allowance) as total from allowance where user_id = $user_id";

                                    $allowanceRes = db_query($allowanceQuery);
                                    $allowanceNum = db_numrows($allowanceRes);
                                    if($allowanceNum > 0)
                                    {
                                       $allowance = db_result($allowanceRes, 0, "total");
                                    }
                                    */
                                    $location_id = db_result($res, $i, "location_id");

                                    $locationObj = new location();
                                    $locationObj->LoadLocationId($location_id);
                                    $locationName = $locationObj->sname;

                                    $belt_prod_id = _BELT_PROD_ID;
                                    //$usedQuery = "select sum(price*qty) as total from orders o, lineitems li where o.order_id = li.order_id and o.user_id = $user_id and li.prod_id != $belt_prod_id";
//                                    $usedQuery = "select sum(price*qty) as total from orders o, lineitems li where o.order_id = li.order_id and o.user_id = $user_id";
                                    $usedQuery = "select sum(price*qty) as total from orders o, lineitems li where o.order_id = li.order_id and o.user_id = $user_id and order_time between '$allowStart' and '$allowEnd'";
//                                    echo "$usedQuery<BR>";
                                    $usedRes = db_query($usedQuery);
                                    $usedNum = db_numrows($usedRes);
                                    $usedAmt = 0;
                                    if($usedNum > 0)
                                    {
                                       $usedAmt = db_result($usedRes, 0, "total")*1.1;
                                    }

//                                    $paidQuery = "select sum(payable) as total from orders where user_id = $user_id";
//                                    $paidRes = db_query($paidQuery);
//                                    $paidNum = db_numrows($paidRes);
//                                    $paidAmt = 0;
//                                    if($paidNum > 0)
//                                    {
//                                       $paidAmt = db_result($paidRes,0, "total"); //gst already included;
//                                    }

                                    $orders = new orders();
                                    $orders->allowanceObj = $allowanceObj;
                                    $paidAmt = $orders->GetAlreadyPaid($user_id, "");
                                    $creditAmt = $orders->calcReturnsValId($user_id)*1.1;
                                    $remaining = ($allowance+$paidAmt) - ($usedAmt+$creditAmt);

                                    if($remaining < 0)
                                       $remaining = 0;

                                    $userlink = "<a href='addstaff.php?action=edit&user_id=$user_id'>$user_name</a>";

                           ?>
                                    <tr id="tr<?php echo $user_id;?>">
                                       <td><input type="checkbox" name="registerBox" value="<?php echo $user_id;?>">&nbsp;</td>
                                       <td><?php echo $userlink;?></td>
                                       <td><?php echo $fullname;?></td>
                                       <td><?php echo $locationName;?></td>
                                       <td align="right"><?php echo formatNumber($allowance);?></td>
                                       <td align="right"><?php echo formatNumber($usedAmt);?></td>
                                       <td align="right"><?php echo formatNumber($paidAmt);?></td>
                                       <td align="right"><?php echo formatNumber($creditAmt);?></td>
                                       <td align="right"><?php echo formatNumber($remaining);?></td>
                                       <td><?php echo $status;?></td>
                                    </tr>
                           <?php
                                 }
                              }
                           ?>
                           </tbody>
                        </table>
<div id="delloc"><input type="submit" id="delete" name="delete" value="Delete"/></div>
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


</body>
</html>