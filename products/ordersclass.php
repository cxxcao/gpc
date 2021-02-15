<?php
$home = dirname(__FILE__) . "/../";
$lib = $home . "/lib";

require_once($home . '/globals.php');
require_once($lib . '/functions.php');
require_once($lib . '/htmlGenerator.php');
require_once($lib . '/phpmailer/class.phpmailer.php');
require_once('productsclass.php');
require_once('../account/staffclass.php');
require_once('../account/locationclass.php');

class orders
{
   var $order_id;
   var $user_id;
   var $emp_id;
   var $name;
   var $address;
   var $suburb;
   var $postcode;
   var $state;
   var $contactnumber;
   var $email;
   var $sname;
   var $orderingfor_name;
   var $staff_range;
   var $ordertime;
   var $status;
   var $courier;
   var $connote;
   var $lastupdated;
   var $comments;
   var $approval_time;
   var $sample;
   var $cardname;
   var $cardnumber;
   var $expiry;
   var $cardtype;
   var $payable;
   var $paid;
   var $iswages; //deduct from wages;
   var $paymentopt;
   var $numpays;
   var $amountperpay;
   var $lineitems;
   var $action;
   var $allowanceObj;
   var $allocationObj;
   var $receipt;
   var $oLocation_id;
   var $staff_role;
   var $hire_year;
   var $staff_worked;
   var $business_unit;
   var $approval_emails;
   var $job_classification;
   var $costcentre;
   var $coordinator_for;
   var $combinedLineitems;
   var $combinedAdded;   
   var $isAUS;
   var $isGST;
   var $staffObj;
   var $locationObj;
   var $bulk;

   function __construct()
   {
      $this->order_id = "";
      $this->staff_worked = 0;
      $this->business_unit = "";
      $this->approval_emails = array();   
      $this->coordinator_for = array();
      $this->job_classification = "";
      $this->hire_year = date("Y");
      $this->staffObj = new staff();
      $this->costcentre="";
      $this->locationObj = new location();
      $this->bulk = "Y";
      //check if ordering for someone was
      $orderingfor_id = "";
      if(minAccessLevel(_BRANCH_LEVEL))
         $orderingfor_id = _checkIsSet("orderingfor_id");;

      if($orderingfor_id)
      {
         $this->user_id = $orderingfor_id;
         $this->getCurrentUserID();
      }
      else
      {
         $this->user_id = $_SESSION[_USER_ID];
         $this->getCurrentUserID();///own login
      }

      //load allocation here, assume its not to update/view an order
      $this->allocationObj = new allocation();
      $this->allocationObj->getMaxAllocations($this->user_id, "");
      
      $this->emp_id = "";
      $this->name = "";
      $this->address = "";
      $this->suburb = "";
      $this->postcode = "";
      $this->state = "";
      $this->contactnumber = "";
//      $this->email = "";
      $this->sname = "";
      $this->ordertime = date("Y-m-d H:i:s");
      $this->status = "APPROVED";
      $this->courier = "";
      $this->connote = "";
      $this->lastupdated = date("Y-m-d H:i:s");
      $this->comments = "";
      $this->approval_time = date("Y-m-d H:i:s");
      $this->sample = "";
      $this->cardname = "";
      $this->cardnumber = "";
      $this->expiry = "";
      $this->cardtype = "";
      $this->payable = "";
      $this->paid = "";
      $this->iswages = "N";
      $this->formsubmitted = "N";
      $this->agree = "N";
      $this->comments = "";
      $this->lineitems = array();
      $this->action = _SAVE;
      
//      $this->jurisdiction = $_SESSION[_JURISDICTION];
      $this->allowanceObj = new allowance();
      $this->combinedLineitems = array();
      $this->combinedAdded = false;      
      $this->receipt = "";
      $this->staff_role = "";
      
      $this->paymentopt = "";
      $this->numpays = 0;
      $this->amountperpay = 0;      

//      $this->staff_range = "";
//      $this->oLocation_id = $_SESSION[_LOCATION_ID];
   }

   function getCurrentUserID()
   {
      $userid = $this->user_id;

      if(strlen($userid) < 1)
         $userid =  $_SESSION[_USER_ID];

      //now load the staff name

      $staff = new staff();
      $staff->LoadStaffId($userid);
      $this->staff = $staff;
      $this->orderingfor_name = $staff->firstname . " " . $staff->lastname;
      $this->approval_emails = $staff->approval_emails;
      
      /*
      $staff->loadCoordinatorLocation($_SESSION[_USER_ID]); //always want the admin user id
      $this->coordinator_for = array();
          foreach($staff->coordinator_for as $key=>$value)
              array_push($this->coordinator_for, $value->location_id);
      */
	  $this->staff_role = $staff->role_id;
      $this->staff_range = $staff->crange;
      $this->isAUS = $staff->isAUS;      
      $this->job_classification = $staff->job_classification;
      $this->oLocation_id = $staff->location_id;
      $loc = new location();
      $loc->LoadLocationId($this->oLocation_id);
      $this->locationObj = $loc;
      $this->staffObj = $staff;
      $this->sname = $loc->sname;
      $this->staff_worked = $staff->daysworked;
      $this->business_unit = $loc->business_unit;
      $this->hire_year = explode("-",$staff->hire_date)[0];
      //get approval emails
      //$this->approval_emails = $staff->approval_emails;
//       $this->costcentre=$staff->cost_centre;
//       echo "ROLE: " . $this->staff_role . "<BR>";
      if($this->isAUS == "N")
         $this->isGST = "N";
      else
         $this->isGST = "Y";
      
      if($this->action != _UPDATE)
      {
         $this->email = $staff->email;
         
      }

      return $userid;
   }
   
   function isBasketEmpty()
   {
   	$query = "select * from basket where user_id = " . $_SESSION[_USER_ID];
   	$res = db_query($query);
   	$num = db_numrows($res);
   	if($num > 0)
   		return 'N';
   	else
   		return 'Y';
   }

   function isFirstOrder()
   {
   	$query = "select * from orders where user_id = " . $this->user_id;
   	$res = db_query($query);
   	$num = db_numrows($res);
   
   	if($num > 0)
   		return false;
   	else
   		return true;
   }   
   
   function UpdateLineItem($prod_id, $size)
   {

   }
   
   /* this will update all products with the same size in the same order*/
   function toggleSize($pid, $size, $oid)
   {
   	$query = "update lineitems set size = '$size' where prod_id = $pid and order_id = $oid";
   	if(db_query($query))
   		return true;
   	else 
   		return false;
   }

   function GrandTotal()
   {
   	$total = 0;
		for($c = 0; $c < 2; $c++)                              
		{
			if($c == 0)
				$lineitems = $this->lineitems;
			else 
			 $lineitems = $this->combinedLineitems;   	
   	
	      $arrKeys = array_keys($lineitems);
	      for($i = 0; $i < count($arrKeys); $i++)
	      {
	         $key = $arrKeys[$i];
	         if(array_key_exists($key, $lineitems))
	         $total += $lineitems[$key]->lineCost();
	      }
		}
      return $total;
   }
   
   function removeZeroQtyLi()
   {
      foreach($this->lineitems as $lkey=>$li)
	   {
	      if($li->qty == 0)
	      {
	       //  echo "LKEY: $lkey " . $li->qty . " <br/>";
	         unset($this->lineitems[$lkey]);
	      }
	   }
	   
      foreach($this->combinedLineitems as $lkey=>$li)
	   {
	      if($li->qty == 0)
	      {
	       //  echo "LKEY: $lkey " . $li->qty . " <br/>";
	         unset($this->combinedLineitems[$lkey]);
	      }
	   }	   
   }   

   function UpdateQty($prod_id, $size, $qty)
   {
      $key =  $prod_id . "_$size";
      $key=str_replace(array(" ","/","(",")"),"-",$key);
      $this->lineitems[$key]->qty = $qty;
     	
     	$this->removeZeroQtyLi();
     	unset($this->lineitems[$key]);
     	$this->addLineItem($qty, $prod_id, $size, 0);
     	
      return  $this->lineitems[$key]->lineCost();
   }
   
   function UpdateQtyCombined($prod_id, $size, $qty)
   {
   	//get the combined qty and compare to alloc
   	
      $key =  $prod_id . "_$size";
      $key=str_replace(array(" ","/","(",")"),"-",$key);
      
      //remove current lineitem, and just addin new qty???
      unset($this->combinedLineitems[$key]);
      $this->addLineItem($qty, $prod_id, $size, 0);
      
         //checkCombinedQty remain, and reorder lineitems if neccessary; qty removed = qty to be placed in lineitems
      $maxCombined = $this->allocationObj->getMaxCatType(_COMBINED_GARMENT_TYPE);
      $combinedOrdered = $this-> getTypeOrderedDB(_COMBINED_GARMENT_TYPE);
      $remainCombined = $maxCombined - $combinedOrdered;

      $cQty = $this->getCombinedQty();
      
    //  echo "REMAIN: $remainCombined updateqty: $qty<BR>\n";
      
      if($remainCombined > 0)
      {
	      foreach($this->lineitems as $lkey=>$li)
	      {
	      	$liQty = $li->qty;
	      	$curPID = $li->product->prod_id;
	      	$curSize = $li->size;
	      	//echo "LIQTY: $liQty<BR>";
	      	//if($curPID != 276 && $curPID != 277) //dont count the free items
	      	{
    	      	if($remainCombined > 0)
    	      	{
    		      	if($liQty >= $remainCombined)
    		      	{
    		      		$this->lineitems[$lkey]->qty -=$remainCombined;
    		      		$this->addLineItem($remainCombined, $curPID, $curSize, 0);
    		      		$remainCombined -= $liQty;
    		      		
    		      		if($this->lineitems[$lkey]->qty == 0)
    		      			unset($this->lineitems[$lkey]);
    		      		
    		      		
    		      		break;
    		      	}
    		      	else if($remainCombined > $liQty)
    		      	{
    		      		//unset the current line from lineitems, add to combined and deduct $qtyRemove
    		      		$this->removeAllItems($curPID, $curSize);
    		      		$this->addLineItem($liQty, $curPID, $curSize, 0);
    		      		$remainCombined -= $liQty;
    		      	}
    	      	}   
	      	}
	      	
	      }      
      }
      
     	$this->removeZeroQtyLi();
     	
      return 1;
    //  return  $this->combinedLineitems[$key]->lineCost();
   }      

   function CalcRemainingExCart($user_id) /* excluding the cart total*/
   {
      $allowance = $this->getAllowanceFromOrderDate();
      $alreadyPaid = $this->calcAlreadyPaid($user_id, $this->order_id);
      $alreadyOrdered = $this->getOrderedTotal();// - $alreadyPaid;

      $totalQualifyAmt = $this->qualifyAmt($user_id);
      
      $allowance = bcadd($allowance, $totalQualifyAmt, 2);
      
      $allowance = bcadd($allowance, $alreadyPaid, 2);

//      echo "ALREADY ORDERED: $alreadyOrdered qualify: $totalQualifyAmt allowance: $allowance paid: $alreadyPaid<BR>";
      $remaining = $allowance - $alreadyOrdered;

      if($remaining < 0)
         $remaining = 0;
      return $remaining;
   }

   function ordersGreaterThanMe()
   {
      /* let corporate orders pass*/
      if($_SESSION["CORPORATE"] == true)
         return 0;

      $order_id = $this->order_id;
      $uid = $this->user_id;
      $query = "select * from orders where order_id > $order_id and user_id = $uid";
//      echo "$query<BR>";
      $res = db_query($query);
      $numrows = db_numrows($res);
      return $numrows;
   }

   function CalcPayableNew()
   {
      //$allowance = $_SESSION[_ALLOWANCE];
      $allowance = $this->getAllowanceFromOrderDate();
      $alreadyOrdered = $this->getOrderedTotal();
      $remaining = $allowance - $alreadyOrdered;
      $carttotal = $this->GrandTotal();

      $payable = $this->CalcPayable();

      if($remaining < 0)
      $remaining = 0;

//      echo "ALREADY ORDERED: $alreadyOrdered payable: $payable allowance: $allowance<BR>";

      $payable = $remaining - ($carttotal);

      if($payable >= 0) // no need to pay!
         $payable = 0;

      return $payable;
   }

