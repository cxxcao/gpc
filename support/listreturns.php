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
require_once('warrantyclass.php');

$action = _checkIsSet(_ACTION);
unset($_SESSION[_WARRANTY_SESSION]);

$action = _checkIsSet("action");

if(!user_isloggedin())
{
   header("Location: " . _CUR_HOST. _DIR);
}

   $status = _checkIsSet(_STATUS);
   $warranty = new warranty();
   if($action == _DELETE)
   {
      $removeArr = _checkIsSet("removeArr");
      $numDel = count($removeArr);
      for($i = 0; $i < $numDel; $i++)
      {
         $delId = $removeArr[$i];
         $query = "delete from warranty where warranty_id = $delId";
         db_query($query);
      }
   }
   else if($action == _SUBMIT)
   {
      $warranty->updateStatus();
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
      "iDisplayLength":10,
      "aoColumns": [
      { "bSortable": false },
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
         url: "ajaxDeleteReturn.php",
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
         url: "ajaxApproveReturn.php",
         data: param,
         dataType: 'json', // expecting json
         success: function(msg)
         {
            if(msg.success == true)
            {
               $("input[name='registerBox']:checked").each(function()
               {
                  var cur = $(this).val();
                  $("#td" + cur).html("APPROVED");
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
            &nbsp;&raquo;&nbsp;Order Management
            &nbsp;&raquo;&nbsp;Support
            &nbsp;&raquo;&nbsp;<strong>Make A Claim</strong>
			</p>
		</div>
	</div> <!-- end breacrumbsSection -->

	<div class="cAlign cFloat">
		<!-- Blog Post List -->
		<div id="mainSection">
			<ul id="articles">
				<li>
					<div class="orderContent">
						<h2>Returns List</h2>
                  <p>
                     <form action="" method="post" id="checkoutform">
                     <?php

                       // $warranty->ListWarranty($query);
                     ?>


                        <table id="box-table-a">
                           <thead>
                           <tr>
                              <th><input type="checkbox" name="checkAll" id="checkAll" onclick="jqCheckAll(this.id, 'registerBox');"></th>
                              <th>Claim Date</th>
                              <th>RA No</th>
                              <th>Order No</th>
                              <th>Name</th>
                              <th>Phone</th>
                              <th>Return Type</th>
                              <th>Status</th>
                           </tr>
                           </thead>
                           <tbody>
                           <?php
                              $branchLocationId = $_SESSION[_LOCATION_ID];
                              $branchId = $_SESSION["branch_id"];
                              $curUserID = $_SESSION[_USER_ID];



                              if($curUserID != 23911247)
                              {
                                 if(minAccessLevel(_ADMIN_LEVEL))
                                    $query = "select *, w.status as wstatus from warranty w order by claim_date desc";
                                 else if(minAccessLevel(_BRANCH_LEVEL))
                                    $query = "select *, w.status as wstatus from orders o, warranty w where o.order_id = w.order_id and o.sname like '$branchId%' order by claim_date desc";
   //                                 $query = "select * from orders o, login l where o.order_id != '' and o.sname like '$branchLocationId%'  and l.location_id =  $branchLocationId and  date(order_time) between '$fromdate' and '$todate' group by order_id";
//                                 $query = "select * from orders o, login l where o.order_id != '' and o.sname like '$branchId%'  and l.location_id =  '$branchLocationId' and  date(order_time) between '$fromdate' and '$todate' group by order_id";
                                 else
                                 {
                                    $user_id = $_SESSION[_USER_ID];
                                    $query = "select *, w.status as wstatus from orders o, warranty w where o.user_id = $user_id and o.order_id = w.order_id order by claim_date desc";
                                 }
                              }
                              else
                                    $query = "select *, w.status as wstatus from orders o, warranty w where o.user_id = $curUserID and o.order_id = w.order_id order by claim_date desc";

                              $res = db_query($query);
                              $num = db_numrows($res);
                              if($num > 0)
                              {
                                 for($i = 0; $i < $num; $i++)
                                 {

                                    $warranty_id = db_result($res, $i, "warranty_id");
                                    $order_id = db_result($res, $i, "order_id");
                                    $claim_date = db_result($res, $i, "claim_date");
                                    $status = db_result($res, $i, "wstatus");
                                    $name = ucwords(strtolower((db_result($res, $i, "name"))));
                                    $phone = db_result($res, $i, "phone");
                                    $returntype = db_result($res, $i, "return_type");
                                    $link = _DIR . "support/returns.php?action=" . _UPDATE ."&" . _WARRANTY_ID . "=$warranty_id";

                                    $returns = "returns_$i";
                                    $claim_date_link = $claim_date;

                                    $return = new warranty();
                                    $return->LoadReturns($warranty_id);
                                    $rtype = "";
                                    $numrt = count($return->returnlines);
                                    for($j = 0; $j < $numrt; $j++)
                                    {
                                       $rl = new returnLines();
                                       $rl = $return->returnlines[$j];
                                       $rtype .= $rl->return_type;
                                       $rtype .= ",";
                                    }
                                    $rtype = substr($rtype, 0, strlen($rtype)-1);
                           ?>
                                    <tr id="tr<?php echo $warranty_id;?>">
                                       <td><input type="checkbox" name="registerBox" value="<?php echo $warranty_id;?>">&nbsp;</td>
                                       <td align="left" class="orderlist"><?php echo $claim_date_link;?></td>
                                       <td valign="left" class="orderlist"><a href="<?php echo $link;?>"><?php echo $warranty_id;?></a></td>
                                       <td align="left" class="orderlist"><?php echo $order_id;?></td>
                                       <td align="left" class="orderlist"><?php echo $name;?></td>
                                       <td align="left" class="orderlist"><?php echo $phone;?></td>
                                       <td align="left" class="orderlist"><?php echo $rtype;?></td>
                                       <td id="td<?php echo $warranty_id?>" align="left" class="orderlist"><?php echo $status;?></td>
                                    </tr>
                           <?php
                                 }
                              }
                           ?>
                           </tbody>
                        </table>




<?php
   if(minAccessLevel(_ADMIN_LEVEL))
   {
?>
<div id="approveloc"><input type="submit" id="approve" name="approve" value="Approve"/></div>
<?php
   }
?>

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