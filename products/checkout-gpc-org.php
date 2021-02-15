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
require_once('ordersclass.php');
require_once('productsclass.php');
require_once('../account/locationclass.php');

$action = _checkIsSet("action");

if(!user_isloggedin())
{
   header("Location: " . _CUR_HOST. _DIR);
}

if($action == _UPDATE)
{
   $orders = new orders();
   $orders->LoadOrder();
   $_SESSION['order'] = serialize($orders);
}

//5123456789012346 05/21 123
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="utf-8">
   <title>Designs To You - DTYLink Online Ordering</title>
   <?php include('../_inc/js_css.php');?>
</head>

<script type="text/javascript">
$(document).ready(function()
{

   $(".checkout").hide();
   $("#loaderdel").hide();
   $("#results").hide();
   $("#creditcardform").hide();
   $("#wagededuction").hide();
   $(document).ajaxStop($.unblockUI);
   $("#receipt").hide();

   var payableAmt = "0";
   var defSID = $("#sname").val();
   var defSname = $("#sname option:selected").text();
   $("#query_val").val(defSID);
   $("#q").val(defSname);

   if($("#amount").length)
      payableAmt = $("#amount").val();

   var payableAmt = Number(payableAmt.replace(/[^0-9\.]+/g,""));

   if(payableAmt >0)
   {
	      $("#paymentRequiredText").show();
	      $("#paymentNotRequiredText").hide();
	      $("#trpayable").show();

	      if($("#paymentopt").val() == "W")
	      {
	         $("#wagededuction").show();
	         $("#creditcardform").hide();
	         $("#paypalopt").hide();

	         {
	            $("#remainingPayablePaymentOpt").hide();
	         }

	      }
	      else if($("#paymentopt").val() == "C")
	      {
	         $("#creditcardform").show();
	         $("#wagededuction").hide();
	         $("#paypalopt").hide();
	      }
	      else if($("#paymentopt").val() == "P")
	      {
	         $("#creditcardform").hide();
	         $("#wagededuction").hide();
	         $("#paypalopt").show();
	      }


   }
   else
   {
      $("#paymentRequiredText").hide();
      $("#paymentNotRequiredText").show();
      $("#trpayable").hide();
   }

   $("#numPays").change(function(e){
         var payable = $("#orgAmount").val();
         UpdateNumPays(payable);
         //$("#remainingPayable").val(parseFloat(remainingPayable).toFixed(2)); //still stays the same!
      });   

   function UpdateNumPays(amtPayable)
   {
      var numPays = $("#numPays").val();
      var amtperpay = 0;

      if(numPays.length > 0)
      {
          amtperpay = parseFloat(amtPayable) / parseFloat(numPays);
      }
      $("#amountperpay").val(parseFloat(amtperpay).toFixed(2));
   }   

   $("#query").autocomplete("ajaxstorequery.php", {
	      width: 260,
	      matchContains: true,
	      selectFirst: false
	   });

   $("#query").result(function(event, data, formatted) {
	      var location_id = data[1];
	      var address = data[2];
	      var suburb = data[3];
	      var state = data[4];
	      var postcode = data[5];
	      var phone = data[6];
	      var fax = data[7];
	      var email = data[8];
	      var country = data[9];

	      $("#query_val").val(data[1]);
	      $("#address").val(address);
	      $("#suburb").val(suburb);
	      $("#state").val(state);
	      $("#postcode").val(postcode);
	      $("#phone").val(phone);
	   });   

   $("#wage").click(function(e){
      e.preventDefault();

      $("#paymentopt").val("W");
      $("#iswage").val(true);
      $("#cardtype").removeClass("validate[required]");
      $("#cardname").removeClass("validate[required]");
      $("#cardnumber").removeClass("validate[required,custom[number]]");
      $("#expiry").removeClass("validate[required]");
      $("#wagededuction").fadeIn();
      $("#creditcardform").fadeOut();
      $(".formError").remove();
   });
   $("#creditcard").click(function(e){
      e.preventDefault();

      $("#paymentopt").val("C");
      $(".checkout").hide();
      $("#iswage").val(false);
      $("#cardtype").addClass("validate[required]");
      $("#cardname").addClass("validate[required]");
      $("#cardnumber").addClass("validate[required,custom[number]]");
      $("#expiry").addClass("validate[required]");
      $("#wagededuction").fadeOut();
      $("#creditcardform").fadeIn();
      $(".formError").remove();
   });

   $(".prodsizeclass").change(function(e){
	
     var idSplit   = (this.id).split("_");
     var prod_id = idSplit[1];
     if($(this).val().length == 0)
     {
         alert("Select a size");
         return false;
     }
     
      var size = $(this).val();	   
	   var vals= "";

      <?php
      if($orders->action == "UPDATE")
	   {
	   ?>
	      vals = "prod_id=" + prod_id + "&size=" + size + "&order_id=" + <?php echo $orders->order_id;?>;
     <?php
 		}
		else 
		{
	  ?>
      	var vals = "";
      <?php
		}
      ?>

	   $.ajax(
			   	{
			    		type: "POST",
			    	   url: "ajaxToggleSize.php",
			    	   data: vals,
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
   
   $(".delline").click(function(e)
   {
      var idSplit   = (this.id).split("_");
      var prod_id = idSplit[1];
      var size = idSplit[2];
      $("#loaderdel").show();
      var vals= "prod_id=" + prod_id + "&size=" + size + "&action=delline";

      $.ajax(
      {
         type: "POST",
         url: "ajaxUpdateLineItem.php",
         data: vals,
         dataType: 'json', // expecting json
         success: function(msg)
         {
            if(msg.success == true)
            {
               $("#tr_"+prod_id+"_"+size).slideToggle('slow');
               var newPayable = parseFloat(msg.payable).toFixed(2);
               var newTotal = parseFloat(msg.grandtotal).toFixed(2);
               var newRemaining = parseFloat(msg.remaining).toFixed(2);
               var newAllowance = parseFloat(msg.allowance).toFixed(2);

               //$("#tdpayable").text(newPayable);
               //$("#bpayable").text(newPayable);

               newPayable = parseFloat(newPayable).toFixed(2);

               $("#tdpayable").text("$" + newPayable + " (inc GST)");
               $("#bpayable").text("$" + newPayable + " (inc GST)");
               $(".payable").val(newPayable);

               $("#remaining").text("$" + newRemaining);
               $("#orderTotal").text("$" + newTotal);
               $("#tdallowance").text("$" + newAllowance);
               $("#amount").val(newPayable);

               if(newPayable > 0)
               {
                  $("#paymentRequiredText").show();
                  $("#paymentNotRequiredText").hide();
                  $("#trpayable").show();
               }
               else
               {
                  $("#paymentRequiredText").hide();
                  $("#creditcardform").hide();
                  $("#paymentNotRequiredText").show();
                  $("#trpayable").hide();
               }


               //alert(newTotal);
               //   alert("new payable: " + newPayable);

            }
            else
            {
               $(".checkout").hide();
               return false;
            }
            $("#loaderdel").hide();
         },
         failure: function(msg)
         {
            $("#loaderdel").hide();
            alert('Error!');
            return false;
         }
      });
   });


   $(".qtyinput").change(function (e)
   {
      var idSplit   = (this.id).split("_");
      var prod_id = idSplit[1];
      var size = idSplit[2];
      var qty = $(this).val();
      $("#loaderdel").show();

      var vals= "prod_id=" + prod_id + "&size=" + size + "&qty=" + qty + "&action=updateqty";
//      alert(vals);
      $.ajax(
      {
         type: "POST",
         url: "ajaxUpdateLineItem.php",
         data: vals,
         dataType: 'json', // expecting json
         success: function(msg)
         {
            if(msg.success == true)
            {
               var newPayable = parseFloat(msg.payable).toFixed(2);
               var newTotal = parseFloat(msg.grandtotal).toFixed(2);
               var newSubtotal = parseFloat(msg.linetotal).toFixed(2);
               var newRemaining = parseFloat(msg.remaining).toFixed(2);
               var newAllowance= parseFloat(msg.allowance).toFixed(2);

               newPayable = parseFloat(newPayable).toFixed(2);
               $("#tdpayable").text("$" + newPayable + " (inc GST)");
               $("#bpayable").text("$" + newPayable + " (inc GST)");

               $(".payable").val(newPayable);

               if(newRemaining < 0)
               {
                  newRemaining = 0;
                  newRemaining = parseFloat(newRemaining).toFixed(2);
               }

               $("#sub_" +prod_id + "_" + size).text("$" + newSubtotal);
               $("#remaining").text("$" + newRemaining);
               $("#orderTotal").text("$" + newTotal);
               $("#tdallowance").text("$" + newAllowance);
               $("#amount").val(newPayable);
               if(newPayable > 0)
               {
                  $("#paymentRequiredText").show();
                  $("#paymentNotRequiredText").hide();
                  $("#trpayable").show();
                  $("#creditcardform").show();
                  
               }
               else
               {
                  $("#paymentRequiredText").hide();
                  $("#paymentNotRequiredText").show();
                  $("#trpayable").hide();
                  $("#wagededuction").hide();
                  $("#creditcardform").hide();
               }
            }
            else
            {
               $(".checkout").hide();
               return false;
            }
            $("#loaderdel").hide();
         },
         failure: function(msg)
         {
            $("#loaderdel").hide();
            alert('Error!');
            return false;
         }
      });

   });

   $(".approve").click(function(e)
		   {
		      $("#loadercheckout").show();
		      var appItems = [];
		      <?php
		      if($orders->action == "UPDATE")
		      {
		      ?>
		      var param = 'itemArr[]=' + <?php echo $orders->order_id;?>;
		      <?php
				}
				else {
		      
		      ?>
		      var param = "";
		      <?php
				}
		      ?>

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
							alert('Order Approved');
							$("#curStatus").html("APPROVED");
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
		   });

   $(".confirm").click(function(e)
   {
      if(!$("#checkoutform").validationEngine('validate'))
         return false;

      $.blockUI({ css: {
         border: 'none',
         padding: '15px',
         backgroundColor: '#000',
         '-webkit-border-radius': '10px',
         '-moz-border-radius': '10px',
         opacity: .5,
         color: '#fff'
      } });

      $(".checkout").show();
      $(".confirm").hide();

     var cardnumber = $("#cardnumber").val();
     var vals = $("#checkoutform").serialize();

     <?php 
     if(minAccessLevel(_ADMIN_LEVEL))
     {
     ?>
       var bypass = true;
     <?php 
	  }
	  else 
	  {
     ?>     
     var bypass = false;     
     <?php 
	  }
     ?>
     //alert(vals);

     if(cardnumber.length > 0 && bypass == false)
     {
        $("#expiry").val($("#year").val() + ''+ $("#month").val());
        var amt = $("#amount").val()*100;

        <?php
        if(_MERCHANTID == "TESTANZDESIGNSTO")
        {
        ?>
         amt = 1000;
         //alert('paying with CC! amt: ' + amt);
       <?php
        }
        ?>
        amt = parseInt(amt);
        var cardtype = $("#cardtype").val();
        var cardname = $("#cardname").val();

        var expiry = $("#expiry").val();
        var securitycode = $("#securitycode").val();
        var txnRef = "GPC: " + $("#fullname").val();
        var ccval = "virtualPaymentClientURL=https://migs.mastercard.com.au/vpcdps&vpc_Version=1&vpc_Command=pay&vpc_AccessCode=<?php echo _ANZ_ACCESS_CODE;?>&vpc_MerchTxnRef="+txnRef+"&vpc_Merchant=<?php echo _MERCHANTID;?>&vpc_OrderInfo=GPC&vpc_Amount="+amt+"&vpc_CardNum="+cardnumber+"&vpc_CardExp="+expiry+"&vpc_CardSecurityCode="+securitycode;
//      alert(vals);

         $.ajax(
         {
            type: "POST",
            url: "ajaxCCTransaction.php",
            data: ccval,
            dataType: 'json', // expecting json
            success: function(msg)
            {
               var txnResponseCode = msg.success;
               var responseDesc = msg.msg;
               var receipt = msg.receipt;
               //msg.success != "7" && msg.success != "No Value Returned"
               if(msg.success == 0)
               {
                  alert(responseDesc);
                  //set the hidden receipt val
                  $("#ccreceipt").val(receipt);
                  var vals = $("#checkoutform").serialize();

                  //alert(vals);
                  $.ajax(
                  {
                     type: "POST",
                     url: "ajaxSaveOrder.php",
                     data: vals,
                     dataType: 'json', // expecting json
                     success: function(msg)
                     {
                        if(msg.success == true)
                        {
                           //alert(msg.msg);
                           $("#checkoutform").fadeOut();
                           $("#receiptno").html(receipt);
                           $("#results").fadeIn();
                           $(".checkout").hide();
                           $.unblockUI;
                           $(".formrowupdate").hide();
                           $("#receipt").show();
                        }
                        else
                        {
                           alert(msg.msg);
                           $(".checkout").hide();
                           $.unblockUI;
                           return false;
                        }
                     },
                     failure: function(msg)
                     {
                        alert('Error!');
                        return false;
                     }
                  });
                  $.unblockUI;
                  $(".checkout").hide();
               }
               else
               {
                  alert(responseDesc + ". Your order has not been saved.");
                  $(".checkout").hide();
                  $("#confirmcreditcard").show();
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
     else //not cc or approved for over allocation order
     {
      $.ajax(
      {
         type: "POST",
         url: "ajaxSaveOrder.php",
         data: vals,
         dataType: 'json', // expecting json
         success: function(msg)
         {
            if(msg.success == true)
            {
               //alert(msg.msg);
               $("#checkoutform").fadeOut();
               $(".formrowupdate").hide();
               $("#results").fadeIn();
               $("#loadercheckout").hide();
            }
            else
            {
               alert(msg.msg);
               $("#loadercheckout").hide();
               $(".confirm").show();
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
      e.preventDefault();
   });

   if(!allowUpdate())
   {
	      if($("#confirmorder").length)
	          $("#confirmorder").hide();

	       if($("#confirmwage").length)
	          $("#confirmwage").hide();

	       if($("#confirmcreditcard").length)
	          $("#confirmcreditcard").hide();

	       if($("#amount").length)
	          $("#paymentRequiredText").hide();
   }

   function allowUpdate()
   {
      //allow update?
      var allowupdate = $("#ordersgreaterthanme").val();
      if(allowupdate > 0)
      {
         alert('This order cannot be updated as there are newer orders on the system. It can be approved if it is pending.');
         return false;
      }
      else
         return true;
   }

   $(".lineitemclass").mouseover(function(e){
      var imgsrc = $(this).attr("src");
      var imgID = $(this).attr("id");
      var imgArr = imgID.split("_");
      var backOrderedQty = imgArr[1];
      var qtyID = "#qty_" + imgArr[2] + "_" + imgArr[3];
      var qtyOrdered = $(qtyID).val();

      if(imgsrc == "../_img/ok.png")
      {
         showInStock("Depatched", " The total quantity for this item has been despatched. If you have yet to receive the goods, please click on the link next to status to track your delivery.");
      }
      else if(imgsrc == "../_img/outofstock.png")
      {
         showInStock("Total Quantity Backordered", backOrderedQty + " of " + qtyOrdered + " on Backorder. ETA 2-4 weeks.");
      }
      else if(imgsrc == "../_img/backorder.png")
      {
         showInStock("Some Quantity Backordered", backOrderedQty + " of " + qtyOrdered + " on Backorder. ETA 2-4 weeks.");
      }

      tooltip.pnotify_display();
      tooltip.css({
          'top': e.clientY + 12,
          'left': e.clientX + 12
      });
   });

   function calcOrderTotal()
   {
      var newTotal = 0;
      $(".subtotal").each(function(i){
         var curVal = $(this).text();
         curVal = curVal.replace(/\$/g, '');

         newTotal += parseFloat(curVal);
      });
      return newTotal;
   }

   function showInStock(title, txt) {
    tooltip = $.pnotify({
        pnotify_title: title,
        pnotify_text: txt,
        pnotify_hide: false,
        pnotify_closer: false,
        pnotify_sticker: false,
        pnotify_history: false,
        pnotify_animate_speed: 100,
        pnotify_opacity: .9,
        pnotify_info_icon: "ui-icon ui-icon-info",
        // Setting stack to false causes Pines Notify to ignore this notice when positioning.
        pnotify_stack: false,
        pnotify_after_init: function(pnotify)
        {
            // Remove the notice if the user mouses over it.
            pnotify.mouseout(function()
            {
                pnotify.pnotify_remove();
            });
        },
        pnotify_before_open: function(pnotify) {
            // This prevents the notice from displaying when it's created.
            pnotify.pnotify({
                pnotify_before_open: null
            });
            return false;
        }
    });
   }

   $('#apptour').click(function(e){
		guidely.init ({ welcome: true, startTrigger: false });
	   });			
     
	if($("#guideactive").val() == "0")
	{   
		guidely.add ({
			attachTo: '#target-1'
			, anchor: 'top-left'
			, title: 'CHECKOUT'
			, text: 'The checkout page outlines what you have placed in your cart and the delivery address that the order will be sent to.'
		});	

		guidely.add ({
			attachTo: '#target-2'
			, anchor: 'top-left'
			, title: 'DELETE LINE ITEM'
			, text: 'If you want to completely remove an item from your cart, click on this RED X.'
		});

		guidely.add ({
			attachTo: '#target-3'
			, anchor: 'top-left'
			, title: 'CHANGE THE QUANTITY'
			, text: 'You can change the quantity of the item ordered by simply entering the quantity in this field. The subtotals will automatically update.'
		});	

		guidely.add ({
			attachTo: '#continueshopping'
			, anchor: 'top-left'
			, title: 'CONTINUE SHOPPING'
			, text: 'To add more items into your cart, click on CONTINUE SHOPPING, this will take you back to the main ordering page.'
		});			

		guidely.add ({
			attachTo: '#deliverydetails'
			, anchor: 'top-left'
			, title: 'DELIVERY DETAILS'
			, text: 'Ensure that your delivery details are correct, including the cost centre and your contact details.'
		});		
			
		guidely.add ({
			attachTo: '#confirmtarget'
			, anchor: 'top-left'
			, title: 'SUBMIT YOUR ORDER'
			, text: 'You will be prompted for your credit card details if any payment is required, alternatively you can select the wage deduction option.  Click CONFIRM ORDER to finalise your order.  A copy of the order will also be sent to you email address.'
		});			

		$("#guideactive").val('1');
	}		   

});


</script>

<body>

   <div id="topHeader" class="cAlign">
 <input type="hidden" name="guideactive" id="guideactive" value="0"/>
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
            &nbsp;&raquo;&nbsp;<strong>Check Out</strong>
         </p>
      </div>
   </div> <!-- end breacrumbsSection -->

   <div class="cAlign cFloat">
      <!-- Blog Post List -->
      <div id="mainSection">
         <ul id="articles">
            <li>
               <div class="orderContent">
                  <?php
                     if($_SESSION['order'])
                     {
                        $orders = unserialize($_SESSION['order']);
                        $orders->getCurrentUserID();
                        $orderingfor_name = $orders->orderingfor_name;
                        $updateText = "";
                        if($orders->action == _UPDATE)
                        {
                           $updateText = " - UPDATING ORDER # " . $orders->order_id;

                        }
                     }
                  ?>
                  <h2 id="target-1">Check Out (<?php echo $orderingfor_name . "'s order";?>) <?php echo $updateText;?></h2>
                  <?php
                  if($_SESSION['order'])
                  {
							if($orders->action == _UPDATE)
                     {
                     	$canReturn = true;
                     	if(minAccessLevel(_ADMIN_LEVEL))
                     		$canReturn = true;
                     	else
                     	{
                     		//check date
                     		$ordertime = $orders->ordertime;
                     		$date_now = date('Y-m-d H:i:s');
                     		$stop_date = date('Y-m-d H:i:s', strtotime($ordertime . ' +60 day'));
                     		
                     		if($stop_date > $date_now)
                     			$canReturn = true;
                     	}
                  ?>
                        <div class="formrowupdate">
                           <label>Returns:</label>
                           <span class="formwrap">
                           <?php 
                           if($canReturn)
                           {
                           ?>
                              <a href="<?php echo _CUR_HOST ._DIR . "support/returns.php?action=new&order_id=" . $orders->order_id . "&state=" . $orders->state;?>">MAKE A CLAIM</a>
                           <?php 
                           }
                           else 
                           {   
                           ?>
                           <?php 
                           }
                           ?>
                           </span>
                           
                        </div>
                  <?php
                     }
                     $status = $orders->status;
                     if($status == _DESPATCHED || $status == "PART DELIVERY")
                     {
                        $connoteDB = $orders->connote;
                        $connoteArr = explode(";", $connoteDB);
                        $onclick = "";
                        for($z = 0; $z < count($connoteArr); $z++)
                        {
                           $connote = $connoteArr[$z];
                           if(strlen($connote) > 2)
                           $onclick .= "window.open(&quot;http://auspost.com.au/track/display.asp?id=$connote&type=consignment&quot;);";
                        }
                        $status = "<a target='_blank' onclick='$onclick'>$status</a>";
                     }
                     ?>
                     <div class="formrowupdate">
                        <label>Date Ordered:</label>
                        <span class="formwrap">
                           <?php echo $orders->ordertime;?>
                        </span>
                     </div>
                     <div class="formrowupdate">
                        <label>Date Approved:</label>
                        <span class="formwrap">
                           <?php echo $orders->approval_time;?>
                        </span>
                     </div>
                     <div class="formrowupdate">
                        <label>Last Updated:</label>
                        <span class="formwrap">
                           <?php echo $orders->lastupdated;?>
                        </span>
                     </div>
                     <div class="formrowupdate">
                        <label>Status:</label>
                        <span class="formwrap" id="curStatus">
                           <?php echo $status;?>
                        </span>
                     </div>
                  <?php
                  }
                  ?>
                  <p>
                  <div id="loaderdel"><img src="../_img/fbloader.gif" alt="loading..."/></div>
                     <form action="" method="post" id="checkoutform">
                     <table id="box-table-a" summary="Employee Pay Sheet">
                        <thead>
                        <tr>
                           <th bgcolor="#4C4E53">&nbsp;</th>
                           <th bgcolor="#4C4E53">Item</th>
                           <th bgcolor="#4C4E53">Description</th>
                           <th bgcolor="#4C4E53">Size</th>
                           <th bgcolor="#4C4E53">Qty</th>
                           <th bgcolor="#4C4E53">Unit Cost</th>
                           <th bgcolor="#4C4E53">Subtotal</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                           $paymentRequired = false;
                           if($_SESSION['order'])
                           {
                              $orders = unserialize($_SESSION['order']);
                              $readOnly = "";
                              if(strlen($orders->receipt) > 0)
                              	$readOnly = "readonly";
                              $lineitems = $orders->lineitems;
                              $numlines = count($lineitems);
                              if($numlines > 0)
                              {
                                 $arrKeys = array_keys($orders->lineitems);
                                 $total = 0;
                                 for($i = 0; $i < $numlines; $i++)
                                 {
                                    $key = $arrKeys[$i];
                                    $li = $orders->lineitems[$key];
                                    $tmpCost = $li->lineCost();
                                    $unitCost = formatNumber($li->unitcost);
                                    $final_cost = formatNumber($tmpCost);
                                    $item_number = $li->product->item_number;
                                    $desc = $li->product->description;
                                    $prod_id = $li->product->prod_id;
                                    $qty = $li->qty;
                                    $size = $li->size;
                                    $qty_text = "qty_$prod_id";
                                    $total += $tmpCost;
                                    $idsize = $prod_id."_".$size;
                                   $backordered = $li->backordered;
                                    $backordered_id = "li_$backordered" . "_$idsize";
                                    $emb=$li->emb;

                                    $idTarget2 = "";
                                    $idTarget3 = "";
                                    if($i == 0)
                                    {
	                                    $idTarget2 = "id='target-2'";
	                                    $idTarget3 = "id='target-3'";
                                    }
                                    
                                    //delivered!
                                    if($backordered == 0)
                                    {
                                       $backorderImg = "<img src='../_img/ok.png' id='$backordered_id' class='lineitemclass' onmousemove=\"tooltip.css({'top': event.clientY+12, 'left': event.clientX+12});\" onmouseout=\"tooltip.pnotify_remove();\">";
                                    }
                                    else if($backordered == $qty) //everythings backordered
                                    {
                                       $backorderImg = "<img src='../_img/outofstock.png' id='$backordered_id' class='lineitemclass' onmousemove=\"tooltip.css({'top': event.clientY+12, 'left': event.clientX+12});\" onmouseout=\"tooltip.pnotify_remove();\">";
                                    }
                                    else //some
                                    {
                                       $backorderImg = "<img src='../_img/backorder.png' id='$backordered_id' class='lineitemclass' onmousemove=\"tooltip.css({'top': event.clientY+12, 'left': event.clientX+12});\" onmouseout=\"tooltip.pnotify_remove();\">";
                                    }
                        ?>
                                    <tr id="<?php echo "tr_$idsize";?>">
                        <?php
                                    $tmpStatus = $orders->status;
                                    if($tmpStatus == _DESPATCHED || $tmpStatus == "PART DELIVERY")
                                    {
                                       //check which item has been backordered (if any)
                        ?>
                                       <td><?php echo $backorderImg;?></td>
                        <?php
                                    }
                                    else if($readOnly != "readonly")
                                    {
                        ?>
                                       <td <?php echo $idTarget2;?> ><img src="../_img/del.png" id="<?php echo "del_$idsize";?>" class="delline"></td>
                        <?php
                                    }
                                    else
                                    {
								?>                                    	
                                    	<td <?php echo $idTarget2;?>>&nbsp;</td>
                        <?php 
                                    }
                        ?>
                                       <td><?php echo $item_number; ?></td>
                                       <td><?php 
                                       
                                       if($emb)
                                           echo $desc . " [ $emb ]";
                                       else
                                           echo $desc;

                                       ?>
                                       </td>
                                       <td>
                                       <?php 
                                       
                                       //echo $size;
                                       if($_SESSION[_USER_NAME] == "1")
                                       {
	                                       $size_text = "size_$prod_id";
	                                       $s_query = "select size, size from sizes where prod_id = $prod_id";
	                                       generateComboQuerySizes($size_text, $s_query, $size, $pid);                                       
                                       }
                                       else
                                       	echo $size;
                                       ?>
                                       </td>
                                       <td <?php echo $idTarget3;?>>
                                       <input type="text" size="3" class="qtyinput" name="qty_<?php echo $idsize;?>" id="qty_<?php echo $idsize;?>" value="<?php echo $qty;?>" <?php echo $readOnly;?>>
                                       </td>
                                       <td>$<?php echo $unitCost;?></td>
                                       <td id="sub_<?php echo $idsize;?>" class="subtotal">$<?php echo $final_cost;?></td>
                                    </tr>
                        <?php
                                 }
                                  $allowance = $orders->getAllowanceFromOrderDate();
                                 $alreadyOrdered = $orders->getOrderedTotal();
                                 $cartTotal = $orders->GrandTotal();
                                 $paidAmt = $orders->GetAlreadyPaid($orders->user_id, $orders->order_id);
                                 $paidOnCurrentOrder = $orders->payable;

//                                 echo "paid: $paidAmt belt: $beltAmt calc: " . ($paidAmt-$beltAmt) ." paid on current: $paidOnCurrentOrder already: $alreadyOrdered<BR>";

                                 //$paidAmt -= $beltAmt; //should this be included?
                                 $paidAmt += $paidOnCurrentOrder;
//echo "PAID: $paidAmt<BR>";
                                 if($paidAmt <0)
                                    $paidAmt*=-1;

                                 $remaining = bcsub($allowance,$alreadyOrdered,2);
                                 $remaining = bcsub($remaining, $cartTotal,2);//+ $paidAmt;
                                 $remaining = bcadd($paidAmt, $remaining,2);


//                                 echo "remain: $remaining<BR>";
//                                 echo "allowance: $allowance<BR>";
//                                 echo "alreadyo: $alreadyOrdered<BR>";
//                                 echo "cart: $cartTotal<BR>";


                                 $payable = $orders->CalcPayable();
                                 if($remaining < 0)
                                    $remaining = 0;     
                                 
//                                     echo "PAYABLE: $payable<BR>";

                        ?>
                                 <input type="hidden" name="hiddenallowance" id="hiddenallowance" value="<?php echo $allowance;?>">
                                 <input type="hidden" name="remaining" id="allowance" value="<?php echo $allowance;?>">

                                 <tr>
                                    <td align="right" colspan="6">Total</td>
                                    <td id="orderTotal">$<?php echo formatNumber($total);?></td>
                                 </tr>
                                 <!-- 
                                 <tr>
                                    <td align="right" colspan="6">Allowance</td>
                                    <td id="tdallowance">$<?php echo formatNumber($allowance);?></td>
                                 </tr>
                                    -->     
                                 <tr>
                                    <td align="right" colspan="6">Allowance Remaining</td>
                                    <td id="remaining">$<?php echo formatNumber($remaining);?></td>
                                 </tr>
				  						                            
                        <?php
                                 if($remaining > 0)
                                 {
                                    $payable = ($payable);

                                 }
                                 //else
                                 {

                                 if($payable < 0)
                                 {
                                    //$payable *=-1.1;//gst
                                    $payable *= -1;
                                    $payable = ($payable);
                                    $paymentRequired = true;
                                 }
                         ?>
                                 <tr id="trpayable">
                                    <td align="right" colspan="6">Payable</td>
                                    <td id="tdpayable">
                                    <?php

                                       //echo formatNumber($payable);
                                       if($orders->isGST == "N")
                                          echo "NZ\$".formatNumber($payable)." (no GST)";
                                       else
                                          echo "\$".formatNumber($payable)." (inc GST)";

                                    ?>
                                    </td>
                                 </tr>
                        <?php
                                 if($orders->action == _UPDATE && $orders->status != _PENDING || ($orders->ordersGreaterThanMe()>0))
                                 {
                        ?>
                        <!--
                                    <tr id="trpayable">
                                       <td align="right" colspan="6">Balance</td>
                                       <td id="tdpayable">$<?php echo formatNumber(0);?> (inc GST)</td>
                                    </tr>
                                    -->
                        <?php
                                    }
                                 }
                              }
                           }
                        ?>
                        </tbody>
                     </table>
                     <input type="hidden" name="isgst" id="isgst" value="<?php echo $orders->isGST;?>"/>
                  <?php
                     if($numlines > 0)
                     {
                  ?>
                     <h2 id="deliverydetails">Delivery Details</h2>
                     <div class="formrow">
                        <label>Full Name</label>
                        <span class="formwrap">
                        <?php
                           $iswage = "N";
                           $iscc = false;
                           $amtPayable = 0;
                           if($orders->action == _UPDATE)
                           {
                              $tmpUserId = $orders->user_id;
                              $tmpLocationId = $orders->location_id;
                              $fullname = $orders->fullname;
                              $sname = $orders->sname;
                              $address = $orders->address;
                              $suburb = $orders->suburb;
                              $postcode = $orders->postcode;
                              $state = $orders->state;
                              $phone = $orders->phone;
                              $fax = $orders->fax;
                              $paymentopt = $orders->paymentopt;
                              $receipt = $orders->receipt;
                              $agree = $orders->agree;
                              $email = $orders->email;
                              $amtPayable = $orders->payable;
                              $costcentre=$orders->costcentre;

                              /*
                              if($amtPayable > 0 && $iswage == "N") //must be CC
                              {
                                 $iscc = true;
                                 $iswage = false;
                              }
                              if($iswage == "Y")
                                 $iswage = true;
                              */

                              $numpays = $orders->numpays;
                              $amountperpay = $orders->amountperpay;
                              $ccamountpaid = $orders->payable;
                              $ccname = $orders->cardname;
                              $ccnumber = $orders->cardnumber;
                              $ccexpiry = $orders->expiry;
                              $cctype = $orders->cardtype;
                              $comments = $orders->comments;
                              $defaultPayFreq = $orders->numPaysDeduction;
                             $comments = $orders->comments;

                           }
                           else
                           {
                           	
                          // 	if($orders->user_id < 9990000)
                           	{
	                           	$tmpLocationId = $orders->oLocation_id;
	                           	if(!$tmpLocationId || $tmpLocationId == 123)
	                           		$tmpLocationId = $_SESSION[_LOCATION_ID];
	                           	//$fullname = $_SESSION[_FIRST_NAME] . " " . $_SESSION[_LAST_NAME];
	                           	$fullname = $orderingfor_name;
	                           	//$sname = "Private Address";
	                           	$costcentre=$orders->costcentre;	                           	
	                           	$location = new location();
	                           	$location->LoadLocationId($tmpLocationId);
	                           	$sname = $location->sname;

	                           	$address = $location->address;
	                           	$suburb = $location->suburb;
	                           	$postcode = $location->postcode;
	                           	$state = $location->state;
	                           	$phone = $location->phone;
	                           	$fax = $location->fax;
	                           	$country = $location->country;
	                           	$email = $orders->email;
                           	}
//                            	else 
//                            	{
//                            		$tmpLocationId = $_SESSION[_LOCATION_ID];
	                           	
// 	                           	$location = new location();
// 	                           	$location->LoadLocationId($tmpLocationId);
// 	                           	$sname = $location->sname;
// 	                           	$costcentre=$orders->costcentre;
// 	                           	$address = $location->address;
// 	                           	$suburb = $location->suburb;
// 	                           	$postcode = $location->postcode;
// 	                           	$state = $location->state;
// 	                           	$phone = $location->phone;
// 	                           	$fax = $location->fax;
// 	                           	$country = $location->country;
// 	                           	$email = $orders->email;                           	
//                            	}
                           											
                           	$iswage = false;
                              $iscc = false;
                              $agree = false;
                           }

                        ?>
                           <input class="validate[required]" type="text" name="fullname" id="fullname" value="<?php echo $fullname;?>"/>
                        </span>
                     </div>
                     <?php
							$readOnly = "";
							$costCentreReadOnly = "";
							if(!minAccessLevel(_BRANCH_LEVEL))
							{
								$costCentreReadOnly = "readonly";
								$readOnly = "readonly";
							}
							
                     ?>
                     <div class="formrow">
                        <label>Location</label>
                        <span class="formwrap">
                           <?php 
                         //  if(minAccessLevel(_BRANCH_LEVEL))
                         if( $status == "APPROVED")
                           {
                           ?>
                           <input class="validate[required] query" type="text" name="q" id="query" value="<?php echo $sname;?>"/> 
                           <?php 
                           }
                           else 
                           {
                           ?>
                           <input class="validate[required] query" type="text" name="q" id="query" value="<?php echo $sname;?>"/>
                           
                           <?php  	
                           }	                           
                           ?>
                           <input type="hidden" name="query_val" id="query_val" />

                        </span>
                     </div>
                     <div class="formrow">
                        <label>Cost Centre</label>
                        <span class="formwrap">
                           <input class="validate[required]" type="text" name="costcentre" id="costcentre"  value="<?php echo $costcentre;?>" <?php echo $costCentreReadOnly;?>/>
                        </span>
                     </div>                     
                     <div class="formrow">
                        <label>Address</label>
                        <span class="formwrap">
                           <input class="validate[required]" type="text" name="address" id="address"  value="<?php echo $address;?>" <?php echo $readOnly;?>/>
                        </span>
                     </div>
                     <div class="formrow">
                        <label>Suburb</label>
                        <span class="formwrap">
                           <input class="validate[required]" type="text" name="suburb" id="suburb" value="<?php echo $suburb;?>" <?php echo $readOnly;?>/>
                        </span>
                     </div>
                     <div class="formrow">
                        <label>State</label>
                        <span class="formwrap">
                           <?php 
                           if(minAccessLevel(_BRANCH_LEVEL))
	                           generateStaticCombo($statesArr, $state, "state", true);
                           else 
                           {
                           ?>
                           <input class="validate[required]" type="text" name="state" id="state" value="<?php echo $state;?>" <?php echo $readOnly;?>/>
                           <?php 	
                           }	
                           ?>
                        </span>
                     </div>
                     <div class="formrow">
                        <label>Postcode</label>
                        <span class="formwrap">
                           <input class="validate[required]" type="text" name="postcode" id="postcode" value="<?php echo $postcode;?>" <?php echo $readOnly;?>/>
                        </span>
                     </div>
                     <div class="formrow">
                        <label>Phone</label>
                        <span class="formwrap">
                           <input class="validate[required,custom[phone]]" type="text" name="phone" id="phone"  value="<?php echo $phone;?>" <?php echo $readOnly;?>/>
                        </span>
                     </div>
                     <div class="formrow">
                        <label>Email</label>
                        <span class="formwrap">
                           <input class="validate[required,custom[email]]" type="text" name="email" id="email"  value="<?php echo $email;?>"/>
                        </span>
                     </div>            
                     <div class="formrow">
                        <label>Comments</label>
                        <span class="formwrap">
                           <textarea name="comments" id="comments" rows="5" cols="40"><?php echo $comments;?></textarea>
                        </span>
                     </div>
                     <!--
                     <div class="formrow">
                        <label>Fax</label>
                        <span class="formwrap">
                           <input class="validate[required,custom[phone]]" type="text" name="fax" id="fax"  value="<?php echo $fax;?>" readonly/>
                        </span>
                     </div>

-->
                     <br/>
                     <br/>
                     <div id="confirmtarget"></div>
                     <div id="paymentRequiredText">
                     <?php 
                     if(minAccessLevel(_ADMIN_LEVEL) && strlen($receipt) ==0)
                     {
                     ?>
                     <div class="formrow">
                        <label>&nbsp;</label>
                        <span class="formwrap">
                              Payment not required by Administrators - charges will be applied to the cost centre.
                        </span>
                     </div>                     

                       
                        <div class="formrow">
                           <label></label>
                           <span class="formwrap">
                             <input type="submit" class="confirm" id="confirmbypass" name="confirm" value="Confirm Order"/>                           
										<div id="loadercheckout" class="checkout"><img src="../_img/fbloader.gif" alt="loading..."/></div>
                           </span>
                        </div>
                                             
                     <?php 
                     }
                     else 
                     {
                     ?>                     
                           <h2>Payment Details</h2>
                           <p>Your order has exceeded your entitlement by <b id="bpayable">$<?php echo formatNumber($payable);?> (inc GST)</b>. You can pay for this order by either wage deduction or credit card.</p>
                           <p>
                           <?php
                           //can't change payment option anymore once order has been saved since
                           //payment are now processed in real time!
                           if($orders->action != _UPDATE)
                           {
                           ?>
                           <p>Please select a payment option. </p>
                           <p><font color="red">Note: Since payment is immediately processed, once you have submitted your order, it <b>cannot be changed or deleted</b> online. Be sure to make <b>your selection</b> carefully. <br/>Please contact us on 03 9753 2555 if you require further assistance.</font></p>
                           <input type="submit" id="wage" value="Wage Deduction" name="action"/>
                           <?php
                           //if($orders->isAUS == "Y")
                           {
                           ?>
                              <input type="submit" id="creditcard" value="Credit Card" name="action"/>
                           <?php
                           }
                           ?>
                           <input type="hidden" id="orgAmount" name="orgAmount" value="<?php echo number_format($payable, 2, '.', '');?>"/>
                           <?php
                           }
                           ?>
                           <input type="hidden" id="payable" value="true" name="payable" class="payable"/>
                           </p>
                     <?php 
                     }
                     ?>
                     </div>
                     
                     
                     <div id="paymentNotRequiredText">
                        <div class="formrow">
                           <label></label>
                           <span class="formwrap">
                           <?php 
                           if(minAccessLevel(_BRANCH_LEVEL) && $status == "PENDING")
                           {
                           ?>
                              <input type="submit" class="approve" id="approveorder" name="approveorder" value="Approve Order"/>
                           <?php 
                           }
                          // else 
                           {
                           ?>
                             <input type="submit" class="confirm" id="confirmorder" name="confirm" value="Confirm Order"/>
                           <?php 
                           }
                           ?>
<div id="loadercheckout" class="checkout"><img src="../_img/fbloader.gif" alt="loading..."/></div>
                              <input type="hidden" id="payable" value="false" name="payable" class="payable"/>
                           </span>
                        </div>
                     </div>

                     <?php
                        //whether or not we should show the wage/credit card option
                        //1. if there are newer orders, dont allow the update since is will incorrectly calculate the payable amount;
                        //2. if orginally the payable amount on the order was ZERO, the newer order will result in the payable amount
                        //   being calculated with the newer order in the calculations, and we dont want that as it may bypass the
                        //   allowances that are set.
                        //3. so DISABLE THE UPDATE OPTIONS.

                        $allowupdate = $orders->ordersGreaterThanMe();
                        
                     ?>

                     <input type="hidden" id="ordersgreaterthanme" value="<?php echo $allowupdate;?>"/>

                     <div id="wagededuction">
                     <p>
                     <!--<b>Wage Deduction Selected.</b>-->
                     <?php

                     if($orders->paymentopt2 == "C2")
                     {

                     ?>
                     <b>Wage Deduction Selected, $<?php echo $amountperpay ." x " . $numpays . " pays with a Credit Card Payment of \$$ccamountpaid."?></b>
                     <p><font color="red"><b>This order cannot be changed.</b> Please contact us on 03 9753 2555 if you require further assistance.</font></p>
                     <?php
                     }
                     else if($orders->paymentopt2 == "P2")
                     {
                     ?>
                     <b>Wage Deduction Selected, $<?php echo $amountperpay ." x " . $numpays . " pays with a PayPal Payment of \$$ccamountpaid."?></b>
                     <p><font color="red"><b>This order cannot be changed.</b> Please contact us on 03 9753 2555 if you require further assistance.</font></p>
                     <?php
                     }
                     else if($orders->paymentopt == "W")
                     {
                     ?>
                     <b>Wage Deduction Selected, $<?php echo $amountperpay ." x " . $numpays . " pays."?></b>
                     <p><font color="red"><b>This order cannot be changed.</b> Please contact us on 03 9753 2555 if you require further assistance.</font></p>
                     <?php
                     }

                     //can't change payment option anymore once order has been saved since
                     //payment are now processed in real time!
                     if($orders->action != _UPDATE)
                     {
                     ?>
                     <div class="formrow">
                        <label>Number of pays to deduct from</label>
                        <span class="formwrap">
                           <?php
                              $payFreq = $orders->pay_frequency;
                              $numPaysArr = array();

                              if(!$defaultPayFreq)
                                 $defaultPayFreq = _checkIsSet("numPays");

                              if($payFreq == "Monthly")
                              {
                                 $numPaysArr = array(1 =>1, 2=>2, 3=>3);
                                 if(!$defaultPayFreq)
                                    $defaultPayFreq = 3;
                              }
                              else
                              {
                                 $numPaysArr = array(1 =>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6);
                                 if(!$defaultPayFreq)
                                    $defaultPayFreq = 6;
                              }
                              generateStaticCombo($numPaysArr, $defaultPayFreq, "numPays", true);
                           ?>
                        </span>
                     </div>
                     <?php
                           $amountPerPay = $payable/$defaultPayFreq;
                           $remainingPayable = 0;
                     ?>

                     <div class="formrow">
                        <label>Amount to be deducted per pay</label>
                        <span class="formwrap">
                           <input type="text" name="amountperpay" id="amountperpay" value="<?php echo formatNumber($amountPerPay);?>" readonly/>
                           <input type="hidden" name="remainingpayable" id="remainingpayable" value="<?php echo formatNumber($remainingPayable);?>"/>
                        </span>
                     </div>

                     </p>
                     <p>
                     <b>NOTE: BY CLICKING</b> the Terms &amp; Conditions box and placing an order you are confirming that you accept all costs associated with the order,
                     including reimbursement to GPC Asia Pacific Pty Ltd of amounts in excess of the allowance
                     and that you agree that GPC Asia Pacific Pty Ltd has the right to  deduct any amount in excess of your allowance from your pay.
                     <br/>
                     <br/>
                     <?php
                        if($orders->agree == "Y")
                        {
                     ?>
                        <input type="checkbox" name="agree" id="agree" class="validate[required]" checked="true"> I HAVE READ, UNDERSTAND AND ACCEPT THE TERMS AND CONDITIONS
                     <?php
                        }
                        else
                        {
                     ?>
                           <input type="checkbox" name="agree" id="agree" class="validate[required]"> I HAVE READ, UNDERSTAND AND ACCEPT THE TERMS AND CONDITIONS
                     <?php
                        }
                     ?>
                     </p>
                     <?php
                     {
                     ?>
                        <input type="submit" class="confirm" id="confirmwage" name="confirm" value="Confirm Order"/>
                     <?php
                     }
                  }
                     ?>

                     <input type="hidden" name="amount" value="<?php echo number_format($payable, 2, '.', '');?>"/>
                     <div class="checkout"><img src="../_img/fbloader.gif" alt="loading..."/></div>

                     </div>

                     <div id="creditcardform">
                     <?php 
                     if(minAccessLevel(_ADMIN_LEVEL))
                     {
                     ?>
                        <input type="hidden" name="amount" id="amount" value="<?php echo formatNumber($payable);?>" class="payable" readonly/>                     
                        <input type="hidden" name="cardname" id="cardname" value="Admin Bypass UID: <?php echo $_SESSION[_USER_ID];?>"/>
                        <input type="hidden" name="cardnumber" id="cardnumber" value="<?php echo $_SESSION[_USER_ID];?>"/>                        
                     <?php 
                     }
                     else
                     {
                     ?>
                     <br/>                     
                        <div class="formrow">
                           <label>Card Type</label>
                           <span class="formwrap">
                              <?php generateStaticCombo($ccArr, $cctype, "cardtype", true);?>
                           </span>
                        </div>
                        <div class="formrow">
                           <label>Card Name</label>
                           <span class="formwrap">
                              <input class="validate[required]"  type="text" name="cardname" id="cardname" value="<?php echo $ccname;?>"/>
                           </span>
                        </div>
                        <div class="formrow">
                           <label>Card Number</label>
                           <span class="formwrap">
                              <input class="validate[required,custom[number]]"  type="text" name="cardnumber" id="cardnumber" value="<?php echo $ccnumber;?>"/>
                           </span>
                        </div>
                        <div class="formrow">
                           <input type="hidden" name="expiry" id="expiry" value=""/>
                           <label>Expiry</label>
                           <span class="formwrap">
                              <select class="validate[required]" name="month" id="month">
                                 <option value="">MM</option>
                                 <option value="01">01</option>
                                 <option value="02">02</option>
                                 <option value="03">03</option>
                                 <option value="04">04</option>
                                 <option value="05">05</option>
                                 <option value="06">06</option>
                                 <option value="07">07</option>
                                 <option value="08">08</option>
                                 <option value="09">09</option>
                                 <option value="10">10</option>
                                 <option value="11">11</option>
                                 <option value="12">12</option>
                              </select>
                           </span>
                           <span class="formwrap">
                              <select class="valueidate[required]" name="year" id="year">
                                 <option value="">YYYY</option>
                                 <?php 
                                 	$tmpYear = date("Y");
                                 	for($y=$tmpYear; $y < ($tmpYear+7); $y++)
                                 	{
                                 		$endDigit = substr($y, 2,2);
                                 		echo "<option value='$endDigit'>$y</option>";
                                 	}
                                 	
                                 ?>                                                                                                                                                                                                
                              </select>
                           </span>
                        </div>
                        <div class="formrow">
                           <label>Security Code (3 Digit)</label>
                           <span class="formwrap">
                              <input class="validate[required,custom[number]]"  type="text" name="securitycode" id="securitycode" value=""/>
                           </span>
                        </div>
                        <div class="formrow">
                           <label>Amount</label>
                           <span class="formwrap">
                              <input type="text" name="amount" id="amount" value="<?php echo formatNumber($payable);?>" class="payable" readonly/>
                           </span>
                        </div>
                       
                        <div class="formrow">
                           <label></label>
                           <span class="formwrap">
                           <?php 
                           if(minAccessLevel(_BRANCH_LEVEL) && $status == "PENDING")
                           {
                           ?>
                              <input type="submit" class="approve" id="approveorder" name="approveorder" value="Approve Order"/>
                           <?php 
                           }
                           else if($orders->paymentopt != "C")
                           {
                           ?>
                             <input type="submit" class="confirm" id="confirmcreditcard" name="confirm" value="Confirm Order"/>
                           <?php 
                           }
                           ?>
<div id="loadercheckout" class="checkout"><img src="../_img/fbloader.gif" alt="loading..."/></div>
                           </span>
                        </div>
						<?php 
                     }//admins don't need to pay for over allocations
						?>                         
                     </div>

                        <input type="hidden" name="iswage" id="iswage" value="<?php echo $iswage;?>">
                        <input type="hidden" name="iscc" id="iscc" value="<?php echo $iscc;?>">
                        <input type="hidden" name="ccreceipt" id="ccreceipt" value="<?php echo $receipt;?>"/>
                        <input type="hidden" name="paymentopt" id="paymentopt" value="<?php echo $paymentopt;?>">
                  <?php
                     }
                  ?>
                     </form>
                  </p>
                  <div id="results">
                  <h3>Order submitted.  Your order will be collated and processed by the end of the month.</h3>
                  <h4>For any urgent orders, please contact DTY on 03 9753 2555 or email sales@designstoyou.com.au </h4>
                  <!-- <h3>An Email confirmation has been sent to <?php echo $email;?></h3> -->
                  </div>

                  <?php 
                  if(strlen($receipt) >0)
                  {
                  ?>
                  <div id="receipt">
                  <h3>Your credit card has been processed, the receipt for this transaction is: <span id="receiptno"><?php echo $receipt;?></span></h3>
                  </div>
						<?php 
                  }
						?>
                  
                  <br/>
                  <br/>
                  <br/>
                  <p>
                  DTYLink v2.0
                  </p>

               </div>
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