   function qualifyAmt($user_id)
   {
      $totalQualifyAmt = 0;
      //$garmentTypes = array(_JACKET_TYPE, _UPPER_TYPE, _LOWER_TYPE, _KNIT_TYPE, _TECHJK_TYPE, _ACC_TYPE, _BELT_TYPE);
      global $garmentTypes;

      for($i = 0; $i < count($garmentTypes); $i++)
      {
         $curTypeID = $garmentTypes[$i];
         //get the max allowed
         $al_end = $this->allowanceObj->end; //need end date to ensure allowance and allocation are still within the same start,end dates
         $query = "select * from rules where user_id = $user_id and cat_type = $curTypeID and end >= '$al_end'" ;

//           $al_start = $this->allocationObj->start[$curTypeID][$i];
//            $al_end = $this->allocationObj->end[$curTypeID][$i];


        //    if($this->ordertime < "2013-04-30")
         //      $query .= " and rules_type != 8";

         $res = db_query($query);
         $num = db_numrows($res);
         $max = 0;
         $oldmax = 0;
         $subTotalPerCat = 0;

         if($num > 0)
            $max = db_result($res, 0, "max_allowed");

//echo "MAX: $max<BR>";
         if($max > 0)
         {
            //$query = "select * from orders o, lineitems li where user_id = $user_id and cat_id = $curTypeID and o.order_id = li.order_id and order_time > '2012-03-04'";
            //$query = "select * from orders o, lineitems li where user_id = $user_id and cat_id = $curTypeID and o.order_id = li.order_id and order_time > '2013-04-29'";
            $query = "select * from orders o, lineitems li where user_id = $user_id and cat_id = $curTypeID and o.order_id = li.order_id";

            //get allowance start and end date, we only want orders between this date and less than current ID
            $al_start = $this->allowanceObj->start;
            $al_end = $this->allowanceObj->end;

            if($this->action == _UPDATE)
            {
               $curOrderId = $this->order_id;
               $query .= " and o.order_id < $curOrderId ";
            }

            //if expiry is on the same day, make sure the end time is 23:59.00
            if($al_start && $al_end) //in case there is no allowance set but there are dates!!
            {
               $query .= " and o.order_time between '$al_start' and '$al_end'";
            }

//echo "$query<BR>";
            $res = db_query($query);
            $num = db_numrows($res);

            if($num > 0)
            {
               $qtyAcc = 0;
               $remainMax = $max;
               //for($j = 0;  $j < $num && $qtyAcc < $max; $j++)
               for($j = 0;  $j < $num && $remainMax > 0; $j++)
               {
//                  $paytype = db_result($res, $j, "cardname");
//                  echo "PAYTYPE: $paytype<BR>";
//                  if($paytype == "Top Up")
//                  {
//                     $max = $num;
//                     $oldmax = $max;
//                  }
//                  else
//                     $max = $oldmax;

                  $qty = db_result($res, $j, "qty");
                  $qtyAcc += $qty;
                  if(($remainMax - $qty) >= 0)
                  {
                     $price = db_result($res, $j, "price");
                     $isGst = db_result($res, $j, "gst");
                     
                     if($isGst == "Y")
                        $price *= 1.1;
                     
                     $subTotalPerCat = bcadd($subTotalPerCat, $qty * $price,2);
                     $remainMax -= $qty;
                  }
                  else
                  {
                     //just charge what is remainMax
                     $price = db_result($res, $j, "price") ;
                     $isGst = db_result($res, $j, "gst");
                     
                     if($isGst == "Y")
                        $price *= 1.1;                     
                     
                     $subTotalPerCat = bcadd($subTotalPerCat, $remainMax * $price,2);
                     $remainMax -= $qty;
                  }
// 						echo "cat_type: $curTypeID QTY: $qty ACC: $qtyAcc max; $max price; $price subtotalpercat: $subTotalPerCat<BR>";
               }
            }
         }
         $totalQualifyAmt = bcadd($totalQualifyAmt, $subTotalPerCat, 2);
      }
     // echo "TOTAL QUALIFY: $totalQualifyAmt<BR>";
      return $totalQualifyAmt;
   }

   function qualifyAmtOnCurrentLineItems($user_id)
   {
      $totalQualifyAmt = 0;
//      $garmentTypes = array(_JACKET_TYPE, _UPPER_TYPE, _LOWER_TYPE, _KNIT_TYPE, _TECHJK_TYPE, _ACC_TYPE, _BELT_TYPE);
      
      global $garmentTypes;

      for($i = 0; $i < count($garmentTypes); $i++)
      {
         $curTypeID = $garmentTypes[$i];
         //get the max allowed
         $query = "select * from rules where user_id = $user_id and cat_type = $curTypeID" ;
         $res = db_query($query);
         $num = db_numrows($res);
         $max = 0;
         $subTotalPerCat = 0;
         if($num > 0)
            $max = db_result($res, 0, "max_allowed");

         if($max > 0)
         {
            $tmpLineitems = array();
            foreach(array_keys($this->lineitems) as $key)
            {
               $li = $this->lineitems[$key];
               if($li->cat_id == $curTypeID)
               {
                  array_push($tmpLineitems, $li);
               }
            }

            $num = count($tmpLineitems);

            if($num > 0)
            {
               for($j = 0;  $j < $num && $j < $max; $j++)
               {
                  $qty = $tmpLineitems[$j]->qty;
                  $price = $tmpLineitems[$j]->unitcost; //ex gst
                  //$qty = db_result($res, $j, "qty");
                  //$price = db_result($res, $j, "price") * 1.1;
                  $subTotalPerCat = bcadd($subTotalPerCat, $qty * $price,2);
               }
            }
         }
         $totalQualifyAmt = bcadd($totalQualifyAmt, $subTotalPerCat, 2);
      }
      return $totalQualifyAmt;
   }
   
   function CalcOptionalPayable()
   {
      //$optionalGarmentTypes = array(_HI_VIS, _OPTIONAL_LOWER, _OPTIONAL_OUTER);  
      global  $optionalGarmentTypes;
      $payable = 0;
      foreach($optionalGarmentTypes as $gtKey => $gtVal)
      {
         $numAlreadyOrdered = $this->getTypeOrderedDB($gtVal);
         $payable += $this->getLineItemPayable($gtVal, $numAlreadyOrdered, $user_id);
      }      
      
      return $payable;
   }

   function CalcPayable()
   {
      if(minAccessLevel(_STATE_LEVEL))
      {
         //load the current order's id??
         $user_id = $this->user_id;
      }
      else
         $user_id = $_SESSION[_USER_ID];

      $user_id = $this->getCurrentUserID();
      
      global $garmentTypes;
      $payable = 0;
      foreach($garmentTypes as $gtKey => $gtVal)
      {
         $numAlreadyOrdered = $this->getTypeOrderedDB($gtVal);
         $payable += $this->getLineItemPayable($gtVal, $numAlreadyOrdered, $user_id);
      }

//     echo "PAYABLE: $payable NUMJACK: $numAcc<BR>"; //6,9,10 category not included in allowance
      if($payable > 0)
      {
         $remaining = $this->CalcRemainingExCart($user_id);
      }
      else
      {
        // $remaining = $_SESSION[_ALLOWANCE];

         //$allowance = bcadd($this->getAllowanceFromOrderDate(),$allowanceAmt,2);
         //$allowance = $this->getAllowanceFromOrderDate();
         $allowance = $this->getTotalAllowance();
         $alreadyOrdered = $this->getOrderedTotal();
         
         $paidAmt = $this->calcAlreadyPaid($user_id, $this->order_id);
         $paidOptionalAmt = $this->calcPaidOptionalItems($user_id, $this->order_id);
         
         $totalQualifyAmt = $this->qualifyAmt($user_id);
         $totalQualifyAmtCurrentOrder = $this->qualifyAmtOnCurrentLineItems($user_id);

         $totalQualifyAmt = bcadd($totalQualifyAmt, $totalQualifyAmtCurrentOrder, 2);

         //$allowance = bcadd($allowance, $totalQualifyAmt, 2);

        // echo "ALREDY ORDER: $alreadyOrdered [$totalQualifyAmt]<BR>";

         $alreadyOrdered = bcsub($alreadyOrdered, $totalQualifyAmt, 2);
//echo "ALREDY ORDER: $alreadyOrdered<BR>";
         $cartTotal = $this->GrandTotal();
        // $cartTotal = 0;
         $remaining = bcadd($allowance,$paidAmt,2);
         $remaining = bcsub($remaining, $alreadyOrdered, 2);
         $remaining = bcsub($remaining, $cartTotal, 2);
         
         //over riding REMAINING??
         $remaining = $this->CalcRemainingExCart($user_id);
         //echo "re: $remaining<BR>";
         if(count($this->lineitems) == 0 && $remaining < 0)
         {
            $remaining = 0;
         }

      }
    //  echo "remain: $remaining PAYABLE: $payable<BR>";

      if($payable > 0) //no entitlements;
      {
         //$allowance = $this->getAllowanceFromOrderDate();
         
         $remaining =bcsub($remaining, $payable, 2);
       //echo "remain2: $remaining pabel: $payable<BR>";
         if($remaining < 0)
         {
	         $payable =bcsub($payable, $remaining, 2);   
	          $payable =bcsub($payable, $remaining, 2);   
         }
         else
            $payable = 0;
         
         $optionalPayable = $this->CalcOptionalPayable();   
         
         if($optionalPayable > 0 && $remaining > 0)
         {
            $remaining =bcadd($remaining, $optionalPayable, 2);
            $payable = bcadd($payable, $optionalPayable, 2);       
         }
         
         //echo "OPTIONAL PAYABLE: $optionalPayable PAYABLE: $payable REMAINING: $remaining<BR>";         
            
      }
      
       //  echo "REMAIN: $remaining QUALIFY: $totalQualifyAmt or: $alreadyOrdered al: $allowance currentorder: $totalQualifyAmtCurrentOrder pad: $paidAmt optioalPad: $paidOptionalAmt<BR>";         

      $payRemainArr[0] = $payable;
      $payRemainArr[1] = $remaining;
//      echo "PAY: $payable re: $remaining<BR>";

      return $payRemainArr;
   }

   function getDressOrdered() //dress classified as lower, need to take 1 from upper allocation
   {
   	$user_id = $this->user_id;
   	$order_id = $this->order_id;
   	
   	//need ordered time > than 2014-06-15 as we don't want to count orders before the allocation was introduced
   	$query = "select sum(qty) as total from orders o, lineitems li where o.user_id = $user_id and o.order_id = li.order_id and li.prod_id = 47";
   	
   	if($this->action == _UPDATE)
   		$query .= " and o.order_id < $order_id";
   	else //new order?
   	{
   		$this->getAllowanceFromOrderDate();
   	}
   	//echo "$query<BR>";
   	
   	//get allowance start and end date, we only want orders between this date and less than current ID
   	
   	//get alloc start/end time
   	$orDateRange = "";
   	$reDateRate = "";
   	//if(!$al_start && !$al_end)
   	$cat_id = 2;
   	if(count($this->allocationObj->start[$cat_id]) > 0)
   	{
   		$orDateRange = " and (";
   		for($i = 0; $i < count($this->allocationObj->start[$cat_id]); $i++)
   		{
	   		$al_start = $this->allocationObj->start[$cat_id][$i];
	   		$al_end = $this->allocationObj->end[$cat_id][$i];
	   	
	   		$orDateRange .= " (o.order_time between '$al_start' and '$al_end')";
	   		$reDateRate .= " (w.claim_date between '$al_start' and '$al_end')";
	   	
	   		if($i+1 != count($this->allocationObj->start[$cat_id]))
   			{
   			$orDateRange .= " or ";
   			}
   		}
   		//$orDateRange .= ")";
   		$query .= $orDateRange;
   	}
   	
   	//if expiry is on the same day, make sure the end time is 23:59.00
   	$al_start = $this->allowanceObj->start;
   	$al_end = $this->allowanceObj->end;
   	
   	if($al_start && $al_end) //in case there is no allowance set but there are dates!!
   	{
   		if($orDateRange != "")
   		{
   			$query .= " or (o.order_time between '$al_start' and '$al_end')";
   			$query .= ")";
   		}
   		else
   			$query .= " and o.order_time between '$al_start' and '$al_end'";
   	}
   	else //no allowances found use curdates?
   	{
   		$al_start = date('Y-m-d 00:00.00');
   		$al_end = date('Y-m-d 23:59.00');
   		if($orDateRange != "")
   		{
   			$query .= " or (o.order_time between '$al_start' and '$al_end')";
   			$query .= ")";
   		}
   		else
   			$query .= " and o.order_time between '$al_start' and '$al_end'";
   	}
   	
   	$res = db_query($query);
   	$num = db_numrows($res);
   	$totalordered = db_result($res, 0, 'total');
   	
   						//returned;
   	$query = "select sum(rcv) as totalrcv from warranty w, orders o, returns r, products p where o.order_id = w.order_id and o.user_id = $user_id and w.warranty_id = r.warranty_id and p.prod_id = r.prod_id and p.prod_id = 47  and $reDateRate";
   	$res = db_query($query);
   	$num = db_numrows($res);
   	$totalreturned= db_result($res, 0, 'totalrcv');
   	$totalordered = bcsub($totalordered, $totalreturned, 0);
   	
   	if(!$totalordered)
   		$totalordered = 0;
   	
   	//echo "$query | $al_start | $al_end | TOTAL ORDERD: $totalordered<BR>";
   	return $totalordered;
   }
   
