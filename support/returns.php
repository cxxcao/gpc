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
require_once('../products/ordersclass.php');


if(!user_isloggedin())
{
   header("Location: " . _CUR_HOST. _DIR);
}

$action = _checkIsSet("action");

if($_SESSION[_WARRANTY_SESSION])
{
   $warranty = unserialize($_SESSION[_WARRANTY_SESSION]);
}
else
   $warranty = new warranty();

if(!$warranty)
{
   $warranty = new warranty();
}

if($action == "new")
{
   $warranty = new warranty();
   //check for order_id;
   $order_id = _checkIsSet(_ORDER_ID);
   if($order_id)
   {
      $warranty->order_id = $order_id;
      $warranty->name = $warranty->getFieldFromOrder($order_id, "name");
      $warranty->email = $warranty->getFieldFromOrder($order_id, "email");
      $warranty->phone = $warranty->getFieldFromOrder($order_id, "contact");
   }
}
else if($action == "SUBMIT")
{
   if($warranty->saveWarranty())
//   echo $_SESSION['msg'] . " slkdfj<BR>";
//       header("Location: " . _CUR_HOST. _DIR ."support/listreturns.php");
     echo "<meta http-equiv=\"refresh\" content=\"0; url="._DIR."support/listreturns.php\">";
}
else if($action == _UPDATE || $warranty->action_type == _UPDATE)
{
   $warranty_id = _checkIsSet("warranty_id");
   if($warranty_id)
      $warranty->LoadWarranty($warranty_id);
//         echo "<meta http-equiv=\"refresh\" content=\"0; url="._DIR."support/listwarranty.php\">";

}



