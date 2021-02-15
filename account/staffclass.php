<?php
$home = dirname(__FILE__) . "/../";
$rootDir = dirname(__FILE__) . "/../../";
$lib = $home . "/lib";

require_once($home . '/globals.php');
require_once($lib . '/functions.php');
require_once($lib . '/htmlGenerator.php');
require_once($lib . '/phpmailer/class.phpmailer.php');
require_once('locationclass.php');
require_once($rootDir .'/genericAccountClass.php');

class staff
{
   var $user_id;
   var $location_id;
   var $user_name;
   var $firstname;
   var $lastname;
   var $password;
   var $access_level;
   var $email;
   var $allowance;
   var $crange;
   var $job_classification;
   var $old_user_id;
   var $role_id;
   var $action;
   var $status;
   var $isAUS;
   var $daysworked;
   var $hire_date;
   var $rulesArr;
   var $oDays_worked;
   var $coordinator_for;
   var $approval_emails;

   function __construct()
   {
      $this->user_id = "";
      $this->old_user_id = "";
      $this->location_id = "";
      $this->user_name = "";
      $this->firstname = "";
      $this->lastname = "";
      $this->password = "";
      $this->access_level = "";
      $this->email = "";
      $this->allowance = "";
      $this->crange = "";
      //$this->job_classification = "";
      $this->role_id = "";
      $this->action = "SAVE";
      $this->status = "ACTIVE";
      $this->isAUS = "Y";
      $this->daysworked = "";
      $this->oDays_worked = "";
      $this->hire_date = "";
      $this->coordinator_for = array();
      $this->approval_emails = array();
   }

   function ChangePassword()
   {
      $user_id = $_SESSION[_USER_ID];
      $curpassword = md5(_checkIsSet("curpassword"));
      $newpassword = md5(_checkIsSet("newpassword"));
      $conpassword = md5(_checkIsSet("conpassword"));

      $query = "select * from login where user_id = $user_id and password = '$curpassword'";
      $res = db_query($query);
      $num = db_numrows($res);

      if($num  > 0)
      {
         if($newpassword != $conpassword)
         {
            $_SESSION['msg'] = "The new password doesn't match the confirmation password";
            return false;
         }
         else
         {
            $query = "UPDATE login SET password = '$newpassword' WHERE (user_id = $user_id)";
            if(db_query($query))
               return true;
            else
            {
               $_SESSION['msg'] = "Failed to change password.";
               return false;
            }

         }
      }
      else
      {
         $_SESSION['msg'] = "Incorrect password";
         return false;
      }
   }

   function LoadStaffId($id)
   {
      $query = "select * from location l1,login l where user_id = $id and l.location_id = l1.location_id";
      $res = db_query($query);
      $num = db_numrows($res);
      if($num > 0)
      {
         $this->old_user_id = db_result($res, 0, "user_id");
         $this->old_user_name = db_result($res, 0, "user_name");
         $this->user_id = db_result($res, 0, "user_id");
         $this->location_id = db_result($res, 0, "location_id");
         $this->user_name = db_result($res, 0, "user_name");
         $this->firstname = db_result($res, 0, "firstname");
         $this->lastname = db_result($res, 0, "lastname");
         $this->email = db_result($res, 0, "email");
         $this->crange = db_result($res, 0, "crange");
         $this->job_classification = db_result($res, 0, "job_classification");
         $this->loadAllowance($this->user_id);
         $this->role_id = db_result($res, 0, "role_id");
         $this->status = db_result($res, 0, "status");
         $this->isAUS = db_result($res, 0, "isAUS");
         $this->access_level = db_result($res, 0, "access_level");
         $this->daysworked = db_result($res, 0, "daysworked");
         $this->oDays_worked = db_result($res, 0, "daysworked");
         $this->hire_date = db_result($res, 0, "start_date");
         $this->action = "UPDATE";
         
         if(!minAccessLevel(_BRANCH_LEVEL))
         	$this->loadApprovalEmails($this->location_id);
         //load approval emails if not BRANCH LEVEL
      }
      else
         return false;
   }

   function loadApprovalEmails($lid)
   {
   	$query = "select distinct(email) from login l, coordinators c where l.user_id = c.user_id and c.location_id = $lid";
   	$res = db_query($query);
   	$num = db_numrows($res);
   	$this->approval_emails = array();
   	if($num > 0)
   	{
   		for($i = 0; $i < $num; $i++)
   		{
   			$aEmail = db_result($res, $i, "email");
   			if(strlen($aEmail) > 0)
	   			array_push($this->approval_emails, $aEmail);
   		}
   	}
   }
   
   function loadAllowance($user_id)
   {
      $query = "select * from allowance where user_id = $user_id order by start";
//      echo "$query<BR>";
      $res = db_query($query);
      $num = db_numrows($res);
      if($num > 0)
      {
         $this->allowanceArr = array();
         for($i = 0; $i < $num; $i++)
         {
            $allowance = new allowances();
            $allowance->allowance_id = db_result($res, $i, "allowance_id");
            $allowance->user_id = db_result($res, $i, "user_id");
            $allowance->allowance = db_result($res, $i, "allowance");
            $startdate = explode(" ", db_result($res, $i, "start"));
            $enddate = explode(" ", db_result($res, $i, "end"));

            $allowance->startdate = $startdate[0];
            $allowance->enddate = $enddate[0];

            array_push($this->allowanceArr, $allowance);
         }
      }
      else
         $this->allowanceArr = array();
   }
   