   function getTypeOrderedDB($cat_id)
   {
      //find what is in the db first
      //$email = $this->emp_email;
      $user_id = $this->user_id;
      $order_id = $this->order_id;
      
      //need ordered time > than 2014-06-15 as we don't want to count orders before the allocation was introduced
      
		$query = "select sum(qty) as total from orders o, lineitems li where o.user_id = $user_id and o.order_id = li.order_id and li.cat_id = $cat_id";

      if($this->action == _UPDATE)
         $query .= " and o.order_id < $order_id";
      else //new order?
      {
         $this->getAllowanceFromOrderDate();
      }
// echo "$query<BR>";

      //get allowance start and end date, we only want orders between this date and less than current ID

      //get alloc start/end time
      $orDateRange = "";
      $reDateRate = "";
      //if(!$al_start && !$al_end)
      //echo "CATID: $cat_id [" . $this->allocationObj->start[$cat_id] . "]<BR>";
      if(count($this->allocationObj->start[$cat_id]) > 0)
      {
         $orDateRange = " and (";
         for($i = 0; $i < count($this->allocationObj->start[$cat_id]); $i++)
         {
            $al_start = $this->allocationObj->start[$cat_id][$i];
            $al_end = $this->allocationObj->end[$cat_id][$i];

            $orDateRange .= " (o.order_time between '$al_start' and '$al_end')";
            $reDateRate .= " (w.claim_date between '$al_start' and '$al_end')";

            if($i+1 != count($this->allocationObj->start[$cat_id]))
            {
               $orDateRange .= " or ";
            }
         }
         //$orDateRange .= ")";
         $query .= $orDateRange;
      }
      //if expiry is on the same day, make sure the end time is 23:59.00
      $al_start = $this->allowanceObj->start;
      $al_end = $this->allowanceObj->end;

      if($al_start && $al_end) //in case there is no allowance set but there are dates!!
      {
         if($orDateRange != "")
         {
            $query .= " or (o.order_time between '$al_start' and '$al_end')";
            $query .= ")";
         }
         else
            $query .= " and o.order_time between '$al_start' and '$al_end'";
      }
      else //no allowances found use curdates?
      {
         $al_start = date('Y-m-d 00:00.00');
         $al_end = date('Y-m-d 23:59.00');
         if($orDateRange != "")
         {
            $query .= " or (o.order_time between '$al_start' and '$al_end')";
            $query .= ")";
         }
         else
            $query .= " and o.order_time between '$al_start' and '$al_end'";
      }

      $res = db_query($query);
      $num = db_numrows($res);
      $totalordered = db_result($res, 0, 'total');
      //returned;
      $query = "select sum(rcv) as totalrcv from warranty w, orders o, returns r, products p where o.order_id = w.order_id and o.user_id = $user_id and w.warranty_id = r.warranty_id and p.prod_id = r.prod_id and p.cat_id = $cat_id  and $reDateRate and r.r_myob_code = 'na'";
      $res = db_query($query);
      $num = db_numrows($res);
      $totalreturned= db_result($res, 0, 'totalrcv');
      $totalordered = bcsub($totalordered, $totalreturned, 0);
      
      if(!$totalordered)
         $totalordered = 0;
//if($cat_id == 6)
//echo "$query<BR>";
// echo "$al_start | $al_end | TOTAL ORDERD: $totalordered total RETURNED: $totalreturned<BR>";

      if($user_id < 9990001)
	      return $totalordered;
      else 
      	return 0;
   }

   function isUserAddedAfter($user_id, $afterdate)
   {
      $query = "select * from login where user_id = $user_id and addedtime > '$afterdate'";
//      echo "$query<BR>";
      $res = db_query($query);
      $num = db_numrows($res);
      if($num > 0) // added after specified date
         return true;
      else
         return false;
   }

   function loginAddedDate($user_id)
   {
      $query = "select * from login where user_id = $user_id";
      //echo "$query<BR>";
      $res = db_query($query);
      $num = db_numrows($res);
      if($num > 0)
      {
         $addeddate = db_result($res, 0, "addedtime");
         return $addeddate;
      }
      return false;
   }

   function qualifyFullAlloc($user_id)
   {
      $query = "select * from login where user_id = $user_id";
//      echo "$query<BR>";
      $res = db_query($query);
      $num = db_numrows($res);
      if($num > 0)
      {
         $qualify = db_result($res, 0, "fullalloc");
         if($qualify == "yes")
            return true;
         else
            return false;
      }
      return false;
   }

   /** NEW WAY TO DETERMINE IF ITEM IS PAYABLE **/
   function getLineItemPayable($cat_id, $num, $user_id)
   {
      $i = 0;
      $payable = 0;
      $charge = false;
      $qtyToCharge = 0;
      $max = $this->allocationObj->getMaxCatType($cat_id);
//       echo "before cat :$cat_id max :  [$max] num: $num<BR>";
      $max -= $num;

      //echo "after cat :$cat_id max :  [$max] num: $num<BR>";
      if($max < 0)
      {
         $max = 0;
      }

      $num = 0; //reset num, num that was parsed in is num already ordered! NOT num in CART!

      $invoiceNo = _checkIsSet("invoice");
      if($invoiceNo)
         $charge = true;

      foreach(array_keys($this->lineitems) as $key)
      {
//         echo "CATTYPE: $cat_id NUM: $num MAX: $max key: $key<BR>";

         $li = $this->lineitems[$key];
         $prod_id = explode("_", $key)[0];
         $curType = $li->cat_id;
         
//          if($prod_id == 47 && ($cat_id == 2))
//          	$num +=1;

//          if($prod_id == 26 && $cat_id == 1 && $this->business_unit == "RIL" && $this->staff_role == 1)
//          	$num+=1;
         
         if($curType == $cat_id)
         {
	         $num += $li->qty;

            //echo "NUM: $num MAX: $max QTY: ".$li->qty." charge:$qtyToCharge<BR>";

            if($num > $max)
            {
                $qtyToCharge = ($num) - $max;
                //echo "QTY2CHARGE: $qtyToCharge<BR>";
                $charge = true;
                //reset num cos we dont want to the to be in this condition anymore
               $num = 0;
            }

            if($charge == true)
            {
               $curPayable = $qtyToCharge * $li->unitcost;
               $payable += $curPayable;
               $li->payable = $curPayable;
//               echo "payable: $payable<BR>";
            }
         }
      }
      return $payable;
   }

   function getMaxAllowed($user_id, $cat_type, $rules_type)
   {
      $query = "select * from rules where user_id = $user_id and cat_type = '$cat_type' and rule_type = $rules_type";
      $res = db_query($query);
      $num = db_numrows($res);
      $max = 0;
      if($num > 0)
      {
         $max = db_result($res, 0, "max_allowed");
      }
      return $max;
   }

   function getGender($user_id)
   {
      $query = "select gender from login where user_id = $user_id";
      $res = db_query($query);
      $num = db_numrows($res);
      if($num > 0)
      {
         $gender = db_result($res, 0, _GENDER);
         return $gender;
      }
      else
         return 0;
   }

   function getTotalAllowance()
   {
      $userid = $this->getCurrentUserID();
      $query = "select sum(allowance) as total from allowance where user_id = $userid";
//      echo "$query<BR>";
      $res = db_query($query);
      $num = db_numrows($res);
      if($num > 0)
         return db_result($res, 0, "total");
      else
         return 0;
   }

   function getAllowanceFromOrderDate()
   {
      //need to determine if viewing an order or updating it, if updating need to use allowance expiry that is > then curdate
      //else use from when order was placed

      $ordertime = $this->ordertime;
      $orderTimeArr = explode(" ", $ordertime);
      $ordertime2 = $orderTimeArr[0];
//echo "ot2: $ordertime2<BR>";
      if(minAccessLevel(_STATE_LEVEL))
      {
         //load the current order's id??
         $userid = $this->user_id;
         if(strlen($userid) < 1)
            $userid =  $_SESSION[_USER_ID];
      }
      else
         $userid = $_SESSION[_USER_ID];

      $userid = $this->getCurrentUserID();

      $this->allowanceObj->loadAllowance($ordertime2, $userid);
      $amt = $this->allowanceObj->amount;
      //add the garment based allowance, as this depends on what they've ordered, we have to dynamically obtain this based on their order history
      //$totalQualifyAmt = $this->qualifyAmt($userid);

      //$amt = bcadd($amt, $totalQualifyAmt, 2);
      if(!$amt)
      {
         $amt = 0;
      }

      return $amt;
   }

   function getOrderedTotal()
   {
      if(minAccessLevel(_STATE_LEVEL))
      {
         //load the current order's id??
         $userid = $this->user_id;
         if(strlen($userid) < 1)
            $userid = $_SESSION[_USER_ID];
      }
      else
         $userid = $_SESSION[_USER_ID];

      $userid = $this->getCurrentUserID();
//      $query = "select sum(qty*price) as total from orders o, lineitems li where user_id = $userid and o.order_id = li.order_id and order_time > '2012-03-04'";
//      $query = "select sum(qty*price) as total from orders o, lineitems li where user_id = $userid and o.order_id = li.order_id and order_time > '2013-04-29'";
      $query = "select sum(qty*price) as total from orders o, lineitems li where user_id = $userid and o.order_id = li.order_id";
      //get allowance start and end date, we only want orders between this date and less than current ID
      $al_start = $this->allowanceObj->start;
      $al_end = $this->allowanceObj->end;

      if($this->action == _UPDATE)
      {
         $curOrderId = $this->order_id;
         $query .= " and o.order_id < $curOrderId ";
      }

      //if expiry is on the same day, make sure the end time is 23:59.00
      if($al_start && $al_end) //in case there is no allowance set but there are dates!!
      {
         $query .= " and o.order_time between '$al_start' and '$al_end'";
      }

//echo "$query<BR>";

      $res = db_query($query);
      $num = db_numrows($res);

      if($num > 0)
      {
         //get already paid amount
         $alreadyPaid = $this->calcAlreadyPaid($userid, $curOrderId);
         $alreadyOrdered = db_result($res, 0, "total");
         if($this->isAUS == "Y")
            $alreadyOrdered *= 1.1; // inc the gst value as price in lineitems are ex gst
//          $alreadyOrdered -= $alreadyPaid;
         $alreadyOrdered += $this->calcReturnsVal(); /* includes gst already 1.1*/
      }

      //$remaining = $allowance - ($carttotal + $alreadyOrdered);
//      echo "AL: $alreadyOrdered<BR>";
      return $alreadyOrdered;
   }

   function calcPaidOptionalItems($userid, $curOrderId)
   {
      $query = "select SUM( price * qty) as total from orders o, lineitems li where user_id = $userid and o.order_id = li.order_id and combine_id is not null ";

      if($curOrderId)
         $query .= " and o.order_id < $curOrderId"; // don't want paid on this order

      //get allowance start & end
      $al_start = $this->allowanceObj->start;
      $al_end = $this->allowanceObj->end;

      if($al_start && $al_end) //in case there is no allowance set but there are dates!!
      {
         $query .= " and order_time between '$al_start' and '$al_end'";
      }

//      echo "$query<BR>";
      $res = db_query($query);
      $num = db_numrows($res);
      $alreadyPaid = 0;

      if($num > 0)
         $alreadyPaid = db_result($res, 0, "total");

      return $alreadyPaid;      
   }
   
   function calcAlreadyPaid($userid, $curOrderId)
   {
//      $query = "select sum(payable) as total from orders where user_id = $userid  and order_time > '2012-03-04'";
//      $query = "select sum(payable) as total from orders where user_id = $userid  and order_time > '2013-04-29'";
      $query = "select sum(payable) as total from orders where user_id = $userid";

      if($curOrderId)
         $query .= " and order_id < $curOrderId"; // don't want paid on this order

      //get allowance start & end
      $al_start = $this->allowanceObj->start;
      $al_end = $this->allowanceObj->end;

      if($al_start && $al_end) //in case there is no allowance set but there are dates!!
      {
         $query .= " and order_time between '$al_start' and '$al_end'";
      }

//      echo "$query<BR>";
      $res = db_query($query);
      $num = db_numrows($res);
      $alreadyPaid = 0;

      if($num > 0)
         $alreadyPaid = db_result($res, 0, "total");

      return $alreadyPaid;
   }


