   <div id="slidingTopWrap">
      <div id="slidingTopContent">
         <div id="basketWrap">
            <div id="basketTitleWrap">
               Your Cart <span id="notificationsLoader"></span>
            </div>
            <div id="basketItemsWrap">
               <ul>
               <li></li>
               <?php
                  if(!$_SESSION['order'])
                  {
                     $orders = new orders();
                     //reset admin's allowance if any
                     $orders->LoadUserIdAllowance($_SESSION[_USER_ID]);
                  }
                  else
                  {
                     $orders = unserialize($_SESSION['order']);
                  }
                  $carttotal = 0.00;                  
                  
				for($c = 0; $c < 2; $c++)                              
				{
					if($c == 0)
						$lineitems = $orders->lineitems;
					else 
						$lineitems = $orders->combinedLineitems;
											
                  $numlines = count($lineitems);

                  //display NZD or AUD
                  $currency = "\$";
                  if($orders->isAUS == "N")
                  {                  
                  	$currency = "NZ\$";
                  	echo "<input type='hidden' id='auscurrency' value='0'>";
                  }
                  else 
                  	echo "<input type='hidden' id='auscurrency' value='1'>";                  

                  if ($numlines > 0)
                  {
                     $arrKeys = array_keys($lineitems);
                     for($i = 0; $i < $numlines; $i++)
                     {
                        $key = $arrKeys[$i];
                        $li = $lineitems[$key];
                        $tmpCost = $li->lineCost();
                        $final_cost = formatNumber($tmpCost);
                        $name = $li->product->item_number;
                        $desc = $li->product->description;
                        $prod_id = $li->product->prod_id;
                        $qty = $li->qty;
                        $size = $li->size;
                        $cat_id = $li->cat_id;
                        $emb = $li->emb;
                        $itemDisp = $name . "-" . $size . " | $desc | $emb ($qty items) ";                        
                        
                        if($c == 1)
                        	$prodIDName = "cproductID_";
                        else
                        	$prodIDName = "productID_";
                        $basketText = $basketText . '<li id="'.$prodIDName . $prod_id . '_'.$size.'"><a id="'.$prodIDName . $prod_id . '_'.$size.'" class="basketitems" href="'._CUR_HOST ._DIR .'products/ajaxcart.php?action=delete&prod_id=' . $prod_id . '" onClick="return false;"><img src="'._CUR_HOST ._DIR .'_img/del.png" id="'.$prodIDName . $prod_id . '_'.$size.'"></a> ' . $itemDisp . '- $<span class="productPrice">' . $final_cost . '</span><span class="productCategory">'.$cat_id.'_'.$qty.'_'.$prod_id.'</span></li>';
                        $carttotal += $tmpCost;
                     }

                     //$carttotal = formatNumber($carttotal);
                  }
					}//for $c    
               echo $basketText;                  

                ?>
               </ul>
               <input id="cartval" type="hidden" class="carttotal" value="<?php echo formatNumber($carttotal);?>"/>
            </div>
         </div>
      </div>
      <div id="slidingTopFooter">
         <div id="totals">
            <ul class="ulcart" id="target-5">
               <?php
                  //$allowance = $_SESSION[_ALLOWANCE];
                  // use this allowance calc as we want to get allowances that are after the current date, even if they placed orders earlier if
                  // use allowance from new end date since editing may be from other the expiry of first allowance
      if(minAccessLevel(_STATE_LEVEL))
      {
         //load the current order's id??
         $userid = $orders->user_id;
      }
      else
         $userid = $_SESSION[_USER_ID];
      $user_id = $orders->getCurrentUserID();
      $orders->LoadUserIdAllowance($user_id);
      $allowance = $orders->getAllowanceFromOrderDate();
      $qualifyAmt = $orders->qualifyAmt($user_id);
      
      $paidOptionalAmt = $orders->calcPaidOptionalItems($user_id, $orders->order_id);
//                 $allowance = $_SESSION[_ALLOWANCE];
      $alreadyOrdered = $orders->getOrderedTotal();
      $payRemainArr = $orders->CalcPayable();
      
      $payable = $payRemainArr[0];
      $remaining = $payRemainArr[1];

      $availableAllowance = bcadd($allowance,$paidAmt,2);
      $availableAllowance = bcadd($allowance, $paidOptionalAmt,2);
      $availableAllowance = bcadd($availableAllowance,$qualifyAmt,2);
      $availableAllowance = bcsub($availableAllowance, $alreadyOrdered,2);
      
      //$remaining = $availableAllowance;
