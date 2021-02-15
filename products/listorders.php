<?php
session_start();
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
   exit(0);
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

	   $("#query").autocomplete("ajaxstorequery.php", {
		      width: 260,
		      matchContains: true,
		      selectFirst: false
		   });
	   
	   $("#query").result(function(event, data, formatted) {
		   	var sname = data[0];
		      var location_id = data[1];
		      var address = data[2];
		      var suburb = data[3];
		      var state = data[4];
		      var postcode = data[5];
		      var phone = data[6];
		      var fax = data[7];
		      var email = data[8];
		      var country = data[9];

		      $("#query_val").val(location_id);

		      //ajaxSaveStoreSession
		         $.ajax(
			         {
				         type: "POST",
			            url: "ajaxSaveStoreSession.php?location_id=" + data[1],
			            dataType: 'json', // expecting json
			            success: function(msg)
			            {
			               if(msg.success == true)
			               {
		                  }
		               },
		               failure: function(msg)
		               {
		                  alert('Error!');
		                  return false;
		               }
		            });			      
// 		      $("#branchname").val(data[1]);		      
		   });	   	
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
					<div class="orderContent">
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
                     <table>
	                     <tr>
   	                  	<td><label for="datefrom">Date From&nbsp;&nbsp;</label></td>
   	                  	<td><input id="datefrom" name="datefrom" type="text" value="<?php echo $fromdate;?>" /></td>   	                  	
   	                  	<td><label for="dateto">Date To&nbsp;&nbsp;</label></td>
   	                  	<td colspan="2"><input id="dateto" name="dateto" type="text" value="<?php echo $todate;?>"/></td>
								</tr>
                        <?php
                        if(minAccessLevel(_ADMIN_LEVEL))
                        {
                            $user_id_val = _checkIsSet("user_id_val");
                            $branch_id_val = _checkIsSet("q");
                            $fullname = _checkIsSet("fullname");
                            $sname = _checkIsSet("q");
                            
                        ?>
                        <tr>
                           <td><label for="staffname">Location&nbsp;&nbsp;</label></td>
                           <td>
											<input class="validate[required] query" type="text" name="q" id="query" value="<?php echo $sname;?>" />  
	                              <input type="hidden" name="query_val" id="query_val" value="<?php echo $branch_id_val;?>" />
									</td>     
	                           <td colspan="4">
										</td>
									</tr>
									<tr>					
									<td>&nbsp;</td>					
										<td colspan="4">
										 <input type="submit" id="go" name="action" value="Submit"/>
										</td>
                           </tr>
                           
                        <?php
                        }
                        else
                        {
								?>
									<tr>					
									<td>&nbsp;</td>					
										<td colspan="4">
										 <input type="submit" id="go" name="action" value="Submit"/>
										</td>
                           </tr>
								<?php                         	
                        }
                        ?>
                                  

                           </table>
                       

                        <table id="box-table-a">
                           <thead>
                           <tr>
                              <th><input type="checkbox" name="checkAll" id="checkAll" onclick="jqCheckAll(this.id, 'registerBox');"></th>
                              <th>Date</th>
                              <th>Order &#8470; </th>
                              <th>Location</th>
                              <th>Name</th>
                              <th>Status</th>
                               <th>Return/Exchange</th> 
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
                              $branchId = $_SESSION["branch_id"];
                              $curUserID = $_SESSION[_USER_ID];
                              $jur = $_SESSION[_JURISDICTION];
                              

                              if(minAccessLevel(_ADMIN_LEVEL))
                              {
                                 //$query = "select * from orders o where o.order_id != '' and  date(order_time) between '$fromdate' and '$todate'";
                                 $query = "select *,o.status as ostatus from orders o where o.order_id != ''";
                              }
                              else if(minAccessLevel(_BRANCH_LEVEL))
                              {
                                  $query = "select *,o.status as ostatus from orders o, login l, location l1 where o.user_id = l.user_id and l.location_id=l1.location_id and l1.location_id = $branchLocationId";
                              }
                                 
                              else
                              {
                                 //$query = "select * from orders o where o.user_id = " . $_SESSION[_USER_ID] . " and date(order_time) between '$fromdate' and '$todate'";
                                 $query = "select * from orders o where o.user_id = " . $_SESSION[_USER_ID] . "";
                              }


                              if($user_id_val)
                                 $query .= " and o.user_id = $user_id_val";
                              
                              if($branch_id_val)
                              	$query .= " and o.sname = '$branch_id_val'";

                              $query .= "  order by order_time desc";
                              
//                               echo "$query<BR>";
                              
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
                                    $state = db_result($res, $i, "state");
                                    $status = db_result($res, $i, "ostatus");
                                    $urllink =  _CUR_HOST . _DIR . "products/checkout.php?order_id=$order_id&action=" . _UPDATE;
                                    $returnUrl = _CUR_HOST . _DIR . "support/returns.php?action=new&order_id=$order_id&state=$state";
                                    $pdflink = _CUR_HOST . _DIR . "products/pdf.php?order_id=$order_id";
                                    $lid = db_result($res, $i, "location_id");


                                    $orderLink = "<a href='$urllink'>$order_id</a>";
                                    $returnLink =  "<a href='$returnUrl'>CREATE RETURN</a>";

                                    if($status == _DESPATCHED || $status == "PART DELIVERY" || $status == "DELIVERED")
                                    {
                                       $connoteDB = db_result($res, $i, "connote");
                                       $connoteArr = explode(";", $connoteDB);
                                       $onclick = "";
                                       for($z = 0; $z < count($connoteArr); $z++)
                                       {
                                          $connote = $connoteArr[$z];
                                          if(strlen($connote) > 2)
                                             $onclick .= "window.open(&quot;https://auspost.com.au/mypost/track/#/details/$connote&quot;);";                                             
                                         // $status = "<a href='http://auspost.com.au/track/display.asp?id=$connote&type=consignment' target='_blank'>$status</a>";
                                       }
                                    $status = "<a target='_blank' onclick='$onclick'>$status</a>";
                                    }

                           ?>
                                    <tr id="tr<?php echo $order_id?>">
                                       <td>
                                       <input type="checkbox" name="registerBox" value="<?php echo $order_id;?>"></td>
                                       <td><?php echo $date;?></td>
                                       <td><font size="3"><?php echo $orderLink;?></font></td>
                                       <td><?php echo $location;?></td>
                                       <td><?php echo $name;?></td>
                                       <td id="td<?php echo $order_id?>"><?php echo $status;?></td>
                                       <td><?php 
                                       
                                          echo $returnLink;
                                       
                                       ?></td>
                                    </tr>
                           <?php
                                 }
                              }
                           ?>
                           </tbody>
                        </table>
<?php
   if(minAccessLevel(_BRANCH_LEVEL))
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