   function calcReturnsValId($userid)
   {
      //looking at old order need to know if there are any credits!
      if($this->order_id) //update
      {
         $query = "select * from orders o, warranty w, returns r where o.order_id = w.order_id and r.warranty_id = w.warranty_id and w.return_type != 'INCORRECTLY SUPPLIED' and o.user_id = $userid and date_add(o.lastupdated, INTERVAL 450 DAY) > claim_date";// and claim_date < '$order_time'";
         $query = "select * from orders o, warranty w, returns r where o.order_id = w.order_id and r.warranty_id = w.warranty_id and o.user_id = $userid and date_add(o.lastupdated, INTERVAL 450 DAY) > claim_date";// and claim_date < '$order_time'";         
      }
      else
      {
         $allowance = new allowance();
         $allowance->getLatestAllowance($userid);
         $startdateArr = explode(" ", $allowance->start);
         $enddateArr = explode(" ", $allowance->end);
         $startdate = $startdateArr[0];
         $enddate = $enddateArr[0];

         //check if allowance has expired
         if($this->ordertime > $enddate)
         {
            $startdate = $this->ordertime ;
            $enddate = $this->ordertime ;
         }
         $query = "select * from orders o, warranty w, returns r where o.order_id = w.order_id and r.warranty_id = w.warranty_id and w.return_type != 'INCORRECTLY SUPPLIED' and o.user_id = $userid and date_add(o.lastupdated, INTERVAL 450 DAY) > claim_date and claim_date between '$startdate' and '$enddate'";
		   $query = "select * from orders o, warranty w, returns r, lineitems li where o.order_id = li.order_id and o.order_id = w.order_id and r.warranty_id = w.warranty_id and o.user_id = $userid and date_add(o.lastupdated, INTERVAL 450 DAY) > claim_date and claim_date between '$startdate' and '$enddate' and li.prod_id = r.prod_id order by li.line_id";         
      }
      
      

      $res = db_query($query);
      $num = db_numrows($res);

      $creditVal = 0;
      if($num > 0)
      {
         for($i = 0; $i < $num; $i++)
         {
            //get the returning product val & rcv qty
            //only credit after we've received the goods here at DTY
            $return_prod_id = db_result($res, $i, "prod_id");
            $request_prod_id = db_result($res, $i, "r_prod_id");
            $rcv = db_result($res, $i, "rcv");

            if($this->isAUS == "Y")
            {
               $return_val = $this->getProductPrice($return_prod_id,$this->isAUS)/1.1 * $rcv; //we want the ex gst val since we'll calc gst later and product class gives prices with GST
               $request_val = $this->getProductPrice($request_prod_id,$this->isAUS)/1.1 * $rcv;
            }
            else
            {
               $return_val = $this->getProductPrice($return_prod_id,$this->isAUS)/1 * $rcv; //we want the ex gst val since we'll calc gst later and product class gives prices with GST
               $request_val = $this->getProductPrice($request_prod_id,$this->isAUS)/1 * $rcv;
            }
//echo "return value: $return_val<BR>";
//echo "request value: $request_val<BR>";
            //requested - returned to calc the remaining allowance, since we're working on already ordered value;
            $creditVal += $request_val - $return_val;
         }
      }
// echo "CREDIT: $creditVal<BR>";
      return $creditVal;
   }

   function calcReturnsVal()
   {
      if(minAccessLevel(_STATE_LEVEL))
      {
         //load the current order's id??
         $userid = $this->user_id;
      }
      else
         $userid = $_SESSION[_USER_ID];

      return $this->calcReturnsValId($userid);
   }

   function getProductPrice($prod_id, $isAUS)
   {

      $tmpProd = new product();
      $tmpProd->LoadProductId($prod_id, $isAUS);
      return $tmpProd->price;
   }

   function SetValues($fields)
   {
      if($this->action != _UPDATE)
      {
         $this->user_id = $this->getCurrentUserID();
         //$this->status = _APPROVED;
      }

      $this->name = $fields{"fullname"};
      $this->address = $fields{"address"};
      $this->suburb = $fields{"suburb"};
      $this->postcode = $fields{"postcode"};
      $this->state = $fields{"state"};
      $this->contactnumber = $fields{"phone"};
      $this->email = $fields{"email"};
      $this->sname = $fields{"q"};
      $this->costcentre = $fields{"costcentre"};
      $this->ordertime = date("Y-m-d H:i:s");

      $this->lastupdated = date("Y-m-d H:i:s");
      $this->approval_time = date("Y-m-d H:i:s");
      $this->cardname = $fields{"cardname"};
      $this->cardnumber = $fields{"cardnumber"};
      $this->expiry = $fields{"expiry"};
      $this->cardtype = $fields{"cardtype"};
      $this->payable = unformatNumber($fields{"amount"});
      $this->bulk = $fields{"bulk"};
      $this->paid = "";
      
      $this->paymentopt = $fields{"paymentopt"};
      $this->numpays = $fields{"numPays"};
      $this->amountperpay = $fields{"amountperpay"};      

      if(strlen($this->cardnumber) > 0)
      {
         $cc = 'xxxxxxxxxxxx'.substr($fields{"cardnumber"},-4);
         $this->receipt = $fields{"ccreceipt"};
      }
      else
      {
         $cc = "";
      }

      $this->cardnumber = $cc;

      if($fields{"iswage"} == "true" || $fields{"iswage"} == 1)
         $this->iswages = "Y";
      else
         $this->iswages = "N";

      if($fields{"agree"} == "true" || $fields{"agree"} == 1  || $fields{"agree"} == "on")
         $this->agree = "Y";
      else
         $this->agree = "N";
   }

