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

                  $numlines = count($orders->lineitems);
                  $carttotal = 0.00;

                  if ($numlines > 0)
                  {
                     $arrKeys = array_keys($orders->lineitems);
                     for($i = 0; $i < $numlines; $i++)
                     {
                        $key = $arrKeys[$i];
                        $li = $orders->lineitems[$key];
                        $tmpCost = $li->lineCost();
                        $final_cost = formatNumber($tmpCost);
                        $name = $li->product->item_number;
                        $desc = $li->product->description;
                        $prod_id = $li->product->prod_id;
                        $qty = $li->qty;
                        $size = $li->size;
                        $cat_id = $li->cat_id;
                        $itemDisp = $name . "-" . $size . " | $desc ($qty items) ";
                        
                        $basketText = $basketText . '<li id="productID_' . $prod_id . '_'.$size.'"><a id="productID_' . $prod_id . '_'.$size.'" class="basketitems" href="'._CUR_HOST ._DIR .'products/ajaxcart.php?action=delete&prod_id=' . $prod_id . '" onClick="return false;"><img src="'._CUR_HOST ._DIR .'_img/del.png" id="productID_' . $prod_id . '_'.$size.'"></a> ' . $itemDisp . '- $<span class="productPrice">' . $final_cost . '</span><span class="productCategory">'.$cat_id.'_'.$qty.'_'.$prod_id.'</span></li>';
                        $carttotal += $tmpCost;
                     }
                     echo $basketText;
                     //$carttotal = formatNumber($carttotal);
                  }

                ?>
               </ul>
               <input id="cartval" type="hidden" class="carttotal" value="<?php echo formatNumber($carttotal);?>"/>
            </div>
         </div>
      </div>
      <div id="slidingTopFooter">
         <div id="slidingTopFooterLeft">
             <a href="no-js.htm" onclick="return false;" id="slidingTopTrigger">View Cart</a>
         </div>
         <div id="totals">
            <ul class="ulcart">
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
//                 $allowance = $_SESSION[_ALLOWANCE];
      $alreadyOrdered = $orders->getOrderedTotal();

      $payRemainArr = $orders->CalcPayable();
      $payable = $payRemainArr[0];
      $remaining = $payRemainArr[1];

      $availableAllowance = bcadd($allowance,$paidAmt,2);
      $availableAllowance = bcadd($availableAllowance,$qualifyAmt,2);
      $availableAllowance = bcsub($availableAllowance, $alreadyOrdered,2);

      if($availableAllowance < 0) //set to zero so it doesnt display a -ve number
         $availableAllowance = 0;

// echo "available allowance: $availableAllowance paid: $paidAmt remain: $remaining<BR>";
//echo "Re: $remaining [$alreadyOrdered]<BR>";
         if($remaining > 0)
            $payable = 0;
         else
            $payable = $remaining * 1;

         if($remaining < 0)
            $remaining = 0;

               ?>
               <li class="carttotal">Available Allowance: $<?php echo formatNumber($availableAllowance);?></li>
               <li class="carttotal">Cart Total: $<?php echo formatNumber($carttotal);?></li>
               <?php
                  if($remaining > 0)
                  {
               ?>
                     <li class="carttotal">Remaining: $<?php echo formatNumber($remaining);?></li>
               <?php
                  }
                  else
                  {
                     $payable *= -1;
               ?>
                      <li class="carttotal">Payable: $<?php echo formatNumber($payable);?></li>
               <?php
                  }
               ?>
            </ul>
            <input type="hidden" name="hiddenallowance" id="hiddenallowance" value="<?php echo $availableAllowance;?>"/>
            <input type="hidden" name="hiddenalreadyordered" id="hiddenalreadyordered" value="<?php echo 0;?>"/>
         </div>
         <div id="checkout1">
         <br/>
            <a id="checkout" href="<?php echo _CUR_HOST ._DIR . "products/checkout.php"?>">CHECK OUT</a>
            <div id="target-6"></div>
         </div>
      </div>
      
