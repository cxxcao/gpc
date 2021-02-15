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
require_once('../products/ordersclass.php');
require_once('../account/staffclass.php');
require_once('../account/locationclass.php');
$action = _checkIsSet("action");

if(!user_isloggedin())
{
   header("Location: " . _CUR_HOST. _DIR);
}



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
      "iDisplayLength":-1,
      "aoColumns": [
      null,
      null,
      null,
      null,
      { "sType": 'currency' },
      { "sType": 'currency' },
      { "sType": 'currency' },
      { "sType": 'currency' },
      { "sType": 'currency' }
      ]
   });

   jQuery.fn.dataTableExt.oSort['currency-asc'] = function(a,b) {
      /* Remove any formatting */
      var x = a == "-" ? 0 : a.replace( /[^\d\-\.]/g, "" );
      var y = b == "-" ? 0 : b.replace( /[^\d\-\.]/g, "" );

      /* Parse and return */
      x = parseFloat( x );
      y = parseFloat( y );
      return x - y;
   };

   jQuery.fn.dataTableExt.oSort['currency-desc'] = function(a,b) {
      var x = a == "-" ? 0 : a.replace( /[^\d\-\.]/g, "" );
      var y = b == "-" ? 0 : b.replace( /[^\d\-\.]/g, "" );

      x = parseFloat( x );
      y = parseFloat( y );
      return y - x;
   };

   $("#staffname").autocomplete("ajaxstaffquery.php", {
      width: 260,
      matchContains: true,
      max: 2000,
      minChars: 1,
      selectFirst: false
   });

   $("#staffname").change(function(e){
      $("#fullname").val("");
      $("#user_id_val").val("");
   });

   $("#staffname").result(function(event, data, formatted) {
      var fullname = data[0];
      var user_id = data[1];
      $("#fullname").val(fullname);
      $("#user_id_val").val(user_id);
   });

   $( "#expiry" ).datepicker({
      showButtonPanel: true,
      dateFormat: "yy-mm-dd",
      showAnim: "clip"
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
            &nbsp;&raquo;&nbsp;Order Management
            &nbsp;&raquo;&nbsp;Reports
            &nbsp;&raquo;&nbsp;<strong>Balance</strong>
         </p>
      </div>
   </div> <!-- end breacrumbsSection -->

   <div class="cAlign cFloat">
      <!-- Blog Post List -->
   <?php
   if(minAccessLevel(_ADMIN_LEVEL))
   {
   ?>
      <div id="mainSection">
         <ul id="articles">
            <li>
               <div class="articleContent">
                  <h2>Allowance Balance</h2>
                  <p>
                     <form action="" method="post" id="checkoutform">
                     <?php
                        $expiry = _checkIsSet("expiry");

                        //if specified date set, load from session
                        if(!$expiry)
                        {
                           $expiry = date('Y-m-d');
                        }
                     ?>
                        <label for="expiry">Allowance Expiry</label>
                        <input id="expiry" name="expiry" type="text" value="<?php echo $expiry;?>" />
                        <?php
                            $user_id_val = _checkIsSet("user_id_val");
                            $fullname = _checkIsSet("fullname");
                        ?>
                           <label for="staffname">Staff Name</label>
                           <input type="text" name="staffname" id="staffname" value="<?php echo $fullname;?>" /> (Please select from the drop down list)
                           <input type="hidden" name="fullname" id="fullname" value="<?php echo $fullname;?>"/>
                           <input type="hidden" name="user_id_val" id="user_id_val" value="<?php echo $user_id_val;?>"/>
                        <?php
                        ?>

                        <input type="submit" id="go" name="action" value="Submit"/>
                        <table id="box-table-a">
                           <thead>
                           <tr>
                              <th>Employee ID</th>
                              <th>Name</th>
                              <th>Location</th>
                              <th>Expiry</th>
                              <th>Total Allowance</th>
                              <th>Total Paid</th>
                              <th>Credits</th>
                              <th>Total Order Value</th>
                              <th>Balance</th>
                           </tr>
                           </thead>
                           <tbody>
                           <?php
                              if(minAccessLevel(_ADMIN_LEVEL))
                                 $query = "select * from login where user_id != 1";

                              $res = db_query($query);
                              $num = db_numrows($res);
                              if($num > 0)
                              {
                                 $expiry .= " 00:00:00";
                                 for($i = 0; $i < $num; $i++)
                                 {
                                    //for each user get to order that is less than the specified date?
                                    $uid = db_result($res, $i, "user_id");
                                    $fullname = db_result($res, $i, "firstname") . " " . db_result($res, $i, "lastname");
                                    $location_id = db_result($res, $i, "location_id");
                                    $locationObj = new location();
                                    $locationObj->LoadLocationId($location_id);
                                    $locationName = $locationObj->sname;
                                    /**
                                     * if there are 2 allowances, the start & expiry dates can't clash so orders between the different expiry dates should
                                     * not clash as well... so the below should work...
                                     */
                                    $query = "select * from allowance where user_id = $uid and end < '$expiry'";
                                    $ares = db_query($query);
                                    $anum = db_numrows($ares);
                                    $totalUserAllowance = 0;
                                    $totalPaid = 0;
                                    $totalSpent = 0;
                                    for($j = 0; $j < $anum; $j++)
                                    {
                                       $start = db_result($ares, $j, "start");
                                       $end = db_result($ares, $j, "end");
                                       $totalUserAllowance += db_result($ares, $j, "allowance");

                                       $oquery = "select sum(o.payable) as totalpaid from orders o where o.user_id = $uid and o.order_time between '$start' and '$end'";
                                       //echo "$oquery<BR>";
                                       $ores = db_query($oquery);
                                       $onum = db_numrows($ores);
                                       if($onum > 0)
                                       {
                                          $totalpayable = db_result($ores, 0, "totalpaid");
                                          $liquery = "select sum(qty*price) as totalspent from orders o, lineitems li where o.user_id = $uid and o.order_id = li.order_id and o.order_time between '$start' and '$end'";
                                          $lires = db_query($liquery);
                                          $linum = db_numrows($lires);
                                          if($linum > 0)
                                          {
                                             $totalspent = db_result($lires, 0, "totalspent")*1.1;
                                          }
                                          $totalPaid += $totalpayable;
                                          $totalSpent += $totalspent;

                                       }
                                       $orders = new orders();
                                       $creditAmt = $orders->calcReturnsValId($uid)*-1.1;
                                       //$totalPaid += $creditAmt;
                                       $remaining = $totalUserAllowance + $totalPaid + $creditAmt - $totalSpent;
                                       if($remaining == 0)
                                          $remaining = 0;
//   echo "USERID: $uid TOTAL PAID: $totalPaid TOTAL SPENT: $totalSpent ALLOWANCE: $totalUserAllowance credit: $creditAmt<BR>";
                                   // if($remaining > 0)
                                    {
                                       $end = explode(" ", $end);
                           ?>
                                    <tr>
                                       <td><?php echo $uid;?></td>
                                       <td><?php echo $fullname;?></td>
                                       <td><?php echo $locationName;?></td>
                                       <td><?php echo $end[0];?></td>
                                       <td>$<?php echo formatNumber($totalUserAllowance);?></td>
                                       <td>$<?php echo formatNumber($totalPaid);?></td>
                                       <td>$<?php echo formatNumber($creditAmt);?></td>
                                       <td>$<?php echo formatNumber($totalSpent);?></td>
                                       <td>$<?php echo formatNumber($remaining);?></td>
                                    </tr>
                           <?php
                                    }
                                 }
                           ?>

                           <?php
                                 }
                              }
                           ?>
                           </tbody>
                        </table>
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
      </div> <!-- end mainSection -->
   <?php
   }
   ?>
      <!-- Sidebar -->
      <div id="sidebar">

      </div> <!-- end sidebar -->

   </div>

   <?php
      include('../_inc/footer.php');
   ?>


</body>
</html>
