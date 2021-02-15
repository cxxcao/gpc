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
else if(!minAccessLevel(_USER_LEVEL))
{
   user_logout();
   header("Location: " . _CUR_HOST. _DIR);
}

if($action != "new")
{
  // if(!$_SESSION["staff"])
   {
      $uid = _checkIsSet("user_id");
      $staff = new staff();
      $staff->LoadStaffId($uid);
      $_SESSION["staff"] = serialize($staff);
   }
   //else
   {
      $staff = unserialize($_SESSION["staff"]);
   }

}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Designs To You - DTYLink Online Ordering</title>
   <link rel="stylesheet" type="text/css" href="<?php  echo _CUR_HOST . _DIR ; ?>_css/page.css" media="screen" />
   <link rel="stylesheet" type="text/css" href="<?php  echo _CUR_HOST . _DIR ; ?>_css/table.css" media="screen" />
   <?php include('../_inc/js_css.php');?>
   
  <style>
  #dialog label, #dialog input { display:block; }
  #dialog label { margin-top: 0.5em; }
  #dialog input, #dialog textarea { width: 95%; }
  #add_tab { cursor: pointer; }
  </style>   
      


<script type="text/javascript">
$(document).ready(function()
{
   $("#loadercheckout").hide();
   $("#checkoutform").validationEngine();
   var olduserId = $("#user_name").val();
   $( "#tabs" ).tabs({ active: $("#numRules").val()-1});

   $(".clinics").fancybox({
	      'titlePosition'   : 'inside'
	});

   $("#query").autocomplete("../products/ajaxstorequery.php", {
      width: 260,
//      matchContains: true,
//      selectFirst: false,
      minChars:0,
      max:3000
   });

   $("#query").result(function(event, data, formatted) {
      var location_id = data[1];
      $("#query_val").val(data[1]);
      var address = data[2] + " " + data[3] + ", " + data[4] + " " + data[5];

      $("#addressinfo").html(address);
      //updateAlloc();
        //echo "$sname|$location_id|$address|$suburb|$state|$postcode|$phone|$fax|$email\n";
   });

   $(this).on('change', ".allocSel", function()
{
		updateNames($(this));
	});

	function updateNames(curField)
	{
		var curVal = curField.val();
		var curName = curField.attr("name");
		var str = curName.split("_");
		var numA = $("#numallowance").val();		
		
		curName = str[0] + "_" + str[1];
		curField.attr('name',curName + "_"+ curVal + "[]");

		$("#max_allowed_"+numA).attr('name',"max_allowed_" + curVal + "[]");
		$("#start_"+numA).attr('name',"start_" + curVal + "[]");
		$("#end_"+numA).attr('name',"end_" + curVal + "[]");
		
	}
   
	$(".allocClass").change(function(e){
		var curVal = $(this).val();
		var curName = $(this).attr("id");
		var tmpIdArr = $(this).attr("id").split("_");
		var idx = tmpIdArr[tmpIdArr.length-1];
		var startDate = $("#idstart_"+idx).val();
		var endDate =  $("#idend_"+idx).val();
		var newAlloc = $("#idmax_allowed_"+idx).val();
		var catType = $("#idcat_type_"+idx).val();

         
		if(endDate < startDate)
		{
			alert("Error, Allocation cannot end before it starts.");
			$(this).val("");
			return false;
		}
		
	   $.ajax(
	   	{
	    		type: "POST",
	    	   url: "ajaxCheckAllocChange.php",
	    	   data: "start=" + startDate + "&end=" + endDate + "&idx=" + idx + "&new_alloc=" + newAlloc + "&cat_type="+catType,
	    	   dataType: 'json', // expecting json
	    	   success: function(msg)
	    	   {
	    	   	if(msg.success == true)
	    	       {
	    			 //updateAlloc();
	    	       }
	    	       else
	    	       {
		    	    	 //reset val back
      			    	    	 
	    	    	   $("#idmax_allowed_"+idx).val(msg.alloc);
	    	    	   $("#idstart_"+idx).val(msg.start);
	    	    	   $("#idend_"+idx).val(msg.end);	    	    	   	    	    	   
	    	    	   $("#idcat_type_"+idx).val(msg.cat_id)
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

   
   /*
   $("#job_classification").autocomplete("../products/ajaxjobclassificationquery.php", {
      width: 260,
      matchContains: true,
      selectFirst: false,
      max:3000
   });

   $("#job_classification").result(function(event, data, formatted) {
      var title = data[0];
      var allowance_first = data[1];
      showAllowanceFields();
      $("#job_classification").val(title);
      $("#allowance1").val(allowance_first);

   });
   */

   $("#hire_date").datepicker({
      showButtonPanel: true,
      dateFormat: "yy-mm-dd"
   });

   $("#user_name").bind('blur', function(e){

      var userId = $("#user_name").val();

      $.ajax(
      {
         type: "POST",
         url: "ajaxCheckStaffUsername.php",
         data: "user_name=" + userId,
         dataType: 'json', // expecting json
         success: function(msg)
         {
            if(msg.success == true)
            {

            }
            else
            {
               alert(msg.msg);
               $('#user_name').val(olduserId);
               $('#user_name').focus();
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

   $("#user_id").bind('blur', function(e){

      var userId = $("#user_id").val();

      $.ajax(
      {
         type: "POST",
         url: "ajaxCheckStaffId.php",
         data: "user_id=" + userId,
         dataType: 'json', // expecting json
         success: function(msg)
         {
            if(msg.success == true)
            {

            }
            else
            {
               alert(msg.msg);
               $('#user_id').val(olduserId);
               $('#user_id').focus();
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

   $("#reset").click(function(e){
      var eid = $("#user_id").val();
      $("#loadercheckout").show();

      if(!confirm('Are you sure you want to reset the password?'))
      {
         $("#loadercheckout").hide();
         return false;
      }
      else
      {
         $.ajax(
         {
            type: "POST",
            url: "ajaxResetPassword.php?user_id=" + eid,
            dataType: 'json', // expecting json
            success: function(msg)
            {
               if(msg.success == true)
               {
                  alert(msg.msg);
                  $("#user_id").val("");
                  $("#firstname").val("");
                  $("#lastname").val("");
                  $("#query").val("");
                  $("#max_allowed").val("");

                  var num = $('.clonedInput').length - 1;
                  for(i = 1; i <= num; i++)
                  {
                     $("#max_allowed_" +  i).val("");
                     $("#cat_type_" +  i).val("");
                     $("#start_" + i).val("");
                     $("#end_" + i).val("");
                  }

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
      }
   });

   $(".datepickerclass").datepicker({
      showButtonPanel: true,
      dateFormat: "yy-mm-dd"
   });

   $("#daysworked").change(function(e)
	{
		//update alloc
		updateAlloc();
	});

   $("#role_id").change(function(e)
   {
      //update alloc
      updateAlloc();
   });
   
   $("#save").click(function(e)
   {
      if(!$("#checkoutform").validationEngine('validate'))
         return false;

      $("#loadercheckout").show();

      var vals = $("#checkoutform").serialize();
      //alert(vals);
      $.ajax(
      {
         type: "POST",
         url: "ajaxSaveStaff.php",
         data: vals,
         dataType: 'json', // expecting json
         success: function(msg)
         {
            if(msg.success == true)
            {
               alert(msg.msg);
               $("#user_id").val("");
               $("#user_name").val("");
               $("#firstname").val("");
               $("#lastname").val("");
               $("#query").val("");
               $("#max_allowed").val("");
               $("#cat_type").val("");               
               $("#email").val("");
               $("#job_classification").val("");
               $("#position").val("");
               $("#role_id").val("");
               $("#range").val("");

               var num = $('.clonedInput').length - 1;
               for(i = 1; i <= num; i++)
               {
                  $("#max_allowed_" +  i).val("");
                  $("#cat_type_" +  i).val("");                  
                  $("#start_" + i).val("");
                  $("#end_" + i).val("");
               }

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

   function pad(numb) {
	    return (numb < 10 ? '0' : '') + numb;
	}
   function checkDateInput(compDate) 
   {

	   //get today's date in string
	   var todayDate = new Date();
	   //need to add one to get current month as it is start with 0
	   var todayMonth = pad(todayDate.getMonth() + 1);
	   var todayDay = pad(todayDate.getDate());
	   var todayYear = todayDate.getFullYear();
	   var todayDateText = todayYear + "-" + todayMonth + "-" + todayDay;
	   var inputDateText = compDate;
	  //Convert both input to date type
	  var inputToDate = inputDateText;
	   var todayToDate =todayDateText;
	   //
	   
	  //compare dates
	   if(inputToDate > todayToDate) 
		{
			return true;
		}
	   else
		   return false;
   }
   
   function updateAlloc()
   {
		var daysworked = $("#daysworked").val(); //fulltime, parttime, casual
		var location_id = $("#query_val").val();
		var role_id = $("#role_id").val();
		var range = $("#range").val();
      var currentTabId=$("ul> .ui-tabs-active").attr('aria-controls');
      var curID = currentTabId.split("-")[1];
      
      var maxAllocationRows = 7;
      var idInc = (curID * maxAllocationRows)+1;	      

      $.ajax({
         type: "POST",
         url: "ajaxGetAllocData.php",
         data: "empstatus=" + daysworked + "&role_id=" + role_id,
         dataType: 'json', // expecting json
         success: function(msg)
         {
            if(msg.success == true)
            {
               var outer = msg.outer;
               var upper = msg.upper;
               var lower = msg.lower;
               var headwear = msg.headwear;
               var footwear = msg.footwear;
               var flame = msg.flame;
               
               $("#idmax_allowed_"+idInc).val(lower);
               idInc++;
               $("#idmax_allowed_"+idInc).val(upper);
               idInc++;
               $("#idmax_allowed_"+idInc).val(outer);
               idInc++;
               $("#idmax_allowed_"+idInc).val(flame);
               idInc++;               
               $("#idmax_allowed_"+idInc).val(headwear);
               idInc++;               
               $("#idmax_allowed_"+idInc).val(footwear);
               
            }
            else
            {
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

   var tabTitle = $( "#tab_title" );
   var editTabTitle = $( "#edit-tab_title" );   

   var dialog = $( "#dialog" ).dialog({
	      autoOpen: false,
	      modal: true,
	      buttons: {
	        Add: function() {
	        	duplicateTab();
	          $( this ).dialog( "close" );
	        },
	        Cancel: function() {
	          $( this ).dialog( "close" );
	        }
	      },
	      close: function() {
	        form[ 0 ].reset();
	      }
	    });   

   var dialogedit = $( "#dialog-edit" ).dialog({
	      autoOpen: false,
	      modal: true,
	      buttons: {
	        Update: function() {
	        	updateTabTitle($(this).data("tabid"));
	          $( this ).dialog( "close" );
	        },
	        Cancel: function() {
	          $( this ).dialog( "close" );
	        }
	      },
	      close: function() {
	        form[ 0 ].reset();
	      }
	    });    

   $(document).on("click", ".ui-icon-pencil, .tabtitle", function(e){
	   var curID = $(this).attr("id").split("-")[1];
	   var editID = "tabTileID-" + curID;	   
  
	   $( "#edit-tab_title" ).val($("#" + editID).text());
	   dialogedit.data("tabid", $(this).attr("id")).dialog( "open" );
      e.preventDefault();
	});   

   function updateTabTitle(idName)
   {
	   //console.log("idName: " + idName);
	   var curID = idName.split("-")[1];
	   var editID = "edit-" + curID;
	   var tabTitleID = "tabTileID-" + curID;	  
	   $("#" + tabTitleID).text(editTabTitle.val());
	   $("#hiddenTabTitleID-" + curID).val(editTabTitle.val());
	   //console.log(tabTitleID);
   } 
   

   function duplicateTab()
   {
	   var maxAllocationRows = 7;
	   var a = $('#input').html();
	   var numRules = parseInt($("#numRules").val());
	   var oldIdc = (numRules * maxAllocationRows)+1;
	   var newRules = numRules + 1;
		var idInc = (newRules * maxAllocationRows)+1;
	   //update
	   for(i = 0; i < maxAllocationRows; i++)
	   {
		   $("#idcat_type_" + (oldIdc+i)).attr("id", "idcat_type_" + (idInc+i));
		   $("#idmax_allowed_" + (oldIdc+i)).attr("id", "idmax_allowed_" + (idInc+i));
		   $("#idstart_" + (oldIdc+i)).attr("id", "idstart_" + (idInc+i));
		   $("#idend_" + (oldIdc+i)).attr("id", "idend_" + (idInc+i));		   
	   }	   

	   //update existing expiry dates so we can start
	   var curDate = new Date();
	   curDate.setDate(curDate.getDate()-1);
	   today = $.datepicker.formatDate('yy-mm-dd', curDate);
	   for(i = maxAllocationRows; i > 0; i--)
	   {
		   if($("#idend_" + (oldIdc-i)).val() > today)
			   $("#idend_" + (oldIdc-i)).val(today);	   

	   }

	   var tabNum = newRules -1;
	   var tabTemplate = "<span class='ui-icon ui-icon ui-icon-pencil'></span><input type='hidden' id='hiddenTabTitleID' name='hiddenTabTitleID' value=''/>";
	   
	   
	   var label = tabTitle.val(),
	        id = "tabs-" + tabNum,
	        li = $( tabTemplate.replace( /#\{href\}/g, "#" + id ).replace( /#\{label\}/g, label ) );
	        	   
	  $("#tabs").tabs('add','#tabs-' + tabNum, tabTitle.val());
	   //$("#tabs-"+tabNum).html( '<span class="ui-icon ui-icon-pencil" id="'+tabNum+'"></span>'+	 tabTitle.val());

	   $("#tabs-" + tabNum).html(a);
	   $("#numRules").val(newRules);
	   $('#tabs').tabs("refresh").tabs({ active: newRules-1});
		//console.log("new rules 1: " + newRules);
 	   $("#tabs").find( ".ui-tabs-active a" ).prepend( tabTemplate);
 	   $("#tabs").find( ".ui-tabs-active span" ).attr("id", "edit-"+tabNum);	  
	   $( ".ui-tabs-active span:nth-child(3)" ).attr("id", "tabTileID-"+tabNum);
	   $("#hiddenTabTitleID").val(tabTitle.val());
	   $("#hiddenTabTitleID").attr("name", "hiddenTabTitleID-"+tabNum);
	   $("#hiddenTabTitleID").attr("id", "hiddenTabTitleID-"+tabNum);


	   //now update newly added tab content names;
	   for(i = 0; i < maxAllocationRows; i++)
	   {
		   var nextInc = parseInt(idInc) + parseInt(i);
		   var prevInc = parseInt(oldIdc) + parseInt(i);

		   //change name;
		   var tmpType = $("#idcat_type_" + nextInc).attr("name").split("-")[1];
		   var tmpMax = $("#idmax_allowed_" + nextInc).attr("name").split("-")[1];
		   var tmpStart = $("#idstart_" + nextInc).attr("name").split("-")[1];
		   var tmpEnd = $("#idend_" + nextInc).attr("name").split("-")[1];
		   
		   $("#idcat_type_" + prevInc).attr("name", tmpType);
		   $("#idmax_allowed_" + prevInc).attr("name", tmpMax);
		   $("#idstart_" + prevInc).attr("name", tmpStart);
		   $("#idend_" + prevInc).attr("name", tmpEnd);			   
	   }	  
	   updateAlloc(); 
   }

   function disablePreviousOptions()
   {
	   $("select option[value='12']").attr('disabled', true); 
   }

   function showAllowanceFields()
   {
		var num = $('.clonedInput').length - 1;
      var newNum  = new Number(num + 1);
      $('#numallowance').val(newNum);

      var newElem = $('#input').clone().attr('id', 'input' + newNum);

      newElem.find("#max_allowed").attr('id','max_allowed_' + newNum);
      newElem.find("#cat_type").attr('id','cat_type_' + newNum);                
      newElem.find("#start").attr('id', 'start_' + newNum);
      newElem.find("#end").attr('id', 'end_' + newNum);

      if(num != 0)
      {
	      $('#input' + num).after(newElem);
      }
      else
      {
         $('#input').after(newElem);
      }
      $('#input' + newNum).show();

      $("#" + 'start_' + newNum).datepicker({
      	showButtonPanel: true,
         dateFormat: "yy-mm-dd"
      });

      $("#" + 'end_' + newNum).datepicker({
 	     showButtonPanel: true,
        dateFormat: "yy-mm-dd"
      });
         	
      $('#btnDel').removeAttr('disabled');
   }
   
   $('#btnAdd').click(function(e) {
	   dialog.dialog( "open" );
   	
      ///showAllowanceFields();
      e.preventDefault();
   });

   var form = dialog.find( "form" ).on( "submit", function( event ) {
		 duplicateTab();
	      dialog.dialog( "close" );
	      event.preventDefault();
	    });
      

   $('#btnDel').click(function(e) {
	   var active = $( "#tabs" ).tabs( "option", "active" );
	   $( "#tabs" ).tabs('remove',$( "#tabs" ).tabs('option','selected'));
	   var numRules = parseInt($("#numRules").val()) - 1;
	   $("#numRules").val(numRules);
	      e.preventDefault();
	   });


            
            $('#btnDel-old').click(function(e) {
                var num = $('.clonedInput').length - 1; // how many "duplicatable" input fields we currently have
	        		var idx = num; //the last element
	        		var startDate = $("#start_"+idx).val();
	        		var endDate =  $("#end_"+idx).val();
	        		var newAlloc = 0;
	        		var catType = $("#cat_type_"+idx).val();
	
	        		if(endDate < startDate)
	        		{
	        			alert("Error, Allocation cannot end before it starts.");
	        			$(this).val("");
	        			return false;
	        		}
	        		
	        	   $.ajax(
	        	   	{
	        	    		type: "POST",
	        	    	   url: "ajaxCheckAllocChange.php",
	        	    	   data: "start=" + startDate + "&end=" + endDate + "&idx=" + idx + "&new_alloc=" + newAlloc + "&cat_type="+catType,
	        	    	   dataType: 'json', // expecting json
	        	    	   success: function(msg)
	        	    	   {
	        	    	   	if(msg.success == true)
	        	    	       {
	        	                $('#input' + num).remove();     // remove the last element
	        	                $('#numallowance').val(num-1);	
	        	    	       }
	        	    	       else
	        	    	       {
	        		    	    	 //reset val back
	        	    	    	   $("#max_allowed_"+idx).val(msg.alloc);
	        	    	    	   $("#start_"+idx).val(msg.start);
	        	    	    	   $("#end_"+idx).val(msg.end);	    	    	   	    	    	   
	        	    	    	   $("#cat_type_"+idx).val(msg.cat_id)
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
                
                e.preventDefault();
            });

});


</script>
</head>
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
            &nbsp;&raquo;&nbsp;Staff Management
            &nbsp;&raquo;&nbsp;<strong>Add Staff</strong>
			</p>
		</div>
	</div> <!-- end breacrumbsSection -->

   <!-- ADD WITH TAB TITLE -->
<div id="dialog" title="Entitlement Name">
  <form>
    <fieldset class="ui-helper-reset">
      <label for="tab_title">Title</label>
      <input type="text" name="tab_title" id="tab_title" value="Tab Title" class="ui-widget-content ui-corner-all">
    </fieldset>
  </form>
</div>   
   
   
   <!-- EDIT TAB TITLE -->
<div id="dialog-edit" title="Change Entitlement Name">
  <form>
    <fieldset class="ui-helper-reset">
      <label for="edit-tab_title">Title</label>
      <input type="text" name="edit-tab_title" id="edit-tab_title" value="Tab Title" class="ui-widget-content ui-corner-all">
    </fieldset>
  </form>
</div>   

	<div class="cAlign cFloat">
		<!-- Blog Post List -->
		<div id="mainSection">
			<ul id="articles">
				<li>
					<div class="articleContent">
                  <?php
                  if($action == "edit" || $action == _UPDATE)
                  {
                     $user_name = $staff->user_name;
                     $user_id = $staff->user_id;
                     $firstname = $staff->firstname;
                     $lastname = $staff->lastname;
                     $location_id = $staff->location_id;
                     $location = new location();
                     $location->LoadLocationId($location_id);
                     $sname = $location->branch_id . " " . $location->sname;
                     $allowance = $staff->allowance;
                     $role_id = $staff->role_id;
                     $defstatus = $staff->status;
                     $defAccessLevel = $staff->access_level;
                     $email = $staff->email;
                     $business_name = $staff->job_classification;
                     $hire_date = $staff->hire_date;
                     
                     $staff->loadRules($user_id);
                     $staff->loadCoordinatorLocation($user_id);
                     
                     $orders = new orders();
                     $orders->allocationObj->getMaxAllocations($user_id, "");
                     
                     $clinicURL = _CUR_HOST ._DIR . "account/clinics.php?sid= $user_id";
                     
                     //$coord_loc = $staff->implodeCoord();
                     
                    //echo "CL: $coord_loc<BR>"; 
                  ?>
                  <h2>Edit Staff</h2>
                  <?php
                  }
                  else
                  {
                     $user_name = "";
                     $user_id = "";
                     $coord_loc = "";
                  ?>
                  <h2>Add Staff</h2>
                  <?php

                  }
                  ?>
                        <?php 
                        $readonly = "readonly";
                        if(minAccessLevel(_BRANCH_LEVEL))
                        	$readonly = "";
                        ?>                  
                  <p>
                     <form action="" method="post" id="checkoutform">
                     <div class="formrow">
                        <label>Status</label>
                        <span class="formwrap">
                          <?php

                           $status = _checkIsSet("status");
                           if(!$status)
                              $status = $defstatus;

                           generateStaticComboSimple($staffStatusArr, $status, "status", true);?>
                        </span>
                     </div>
                     <div class="formrow">
                        <label>Access Level</label>
                        <span class="formwrap">
                          <?php
			
                           $accessLevel = _checkIsSet("access_level");
                           if(!$accessLevel)
                              $accessLevel = $defAccessLevel;
									if(minAccessLevel(_BRANCH_LEVEL))
	                           generateStaticComboSimple($accessArr, $accessLevel, "access_level", true);
	                        else
	                        {
	                        ?>
                           <input type="text" name="access_level_name" id="access_level_name" value="<?php echo $accessArr[$accessLevel];?>" readonly/>
                           <input type="hidden" name="access_level" id="access_level" value="<?php echo $accessLevel;?>"/>                           
	                        <?php 
	                        }
	                        ?>
                        </span>
                     </div>

                     <div class="formrow">
                        <label>DTYLink ID</label>
                        <span class="formwrap">
                           <input type="text" name="user_id" id="user_id" value="<?php echo $user_id;?>" placeholder="Automatically Generated" readonly/>
                        </span>
                     </div>
                     <div class="formrow">
                        <label>Employee ID/Username</label>
                        <span class="formwrap">
                           <input type="text" name="user_name" id="user_name" value="<?php echo $user_name;?>" <?php echo $readonly;?>/>
                        </span>
                     </div>
                     <div class="formrow">
                        <label>First Name</label>
                        <span class="formwrap">
                           <input class="validate[required]" type="text" name="firstname" id="firstname" value="<?php echo $firstname;?>" <?php echo $readonly;?>/>
                        </span>
                     </div>
                     <div class="formrow">
                        <label>Last Name</label>
                        <span class="formwrap">
                           <input class="validate[required]" type="text" name="lastname" id="lastname" value="<?php echo $lastname;?>" <?php echo $readonly;?>/>
                        </span>
                     </div>
                     <div class="formrow">
                        <label>Email</label>
                        <span class="formwrap">
                           <input class="validate[optional,custom[email]]" type="text" name="email" id="email" value="<?php echo $email;?>" <?php echo $readonly;?>/>
                        </span>
                     </div>
                     <div class="formrow">
                        <label>Hire Date</label>
                        <span class="formwrap">
                           <input class="validate[required,custom[date]]" type="text" name="hire_date" id="hire_date" value="<?php echo $hire_date;?>" <?php echo $readonly;?>/>
                        </span>
                     </div>
                     <div class="formrow">
                        <label>Location</label>
                        <span class="formwrap">
                           <input class="validate[required] query" type="text" name="q" id="query" value="<?php echo $sname;?>"/><span id="addressinfo"></span>
                           <input type="hidden" name="query_val" id="query_val" value="<?php echo $location_id;?>"/>
                        </span>
                     </div>                      
                     <div class="formrow">
                        <label>Range</label>
                        <span class="formwrap">
                           <?php
                           $rangeArr = array(1=>"Womenswear", 2=> "Menswear");
                           $range = _checkIsSet("range");
                           if(!$range)
                              $range = $staff->crange;

                           generateStaticComboSimple($rangeArr, $range, "range", true);?>
                        </span>
                     </div>
                           <?php
                           if(minAccessLevel(_BRANCH_LEVEL)) 
                           {
                              ?>
                     <div class="formrow">
                        <label>Uniform Type</label>
                        <span class="formwrap">
                        <?php 
                              $role_id = _checkIsSet("role_id");
                              if(!$role_id)
                              {
                                 $role_id = $staff->role_id;

                              }
                              
                             // generateDropDownRoleNZ(role_id, "select name, employeerole_id from employee_role", $role_id, true, $staff->isAUS );
                                 
                              
                              generateStaticComboSimple($employeeRoleIDArr, $role_id, "role_id", true);
                              
//                            generateStaticComboSimple($divisionArr, $role_id, "role_id", true);
                        ?>
                        </span>
                     </div>                              
                     <?php 
                           }
                           else 
                           {
                           ?>
                             <input type="hidden" name="role_id" id="role_id" value="<?php echo $role_id;?>"/> <!--  default ID -->   
                           <?php 
                           }
                           ?>

                                          
                     <div class="formrow">
                        <label>Australian Based</label>
                        <span class="formwrap">
                           <?php
                           $isAUS = _checkIsSet("isAUS");
                           if(!$isAUS)
                              $isAUS = $staff->isAUS;

                           generateStaticCombo($yesNoArr, $isAUS, "isAUS", true);?>
                        </span>
                     </div>
                     
                     <div class="formrow">
                        <label>Days Worked</label>
                        <span class="formwrap">
                           <?php
                           $daysworked = _checkIsSet("daysworked");
                           if(!$daysworked)
                              $daysworked = $staff->daysworked;

                           generateStaticComboSimple($daysWorkedArr, $daysworked, "daysworked", true);?>
                        </span>
                     </div>                     

                     <!--
                     <div class="formrow">
                        <label>Allowance</label>
                        <span class="formwrap">
                           <input class="validate[required,custom[number]]" type="text" name="allowance" id="allowance" value="<?php echo $allowance;?>" />
                        </span>
                     </div>
                     -->
                     
                     <?php 
	                     $query = "select * from rules where user_id = $user_id order by rule_type";
	                     $res = db_query($query);
	                     $num = db_numrows($res);
	                     
	                     
                     ?>
                     
                     <div class="staffformrow">
                        <div id="tabs" class="mainTabs">
  <ul>
  	<?php 
  	//get num of rules
  	
  	$numRules = $staff->numRules($user_id);
  	for($i = 0; $i < $numRules; $i++)
  	{
  		$tabID = "#tabs-" . $i;
  		$tabName = "tabname-" .$i;
  		$tabEditID = "#edit-" . $i;
  		$tabTitleID = "tabTileID-" . $i;
  		$hiddenTabTitleID = "hiddenTabTitleID-" .$i;
  		$tabTileVal = $staff->ruleTitle($user_id, $i+1);
  	?>
    <li>
    
    <a name="<?php echo $tabName;?>" href="<?php echo $tabID;?>">
    <span class="ui-icon ui-icon-pencil" id="<?php echo $tabEditID;?>"></span>
    <span class="tabtitle" id="<?php echo $tabTitleID;?>"><?php echo $tabTileVal;?> </span>
    <input type="hidden" id="<?php echo $hiddenTabTitleID;?>" name="<?php echo $hiddenTabTitleID;?>" value="<?php echo $tabTileVal;?>"/>
    </a>
    	

    </li>
    
    <?php 
  	}
    ?>
  </ul>
  <input type="hidden" name="numRules" id="numRules" value="<?php echo $numRules;?>"/>
						<?php 
						$k=1;
						//$disableSelectOptions = array(_COMBINED_GARMENT_TYPE_2017);
						  	for($r = 0; $r < $numRules; $r++)
  							{
								$tabID = "tabs-".$r;  								
						?>						   
						                       
                          <div id="<?php echo $tabID;?>">
	                           <span class="formwrap">
	                        <?php
	                           //$len = count($staff->rulesArr);
	                           //$catIDArr = array("1","2","3","5","7","8","11",_COMBINED_GARMENT_TYPE_2017,_COMBINED_GARMENT_TYPE);
	                           
	                           $catIDquery = "select cat_id from category where inactive = 'N'";
	                           $catRes = db_query($catIDquery);
	                           $len = db_numrows($catRes);
	                           
	                           //$len = count($catIDArr);
	                           for($i = 1; $i <= $len; $i++,$k++)
	                           {
	                           	
	                           	$curCatID = db_result($catRes, $i-1, "cat_id");
	                           	
	                           	$ruleKey = $curCatID . "_" . ($r+1);
// 	                           	echo "KEY: $ruleKey<BR>";
	                           	if(array_key_exists($ruleKey,$staff->rulesArr))
	                           	{
		                              $cat_type = $staff->rulesArr[$ruleKey]->cat_type;
		                              $max_allowed = $staff->rulesArr[$ruleKey]->max_allowed;
		                              $startdate = $staff->rulesArr[$ruleKey]->start;
		                              $enddate = $staff->rulesArr[$ruleKey]->end;
	                           	}
	                           	else
	                           	{
	                           		$max_allowed = 0;
	                           		$cat_type = $curCatID;
		                           	$startdate = "2019-01-01";//default
		                           	$enddate = "2020-01-02";//default
	                           	}
	                           	
		                              $catTypeName = "cat_type_" . $curCatID . "[]";
		                              $maxAllowedName = "max_allowed_" . $curCatID . "[]";
		                              $startName = "start_" . $curCatID . "[]";
		                              $end_Name = "end_" . $curCatID . "[]";
		                              
		                              $catTypeID = "idcat_type_$k";
		                              $maxID = "idmax_allowed_$k";
		                              $startID = "idstart_$k";
		                              $endID = "idend_$k";	                	                           	
	                              
	                        ?>
	
	                              <div id="input<?php echo $k;?>" class="clonedInput tabEntitlement">
				                          <?php 
				                          if(minAccessLevel(_BRANCH_LEVEL))
				                          {
					                          generateStaticComboID($categoryArr, $cat_type, $catTypeName,$catTypeID, true);
				                          ?>                              
			                                 &nbsp;Max Allowed:&nbsp;<input class="validate[required,custom[number]] allocClass" type="text" name="<?php echo $maxAllowedName;?>" id="<?php echo $maxID;?>" value="<?php echo $max_allowed;?>"/>
	      		                           &nbsp;Start:&nbsp;<input class="validate[required,custom[date]] datepickerclass allocClass" type="text" name="<?php echo $startName;?>" id="<?php echo $startID;?>" value="<?php echo $startdate;?>"/>
	            		                     &nbsp;Expiry:&nbsp;<input class="validate[required,custom[date]] datepickerclass allocClass" type="text" name="<?php echo $end_Name;?>" id="<?php echo $endID;?>" value="<?php echo $enddate;?>"/>
	           		                    <?php 
				                          }
				                          else
				                          {
				                          ?>
				                          
                 									<input class="" type="text" name="" id="" value="<?php echo  $categoryArr[$cat_type];?>" <?php echo $readonly;?>/>									
			                                 &nbsp;Max Allowed:&nbsp;<input class="validate[required,custom[number]] allocClass" type="text" name="<?php echo $maxAllowedName;?>" id="<?php echo $maxID;?>" value="<?php echo $max_allowed;?>" <?php echo $readonly;?>/>
	      		                           &nbsp;Start:&nbsp;<input class="validate[required,custom[date]] datepickerclass allocClass" type="text" name="<?php echo $startName;?>" id="<?php echo $startID;?>" value="<?php echo $startdate;?>" <?php echo $readonly;?>/>
	            		                     &nbsp;Expiry:&nbsp;<input class="validate[required,custom[date]] datepickerclass allocClass" type="text" name="<?php echo $end_Name;?>" id="<?php echo $endID;?>" value="<?php echo $enddate;?>" <?php echo $readonly;?>/>				                          
				                          <?php 
				                          }
	           		                    ?>
	                              </div>
	                        <?php
	                           }
									?>	     
	                        </span>
	                        </div><!-- tab1 -->						                      
	      	           <?php           
  								}//number of rules
  								?>
  							
	                           <div style="display:none;" id="input" class="clonedInput">
	                           
	                        <?php
// 	                           	$catIDArr = array("1","2","3","5","7","8","11",_COMBINED_GARMENT_TYPE);
// 		                           $len = count($catIDArr);
		                           $i = $k ;
   	                           $catIDquery = "select cat_id from category where inactive = 'N'";
   	                           $catRes = db_query($catIDquery);
   	                           $len = db_numrows($catRes);		                           
		                           for($j = 1; $j <= $len; $i++,$j++)
		                           {
		                              $cat_type = db_result($catRes, $j-1, "cat_id");
		                              $max_allowed = 0;
		                              $startdate = date('Y-m-d');
		                              $enddate = date('Y-m-d', strtotime("+1 year")); //2 year cycle for sports med

		                              $catTypeID = "cat_type_$i";
		                              $catTypeName = "-cat_type_" . $cat_type . "[]";
		                              $maxAllowedName = "-max_allowed_" . $cat_type . "[]";
		                              $startName = "-start_" . $cat_type . "[]";
		                              $end_Name = "-end_" . $cat_type . "[]";
		                              
		                              $catTypeID = "idcat_type_$i";
		                              $maxID = "idmax_allowed_$i";
		                              $startID = "idstart_$i";
		                              $endID = "idend_$i";		                              
		                        ?>
		
		                              <div id="input<?php echo $i;?>" class="clonedInput">
					                          <?php generateStaticComboID($categoryArr, $cat_type, $catTypeName,$catTypeID, true);?>                              
		                                 &nbsp;Max Allowed:&nbsp;<input class="validate[required,custom[number]] allocClass" type="text" name="<?php echo $maxAllowedName;?>" id="<?php echo $maxID;?>" value="<?php echo $max_allowed;?>"/>
		                                 &nbsp;Start:&nbsp;<input class="validate[required,custom[date]] datepickerclass allocClass" type="text" name="<?php echo $startName;?>" id="<?php echo $startID;?>" value="<?php echo $startdate;?>"/>
		                                 &nbsp;Expiry:&nbsp;<input class="validate[required,custom[date]] datepickerclass allocClass" type="text" name="<?php echo $end_Name;?>" id="<?php echo $endID;?>" value="<?php echo $enddate;?>"/>
		                              </div>
		                        <?php           
		                           }
	                        ?> 	                           
	                           </div><!-- clonedInput display:none -->
	                           <!-- 
	                          <?php generateStaticCombo($categoryArr, "", "cat_type", true);?>
	                              &nbsp;Max Allowed:&nbsp; <input class="validate[required,custom[number]] allocClass" type="text" name="max_allowed" id="max_allowed" />
	                              &nbsp;Start:&nbsp;<input class="validate[required,custom[date]] allocClass" type="text" name="startdate" id="start" class="startdate" />
	                              &nbsp;Expiry:&nbsp;<input class="validate[required,custom[date]] allocClass" type="text" name="enddate" id="end" class="enddate" />
	                           </div>
	                            -->

	                        
                       </div> <!-- tabs -->
                        
                     </div>

                     <?php
                     if(minAccessLevel(_BRANCH_LEVEL))//only admins can add staff, branch managers can view
                     {
                     ?>
                     <div class="staffformbtnrow">
                        <label></label>
                        <span class="formwrap">
                              <input type="hidden" name="numallowance" id="numallowance" value="<?php echo $i-1;?>">
                              <?php 
                             	if(minAccessLevel(_BRANCH_LEVEL))
                             	{
                              ?>
                              <input type="submit" id="btnAdd" value="Add Allocation" />
                              <input type="submit" id="btnDel" value="Remove Allocation" />
			                     <?php 
		                     	}
			                     ?>                              
                        </span>
                     </div>

                     <div class="formrow">
                        <label></label>
                        <span class="formwrap">
                          <input type="submit" id="save" name="save" value="Submit"/>
                          <!-- <input type="submit" id="reset" name="reset" value="Reset Password"/> -->
                          <div id="loadercheckout"><img src="../_img/fbloader.gif" alt="loading..."/></div>
                        </span>
                     </div>
                     <?php
                     }
                     ?>
                     </form>

                  <p>
                  DTYLink v2.0
                  </p>

					</div>
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