   function loadCoordinatorLocation($user_id)
   {
   	$query = "select * from coordinators c, location l where user_id = $user_id and l.location_id= c.location_id";
   	//echo "$query<BR>";
   	$res = db_query($query);
   	$num = db_numrows($res);
   	
   	if($num > 0)
   	{
   		$this->coordinator_for = array();
   		
   		for($i = 0; $i < $num; $i++)
   		{
   			$coord = new coordinators();
   			$coord->coordinator_id = db_result($res, $i, "coordinator_id");
   			$coord->user_id = $user_id;
   			$coord->location_id = db_result($res, $i, "location_id");
   			$coord->location_bu = db_result($res, $i, "business_unit");
   			$coord->location_costcentre = db_result($res, $i, "branch_id");
   			array_push($this->coordinator_for, $coord);
   		}
   	}
   	else
   		$this->coordinator_for = array();
   }
   
   function toogleUC($lid, $sid, $isUC)
   {
   	if(!$lid || !$sid)
   		return false;
   	$query = "";
   	if($isUC == "true")
   	{
			$query = "insert into `coordinators` (`user_id`, `location_id`) values ($sid, $lid)";
   	}
   	else 
   	{
   		$query = "delete from coordinators where location_id = $lid and user_id = $sid";
   	}
   	$res = db_query($query);
   	if($res)
   		return true;
   	else
   		return false;
   }
   
   function implodeCoord()
   {
   	$str = "";
   	
   	for($i = 0; $i < count($this->coordinator_for); $i++)
   	{
   		$str .= $this->coordinator_for[$i]->location_costcentre;
   		
   		if($i+1 < count($this->coordinator_for))
   			$str .=";";
   	}
   	return $str;
   	
   }
   
   function loadRules($user_id)
   {
   	$query = "select * from rules where user_id = $user_id order by rule_type, start";
   	$res = db_query($query);
   	$num = db_numrows($res);

   	if($num > 0)
   	{
   		$this->rulesArr = array();
   		
   		for($i = 0; $i < $num; $i++)
   		{
   			$rules = new rules();
   			$rules->rules_id = db_result($res, $i, "rules_id");
   			$rules->rule_type = db_result($res, $i, "rule_type");
   			$rules->rule_name = db_result($res, $i, "title");
   			
   			$rules->cat_type = db_result($res, $i, "cat_type");
   			$rules->max_allowed = db_result($res, $i, "max_allowed");
   			
            $startdate = explode(" ", db_result($res, $i, "start"));
            $enddate = explode(" ", db_result($res, $i, "end"));

            $rules->start = $startdate[0];
            $rules->end = $enddate[0];
//            array_push($this->rulesArr, $rules);
				$rulesKey = $rules->cat_type . "_" . $rules->rule_type;
// 				echo "RK: $rulesKey<BR>";
            $this->rulesArr[$rulesKey] = $rules;
   		}
   	}
   	else
   		$this->rulesArr = array();
   	
   	
   }
   

   function delete()
   {
      if(!minAccessLevel(_BRANCH_LEVEL))
      {
         $_SESSION['msg'] = "You are not authorized to perform this action.";
         return false;
      }
      $idArr = _checkIsSet("itemArr");
      $num = count($idArr);
      $numdel = 0;

      for($i = 0; $i < $num; $i++)
      {
         $curId = $idArr[$i];
         $query = "update login set status = 'INACTIVE' where user_id = $curId";
         if(db_query($query))
            $numdel++;
      }
      if($numdel > 0)
         return true;
      else
         return false;

   }

   function checkExistingUserID($id)
   {
      //check for self;
      if($this->action == "UPDATE")
      {
         if($id == $this->old_user_id)
            return false;
      }

      $query = "select * from login where user_name = $id";

      $res = db_query($query);
      $num = db_numrows($res);
      if($num > 0)
         return true; //id already in db
      else
         return false; //new id;
   }

   function checkExistingUsername($id)
   {
      //check for self;
      if($this->action == "UPDATE")
      {
         if($id == $this->old_user_name)
            return false;
      }

      if(strlen($id) == 0)
         return false;

      $query = "select * from login where user_name = '$id'";

      $res = db_query($query);
      $num = db_numrows($res);
      if($num > 0)
         return true; //id already in db
      else
         return false; //new id;
   }