   function saveOrder()
   {
      db_query(_BEGIN);

      $iswage = _checkIsSet("iswage");
      $ispayable = _checkIsSet("payable");
      $amount = _checkIsSet("amount");
      $paymentopt = _checkIsSet("paymentopt");
      //check and sync addy info
      if($amount > 0)
      {
        if($iswage == "false" || !$iswage)
         {
            $paymentopt = "C";//always credit card if not wages;
            $req_addressfields = array("costcentre","fullname", "address", "suburb", "state", "postcode", "email", "phone", "cardtype", "cardname", "cardnumber", "expiry", "amount", "q", "iswage", "ccreceipt", "paymentopt", "bulk");
         }
        else //wages
           $req_addressfields = array("costcentre","fullname", "address", "suburb", "state", "postcode", "country", "phone", "email", "amount", "q", "paymentopt", "agree", "numPays", "amountperpay", "bulk");
      }
      else
      {
         $req_addressfields = array("costcentre","fullname", "address", "suburb", "state", "postcode", "email", "phone", "q", "iswage", "bulk");
      }
      $return_addressfields = GenericCheckAndSync($req_addressfields, false);

      $this->SetValues($return_addressfields);

      $return_size = count($return_addressfields);
      $required_size = count($req_addressfields);

//      if(user_isloggedin())
//         $user_id = $_SESSION[_USER_ID];

         $user_id = $this->user_id;

         $name = $this->name;
         $address = $this->address;
         $suburb = $this->suburb;
         $postcode = $this->postcode;
         $state = $this->state;
         $contact = $this->contactnumber;
         $email = $this->email;
         $sname = $this->sname;
         $ordertime = $this->ordertime;
         $status = $this->status;
         $lastupdated = $this->lastupdated;
         $approvaltime = $this->approval_time;
         $cardname = $this->cardname;
         $cardnumber = $this->cardnumber;
         $expiry = $this->expiry;
         $cardtype = $this->cardtype;
         $payable = $this->payable;
         $iswages = $this->iswages;
         $comments = _checkIsSet("comments");
         $comments = str_replace('"',"'",$comments);
         $this->comments = $comments;
         $formsubmitted = "N";
         $agree = $this->agree;
         $receipt = $this->receipt;
         $costcentre = $this->costcentre;
         $paymentopt = $this->paymentopt;
         $bulk = $this->bulk;

         $orderedByStaff = new Staff();
         $orderedByStaff->LoadStaffId($_SESSION[_USER_ID]);
         $orderedBy = $orderedByStaff->firstname . " " . $orderedByStaff->lastname;
         $jurisdiction = $this->jurisdiction;
     
//          echo "ISAUD [$this->isAUS]<BR>";
     
         if($this->isAUS == "Y")
         {
         	$jurisdiction = "AU";
         	$country = "AU";
         }
         else
         {
         	$jurisdiction = "NZ";
         	$country = "NZ";
         }
         
      //save the order, get the id then save lineitems
      $query = "";
      $order_id = "";
      //echo "STATUS: $status<BR>";
      if(!$payable)
      	$payable = 0;


      $numpays = $this->numpays;
      $amountperpay = $this->amountperpay;
         
      if(!$numpays)
         $numpays = 0;
         
      if(!$amountperpay)
         $amountperpay = 0;
            
      if($this->order_id) //update
      {
         if($status != _APPROVED && $this->status != _PENDING)
         {
             $_SESSION['msg'] = "Only orders in the APPROVED/PENDING state can be changed.";
             db_query(_ROLLBACK);
             return false;
         }
         
         if($status == _PENDING && minAccessLevel(_BRANCH_LEVEL))
         	$status = "APPROVED";


         $order_id = $this->order_id;
         $updateQuery = "UPDATE orders SET `ordered_by` = \"$orderedBy\", `country` = '$country', `jurisdiction` = '$jurisdiction',  `cost_centre` = \"$costcentre\",`receipt` = \"$receipt\", user_id = $user_id, email= \"$email\", name = \"$name\", address = \"$address\", suburb = '$suburb', state = '$state', postcode = '$postcode', contact = '$contact',  status = '$status',lastupdated = '$lastupdated', sname = '$sname',approvaltime = '$approvaltime', cardname = \"$cardname\", cardnumber = '$cardnumber', cardtype = '$cardtype', expiry = '$expiry', payable = $payable, iswages = '$iswages', comments = \"$comments\", agree = \"$agree\" WHERE (order_id = $order_id)";
         $res = db_query($updateQuery);
//          echo "$updateQuery<BR>";
         if(!$res)
         {
            //error out - no order ID, order update failed
            $_SESSION['msg'] = "Could not find order";
            db_query(_ROLLBACK);
            return false;
         }
         //delete lineitems in the db!
         $delQuery = "delete from lineitems where order_id = $order_id";
         $delRes = db_query($delQuery);

         if(!$delRes)
         {
            //error out - delete lineitems failed
            $_SESSION['msg'] = "Failed to save new items, please try again";
            db_query(_ROLLBACK);
            return false;
         }

      }
      else
      {
         $query = "INSERT INTO orders (`user_id`, `name`, `address`, `suburb`, `state`, `postcode`, `contact`, `status`, `email`, `sname`, `cardname`, `cardnumber`, `expiry`, `cardtype`, `payable`, `iswages`, `order_time`, `approvaltime`, `lastupdated`, `comments`, `formsubmitted`, `agree`, `receipt`, `cost_centre`, `paymentopt`, `numpays`, `amountperpay`, `ordered_by`, `jurisdiction`, `country`) VALUES  ( $user_id, \"$name\", \"$address\", \"$suburb\", '$state', '$postcode', '$contact', '$status',\"$email\", '$sname', \"$cardname\", '$cardnumber', '$expiry', '$cardtype', '$payable', '$iswages','$ordertime', '$approvaltime', '$lastupdated', \"$comments\", \"$formsubmitted\", \"$agree\", \"$receipt\", \"$costcentre\", \"$paymentopt\", '$numpays', '$amountperpay', \"$orderedBy\", \"$jurisdiction\", \"$country\")";
         $res = db_query($query);
         
//          global $environment;
//          if($environment == "DEV" || $environment == "DEV")
            $order_id = mysqli_insert_id(db_connect()); // FOR PRODUCTION SERVER and LOCAL SERVERs
//          else
//             $order_id = mysql_insert_id();
      }
// echo "[$order_id] $query<BR>";
      if($order_id)
      {
         $numLines = count($this->lineitems);
         $arrKeys = array_keys($this->lineitems);
         for($i = 0; $i < $numLines; $i++)
         {
            $key = $arrKeys[$i];
            $li = $this->lineitems[$key];
            if(!$li->saveLineItem($order_id, $this->isAUS))
            {
               //error out - can't save lineitems
               db_query(_ROLLBACK);
               return false;
            }
         }
         
         //save combined;
         $numLines = count($this->combinedLineitems);
         $arrKeys = array_keys($this->combinedLineitems);
         for($i = 0; $i < $numLines; $i++)
         {
            $key = $arrKeys[$i];
            $li = $this->combinedLineitems[$key];
            if(!$li->saveLineItem($order_id, $this->isAUS))
            {
               //error out - can't save lineitems
               db_query(_ROLLBACK);
               return false;
            }
         } 
         
      }
      else
      {
         //error out - no order ID, order save failed
         $_SESSION['msg'] = "Failed to submit order, please try again";
         db_query(_ROLLBACK);
         return false;
      }

    if(_ENV != "DEV")
      {
         if(!$this->emailOrder($order_id, false))
         {
            $_SESSION['msg'] = "Failed to send confirmation.";
            db_query(_ROLLBACK);
            return false;
         }
         /*
         else  //send approval
         {
         	$approvalReqArr = array("SA"=>"SA", "RIL"=>"RIL", "QLD"=>"QLD");
         	if(array_key_exists($this->business_unit, $approvalReqArr) && !minAccessLevel(_BRANCH_LEVEL))
         	{
         		if(!$this->emailOrder($order_id, true))
         		{
         			$_SESSION['msg'] = "Failed to send approval to the Uniform Coordinator.";
         			db_query(_ROLLBACK);
         			return false;
         		}         		
         	}
         }
         */
      }
      
      //remove all items in the basket if save is successful!
      if($this->user_id == $_SESSION[_USER_ID])
	      $this->DeleteBasket($_SESSION[_USER_ID]);
      
      $_SESSION['msg'] = "Order Submitted";
      db_query(_COMMIT);
      return true;
   }

function emailOrder($orderId, $approvalReq)
   {
      $htmlBody = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
               <style type="text/css">
               <!--
                body{
               font-family: "Muli", Arial, Tahoma, sans-serif;
               font-weight: 400;
               font-size:12px;
               width:600px;
               }
               h4
               {
                   font-size: 14px;
                   line-height: 28px
               }
               .action {
                   padding: 0;
                   padding-left: 0px;
                   border: none;
                   display: inline-flex;
                   height: 41px;
                   width: 155px;
                   align-items: center;
                   background: #6495ED;
                   color: #fff;
                   cursor: pointer;
                   position: relative;
                   padding-left: 10px;
                   padding-right: 10px;
                   font-size: 12px;
                   border-radius:8px;
                   text-decoration:none;
               }
.header-wrap {
  width: 100%;
  padding: 10px 0;
  background: #fff;
  color: #888;
}
.header-wrap a {
  text-decoration: none;
}               
                  table.catable
                  {
                     width:600px;
                     border-collapse: collapse;
                     border: 1px solid #A9A9A9;
                     font: arial;
                     color: #363636;
                  }
                  table.catabledetails
                  {
                     width:100%;
                     border-collapse: collapse;
                     border: 1px solid #A9A9A9;
                     font: arial;
                     color: #363636;
                  }
                  td.catdlg
                  {
                    border: 1px solid #A9A9A9;
                    padding: .4em;
                    font: arial;
                    font-weight:bold;
                    font-size: 14;
                    color: #363636;

                  }
                  td.catdnb
                  {
                    #border: 1px solid #A9A9A9;
                    padding: .4em;
                    font: arial;
                    #font-weight:bold;
                    font-size: 12;
                    color: #363636;
                    background:#C0C0C0;
                  }
                  td.catd
                  {
                    border: 1px solid #A9A9A9;
                    padding: .4em;
                    color: #363636;
                  }
                  td.catdbg
                  {
                    border: 1px solid #A9A9A9;
                    padding: .4em;
                    font: arial;
                    font-weight:bold;
                    font-size: 12;
                    color: #363636;
                    background: #DCDCDC

                  }
                  tr.catrbg
                  {

                    background: #e0e6e6
                  }
                  
                  hr
                  {
                     height:1px; border:none; color:#A9A9A9; background-color:#A9A9A9;
                  }
               -->
               </style>
            </head>
            <body>
            
            
      <div class="content" style="display: block; margin: 0 auto; max-width: 600px;">
        <table class="header-wrap"  style="width: 100%;">
            <tr>
               <td align="left" width="170">
                  <a href="https://www.designstoyou.com.au/"><img src="https://www.designstoyou.com.au/dty-logo-email.jpg" alt="Designs To You" style="height: auto; max-width: 20%;"></a>
               </td>
          </tr>
       </table>
    </div>            
<hr class="thin"/>';

         $user_id = $this->user_id;
         $name = $this->name;
         $address = $this->address;
         $suburb = $this->suburb;
         $postcode = $this->postcode;
         $state = $this->state;
         $contact = $this->contactnumber;
         $email = $this->email;
         $sname = $this->sname;
         $ordertime = $this->ordertime;
         $status = $this->status;
         $lastupdated = $this->lastupdated;
         $approvaltime = $this->approval_time;
         $cardname = $this->cardname;
         $cardnumber = $this->cardnumber;
         $expiry = $this->expiry;
         $cardtype = $this->cardtype;
         $payable = $this->payable;
         $iswages = $this->iswages;
         $jurisdiction = $this->jurisdiction;
         $comments = $this->comments;

         $centre_name = $sname;
         $centre_address = $address . "<br>" . $suburb . " " . $state . " " . $postcode;
         $attn = $name;

         $htmlBody .= "
         <h4>Thank you for your purchase, $name!</h4>     
         <p>
         Your order has been received and will be processed soon. Your order details are below.
         </p>
<hr/>
      <table width='600' cellpadding='0' cellspacing='0' border='0' class='yiv0284486197w320' style='position:relative;'>
         <tbody>
         <tr>
            <td align='left'>
               <table align='left' width='300' cellpadding='0' cellspacing='0' border='0'>
                  <tbody>
                     <tr>
                        <td style='padding:0px;'>
                           <table width='100%' cellpadding='0' cellspacing='0' border='0'>
                              <tbody>
                              <tr>
                                 <td valign='top' style='line-height:16px;color:#2c2c2c;padding:5px 0 5px 0;' width='160'>
                                    Order Number:
                                 </td>
                                 <td valign='top' style='font-weight:bold;line-height:16px;color:#2c2c2c;padding:5px 0 5px 0;' width='160'>
                                    $orderId
                                 </td>
                              </tr>
                              <tr>
                                 <td valign='top' style='line-height:16px;color:#2c2c2c;padding:5px 0 5px 0;' width='160'>
                                    Status:
                                 </td>
                                 <td valign='top' style='font-weight:bold;line-height:16px;color:#2c2c2c;padding:5px 0 5px 0;' width='160'>
                                    $status
                                 </td>
                              </tr>  
                           </tbody></table>
                        </td>
                     </tr>
                  </tbody>
               </table>
               
               <table align='left' width='300' cellpadding='0' cellspacing='0' border='0'>
                  <tbody>
                     <tr>
                        <td valign='top' style='line-height:18px;color:#2c2c2c;padding:5px 0 5px 0;'>
                           <b>Shipping Address:</b><br>
                           $centre_name<br/>
                           $attn<br/>
                           $centre_address<br/>
                           $contact
                        </td>
                     </tr>
                  </tbody>
               </table>
            </td>
         </tr>
      </tbody>
      </table>";

         
      $htmlBody .= "<table class=\"catable\">
      <tr><td class=\"catdbg\">QTY</td><td class=\"catdbg\">ITEM</td><td class=\"catdbg\">DESCRIPTION</td><td class=\"catdbg\" align='right'>UNITCOST</td><td class=\"catdbg\" align='right'>SUBTOTAL</td></tr>";

      $lineitems = $this->lineitems;
      $numlines = count($lineitems);
      $total = 0;
      $arrKeys = array_keys($this->lineitems);
      for($i = 0; $i < $numlines; $i++)
      {
         $key = $arrKeys[$i];
         $li = $lineitems[$key];
         $qty =  $li->qty;

         $name = $li->product->item_number . "-" . $li->size;
         $desc = $li->product->description;
         $subtotal = $li->lineCost();
         $total += $subtotal;
         $unitcost = formatNumber($li->unitcost);
         $subtotal = formatNumber($subtotal);


         $htmlBody .= "<tr><td class=\"catd\">$qty</td><td class=\"catd\">$name</td><td class=\"catd\">$desc</td><td class=\"catd\" align='right'>\$$unitcost</td><td class=\"catd\" align='right'>\$$subtotal</td></tr>";
      }

      $gst = $total - ($total / 1.1) ; //from lineitems so we divide to get gst
      $grandtotal = $total;
      $total = formatNumber($total);
      
      if($this->isAUS == "N")
      {
         $gst = 0;
      }
      
      $gst = formatNumber($gst);
      $grandtotal = formatNumber($grandtotal);

//       if($payable > 0)
//       	$paymentOption = "Credit Card - ".$this->cardnumber."";
      
      $paymentOption = "";
      if($this->paymentopt == "W")
         $paymentOption = "Wage Deduction, \$" . formatNumber($this->amountperpay) . " x " .$this->numpays ." pays.";
      else if($this->paymentopt == "C")
         $paymentOption = "Credit Card - ".$this->cardnumber."";
      
      $htmlBody .= "<tr><td colspan=\"4\" class=\"catdbg\" align='right'>SUBTOTAL</td><td class=\"catdbg\" align='right'>\$$total</td></tr>";
      $htmlBody .= "<tr><td colspan=\"4\" class=\"catdbg\" align='right'>GST</td><td class=\"catdbg\" align='right'>\$$gst</td></tr>";
      $htmlBody .= "<tr><td colspan=\"4\" class=\"catdbg\" align='right'>ORDER TOTAL</td><td class=\"catdbg\" align='right'>\$$grandtotal</td></tr>";
      
      
      if($payable > 0)
      {
            $htmlBody .= "<tr><td colspan=\"4\" class=\"catdbg\" align='right'>STAFF PAYMENT TOTAL</td><td class=\"catdbg\" align='right'>\$".formatNumber($payable)."</td></tr>";
	         $htmlBody .= "<tr><td colspan=\"4\" class=\"catdbg\" align='right'>PAYMENT OPTION</td><td class=\"catdbg\" align='right'>".$paymentOption."</td></tr>";
            $htmlBody .= "<tr><td colspan=\"4\" class=\"catdbg\" align='right'>RECEIPT</td><td class=\"catdbg\" align='right'>".$this->receipt."</td></tr>";
      }
      
      
      $htmlBody.="</table>
         <br>
            <br>
            Regards,<br/>
            DTY Customer Service<br/>
            ---<br/>
            <p>
            D E S I G N S   T O  Y O U<br/>
            31 Enterprise Drive<br/>
            Rowville VIC 3178, Australia<br/>
            T.  +61 3 9753 2555<br/>
            E.  sales@designstoyou.com.au<br/>
            W.  www.designstoyou.com.au<br/>
            </p>
      </body></html>";

      $mail = new PHPMailer();

      $mail->IsSMTP(); // telling the class to use SMTP
      $mail->SMTPKeepAlive = true;

      //$mail->Host = "mail.bigpond.com"; // SMTP server
      $mail->Host = _MAILHOST; // SMTP server
      $mail->FromName = "Designs To You";
      $mail->From = "sales@designstoyou.com.au";

      $mail->AddBCC("c.cao@designstoyou.com.au");
      $realm = _REALM;
      if($this->action == _UPDATE)
      	$mail->Subject = "DTYLink/$realm Online Order: $orderId - Updated";
      else
      	$mail->Subject = "DTYLink/$realm Online Order: $orderId";      
      
      if($approvalReq)
      {
      	/*
      	$this->approval_emails;
      	for($i = 0; $i < count($this->approval_emails); $i++)
      	{
	      	$mail->AddAddress($this->approval_emails[$i]);
      	}
      	
      	if($this->action == _UPDATE)
      		$mail->Subject = "DTYLink/$realm Online Order: $orderId - Updated (Approval Required)";
      	else
      		$mail->Subject = "DTYLink/$realm Online Order: $orderId (Approval Required)";      
      		*/	
      }
      else 
      {
      	if(_ENV != "DEV")
      		$mail->AddAddress($email);      	
      }

      $mail->Body = $htmlBody;
      $mail->isHTML(true);
      if(!$mail->Send())
      {
//       echo "1ERROR: " . $mail->ErrorInfo . "<BR>";
         $mail->SmtpClose();
         return false;
      }
      else
      {
         $mail->SmtpClose();
         return true;
      }
   }

   function ListOrders()
   {
      if(!minAccessLevel(_STATE_LEVEL))
         exit('You are not authorized to view this page.');

      $query = "select * from orders";
      $res = db_query($query);
      $num = db_numrows($res);

      echo "
            <table cellspacing='0' cellpadding='0' id='orders'>
               <tr>
                  <td class='img'>Order#</td>
                  <td class='img'>Date</td>
                  <td class='img'>Name</td>
                  <td class='img'>Address</td>
                  <td class='img'>Phone</td>
                  <td class='img'>Email</td>
                  <td class='img'>Status</td>
                  <td class='img'>Amount</td>
                  <td class='img'><img src='img/edit-page-grey.gif' width='14' height='14' /></td>
                  <td class='img'><img src='img/edit-page-grey.gif' width='14' height='14' /></td>
                  <td class='img'><img src='img/delete-page-red' width='14' height='14' /></td>
               </tr>";

      if($num > 0)
      {
         for($i = 0; $i < $num; $i++)
         {
            $order_id = db_result($res, $i, "order_id");
            $date = db_result($res, $i, "ordertime");
            $fullname = db_result($res, $i, "fullname");
            $address1 = db_result($res, $i, "address1");
            $suburb = db_result($res, $i, "suburb");
            $postcode = db_result($res, $i, "postcode");
            $state = db_result($res, $i, "state");
            $totalcost = db_result($res, $i, "totalcost");
            $phone = db_result($res, $i, "contactnumber");
            $email = db_result($res, $i, "email");
            $status = db_result($res, $i, "status");

            $fulladdress = "$address1, $suburb, $state $postcode";

            $link = "view-order.php?action=VIEW&order_id=$order_id";
            $editlink = '<a href="'.$link.'">View</a>';
            $del = "list-orders.php?action=DELETE&order_id=$order_id";
            $dellink = '<a href="'.$del.'">Delete</a>';
            $des = "list-orders.php?action=DESPATCH&order_id=$order_id";
            $deslink = '<a href="'.$des.'">Despatch</a>';
            echo "
               <tr>
                  <td class='catDesc'>$order_id</td>
                  <td class='status'>$date</td>
                  <td class='status'>$fullname</td>
                  <td class='status'>$fulladdress</td>
                  <td class='status'>$phone</td>
                  <td class='status'>$email</td>
                  <td class='status'>$status</td>
                  <td class='status'>$totalcost</td>
                  <td class=\"status\">$deslink</td>
                  <td class=\"status\">$editlink</td>
                  <td class='status'>$dellink</td>
               </tr>";
         }
      }

      echo "</table>";
   }