?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="utf-8">
   <title>Designs To You - DTYLink Online Ordering</title>
   <?php include('../_inc/js_css_returns.php');?>
   <link rel="stylesheet" type="text/css" href="<?php  echo _CUR_HOST . _DIR ; ?>_css/jquerytable.css" media="screen" />

   <script type="text/javascript">
         function populateSizes(prodEleID, sizeEleID, defaultVal)
         {
             console.log("PID ELE ID: " + prodEleID);
            var pid = $(prodEleID).val();
            param = 'prod_id=' + pid;

            $.ajax({ // sending an ajax request to addtocart.php
               type: "POST",
               url: "ajaxGetSizes.php",
               data: param,   // the product image as a parameter
               dataType: 'json', // expecting json
               success: function(msg){
                  var selOptions = '';
                  var tmpSelID = sizeEleID.split("#");
                  var splitSelID = tmpSelID[1];
                  $(sizeEleID).empty();

                  for(j = 0; j < msg.length; j++)
                  {
                     item1 = msg[j];
                     disp1 = msg[j];
                     //selOptions += '<option value="' + item1 + '" class="'+splitSelID+'sel">' + disp1;
                     if(item1 == defaultVal)
                        $("<option>").attr("value", item1).attr("selected", true).text(disp1).appendTo(sizeEleID);
                     else
                        $("<option>").attr("value", item1).text(disp1).appendTo(sizeEleID);
                  }
               },
               failure: function(msg){
                  alert('Error!');
               }
            });
         }

      $(document).ready(function() {
         $("#checkoutform").validationEngine({
         });


		   $('#apptour').click(function(e){
				guidely.init ({ welcome: true, startTrigger: false });
			   });			   
			if($("#guideactive").val() == "0")
			{   
				guidely.add ({
					attachTo: '#target-1'
					, anchor: 'top-left'
					, title: 'CREATE A RETURN/EXCHANGE'
					, text: 'This guide will help you make a claim to return your garments.  For your convenience, some fields have been automatically filled out.'
				});

				guidely.add ({
					attachTo: '#email'
					, anchor: 'top-right'
					, title: 'Email and Status'
					, text: 'It is important that you ensure the email address provided is correct as we\'ll send you information on how the garments can be returned to us as well as an Australia Post Return Post Label.<br/><br/>When you submit your request, it will remain as PENDING until we review and APPROVE your request, this is normally actioned within 24 hours.'
				});
				
				guidely.add ({
					attachTo: '#target-3'
					, anchor: 'top-left'
					, title: 'Garment Ordered'
					, text: 'This table lists all the garments that you have ordered in the order # specified above.  The first 3 columns of the table shows the garment you ordered and received, including the size and quantity ordered.'
				});
				
				guidely.add ({
					attachTo: '#target-4' 
					, anchor: 'top-right'
					, title: 'Garment Details'
					, text: 'If you are unsure what the item codes mean, click on the RED garment icon to view the garment details.'
				});

				guidely.add ({
					attachTo: '#target-5'
					, anchor: 'top-right'
					, title: 'REPLACEMENT GARMENT'
					, text: 'The next few columns indicate the replacement garment that you\'re requesting. Select the garment from the dropdown list, then select the size and quantity you wish to replace.' 
				});

		 		guidely.add ({
		 			attachTo: '#target-6'
		 			, anchor: 'top-right'
		 			, title: 'REASON FOR RETURN'
		 			, text: 'Select the reason that you are returning the garment to help us better process your request.'
		 		});	

		 		guidely.add ({
		 			attachTo: '#reason'
		 			, anchor: 'top-right'
		 			, title: 'COMMENTS'
		 			, text: 'Any comments or extra information that you think is important in helping us process your request can be entered here.'
		 		});	

		 		guidely.add ({
		 			attachTo: '#highlight-table3'
		 			, anchor: 'top-right'
		 			, title: 'EVENTS LOG'
		 			, text: 'The events log can be used to track any activities that we can actioned in relation to your request.'
		 		});		

		 		guidely.add ({
		 			attachTo: '#action'
		 			, anchor: 'top-right'
		 			, title: 'SUBMIT YOUR REQUEST'
		 			, text: 'Once you have filled out all the necessary details, click on SUBMIT send your request to us.  Please note that we will need to APPROVE your request, and the garments must be RETURNED to us before the replacement can be sent.'
		 		});		 			 		
		 			 		
		 		$("#guideactive").val('1');
			}			

	   		<?php 
	   			   if($warranty->isFirst())
	   			   {
	   			   ?>
	   				   guidely.init ({ welcome: true, startTrigger: false });
	   				<?php
	   				}
	   				?>

         $(".iframe").fancybox({
            'width'        : '80%',
            'height'       : '80%',
            'autoScale'    : false,
            'transitionIn' : 'none',
            'transitionOut': 'none',
            'type'         : 'iframe'
         });

         $("#action").click(function(e)
         {
            var totalreturned = 0;
            $(".rqtyinput").each(function(e){
               totalreturned += $(this).val();
            });

            if(totalreturned == 0)
            {
               e.preventDefault();
               alert("Please enter the quantity you wish to return.");
            }
            else
            {
              // $(this).submit();
            }
         });

         //set default values
         $(".naProductClass").each(function(e){
            var idSelArr = $(this).attr('id').split("_");

            var idSel = idSelArr[1] + "_" + idSelArr[2];
            if(idSelArr.length > 3)
               idSel += "_" + idSelArr[3];

            var rProdVal = $("#w_rprod_id" + idSel).val();
            var rSizeVal = $("#w_rsize" + idSel).val();
            var rQtyVal = $("#w_rqty" + idSel).val();
            var rRcvVal = $("#w_rcv" + idSel).val();
            var rRjtVal = $("#w_rjt" + idSel).val();
            var rReturnTypeVal = $("#w_return_type" + idSel).val();
//alert("#w_rsize" + idSel + " S: " + rSizeVal);
            $("#rproducts_" + idSel).val(rProdVal);
            populateSizes('#rproducts_' + idSel, '#rsizes_' + idSel, rSizeVal);

            var imgName = "#rimg_" + idSel;
            var imgURL = "<?php echo _CUR_HOST ._DIR . "products/garment-spec.php?prod_id="?>" + rProdVal;
            $(imgName).attr('href', imgURL);

            $("#rqty_" + idSel).val(rQtyVal);
            $("#rcv_" + idSel).val(rRcvVal);
            $("#rjt_" + idSel).val(rRjtVal);
            $("#returntype_" + idSel).val(rReturnTypeVal);
         });

         $(".rqtyinput").change(function(e){
            var idSelArr = $(this).attr('id').split("_");
            var idSel = idSelArr[1] + "_" + idSelArr[2];
            if(idSelArr.length > 3)
               idSel += "_" + idSelArr[3];

            var qtyEntered = $(this).val();
            var qtyOrdered = $("#qty_" + idSel).val();
            var returnTypeName = "#returntype_" + idSel;

            if(qtyEntered > qtyOrdered)
            {
               alert("The quantity you are requesting to replace is greater than what you have ordered.");
               $(this).val("");
            }

            if(qtyEntered > 0)
            {
               $(returnTypeName).addClass("validate[required]");
            }
            else
               $(returnTypeName).addRemove("validate[required]");

         });

         $(".naProductClass").change(function(e)
         {
            var curFullID = $(this).attr('id');
            var curID = curFullID.substr('rproducts'.length, curFullID.length);
            replacementNotRequired = jQuery('#' + curFullID).val();
            var newProdId = $(this).val();
console.log("PID: " + curFullID + " NEWPID: " + newProdId + " CURID: " + curID); 
            var imgName = "#rimg" + curID;
            var imgURL = "<?php echo _CUR_HOST ._DIR . "products/garment-spec.php?prod_id="?>" + newProdId;

            $(imgName).attr('href', imgURL);

            //alert(replacementNotRequired);
            if(replacementNotRequired != 'na')
            {
               populateSizes('#rproducts' + curID, '#rsizes' + curID, '');
               jQuery('#rqty' + curID).addClass("validate[required,custom[number]]");
               jQuery('#rsizes' + curID).addClass("validate[required] sizeclass");
               jQuery('#rqty' + curID).val(jQuery('#nqty' + curID).val());
            }
            else
            {
               jQuery('#rsizes' + curID).empty();
               jQuery('#rqty' + curID).val('');
               jQuery('#rsizes' + curID).removeClass("validate[required] sizeclass");
               jQuery('#rqty' + curID).removeClass("validate[required,custom[number]]");
            }



            $(".formError").remove();
         });
      });

   </script>