   function resetPassword($status)
   {
      if(!minAccessLevel(_ADMIN_LEVEL))
      {
      	//$errorHtml = '<div class="warning" style="display: none;">You are not authorized for perform this action.<img src="../img/close.png" alt="" class="close" id="msgClose" /></div>';
      	//$json = '{"status":"1","msg":'.json_encode($errorHtml).',"mtype":"warning"}';	
      	//return $json;
      	
            $_SESSION['msg'] = "You are not authorized for perform this action.";
            return false;      	
      }
      $idArr = _checkIsSet("itemArr");
      
      
      $num = count($idArr);
      $numchange = 0;
      
      $query = "select * from login where user_id in (".implode(",",$idArr).")";
     // echo "$query\n";
      $res = db_query($query);
      $qnum = db_numrows($res);
      
      if($qnum != count($idArr))
      {      
			//$errorHtml = '<div class="warning" style="display: none;">Error, only INACTIVE logins can be activated - select INACTIVE logins only.<img src="../img/close.png" alt="" class="close" id="msgClose" /></div>';
			//$json = '{"status":"1","msg":'.json_encode($errorHtml).',"mtype":"warning"}';	
			//return $json;      
			
            $_SESSION['msg'] = "Error, only ACTIVE logins can be reset - select ACTIVE logins only.";
            return false;         	
      }
      else 
      {
      	for($i = 0; $i < count($idArr); $i++)    
      	{
				$curId = $idArr[$i];
      		$query = "select * from login where user_id = $curId";
      		$res = db_query($query);
      		$num = db_numrows($res);
      		
      		if($num > 0)
      		{
      			$email = db_result($res, 0, "email");
      			$storeno = db_result($res, 0, "user_name");

					$min = pow(10, 6 - 1) ;
					$max = pow(10, 6) - 1;
					$genPassword = mt_rand($min, $max);        			
					$md5Pass = md5($genPassword);
					
      			$query = "update login set password='$md5Pass', status = 'ACTIVE' where user_id = $curId";
      			//echo "$query\b";
      			$res = db_query($query);
      			if($res)
      			{
      				$numchange++;
      				
		$htmlBody = "<html><body><h1 style='color:#333;font-size:30px;font-weight:normal;margin:0;' >GPC Uniforms<h1>";
		$htmlBody .= "<h2 style='font-size:24px;font-weight:normal;margin:0 0 10px;'>Password Reset<h2>";				      
						
		$htmlBody .= "<p style='color:#777;font-size:16px;line-height:150%;margin:0;'>
		 		Your password has been reset.  You can now use our online ordering system to place your order.</p><br/>";
				      
		$htmlBody .= "<p style='color:#999;font-size:14px;line-height:150%;margin:0;'>USER NAME: $storeno<br/>PASSWORD: $genPassword</p><br/>";

				      
		$htmlBody .= "<a rel='nofollow' target='_blank' href='https://www.designstoyou.com.au/gpc' style='color:#1990C6;font-size:16px;text-decoration:none;'>Visit the ordering site</a><br/><hr/>";			      
				      
		$htmlBody .= "<p style='color:#999;font-size:14px;line-height:150%;margin:0;'>
		    If you have any questions, reply to this email or contact us at <a rel='nofollow' href='sales@designstoyou.com.au' target='_blank' style='color:#1990C6;font-size:14px;text-decoration:none;'>sales@designstoyou.com.au</a></p>";
   	
      $htmlBody .="</body></html>";

      
						
						//email
					    $mail = new PHPMailer();
				
				      $mail->IsSMTP(); // telling the class to use SMTP
				      $mail->SMTPKeepAlive = true;
				
				      //$mail->Host = "mail.bigpond.com"; // SMTP server
				      $mail->Host = _MAILHOST; // SMTP server
				      $mail->FromName = "Designs To You";
				      $mail->From = "sales@designstoyou.com.au";
						$mail->AddAddress($email);
				      $mail->AddBCC("c.cao@designstoyou.com.au");
				      $mail->AddBCC("sales@designstoyou.com.au");				      
  				      //$mail->AddBCC("m.grossi@designstoyou.com.au");      
  				      				
					   $mail->Subject = "DTY/GPC Password Reset - $storeno";
					   
				      $mail->Body = $htmlBody;
				      $mail->isHTML(true);
				
				      if(!$mail->Send())
				      {
				//       echo "1ERROR: " . $mail->ErrorInfo . "<BR>";
				         $mail->SmtpClose();
				      }
				      else
				      {
				         $mail->SmtpClose();
				      }      				
      			}
      		}
      	}
      }
      
      if($numchange > 0)
      {
		   db_query(_COMMIT);

		   //$errorHtml = '<div class="success" style="display: none;">Success, '.$numchange.' login(s) updated.<img src="../img/close.png" alt="" class="close" id="msgClose" /></div>';
		   //$json = '{"status":"1","msg":'.json_encode($errorHtml).',"mtype":"success"}';	
         //return $json;
         
         $_SESSION['msg'] = "Success, '.$numchange.' login(s) updated.";
         return true;           
      }
      else
      {
      	//$errorHtml = '<div class="warning" style="display: none;">Error, nothing selected.<img src="../img/close.png" alt="" class="close" id="msgClose" /></div>';
      	//$json = '{"status":"1","msg":'.json_encode($errorHtml).',"mtype":"warning"}';      	
         //return $json;

         db_query(_ROLLBACK);         
         $_SESSION['msg'] = "Error, nothing selected.";
         return false;            
      }
      //$errorHtml = '<div class="warning" style="display: none;">Error, nothing selected.<img src="../img/close.png" alt="" class="close" id="msgClose" /></div>';
      //$json = '{"status":"1","msg":'.json_encode($errorHtml).',"mtype":"warning"}';
      //return $json;         
      
      $_SESSION['msg'] = "Error, nothing selected.";
      return false;         
   }     
   
