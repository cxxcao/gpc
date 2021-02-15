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
   function emailInvoice(orderID)
   {
      $("#loadercheckout").show();
      param = 'order_id=' + orderID;
      $.ajax(
      {
         type: "POST",
         url: "ajaxInvoiceRequest.php",
         data: param,
         dataType: 'json', // expecting json
         success: function(msg)
         {
            if(msg.success == true)
            {
               alert(msg.msg);
               $("#loadercheckout").hide();
               return false;
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

      return false;
   }
</script>
<script type="text/javascript">
$(document).ready(function()
{
   function emailInvoice()
   {
      $("#loadercheckout").show();
      var orderID = $(this).attr('id');
      param = 'order_id=' + orderID;
      $.ajax(
      {
         type: "POST",
         url: "ajaxInvoiceRequest.php",
         data: param,
         dataType: 'json', // expecting json
         success: function(msg)
         {
            if(msg.success == true)
            {
               alert(msg.msg);
               $("#loadercheckout").hide();
               return false;
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
   $("#articleCommentForm").validationEngine();
   $('#box-table-a').dataTable({
      "aLengthMenu": [[-1,10, 25, 50, 100], ["All", 10, 25, 50, 100]],
      "iDisplayLength":-1
   });

   $("#staffname").focus(function(e)
   {
      $(this).select();
   });

   $("#staffname").mouseup(function(e){
      e.preventDefault();
   })

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

   $( "#datefrom" ).datepicker({
      showButtonPanel: true,
      dateFormat: "yy-mm-dd",
      showAnim: "clip"
   });

   $( "#dateto" ).datepicker({
      showButtonPanel: true,
      dateFormat: "yy-mm-dd",
      showAnim: "clip"
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
         url: "ajaxDeleteOrder.php",
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

      $.ajax(
      {
         type: "POST",
         url: "ajaxApproveOrder.php",
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
            &nbsp;&raquo;&nbsp;<strong>List Orders</strong>
			</p>
		</div>
	</div> <!-- end breacrumbsSection -->

	<div class="cAlign cFloat">
		<!-- Blog Post List -->
		<div id="mainSection">
			<ul id="articles">
				<li>
					<div class="articleContent">
						<h2>Order List</h2>
                  <p>
                     <form action="" method="post" id="checkoutform">
                     <?php
                        $todate = _checkIsSet("dateto");
                        $fromdate = _checkIsSet("datefrom");

                        //if specified date set, load from session
                        if(!$todate)
                        {
                           $todate = date('Y-m-t');
                        }

                        if(!$fromdate)
                        {
                              $fromyear = date('Y');
                              $frommonth = date('m');
                              if($frommonth == 1) //jan set to previous year and month
                              {
                                 $fromyear = $fromyear - 1;
                                 $frommonth = 12;
                              }
                              else
                                 $frommonth = date('m') - 1;

                              if(strlen($frommonth) < 2)
                                 $frommonth = "0$frommonth";

                              $fromdate = "$fromyear-$frommonth-01";
                        }
                     ?>
                        <label for="datefrom">Date From</label>
                        <input id="datefrom" name="datefrom" type="text" value="<?php echo $fromdate;?>" />
                        <label for="dateto">Date To</label>
                        <input id="dateto" name="dateto" type="text" value="<?php echo $todate;?>"/>

                        <?php
                        if(minAccessLevel(_BRANCH_LEVEL))
                        {
                            $user_id_val = _checkIsSet("user_id_val");
                            $fullname = _checkIsSet("fullname");
                        ?>
                           <label for="staffname">Staff Name</label>
                           <input type="text" name="staffname" id="staffname" value="<?php echo $fullname;?>" /> (Please select from the drop down list)
                           <input type="hidden" name="fullname" id="fullname" value="<?php echo $fullname;?>"/>
                           <input type="hidden" name="user_id_val" id="user_id_val" value="<?php echo $user_id_val;?>"/>
                        <?php
                        }
                        ?>

                        <input type="submit" id="go" name="action" value="Submit"/>

                        <table id="box-table-a">
                           <thead>
                           <tr>
                              <th><input type="checkbox" name="checkAll" id="checkAll" onclick="jqCheckAll(this.id, 'registerBox');"></th>
                              <th>Date</th>
                              <th>Order &#8470; </th>
                              <th>Location</th>
                              <th>Name</th>
                              <th>Status</th>
                           </tr>
                           </thead>
                           <tbody>
                           <?php
                           /*
                              if(minAccessLevel(_ADMIN_LEVEL))
                                 $query = "select * from orders order by order_time desc";
                              else
                                 $query = "select * from orders where user_id = " . $_SESSION[_USER_ID] . " order by order_time desc";
                           */
                              $branchLocationId = $_SESSION[_LOCATION_ID];
                              if(minAccessLevel(_ADMIN_LEVEL))
                                 $query = "select * from orders o where o.order_id != '' and  date(order_time) between '$fromdate' and '$todate'";
                              else if(minAccessLevel(_BRANCH_LEVEL))
                                 $query = "select * from orders o, login l where o.order_id != '' and o.sname like '$branchLocationId%'  and l.location_id =  $branchLocationId and  date(order_time) between '$fromdate' and '$todate' group by order_id";
                              else
                                 $query = "select * from orders o where o.user_id = " . $_SESSION[_USER_ID] . " and date(order_time) between '$fromdate' and '$todate'";

                              if($user_id_val)
                                 $query .= " and o.user_id = $user_id_val";

                              $query .= "  order by order_time desc";

                              $res = db_query($query);
                              $num = db_numrows($res);
                              if($num > 0)
                              {
                                 for($i = 0; $i < $num; $i++)
                                 {
                                    $date = db_result($res, $i, "order_time");
                                    $dateArr = explode(" ", $date);
                                    $date = $dateArr[0];
                                    $order_id = db_result($res, $i, "order_id");
                                    $location = db_result($res, $i, "sname");
                                    $name = db_result($res, $i, "name");
                                    $status = db_result($res, $i, "status");
                                    $urllink =  _CUR_HOST . _DIR . "products/checkout.php?order_id=$order_id&action=" . _UPDATE;
                                    $pdflink = _CUR_HOST . _DIR . "products/pdf.php?order_id=$order_id";


                                    $orderLink = "<a href='$urllink'>$order_id</a>";

                                    if($status == _DESPATCHED || $status == "PART DELIVERY" || $status == "DELIVERED")
                                    {
                                       $connoteDB = db_result($res, $i, "connote");
                                       $connoteArr = explode(";", $connoteDB);
                                       $onclick = "";
                                       for($z = 0; $z < count($connoteArr); $z++)
                                       {
                                          $connote = $connoteArr[$z];
                                          if(strlen($connote) > 2)
                                             $onclick .= "window.open(&quot;http://auspost.com.au/track/display.asp?id=$connote&type=consignment&quot;);";
                                         // $status = "<a href='http://auspost.com.au/track/display.asp?id=$connote&type=consignment' target='_blank'>$status</a>";
                                       }
                                    $status = "<a target='_blank' onclick='$onclick'>$status</a>";
                                    }

                           ?>
                                    <tr id="tr<?php echo $order_id?>">
                                       <td>
                                       <input type="checkbox" name="registerBox" value="<?php echo $order_id;?>"></td>
                                       <td><?php echo $date;?></td>
                                   <?php if(minAccessLevel(_USER_LEVEL))
                                         {
                                   ?>
                                       <td><a href="<?php echo $pdflink;?>"><img src="../_img/pdf.png" border="0"/></a>&nbsp;<a onclick="if(confirm('Are you sure you want a copy of the invoice'))emailInvoice('<?php echo $order_id;?>');" class="invoicereq" id="<?php echo $order_id;?>"><img src="../_img/invoice.png" border="0"/></a>&nbsp;<?php echo $orderLink;?></td>
                                   <?php
                                         }
                                         else
                                         {
                                   ?>
                                            <td><a href="<?php echo $pdflink;?>"><img src="../_img/pdf.png" border="0"/></a>&nbsp;<?php echo $orderLink;?></td>
                                   <?php
                                         }
                                   ?>
                                       <td><?php echo $location;?></td>
                                       <td><?php echo $name;?></td>
                                       <td id="td<?php echo $order_id?>"><?php echo $status;?></td>
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