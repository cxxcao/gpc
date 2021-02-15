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
require_once('../products/productsclass.php');

$action = _checkIsSet("action");

if(!user_isloggedin())
{
   header("Location: " . _CUR_HOST. _DIR);
}
//else if(!minAccessLevel(_ADMIN_LEVEL))
//{
//   user_logout();
//   header("Location: " . _CUR_HOST. _DIR);
//}

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
      null
      ]
   });

   $("#loadercheckout").hide();

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
            &nbsp;&raquo;&nbsp;<strong>My Details</strong>
         </p>
      </div>
   </div> <!-- end breacrumbsSection -->

   <div class="cAlign cFloat">
      <!-- Blog Post List -->
      <div id="mainSection">
         <ul id="articles">
            <li>
               <div class="articleContent">
                  <h2>My Details</h2>
                  <p>
                  <?php
                     $query = "select * from login l, location l1 where l.user_id = " . $_SESSION[_USER_ID] . " and l.location_id = l1.location_id";
                     $res = db_query($query);
                     $num = db_numrows($res);
                     $orders = new orders();

                     if($num > 0)
                     {
                        $username = db_result($res, 0, "user_name");
                        $userid = db_result($res, 0, "user_id");
                        $locationid = db_result($res, 0, "location_id");
                        $branch_id = db_result($res, 0, "branch_id");
                        $firstname = db_result($res, 0, "firstname");
                        $lastname = db_result($res, 0, "lastname");
                        $payFreq = db_result($res, 0, "pay_frequency");
                        $fullname = "$firstname $lastname";
                        //reset admin's allowance if any
                        $orders = new orders();

                  $orders->LoadUserIdAllowance($orders->getCurrentUserID());

                  //$allowance = $_SESSION[_ALLOWANCE];
                  $allowance = $orders->getAllowanceFromOrderDate();
                  $alreadyOrdered = $orders->getOrderedTotal();
                  $paidAmt = $orders->GetAlreadyPaid($orders->getCurrentUserID(), $orders->order_id);
                  //$paidAmt = $orders->GetAlreadyPaid($_SESSION[_USER_ID], $orders->order_id);
//                   echo "p: $paidAmt a: $allowance o: $alreadyOrdered<BR>";
                  //$remaining = ($allowance + $paidAmt) - $alreadyOrdered;
//
//                  if($remaining < 0)
//                     $remaining = 0;
                  //$payable = $orders->CalcPayable($carttotal);

                  $cartTotal = $orders->GrandTotal();

                  $remaining = $allowance+$paidAmt;
                  $remaining = bcsub($remaining, $alreadyOrdered, 2);
                  $remaining = bcsub($remaining, $cartTotal, 2);
                 // $remaining -= $cartTotal;
//echo "action: " . $orders->action . "<BR>";
//echo "REMAIN: $remaining<BR>";
//echo "ALL: $allowance REMAINING: $remaining ALREADYORDERED: $alreadyOrdered CARTTOTAL: $cartTotal PAID: $paidAmt<BR>";
                  $payable = $orders->CalcPayable($total);

                  if($remaining < 0)
                     $remaining = 0;

                  $availableAllowance = bcadd($allowance,$paidAmt,2);
                  $availableAllowance = bcsub($availableAllowance, $alreadyOrdered,2);

                  if($availableAllowance < 0) //set to zero so it doesnt display a -ve number
                     $availableAllowance = 0;

                        $location = new location();
                        $location->LoadLocationId($locationid);

                        $credits = $orders->calcReturnsVal();
//                        echo "credits: $credits<BR>";
                     		                  //display NZD or AUD
		                  $currency = "\$";
		                  if($orders->isAUS == "N")
		                  {                  
		                  	$currency = "NZ\$";
		                  }

                  ?>
                     <table width="300px">
                        <tr><td>User ID:</td><td><?php echo $userid;?></td></tr>
                        <tr><td>Login ID:</td><td><?php echo $username;?></td></tr>
                        <tr><td>Cost Centre ID:</td><td><?php echo $branch_id;?></td></tr>
                        <tr><td>Name:</td><td><?php echo $fullname;?></td></tr>
                        <tr><td>Pay Frequency:</td><td><?php echo $payFreq;?></td></tr>
                        <tr><td valign="top">Allowance:</td><td>
                        <?php
                           $query = "select * from allowance where user_id = $userid order by end asc";
                           $res = db_query($query);
                           $num = db_numrows($res);
                           if($num > 0)
                           {
                              echo "<table width='200px'><tr><td><b>Amount</b></td><td><b>Expiry</b></td></tr>";
                              for($i = 0; $i < $num; $i++)
                              {
                                 $allowance = db_result($res, $i, "allowance");
                                 $expiry = db_result($res, $i, "end");
                                 $expiry2 = explode(" ", $expiry);
                       ?>
                              <tr><td><?php echo $currency.formatNumber($allowance);?></td><td><?php echo $expiry2[0];?></td></tr>
                       <?php
                              }
                              echo "</table>";
                           }
                           

                           
                           
                        ?>
                        </td></tr>
                        <tr><td>Credits:</td><td><?php echo $currency.formatNumber($credits);?></td></tr>
                        <tr><td>Remaining:</td><td><?php echo $currency.formatNumber($remaining);?></td></tr>
                        <tr><td>Location Name:</td><td><?php echo $location->sname;?></td></tr>
                        <tr><td>Address:</td><td><?php echo $location->address;?></td></tr>
                        <tr><td>Surbub:</td><td><?php echo $location->suburb;?></td></tr>
                        <tr><td>State:</td><td><?php echo $location->state;?></td></tr>
                        <tr><td>Postcode:</td><td><?php echo $location->postcode;?></td></tr>
                     </table>
                  <?php
                     }
                  ?>
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