   function approveAccess($status)
   {
      if(!minAccessLevel(_ADMIN_LEVEL))
      {
      	//$errorHtml = '<div class="warning" style="display: none;">You are not authorized for perform this action.<img src="../img/close.png" alt="" class="close" id="msgClose" /></div>';
      	//$json = '{"status":"1","msg":'.json_encode($errorHtml).',"mtype":"warning"}';	
      	//return $json;
      	
            $_SESSION['msg'] = "You are not authorized for perform this action.";
            return false;      	
      }
      $idArr = _checkIsSet("itemArr");
      
      
      $num = count($idArr);
      $numchange = 0;
      
      $query = "select * from login where user_id in (".implode(",",$idArr).") and status = 'INACTIVE'";
     // echo "$query\n";
      $res = db_query($query);
      $qnum = db_numrows($res);
      
      if($qnum != count($idArr))
      {      
			//$errorHtml = '<div class="warning" style="display: none;">Error, only INACTIVE logins can be activated - select INACTIVE logins only.<img src="../img/close.png" alt="" class="close" id="msgClose" /></div>';
			//$json = '{"status":"1","msg":'.json_encode($errorHtml).',"mtype":"warning"}';	
			//return $json;      
			
            $_SESSION['msg'] = "Error, only INACTIVE logins can be activated - select INACTIVE logins only.";
            return false;         	
      }
      else 
      {
      	for($i = 0; $i < count($idArr); $i++)    
      	{
				$curId = $idArr[$i];
      		$query = "select * from login where user_id = $curId";
      		$res = db_query($query);
      		$num = db_numrows($res);
      		
      		if($num > 0)
      		{
      			$email = db_result($res, 0, "email");
      			$storeno = db_result($res, 0, "user_name");

					$min = pow(10, 6 - 1) ;
					$max = pow(10, 6) - 1;
					$genPassword = mt_rand($min, $max);        			
					$md5Pass = md5($genPassword);
					
      			$query = "update login set password='$md5Pass', status = 'ACTIVE' where user_id = $curId";
      			//echo "$query\b";
      			$res = db_query($query);
      			if($res)
      			{
      				$numchange++;
      				
		$htmlBody = "<html><body><h1 style='color:#333;font-size:30px;font-weight:normal;margin:0;' >GPC Uniforms<h1>";
		$htmlBody .= "<h2 style='font-size:24px;font-weight:normal;margin:0 0 10px;'>Welcome to Designs To You!<h2>";				      
						
		$htmlBody .= "<p style='color:#777;font-size:16px;line-height:150%;margin:0;'>
		 		Your account has been activated.  You can now use our online ordering system to place your order.</p><br/>";
				      
		$htmlBody .= "<p style='color:#999;font-size:14px;line-height:150%;margin:0;'>USER NAME: $storeno<br/>PASSWORD: $genPassword</p><br/>";

				      
		$htmlBody .= "<a rel='nofollow' target='_blank' href='https://www.designstoyou.com.au/gpc' style='color:#1990C6;font-size:16px;text-decoration:none;'>Visit the ordering site</a><br/><hr/>";			      
				      
		$htmlBody .= "<p style='color:#999;font-size:14px;line-height:150%;margin:0;'>
		    If you have any questions, reply to this email or contact us at <a rel='nofollow' href='sales@designstoyou.com.au' target='_blank' style='color:#1990C6;font-size:14px;text-decoration:none;'>sales@designstoyou.com.au</a></p>";
   	
      $htmlBody .="</body></html>";

      
						
						//email
					    $mail = new PHPMailer();
				
				      $mail->IsSMTP(); // telling the class to use SMTP
				      $mail->SMTPKeepAlive = true;
				
				      //$mail->Host = "mail.bigpond.com"; // SMTP server
				      $mail->Host = _MAILHOST; // SMTP server
				      $mail->FromName = "Designs To You";
				      $mail->From = "sales@designstoyou.com.au";
						$mail->AddAddress($email);
				      $mail->AddBCC("c.cao@designstoyou.com.au");
				      $mail->AddBCC("sales@designstoyou.com.au");				      
  				      //$mail->AddBCC("m.grossi@designstoyou.com.au");      
  				      				
					   $mail->Subject = "DTY/GPC Login - $storeno";
					   
				      $mail->Body = $htmlBody;
				      $mail->isHTML(true);
				
				      if(!$mail->Send())
				      {
				//       echo "1ERROR: " . $mail->ErrorInfo . "<BR>";
				         $mail->SmtpClose();
				      }
				      else
				      {
				         $mail->SmtpClose();
				      }      				
      			}
      		}
      	}
      }
      
      if($numchange > 0)
      {
		   db_query(_COMMIT);

		   //$errorHtml = '<div class="success" style="display: none;">Success, '.$numchange.' login(s) updated.<img src="../img/close.png" alt="" class="close" id="msgClose" /></div>';
		   //$json = '{"status":"1","msg":'.json_encode($errorHtml).',"mtype":"success"}';	
         //return $json;
         
         $_SESSION['msg'] = "Success, '.$numchange.' login(s) updated.";
         return true;           
      }
      else
      {
      	//$errorHtml = '<div class="warning" style="display: none;">Error, nothing selected.<img src="../img/close.png" alt="" class="close" id="msgClose" /></div>';
      	//$json = '{"status":"1","msg":'.json_encode($errorHtml).',"mtype":"warning"}';      	
         //return $json;

         db_query(_ROLLBACK);         
         $_SESSION['msg'] = "Error, nothing selected.";
         return false;            
      }
      //$errorHtml = '<div class="warning" style="display: none;">Error, nothing selected.<img src="../img/close.png" alt="" class="close" id="msgClose" /></div>';
      //$json = '{"status":"1","msg":'.json_encode($errorHtml).',"mtype":"warning"}';
      //return $json;         
      
      $_SESSION['msg'] = "Error, nothing selected.";
      return false;         
   }   

