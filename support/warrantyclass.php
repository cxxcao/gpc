<?php
session_start();
$home = dirname(__FILE__) . "/../";
$lib = $home ."/lib/";

require_once($lib . 'database.php');
require_once($home . '/globals.php');
require_once($lib . 'functions.php');
require_once($lib . 'dbfunctions.php');
require_once($lib . 'loginfunctions.php');
require_once($lib . 'htmlGenerator.php');
require_once($lib . 'phpmailer/class.phpmailer.php');

require_once($home . 'products/productsclass.php');
require_once($home . 'products/ordersclass.php');

if(_ENV == "DEV")
	require_once($home . '../consignment/consignmentclass.php');
else
	require_once($home . '../consignment/consignmentclass.php');

class jsonLineItem
{
   var $prod_id;
   var $myob_code;
   var $qty;
   var $size;
   var $lineitem_id;
   var $size_arr;

   function jsonLineItem()
   {

   }
}

class returnLines
{
   var $prod_id;
   var $myob_code;
   var $qty;
   var $size;
   var $line_id;

   var $rprod_id;
   var $rmyob_code;
   var $rqty;
   var $rsize;

   var $rcv;
   var $rjt;

   var $return_type;
}

class warranty
{
   var $warranty_id;
   var $order_id;
   var $name;
   var $email;
   var $phone;
   var $reason;
   var $status;
   var $claim_date;
   var $lastupdated;
   var $jurisdiction;
   var $eventlog;
   var $action_type;
   var $returnlines;
   var $numrowsadded;
   var $consignmentno;
   var $con_barcode;
   var $isAUS;
   var $order;

   function warranty()
   {
      $this->warranty_id = "";
      $this->order_id = _checkIsSet("order_id");
      $this->name = "";
      $this->phone = "";
      $this->email = "";
      $this->reason = "";
      $this->return_type = "";
      $this->status = _PENDING;
      $this->claim_date = date('Y-m-d');
      $this->lastupdated = "";

      $this->eventlog = array();
      $this->returnlines = array();
      $this->action_type = _SAVE;
      $this->numrowsadded = 0;
      $this->consignmentno = "";
      $this->con_barcode = "";
      $this->isAus = "Y";
      $this->order = new orders();
      $this->order->LoadOrderId($this->order_id);
      $this->jurisdiction = $this->order->jurisdiction; 
   }

   function FindLineitems($order_id)
   {
      $query = "select * from lineitems where order_id = $order_id";
      $res = db_query($query);
      $num = db_numrows($res);
      $jsonArr = array();
      //{lineitems: [{"prod_id" : abc, "myob_code" : def}, {etc}]}
      //construct json here?
      if($num > 0)
      {
         for($i = 0 ; $i < $num; $i++)
         {
            $prod_id = db_result($res, $i, "prod_id");
            $myob_code = db_result($res, $i, "myob_code");
            $qty = db_result($res, $i, "qty");
            $size = db_result($res, $i, "size");
            $lineitem_id = db_result($res, $i, "line_id");
            $this->isAUS = db_result($res, $i, "gst");

            $jli = new jsonLineItem();
            $jli->prod_id = $prod_id;
            $jli->myob_code = $myob_code;
            $jli->qty = $qty;
            $jli->size = $size;
            $jli->lineitem_id = $lineitem_id;
            $jli->size_arr = $this->getSize($prod_id);

            //$jsonStr .= "{\"prod_id\": $prod_id, \"myob_code\":$myob_code, \"qty\":$qty, \"size\":$size}";

            //if(($i+1) < $num)
            //   $jsonStr .= ",";
            array_push($jsonArr, $jli);
         }
      }

      $jsonStr .= "]";
      //echo "JSON STR: " . $jsonStr . "<BR>";
//      echo "<BR><BR>JSON STR: " . json_encode($jsonArr) . "<BR>";
      return json_encode($jsonArr);
   }

   function getSize($prod_id)
   {
      $query = "select * from sizes where prod_id = $prod_id";
      $res = db_query($query);
      $num = db_numrows($res);
      $sizeArr = array();
      if($num > 0)
      {
         for($i = 0; $i < $num; $i++)
         {
            $psize = db_result($res, $i, 'size');
            array_push($sizeArr, $psize);
         }
      }
      return $sizeArr;
   }
   
   function isFirst()
   {
   	$query = "select * from warranty w, orders o where o.order_id = w.order_id and o.user_id = " . $this->order->user_id;
   	$res = db_query($res);
   	$num = db_numrows($res);

   	if($num > 0)
   		return false;
   	else
   		return true;
   }


   function delete()
   {
      $idArr = _checkIsSet("itemArr");
      $num = count($idArr);
      $numdel = 0;

      for($i = 0; $i < $num; $i++)
      {
         $curId = $idArr[$i];
         //check status
         $this->LoadWarranty($curId);
         if($this->status != _PENDING)
         {
             $_SESSION['msg'] = "Could not delete ($curId) - Only returns in the PENDING state can be deleted.";
             return false;
         }
         $query = "delete from warranty where warranty_id = $curId";
         if(db_query($query))
            $numdel++;
      }
      if($numdel > 0)
         return true;
      else
         return false;

   }

   function ListWarranty($query)
   {
      $res = db_query($query);
      $num = db_numrows($res);

      echo '
          <table id="box-table-a">
          <thead>
            <tr>
              <th width="20px"><input id="checkbox" type="checkbox" name="mainbox" onClick="checkUnCheckAll(this.form)"></th>
              <th width="150px" align="left">Claim Date</th>
              <th width="150px" align="left">RA No</th>
              <th width="150px" align="left">Order No</th>
              <th width="200px" align="left">Name</th>
              <th width="150px" align="left">Phone</th>
              <th width="80px" align="left">Status</th>
            </tr>
          </thead>
          <tbody>';
      for($i = 0; $i < $num; $i++)
      {
         $warranty_id = db_result($res, $i, "warranty_id");
         $order_id = db_result($res, $i, "order_id");
         $claim_date = db_result($res, $i, "claim_date");
         $status = db_result($res, $i, "status");
         $name = ucwords(strtolower((db_result($res, $i, "name"))));
         $phone = db_result($res, $i, "phone");
         $link = _DIR . "support/returns.php?action=" . _UPDATE ."&" . _WARRANTY_ID . "=$warranty_id";

         $returns = "returns_$i";
         $claim_date_link = $claim_date;
         if(minAccessLevel(_ADMIN_LEVEL))
         {
            $claim_date_link = '<a href="#" id="'.$returns.'">'.$claim_date.'</a>';
         }

         echo'
            <tr>
              <td valign="left"><input id="checkbox" type="checkbox" name="removeArr[]" value="'.$warranty_id.'"></td>
              <td align="left" class="orderlist">'.$claim_date_link.'</td>
              <td valign="left" class="orderlist"><a href="'.$link.'">'.$warranty_id.'</a></td>
              <td align="left" class="orderlist">'.$order_id.'</td>
              <td align="left" class="orderlist">'.$name.'</td>
              <td align="left" class="orderlist">'.$phone.'</td>
              <td align="left" class="orderlist">'.$status.'</td>
            </tr>';
      }
      echo '</tbody></table>';
   }