   function LoadUserIdAllowance($user_id)
   {
      $curdate = date('Y-m-d');
      if(!$this->ordertime)
         $this->ordertime = $curdate;
      else
         $curdate = $this->ordertime;

      $this->allowanceObj->loadAllowance($curdate, $user_id);

      $amt = $this->allowanceObj->amount;
      if(!$amt)
         $amt = 0;

      $_SESSION[_ALLOWANCE] = $amt;
   }
   
   function GetAlreadyPaid($user_id, $order_id)
   {
      $query = "select sum(payable) as total from orders where user_id = $user_id";
//echo "$query<BR>";
      if($order_id)
         $query .= " and order_id < $order_id"; // don't want paid on this order

      //get allowance start & end
      $al_start = $this->allowanceObj->start;
      $al_end = $this->allowanceObj->end;

      if($al_start && $al_end) //in case there is no allowance set but there are dates!!
      {
         $query .= " and order_time between '$al_start' and '$al_end'";
      }

//      echo "$query<BR>";
      $res = db_query($query);
      $num = db_numrows($res);
      $alreadyPaid = 0;

      if($num > 0)
         $alreadyPaid = db_result($res, 0, "total");

      return $alreadyPaid;
   }   

   function LoadOrderId($order_id)
   {
      $query = "select * from orders where order_id = $order_id";
      $res = db_query($query);
      $num = db_numrows($res);
      if($num > 0)
      {
         $this->order_id = $order_id;
         $this->user_id = db_result($res, 0, "user_id");
         $this->location_id = db_result($res, 0, "location_id");
         $this->ordertime = db_result($res, 0, "order_time");
         $this->lastupdated = db_result($res, 0, "lastupdated");
         $this->fullname = db_result($res, 0, "name");
         $this->address = db_result($res, 0, "address");
         $this->suburb = db_result($res, 0, "suburb");
         $this->postcode = db_result($res, 0, "postcode");
         $this->state = db_result($res, 0, "state");
         $this->phone = db_result($res, 0, "contact");
         $this->email = db_result($res, 0, "email");
         $this->status = db_result($res, 0, "status");
         $this->payable = db_result($res, 0, "payable");
         $this->receipt = db_result($res, 0, "receipt");
         $this->iswages = db_result($res, 0, "iswages");
         $this->approval_time = db_result($res, 0, "approvaltime");
         $this->formsubmitted = db_result($res, 0, "formsubmitted");
         $this->paid = db_result($res, 0, "paid");
         $this->sname = db_result($res, 0, "sname");
         $this->cardname = db_result($res, 0, "cardname");
         $this->cardnumber = db_result($res, 0, "cardnumber");
         $this->expiry = db_result($res, 0, "expiry");
         $this->cardtype = db_result($res, 0, "cardtype");
         $this->comments = db_result($res, 0, "comments");
         $this->connote = db_result($res, 0, "connote");
         $this->agree = db_result($res, 0, "agree");
         $this->jurisdiction = db_result($res, 0, "jurisdiction");
         $this->bulk = db_result($res, 0, "bulk");
         $this->costcentre = db_result($res, 0, "cost_centre");
         $this->country = db_result($res, 0, "country");
  
         if($this->jurisdiction == "AU" || !$this->jurisdiction)
            $this->jurisdiction = $this->state;
         
         $this->lineitems = array();
         
         $this->numpays = db_result($res, 0, "numpays");
         $this->amountperpay = db_result($res, 0, "amountperpay");
         $this->paymentopt = db_result($res, 0, "paymentopt");
         $this->LoadUserIdAllowance($this->user_id);
         $this->PopulateLineItems($order_id);
         //now load lineitems

         $this->email = db_result($res, 0, "email");
         $this->action = _UPDATE;
      }
   }

   function LoadOrder()
   {
      $order_id = _checkIsSet('order_id');
      $this->LoadOrderId($order_id);

      //now load the allocation
      //load allocation here, assume its not to update/view an order
      $this->allocationObj = new allocation();
      $this->allocationObj->getMaxAllocations($this->user_id, $this->ordertime );
   }

   function UpdateCart()
   {
      //check for items to del
      $prodIdArr = _checkIsSet("prod_id");
      for($i = 0; $i < count($prodIdArr); $i++)
      {
         $id = $prodIdArr[$i];
         $qtyName = "qty$id";
         $delName = "del$id";
         $qty = _checkIsSet($qtyName);
         $minQty = $this->lineitems[$id]->product->minQty;

         if($qty < $minQty)
            $qty = $minQty;
         $this->lineitems[$id]->qty = $qty;

         $del = _checkIsSet($delName);
         if($del)
            unset($this->lineitems[$id]);

      }
   }

   function PopulateBasket($user_id)
   {
   	$query = "select * from basket where user_id = $user_id";
   	$res = db_query($query);
   	$numrows = db_numrows($res);
   	if($numrows > 0)
   	{
   		for($i = 0; $i < $numrows; $i++)
   		{
   		$prod_id = db_result($res, $i, "prod_id");
   		$size = db_result($res, $i, "size");
   		$qty = db_result($res, $i, "qty");
   		$unitcost = db_result($res, $i, "unitcost");
   		$backordered = db_result($res, $i, "backordered");
   		$this->addLineItemBasket($qty, $prod_id, $size, $backordered);
   		}
   	} 
   	return $numrows;  	
   }
   
   function DeleteBasket($user_id)
   {
   	$query = "delete from basket where user_id = $user_id";
   	db_query($query);
   }
   
   function PopulateLineItems($order_id)
   {
      $query = "select * from lineitems where order_id = $order_id";
      $res = db_query($query);
      $numrows = db_numrows($res);
      if($numrows > 0)
      {
         for($i = 0; $i < $numrows; $i++)
         {
            $prod_id = db_result($res, $i, "prod_id");
            $size = db_result($res, $i, "size");
            $qty = db_result($res, $i, "qty");
            $unitcost = db_result($res, $i, "unitcost");
            $backordered = db_result($res, $i, "backordered");
//             $this->addLineItem($qty, $prod_id, $size, $backordered);
            $emb = db_result($res, $i, "emb");
            $this->addLoadedLineItem($qty, $prod_id, $size, $backordered,$emb);                
         }
      }
   }

   function delete()
   {
      $idArr = _checkIsSet("itemArr");
      $num = count($idArr);
      $numdel = 0;

      for($i = 0; $i < $num; $i++)
      {
         $curId = $idArr[$i];

         $this->LoadOrderId($curId);
         if($this->status != _APPROVED && $this->status != _PENDING)
         {
         	$_SESSION['msg'] = "Could not delete ($curId) - Only orders in the APPROVED/PENDING state can be deleted.";
         	db_query(_ROLLBACK);
         	return false;
         }

         $query = "delete from orders where order_id = $curId";
         if(db_query($query))
            $numdel++;
      }
      if($numdel > 0)
         return true;
      else
         return false;

   }

//   function Delete()
//   {
//      $order_id = _checkIsSet("order_id");
//      if($order_id)
//      {
//         $query = "delete from orders where order_id = $order_id";
//         $res = db_query($query);
//         if($res)
//         {
//            $_SESSION['msg'] = "Order deleted";
//            return true;
//         }
//         else
//         {
//            $_SESSION['msg'] = "Delete failed, please try again.";
//            return false;
//         }
//      }
//      $_SESSION['msg'] = "Delete failed, please try again.";
//      return false;
//   }

   function Despatch()
   {
      $order_id = _checkIsSet("order_id");
      if($order_id)
      {
         $query = "update orders set status = 'DESPATCHED' where order_id = $order_id";
         $res = db_query($query);
         if($res)
         {
            $_SESSION['msg'] = "Order despatched";
            return true;
         }
         else
         {
            $_SESSION['msg'] = "Despatch failed, please try again.";
            return false;
         }
      }
      $_SESSION['msg'] = "Despatch failed, please try again.";
      return false;
   }

   function removeAllItems($prod_id, $size)
   {
      $key = $prod_id . "_$size";
      $key=str_replace(array(" ","/","(",")"),"-",$key);
  
      if(array_key_exists($key, $this->lineitems))
      {
         $tmpCost = $this->lineitems[$key]->lineCost();
         unset($this->lineitems[$key]);
      }
      else if(array_key_exists($key, $this->lineitems))
      {
         $tmpCost = $this->combinedLineitems[$key]->lineCost();
         unset($this->combinedLineitems[$key]);         
      }
      
      $query = "delete from basket where prod_id = $prod_id and size = '$size' and user_id = " . $_SESSION[_USER_ID];
      db_query($query);
      
      return $tmpCost;
   }
   
   function getCombinedQty()
   {
   	$cQty = 0;
      foreach($this->combinedLineitems as $combinedKey=>$cli)
      {
      	$cli_prod_id = $cli->product->prod_id;
      	$cli_qty = $cli->qty;
      	$cli_cat_id = $cli->product->cat_id;
      	$tmpQty = $cli_qty;
      	
      	$cQty += $tmpQty;
      }
//       $prod = new product();
//       $prod->LoadProductId($prod_id);
//       if($prod->cat_id == 2 || $prod->cat_id == 3)
//       	$cQty += 
//       	echo "=CLI= PRODID: $cli_prod_id QTY: $cli_qty TMP QTY: $tmpQty CAT_ID: $cli_cat_id <BR>\n";      
      return $cQty;   	
   }   
   