   function save()
   {
      if(!minAccessLevel(_BRANCH_LEVEL))
      {
         $_SESSION['msg'] = "You are not authorized to perform this action.";
         return false;
      }
      db_query(_BEGIN);
      //query_val = locationid
      $req_addressfields = array("user_id", "user_name", "firstname", "lastname", "query_val", "role_id", "status", "range", "daysworked", "hire_date", "isAUS"); //query_val = location_id
      $return_addressfields = GenericCheckAndSync($req_addressfields, false);

      $this->SetValues($return_addressfields);

      $return_size = count($return_addressfields);
      $required_size = count($req_addressfields);

      //$user_id = $this->user_id;

      $firstname = $this->firstname;
      $lastname = $this->lastname;
      $location_id = $this->location_id;
      $username = $this->user_name;
      $user_id = $this->user_id;
      $status = $this->status;
     // $job_classification = $this->job_classification;
      $crange = $this->crange;
      $email = _checkIsSet("email");

      //$allowance = $this->allowance;
      $role_id = $this->role_id;
      $password = md5(strtolower("gpc123"));
      //$access_level = "2";
      $access_level = $this->determineAccessLevel();
      $realm = _REALM;
      $isAUS = $this->isAUS;
      $daysworked = $this->daysworked;
      $hire_date = $this->hire_date;
      
      //get location BU since alloc rules are different
      $loc = new location();
      $loc->LoadLocationId($this->location_id);
      $loc_bu = $loc->business_unit;
      $staffJur = $loc->jurisdiction;
      
      if($this->action == "SAVE")
      {
      	$username = $username;
      }

      if($this->checkExistingUsername($username))
      {
      	$_SESSION['msg'] = "Username exists, please try again.";
         return false; //found userid
      }

      if($this->action == "SAVE")
         $query = "INSERT INTO login ( `location_id`, `user_name`, `firstname`, `lastname`, `password`, `access_level`, `role_id`, `status`, `crange`, `realm`, `isAUS`, `daysworked`, `email`, `start_date`, `jurisdiction`, `job_classification`) VALUES  ('$location_id', \"$username\", \"$firstname\", \"$lastname\", '$password', '$access_level', '$role_id', '$status', '$crange', '$realm', '$isAUS', '$daysworked', '$email', '$hire_date', '$staffJur', '$staffJur')";
      else
      {
         $ouid = $this->old_user_id;
         $query = "UPDATE login SET `jurisdiction` = '$staffJur', `start_date` = '$hire_date', `user_id` = $user_id, `location_id` = '$location_id', `realm` = \"$realm\", `access_level` = \"$access_level\", `email` = \"$email\", `user_name` = \"$username\", `firstname` = \"$firstname\", `lastname` = \"$lastname\", `role_id` = $role_id, `status`= \"$status\", `crange`=\"$crange\", isAUS=\"$isAUS\", daysworked=\"$daysworked\" WHERE (`user_id` = $ouid)";

      }
//      echo "$query<BR>";
//return false;
      $res = db_query($query);
      
      if($res)
      {
         if($this->action == "SAVE")
         {
            $user_id = mysqli_insert_id(db_connect()); 
            
             //save default alloc, not used since we're giving them the option to add allocations
             //2/2/21 changed to allowance - $ value
            // if(!$this->saveAllocation($user_id, $loc_bu, $daysworked, $crange, $role_id,$hire_date))
            if(!$this->saveAllowance($user_id))
             {
	             db_query(_ROLLBACK);
	             return false;
             }
            
         }
         else
         {
            $user_id = $ouid;

            if(!$this->saveAllowance($user_id))
            {
               db_query(_ROLLBACK);
               return false;
            }
            /*            
	         //now save the allowances
	         if(!$this->saveAllocation($user_id, $loc_bu, $daysworked, $crange, $role_id,$hire_date))
	         {
	            db_query(_ROLLBACK);
	            return false;
            }
            */
         }                
         
         db_query(_COMMIT);
         global $portalURL;
         $helpLinksArr = array(
            "How to order your uniform" => "$portalURL/help/GPC_Uniform_Ordering guide_2019_03_25.pdf",
            "Measuring guide" => "$portalURL/help/Measuring-Guide.pdf",
            "Uniform range" => "$portalURL/help/GPC_Range.pdf"
         );
         
         emailNotification($email, $username, $firstname, "gpc", $portalURL, $this->action, "gpc123", $helpLinksArr);
         return true;
      }
      else
      {
         db_query(_ROLLBACK);
         return false;
      }

   }

   function determineAccessLevel()
   {
      $selAccessLevel = _checkIsSet("access_level");
      $myAccessLevel = $_SESSION[_ACCESS_LEVEL];

      if($selAccessLevel < $myAccessLevel) //return the lowest, can't change an access level higher than your own
      {
         return _USER_LEVEL;
      }
      else
         return $selAccessLevel;


   }

   function SetValues($fields)
   {
      $this->user_name = $fields{"user_name"};
      $this->user_id = $fields{"user_id"};
      $this->firstname = $fields{"firstname"};
      $this->email = $fields{"email"};
      $this->lastname = $fields{"lastname"};
      $this->location_id = $fields{"query_val"};
      $this->allowance = $fields{"allowance"};
      $this->status = $fields{"status"};
      $this->role_id = $fields{"role_id"};
      $this->crange = $fields{"range"};
      //$this->job_classification = $fields{"job_classification"};
      $this->isAUS = $fields{"isAUS"};
      $this->daysworked = $fields{"daysworked"};
      $this->hire_date = $fields{"hire_date"};
   }
   
   function deleteGarmentAlloc($user_id, $rule_type)
   {
   	//delete any old rules
   	$query = "delete from rules where user_id = $user_id and rule_type = $rule_type";
   	$res = db_query($query);
   	if(!$res)
   		return false;
   	else
   		return true;
   }
   
   function numRules($user_id)
   {
   	    $query = "select * from rules where user_id = $user_id group by rule_type";
    	$res = db_query($query);
		$num = db_numrows($res);
		return $num;		
   }
   
   function numAllowance($user_id)
   {
   	    $query = "select * from allowance where user_id = $user_id";
    	$res = db_query($query);
		$num = db_numrows($res);
		return $num;	       
   }
   
   function allowanceTitle($user_id, $rule_type)
   {

   }     
   
   function ruleTitle($user_id, $rule_type)
   {
   	$query = "select title from rules where user_id = $user_id and rule_type = $rule_type";
   	$res = db_query($query);
		$num = db_numrows($res);
		$title = "";
		if($num > 0)
			$title = db_result($res, 0, "title");
		return $title;
   }   

