<?php
$home = dirname(__FILE__) . "/../";
$lib = $home . "/lib";

require_once($home . '/globals.php');
require_once($lib . '/functions.php');
require_once($lib . '/htmlGenerator.php');
require_once($lib . '/phpmailer/class.phpmailer.php');
require_once("staffclass.php");

class request
{
   var $requests_id;
   var $user_id;
   var $location_id;
   var $amount;
   var $comments;
   var $status;
   var $request_date;
   var $approval_time;
   var $approvedby;
   var $lastupdated;
   var $contact_number;
   var $email;
   var $request_type;
   var $action;

   function request()
   {
      $this->requests_id = "";
      $this->user_id = $_SESSION["user_id"];
      $this->location_id = "";
      $this->amount = "0.00";
      $this->comments = "";
      $this->status = _PENDING;
      $this->request_date = date('Y-m-d h:i:s');
      $this->approval_time = "";
      $this->approvedby = "";
      $this->lastupdated =  date('Y-m-d h:i:s');
      $this->contact_number = "";
      $this->email = "";
      $this->request_type = "TOPUP";
      $this->action = "SAVE";
   }

   function emailNotification($fullname, $request_id)
   {
      $htmlBody = '
         <html>
            <head>
               <title>::: DTYLink :::</title>
               <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
               <meta name="Security" content="public" />
               <meta name="Robots" content="noindex,nofollow" />
               <meta name="Description" content="DTYLink order" />
               <meta name="Abstract" content="Designs To You - Online Order Management System" />
               <meta name="Owner" content="ccao@designstoyou.com.au" />
               <meta name="Keywords" content="Designs To You" />

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
            <body>';

      $amount = $this->amount;
      $contact_number = $this->contact_number;
      $comments = $this->comments;

      $htmlBody .= "<b>$fullname</b> has requested a Top Up Amount of <b>\$$amount</b> the request ID is: <b>$request_id</b>.";
      $htmlBody .= "<br/><br/><b>Comments:</b> $comments";

      $mail = new PHPMailer();

      $mail->IsSMTP(); // telling the class to use SMTP
      $mail->SMTPKeepAlive = true;

      $mail->Host = _MAILHOST; // SMTP server
      $mail->FromName = "Designs To You";
      $mail->From = "sales@designstoyou.com.au";

      $email = "c.cao@designstoyou.com.au";

      $mail->AddBCC("c.cao@designstoyou.com.au");
      $mail->AddAddress("hr.vic@reece.com.au");
      if($this->action == _UPDATE)
         $mail->Subject = "DTYLink/Reece Online Request: $request_id - Top Up Notification Updated";
      else
         $mail->Subject = "DTYLink/Reece Online Request: $request_id - Top Up Notification";
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

   function LoadRequestId($id)
   {
      $query = "select * from requests where requests_id = $id";
      $res = db_query($query);
      $num = db_numrows($res);
      if($num > 0)
      {
         $this->requests_id = db_result($res, 0, "requests_id");
         $this->user_id = db_result($res, 0, "user_id");
         $this->amount = db_result($res, 0, "amount");
         $this->comments = db_result($res, 0, "comments");
         $this->status = db_result($res, 0, "status");
         $this->request_date = db_result($res, 0, "request_date");
         $this->approval_time = db_result($res, 0, "approval_time");
         $this->approvedby = db_result($res, 0, "approvedby");
         $this->lastupdated = db_result($res, 0, "lastupdated");
         $this->contact_number = db_result($res, 0, "contact_number");
         $this->email = db_result($res, 0, "email");
         $this->request_type = db_result($res, 0, "request_type");
         $this->action = "UPDATE";
      }
      else
         return false;
   }

   function delete()
   {
      $idArr = _checkIsSet("itemArr");
      $num = count($idArr);
      $numdel = 0;

      for($i = 0; $i < $num; $i++)
      {
         $curId = $idArr[$i];
         $query = "delete from requests where requests_id = $curId";
         if(db_query($query))
            $numdel++;
      }
      if($numdel > 0)
         return true;
      else
         return false;

   }

   function changeStatus($status)
   {
      db_query(_BEGIN);
      if(!minAccessLevel(_BRANCH_LEVEL))
         return false;
      $idArr = _checkIsSet("itemArr");
      $num = count($idArr);
      $numchange = 0;

      for($i = 0; $i < $num; $i++)
      {
         $curId = $idArr[$i];
         $query = "select status from requests where requests_id = $curId";
         $res = db_query($query);
         $qnum = db_numrows($res);
         if($qnum > 0)
         {
            $curstatus = db_result($res, 0, "status");
            if($curstatus != _PENDING)
            {
               $_SESSION['msg'] = "Only PENDING requests may be approved.";
               return false;
            }
            $req = new request();
            $req->LoadRequestId($curId);
            $r_user_id = $req->user_id;
            $r_amount = $req->amount;

            //update/add allowance
            if(!$this->UpdateAllowanceInfo($r_user_id, $r_amount))
            {
               $_SESSION['msg'] = "An error occurred while trying to update allowance. Please try again.";
               return false;
            }
            else
            {
               $approval_date = date('Y-m-d h:i:s');
               $admin_user_id = $_SESSION[_USER_ID];
               $query = "select concat_ws(firstname, lastname) as fullname from login where user_id = $admin_user_id";
               $res = db_query($query);
               $fullname = db_result($res, 0, "fullname");

               $query = "update requests set status='$status', approval_time = '$approval_date', approvedby = '$fullname' where requests_id = $curId";
               //echo "$query<BR>";
               if(db_query($query))
                  $numchange++;
            }
         }
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

   function UpdateAllowanceInfo($user_id, $amount)
   {
      $cur_date = date('Y-m-d');
      $query = "select * from allowance where user_id = $user_id and end >= '$cur_date'";
      $res = db_query($query);
      $num = db_numrows($res);

      if($num > 0) //just update the current amount
      {
         $allowance_id = db_result($res, 0, "allowance_id");
         $cur_amount = db_result($res, 0, "allowance");
         $new_amount = $cur_amount + $amount;
         $query = "update allowance set allowance = '$new_amount' where allowance_id = $allowance_id";
      }
      else
      {
         //add new allowance with 3 months expiry?
         $expiry =  strtotime(date('Y-m-d') . " +3 months");
         $expiry = date('Y-m-d', $expiry);
         $query = "insert into allowance(user_id, allowance, start, end) value ($user_id, '$amount', '$cur_date', '$expiry')";

      }
//echo "$query<BR>";
      $res2 = db_query($query);
      if($res2)
         return true;
      else
         return false;

   }

   function save()
   {
      db_query(_BEGIN);
      //query_val = locationid
      $req_addressfields = array("user_id", "phone", "amount", "status"); //query_val = location_id
      $return_addressfields = GenericCheckAndSync($req_addressfields, false);

      $this->SetValues($return_addressfields);

      $return_size = count($return_addressfields);
      $required_size = count($req_addressfields);

      $comments = _checkIsSet("comments");
      $comments = str_replace('"',"'",$comments);
      $email = _checkIsSet("email");

      $user_id = $this->user_id;
      //get the user's location
      $staff = new staff();
      $staff->LoadStaffId($user_id);
      $location_id = $staff->location_id;

      $contact_number = $this->contact_number;
      $amount = $this->amount;
      $status = $this->status;
      $request_date = $this->request_date;

      $requests_id = $this->requests_id;
      $request_type = $this->request_type;


      if($this->action == "SAVE")
      {
         $this->comments = $comments;
         $query = "INSERT INTO requests (user_id, location_id,  amount, comments, status, request_date, contact_number, email, request_type) values ($user_id, '$location_id', '$amount', \"$comments\", '$status', '$request_date', '$contact_number', '$email', '$request_type')";
      }
      else
      {
         $query = "UPDATE requests SET amount='$amount', comments=\"$comments\", status='$status', contact_number='$contact_number', email='$email' WHERE (requests_id = $requests_id)";
      }
//      echo "$query<BR>";
      $res = db_query($query);
      if($res)
      {
         //email request to admin!
         $fullname = _checkIsSet("fullname");

         if($this->action == "SAVE")
            $request_id = mysql_insert_id();
         else
            $request_id = $this->requests_id;

         $this->emailNotification($fullname, $request_id);

         db_query(_COMMIT);
         return true;
      }
      else
      {
         db_query(_ROLLBACK);
         return false;
      }

   }

   function SetValues($fields)
   {
      $this->user_id = $fields{"user_id"};
      $this->contact_number = $fields{"phone"};
      $this->email = $fields{"email"};
      $this->amount = $fields{"amount"};
      $this->status = $fields{"status"};
      //$this->request_date = $fields{"request_date"};
   }
}

?>
