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

   $("#staffname").focus(function(e)
   {
      $(this).select();
   });

   $("#staffname").mouseup(function(e){
      e.preventDefault();
   })

   $("#staffname").autocomplete("../products/ajaxstaffquery.php", {
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
         url: "ajaxDeleteRequest.php",
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
         url: "ajaxApproveRequest.php",
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
            &nbsp;&raquo;&nbsp;Account Details
            &nbsp;&raquo;&nbsp;Allowance
            &nbsp;&raquo;&nbsp;<strong>List Requests</strong>
         </p>
		</div>
	</div> <!-- end breacrumbsSection -->

	<div class="cAlign cFloat">
		<!-- Blog Post List -->
		<div id="mainSection">
			<ul id="articles">
				<li>
					<div class="orderContent">
						<h2>Request Top Up List</h2>
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
                              <th>Request No</th>
                              <th>Name</th>
                              <th>Amount</th>
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
                              if(minAccessLevel(_ADMIN_LEVEL))
                                 $query = "select * from requests where requests_id != '' and  date(request_date) between '$fromdate' and '$todate'";
                             else if(minAccessLevel(_BRANCH_LEVEL))
                             {
                                $branchLocation_id = $_SESSION[_LOCATION_ID];
                                $query = "select * from requests where requests_id != '' and location_id = $branchLocation_id and  date(request_date) between '$fromdate' and '$todate'";
                             }
                              else
                                 $query = "select * from requests where user_id = " . $_SESSION[_USER_ID] . " and date(request_date) between '$fromdate' and '$todate'";

                              if($user_id_val)
                                 $query .= " and user_id = $user_id_val";

                              $query .= "  order by request_date desc";
//echo "$query<BR>";
                              $res = db_query($query);
                              $num = db_numrows($res);
                              if($num > 0)
                              {
                                 for($i = 0; $i < $num; $i++)
                                 {
                                    $date = db_result($res, $i, "request_date");
                                    $dateArr = explode(" ", $date);
                                    $date = $dateArr[0];
                                    $request_id = db_result($res, $i, "requests_id");
                                    $user_id = db_result($res, $i, "user_id");
                                    $amount = db_result($res, $i, "amount");

                                    $staff = new staff();
                                    $staff->LoadStaffId($user_id);
                                    $fullname = $staff->firstname . " " . $staff->lastname;

                                    $status = db_result($res, $i, "status");
                                    $urllink =  _CUR_HOST . _DIR . "account/addrequest.php?request_id=$request_id&action=" . _UPDATE;
                                    $requestLink = "<a href='$urllink'>$request_id</a>";
                           ?>
                                    <tr id="tr<?php echo $request_id?>">
                                       <td>
                                       <input type="checkbox" name="registerBox" value="<?php echo $request_id;?>"></td>
                                       <td><?php echo $date;?></td>
                                       <td><?php echo $requestLink;?></td>
                                       <td><?php echo $fullname;?></td>
                                       <td><?php echo $amount;?></td>
                                       <td id="td<?php echo $request_id?>"><?php echo $status;?></td>
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