   function saveGarmentAlloc($user_id, $fullname, $username, $hiredate, $role_id, $daysworked,$crange,$loc_bu)
   {
   	$hiredatePlusMonths = strtotime(date("Y-m-d", strtotime($hiredate)) . "+1 year");
   	$endTime = date('Y-m-d', $hiredatePlusMonths);
   
   	//if($this->deleteGarmentAlloc($user_id, 1)) delete later, lets check if we've used up the alloc first
   	{
	   	$rule_type = 1;

	   	$jacket = 0;
	   	$upper = 0;
	   	$lower = 0;
	   	$techjk = 0;
	   	$knit = 0;
	   	$polo = 0;
	   	$belt = 0;
	   	$acc = 0;
	   	$combined = 0;
	   	
  		for($i = 1; $i < 8; $i++) //9 = garment categories and not number of allowances!!
   	{
   		$cat_type = _checkIsSet("cat_type_$i");
   		$max_allowed = _checkIsSet("max_allowed_$i");
			$startdate = _checkIsSet("start_$i");
	  		$enddate = _checkIsSet("end_$i");
// 	  		echo "I: $i catType: $cat_type<BR>\n";
         //NEED TO CHECK FOR CLASHES OF EXPIRY AND START DATES IN SAY CATEGORY!
//          if($i >= 2)
//          {
//             if($startdate <= $oldexpire)
//             {
//    	         $_SESSION['msg'] = "Error! The start date clashes with the expiry date of the previous allowance.";
//                return false;
//             }
//          }
//     		else
//      		{
//         		$oldexpire = $enddate;
//    		}
   	
   		//         echo "allowance: $allowance expiry: $expiry<BR>";
   		$numCat = count($cat_type);
//    		$rule_type = $numCat;

   		for($j = 0; $j < count($cat_type); $j++,$rule_type--)
   		{
   			$tmp_cat_type = $cat_type[$j];

   			$tmp_max_allowed = $max_allowed[$j];
   			$tmp_startdate = $startdate[$j];
   			$tmp_enddate = $enddate[$j];
   			$rule_type = $j+1;
   			
   			//get the actual name of category rather than number
   			$cat_type_name = $categoryArr[$tmp_cat_type];
   	
   			$ruleName = _checkIsSet("hiddenTabTitleID-" . $j);
// 			echo "cat: $tmp_cat_type NUM: $numCat QTY: $tmp_max_allowed\n";
   			
	   		if($tmp_startdate > $tmp_enddate) //error
	   		{
	   			$_SESSION['msg'] = "Error! The allocation for $cat_type_name is set to expire before the start date.";
	            return false;
	         }   				   		   
   			
   			if(strlen($tmp_enddate) > 0)
   			{
		        // $query = "INSERT INTO `rules` ( `user_id`, `cat_type`, `max_allowed`, `rule_type`,`start`, `end`) VALUES ('$user_id', '$tmp_cat_type', '$tmp_max_allowed', $rule_type, '$tmp_startdate', '$tmp_enddate')";
   	         $query = "INSERT INTO `rules` ( `user_id`, `cat_type`, `max_allowed`, `rule_type`,`start`, `end`, `title`) VALUES ('$user_id', '$tmp_cat_type', '$tmp_max_allowed', $rule_type, '$tmp_startdate', '$tmp_enddate', \"$ruleName\")";
// 	   		       echo "$query\n";
	   			if(!db_query($query))
	   				return false;
   			}
   		}
   	}   		
   	return true;	
   		
   	}
   	//else
   		return false; // delete failed
   	}
   					 
   					 
	function saveAllocation($user_id, $loc_bu, $daysworked, $crange, $role_id,$hiredate)
   {
      $numAllowance = _checkIsSet("numallowance");
   	
   	$hiredatePlusMonths = strtotime(date("Y-m-d", strtotime($hiredate)) . "+1 year");
   	$endTime = date('Y-m-d', $hiredatePlusMonths);   	
   	    
      global $garmentTypes;
      global $categoryArr;



         $garmentTypeNewAllocArr = array();
         $garmentStartArr = array();
         $garmentEndArr = array();
         $garmentTypeUsedArr = array();
         
         foreach($garmentTypes as $gtkey => $gtval)
         {
            $gtname = $categoryArr[$gtval];
         	$dataArr = $this->lookUpManualAlloc($gtval);
         	if($dataArr == -1)
         	{
      			$_SESSION['msg'] = "Only one $gtname allocation can be active at a time, please check the start and end dates of the $gtname allocation.";
      			return false;      			
         	}
         	
            $garmentTypeNewAllocArr[$gtval] = $dataArr[0];
            $garmentStartArr[$gtval] = $dataArr[1];
            $garmentEndArr[$gtval] = $dataArr[2];   	
            
            $tmpUsed =  $this->allocationUsedLineitems($user_id, $garmentStartArr[$gtval], $garmentEndArr[$gtval], $garmentTypeNewAllocArr[$gtval], $gtval);
            $garmentTypeUsedArr[$gtval] = $tmpUsed;
         }

         if(array_sum($garmentTypeUsedArr) == 0) //used returned all false which means not enough allocated
         {
            $msg = "Some garment allocations have already been used, please increase the allocations.";
            foreach($garmentTypeUsedArr as $gtUsedKey => $usedVal)
            {
               if($usedVal == 0)
               {
                  $gtname = $categoryArr[$gtval];
                  $msg .= "\n $gtname Used.";
               }
            }
	   		$_SESSION['msg'] = $msg;
	   		return false;            
         }
         
   	
   	//delete old allowances first;
   	$query = "delete from rules where user_id = $user_id";
  		$delres = db_query($query);
  		if(!$delres)
  			return false;
  		$oldexpire = "";
      $maxGarmentID = max($garmentTypes);  		
//   		for($i = 1; $i <= $numAllowance; $i++)
  		for($i = 1; $i <= $maxGarmentID; $i++) //9 = garment categories and not number of allowances!!
   	{
   		$cat_type = _checkIsSet("cat_type_$i");
   		$max_allowed = _checkIsSet("max_allowed_$i");
			$startdate = _checkIsSet("start_$i");
	  		$enddate = _checkIsSet("end_$i");
	  		
         //NEED TO CHECK FOR CLASHES OF EXPIRY AND START DATES IN SAY CATEGORY!
//          if($i >= 2)
//          {
//             if($startdate <= $oldexpire)
//             {
//    	         $_SESSION['msg'] = "Error! The start date clashes with the expiry date of the previous allowance.";
//                return false;
//             }
//          }
//     		else
//      		{
//         		$oldexpire = $enddate;
//    		}
   	
   		//         echo "allowance: $allowance expiry: $expiry<BR>";
   		$numCat = count($cat_type);
   		for($j = 0; $j < count($cat_type); $j++,$rule_type--)
   		{
   			$tmp_cat_type = $cat_type[$j];
   			$tmp_max_allowed = $max_allowed[$j];
   			$tmp_startdate = $startdate[$j];
   			$tmp_enddate = $enddate[$j];

 		   						   			
   			$ruleName = _checkIsSet("hiddenTabTitleID-" . $j);
   			$rule_type = $j+1;
   			
   			//get the actual name of category rather than number
   			$cat_type_name = $categoryArr[$tmp_cat_type];
   	
   			
// 			echo "cat: $tmp_cat_type NUM: $numCat QTY: $tmp_max_allowed\n";
   			
	   		if($tmp_startdate > $tmp_enddate) //error
	   		{
	   			$_SESSION['msg'] = "Error! The allocation for $cat_type_name is set to expire before the start date.";
	            return false;
	         }   				   		   
   			
   			if(strlen($tmp_enddate) > 0 && $tmp_max_allowed >0)
   			{
		         $query = "INSERT INTO `rules` ( `user_id`, `cat_type`, `max_allowed`, `rule_type`,`start`, `end`, `title`) VALUES ('$user_id', '$tmp_cat_type', '$tmp_max_allowed', $rule_type, '$tmp_startdate', '$tmp_enddate', \"$ruleName\")";
	   		      // echo "$query\n";
	   			if(!db_query($query))
	   				return false;
   			}
   		}
   	}
   	return true;
   }
   