   function addLineItem($qty, $prod_id, $size, $backordered)
   {
      $li = new lineitems();
      //found existing
      $key = $prod_id . "_$size";    
      $key=str_replace(array(" ","/","(",")"),"-",$key);
      
     	//check if combined allocated and store in separte lineitemArr;
      $maxCombined = $this->allocationObj->getMaxCatType(_COMBINED_GARMENT_TYPE);
      $combinedOrdered = $this-> getTypeOrderedDB(_COMBINED_GARMENT_TYPE);
      $remainCombined = $maxCombined - $combinedOrdered;
      $curProd = new product();
      $curProd->LoadProductId($prod_id, $this->isAUS);
      $prodCatID = $curProd->cat_id;
      $cQty = 0;

      $personalName = _checkIsSet("embroideryLogo");
      
      if(strlen($personalName) > 0 && $personalName != "Emb-GPC")
          $key .= "_$personalName";      

      $cQty = $this->getCombinedQty();
      $remainCombined -= $cQty;
      $qtySave = $qty;
      
	   if($remainCombined > 0) //all garment categories will be included in the combined topup allocation 2017 except for PP, 9= PP, 10 = maternity
   	{
   	//	echo "rcQTY: $qty\n";
   		//qty ordered is enough to cover what is remaining
     		if($qty <= $remainCombined)
     		{
     			$cQty = $qty;
     			
     		}
     		else 
     		{
     			$cQty = $remainCombined;
     		}
     		
     		$qty -= $remainCombined;
     		
     		if($qty <= 0 && $remainCombined >0)
     		{
	     		if(isset($this->combinedLineitems[$key]))
			   {
			      $this->combinedLineitems[$key]->qty = $this->combinedLineitems[$key]->qty + $cQty;
			      $this->combinedLineitems[$key]->cat_id_charge = $this->combinedLineitems[$key]->qty;
			      
// 			      if($prod_id == 266 || $prod_id == 267)
               if($personalName)
			      	$this->combinedLineitems[$key]->emb = "$personalName";
			      
			      $newQty = $this->combinedLineitems[$key]->qty;
	 		      $this->combinedAdded = true;
			      //update basket
			      if($this->user_id == $_SESSION[_USER_ID])
				      $query = "UPDATE `basket` SET  `combine_id` = "._COMBINED_GARMENT_TYPE.", qty` =  '$newQty' WHERE  `prod_id` = $prod_id and size = '$size' and user_id = " . $_SESSION[_USER_ID];
			      db_query($query);
			   }
			   else
			   {
			      if($li->addItem($cQty, $prod_id, $size, $backordered, $this->user_id, $this->isAUS))
			      {
			      	$li->cat_id_charge = $cQty;
// 			      	if($prod_id == 266 || $prod_id == 267)
                  if($personalName)
                  {
                     if(strlen($li->emb) == 0)
   			      		$li->emb = "$personalName";
                     else
                        $li->emb .=";$personalName";
                  }
			      	//echo "adding LINEITEM!\n";
			         $this->combinedLineitems[$key] = $li;
			         $this->combinedAdded = true;
			         if($qty <= 0) //add balance to stand lineitems
				         return true;
			      }
			      else
			         return false;
			   } 
     		}
     		else 
     		{
     			//if we don't have enough combined, revert back to ordered qty so that the actual qty ordered is saved in the lineitem array
     			$this->combinedAdded = false;
     			$qty = $qtySave;
     		}
    	}
    	else $this->combinedAdded = false;
    	
    	// echo "QTY LEFTOVER: $qty<BR>\n";

        // echo "AFTER COMBINED ADD addLineitem: $remainCombined COUNT OF LINEITEMS: " .count($this->combinedLineitems). " QTY TO ADD: $qty<BR>\n"     ;
        // echo "COMBINED QTY: $cQty LEFT OVER QTY: $qty<BR>\n";
    	// either add the full qty add to cart or just the remained from the combined
    	if($qty > 0)
    	{ 
    	   $li = new lineitems();
    	   
	       //if($this->lineitems[$key] != null)
	       if(isset($this->lineitems[$key]))
	       {
	          $this->lineitems[$key]->qty = $this->lineitems[$key]->qty + $qty;
	          global $optionalGarmentTypes;
       	   if(in_array($prodCatID, $optionalGarmentTypes))
       	      $this->lineitems[$key]->cat_id_charge =  $this->lineitems[$key]->cat_id_charge + $qty;	          
	          
// 	      	  if($prod_id == 266 || $prod_id == 267)
            if($personalName)
	      		 $this->lineitems[$key]->emb = "$personalName";	         
	          $newQty = $this->lineitems[$key]->qty;
	         
	          if(!$this->combinedAdded)
	      	     $this->combinedAdded = false;	         
	          //update basket
	          if($this->user_id == $_SESSION[_USER_ID])
		         $query = "UPDATE `basket` SET  `qty` =  '$newQty' WHERE  `prod_id` = $prod_id and size = '$size' and user_id = " . $_SESSION[_USER_ID];
	          db_query($query);
	       }
	       else
	       {
	          if($li->addItem($qty, $prod_id, $size, $backordered, $this->user_id,$this->isAUS))
	          {
global $optionalGarmentTypes;	             
          	   if(in_array($prodCatID, $optionalGarmentTypes))
          	   {
          	      $li->cat_id_charge = $qty;	             
//           	      echo "ITEM:$prodCatID needs to pay $qty<BR>"; 
          	   }
// 	             echo "ADDING TO STANDARD LI [$prodCatID]!<BR>\n";
               if($personalName)
               {
                  if(strlen($li->emb) == 0)
   			   		$li->emb = "$personalName";
                  else
                     $li->emb .=";$personalName";
               }
			      		         	
	             $this->lineitems[$key] = $li;
		         if(!$this->combinedAdded)	            
			         $this->combinedAdded = false;
	             return true;
	          }
	          else
	             return false;
	       }
    	}
   }   
   
   function addLoadedLineItem($qty, $prod_id, $size, $backordered, $emb)
   {
      $li = new lineitems();
      //found existing
      $key = $prod_id . "_$size";      
      $key=str_replace(array(" ","/","(",")"),"-",$key);
      
     	//check if combined allocated and store in separte lineitemArr;
      $maxCombined = $this->allocationObj->getMaxCatType(_COMBINED_GARMENT_TYPE);
      $combinedOrdered = $this-> getTypeOrderedDB(_COMBINED_GARMENT_TYPE);
      $remainCombined = $maxCombined - $combinedOrdered;
      $curProd = new product();
      $curProd->LoadProductId($prod_id, $this->isAUS);
      
      $prodCatID = $curProd->cat_id;
      $cQty = 0;

      $personalName = $emb;
      
      if(strlen($personalName) > 0 && $personalName != "Emb-GPC")
          $key .= "_$personalName";      

      $cQty = $this->getCombinedQty();
      $remainCombined -= $cQty;
      $qtySave = $qty;
      
	  if($remainCombined > 0) //all garment categories will be included in the combined topup allocation 2017 except for PP, 9= PP, 10 = maternity
   	  {
   		//qty ordered is enough to cover what is remaining
     		if($qty <= $remainCombined)
     		{
     			$cQty = $qty;
     			
     		}
     		else 
     		{
     			$cQty = $remainCombined;
     		}
     		
	  		$qty -= $remainCombined;
     		
     	//	echo "KEY: $key rcombined: $remainCombined QTY: $qty<BR>";
     		if($qty <=0)
     		{
	     		if(isset($this->combinedLineitems[$key]))
			   {
			      $this->combinedLineitems[$key]->qty = $this->combinedLineitems[$key]->qty + $cQty;
			      $this->combinedLineitems[$key]->cat_id_charge = $this->combinedLineitems[$key]->qty;
			      
// 			      if($prod_id == 266 || $prod_id == 267)
               if($personalName)
			      	$this->combinedLineitems[$key]->emb = "$personalName";
			      
			      $newQty = $this->combinedLineitems[$key]->qty;
	 		      $this->combinedAdded = true;
			      //update basket
			      if($this->user_id == $_SESSION[_USER_ID])
				      $query = "UPDATE `basket` SET  `combine_id` = "._COMBINED_GARMENT_TYPE.", qty` =  '$newQty' WHERE  `prod_id` = $prod_id and size = '$size' and user_id = " . $_SESSION[_USER_ID];
			      db_query($query);
			   }
			   else
			   {
			      if($li->addItem($cQty, $prod_id, $size, $backordered, $this->user_id, $this->isAUS))
			      {
			      	$li->cat_id_charge = $cQty;
// 			      	if($prod_id == 266 || $prod_id == 267)
                  if($personalName)
                  {
                     if(strlen($li->emb) == 0)
   			      		$li->emb = "$personalName";
                     else
                        $li->emb .=";$personalName";
                  }
			      	//echo "adding LINEITEM!\n";
			         $this->combinedLineitems[$key] = $li;
			         $this->combinedAdded = true;
			         if($qty <= 0) //add balance to stand lineitems
				         return true;
			      }
			      else
			         return false;
			   } 
     		}
     		else 
     		{
     			//if we don't have enough combined, revert back to ordered qty so that the actual qty ordered is saved in the lineitem array
     			$this->combinedAdded = false;
     			$qty = $qtySave;
     		}
    	}
    	else $this->combinedAdded = false;
    	//
    	//echo "QTY LEFTOVER: $qty<BR>\n";

     // echo "AFTER COMBINED ADD addLineitem: $remainCombined COUNT OF LINEITEMS: " .count($this->combinedLineitems). " QTY TO ADD: $qty<BR>\n"     ;
   //  echo "COMBINED QTY: $cQty LEFT OVER QTY: $qty<BR>\n";
    	//either add the full qty add to cart or just the remained from the combined
    	if($qty > 0)
    	{ 
    		$li = new lineitems();
	      //if($this->lineitems[$key] != null)
	      if(isset($this->lineitems[$key]))
	      {
	         $this->lineitems[$key]->qty = $this->lineitems[$key]->qty + $qty;
// 	      	if($prod_id == 266 || $prod_id == 267)
            if($personalName)
	      		$this->lineitems[$key]->emb = "$personalName";	         
	         $newQty = $this->lineitems[$key]->qty;
	         
	         if(!$this->combinedAdded)
	      	   $this->combinedAdded = false;	         
	         //update basket
	         if($this->user_id == $_SESSION[_USER_ID])
		         $query = "UPDATE `basket` SET  `qty` =  '$newQty' WHERE  `prod_id` = $prod_id and size = '$size' and user_id = " . $_SESSION[_USER_ID];
	         db_query($query);
	      }
	      else
	      {
	         if($li->addItem($qty, $prod_id, $size, $backordered, $this->user_id, $this->isAUS))
	         {
	         	//echo "ADDING TO STANDARD LI!<BR>\n";
// 			     if($prod_id == 266 || $prod_id == 267)
                 if($personalName)
                  {
                     if(strlen($li->emb) == 0)
   			      		$li->emb = "$personalName";
                     else
                        $li->emb .=";$personalName";
                  }
			      		         	
	            $this->lineitems[$key] = $li;
		         if(!$this->combinedAdded)	            
			         $this->combinedAdded = false;
	            return true;
	         }
	         else
	            return false;
	      }
    	}
   }      

   function addLineItemOLD($qty, $prod_id, $size, $backordered)
   {
      $li = new lineitems();
      //found existing
      $key = $prod_id . "_$size";
      $key=str_replace(array(" ","/","(",")"),"-",$key);
      //if($this->lineitems[$key] != null)
      if(isset($this->lineitems[$key]))
      {
         $this->lineitems[$key]->qty = $this->lineitems[$key]->qty + $qty;
         $newQty = $this->lineitems[$key]->qty;
         //update basket
         if($this->user_id == $_SESSION[_USER_ID])
	         $query = "UPDATE `basket` SET  `qty` =  '$newQty' WHERE  `prod_id` = $prod_id and size = '$size' and user_id = " . $_SESSION[_USER_ID];
         db_query($query);
      }
      else
      {
         if($li->addItem($qty, $prod_id, $size, $backordered, $this->user_id, $this->isAUS))
         {
            $this->lineitems[$key] = $li;
            return true;
         }
         else
            return false;
      }
   }
   
   function loadRulesRemain()
   {
   	//load rules where start >= order time and end <= order time
   	$uid = $this->user_id;
   	$odate = $this->ordertime;
   	$tmpTimeArr = explode(" ", $odate);
		$sdate = $tmpTimeArr[0] . " 00:00:00";
		$edate = $tmpTimeArr[0] . " 23:59:00";
   	
   	$query = "select * from rules where user_id = $uid and start <= '$sdate' and end >= '$edate'";
   	$res = db_query($query);
   	$num = db_numrows($res);
//    	echo "$query<BR>";
   	$tmpRulesArr = array();
   	$tmpRulesTitle = array();
   	
   	if($num > 0)
   	{
   		for($i = 0; $i < $num; $i++)
   		{
   			$cat_type = db_result($res, $i, "cat_type");
   			$max = db_result($res, $i, "max_allowed");
   			$alreadyOrderQtyCatID = $this->getTypeOrderedDB($cat_type);
   			$tmpRulesArr[$cat_type] += ($max - $alreadyOrderQtyCatID);
   			$tmpRulesTitle[$cat_type] = db_result($res, $i, "title");
   			
   			//get already ordered;
   			
   			
//    			echo "ALREADY ORDER CATID: $alreadyOrderQtyCatID CID: $cat_type<BR>";
   		}
   	}
   	
//    	if($this->action == _SAVE)
//    	{
   		
//    		//check if we've used alloc on older orders
//    		$query = "select * from orders o, lineitems li where o.order_id = li.order_id and o.user_id = $uid and o.ordertime > " . $this->order_id;
//    		echo "$query<BR>";
//    		$res = db_query($query);
//    		$num = db_numrows($res);
//    		if($num > 0)
//    		{
//    			for($i = 0; $i < $num; $i++)
//    			{
//    				$cat_type = db_result($res, $i, "cat_id");
//    				$qty = db_result($res, $i, "qty");
   				
// 			   	$tmpRulesArr[$cat_type] -= $qty;
//    			}
//    		}
   		
//    	}
		$rulesArr[0] =$tmpRulesArr;
		$rulesArr[1] = $tmpRulesTitle;
   	return $rulesArr;
   }   
   
   function addLineItemBasket($qty, $prod_id, $size, $backordered)
   {
   	$li = new lineitems();
   	//found existing
   	$key = $prod_id . "_$size";
   	 $key=str_replace(array(" ","/","(",")"),"-",$key);
   	//if($this->lineitems[$key] != null)

   		if($li->addItemBasket($qty, $prod_id, $size, $backordered, $this->isAUS))
   		{
   			$this->lineitems[$key] = $li;
   			return true;
   		}
   		else
   			return false;
   }   

