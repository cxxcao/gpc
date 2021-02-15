function jqCheckAll(id, name)
{
   $("INPUT[@name=" + name + "][type='checkbox']").attr('checked', $('#' + id).is(':checked'));
}

function calcSize(pid, chest, waist, hip, lowwaist, collar)
{
	var chestval = $("#Chest").val();
	var waistval = $("#Waist").val();
	var hipval = $("#Hip").val();
	var lowwaistval = $("#Lowwaist").val();
	var collarval = $("#Collar").val();
	
	
//	httpObject = getHTTPObject();
//	if (httpObject != null) 
//	{
//		var chestval = 0;
//		var waistval = 0;
//		var hipval = 0;
//		var lowwaistval = 0;
//		var collarval = 0;
//		if(chest == 1)
//		{
//			chestval = document.getElementById("Chest").value;
//		}
//		
//		if(waist == 1)
//		{
//			waistval = document.getElementById("Waist").value;
//		}
//
//		if(hip == 1)
//		{
//			hipval = document.getElementById("Hip").value;
//		}
//
//		if(lowwaist == 1)
//		{
//			lowwaistval = document.getElementById("Lowwaist").value;
//		}
//		
//		if(collar == 1)
//		{
//			collarval = document.getElementById("Collar").value;
//		}
//		
//		var queryStr = "pid=" + pid + "&chest=" + chestval + "&waist=" + waistval + "&hip=" + hipval + "&lowwaist=" + lowwaistval + "&collar=" + collarval;
//		
//		httpObject.open("GET", "/bupa/products/performcalc.php?" + queryStr, true);
//		httpObject.send(null);
//		httpObject.onreadystatechange = function()
//		{
//			if(httpObject.readyState == 4)
//			{			
//				//alert(httpObject.responseText);
//				if(!httpObject.responseText)
//				{
//				}
//				else
//				{
//					var sizecalc = httpObject.responseText;
//					document.getElementById("recommendspan").innerHTML="<label>Recommended size:</label><p><b>" + sizecalc + "</b></p>";
//					var sizeText = "size_" + pid;
//					if(sizecalc == "Special Make")
//						sizecalc = "special";
//					for(i=0;i<document.getElementById(sizeText).length;i++)
//					{
//						if(document.getElementById(sizeText).options[i].value==sizecalc)
//						{
//							document.getElementById(sizeText).selectedIndex=i
//						}
//					}
//				}
//			}
//		}
//	}
}