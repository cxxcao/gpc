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
require_once('../account/staffclass.php');

$action = _checkIsSet("action");
$orderingfor = _checkIsSet("orderingfor");
$orderingfor_id = _checkIsSet("orderingfor_id");
//echo "action:$action for: $orderingfor id: $orderingfor_id<BR>";
if(!user_isloggedin())
{
   header("Location: " . _CUR_HOST. _DIR);
}

if($action == "new")
{
   unset($_SESSION["order"]);
   $orders = new orders();
   //$_SESSION["savestoresession"] = $orders->oLocation_id;
   $_SESSION["order"] = serialize($orders);
}
else if($orderingfor_id)
{
   //check if existing user_id is different to sent order_id
   $orders = unserialize($_SESSION["order"]);
   if($orders->user_id != $orderingfor_id)
   {
      //update the ID
      $orders = new orders();
      $orders->user_id = $orderingfor_id;
      $orders->LoadUserIdAllowance($orders->getCurrentUserID());
      $_SESSION["order"] = serialize($orders);
   }
}
else 
{
	$orders = unserialize($_SESSION["order"]);
	//$_SESSION["order"] = serialize($orders);
}



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

	$.ajaxSetup({ cache: false });
   $(".alreadyorderedclass").hide();
   $(".prodimgcheck").hide();
   $('.slidedeck').slidedeck().options.scroll = false;
   $(".iframe").fancybox({
      'width'        : '80%',
      'height'       : '80%',
      'autoScale'    : false,
      'transitionIn' : 'none',
      'transitionOut': 'none',
      'type'         : 'iframe'
   });

   $("#dialog").dialog({
       autoOpen: false,
       show: {
           effect: "blind",
           duration: 1000
       },
       hide: {
           effect: "explode",
           duration: 1000
       }
   });      

   if($("#basket").val() == 'N')
   {
	   if(confirm('An incomplete order was found, would you like to continue with this order?'))
	   {
	         $.ajax(
	                 {
	                    type: "POST",
	                    url: "ajaxLoadBasket.php?action=load",
	                    dataType: 'json', // expecting json
	                    success: function(msg)
	                    {
	                       if(msg.success == true)
	                       {
		                       alert(msg.numitems + " items added to the cart from a previous incomplete order.");
	                    	   window.location = '<?php echo _CUR_HOST . _DIR . "products/neworder.php"?>';
	                       }
	                    },
	                    failure: function(msg)
	                    {
	                       alert('Error!');
	                       return false;
	                    }
	                 });		   
	   }
	   else
	   {
	         $.ajax(
	                 {
	                    type: "POST",
	                    url: "ajaxLoadBasket.php?action=delete",
	                    dataType: 'json', // expecting json
	                    success: function(msg)
	                    {
	                       if(msg.success == true)
	                       {
		                       //alert("All items from your previous incomplete order have been removed.");
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
   
   $("#orderingfor").focus(function(e)
   {
      $(this).select();
   });

   $("#orderingfor").mouseup(function(e){
      e.preventDefault();
   })
   
   $("#query").change(function(e){
	   if($(this).val().length == 0)
	   {
         $.ajax(
  	      {
  		      type: "POST",
  	         url: "ajaxSaveStoreSession.php?location_id=0",
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
			   }
	});
   
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

	      $("#location_id").val(data[1]);
	      $("#address").val(address);
	      $("#suburb").val(suburb);
	      $("#state").val(state);
	      $("#postcode").val(postcode);
	      $("#phone").val(phone);

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
	   });      

   //show already ordered basket
   $(".hiddenBasketID").each(function (e) {
      var curID = $(this).val();
      var spanID = "span_" + curID;
      $("#"+spanID).show();
   });

   $(".spine").click(function(e){
      var title = $(this).html();
      var pos = title.search("Starter Kit");

      if(pos == 0) //clicked on starter
      {
         $("#hiddenstarterkit").val("1");
      }
      else
         $("#hiddenstarterkit").val("0");

   });

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

//            pnotify.mousemove(function(){
//      tooltip.css({
//          'top': e.clientY + 12,
//          'left': e.clientX + 12
//      });
//            });
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

   $(".prodimg").fancybox({
      'titlePosition'   : 'inside'
   });

   $(".prodimgcheck").mouseover(function(e){
      var imgsrc = $(this).attr("src");

      if(imgsrc == "../_img/ok.png")
      {
         showInStock("Stock Available", "This item is currently in stock.");
      }
      else if(imgsrc == "../_img/outofstock.png")
      {
         showInStock("Out of Stock", "This item may need to be back ordered.");;
      }

      tooltip.pnotify_display();
      tooltip.css({
          'top': e.clientY + 12,
          'left': e.clientX + 12
      });
   });

   $(".alreadyorderedclass").mouseover(function(e){
      showInStock("Already Added", "This item is currently in your cart.");

      tooltip.pnotify_display();
      tooltip.css({
          'top': e.clientY + 12,
          'left': e.clientX + 12
      });
   });

   $(".qtyclass").change(function(e){
      var itemName = $(this).attr("id");
      var itemArr = itemName.split("_");
      var itemID = itemArr[1];
      var  qtyReq = $(this).val();
      var  sizeSel = $("#size_" + itemID).val();

      checkQty(itemID, sizeSel, qtyReq);
   });

   function checkQty(itemID, sizeSel, qtyReq)
   {
      if(qtyReq.length == 0)
         qtyReq = 0;
      param = 'item_id=' + itemID + '&item_size=' + sizeSel + '&qty=' + qtyReq;

      if(sizeSel.length <= 0 )
      {
         $("#img_" + itemID).hide();
      }
      else
      {
         $.ajax(
         {
            type: "POST",
            url: "ajaxQty.php",
            data: param,
            dataType: 'json', // expecting json
            success: function(msg)
            {
               if(msg.success == true)
               {
                  var qty = parseInt(msg.qty);
                  qtyReq = parseInt(qtyReq);
                  var img_prod_id = "img_" + itemID;
                  if(qty >= qtyReq) //compare the qty required with onhand
                  {
                     $("#" + img_prod_id).attr("src", "../_img/ok.png");
                     if(itemID > 59)
                     {
                        $("#"+itemID).show();
                        $("#sq"+itemID).hide();
                     }
                  }
                  else
                  {
                     $("#"+itemID).show();
                     $("#sq"+itemID).hide();
                     $("#" + img_prod_id).attr("src", "../_img/outofstock.png");
                  }

                  //$("#" + img_prod_id).show();
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

   $("#orderingfor").autocomplete("ajaxstaffquery.php", {
      width: 260,
      max:50,
      matchContains: true,
      selectFirst: false,
      cacheLength: 0
   });

   $("#orderingfor").result(function(event, data, formatted) {
      var fullname = data[0]
      var user_id = data[1];
      var user_name = data[2];
      $("#orderingfor_id").val(user_id);
      $("#range").val("");
      $("#checkoutform").submit();
   });

//    $("#checkout").click(function(e){
// 	   var sum = 0;
// 	    var quantity = 0;
// 	    $('.remainqty').each(function() {
// 	        sum += parseInt($(this).text());
// 	    });


// 	    if(sum > 0)
// 	    {
// 		    if(knitoptional == true && parseInt($("#remain5").text()) == 1 && sum == 1)
// 		    {
// 			    return true;
// 		    }
// 		    alert("There are still " + sum + " items to be ordered. Please order your complete allocation before checking out.");
// 		   return false;
// 	    }
// 	    else
// 		    return true;
//    });

		   $('#apptour').click(function(e){
				guidely.init ({ welcome: true, startTrigger: false });
			   });			
		      
			if($("#guideactive").val() == "0")
			{   
				guidely.add ({
					attachTo: '#target-1'
					, anchor: 'top-left'
					, title: 'Ordering'
					, text: 'Welcome to the New GPC Online Ordering System. This guide will help you place a new order.  '
				});

				guidely.add ({
					attachTo: '#totals'
					, anchor: 'top-left'
					, title: 'Cart Total'
					, text: 'As items are added into the cart, the totals automatically update and any entitlements are deducted.'
				});
				
				guidely.add ({
					attachTo: '#target-3'
					, anchor: 'top-left'
					, title: 'Garment Category'
					, text: 'Click on these tabs to navigate through the different garment categories.'
				});
				
				guidely.add ({
					attachTo: '#target-4' 
					, anchor: 'top-right'
					, title: 'Add to Cart'
					, text: 'Enter the quantity you would like to order, then select the size and click on <b>Add to Cart</b>.<br/><br/>For some garments, you will be prompted to confirm the first name to be <b>embroidered.</b><br/><br>Size <b>SP</b> is a Made To Measure option, if the size you require is not listed here, select this option and we will contact you to determine a suitable size this may mean that we make a garment specifically for you.'
				});

				guidely.add ({
					attachTo: '#slidingTopFooter'
					, anchor: 'top-right'
					, title: 'Updated Cart Info'
					, text: 'Once the item has been added to the cart, the details here will be updated. ' 
				});

		 		guidely.add ({
		 			attachTo: '#target-6'
		 			, anchor: 'top-right'
		 			, title: 'Checkout!'
		 			, text: 'Click here if you\'re done shopping and proceed to checkout to finalise your order.'
		 		});	
		 		$("#guideactive").val('1');
			}			

	   		<?php 
	   			   if($orders->isFirstOrder() && !minAccessLevel(_ADMIN_LEVEL))
	   			   {
	   			   ?>
	   				   guidely.init ({ welcome: true, startTrigger: false });
	   				<?php
	   				}
	   				?>
});
</script>

            <!-- Fire up the slider -->
            <script type="text/javascript">
                // The most basic implementation using the default options

            </script>
<body>
<?php 
?>
	<div id="topHeader" class="cAlign2">
 <input type="hidden" name="guideactive" id="guideactive" value="0"/>
 <?php
 $basketEmpty = "Y"; 
 if($action == "new")
 {
 	$basketEmpty = $orders->isBasketEmpty();
 }
 ?>
 <input type="hidden" name="basket" id="basket" value="<?php echo $basketEmpty?>"/>
 
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
		<div class="cAlign2">
			<!-- Categories -->
         <?php
            include('../_inc/middlenav.php');
         ?>

			<div style="clear: both"><!-- --></div>


		</div>
	</div> <!-- end categorySection -->

	<!-- Breadcrumbs -->
	<div id="breadcrumbsSection">
	
<div class="row promoWrapper headerBarBanner">
        
    <div class="flexslider promoSlider container">
            <ul class="slides">
                        <!-- Begin Two Link Banner-->
                        <li class="slide1 promoSlide twoLinkBanner largePercentOff htmlSlider">
                         <div class="contentWrapper">
                             <h3>
                                        <span class="extraBoldText shippingText">GPC ASIA PACIFIC - ONLINE ORDERING</span>
                                        <span class="extraBoldText smallText">Uniforms &amp; Workwear</span>
                             </h3>
                                <span class="divider"><img src="<?php  echo _CUR_HOST . _DIR ; ?>_img/divider.png" alt=""></span>
                         </div>
                      </li>
                      
                   <li class="slide2 promoSlide twoLinkBanner largePercentOff htmlSlider">
                         <div class="contentWrapper">
                             <h3>
                             	<span class="extraBoldText shippingText">12.5% DISCOUNT WHEN YOU SELECT THE BULK ORDER OPTION</span>
                                	<span class="extraBoldText smallText">SELECT THE OPTION AT CHECKOUT FOR THE DISCOUNT TO APPLY</span>
                             </h3>
                                <span class="divider"><img src="<?php  echo _CUR_HOST . _DIR ; ?>_img/divider.png" alt=""></span>                             
                         </div>
                      </li>                           
            </ul>
        </div>
    </div>	
    	
	
		<div class="cAlign2 cFloat">
			<p>
				You are here:&nbsp;&nbsp;
				Home
				&nbsp;&raquo;&nbsp;Order Management
            &nbsp;&raquo;&nbsp;<strong>New Order</strong>
			</p>
		</div>

	</div> <!-- end breacrumbsSection -->

	<div class="cAlign2 cFloat">
		<!-- Blog Post List -->
		<div id="mainSection">
			<ul id="articles">
				<li>

					<div class="articleContent">
						<h2 id="target-1">New Order</h2>
                  <form id="checkoutform" method="get">
                  <?php
                     $range = _checkIsSet("range");
                     $role = _checkIsSet("role");
                     $readOnly = "";
                     
                     if(!$role)
                        $role =  $_SESSION["ROLE_ID"] ;

                  if(!$_SESSION['order'])
                  {
                     $orders = new orders();
                     //reset admin's allowance if any
                     //$orders->LoadUserIdAllowance($_SESSION[_USER_ID]);

                  }
                  else
                  {
                     $orders = unserialize($_SESSION["order"]);
                  }
                     $orders->LoadUserIdAllowance($orders->getCurrentUserID());
                     if(!$orderingfor)
                     {
                        $sfullname = $orders->orderingfor_name;
                        if(!$range)
                           $range = $orders->staff_range;
                     }
                     else //staff selected
                     {
                        if($orderingfor_id)
                        {
                           $sfullname = $orders->orderingfor_name;
                          if(!$range)
                              $range = $orders->staff_range;

                        }
                        else
                           $sfullname = "Error - Staff Not Found";
                     }
                     if(!$range)
                     {
                        $range =  $_SESSION["crange"];
                     }
                    // if(minAccessLevel(_BRANCH_LEVEL))
                     {
                  ?>
                        <p>Range:
                        <select name="range" id="range" onchange="submit()">
                          <option value=""></option>
                        <?php
                           if($range == "1")
                           {
                        ?>
                              <option value="1" selected>Womenswear</option>
                        <?php
                           }
                           else
                           {
                        ?>
                              <option value="1">Womenswear</option>
                         <?php
                           }
                        ?>
                        <?php
                           if($range == "2")
                           {
                        ?>
                              <option value="2" selected>Menswear</option>
                        <?php
                           }
                           else
                           {
                        ?>
                              <option value="2">Menswear</option>
                         <?php
                           }
                        ?>
                        </select>
                     <?php
                     }
                    // else //staff can't search
                     {
                     //	$readOnly = "readonly";
                     ?>
                        <p>
                     <?php
                     }
                     
                     $q = _checkIsSet("q");
                     ?>
                     <!-- 
                        <label>Location:</label>
                        <span class="formwrap">
	                        <input class="query" type="text" name="q" id="query" value="<?php echo $q;?>" <?php echo $readOnly;?>/>
                           <input type="hidden" name="location_id" id="location_id" value=""/>
                        </span>                     
							 -->
							 <?php 
							 if(minAccessLevel(_BRANCH_LEVEL))
							 {
							 ?>
                        <label>Ordering For:</label>
                        <span class="formwrap">
                           <input class="validate[required] query" type="text" name="orderingfor" id="orderingfor" value="<?php echo $sfullname;?>" <?php echo $readOnly;?>/>
                           <input type="hidden" name="orderingfor_id" id="orderingfor_id" value="<?php echo $orders->user_id;?>"/>
                        </span>
                     <?php 
                     }
                     ?>                        
                     </p>
			           <input type="hidden" name="role" id="role" value="<?php echo $role_id;?>"/>      
                  </form>
                  <?php

                     if(!minAccessLevel(_ADMIN_LEVEL))
                     {
                        $role_id = $orders->staff_role;
                        $query = "select * from employee_role where employeerole_id = $role_id";
                     }
                     else 
                        $query = "select * from employee_role";
                     
//                     echo "!!$query<BR>";
                     $res = db_query($query);
                     $num = db_numrows($res);
                     if($num > 0)
                     {
                     	$roleB = false;
                        for($i = 0; $i < $num; $i++)
                        {
                           $role_id = db_result($res, $i, "employeerole_id");
                           $name = db_result($res, $i, "name");

                           if($role == $role_id)
                           {
                              echo "<b>";
                              $roleB = true;
                           }
                           
                           if($orders->staff_role == $role_id && !$roleB && !$role)
                           {
                           	$roleB = true;
                           	echo "<b>";
                           }

                           $pdflink = _CUR_HOST . _DIR . "help/$role_id.pdf";
                  ?>
                         <!--  <a href="<?php echo $pdflink;?>" target="_blank"><img src="../_img/pdf_sm.png" border="0"/></a>--> 
                         <a href="<?php echo _CUR_HOST ._DIR;?>products/neworder.php?role=<?php echo $role_id;?>&range=<?php echo $range;?>"><?php echo $name;?></a> | 
                  <?php
                           if($roleB)
                           {
                              echo "</b>";
                           }
                        }
                     }

                     if(!minAccessLevel(_BRANCH_LEVEL))
                     {
                        //echo "<br/><br/><br/><br/><br/>";
                     }
                  ?>
                  <p>
   <div class="fr">
   <?php
      include('../_inc/cartwrap.php');
   ?>
   </div>

   <div id="sliderSection" class="fl">
      <div class="cAlign">
         <div id="slidedeckFrame" class="skin-slidedeck-basics">
           <dl id="target-3" class="slidedeck">
           <input type="hidden" name="hiddenstarterkit" id="hiddenstarterkit" value="0"/>
     
           <?php
              $cquery = "select * from category"; //dont show starter kits unless at least branch manager

              $cres = db_query($cquery);
              $cnum = db_numrows($cres);
              if($cnum > 0)
              {
                 for($c = 0; $c < $cnum; $c++)
                 {
                    $name = db_result($cres, $c, _NAME);
                    $cat_id = db_result($cres, $c, _CAT_ID);
/*
                    if($range == 1)
                    {
                       switch($cat_id)
                       {
                          case "2":
                             $name = "Upper - Blouses/Shirts";
                             break;
                          case "3":
                             $name = "Lower - Skirts/Pants/Dresses";
                             break;
                       }
                    }
                    else if($range == 2)
                    {
                       switch($cat_id)
                       {
                          case "2":
                             $name = "Upper - Shirts";
                             break;
                          case "3":
                             $name = "Lower - Pants";
                             break;
                          case "7":
                          	$name = "Tie";
                       }
                    }
*/
                     $query = "select * from products where cat_id = $cat_id";
                     if($range)
                     	$query .= " and category = $range";
                     
//                      $query .=  " order by category, myob_code";
						  $role_id = _checkIsSet("role");
						  if(!$role_id)
						     $role_id = $orders->staff_role;

                    $query = "select * from employee_role er, productcategory pc, products p where p.category = '$range' and p.cat_id = $cat_id and er.employeerole_id = $role_id and er.employeerole_id = pc.employeerole_id and pc.prod_id = p.prod_id";
                    
//                     if(!minAccessLevel(_BRANCH_LEVEL))
//                         $query .= " and p.cat_id !=4";

                    $query .="  order by category, myob_code";
//                     echo "$query<BR>";
                     $res = db_query($query);
                     $num = db_numrows($res);

                     if($num > 0)
                     {
           ?>

              <dt><?php echo $name;?></dt>
              <dd>
               <div class="container_12">
                  <?php
                        {
                           for($i = 0; $i < $num; $i++)
                           {
                              $imageloc = "../". _IMGLOC;
                              $prod_id = db_result($res, $i, _PROD_ID);
                              $item_number = db_result($res, $i, _ITEM_NUMBER);
                              $myobcode = db_result($res, $i, _MYOB_CODE);
                              $description = db_result($res, $i, _DESCRIPTION);
                              $fabric = db_result($res, $i, _FABRIC);
                              $colour = db_result($res, $i, "colour");
                              $price = db_result($res, $i, "price");
                              $origin = db_result($res, $i, "origin");
                              
// 							         if($prod_id > 58 && date("Y-m-d") < "2017-05-01")
// 							        		$price = $price*0.9;//10% discount                                 
                              
                              //$formattedPrice = "\$" . formatNumber($price) . " (inc GST)";
                              
                              if($orders->isAUS == "N")
                              {
                                 $price = db_result($res, $i, "price_nz");
                                 $formattedPrice = "NZ\$" . formatNumber($price) . " (no GST)";
                              }
                              else
                              {
                                 $price = db_result($res, $i, "price");
                                 $price *=1.1;
                                 $formattedPrice = "\$" . formatNumber($price) . " (inc GST)";
                              }


                              	$sm_img = $item_number . "_tmb.jpg";
                              	$img = $item_number . ".jpg";


                              if($i == 0)
                                 $tmpImg = $sm_img;

                              if(!file_exists($imageloc. $sm_img))
                              {
                                 $sm_img = "noimage.jpg";
                              }
                              $size_text = "size_$prod_id";
                              $qty_text = "qty_$prod_id";
                              $span_text = "span_$prod_id";
                              
                     ?>

                     <div class="grid_3">
                     <div class="img-wrapper">
                              <a class="prodimg" href="<?php echo $imageloc . $img;?>" title="<?php echo $item_number ." - ". $description; ?>"><img alt="<?php echo $item_number;?>" src="<?php echo $imageloc . $sm_img;?>" /></a>
					<div class="tags tags-left">                
                <?php 
                 if($prod_id == 3 || $prod_id == 26) 
                 {
                ?>              

                  <span class="label-tags"><span class="label label-danger">PRE-ORDER NOW</span></span>
                            
                <?php 
                 }
                 if($origin == "Australia")
                 {
                ?>
         					
               <?php 
                 }
                ?>
					</div>    <!-- tags -->
							</div>                              
                        <div class="box">
   
                           <div class="blocktmp">
                              <p><?php echo $item_number;?></p>
                              <p><?php echo $description;?></p>
                              <p><?php echo $fabric;?></p>
                              <!-- 
                              <p><?php echo $colour;?></p>
										 -->
                                <p><?php echo $formattedPrice;?></p>
                              
                              <p><a onclick="return false;" class="iframe" href="<?php echo _CUR_HOST ._DIR . "products/sizecalc.php?range=$range&cat_id=$cat_id&prod_id=$prod_id"?>">Item Measurements</a></p>
                              <p>
                                 QTY:&nbsp;<input class="qtyclass" type="text" name="<?php echo $qty_text;?>" id="<?php echo $qty_text;?>" value="1" size="3"/><span id="<?php echo $span_text;?>" class="alreadyorderedclass"><img src="../_img/basket.png"/ title="Already Ordered"></span>&nbsp;
                                 <?php
                                    $s_query = "select size, size from sizes where prod_id = $prod_id";
                                    generateComboQuerySizes($size_text, $s_query, "", $pid);

                                    $img_id = "img_" . $prod_id;
                                 ?>
                                 <img class="prodimgcheck" id="<?php echo $img_id?>" src="<?php echo $imageloc . "../ok.png";?>"  onmousemove="tooltip.css({'top': event.clientY+12, 'left': event.clientX+12});" onmouseout="tooltip.pnotify_remove();"/>
                                 <!--<a onclick="return false;" class="iframe" href="<?php echo _CUR_HOST ._DIR . "products/sizecalc.php?prod_id=$prod_id"?>"> <img src="<?php echo $imageloc . "../calculator.png";?>"/></a>-->
                              </p>
                              <p id="target-4">
	                              <a onclick="return false;" class="productPriceWrapRight" id="<?php echo $prod_id;?>" name="<?php echo $prod_id;?>" href="cart.php?action=add&prod_id=<?php echo $prod_id;?>&qty=1">Add to cart</a>

                              </p>

                           </div>
                        </div>
                     </div>
                     <?php
                           }
                      
                        }//else
                        //some hidden values for the basket icon
                        for($i = 0; $i < count($orders->prodOnlyLines); $i++)
                        {

                        }

                        $prodOnlyLines = $orders->prodOnlyLines;
                        $numProdOnly = count($prodOnlyLines);
                        if($numProdOnly > 0)
                        {
                           $arrKeys = array_keys($orders->prodOnlyLines);
                           for($i = 0; $i < $numProdOnly; $i++)
                           {
                              $key = $arrKeys[$i];
                        ?>
                              <input type="hidden" class="hiddenBasketID" value="<?php echo $key;?>"/>
                        <?php
                           }
                        }


//                     }
//                     else
//                     {
//                        echo "NO ITEMS ARE AVAILABLE UNDER THIS CATEGORY";
//                     }
                  ?>
               </div>
              </dd>
           <?php
                     }
                 }//category (garment type) for loop
              }
           ?>
           </dl>

         </div> <!-- end slidedeckFrame -->
      </div>
   </div> <!-- end sliderSection -->
                  </p>


					</div>
				</li>

			</ul>

		</div> <!-- end mainSection -->

		<!-- Sidebar -->
		<div id="sidebar">

		</div> <!-- end sidebar -->

	</div>

<div id="dialog" title="First Name">
    <p>Please confirm the first name to embroider</p>
    <input class="personalNameText" type="text" value="<?php echo explode(" ", $orders->orderingfor_name)[0] ;?>"/>
     <p class="importantmsg">Please refer to the size guide before selecting your size as any customised items with your first name embroidered and cannot be exchanged.</p>
    
</div>   

   <?php
      include('../_inc/footer.php');
   ?>


</body>
</html>