   function lookUpManualAlloc($garmentCat)
   {
		$garmentArr = _checkIsSet("max_allowed_" . $garmentCat);
	   $garmentStartArr = _checkIsSet("start_" . $garmentCat);
	   $garmentEndArr  = _checkIsSet("end_" . $garmentCat);

	   $numGarmentAlloc = count($garmentArr);
	   if($numGarmentAlloc > 1)
	   {
	   	$i=0;
	   	while(strlen($garmentArr[$i]) > 0 && $garmentArr[$i] <= 0)
	   	{
	   		$i++;
	   	}
	 
		   $endDate = $garmentEndArr[$i];
		   $garment = $garmentArr[$i];
		   $garmentStart = $garmentStartArr[$i];
		   $garmentEnd = $garmentEndArr[$i];			   
		   $i++;
		   for(; $i < $numGarmentAlloc; $i++)
		   {
		   	//echo "len: $i " .strlen($garmentEndArr[$i])."\n";
		   	//echo "max max_allowed_$garmentCat [$numGarmentAlloc] gar[$i] = " . $garmentStartArr[$i]  . " END $endDate\n";
		   	if(strlen($garmentEndArr[$i]) > 0)
		   	{
			   	if(strlen($garmentEndArr[$i]) > 0 && $garmentStartArr[$i] < $endDate)
			   	{
			   		return -1;
			   	}
			   	else
			   	{
			   		$endDate = $garmentEndArr[$i];
					   $garment = $garmentArr[$i];
					   $garmentStart = $garmentStartArr[$i];
					   $garmentEnd = $garmentEndArr[$i];	 	
	   //echo "GS[$i] $garmentStart EN: $garmentEnd $garment\n";		   		
			   	}
		   	}
		   }
	   }
	   else 
	   {
		   $garment = $garmentArr[0];
		   $garmentStart = $garmentStartArr[0];
		   $garmentEnd = $garmentEndArr[0];	 	   	
	   }
 


	   $dataArr[0] = $garment;
	   $dataArr[1] = $garmentStart;
	   $dataArr[2] = $garmentEnd;
	   
	   
	   return $dataArr;
	   
   }
   
   function allocationUsedLineitems($user_id, $start, $end, $new_alloc, $catType)
   {
   	$query = "select * from rules where user_id = $user_id and cat_type = $catType and start >= '$start' order by start";
		//echo "0. $query\n";
   	
   	
   	$res = db_query($query);
   	$num = db_numrows($res);
   	
   	if($num > 0)
   	{
   		$curAlloc = db_result($res, 0, "max_allowed");
   	}
   	


   	if($curAlloc == 0 && $new_alloc >= 0)
   		return true;
   	else if($curAlloc == $new_alloc)
   		return true;
   	  // 	echo "CUR: $curAlloc NEW: $new_alloc\n";
      //	echo "1. $query\n";
   	$query = "select sum(qty) as total from orders o, lineitems li where o.order_id = li.order_id and o.user_id = $user_id and o.order_time between '$start' and '$end' and li.cat_id = $catType";
 // 	echo "2. $query\n";
   	
   	$res = db_query($query);
   	$num = db_numrows($res);
   //	echo "NUM: $num cat_ID $catType\n\n";
   	if($num > 0)
   	{
//    		if($cat_id != $catType) //there are some orders so can't change cat type/id
//    			return false;
   		
   		$total = db_result($res, 0, "total");
   		// 			if($total == null)
   			// 				return true;
   			//	echo "TOTAL: $total NEW: $new_alloc<BR>";
   				
   		if($total > $new_alloc)
   			return false;
   		else return true;
   	}
   	else
   		return true;
   }
      
   function allocationUsed($user_id, $start, $end, $idx, $new_alloc, $catType)
   {
   	//load current allocation
   	$this->loadRules($user_id);
//    	echo "LEN: " . count($this->rulesArr). "<BR>";

   	if($idx < 6)
   		$rule_type = 1;
   	else 
   	{
   		$multi = ($idx - $idx%5)/5;
   		if($multi > 0)
   			$rule_type += $multi;
   	}
   	
		$rulesKey = $catType . "_" . $rule_type;
   	//$curRule = $this->rulesArr[$idx-1];
   //	print_r($this->rulesArr);
   	$curRule = $this->rulesArr[$rulesKey];
   	$cat_id = $curRule->cat_type;
   	$curAlloc = $curRule->max_allowed;

   //	echo "CUR: $curAlloc NEW: $new_alloc KEY: $rulesKey<BR>";
   	
   	if($curAlloc == 0 && $new_alloc >= 0)
   		return true;
   	
   	$query = "select sum(qty) as total from orders o, lineitems li where o.order_id = li.order_id and o.user_id = $user_id and o.order_time between '$start' and '$end' and li.cat_id = $cat_id";
//    	echo "$query<BR>";
   	$res = db_query($query);
   	$num = db_numrows($res);
   	//echo "NUM: $num<BR>";
   	if($num > 0)
   	{
			$total = db_result($res, 0, "total");   		
			if($total != null)
			{
	   		if($cat_id != $catType) //there are some orders so can't change cat type/id
	   			return false;
		
	// 			if($total == null)
	// 				return true;
				if($total > $new_alloc)
					return false;
				else return true;
			}
			else return true;
   	}
   	else 
   		return true;
   }	