   function EmailEvent($email, $ra_number, $event)
   {
         $mail = new PHPMailer();

         $mail->IsSMTP(); // telling the class to use SMTP
         $mail->SMTPKeepAlive = true;

         //$mail->Host = "mail.bigpond.com"; // SMTP server
         $mail->Host = _MAILHOST; // SMTP server
         $mail->FromName = "DTY Support";
         $mail->From = "sales@designstoyou.com.au";
         $mail->AddBCC("c.cao@designstoyou.com.au");
         $mail->AddAddress($email);
         //$mail->AddAddress("sales@designstoyou.com.au");

         $htmlBody = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
</head>
<body><br>
Hi,<br>
<br>

'.$event.'

<br>
<br>
Thanks,
<br><br>
DTY Support<br>
Tel:  +61 3 9753 2555 | Fax:  +61 3 9753 2559
</body>
</html>';

         $mail->Subject = "Re: Return Authority $ra_number - Please respond.";

         $mail->Body = $htmlBody;
         $mail->isHTML(true);
         if(!$mail->Send())
         {
            //echo "1ERROR: " . $mail->ErrorInfo . "<BR>";
            $mail->SmtpClose();
         }
         else
         {
            $mail->SmtpClose();
         }
   }

   function EmailCreditNotification($ra_number)
   {
      $this->LoadWarranty($ra_number);
      $comments = stripslashes($this->reason);
      $contactname = $this->name;
      $returnType = $this->returnlines[0]->return_type;

      $order = $this->order;
      $user_id = $order->user_id;
      $payable = $order->payable;
      $numPays = $order->numpays;
      $paymentOpt = $order->paymentopt;
      $tableinfo = "";

      //if($returnType == _FAULTY || $returnType == _INCORRECTLY_SUPPLIED)
      {
         $mail = new PHPMailer();
         $realm = _REALM;

         $mail->IsSMTP(); // telling the class to use SMTP
         $mail->SMTPKeepAlive = true;

         //$mail->Host = "mail.bigpond.com"; // SMTP server
         $mail->Host = _MAILHOST; // SMTP server
         $mail->FromName = "DTY Support";
         $mail->From = "sales@designstoyou.com.au";
         $mail->AddBCC("c.cao@designstoyou.com.au");

        // $tableinfo = "<table class=\"catable\"><tr><td class=\"catdnb\"><b>RA#$ra_number</b></td><td class=\"catdnb\"><b>Investigate the following garments</b></td></tr>
        // <tr><td class='catdbg'>QTY</td><td class='catdbg'>ITEM</td></tr>";

         $query = "select * from return_charges where warranty_id = $ra_number";
         $res = db_query($query);
         $num = db_numrows($res);
         $totalCharge = 0;

         for($i = 0; $i < $num; $i++)
         {
            $itemOrdered = db_result($res, $i, "item_ordered");
            $itemOrderedVal = db_result($res, $i, "item_ordered_val");
            $replacementItem = db_result($res, $i, "replacement_item");
            $replacementItemVal = db_result($res, $i, "replacement_item_val");
            $chargeAmt = db_result($res, $i, "charge_amount");
            $totalCharge += $chargeAmt;

            //$tableinfo .= "<tr><td class=\"catd\">$w_qty</td><td class=\"catd\">$w_myob_code-$w_size</td></tr>";
         }

        // $tableinfo .="</table>";

         $paid = $this->order->payable;
         $paymentOpt = $this->order->paymentopt;

         $remainingPaid = bcsub($paid, $totalCharge, 2);

         if($remainingPaid > $totalCharge)
            $amountToCreditCharge = bcsub($remainingPaid, $totalCharge, 2);
         else
            $totalCharge = $remainingPaid; // just credit the remaining amount on paid

         $htmlBody = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
               <style type="text/css">
               <!--
                  table.catable
                  {
                     width:500px;
                     border-collapse: collapse;
                     border: 1px solid #03476F;
                     font: arial;
                     color: #363636;
                  }
                  table.catabledetails
                  {
                     width:100%;
                     border-collapse: collapse;
                     border: 1px solid #03476F;
                     font: arial;
                     color: #363636;
                  }
                  td.catdlg
                  {
                    border: 1px solid #03476F;
                    padding: .4em;
                    font: arial;
                    font-weight:bold;
                    font-size: 14;
                    color: #363636;

                  }
                  td.catdnb
                  {
                    #border: 1px solid #03476F;
                    padding: .4em;
                    font: arial;
                    #font-weight:bold;
                    font-size: 12;
                    color: #363636;
                  }
                  td.catd
                  {
                    border: 1px solid #03476F;
                    padding: .4em;
                    font: arial;
                    #font-weight:bold;
                    font-size: 12;
                    color: #363636;
                  }
                  td.catdbg
                  {
                    border: 1px solid #03476F;
                    padding: .4em;
                    font: arial;
                    font-weight:bold;
                    font-size: 12;
                    color: #363636;
                    background: #e0e6e6

                  }
                  tr.catrbg
                  {
                    #border: 1px solid #03476F;
                    #padding: .4em;
                    #font: arial;
                    #font-size: 12;
                    background: #e0e6e6
                  }
               -->
               </style>
            </head>
            <body><br>
Hi,<br>
<br>
This is an automatic notification to let you know that <b>'.$contactname.' (Employee #: '.$user_id.')</b> wage deduction needs to be adjusted/cancelled for order <b>'.$this->order_id.'.</b>
The new amount should be <b>$'.formatNumber($remainingPaid).'</b> over <b>'.$numPays.'</b> pays.';

$htmlBody.= '
<br>
'.$tableinfo.'
<br>


            <br>
            Thanks,
            <br><br>
            DTY Support<br>
            </body>
            </html>';


         $mail->Subject = "WAGE DEDUCTION ADJUSTMENT: $returnType (RA# $ra_number) - " . _CLIENT_ALT;

         $mail->Body = $htmlBody;
         $mail->isHTML(true);
         if(!$mail->Send())
         {
            //echo "1ERROR: " . $mail->ErrorInfo . "<BR>";
            $mail->SmtpClose();
         }
         else
         {
            $mail->SmtpClose();
         }
      }
         return true;
   }

   function EmailConformance($ra_number)
   {
      $this->LoadWarranty($ra_number);
      $comments = stripslashes($this->reason);
      $contactname = $this->name;
      $returnType = $this->returnlines[0]->return_type;

      if($returnType == _FAULTY || $returnType == _INCORRECTLY_SUPPLIED)
      {
         $email = "e.balaz@designstoyou.com.au";
      $completedby = strtotime("+14 day");
      $completedby = date('Y-m-d', $completedby);


        $mail = new PHPMailer();
         $realm = _REALM;

         $mail->IsSMTP(); // telling the class to use SMTP
         $mail->SMTPKeepAlive = true;

         //$mail->Host = "mail.bigpond.com"; // SMTP server
         $mail->Host = _MAILHOST; // SMTP server
         $mail->FromName = "DTY Support";
         $mail->From = "donotreply@designstoyou.com.au";
         $mail->AddBCC("c.cao@designstoyou.com.au");

         if(_ENV != "DEV")
         {
            $mail->AddAddress("e.balaz@designstoyou.com.au");
            $mail->AddAddress("r.le@designstoyou.com.au");
         }

         $tableinfo = "<table class=\"catable\"><tr><td class=\"catdnb\"><b>RA#$ra_number</b></td><td class=\"catdnb\"><b>Investigate the following garments</b></td></tr>
         <tr><td class='catdbg'>QTY</td><td class='catdbg'>ITEM</td></tr>";

         for($i = 0; $i < count($this->returnlines); $i++)
         {
            $rl = $this->returnlines[$i];
            $w_prod_id = $rl->prod_id;
            $w_myob_code = $rl->myob_code;
            $w_size = $rl->size;
            $w_qty = $rl->qty;

            $tableinfo .= "<tr><td class=\"catd\">$w_qty</td><td class=\"catd\">$w_myob_code-$w_size</td></tr>";
         }
         $tableinfo .="</table>";

         $this->LoadWarranty($ra_number);
         $comments = stripslashes($this->reason);
         $contactname = $this->name;

         $tableinfo .= "<tr><td class=\"catdnb\"><b>Returns Type</b></td><td class=\"catdnb\">$returnType</tr>";
         $tableinfo .= "<tr><td class=\"catdnb\"><b>Comments</b></td><td class=\"catdnb\">$comments</tr>";
         $tableinfo .="</table>";

         $tableinfo .= "<br/><br/><table class=\"catable\"><tr><td class='catdbg'>EVENT LOG</td></tr>";
         $eventQuery = "select * from eventlog where warranty_id = $ra_number";
         $eventRes = db_query($eventQuery);
         $eventNum = db_numrows($eventRes);

         for($i = 0; $i < $eventNum; $i++)
         {
            $event = db_result($eventRes, $i, "message");
            $tableinfo .= "<tr><td>$event</td></tr>";
         }
         $tableinfo .="</table>";

         $htmlBody = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
               <style type="text/css">
               <!--
                  table.catable
                  {
                     width:500px;
                     border-collapse: collapse;
                     border: 1px solid #03476F;
                     font: arial;
                     color: #363636;
                  }
                  table.catabledetails
                  {
                     width:100%;
                     border-collapse: collapse;
                     border: 1px solid #03476F;
                     font: arial;
                     color: #363636;
                  }
                  td.catdlg
                  {
                    border: 1px solid #03476F;
                    padding: .4em;
                    font: arial;
                    font-weight:bold;
                    font-size: 14;
                    color: #363636;

                  }
                  td.catdnb
                  {
                    #border: 1px solid #03476F;
                    padding: .4em;
                    font: arial;
                    #font-weight:bold;
                    font-size: 12;
                    color: #363636;
                  }
                  td.catd
                  {
                    border: 1px solid #03476F;
                    padding: .4em;
                    font: arial;
                    #font-weight:bold;
                    font-size: 12;
                    color: #363636;
                  }
                  td.catdbg
                  {
                    border: 1px solid #03476F;
                    padding: .4em;
                    font: arial;
                    font-weight:bold;
                    font-size: 12;
                    color: #363636;
                    background: #e0e6e6

                  }
                  tr.catrbg
                  {
                    #border: 1px solid #03476F;
                    #padding: .4em;
                    #font: arial;
                    #font-size: 12;
                    background: #e0e6e6
                  }
               -->
               </style>
            </head>
            <body><br>
Hi,<br>
<br>
This is an automatic notification to let you know that a NON CONFORMANCE return ('.$returnType.' garment(s)) has been submitted.

The following actions are required to be completed by: '.$completedby.'

<ul>
   <li>Investigate and report on the root cause of the issue</li>
   <li>Draft a preventative action plan</li>
</ul>';

$htmlBody.= '
<br>
'.$tableinfo.'
<br>


            <br>
            Thanks,
            <br><br>
            DTY Management<br>
            </body>
            </html>';


         $mail->Subject = "NON CONFORMANCE: $returnType (RA# $ra_number) - " . _CLIENT_ALT;

         $mail->Body = $htmlBody;
         $mail->isHTML(true);
         if(!$mail->Send())
         {
            //echo "1ERROR: " . $mail->ErrorInfo . "<BR>";
            $mail->SmtpClose();
         }
         else
         {
            $mail->SmtpClose();
         }
      }
         return true;
   }

   function multiReturns($ra_number)
   {
   	$query = "select * from warranty where warranty_id = $ra_number";
   	$res = db_query($query);
   	$num = db_numrows($res);
   	$order_id = db_result($res, 0, "order_id");

	  	$query = "select * from warranty where order_id = $order_id";
   	$res = db_query($query);
   	$num = db_numrows($res);
   	
   	if($num > 1) //more than 1 return
   		return true;
   	else 
   		return false;
   }
   
   function EmailNotification($email, $ra_number)
   {
         $mail = new PHPMailer();
         $realm = _REALM;

         $mail->IsSMTP(); // telling the class to use SMTP
         $mail->SMTPKeepAlive = true;

         //$mail->Host = "mail.bigpond.com"; // SMTP server
         $mail->Host = _MAILHOST; // SMTP server
         $mail->FromName = "DTY Support";
         $mail->From = "donotreply@designstoyou.com.au";
         $mail->AddBCC("c.cao@designstoyou.com.au");
         $mail->AddAddress($email);
         //$mail->AddAddress("sales@designstoyou.com.au");
//         $mail->AddAddress("ccao@coacsys.com.au");

         $tableinfo = "<table class=\"catable\"><tr><td class=\"catdnb\"><b>RA#$ra_number</b></td><td class=\"catdnb\"><b>Please send the following garments back to Designs To You</b></td></tr>
         <tr><td class='catdbg'>QTY</td><td class='catdbg'>ITEM</td></tr>";

         for($i = 0; $i < count($this->returnlines); $i++)
         {
            $rl = $this->returnlines[$i];
            $w_prod_id = $rl->prod_id;
            $w_myob_code = $rl->myob_code;
            $w_size = $rl->size;
            $w_qty = $rl->qty;
				$returnType = $rl->return_type;
            $tableinfo .= "<tr><td class=\"catd\">$w_qty</td><td class=\"catd\">$w_myob_code-$w_size</td></tr>";
         }
         $tableinfo .="</table>";

         $this->LoadWarranty($ra_number);
         $comments = stripslashes($this->reason);
         $contactname = $this->name;
       

         $tableinfo .= "<tr><td class=\"catdnb\"><b>Returns Type</b></td><td class=\"catdnb\">$returnType</tr>";
         $tableinfo .= "<tr><td class=\"catdnb\"><b>Comments</b></td><td class=\"catdnb\">$comments</tr>";
         $tableinfo .="</table>";

         $tableinfo .= "<br/><br/><table class=\"catable\"><tr><td class='catdbg'>EVENT LOG</td></tr>";
         $eventQuery = "select * from eventlog where warranty_id = $ra_number";
         $eventRes = db_query($eventQuery);
         $eventNum = db_numrows($eventRes);

         for($i = 0; $i < $eventNum; $i++)
         {
            $event = db_result($eventRes, $i, "message");
            $tableinfo .= "<tr><td>$event</td></tr>";
         }
         $tableinfo .="</table>";

         if(_FREE_RETURNS == false)
         {
            if($returnType == _INCORRECTLY_SUPPLIED || $returnType == _FAULTY)
            {
               $returnMsg = "EPARCEL";
               //create the consignment here!!
               $con = new consignment($realm, $ra_number);

               if(!$con->consignmentNumber)
                  return false;
            }
            else
            {
               $returnMsg = "<b>Please note that returning items will be at your expense.</b><br/><b>Please return the items to:<br/>Designs To You<br/>31 Enterprise Drive</br>Rowville VIC 3178</br></b>";
            }
         }
         else
         {
            $returnMsg = "EPARCEL";
            $con = new consignment($realm, $ra_number);
            if(!$con->consignmentNumber)
               return false;
         }
         $consignmentNo = $con->consignmentNumber;
         $barcode = $con->barcode;

         $this->consignmentno = $consignmentNo;
         $this->con_barcode = $barcode;

         $conLink = "https://www.designstoyou.com.au/consignment/consignment.php?bc=$barcode&ra=$ra_number&cust=$realm";
         $austpost = "http://auspost.com.au/track/track.html?id=$consignmentNo&type=consignment";
        if($consignmentNo)
         {
            $query = "INSERT INTO `eventlog` (`warranty_id`, `message`, `updateby` ) VALUES
                        ($ra_number, 'To reprint your delivery label, click <a href=\"$conLink\" target=\"_blank\">here</a>', 'DTYLink'),
                        ($ra_number, 'To track your parcel once you have sent it, click <a href=\"$austpost\" target=\"_blank\">here</a>', 'DTYLink')";
            $res = db_query($query);
         }



         $htmlBody = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
               <style type="text/css">
               <!--
                  table.catable
                  {
                     width:500px;
                     border-collapse: collapse;
                     border: 1px solid #03476F;
                     font: arial;
                     color: #363636;
                  }
                  table.catabledetails
                  {
                     width:100%;
                     border-collapse: collapse;
                     border: 1px solid #03476F;
                     font: arial;
                     color: #363636;
                  }
                  td.catdlg
                  {
                    border: 1px solid #03476F;
                    padding: .4em;
                    font: arial;
                    font-weight:bold;
                    font-size: 14;
                    color: #363636;

                  }
                  td.catdnb
                  {
                    #border: 1px solid #03476F;
                    padding: .4em;
                    font: arial;
                    #font-weight:bold;
                    font-size: 12;
                    color: #363636;
                  }
                  td.catd
                  {
                    border: 1px solid #03476F;
                    padding: .4em;
                    font: arial;
                    #font-weight:bold;
                    font-size: 12;
                    color: #363636;
                  }
                  td.catdbg
                  {
                    border: 1px solid #03476F;
                    padding: .4em;
                    font: arial;
                    font-weight:bold;
                    font-size: 12;
                    color: #363636;
                    background: #e0e6e6

                  }
                  tr.catrbg
                  {
                    #border: 1px solid #03476F;
                    #padding: .4em;
                    #font: arial;
                    #font-size: 12;
                    background: #e0e6e6
                  }
               -->
               </style>
            </head>
            <body><br>
Hi '.$contactname.',<br>
<br>
This is an automatic notification to let you know that your online warranty request to Return/Replace garment(s) has been Approved.

<br/><br/><b>IMPORTANT! OUR RETURNS PROCESS HAS CHANGED</b><br/>

<br/>To better service the needs of our customers and improve the turnaround times of returns, we have enabled the tracking of parcels that are being returned to us.<br/>
<br/>Please read and follow these instructions carefully to ensure that your returns are handled in a timely manner.<br/>

<ol>
   <li>Items must be returned to us in their <b>original condition</b> and <b>original or equivalent packaging</b>, otherwise your returns will not be accepted.</li>
   <li>Please send the garment(s) back with a <b>copy of this email</b>.</li>';
   if($returnMsg == "EPARCEL")
   {
      $htmlBody.= '
      <li>Please click <b>"PRINT DELIVERY LABEL"</b> below to print a delivery label.</li>
      <li>Stick the printed label onto the box with the goods you are sending back to us.</li>
      <li>Please read and follow the instructions on the label print out.</li>';
   }
   else
   {
      $htmlBody.= "<li>$returnMsg</li>";
   }
   $htmlBody.= '
   <li>Returned items must reach us within 14 days of receipt of this email/approval.</li>
   <li>Your parcel is your responsibility until it reaches us.</li>
   <li>All credits/replacement will be processed within 30 working days of us receiving the item.</li>
   <li>Our reply paid number is no longer valid, please <u><b>DO NOT</b></u> use our reply paid number as the goods may not get to us.</li>
</ol>';

   if($returnMsg == "EPARCEL")
   {
      $htmlBody.= '<h3> <a href="'.$conLink.'">PRINT DELIVERY LABEL </a></h3>';
   }
   else
   {
      $htmlBody.= "<h3>$returnMsg</h3>";
   }

$htmlBody.= '
<br>
'.$tableinfo.'
<br>


            <br>
            Thanks,
            <br><br>
            DTY Support<br>
            Tel:  +61 3 9753 2555 | Fax:  +61 3 9753 2559
            </body>
            </html>';


         $mail->Subject = "Instructions for garments being Returned ($contactname RA# $ra_number) - " . _CLIENT_ALT;

         $mail->Body = $htmlBody;
         $mail->isHTML(true);
         if(!$mail->Send())
         {
            //echo "1ERROR: " . $mail->ErrorInfo . "<BR>";
            $mail->SmtpClose();
         }
         else
         {
            $mail->SmtpClose();
         }
         return true;
   }


   function SetValues($fields)
   {
      $this->order_id = $fields{_ORDER_ID};
      $this->claim_date = $fields{_CLAIM_DATE};
      $this->phone = $fields{_PHONE};
      $this->name = $fields{_NAME};
      $this->email = $fields{_EMAIL};
      $this->status = $fields{_STATUS};
      $this->jurisdiction = $fields{_JURISDICTION};
      $this->numrowsadded = $fields{"numrowsadded"};

      if(!$this->status)
      {
         $status = _checkIsSet(_STATUS);
         if($status)
            $this->status = $status;
         else
            $this->status = _PENDING;
      }
      $this->return_type = $fields{_RETURN_TYPE};
      $this->reason = addslashes($fields{_REASON});
   }

   function LoadWarranty($id)
   {
      $query = "select * from warranty where warranty_id = $id";
      $all_fields = array(_ORDER_ID, _CLAIM_DATE, _PHONE, _NAME, _REASON, _STATUS, _RETURN_TYPE, _EMAIL, "numrowsadded", _JURISDICTION);
      $valuesArr = GenericLoadRecord($query, $all_fields);

      $this->SetValues($valuesArr);
      $this->warranty_id = $id;
      $this->action_type = _UPDATE;
      $this->eventlog = array();
      $this->returnlines = array();
      $this->LoadEventLog($id);
      $this->LoadReturns($id);
      $this->order->LoadOrderId($this->order_id);
   }

   function LoadReturns($id)
   {
      $query = "select * from returns where warranty_id = $id";
      $res = db_query($query);
      $num = db_numrows($res);

      $this->returnlines = array(); //reset the array so we dont keep adding to it!!!
      for($i = 0; $i < $num; $i++)
      {
         $line_id = db_result($res, $i, 'line_id');
         $prod_id = db_result($res, $i, 'prod_id');
         $myob_code = db_result($res, $i, 'myob_code');
         $size = db_result($res, $i, 'size');
         $qty = db_result($res, $i, 'qty');

         $rprod_id = db_result($res, $i, 'r_prod_id');
         $rmyob_code = db_result($res, $i, 'r_myob_code');
         $rqty = db_result($res, $i, 'r_qty');
         $rsize = db_result($res, $i, 'r_size');

         $rcv = db_result($res, $i, 'rcv');
         $rjt = db_result($res, $i, 'rjt');
         $return_type = db_result($res, $i, 'return_type');

         $rl = new returnLines();
         $rl->prod_id = $prod_id;
         $rl->myob_code = $myob_code;
         $rl->qty = $qty;
         $rl->size = $size;
         $rl->line_id = $line_id;
         $rl->rprod_id = $rprod_id;
         $rl->rmyob_code = $rmyob_code;
         $rl->rqty = $rqty;
         $rl->rsize = $rsize;
         $rl->rcv = $rcv;
         $rl->rjt = $rjt;
         $rl->return_type = $return_type;

         array_push($this->returnlines, $rl);
      }

//      `warranty_id`, `line_id`, `prod_id`, `myob_code`, `size`, `qty`, `r_prod_id`, `r_myob_code`, `r_size`, `r_qty`, `rcv`, `rjt`
   }

   function LoadEventLog($id)
   {
      $query = "select * from eventlog where warranty_id = $id order by lastupdated asc";
      $res = db_query($query);
      $num = db_numrows($res);

      for($i = 0; $i < $num; $i++)
      {
         $eid = db_result($res, $i, "eventlog_id");
         $message = db_result($res, $i, "message");
         $lastupdated = db_result($res, $i, "lastupdated");
         $updateby = db_result($res, $i, "updateby");
         $warranty_id = db_result($res, $i, "warranty_id");
         $event = new events();
         $event->event_id = $eid;
         $event->message = $message;
         $event->lastupdated = $lastupdated;
         $event->updateby = $updateby;
         $event->warranty_id = $warranty_id;
         array_push($this->eventlog, $event);
      }
   }

   function updateStatus()
   {
      $status = _APPROVED;//_checkIsSet(_STATUS);
      $removeArr = _checkIsSet("itemArr");
      $num = count($removeArr);

      for($i = 0; $i < $num; $i++)
      {
         $warranty_id = $removeArr[$i];
         $query = "update warranty set status = '$status' where warranty_id = $warranty_id";
         db_query($query);
         //add in the event
         $query = "INSERT INTO `eventlog` (`warranty_id`, `message`, `updateby` ) VALUES  ($warranty_id, 'Status changed to: $status', 'DTYLink')";
         $res = db_query($query);
         //send the email!
         if($status == _APPROVED)
         {
            $query = "select * from warranty where warranty_id = $warranty_id";
            $res = db_query($query);
            $email = db_result($res, 0, _EMAIL);
            $this->LoadReturns($warranty_id);
            $this->LoadWarranty($warranty_id);
            //$this->EmailNotification($email, $warranty_id);

            if(!$this->EmailNotification($email, $warranty_id))
               return false;

            $barcode = $this->con_barcode;
            $consignmentno = $this->consignmentno;
            $realm = _REALM;

            $conLink = "https://www.designstoyou.com.au/consignment/consignment.php?bc=$barcode&ra=$warranty_id&cust=$realm";
            $austpost = "http://auspost.com.au/track/track.html?id=$consignmentno&type=consignment";
//MOVE THIS TO EMAIL NOTIFICATION
//               $query = "INSERT INTO `eventlog` (`warranty_id`, `message`, `updateby` ) VALUES
//                        ($warranty_id, 'To reprint your delivery label, click <a href=\"$conLink\" target=\"_blank\">here</a>', 'DTYLink'),
//                        ($warranty_id, 'To track your parcel once you have sent it, click <a href=\"$austpost\" target=\"_blank\">here</a>', 'DTYLink')";
//            $res = db_query($query);

         }
      }
      if($i == $num)
         return true;
      else
         return false;
   }

   function getFieldFromOrder($order_id, $field)
   {
      $query = "select $field from orders where order_id = $order_id";
//      echo "$query<BR>";
      $res = db_query($query);
      $state = db_result($res, 0, $field);
      return strtoupper($state);
   }

   function printEventLog()
   {
      $num = count($this->eventlog);

      for($i = 0; $i < $num; $i++)
      {
         $msg = $this->eventlog[$i]->message;
         $date = $this->eventlog[$i]->lastupdated;
         $formattedDate = date("D j M Y, g:i a", strtotime($date));
         $author = $this->eventlog[$i]->updateby;
         $eid = $this->eventlog[$i]->event_id;
         echo "
               <tr>
                  <td align=\"left\"><input id=\"checkbox\" type=\"checkbox\" name=\"registerBox[]\" value=\"".$eid."\"></td>
                  <td>$formattedDate</td><td>$msg</td><td>$author</td>
               </tr>";
      }

   }

   function saveWarranty()
   {
      //may need to add in access control
      //check status
      if(user_getid() != "1")
      {
         if($this->status != _PENDING)
         {
            $_SESSION['msg'] = 'Only returns in the pending state can be changed.';
            return false;
         }
      }

      $oldstatus = $this->status;
      $oldreturn = $this->return_type;
      $oldjurisdiction = $this->jurisdiction;
      $req_addressfields = array(_ORDER_ID, _CLAIM_DATE, _PHONE, _NAME, _EMAIL);
      $return_addressfields = GenericCheckAndSync($req_addressfields, true);

      $this->SetValues($return_addressfields);

      $return_size = count($return_addressfields);

      if($return_size != count($req_addressfields))
         return false;

      $numrowsadded = _checkIsSet("numRowsAdded");

      //check for comments
      $reason = _checkIsSet(_REASON);
      $return_addressfields{_REASON} = $reason;
      array_push($req_addressfields, _REASON);
      /*
      $return_addressfields{_LASTUPDATED} = date("Y-m-d H:i:s");
      array_push($req_addressfields, _LASTUPDATED);
      */
      db_query(_BEGIN);
      if($this->action_type == _SAVE)
      {
         $return_addressfields{_STATUS} = _PENDING;
         array_push($req_addressfields, _STATUS);

         //save the numrowsadded into the warranty table
         $return_addressfields{"numrowsadded"} = $numrowsadded;
         array_push($req_addressfields, "numrowsadded");
      }
      else
      {
         $newstatus = _checkIsSet(_STATUS);
//         echo "status: $newstatus<BR>";
         $return_addressfields{_STATUS} = $newstatus;
         array_push($req_addressfields, _STATUS);

         //save the numrowsadded into the warranty table
         $return_addressfields{"numrowsadded"} = $numrowsadded;
         array_push($req_addressfields, "numrowsadded");

         if($oldstatus != $newstatus)
         {
            $msg = "Status changed to: $newstatus";
            $author = "DTYLink";
            $warranty_id = $this->warranty_id;
            $query = "INSERT INTO `eventlog` (`warranty_id`, `message`, `updateby` ) VALUES  ($warranty_id, '$msg', 'DTYLink')";
            $res = db_query($query);

            //send the email!
            if($newstatus == _APPROVED)
            {
               $warquery = "select * from warranty where warranty_id = $warranty_id";
               $warres = db_query($warquery);
               $waremail = db_result($warres, 0, _EMAIL);
               if(!$this->EmailNotification($waremail, $warranty_id))
                  return false;

               $barcode = $this->con_barcode;
               $consignmentno = $this->consignmentno;
               $realm = _REALM;

               $conLink = "https://www.designstoyou.com.au/consignment/consignment.php?bc=$barcode&ra=$warranty_id&cust=$realm";
               $austpost = "http://auspost.com.au/track/track.html?id=$consignmentno&type=consignment";
//MOVE THIS TO EMAIL NOTIFICATION
//               $query = "INSERT INTO `eventlog` (`warranty_id`, `message`, `updateby` ) VALUES
//                        ($warranty_id, 'To reprint your delivery label, click <a href=\"$conLink\" target=\"_blank\">here</a>', 'DTYLink'),
//                        ($warranty_id, 'To track your parcel once you have sent it, click <a href=\"$austpost\" target=\"_blank\">here</a>', 'DTYLink')";
//               $res = db_query($query);
            }
         }

         $newreturn = $return_addressfields{_RETURN_TYPE};
         if($oldreturn != $newreturn)
         {
            $msg = "Return type changed to: $newreturn";
            $author = "DTYLink";
            $warranty_id = $this->warranty_id;
            $query = "INSERT INTO `eventlog` (`warranty_id`, `message`, `updateby` ) VALUES  ($warranty_id, '$msg', 'Customer')";
            $res = db_query($query);


         }
      }

      $return_addressfields{_JURISDICTION} = $oldjurisdiction;
      array_push($req_addressfields, _JURISDICTION);

      $warranty_id = "";
      $insert_status = false;
      if($this->action_type == _SAVE)
      {
         $warranty_id = GenericSaveUpdate("warranty", $req_addressfields, $return_addressfields, _SAVE, "", "");
         if($warranty_id)
         {
            $insert_status = true;
         }
      }
      else
      {
         $warranty_id = $this->warranty_id;
         $insert_status = GenericSaveUpdate("warranty", $req_addressfields, $return_addressfields, _UPDATE, _WARRANTY_ID, $warranty_id);
      }

      
      if($warranty_id)
      {
         //email conformance;
       //  $this->EmailConformance($warranty_id);

         if($this->action_type == _SAVE)
         {
            //add in the event
            $query = "INSERT INTO `eventlog` (`warranty_id`, `message`, `updateby` ) VALUES  ($warranty_id, 'Customer submitted request for Return Authorisation', 'CUSTOMER')";
            $res = db_query($query);
            //save the events!
            $this->saveEvent($warranty_id, '',$this->email);
            //del any events
            $this->deleteEvent();

            /**** SAVE INSERT LINE ITEMS!! ****/

            if(!$this->saveReturnLines2($warranty_id, $this->order_id, $numrowsadded))
            {
               db_query(_ROLLBACK);
               $_SESSION['msg'] = "Please click on Add Item and select the garments you would like to exchange.";
               return false;
            }

            /**********************************/

            if($res)
               $_SESSION['msg'] = "Warranty details saved.";
            else
            {
               db_query(_ROLLBACK);
               $_SESSION['msg'] = "722 - An error occurred while trying to save.";
               return false;
            }
         }
         else
         {
            //save the events!
            $this->saveEvent($warranty_id, '',$this->email);
            $this->deleteEvent();
            $_SESSION['msg'] = "Warranty details updated.";
            /**** SAVE INSERT LINE ITEMS!! ****/

            if(!$this->saveReturnLines2($warranty_id, $this->order_id, $numrowsadded))
            {
               db_query(_ROLLBACK);
               $_SESSION['msg'] = "723 - An error occurred while trying to save.";
               return false;
            }

            /**********************************/
         }

         //test only
//         $_SESSION['msg'] = "Test error msg";
//         db_query(_ROLLBACK);
//         return false;
         db_query(_COMMIT);
         return true;
      }
      else
      {
         db_query(_ROLLBACK);
         $_SESSION['msg'] = "An error occurred while trying to save.";
         return false;
      }
   }


   function saveReturnLines2($warranty_id, $order_id, $numrowsadded)
   {
      $query = "INSERT INTO `returns` (`warranty_id`, `line_id`, `prod_id`, `myob_code`, `size`, `qty`, `r_prod_id`, `r_myob_code`, `r_size`, `r_qty`, `rcv`, `rjt`, `return_type` ) VALUES ";

      //delete all the old return lineitems first;
      if($this->action_type == _UPDATE)
      {
         $delquery = "delete from returns where warranty_id = $warranty_id";
         $delres = db_query($delquery);
         if(!$delres)
            return false;
      }

      //get the lineitems of the order
      $arr = json_decode($this->FindLineitems($order_id));
      $user_id = $this->order->user_id;
      $numReturns = 0;
      $rjtEvent = "";
      $rcvEvent = "";
      $sendNotification = false;

      for($i = 0; $i < count($arr); $i++)
      {
         $jli = $arr[$i];
         $line_id = $jli->lineitem_id;
         $prod_id = $jli->prod_id;
         $myob_code = $jli->myob_code;
         $sze = $jli->size;
         $qty = $jli->qty;

         $prod_size_id = $prod_id."_".str_replace ("/", "_", $sze);

         $rProdName = "rproducts_$prod_size_id";
         $rSizeName = "rsizes_$prod_size_id";
         $rQtyName = "rqty_$prod_size_id";
         $rRcvName = "rcv_$prod_size_id";
         $rRjtName = "rjt_$prod_size_id";
         $returnTypeName = "returntype_$prod_size_id";

         //check the qty returned for a value, then save if its non zero!
         $rQty = _checkIsSet($rQtyName);

         $rProdId = _checkIsSet($rProdName);
         $rSize = _checkIsSet($rSizeName);
         $returnType = _checkIsSet($returnTypeName);

            //get myob code of ordered item
            $itemOrdered = $this->getMyobCode($prod_id);
            $replacementItem = $this->getMyobCode($rProdId);
            $valueOfItemOrdered = $this->getItemValue($prod_id);
            $valueOfReplacement = $this->getItemValue($rProdId);
            $replacementItemSize = "$replacementItem-$rSize";
            $orderedItemSize = "$itemOrdered-$sze";
            $amtToCredit = 0; //reset credit/charge amt

            //echo "ITEM ORDERED VAL: $valueOfItemOrdered REPLACEMENT VALUE: $valueOfReplacement<BR>";


            //now check to see if we have rcv or rjt any of the qty;
            $rcvQty = _checkIsSet($rRcvName);
            $rjtQty = _checkIsSet($rRjtName);

            //find existing rcv, rjt qty
            $findArr = explode(",", $this->findRcvRjt($prod_size_id));
            $fRcv = $findArr[0];
            $fRjt = $findArr[1];


            if(!$rcvQty)
            {
               //check weather it was initally a 1 and changed to a zero
               $rcvQty = 0;
               $qtyToCredit = 0;
               if($rcvQty != $fRcv)
               {
                  $qtyToCredit = $rcvQty - $fRcv;

                  //cancellation of recv item, need to start deducting wages again.
                  $amtToCredit = $this->amountToCreditOrCharge($orderedItemSize, $replacementItemSize, $qtyToCredit, $valueOfItemOrdered, $valueOfReplacement);
                  //echo "CANCELLATION OF AMOUNT TO CREDIT = $amtToCredit QTY TO CREDIT: $qtyToCredit<BR>";

                  if($amtToCredit > 0)
                     $creditMsg = "$amtToCredit was credited.";
                   else
                      $creditMsg = "$amtToCredit was charged.";

                  $rcvEvent .= $qtyToCredit . " x $myob_code-$sze was received.<BR>";
               }
            }
            else
            {
               if($rcvQty != $fRcv)
               {
                  $qtyToCredit = 0;
                  $qtyToCredit = $rcvQty - $fRcv;

                  //credit or pay extra for item!
                  $amtToCredit = $this->amountToCreditOrCharge($orderedItemSize, $replacementItemSize, $qtyToCredit, $valueOfItemOrdered, $valueOfReplacement);
                  //echo "AMOUNT TO CREDIT = $amtToCredit QTY TO CREDIT: $qtyToCredit<BR>";

                  $rcvEvent .= $rcvQty - $fRcv . " x $myob_code-$sze was received.<BR>";

               }
            }

            if($amtToCredit != 0)
            {
               //save the amtToCredit; then calc the refund/charge
               $return_charges = new return_charges();
               //we want the existing return amount, if the total is still less than what has
               //been paid plus the new then we'll need to refund them full amount, else just refund
               //the difference as the remainder will be part of the allowance.
               $totalAmtReturnedBeforeNewItem = $return_charges->getTotalReturnCharges($warranty_id);
               $return_charges->saveReturnCharges($warranty_id, $order_id, $user_id, $orderedItemSize, $valueOfItemOrdered, $replacementItemSize, $valueOfReplacement, $amtToCredit, $qtyToCredit);

               $paid = $this->order->payable;
               $paymentOpt = $this->order->paymentopt;

               $remainingPaid = bcsub($paid, $totalAmtReturnedBeforeNewItem,2);

               if($remainingPaid > $amtToCredit)
                  $amountToCreditCharge = bcsub($remainingPaid, $amtToCredit,2);
               else
                  $amtToCredit = $remainingPaid; // just credit the remaining amount on paid


               if($amtToCredit > 0)
                  $creditMsg = "\$$amtToCredit was credited.";
               else
               {
                  $tmpCredit = $amtToCredit * -1;
                  $creditMsg = "\$$tmpCredit was charged.";
               }

               $rcvEvent .= " $creditMsg";
       
               $sendNotification = true;
               //
               //echo "CHARGE/REFUND AMT IS: $amtToCredit<BR>";

            }


            if(!$rjtQty)
            {
               //check weather it was initally a 1 and changed to a zero
               $rjtQty = 0;
               if($rjtQty != $fRjt)
               {
                  $rjtEvent .= $rjtQty - $fRjt." x $myob_code-$sze was rejected.<BR>";
               }
            }
            else
            {
               if($rjtQty != $fRjt)
               {
                  $rjtEvent .= $rjtQty - $fRjt." x $myob_code-$sze was rejected.<BR>";
               }
            }


         if(strlen($rQty) > 0 && $rQty > 0)
         {

               if($rProdId == "na")
               {
                  $rProdId = 0;
                  $replacementItem = "na";

               }
               $returnSize = _checkIsSet($rSize);
               $query .= "($warranty_id, '$prod_size_id', $prod_id, '$itemOrdered', '$sze', $rQty,  $rProdId, '$replacementItem','$rSize', $rQty,$rcvQty, $rjtQty, '$returnType'),";
         }

//         else
//         {
//            if($prod_id)
//               $query .= "($warranty_id, $line_id, $prod_id, '$myob_code', '$sze', $qty,  0, 'na','', 0,$rcvQty, $rjtQty, '$returnType'),";
//         }

      }

      //save event
      if(strlen($rcvEvent) > 1)
      {
         $this->saveEvent($warranty_id, $rcvEvent, '');
      }
      if(strlen($rjtEvent) > 1)
      {
         $this->saveEvent($warranty_id, $rjtEvent, '');
      }

      if($sendNotification && $this->order->isinvoiced == "Y")
         $this->EmailCreditNotification($warranty_id);
     // echo "TOTAL CREDIT/CHARGE : " . $return_charges->getTotalReturnCharges($warranty_id) . "<BR>";


//return false;
      //remove the last comma
      $query = substr($query, 0, strlen($query)-1);
      $res = db_query($query);

//echo "$query<BR>";
      if($res)
         return true;
      else
      return false;
   }

   function amountToCreditOrCharge($itemOrdered, $replacementItem, $qtyToCredit, $valueOfItemOrdered, $valueOfReplacement)
   {
      $ordVal = $qtyToCredit * $valueOfItemOrdered;
      $replVal = $qtyToCredit * $valueOfReplacement;
      $amtToCredit = 0;
      $paid = $this->order->payable;
      $paymentOpt = $this->order->paymentopt;

//echo "ordval: $ordVal repl: $replVal alreadyPaid: $paid OPT: $paymentOpt<BR>";

     // if($qtyToCredit > 0)
      {
        // if($ordVal > $replVal)
         {
            $amtToCredit = $ordVal - $replVal;
         }
      }
     // else // -ve = need to pay!
      {

      }


      return $amtToCredit;
   }

   function findRcvRjt($line_id)
   {
      for($i = 0; $i < count($this->returnlines); $i++)
      {
         $rl = $this->returnlines[$i];
         $cur_line_id = $rl->line_id;
         $rcv = $rl->rcv;
         $rjt = $rl->rjt;

         if($cur_line_id == $line_id)
            return "$rcv,$rjt";
      }
      return "0,0";
   }

   function saveReturnLines($warranty_id, $order_id, $numrowsadded)
   {
      $query = "INSERT INTO `returns` (`warranty_id`, `line_id`, `prod_id`, `myob_code`, `size`, `qty`, `r_prod_id`, `r_myob_code`, `r_size`, `r_qty`, `rcv`, `rjt` ) VALUES ";

      //delete all the old return lineitems first;
      if($this->action_type == _UPDATE)
      {
         $delquery = "delete from returns where warranty_id = $warranty_id";
         $delres = db_query($delquery);
         if(!$delres)
            return false;
      }

      $arr = json_decode($this->FindLineitems($order_id));
      $numReturns = 0;
      $rjtEvent = "";
      $rcvEvent = "";

      for($i = 0; $i < count($arr); $i++)
      {
         $jli = $arr[$i];
         $line_id = $jli->lineitem_id;
         $prod_id = $jli->prod_id;
         $myob_code = $jli->myob_code;
         $sze = $jli->size;
         $qty = $jli->qty;

         $rProd = "rproducts$line_id";
         $rSize = "rsizes$line_id";
         $rQty = "rqty$line_id";
         $rRcv = "rcv$line_id";
         $rRjt = "rjt$line_id";

         $returnQty = _checkIsSet($rQty);
         $rcvQty = _checkIsSet($rRcv);
         $rjtQty = _checkIsSet($rRjt);

         $findArr = explode(",", $this->findRcvRjt($line_id));
         $fRcv = $findArr[0];
         $fRjt = $findArr[1];

//echo "rqty: $returnQty rec: $rcvQty $rQty<BR>";

         if(!$rcvQty)
         {
            //check weather it was initally a 1 and changed to a zero
            $rcvQty = 0;
            if($rcvQty != $fRcv)
            {
               $rcvEvent .= $rcvQty - $fRcv . " x $myob_code-$sze was received.<BR>";
            }
         }
         else
         {
            if($rcvQty != $fRcv)
            {
               $rcvEvent .= $rcvQty - $fRcv . " x $myob_code-$sze was received.<BR>";
            }
         }

         if(!$rjtQty)
         {
            //check weather it was initally a 1 and changed to a zero
            $rjtQty = 0;
            if($rjtQty != $fRjt)
            {
               $rjtEvent .= $rjtQty - $fRjt." x $myob_code-$sze was rejected.<BR>";
            }
         }
         else
         {
            if($rjtQty != $fRjt)
            {
               $rjtEvent .= $rjtQty - $fRjt." x $myob_code-$sze was rejected.<BR>";
            }
         }
//echo "RCV: $rcvQty line: $line_id arr: " . $this->findRcv() . "<BR>";
         //if there is something to return, save the lineitems
         if($returnQty)
         {
            $returnProd = _checkIsSet($rProd); // want to exchange for this item
            $returnMyobCode = $this->getMyobCode($returnProd);
            //echo "returning: $returnMyobCode<BR>";
            $returnSize = _checkIsSet($rSize);
            $query .= "($warranty_id, $line_id, $prod_id, '$myob_code', '$sze', $qty,  $returnProd, '$returnMyobCode','$returnSize', $returnQty,$rcvQty, $rjtQty),";
         }
         $numReturns++;
      }
  //echo "$query<BR>";
      for($i = $numrowsadded; $i > 0; $i--)
      {
         $prodId = "products$i";
         $size = "sizes$i";
         $qtyname = "nqty$i";

         $line_id = $i;
         $prod_id = _checkIsSet($prodId);
         $myob_code = $this->getMyobCode($prod_id);
         $sze = _checkIsSet($size);
         $qty = _checkIsSet($qtyname);

         $rProdId = "rproducts$i";
         $rSize = "rsizes$i";
         $rQty = "rqty$i";
         $rRcv = "rcv$i";
         $rRjt = "rjt$i";
//echo "MYOB: $myob_code pid: [$prod_id] prodid [$prodId] rqty[$rQty]<BR>";

         $returnQty = _checkIsSet($rQty);
         $rcvQty = _checkIsSet($rRcv);
         $rjtQty = _checkIsSet($rRjt);

         $findArr = explode(",", $this->findRcvRjt($line_id));
         $fRcv = $findArr[0];
         $fRjt = $findArr[1];

         if(!$rcvQty)
         {
            //check weather it was initally a 1 and changed to a zero
            $rcvQty = 0;
            if($rcvQty != $fRcv)
            {
               $rcvEvent .= $rcvQty - $fRcv . " x $myob_code-$sze was received.<BR>";
            }
         }
         else
         {
            if($rcvQty != $fRcv)
            {
               $rcvEvent .= $rcvQty - $fRcv . " x $myob_code-$sze was received.<BR>";
            }
         }

         if(!$rjtQty)
         {
            //check weather it was initally a 1 and changed to a zero
            $rjtQty = 0;
            if($rjtQty != $fRjt)
            {
               $rjtEvent .= $rjtQty - $fRjt ." x $myob_code-$sze was rejected.<BR>";
            }
         }
         else
         {
            if($rjtQty != $fRjt)
            {
               $rjtEvent .= $rjtQty - $fRjt ." x $myob_code-$sze was rejected.<BR>";
            }
         }

         //if there is something to return, save the lineitems
         if($returnQty)
         {
            //echo "RETURNQTY: $returnQty<BR>";
            $returnProd = _checkIsSet($rProdId); // want to exchange for this item
            $returnMyobCode = $this->getMyobCode($returnProd);
            $returnSize = _checkIsSet($rSize);
            $query .= "($warranty_id, $line_id, $prod_id, '$myob_code', '$sze', $qty,  $returnProd, '$returnMyobCode','$returnSize', $returnQty,$rcvQty, $rjtQty),";
         }
         else
         {
            if($prod_id)
               $query .= "($warranty_id, $line_id, $prod_id, '$myob_code', '$sze', $qty,  0, 'na','', 0,$rcvQty, $rjtQty),";
         }
      }

      //save event
      if(strlen($rcvEvent) > 1)
      {
         $this->saveEvent($warranty_id, $rcvEvent, '');
      }
      if(strlen($rjtEvent) > 1)
      {
         $this->saveEvent($warranty_id, $rjtEvent, '');
      }

      //remove the last comma
      $query = substr($query, 0, strlen($query)-1);
  //echo "$query<BR>";
      $res = db_query($query);
      if($res)
         return true;
      else return false;

      //print_r($arr);
   }

   function deleteEvent()
   {
      $removeArr = _checkIsSet("registerBox");
      $numDel = count($removeArr);
      for($i = 0; $i < $numDel; $i++)
      {
         $event_id = $removeArr[$i];
         $query = "delete from eventlog where eventlog_id = $event_id";
         $res = db_query($query);

      }
   }

   function getMyobCode($prod_id)
   {
      $query = "select * from products where prod_id = $prod_id";
      $res = db_query($query);
      $num = db_numrows($res);

      if($num > 0)
         return db_result($res, 0, 'myob_code');
      else
         return "N/A";
   }

   //get item value. Need to know whether or not to include GST
   function getItemValue($prod_id)
   {
      $query = "select * from products where prod_id = $prod_id";
      $res = db_query($query);
      $num = db_numrows($res);
      $price = 0;

      if($num > 0)
      {
         if($this->isAUS == "Y")
            $price =  db_result($res, 0, 'price') * 1.1; //gst
         else
            $price =  db_result($res, 0, 'price_nz');
      }

      return $price;
   }

   function saveEvent($warranty_id, $eventMsg, $email)
   {
      $msg = _checkIsSet(_MESSAGE);

      if($msg)
      {
         $query = "INSERT INTO `eventlog` (`warranty_id`, `message`, `updateby` ) VALUES  ($warranty_id, '$msg', 'DTYLink')";
         $res = db_query($query);
      }

      if($eventMsg)
      {
         $query = "INSERT INTO `eventlog` (`warranty_id`, `message`, `updateby` ) VALUES  ($warranty_id, '$eventMsg', 'DTYLink')";
         $res = db_query($query);
      }

      $emailEvent = _checkIsSet("emailEvent");
      if($emailEvent)
      {

         //$this->emailEvent($email, $warranty_id, $msg);
      }
   }
}

class return_charges
{
   var $return_charges_id;
   var $warranty_id;
   var $item_ordered;
   var $item_ordered_val;
   var $replacement_item;
   var $replacement_item_val;
   var $charge_type;
   var $charge_amount;

