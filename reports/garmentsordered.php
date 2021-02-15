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
   $('#box-table-a').dataTable({
      "aLengthMenu": [[-1,10, 25, 50, 100], ["All", 10, 25, 50, 100]],
      "iDisplayLength":-1,
      "aoColumns": [
      null,
      null,
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
               <div class="articleContent">
                  <h2>Garments Ordered</h2>
                  <p>
                     <form action="" method="post" id="checkoutform">
                        <table id="box-table-a">
                           <thead>
                           <tr>
                              <th>Item Number</th>
                              <th>Total Qty</th>
                              <th>Total Value</th>
                           </tr>
                           </thead>
                           <tbody>
                           <?php
                              $query = "select prod_id, myob_code, sum(qty) totalqty, sum(qty*price) as totalval from lineitems group by prod_id order by prod_id";
                              $res = db_query($query);
                              $num = db_numrows($res);
                              if($num > 0)
                              {
                                 for($i = 0; $i < $num; $i++)
                                 {
                                    $prod_id = db_result($res, $i, "prod_id");
                                    $myob_code = db_result($res, $i, "myob_code");
                                    $totalQty = db_result($res, $i, "totalqty");
                                    $totalVal = db_result($res, $i, "totalval");
                           ?>
                                    <tr>
                                       <td><?php echo $myob_code;?></td>
                                       <td><?php echo $totalQty;?></td>
                                       <td>$<?php echo formatNumber($totalVal);?></td>
                                    </tr>
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

      <!-- Sidebar -->
      <div id="sidebar">

      </div> <!-- end sidebar -->

   </div>

   <?php
      include('../_inc/footer.php');
   ?>

</body>
</html>