</head>
<body>

   <div id="topHeader" class="cAlign">
   <?php 
      if(user_getid() != "1")
      {
   ?>
	    <input type="hidden" name="guideactive" id="guideactive" value="0"/>
    <?php 
      }
      else
      { 
    ?>
	    <input type="hidden" name="guideactive" id="guideactive" value="1"/>
    <?php 
      }
    ?>
      <!-- Logo -->
      <a href="<?php  echo _CUR_HOST . _DIR ; ?>index.php" id="logo"><img src="<?php  echo _CUR_HOST . _DIR ; ?>_img/dtylink_logo.png" alt="DTY Link - Online Ordering System" /></a>
      <div style="float:right">
      <img src="<?php  echo _CUR_HOST . _DIR ; ?>_img/<?php  echo _CLIENT_LOGO; ?>" alt="<?php  echo _CLIENT_ALT; ?>" />
      </div>
      <div class="cBoth"><!-- --></div>
   </div> <!-- end topheader -->

   <!-- Category Section -->
   <div id="categorySection">
      <div class="cAlign">
         <!-- Categories -->
         <?php
            include('../_inc/middlenav.php');
         ?>
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
            &nbsp;&raquo;&nbsp;<strong>Create Return/Exchange</strong>
         </p>
      </div>
   </div> <!-- end breacrumbsSection -->

   <div class="cAlign cFloat">
      <!-- Blog Post List -->
      <div id="mainSection">
         <ul id="articles">
            <li>
               <div class="articleContent">
                  <h2 id="target-1">CREATE RETURN/EXCHANGE</h2>
                  <h4>Please ensure that you have read and understood our <a href="<?php echo _CUR_HOST ._DIR . "help/returns-policy.php"?>">Returns Policy</a> before proceeding.</h4>
                  <p>
                  <?php
                     $claim_date = date('Y-m-d');
                     $warranty_id = $warranty->warranty_id;
                     $order_id = $warranty->order_id;
                     $claim_date = $warranty->claim_date;
                     $name = $warranty->name;
                     $phone = $warranty->phone;
                     $reason = $warranty->reason;
                     $status = $warranty->status;
                     $email = $warranty->email;

                     //get the user id from the order id since admin might be submitting returns on behalf of the staff
                     //$orders = new orders();
                     //$orders->LoadOrderId($order_id);
                     $orders = $warranty->order;
                     $user_id = $orders->user_id;
                     //$remaining = $orders->CalcRemaining();
                     //echo "REMAIN: $remaining<BR>";

                  ?>
                  <form id="checkoutform" method="post" action="returns.php">
                     <div class="formrow">
                        <label>RA No:</label>
                        <span class="formwrap">

                           <?php
                           if($warranty_id)
                            echo '<input type="text" value="'.$warranty_id.'" readonly/>';
                           ?>
                        </span>
                     </div>
                     <div class="formrow">
                        <label>Order No:</label>
                        <span class="formwrap">
                           <input class="validate[required,length[0,30]] text-input" type="text" name="<?php echo _ORDER_ID;?>" id="<?php echo _ORDER_ID;?>" value="<?php echo $order_id;?>"/>
                        </span>
                     </div>
                     <div class="formrow">
                        <label>Claim Date:</label>
                        <span>
                           <input type="text" name="<?php echo _CLAIM_DATE;?>" value="<?php echo $claim_date;?>" readonly/>
                        </span>
                     </div>
                     <div class="formrow">
                        <label>Contact Name:</label>
                        <span>
                           <input class="validate[required,length[0,50]] text-input" type="text" class="big" name="<?php echo _NAME;?>" id="<?php echo _NAME;?>" value="<?php echo $name;?>" size="50"/>
                        </span>
                     </div>
                     <div class="formrow">
                        <label>Contact Number:</label>
                        <span>
                           <input class="validate[required,length[0,50]] text-input" type="text" class="big" name="<?php echo _PHONE;?>" id="<?php echo _PHONE;?>" value="<?php echo $phone;?>"/>
                        </span>
                     </div>
                     <div class="formrow">
                        <label>Contact Email:</label>
                        <span>
                           <input class="validate[required,custom[email]] text-input" type="text" class="big" name="<?php echo _EMAIL;?>" id="<?php echo _EMAIL;?>" value="<?php echo $email;?>" size="50"/>
                        </span>
                     </div>
                     <!--
                     <div class="formrow">
                        <label>Returns Type:</label>
                        <span>
                           <?php
                              $returntype = _checkIsSet(_RETURN_TYPE);
                              if(!$returntype)
                                 $returntype = $warranty->return_type;
                              generateComboDynamic($returnTypeArr, $returntype, _RETURN_TYPE, "required");
                           ?>
                        </span>
                     </div>
                     -->
                     <div class="formrow">
                        <label>Status:</label>
                        <span>
                        <?php
                           if(user_getid() == "1")
                           {
                              generateStaticCombo($warrantyStatusArr, $status, _STATUS, "optional");
                           }
                           else
                           {
                              echo '<input type="text" name="'._STATUS.'" value="'.$status.'" readonly/>';
//                              echo $status;
                           }
                        ?>
                        </span>
                     </div>
                     <div class="formrow">
                     <?php
                        if(user_getid() == "1")
                        {
                           $msg = _checkIsSet(_MESSAGE);
                     ?>

                        <label>Add Event</label>
                         <span>
                        <input type="text" name="<?php echo _MESSAGE;?>" class="bigger" value="<?php echo $msg;?>">
                        <!--
                        <label>Email Event to Claimant</label>
                        <input type="checkbox" name="emailEvent">
                        -->
                     </span>
                     <?php
                        }
                     ?>
                     </div>
   <table id="orderReg" cellspacing="0">
      <colgroup>
         <col id="column1"></col>
         <col id="column2"></col>
         <col id="column3"></col>
         <col id="column4"></col>
         <col id="column5"></col>
         <col id="column6"></col>
         <col id="column7"></col>
   <?php
      if(user_getid() == "1")
      {
   ?>
         <col id="column8"></col>
         <col id="column9"></col>
   <?php
      }
   ?>
      </colgroup>
      <thead>
         <tr>
            <th>Garment Ordered/Received</th>
            <th>Size</th>
            <th id="target-3">Qty</th>
            <th><div style="text-align:center;">&raquo;&nbsp;RETURN FOR&nbsp;&raquo;</div></th>
            <th>New Garment</th>
            <th>Size</th>
            <th id="target-5">Qty</th>
            <th id="target-6">Reason for Return</th>
   <?php
      if(user_getid() == "1")
      {
   ?>
            <th>Rcv</th>
            <th>Rjt</th>
   <?php
      }
   ?>
         </tr>
      </thead>
      <tbody>

      <?php
         //load garments based on order
         $lineitems = $orders->lineitems;
         $numlines = count($lineitems);
         if($numlines > 0)
         {
            $arrKeys = array_keys($orders->lineitems);
            $total = 0;
            $imageloc = "../". _IMGLOC;
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
               $cat_id = $li->product->cat_id;
               $qty = $li->qty;
               $size = $li->size;
               $size = str_replace ("/", "_", $size);
               $qty_text = "qty_$prod_id";
               $total += $tmpCost;
               $idsize = $prod_id."_".$size;
               $backordered = $li->backordered;
               $backordered_id = "li_$backordered" . "_$idsize";
               
               if($prod_id != 58)
               {
     ?>
               <tr>
                  <td>
                  <?php 
                  if($i == 0)
                  {
                  ?>
                  <a onclick="return false;" class="iframe" href="<?php echo _CUR_HOST ._DIR . "products/garment-spec.php?prod_id=$prod_id"?>"> <img src="<?php echo $imageloc . "../garment.png";?>" id="target-4"/></a>
                  <?php 
                  }
                  else 
                  {
                  ?>
                  <a onclick="return false;" class="iframe" href="<?php echo _CUR_HOST ._DIR . "products/garment-spec.php?prod_id=$prod_id"?>"> <img src="<?php echo $imageloc . "../garment.png";?>"/></a>
                  <?php 
                  }
                  ?>
                  &nbsp;<?php echo $item_number;?></td>
                  <td><?php echo $size;?></td>
                  <td>
                     <input type="text" size="3" class="qtyinput" name="qty_<?php echo $idsize;?>" id="qty_<?php echo $idsize;?>" value="<?php echo $qty;?>" readyonly>
                  </td>
                  <td><div style="text-align:center;">&raquo;</div></td>
                  <td>
                  <a id="rimg_<?php echo $idsize;?>" onclick="return false;" class="iframe" href="<?php echo _CUR_HOST ._DIR . "products/garment-spec.php"?>"> <img src="<?php echo $imageloc . "../garment.png";?>"/></a>
                     <?php
								/** ADMIN HAVE ACCESS TO FULL RANGE FOR RETURNS! **/                     
                     	if(minAccessLevel(_BRANCH_LEVEL))
                     	{
                     		$query = "select item_number, prod_id from products order by item_number";
                     	}
                     	else
                     	{
                     		$role_id = $_SESSION["ROLE_ID"];
                     		$crange = $_SESSION["crange"];
                     		$query = "select p.item_number, p.prod_id from products p, employee_role er, productcategory pc where p.cat_id = $cat_id and er.employeerole_id = $role_id and er.employeerole_id = pc.employeerole_id and pc.prod_id = p.prod_id order by category, myob_code";	                     		
                     	}
//                         $query = "select item_number, prod_id from products order by item_number";
                        generateComboQueryNA("rproducts_$idsize", $query, $default, "")
                     ?>
                  </td>
                  <td>
                     <div>
                        <select id="rsizes_<?php echo $idsize;?>" name="rsizes_<?php echo $idsize;?>" class="rsizes">
                           <option value=""></option>
                        </select>
                     </div>
                  </td>
                  <td><div><input type="text" class="rqtyinput" name="rqty_<?php echo $idsize;?>" id="rqty_<?php echo $idsize;?>" size="2"></div></td>
                  <td><div>
                           <?php
                              $returntype = _checkIsSet(_RETURN_TYPE);
                              if(!$returntype)
                                 $returntype = $warranty->return_type;
                              generateComboDynamic($returnTypeArr, $returntype, "returntype_" . $idsize, "");
                           ?>

                  </div></td>
               <?php
                  if(user_getid() == "1")
                  {
               ?>
                     <td><div><input type="text" name="rcv_<?php echo $idsize;?>" id="rcv_<?php echo $idsize;?>" size="2"></div></td>
                     <td><div><input type="text" name="rjt_<?php echo $idsize;?>" id="rjt_<?php echo $idsize;?>" size="2"></div></td>
               <?php
                  }
               ?>
               </tr>
     <?php
            	}
            }//for
            
         }
      ?>


   <?php
      /*** LOAD SOME HIDDEN VALUES HERE! && CHECK FOR OTHER LINEITEMS AND LOAD ***/
      if($action == _UPDATE || $warranty->action_type == _UPDATE)
      {

         $rlarr = $warranty->returnlines;

         for($i = 0; $i < count($rlarr);$i++)
         {
            $rl = $rlarr[$i];
            $w_line_id = $rl->line_id;
            $w_prod_id = $rl->prod_id;
            $w_myob_code = $rl->myob_code;
            $w_qty = $rl->qty;
            $w_size = $rl->size;

            $w_rprod_id = $rl->rprod_id;
            $w_rmyob_code = $rl->rmyob_code;
            $w_rqty = $rl->rqty;
            $w_rsize = $rl->rsize;
//            $w_rsize = str_replace ("/", "_", $w_rsize);
//            echo "prod: $w_rprod_id size: $w_rsize<BR>";
            $w_rcv = $rl->rcv;
            $w_rjt = $rl->rjt;
            $w_return_type = $rl->return_type;

//            if($w_line_id > 100)
            {
               echo "<input type='hidden' name='w_prod_id$w_line_id' id='w_prod_id$w_line_id' value='$w_prod_id'>\n";
               echo "<input type='hidden' name='w_myob_code$w_line_id' id='w_myob_code$w_line_id' value='$w_myob_code'>\n";
               echo "<input type='hidden' name='w_qty$w_line_id' id='w_qty$w_line_id' value='$w_qty'>\n";
               echo "<input type='hidden' name='w_size$w_line_id' id='w_size$w_line_id' value='$w_size'>\n";

               echo "<input type='hidden' name='w_rprod_id$w_line_id' id='w_rprod_id$w_line_id' value='$w_rprod_id'>\n";
               echo "<input type='hidden' name='w_rmyob_code$w_line_id' id='w_rmyob_code$w_line_id' value='$w_rmyob_code'>\n";
               echo "<input type='hidden' name='w_rqty$w_line_id' id='w_rqty$w_line_id' value='$w_rqty'>\n";
               echo "<input type='hidden' name='w_rsize$w_line_id' id='w_rsize$w_line_id' value='$w_rsize'>\n";
               echo "<input type='hidden' name='w_rcv$w_line_id' id='w_rcv$w_line_id' value='$w_rcv'>\n";
               echo "<input type='hidden' name='w_rjt$w_line_id' id='w_rjt$w_line_id' value='$w_rjt'>\n";
               echo "<input type='hidden' name='w_return_type$w_line_id' id='w_return_type$w_line_id' value='$w_return_type'>\n";
            }
         }

      }

   ?>

      </tbody>
   </table>
   <br/>
   <br/>
       <fieldset>
         <legend>Comments</legend>
         <textarea id="<?php echo _REASON;?>" name="<?php echo _REASON;?>" rows="10" cols="40"><?php echo stripslashes($reason);?></textarea>
       </fieldset>


       <fieldset>
         <!--<legend>Event Log</legend>-->
         <h3>Returns Event Log</h3>
         <table id="highlight-table3" cellspacing="0" cellpadding="0">
          <thead>
              <th width="20px"><input type="checkbox" name="checkAll" id="checkAll" onclick="jqCheckAll(this.id, 'registerBox');"></th>
              <th width="110px" align="left">Date</th>
              <th width="220px" align="left">Event</th>
              <th width="90px" align="left">Author</th>
          </thead>
            <tbody>
            <?php
               $warranty->printEventLog();
            ?>
            <tr></tr>
            </tbody>
         </table>
       </fieldset>

       </fieldset>

       <br/>
       <table>
         <tr>
            <td>
               <input type="submit" class="button" name="action" id="action" value="SUBMIT"/> (Please wait until we have <b>APPROVED</b> your return before sending the garments back to us.  You will receive an email to indicate this.)
            </td>
         </tr>
      </table>


       <input type="hidden" name="<?php echo _WARRANTY_ID;?>" value="<?php echo $warranty_id;?>" />

    </div>

    <input type="hidden" name="numRowsAdded" id="numRowsAdded" value="<?php echo $warranty->numrowsadded;?>"/>
    <!--plist-->

    </form>

                  </p>

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
<?php
   //save the session
$_SESSION[_WARRANTY_SESSION] = serialize($warranty);
unset($_SESSION['msg'])

?>