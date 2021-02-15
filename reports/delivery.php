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

$action = _checkIsSet("action");

if(!user_isloggedin())
{
   header("Location: " . _CUR_HOST. _DIR);
}

if(!minAccessLevel(_ADMIN_LEVEL))
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
//   $('#box-table-a').dataTable({
//      "aLengthMenu": [[-1,10, 25, 50, 100], ["All", 10, 25, 50, 100]],
//      "iDisplayLength":10,
//      "aoColumns": [
//      null,
//      null,
//      null,
//      null,
//      null
//      ]
//   });



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
            &nbsp;&raquo;&nbsp;<strong>Garments Ordered</strong>
         </p>
      </div>
   </div> <!-- end breacrumbsSection -->

   <div class="cAlign cFloat">
      <!-- Blog Post List -->
      <div id="mainSection">
         <ul id="articles">
            <li>
               <div class="orderContent">
                  <h2>Delivery Times</h2>
                  <p>
                        <table id="box-table-b">
                           <thead>
                           <tr>
                              <th>Number of Orders</th>
                              <th>Delivered (3 days)</th>
                              <th>Delivered (4 - 7 days)</th>
                              <th>Delivered (8 - 10 days)</th>
                              <th>Delivered (more than 10 days)</th>
                           </tr>
                           </thead>
                           <tbody>
                           <?php

                              $query = "select * from orders where status = 'DELIVERED'";
                              $res = db_query($query);
                              $num = db_numrows($res);
                              $daysData = "";
                              $one = 0;
                              $two = 0;
                              $three = 0;
                              $four = 0;
                              $five = 0;
                              $six = 0;
                              $seven = 0;
                              $eight = 0;
                              $nine = 0;
                              $ten = 0;
                              $greaterTen = 0;

                              if($num > 0)
                              {
                                 $days3 = 0;
                                 $days4_7 = 0;
                                 $days8_10 = 0;
                                 $days11plus = 0;
                                 $totalDays = 0;
                                 for($i = 0; $i < $num; $i++)
                                 {
                                    $dateOrdered = db_result($res, $i, "order_time");
                                    $lastudpated = db_result($res, $i, "lastupdated");

                                    $dateOrderedObj = new DateTime($dateOrdered);
                                    $lastupdatedObj = new DateTime($lastudpated);
                                    $dateDiff = $dateOrderedObj->diff($lastupdatedObj);
                                    $days = $dateDiff->d;

                                    if($days == 1)
                                       $one++;
                                    else if($days == 2)
                                       $two++;
                                    else if($days == 3)
                                       $three++;
                                    else if($days == 4)
                                       $four++;
                                    else if($days == 5)
                                       $five++;
                                    else if($days == 6)
                                       $six++;
                                    else if($days == 7)
                                       $seven++;
                                    else if($days == 8)
                                       $eight++;
                                    else if($days == 9)
                                       $nine++;
                                    else if($days == 10)
                                       $ten++;
                                    else
                                       $greaterTen++;


                                    if($days < 4)
                                       $days3++;
                                    else if($days > 3 && $days < 8)
                                       $days4_7++;
                                    else if($days > 7 && $days < 11)
                                       $days8_10++;
                                    else if($days > 10)
                                       $days11plus++;

                                    $totalDays+= $days;
                                 }
                              }
                           ?>
                                <tr>
                                   <td><?php echo $num;?></td>
                                   <td><?php echo $days3;?></td>
                                   <td><?php echo $days4_7;?></td>
                                   <td><?php echo $days8_10;?></td>
                                   <td><?php echo $days11plus;?></td>
                                </tr>


                           </tbody>
                        </table>
                     </p>
                     <h2>Orders Per Month</h2>
                     <p>
                        <!-- sales by month -->
                        <div style="width:350px;float:right">
                        <table id="box-table-b">
                           <thead>
                           <tr>
                              <th>Month</th>
                              <th>Number of Orders</th>
                           </tr>
                           </thead>
                           <tbody>
                           <?php

                              $query = "SELECT count(*) as total, YEAR(order_time) as year, MONTH(order_time) as month FROM orders group by month, year order by year, month";
                              $res = db_query($query);
                              $num = db_numrows($res);
                              $barData = "";
                              $xAxis = "";

                              if($num > 0)
                              {
                                 for($i = 0; $i < $num; $i++)
                                 {
                                    $numOrders = db_result($res, $i, "total");
                                    $monthNum = db_result($res, $i, "month");
                                    $year = db_result($res, $i, "year");
                                    $totalOrders += $numOrders;

                                    $monthName = date("M", mktime(0, 0, 0, $monthNum, 12));
                                    $monthYear = "$monthName $year";
                                    $barData .= "[$i, $numOrders],";
                                    $xAxis .= "[$i, \"$monthYear\"],";
                              ?>
                              <tr>
                                 <td><?php echo $monthYear;?></td>
                                 <td><?php echo $numOrders;?></td>
                              </tr>
                              <?php
                                 }
                                 if(strlen($barData) > 0)
                                 {
                                    $barData = substr($barData, 0, strlen($barData)-1);
                                    $xAxis = substr($xAxis, 0, strlen($xAxis)-1);
                                 }
                              }
                              ?>
                           </tbody>
                        </table>
                        </div>
                        <!-- end sales by month -->

                         <div id="delivery" style="width:600px;height:300px;float:left;padding-right:10px;"></div>

                        <div id="placeholder" style="width:600px;height:300px;float:left;"></div>
                        <script type="text/javascript">

                        $.plot($("#delivery"),
                           [
                            {
                              label: "Delivery",
                              data: [ [1,<?php echo $one;?>],[2,<?php echo $two;?>],[3,<?php echo $three;?>],[4,<?php echo $four;?>],[5,<?php echo $five;?>],[6,<?php echo $six;?>],[7,<?php echo $seven;?>],[8,<?php echo $eight;?>],[9,<?php echo $nine;?>],[10,<?php echo $ten;?>],[11,<?php echo $greaterTen;?>]],
                              color: "#4DA74D",
                            }
                         ],
                         {
                           xaxis: {
                             ticks:
                              [[1,"1 Day"],[2,"2 Days"],[3,"3 Days"],[4,"4 Days"],[5,"5 Days"],[6,"6 Days"],[7,"7 Days"],[8,"8 Days"],[9,"9 Days"],[10,"10 Days"],[11,"10+ Days"]]
                           }
                         }
                        );

                        $.plot(
                           $("#placeholder"),
                           [
                            {
                              label: "Orders Per Month",
                              data: [ <?php echo $barData;?> ],
                              bars: {
                                show: true,
                                barWidth: 0.5,
                                align: "center"
                              }
                            }
                         ],
                         {
                           xaxis: {
                             ticks: [
                              <?php echo $xAxis;?>
                             ]
                           }
                         }
                        );
                        </script>
                        <?php
                           $avgDays = $totalDays/$num;
//                           echo "Average: $avgDays<BR>";
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