   function saveAllowance($user_id)
   {
      $numAllowance = _checkIsSet("numallowance");

      //delete old allowances first;
      $query = "delete from allowance where user_id = $user_id";
      $delres = db_query($query);
      if(!$delres)
         return false;

      $oldexpire = "";
      for($i = 1; $i <= $numAllowance; $i++)
      {

         $allowance = _checkIsSet("allowance$i");
         $startdate = _checkIsSet("start$i");
//         $startdate = date('Y-m-d');
         $enddate = _checkIsSet("end$i");

         if($startdate > $enddate) //error
         {
            $_SESSION['msg'] = "Error! The allowance is set to expire before the start date.";
            return false;
         }

         if($i >= 2)
         {
            if($startdate <= $oldexpire)
            {
               $_SESSION['msg'] = "Error! The start date clashes with the expiry date of the previous allowance.";
               return false;
            }
         }
         else
         {
            $oldexpire = $enddate;
         }

//         echo "allowance: $allowance expiry: $expiry<BR>";
         $query = "INSERT INTO allowance (user_id, allowance, start, end) VALUES  ($user_id, $allowance, '$startdate', '$enddate')";
       // echo "$query<BR>";
         if(!db_query($query))
            return false;
      }
      return true;
   }
}

class rules
{
	var $rules_id;
	var $full_name;
	var $user_name;
	var $user_id;
	var $cat_type;
	var $max_allowed;
	var $rule_type;
	var $start;
	var $end;
	
	function rule()
	{
		$this->rules_id = "";
		$this->full_name = "";
		$this->user_name = "";
		$this->user_id = "";
		$this->cat_type = "";
		$this->max_allowed = "";
		$this->rule_type = "";
		$this->start = "";
		$this->end = "";
	}
	
	function addRule($user_id, $cat_type, $max_allowed, $startdate, $enddate)
	{
		$this->user_id = $user_id;
		$this->cat_type = $cat_type;
		$this->max_allowed = $max_allowed;
		$this->start = $startdate;
		$this->end = $enddate;
		return true;
	}
	
	function saveRule()
	{
		$user_id = $this->user_id;
		$cat_type = $this->cat_type;
		$max_allowed = $this->max_allowed;
		$startdate = $this->start;
		$enddate = $this->end;
		$query = "INSERT INTO `rules` ( `user_id`, `cat_type`, `max_allowed`, `start`, `end`) VALUES ('$user_id', '$cat_type', '$max_allowed', '$startdate', '$enddate')";
		$res = db_query($query);
		
		if($res)
			return true;
		else
			return false;
	}
	
	function loadRule($ordertime, $userid)
	{
		//reset order time to 00:00:00
		$tmpTimeArr = explode(" ", $ordertime);
		$ordertime = $tmpTimeArr[0] . " 00:00:00";
	
		$query = "select * from rules where start <= '$ordertime' and end >= '$ordertime' and user_id = $userid";
	
		$res = db_query($query);
		$num = db_numrows($res);
	
		//      echo "$query<BR>";
		if($num > 0)
		{
			$this->rules_id = db_result($res, 0, 'rules_id');
			$this->user_id = db_result($res, 0, 'user_id');
			$this->start = db_result($res, 0, 'start');
			$tmpEnd = db_result($res, 0, 'end');
			$endTimeArr = explode(" ", $tmpEnd);
			$this->end = $endTimeArr[0] . " 23:59:00"; // change the end to end of day
		
			$this->cat_type = db_result($res, 0, "cat_type");
			$this->max_allowed = db_result($res, 0, "max_allowed");
		}
	}	
}

class coordinators
{
	var $coordinator_id;
	var $user_id;
	var $location_id;
	var $location_bu;
	var $location_costcentre; //branch_id in table;
	
	function __construct()
	{
		$this->coordinator_id = "";
		$this->user_id= "";
		$this->location_id = "";
		$this->location_bu = "";
		$this->location_costcentre = "";
	}
	
	
	
}

class allowances
{
   var $allowance_id;
   var $user_id;
   var $allowance;
   var $startdate;
   var $enddate;
   var $title;
   var $comments;

   function allowance()
   {
      $this->allowance_id = "";
      $this->user_id = "";
      $this->allowance = "";
      $this->startdate = "";
      $this->enddate = "";
      $this->title = "";
      $this->comments = "";
   }

   function addAllowance($user_id, $allowance, $startdate, $enddate)
   {
      $this->user_id = $user_id;
      $this->allowance = $allowance;
      $this->startdate = $startdate;
      $this->enddate = $enddate;

      return true;
   }

   function saveAllowance()
   {
      $user_id = $this->user_id;
      $allowance = $this->allowance;
      $startdate = $this->startdate;
      $enddate = $this->enddate;

      $query = "INSERT INTO allowance (`user_id`, `allowance`, `start`, `end`, `title`, `comments` ) VALUES  ($user_id, $allowance, '$startdate', '$enddate', \"$title\", \"$comments\")";
      $res = db_query($query);

      if($res)
         return true;
      else
         return false;
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
         $this->title = db_result($res, 0, 'title');
         $this->comments = db_result($res, 0, 'comments');
      }
   }

}
?>
