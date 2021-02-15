$(document).ready(function(){ 
	var path = "../";
	$("#basketItemsWrap li:first").hide();
	$("#slidingTopContent").hide();
	var cartval = $("#cartval").val();
	var allowance = $("#hiddenallowance").val();
	var alreadyordered = $("#hiddenalreadyordered").val();
//	if(cartval.length == 0)
//		cartval = 0;
//	if(allowance.length == 0)
//		allowance = 0;
	if($("#hiddenalreadyordered").length)
    {
		if(alreadyordered.length == 0)
		{
			alreadyordered = 0;	
		}
    }
	var totalStr = calcCart(allowance, cartval, alreadyordered);
	updateAllocationTable();
//	alert(totalStr);
	$("#slidingTopTrigger").live("click", function(event) {
      	$("#slidingTopContent").slideToggle("slow", function(){
			if ($("#slidingTopContent").is(":visible")) 
			{
				$("#slidingTopTrigger").text('Hide Cart');
			    $("#totals").html(totalStr);
			    $('.money').formatCurrency();
			} 
			else 
			{
				$("#slidingTopTrigger").html('View Cart');
			    $("#totals").html(totalStr);
			    $('.money').formatCurrency();
			}
		});
    }); 


	$(".productPriceWrapRight").click(function() {
		var productIDVal 			= this.id;
		var sizeID = "size_" + productIDVal;
		var qtyID = "qty_" + productIDVal;
		//check for qty & size;
		var sizeVal = $("#" + sizeID).val();	
		var qtyVal = $("#" + qtyID).val();		
		
		var numberRegex = /^[+-]?\d+(\.\d+)?([eE][+-]?\d+)?$/;

		if(sizeVal.length == 0)
		{
			alert('Please specify a size.');
			return false;
		}
		else if(qtyVal.length == 0)
		{
			alert('Please enter a quantity.');		
			return false;			
		}
		else if(!numberRegex.test(qtyVal))
		{
			alert('Please enter valid number.');
			return false;			
		}

	//	var upperGarments = ["1","2","3","4","5","6","7","8","9","10","13","19","20","24","25","27","28","29","30","36"];
		var upperGarments = ["1","2","19","20","24","25"];
		var idxPID = upperGarments.indexOf(productIDVal);
		//console.log("IDX: " + idxPID + " PID: " + productIDVal);
		if(idxPID >= 0) //ladies uppers
		{
//			$("#dialog").dialog("open");	
//			$("#dialog").on( "dialogclose", function( event, ui ) {
//			      alert("something");
//			});		
			
			$("#dialog").dialog({
			    modal: true,
			    buttons: {
			    "Submit": function()
			    {
			    	//var embLogo = $(".embselect").val();
			    	var embLogo = $('.personalNameText').val();
			    	//console.log("LOGO TO EMB: " + embLogo);
			    	addtocart(productIDVal, sizeID, qtyID, sizeVal, qtyVal, embLogo);
			    	$("#dialog").dialog("close");	
			    }},
			    close: function(event, ui){

			    }
			});
			$("#dialog").dialog("open");
			
		}
		else
			addtocart(productIDVal, sizeID, qtyID, sizeVal, qtyVal,"");
				
	});
	
	
	function addtocart(productIDVal, sizeID, qtyID, sizeVal, qtyVal, embroideryLogo)
	{
		var ajaxurl = path + "products/ajaxcart.php?action=add&prod_id="+productIDVal+"&qty="+qtyVal+"&size="+sizeVal+"&embroideryLogo="+embroideryLogo;
		if ($("#slidingTopContent").is(":visible")) {
			$("#notificationsLoader").html('<img src="../_img/fbloader.gif" alt="loading..."/>');
		
			$.ajax({  
			type: "POST",
			async: false,
			url: ajaxurl,  
			success: function(theResponse) {
				var newSizeVal = sizeVal.replace(/[- \/)(]+/g,'-');
				tmpProdVal = "#productID_" + productIDVal + "_" + newSizeVal;
				
				if(embroideryLogo.length > 0)
					tmpProdVal += "_" + embroideryLogo;
		
				if( $(tmpProdVal).length > 0)
				{
					$(tmpProdVal).animate({ opacity: 0 }, 500);
					$(tmpProdVal).before(theResponse).remove();
					$(tmpProdVal).animate({ opacity: 0 }, 500);
					$(tmpProdVal).animate({ opacity: 1 }, 500);
					$("#notificationsLoader").empty();
					
				} 
				else 
				{
					$("#basketItemsWrap li:first").before(theResponse);
					$("#basketItemsWrap li:first").hide();
					$("#basketItemsWrap li:first").show("slow");  
					$("#notificationsLoader").empty();			
				}
				
				orderVal = 0.0;
				$('.productPrice').each(function() {
					tmpVal = $(this).text();
					var number = Number(tmpVal.replace(/[^0-9\.]+/g,""));
					orderVal += number;
				});					
				
				totalStr = calcCart(allowance, orderVal, alreadyordered);
				updateAllocationTable();
				$("#slidingTopTrigger").html('Hide Cart');				
			    $("#totals").html(totalStr);
				$('.money').formatCurrency();
	
			}  
			}); 
	
		} else {
			$("#slidingTopContent").slideToggle("slow", function(){		
																 
				//$("#slidingTopFooterLeft").html('<a href="aaa.htm" onclick="return false;" id="slidingTopTrigger">Hide Cart</a>');
				$("#notificationsLoader").html('<img src="../_img/fbloader.gif" alt="loading..."/>');
			
				$.ajax({  
				type: "POST",
				async: false,
				url: ajaxurl,  
				success: function(theResponse) {
					//alert(theResponse);
					var newSizeVal = sizeVal.replace(/[- \/)(]+/g,'-');
					tmpProdVal = "#productID_" + productIDVal + "_" + newSizeVal;
					
					if(embroideryLogo.length > 0)
						tmpProdVal += "_" + embroideryLogo;					
					
					if( $(tmpProdVal).length > 0){
						$(tmpProdVal).animate({ opacity: 0 }, 500);
						$(tmpProdVal).before(theResponse).remove();
						$(tmpProdVal).animate({ opacity: 0 }, 500);
						$(tmpProdVal).animate({ opacity: 1 }, 500);
						$("#notificationsLoader").empty();
						
					} else {
						$("#basketItemsWrap li:first").before(theResponse);
						$("#basketItemsWrap li:first").hide();
						$("#basketItemsWrap li:first").show("slow");  
						$("#notificationsLoader").empty();			
					}
					orderVal = 0.0;
					$('.productPrice').each(function() {
						tmpVal = $(this).text();
						var number = Number(tmpVal.replace(/[^0-9\.]+/g,""));
						orderVal += number;
					});		
	
					totalStr = calcCart(allowance, orderVal, alreadyordered);		
					updateAllocationTable();
					$("#slidingTopTrigger").html('View Cart');
				    $("#totals").html(totalStr);		
				    $('.money').formatCurrency();
				}  
				}); 
				
				
				$("#slidingTopTrigger").fadeTo(4000, 1, function(){
					$("#slidingTopContent").slideToggle("slow", function(){
						$("#slidingTopTrigger").html('View Cart');
					    $("#totals").html(totalStr);
					    $('.money').formatCurrency();
					});
					
				});
	
			});												 
		}	
	}
	
	$("#basketItemsWrap li img").live("click", function(event) { 
		var productIDValSplitter 	= (this.id).split("_");
		var productIDVal 			= productIDValSplitter[1];
		var sizeVal 				= productIDValSplitter[2];
		var tmpProdVal = "#productID_" + productIDVal + "_" + sizeVal;		
		var ajaxurl = path + "products/ajaxcart.php?action=delete&prod_id="+productIDVal+"&qty=1&size=" + sizeVal;

		$("#notificationsLoader").html('<img src="../_img/fbloader.gif" alt="loading..."/>');

		$.ajax({  
		type: "POST",  
		url: ajaxurl,  
		success: function(theResponse) {
			$(tmpProdVal).hide("slow",  function() {
				$(this).remove();
				updateAllocationTable();
			});
			$("#notificationsLoader").empty();
		
			orderVal = 0.0;
			$('.productPrice').each(function() {
				tmpVal = $(this).text();
				var number = Number(tmpVal.replace(/[^0-9\.]+/g,""));
				orderVal += number;
			});	
			
			
		    orderVal -= theResponse;
		    totalStr = calcCart(allowance, orderVal, alreadyordered);					
		    
			$("#slidingTopTrigger").html('View Cart');
		    $("#totals").html(totalStr);		
		    $('.money').formatCurrency();			
		}
					
		});  	
		
	});
	
	function updateAllocationTable()
	{
		var orderedArr = new Array();
		var cartArr = new Array();
		var remainArr = new Array();
		var emptyCatArr = new Array();		
		for(i = 1; i < 10; i++)
		{
	       //orderedArr[i] = "0";//parseInt($("#ordered" + i).text());
	       //remainArr[i] = "0";//parseInt($("#remain" + i).text());
  		   var maxCat = parseInt($("#max" + i).val());
   		   var remain = parseInt($("#remain" + i).text());
  		   var ordered = parseInt($("#ordered" + i).text());
  		   var incart = parseInt($("#cart" + i).text());
	       
		   var remaining = remain - (ordered + incart);
		   if(remaining < 0)
			   remaining = 0;  		   
  		   //alert('max: ' + maxCat + ' ordered: ' + ordered);
  		   orderedArr[i] = parseInt(ordered);
	       //remainArr[i] = parseInt(remaining);	       
		   cartArr[i] = parseInt("0");//parseInt($("#ordered" + i).text());
	       remainArr[i] = parseInt("0");//parseInt($("#remain" + i).text());
		   
	       emptyCatArr[i] = true;
		}
		
		var catVal = 0;
		var catQty = 0;
	    var maxCat = 0;
        var remain = 0;
		
		/* Calc remaining allocations */
		$(".productCategory").each(function()
		{
		   var catArr = $(this).text().split("_");
		   var catVal = catArr[0];
		   catQty = parseInt(catArr[1]);
		   var prod_id = parseInt(catArr[2]);

		   var remain = parseInt($("#remain" + catVal).text());
		   
		   var remaining = remain - catQty;
		   if(remaining < 0)
			   remaining = 0;
		   
		   //alert("cat: " + catVal + " ordered: " + orderedArr[catVal] + " qty: " + catQty);
		   //orderedArr[catVal] = catQty;
		   cartArr[catVal] += catQty;
		   remainArr[catVal] = remaining;
		   
		   
		   //alert("cat: " + catVal + " ordered: " + orderedArr[catVal] + " qty: " + catQty + " remain: " + remainArr[catVal] );		   
		   //$("#ordered" + catVal).text(catQty);
		   //$("#remain" + catVal).text(remaining);		
		   emptyCatArr[catVal] = false;
	
		});		
		
		   var catMsg = "";
		for(i = 1; i < 10; i++)
	    {
		   var alloc = parseInt($("#alloc" + i).text());
		   var maxCat = parseInt($("#max" + i).val());
		   var ordered = parseInt(orderedArr[i]);
		   var curCart = parseInt(cartArr[i]);
		   var curRemain = parseInt(remainArr[i]);

		   if(emptyCatArr[i] == true)
		   {
			   curRemain = alloc - ordered;
			   curOrdered = 0;
		   }
		   else
		   {
 		      var curRemain = maxCat - (curCart + ordered);
		   }
		   
		   if(curRemain < 0)
		      curRemain = 0;


		   if(curRemain == 0 && alloc > 0)
		   {
			   var category = "";
			   var alertMsg = false;
			   switch(i)
			   {
			      case 1: 
			    	  category = "Jacket";
			    	  alertMsg = true;
			    	  break;
			      case 2: 
			    	  category = "Lower";
			    	  alertMsg = true;
			    	  break;			
			      case 3: 
			    	  category = "Upper";
			    	  alertMsg = true;
			    	  break;			
			      case 4: 
			    	  category = "Outer";
			    	  alertMsg = true;
			    	  break;
			      case 5: 
			    	  category = "Flame Retardant";
			    	  alertMsg = true;
			    	  break;
			      case 7: 
			    	  category = "Headwear";
			    	  alertMsg = true;
			    	  break;			    
			      case 8: 
			    	  category = "Footwear";
			    	  alertMsg = true;
			    	  break;	
               case 9: 
                  category = "Optional";
                  alertMsg = true;
                  break;    			    	  
			   }

			   catMsg += category + ", ";
		   }

		   
		   $("#cart" + i).text(curCart);
		   $("#remain" + i).text(curRemain);	
	    }
		   
		catMsg = catMsg.substring(0, catMsg.length - 2);
		   if(alertMsg == true && $("#alert1").val() == 0)
		   {
			   //alert("You have used up your " + catMsg + " entitlement(s), any additional garments purchased in this category will need to be paid for with a Credit Card. ")
//			   $("#alert1").val(1);
		   }
	}
	
	
	function calcCart(allowance, orderVal, alreadyordered)
	{
		var isAUS = $("#auscurrency").val();
		var totalStr = "";
		var remaining = 0;
		var payable = 0;

		var ajaxurl = path + "products/ajaxCalcPayable.php";
	      $.ajax(
	    	      {
	    	         type: "POST",
	    	         url: ajaxurl,
	    	         dataType: 'json', // expecting json
	    	         async: false,
	    	         success: function(msg)
	    	         {
	    	        	 if(msg != null)
	    	        	{
		    	            if(msg.success == true)
		    	            {
		    	            	remaining = msg.remaining;
		    	            	payable = msg.payable;
		    	            	orderVal = msg.grandtotal;
		    	            	
		    	            }
		    	            else
		    	            {
		    	            }
	    	        	}
	    	         },
	    	         failure: function(msg)
	    	         {
	    	         }
	    	      });	
		
		if(payable > 0)
		{
			if(isAUS == 0)
				totalStr = '<ul class="ulcart">'+
				'<li class="carttotal"><div class="cartmoney">Available Allowance: </div><div class="money">NZ'+allowance+'</div></li>'+
				'<li class="carttotal"><div class="cartmoney">Cart Total: </div><div class="money">NZ'+orderVal+'</div></li>'+
				'<li class="carttotal"><div class="cartmoney">Payable:</div><div class="money">NZ'+payable+'</div></li>'+
				'</ul>';				
			else
				totalStr = '<ul class="ulcart">'+
				'<li class="carttotal"><div class="cartmoney">Available Allowance: </div><div class="money">'+allowance+'</div></li>'+
				'<li class="carttotal"><div class="cartmoney">Cart Total: </div><div class="money">'+orderVal+'</div></li>'+
				'<li class="carttotal"><div class="cartmoney">Payable: </div><div class="money">'+payable+'</div></li>'+
				'</ul>';
		}
		else
		{
			payable *= -1; //gst
			if(isAUS == 1)			
				totalStr = '<ul class="ulcart">'+
				'<li class="carttotal"><div class="cartmoney">Available Allowance: </div><div class="money">'+allowance+'</div></li>'+
				'<li class="carttotal"><div class="cartmoney">Cart Total: </div><div class="money">'+orderVal+'</div></li>'+
				'<li class="carttotal"><div class="cartmoney">Remaining: </div><div class="money">'+remaining+'</div></li>'+
				'</ul>';		
			else
				totalStr = '<ul class="ulcart">'+
				'<li class="carttotal"><div class="cartmoney">Available Allowance: </div><div class="money">NZ'+allowance+'</div></li>'+
				'<li class="carttotal"><div class="cartmoney">Cart Total: </div><div class="money">NZ'+orderVal+'</div></li>'+
				'<li class="carttotal"><div class="cartmoney">Remaining:</div><div class="money">NZ'+remaining+'</div></li>'+
				'</ul>';		
				
		}	

		return totalStr;
	}
});

