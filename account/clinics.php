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
$sid = _checkIsSet("sid");

?>


<script type="text/javascript">
{
   $("#articleCommentForm").validationEngine();
   $('#box-table-a').dataTable({
      "aLengthMenu": [[-1,10, 25, 50, 100], ["All", 10, 25, 50, 100]],
      "iDisplayLength":25,
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

   $(".uniformCoord").change(function(e){
		var lid = $(this).val();
		var sid = <?php echo $sid;?>;
		var isUC = false;

		if($(this).checked)
			isUC = true;

	   $.ajax(
   	{
    		type: "POST",
    	   url: "ajaxToggleUC.php",
    	   data: "lid=" + lid + "&sid=" + sid + "&isUC=" + isUC,
    	   dataType: 'json', // expecting json
    	   success: function(msg)
    	   {
    	   	if(msg.success == true)
    	       {
    	   		alert(msg.msg);
    	       }
    	       else
    	       {
    	    	    alert(msg.msg);
    	    	    return false;
    	       }
    	   },
    	   failure: function(msg)
    	   {
    	   	alert('Error!');
    	      return false;
    	   }
    	});		
	});   

	function toogleUC(lid)
	{
		//var lid = $(this).val();
		var sid = <?php echo $sid;?>;
		var isUC = false;

		if($("#coordBox_" + lid).is(":checked"))
			isUC = true;

	   $.ajax(
   	{
    		type: "POST",
    	   url: "ajaxToggleUC.php",
    	   data: "lid=" + lid + "&sid=" + sid + "&isUC=" + isUC,
    	   dataType: 'json', // expecting json
    	   success: function(msg)
    	   {
    	   	if(msg.success == true)
    	       {
    	   		alert(msg.msg);
    	       }
    	       else
    	       {
    	    	    alert(msg.msg);
    	    	    return false;
    	       }
    	   },
    	   failure: function(msg)
    	   {
    	   	alert('Error!');
    	      return false;
    	   }
    	});		
	}

}
</script>

<body>


	<div id="clinic-z" class="cAlign cFloat">
		<!-- Blog Post List -->
		<div id="mainSection1">
			<ul id="articles">
				<li>
					<div class="orderContent">
						<h2>Clinics</h2>
                  <p>
                     <form action="" method="post" id="checkoutform">
                        <table id="box-table-a">
                           <thead>
                           <tr>
                              <th><input type="checkbox" name="checkAll" id="checkAll" onclick="jqCheckAll(this.id, 'registerBox');"></th>
                              <th scope="col">Brand</th>
                              <th scope="col">Name</th>
                              <th scope="col">Cost Centre</th>
                              <th scope="col">Address</th>
                              <th scope="col">Suburb</th>
                              <th width="50px">State</th>
                              <th width="70px">Postcode</th>
                           </tr>
                           </thead>
                           <tbody>
                           <?php
                              //$query = "select * from location";
                           if(minAccessLevel(_ADMIN_LEVEL))
                           {
                              $query = "select * from location l left join coordinators c on l.location_id = c.location_id and user_id = $sid order by coordinators_id desc";
  //echo "$query<BR>";
                              
                              $res = db_query($query);
                              $num = db_numrows($res);
                           }
                           else 
                           	$num = 0;
                              if($num > 0)
                              {
                                 for($i = 0; $i < $num; $i++)
                                 {
                                 	$coordinator_id = db_result($res, $i, "coordinators_id");
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
                                    
                                    $isUC = "";
                                    if($coordinator_id != null)
	                                    $isUC = "checked";
                           ?>
                                    <tr id="tr<?php echo $location_id?>">
                                       <td><input onclick="toogleUC(<?php echo $location_id;?>);" class="uniformCoord1" type="checkbox" name="registerBox" id="coordBox_<?php echo $location_id;?>" value="<?php echo $location_id;?>" <?php echo $isUC;?>/>&nbsp;</td>
                                       <td><?php echo $business_name; ;?></td>
                                       <td><?php echo $name;?></td>                                       
                                       <td><?php echo $costcentre;?></td>
                                       <td><?php echo $address;?></td>
                                       <td><?php echo $suburb;?></td>
                                       <td><?php echo $state;?></td>
                                       <td><?php echo $postcode;?></td>
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


			</ul>


		</div> <!-- end mainSection -->



	</div>
