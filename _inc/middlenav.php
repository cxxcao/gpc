<?php
   if(user_isloggedin())
   {
?>
         <ul id="nav2">
            <li>
               <a href="#">Order Management</a>
               <ul>
                  <!-- <li><a href="<?php echo _CUR_HOST ._DIR . "products/neworder.php?action=new"?>">HOME</a></li> -->
                  <li><a href="<?php echo _CUR_HOST ._DIR . "products/neworder.php?action=new"?>">Start A New Order</a></li>
                  <li><a href="<?php echo _CUR_HOST ._DIR . "products/listorders.php"?>">List Orders/Create Return</a></li>
                  <?php
                  if(minAccessLevel(_ADMIN_LEVEL))
                  {
                  ?>

                  <li>
                     <a href="#">Reports</a>
                     <ul>
                        <li><a href="<?php echo _CUR_HOST ._DIR . "reports/garmentsordered.php"?>">Garments Ordered</a></li>
                        <!-- 
                        <li><a href="<?php echo _CUR_HOST ._DIR . "reports/delivery.php"?>">Order Status Report</a></li>
                        <li><a href="<?php echo _CUR_HOST ._DIR . "reports/balance.php"?>">Balance</a></li>
                         -->
                     </ul>
                  </li>

                  <?php
                  }
                  ?>
                  <li>
                     <a href="#">Support</a>
                     <ul>
                     <!--
                        <li>
                           <a href="<?php echo _CUR_HOST ._DIR . "support/returns.php?action=new"?>">Make A Claim</a>
                        </li>
                        -->
                        <li>
                           <a href="<?php echo _CUR_HOST ._DIR . "support/listreturns.php"?>">List Returns</a>
                        </li>
                     </ul>
                  </li>
               </ul>
            </li>
            <li><a href="<?php echo _CUR_HOST ._DIR . "products/listorders.php"?>">Create Return</a></li>            
            <li>
               <a href="#">Account Details</a>
               <ul>
                  <li>
                     <a href="<?php echo _CUR_HOST ._DIR . "account/mydetails.php"?>">My Details</a>
                  </li>
                  <!--
                  <li>
                     <a href="<?php echo _CUR_HOST ._DIR . "account/changepassword.php"?>">Change My Password</a>
                  </li>
                  -->
                  <?php
                  if(minAccessLevel(_ADMIN_LEVEL))
                  {
                  ?>
                  <!-- 
                  <li>
                     <a href="#">Allowance</a>
                     <ul>
                        <li><a href="<?php echo _CUR_HOST ._DIR . "account/addrequest.php?action=new"?>">Request Top Up</a></li>
                        <li><a href="<?php echo _CUR_HOST ._DIR . "account/listrequests.php"?>">List Requests</a></li>
                     </ul>
                  </li>
						 -->
                  <li>
                     <a href="#">Location Management</a>
                     <ul>
                        <li><a href="<?php echo _CUR_HOST ._DIR . "account/addlocation.php"?>">Add Location</a></li>
                        <li><a href="<?php echo _CUR_HOST ._DIR . "account/listlocation.php"?>">List Location</a></li>
                     </ul>
                  </li>
                  <?php
                  }
                  if(minAccessLevel(_BRANCH_LEVEL))
                  {
                  ?>
                  <li>
                     <a href="#">Staff Management</a>
                     <ul>
                     <?php
                        if(minAccessLevel(_BRANCH_LEVEL))//only admins can add staff, branch managers can view
                        {
                     ?>
                        <li><a href="<?php echo _CUR_HOST ._DIR . "account/addstaff.php"?>">Add Staff</a></li>
                     <?php
                        }
                     ?>
                        <li><a href="<?php echo _CUR_HOST ._DIR . "account/liststaff.php"?>">List Staff</a></li>
                     </ul>
                  </li>
                  <?php
                  }
                  ?>
               </ul>
            </li>
            <li>
               <a href="#">Help</a>
               <ul>
                  <li><a href="<?php echo _CUR_HOST ._DIR . "help/faq.php"?>">FAQ</a></li>
						<!-- 
                  <?php
                  if(minAccessLevel(_ADMIN_LEVEL))
                  {
                  ?>
                     <li><a href="<?php echo _CUR_HOST ._DIR . ""?>">Administrator User Guide</a></li>
                  <?php
                  }
                  else if(minAccessLevel(_BRANCH_LEVEL))
                  {
                  ?>
                     <li><a href="<?php echo _CUR_HOST ._DIR . ""?>">Uniform Coordinator User Guide</a></li>
                  <?php
                  }
                  else
                  {
                  ?>
                     <li><a href="<?php echo _CUR_HOST ._DIR . ""?>">User Guide</a></li>
                  <?php
                  }
                  ?>

						 -->
                  <li><a target="_blank" href="<?php echo _CUR_HOST ._DIR . "help/GPC_Range.pdf"?>">Catalogue</a></li>                  						 
                  <li><a target="_blank" href="<?php echo _CUR_HOST ._DIR . "help/Measuring-Guide.pdf"?>">Measuring Guide</a></li>                  
                 <!-- <li><a target="_blank" href="<?php echo _CUR_HOST ._DIR . "help/Care_Guide.pdf"?>">Care Guide</a></li> -->
                  <li><a href="<?php echo _CUR_HOST ._DIR . "help/returns-policy.php"?>">Returns Policy</a></li>
                  <li><a href="<?php echo _CUR_HOST ._DIR . "help/terms.php"?>">Terms Of Use</a></li>

               </ul>
            </li>
            <li><a href="javascript:;" id="apptour">ONLINE TUTORIAL</a></li>
            <?php
               $tmpUrl = $_SERVER["REQUEST_URI"];
               $urlArr = explode("/", $tmpUrl);
               $curPage = $urlArr[count($urlArr)-1];
               $curPageArr = explode("?", $curPage);
               $curPage = $curPageArr[0];

               if($curPage == "checkout.php" || $curPage == "mydetails.php" || $curPage == "faq.php" || $curPage == "returns-policy.php" || $curPage == "terms.php")
               {
                  if($_SESSION['order'])
                  {
                     $tmpOrder = unserialize($_SESSION['order']);
                     //if($tmpOrder->status != _APPROVED || $tmpOrder->user_id != $_SESSION[_USER_ID] || ($tmpOrder->ordersGreaterThanMe() >0))
                     if((($tmpOrder->status != "PENDING" && $tmpOrder->status != _APPROVED) || ($tmpOrder->ordersGreaterThanMe() >0) ) || strlen($tmpOrder->receipt) > 0)
                     {
            ?>
                        <li><a href="<?php echo _CUR_HOST ._DIR . "products/neworder.php?action=new&role=3&range=1"?>">New Order</a></li>
            <?php
                     }
                     else
                     {
            ?>
                        <li><a id="continueshopping" href="<?php echo _CUR_HOST ._DIR . "products/neworder.php"?>">Continue Shopping</a></li>
             <?php
                     }
                  }
               }
               else
               {
            ?><!-- 
            <li><a href="<?php echo _CUR_HOST ._DIR . "products/checkout.php"?>">Check Out</a></li>
             -->
            <?php
               }
            ?>
         </ul>
         <ul id="logoutul">
            <li><a href="<?php echo _CUR_HOST ._DIR . "logout.php"?>">Log Out</a></li>
         </ul>
<?php
   }
   else
   {
?>
         <ul id="nav2">
            <li>
               <a href="<?php echo _CUR_HOST ._DIR . "index.php"?>">HOME</a>
            </li>
             <li><a href="<?php echo _CUR_HOST ._DIR . "help/faq.php"?>">FAQ</a></li>
              <li><a href="<?php echo _CUR_HOST ._DIR . "help/returns-policy.php"?>">Returns Policy</a></li>
         </ul>
<?php
   }
?>