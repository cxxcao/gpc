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

//unset location in case user moves back and forth
unset($_SESSION["location"]);


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
         url: "ajaxDeleteLocation.php",
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
            &nbsp;&raquo;&nbsp;Location Management
            &nbsp;&raquo;&nbsp;<strong>List Location</strong>
			</p>
		</div>
	</div> <!-- end breacrumbsSection -->

	<div class="cAlign cFloat">
		<!-- Blog Post List -->
		<div id="mainSection">
			<ul id="articles">
				<li>
					<div class="orderContent">
						<h2>Location List</h2>
                  <p>
                     <form action="" method="post" id="checkoutform">
                        <table id="box-table-a">
                           <thead>
                           <tr>
                              <th><input type="checkbox" name="checkAll" id="checkAll" onclick="jqCheckAll(this.id, 'registerBox');"></th>
                              <th scope="col">Location Name</th>
                              <th scope="col">Cost Centre</th>
                              <th scope="col">Address</th>
                              <th scope="col">Suburb</th>
                              <th width="50px">State</th>
                              <th width="70px">Postcode</th>
                              <th scope="col">Phone</th>
                           </tr>
                           </thead>
                           <tbody>
                           <?php
                              $query = "select * from location";
                              $res = db_query($query);
                              $num = db_numrows($res);
                              if($num > 0)
                              {
                                 for($i = 0; $i < $num; $i++)
                                 {
                                    $location_id = db_result($res, $i, "location_id");
                                    $costcentre = db_result($res, $i, "branch_id");
                                    $business_name = db_result($res, $i, "business_name");
                                    $entity = db_result($res, $i, "entity");
                                    $name = db_result($res, $i, "sname");
                                    $stype = db_result($res, $i, "stype");
                                    $address = db_result($res, $i, "address");
                                    $suburb = db_result($res, $i, "suburb");
                                    $state = db_result($res, $i, "state");
                                    $postcode = db_result($res, $i, "postcode");
                                    $phone = db_result($res, $i, "phone");
                                    $fax = db_result($res, $i, "fax");
                                    $email = db_result($res, $i, "email");
                                    $name = "<a href='addlocation.php?action=edit&location_id=$location_id'>$name</a>";
                           ?>
                                    <tr id="tr<?php echo $location_id?>">
                                       <td><input type="checkbox" name="registerBox" value="<?php echo $location_id;?>">&nbsp;</td>
                                       <td><?php echo $name;?></td>                                       
                                       <td><?php echo $costcentre;?></td>
                                       <td><?php echo $address;?></td>
                                       <td><?php echo $suburb;?></td>
                                       <td><?php echo $state;?></td>
                                       <td><?php echo $postcode;?></td>
                                       <td><?php echo $phone;?></td>
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