   function changeStatus($status)
   {
      if(!minAccessLevel(_STATE_LEVEL))
         return false;
      $idArr = _checkIsSet("itemArr");
      $num = count($idArr);
      $numchange = 0;

      db_query(_BEGIN);
      for($i = 0; $i < $num; $i++)
      {
         $curId = $idArr[$i];

         $query = "select status from orders where order_id = $curId";
         $res = db_query($query);
         $qnum = db_numrows($res);
         if($qnum > 0)
         {
            $curstatus = db_result($res, 0, "status");
            if($curstatus != _PENDING)
            {
               $_SESSION['msg'] = "Only PENDING orders may be approved.";
               db_query(_ROLLBACK);
               return false;
            }
         }

         $query = "update orders set status='$status' where order_id = $curId";
         if(db_query($query))
            $numchange++;
      }
      if($numchange > 0)
      {
         db_query(_COMMIT);
         return true;
      }
      else
      {
         db_query(_ROLLBACK);
         return false;
      }

   }
}

class allocation
{
   var $allocation_id;
   /*
   var $maxJacket;
//   var ${_UPPER_TYPE};
   var $maxLower;
   var $maxAcc;
   var $maxPolo;
   var $maxOuter;
   var $maxKnit; //softshell, outer, etc
   var $maxShirt;
   var $maxHiVis;
   var $maxCombined;
   var $maxBadge;
   */
   var $start;
   var $end;
   var $user_id;
   var $rule_type;

   function __construct()
   {
      $this->allocation_id = array();
      
      /*
      $this->maxJacket = 0;//RESERVE
      $this->{"g". _UPPER_TYPE} = 0; 
      $this->maxLower = 0;//optional
      $this->maxAcc = 0;
      $this->maxPolo = 0;
      $this->maxOuter = 0; 
      $this->maxShirt = 0;
      $this->maxHiVis = 0;
      $this->maxKnit = 0;
      $this->maxCombined = 0;
      $this->maxBadge = 0;
      */
      
      global $garmentTypes;
      for($i = 0; $i < count($garmentTypes); $i++)
      {      
         $curGarmentType = $garmentTypes[$i];
         $this->{"g". $curGarmentType} = 0;          
      }
      
      $this->start = array();
      $this->end = array();
      $this->user_id = "";
      $this->rule_type = "";
      
     // echo "UPPER TYPE: " .     $this->{"g" . _UPPER_TYPE} . "<BR>";
   }

   /* now that we have expiry, don't need ruletype */
   function getMaxAllocations($user_id, $order_time)
   {
      $endDate = date('Y-m-d'); //current date
      $curDate = date('Y-m-d h:m:s');
      if($order_time != "")
      {
         $endDate = $order_time;
         $endTimeArr = explode(" ", $endDate);
         //$this->end = $endTimeArr[0] . " 23:59:59"; // change the end to end of day
         $endDate = $endTimeArr[0] . " 23:59:59"; // change the end to end of day

         $endTimeArr = explode(" ", $endDate);
         $startDate = $endTimeArr[0] . " 00:00:00";

         //$query = "select * from rules where user_id = $user_id and start > '$startDate'";
         $query = "select * from rules where user_id = $user_id and end >= '$order_time' and start <= '$order_time'";
      }
      else
         $query = "select * from rules where user_id = $user_id and end >= '$endDate'";
// echo "$query<BR>";
      $res = db_query($query);
      $num = db_numrows($res);

      if($num > 0)
      {
         for($i = 0; $i < $num; $i++)
         {
            $cat_type = db_result($res, $i, "cat_type");
            $alloc = db_result($res, $i, "max_allowed");
            $endDate = db_result($res, $i, "end");
            $startDate = db_result($res, $i, "start");

            /* not safe! */
            if(count($this->start[$cat_type]) == 0)
               $this->start[$cat_type] = array();
            array_push($this->start[$cat_type], $startDate);
            //$this->start[$cat_type] = $startDate;

            if(count($this->end[$cat_type]) == 0)
               $this->end[$cat_type] = array();
            array_push($this->end[$cat_type], $endDate);
            //$this->end[$cat_type] = $endDate;
//echo "ST: [$startDate] cur: [$curDate]<BR>";
            if($order_time != "")
            {
//               echo "ot: $order_time ed: $endDate st: $startDate<BR>";
               if($curDate < $startDate)
               {
                  $alloc = 0;
               }
               else if($order_time >= $startDate && $order_time < $endDate)
               {
                  //all ok!
               }
               else
               {
                  $alloc = 0;
                  //echo "greater than enddate<BR>";
               }
            }

            if($curDate < $startDate)
            {
               if($curDate != $startDate) //today is the day so just allow!
               {
                  $alloc = 0;
               }
            }

            $this->{"g". $cat_type} += $alloc;
         }
      }
   }
   
   function getMaxCatType($cat_type)
   {
      $max = $this->{"g". $cat_type};
      return $max;
   }
}
class allowance
{
   var $allowance_id;
   var $user_id;
   var $start;
   var $end;
   var $amount;

   function __construct()
   {
      $this->allowance_id = 0;
      $this->user_id = "";
      $this->start = "";
      $this->end = "";
      $this->amount = "";
   }

   function getLatestAllowance($userid)
   {
      $query = "select * from allowance where user_id = $userid order by allowance_id desc";
      $res = db_query($query);
      $num = db_numrows($res);
      if($num > 0)
      {
         $this->allowance_id = db_result($res, 0, 'allowance_id');
         $this->user_id = db_result($res, 0, 'user_id');
         $this->start = db_result($res, 0, 'start');
         $tmpEnd = db_result($res, 0, 'end');
         $endTimeArr = explode(" ", $tmpEnd);
         $this->end = $endTimeArr[0] . " 23:59:00"; // change the end to end of day
         $this->amount = db_result($res, 0, 'allowance');
      }
   }

   function loadAllowance($ordertime, $userid)
   {
      //reset order time to 00:00:00
      $tmpTimeArr = explode(" ", $ordertime);
      $ordertime = $tmpTimeArr[0] . " 00:00:00";

      $query = "select * from allowance where start <= '$ordertime' and end >= '$ordertime' and user_id = $userid";

      $res = db_query($query);
      $num = db_numrows($res);

//      echo "$query<BR>";
      if($num > 0)
      {
         $this->allowance_id = db_result($res, 0, 'allowance_id');
         $this->user_id = db_result($res, 0, 'user_id');
         $this->start = db_result($res, 0, 'start');
         $tmpEnd = db_result($res, 0, 'end');
         $endTimeArr = explode(" ", $tmpEnd);
         $this->end = $endTimeArr[0] . " 23:59:00"; // change the end to end of day
         $this->amount = db_result($res, 0, 'allowance');
      }
   }
}


class lineitems
{
   var $lineitem_id;
   var $order_id;
   var $qty;
   var $size;
   var $product;
   var $unitcost;
   var $emb;
   var $cat_id;
   var $cat_id_charge; // for combined garment, qty to charge on this lineitem - e.g. ordered 3, qualify 2 charge should = 1
   var $category; //womens or menswear
   var $backordered;

   function __construct()
   {
      $this->lineitem_id = "";
      $this->order_id = "";
      $this->qty = 0;
      $this->size = "";
      $this->emb = "";
      $this->backordered = 0;
      $this->cat_id_charge = -1;
      $this->product = new product();
   }

   function loadLineCost()
   {
      return $this->qty * $this->unitcost;
   }
   function lineCost()
   {
      $qtyEntered = $this->qty;
      $unitcost = $this->unitcost;
      return $qtyEntered * $unitcost;
   }

  function determineEmb($prod_id, $cat_id)
  {
		$query = "select * from productcategory where prod_id = $prod_id";
		$res = db_query($query);
		$num = db_numrows($res);
  	
		if($num > 0)
		{
			return db_result($res, 0, "emb");
  		}

  		return "";
  }

   function addItem($qty, $prod_id, $size, $backordered, $user_id, $isAUS)
   {
      $this->qty = $qty;
      $this->size = $size;
      $this->backordered = $backordered;
      $unitcost = 0;
      if($this->product->LoadProductId($prod_id, $isAUS))
      {
//             if($isAUS == "Y")
//                $unitcost = $this->product->price*1.1;
//             else 
               $unitcost = $this->product->price;
            
            $this->unitcost = $unitcost;
            
         $this->cat_id = $this->product->cat_id;
         $this->category = $this->product->category;
         
         $this->emb = $this->determineEmb($prod_id, $this->cat_id);
//          if($user_id == $_SESSION[_USER_ID])
// 	         $this->saveBasket($_SESSION[_USER_ID]);
         return true;
      }
      else
         return false;
   }
   
   function addItemBasket($qty, $prod_id, $size, $backordered, $isAUS)
   {
   	$this->qty = $qty;
   	$this->size = $size;
   	$this->backordered = $backordered;
   
   	if($this->product->LoadProductId($prod_id, $isAUS))
   	{
            if($isAUS == "Y")
               $unitcost *= $this->product->price*1.1;
            else 
               $unitcost = $this->product->price;
            
            $this->unitcost = $unitcost;
            
   		$this->cat_id = $this->product->cat_id;
   		$this->category = $this->product->category;
   		 
   		$this->emb = $this->determineEmb($prod_id, $this->cat_id);
   		return true;
   	}
   	else
   		return false;
   }   
   
   function saveBasket($user_id)
   {
   	$prod_id = $this->product->prod_id;
   	$item_number = $this->product->item_number;
   	$myob_code = $this->product->myob_code;
   	$qty = $this->qty;
   	$size = $this->size;
   	$emb = $this->emb;
   	$unitcost = $this->product->price; //ex gst prices - already ex GST!!!!
   	$cat_id = $this->product->cat_id;
   
   	if(!$qty)
   		$qty = "0";
   	$query = "INSERT INTO basket (user_id, prod_id, myob_code, qty, `size`, price, cat_id ) VALUES  ($user_id, $prod_id, '$myob_code', $qty, '$size', $unitcost, $cat_id)";
   	$res = db_query($query);
   	if($res)
   	{
 			return true;
   	}
   	else
   		return false;
   }   

   function saveLineItem($order_id, $isAUS)
   {
      $prod_id = $this->product->prod_id;
      $item_number = $this->product->item_number;
      $myob_code = $this->product->myob_code;
      $qty = $this->qty;
      $size = $this->size;
      $emb = $this->emb;
      $unitcost = $this->product->price; //ex gst prices - already ex GST!!!!
      $cat_id = $this->product->cat_id;
      $gst = "Y";
      
      if($isAUS == "Y")
      {
         $unitcost = $this->product->price/1.1; //need ex gst prices, as prices from prod class inc gst         
      }
      else //NZ pricing
      {
         $unitcost = $this->product->price;
         $gst = "N";
      }      

      $embArr = explode(";", $emb);
      $myob_emb = "";
      if($embArr > 0)
      {
         for($i = 0; $i < count($embArr); $i++)
         {
            $tmpEmb = $embArr[$i];
//             echo "EMPEMB: $tmpEmb [" .substr($tmpEmb, 0, 3) . "]<BR>" ;
            if(strlen($tmpEmb) > 0)
            {
               if(substr($tmpEmb, 0, 3) == "Emb")
                  $myob_emb .= "$tmpEmb;";
               else 
                  $myob_emb .= "Personal Name-RHS;"; //assume personal name
            }
         }
      }
      
      if(!$qty)
         $qty = "0";
      if($this->cat_id_charge > 0)
      {
      	$combined_id = _COMBINED_GARMENT_TYPE;
         $query = "INSERT INTO lineitems (order_id, prod_id, myob_code, `emb`, `myob_emb`, qty, `size`, price, cat_id, `combine_id`, `gst`) VALUES  ($order_id, $prod_id, '$myob_code', \"$emb\", \"$myob_emb\",$qty, '$size', $unitcost, $cat_id, $combined_id, '$gst')";
      }
      else
	      $query = "INSERT INTO lineitems (order_id, prod_id, myob_code, `emb`, `myob_emb`, qty, `size`, price, cat_id, `gst`) VALUES  ($order_id, $prod_id, '$myob_code', \"$emb\", \"$myob_emb\",$qty, '$size', $unitcost, $cat_id, '$gst')";
//  echo "$query<BR>";
      $res = db_query($query);
      
      if($res)
      {
         /*     
         if($emb)
         {
         	
         	if($prod_id == 48)
         	{
         	   /*
               $queryEmb = "INSERT INTO lineitems (order_id, myob_code, qty, price, emb ) VALUES  ($order_id, '$myob_code', $qty, 0, '$emb')";
               $resEmb = db_query($queryEmb);     
     // echo "$queryEmb<BR>";                   	
	            if($resEmb)
	               return true;
	            else
	               return false;               	
	               *
	            //dont need for badges
	            return true;
         	}
         	else {
	            $emb_code = $this->emb;
	            $queryEmb = "INSERT INTO lineitems (order_id, myob_code, qty, price ) VALUES  ($order_id, '$emb_code', $qty, 0)";
	            $resEmb = db_query($queryEmb);
	            if($resEmb)
	               return true;
	            else
	               return false;
         	}
         }
         else
            return true;
         */
         return true;
      }
      else
         return false;
   }
}
?>