// echo "optional: $optionalPayable available allowance: $availableAllowance paid: $paidAmt remain: $remaining alreadyordered: $alreadyOrdered qualify: $qualifyAmt<BR>";


      if($availableAllowance < 0) //set to zero so it doesnt display a -ve number
         $availableAllowance = 0;
// $availableAllowance  =0;

// echo "Re: $remaining [$alreadyOrdered]<BR>";

      //if($paidAmt > 0)
      
         if($remaining > 0)
            $payable = 0;
         else
            $payable = $remaining * 1;

         if($remaining < 0)
            $remaining = 0;

               ?>
             <li class="carttotal" style="display:none;"><div class="cartmoney">Available Allowance: </div><div class="money"><?php echo "\$". formatNumber($availableAllowance);?></div></li> 
               <li class="carttotal"><div class="cartmoney">Cart Total: </div> <div class="money"><?php echo "\$". formatNumber($carttotal);?></div></li>
               <?php
                  if($remaining > 0)
                  {
               ?>
                     <li class="carttotal"><div class="cartmoney">Remaining: </div><div class="money"><?php  echo "\$".  formatNumber($remaining);?></div></li>
               <?php
                  }
                  else
                  {
                     //$payable *= -1.1;//gst
                     $payable *= -1;
                     //$payable += $belttotal;
                     $formattedPayable = "";
                     if($orders->isAUS == "N")
                     {
                        $formattedPayable = "NZ\$" . formatNumber($payable);
                     }
                     else
                     {
                        $formattedPayable = "\$" . formatNumber($payable);
                     }
               ?>
                      <li class="carttotal"><div class="cartmoney">Payable: </div><div class="money"><?php echo $formattedPayable;?></div></li>
               <?php
                  }
               ?>
            </ul>
            <input type="hidden" name="hiddenallowance" id="hiddenallowance" value="<?php echo $availableAllowance;?>"/>
            <input type="hidden" name="hiddenalreadyordered" id="hiddenalreadyordered" value="<?php echo 0;?>"/>
         </div>
         
         <div id="slidingTopFooterLeft">
             <a href="no-js.htm" onclick="return false;" id="slidingTopTrigger">View Cart</a>            
			    <a id="checkout" href="<?php echo _CUR_HOST ._DIR . "products/checkout.php"?>">CHECK OUT</a>            
             <div id="target-6"></div> 
         </div>
                  
         <div id="checkout1">
         </div>
      </div>
      <div id="slidingFooterInfo">
      <p>Your current garment entitlement is listed below. Payment via credit card is required if your order exceeds your uniform entitlement.</p>

      <table id="allocation-table">
      <thead>
         <tr>
            <td colspan="5" style="text-align:center;">Days Worked: <?php echo $orders->staff_worked;?> / <?php  
            
                         $roleQuery = "select * from employee_role where employeerole_id = " . $orders->staff_role;
                         $res = db_query($roleQuery);
                         $uniform_type = db_result($res, 0, "name");                                  
            
            echo $uniform_type;
            
            ?> </td>
         </tr>      
         <tr><th>Garment</th><th>Entitled</th><th>Ordered</th><th>Cart</th><th>Remain</th></tr>
      <thead>
      <tbody>
      
      <?php
      
      global $garmentTypes;
      global $categoryArr;
      $maxArr = array();
      $remainArr = array();
      $orderedArr = array();
      
      for($i = 0; $i < count($garmentTypes); $i++)
      {
         $curType = $garmentTypes[$i];
         
        $max = $orders->allocationObj->getMaxCatType($curType);
        $ordered = $orders-> getTypeOrderedDB($curType);
        $remain = $max - $ordered;
        
        if($remain < 0)
           $remain = 0;
        
        $maxArr[$curType] = $max;
        $orderedArr[$curType] = $ordered;           
        $remainArr[$curType] = $remain;
        if($i == 0)
           echo '<tr id="target-2">';
        else
           echo '<tr>';
       ?>
            <td><?php echo ucwords(strtolower($categoryArr[$curType])); ?></td>
            <td id="alloc<?php echo $curType;?>"><?php echo $max;?></td>
            <td id="ordered<?php echo $curType;?>"><?php echo $ordered;?></td>
            <td id="cart<?php echo $curType;?>">0</td>
            <td id="remain<?php echo $curType;?>" class="remainqty"><?php echo $remain;?></td>
            
            <input type="hidden" name="max<?php echo $curType;?>" id="max<?php echo $curType;?>" value="<?php echo $max;?>"/>
            <input type="hidden" name="alert<?php echo $curType;?>" id="alert<?php echo $curType;?>" value="0"/>
         </tr>  
                
       <?php         
     
      }
         
            
      ?>
      </tbody>
      </table>
      </div>
      <?php
      //}
      ?>
   </div>