   function return_charges()
   {
      $this->return_charges_id = "";
      $this->warranty_id = "";
      $this->item_ordered = "";
      $this->item_ordered_val = "";
      $this->replacement_item = "";
      $this->replacement_item_val = "";
      $this->charge_type = "";
      $this->charge_amount = "";
   }

   function saveReturnCharges($warranty_id, $order_id, $user_id, $item_ordered, $item_ordered_val, $replacement_item, $replacement_item_val, $charge_amount, $qtyToCredit)
   {
      //+ve amount is a refund amount to the customer, -ve need to charge extra!
      $chargeType = "REFUND";
      if($charge_amount < 0)
         $chargeType = "CHARGE";

      $query = "INSERT INTO  `return_charges` (`warranty_id`, `order_id`, `user_id`, `item_ordered` ,`item_ordered_val` ,`replacement_item` ,`replacement_item_val` ,`charge_amount`, `qty`, `charge_type`)
                VALUES ($warranty_id, $order_id, $user_id, '$item_ordered',  $item_ordered_val,  '$replacement_item', $replacement_item_val, $charge_amount,$qtyToCredit,'$chargeType')";
      //echo "$query<BR>";
      db_query($query);

   }

   function getTotalReturnCharges($warranty_id)
   {
      $query = "select sum(charge_amount) as total from return_charges where warranty_id = $warranty_id";
      $res = db_query($query);
      $num = db_numrows($res);
      $totalCharges = 0;
      if($num > 0)
      {
         $totalCharges = db_result($res, 0, 'total');
      }
      return $totalCharges;
   }

}

class events
{
   var $event_id;
   var $message;
   var $lastupdated;
   var $updateby;

   function events()
   {
      $this->event_id = "";
      $this->message = "";
      $this->lastupdated = "";
      $this->updateby = "";
   }
}

?>
