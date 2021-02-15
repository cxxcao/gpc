<?php
// session_unset();
// session_destroy();
// $_SESSION = array();
session_start();

$home = dirname(__FILE__) . "/../";
$lib = $home ."/lib/";

require_once($lib . 'database.php');
require_once($home . '/globals.php');
require_once($lib . 'functions.php');
require_once($lib . 'loginfunctions.php');
require_once($lib . '/phpmailer/class.phpmailer.php');


$storeno = _checkIsSet("storeno");
$storename =  _checkIsSet("storename");
$address =  _checkIsSet("address");
$suburb =  _checkIsSet("suburb");
$state =  _checkIsSet("state");
$postcode =  _checkIsSet("postcode");
$firstname = _checkIsSet("firstname");
$lastname = _checkIsSet("lastname");
$phone = _checkIsSet("phone");
$email = _checkIsSet("email");


   function checkUnique($username)
   {
		//checkusername
		$checkQuery = "select * from login where user_name='$username'";
		$checkRes = db_query($checkQuery);   	
		$checkNum = db_numrows($checkRes);
		if($checkNum == 0)
			return true;
		else 
			return false;
   }
   

if(!$storename || !$address || !$suburb || !$state || !$postcode || !$firstname|| !$lastname || !$phone || !$email)
{
	$errMsg = "All fields required.";
	echo '{"success":false,"msg":"'.$errMsg.'"}';
}
else 
{
	if(!$storeno)//auto generate
	{
		$min = pow(10, 6 - 1) ;
		$max = pow(10, 6) - 1;
		$storeno = mt_rand($min, $max);        			
		while(!checkUnique($storeno))
		{
			$min = pow(10, 6 - 1) ;
			$max = pow(10, 6) - 1;
			$storeno = mt_rand($min, $max);  						
		}		
	}
	
	//check if store# exists
	$query = "select * from location where branch_id = '$storeno'";
	$res = db_query($query);
	$num = db_numrows($res);
	
	if($num > 0)
	{
		$errMsg = "This store is already registered for access.";
		echo '{"success":false,"msg":"'.$errMsg.'"}';   		
	}
	else 
	{
		db_query(_BEGIN);
		$query = "INSERT INTO `location` (`branch_id`, `business_name`, `entity`, `business_unit`, `hospital`, `sname`, `address`, `suburb`, `state`, `postcode`, `country`, `stype`, `phone`, `fax`, `email`, `status`) VALUES
				 ('$storeno', \"$storename\", \"$storename\", \"$storename\", 'N', \"$storename\", \"$address\", \"$suburb\", '$state', '$postcode', 'AU', 'Pharmacy', '$phone', NULL, \"$email\", 'ACTIVE')";
		$res = db_query($query);
		$location_id = "";
		
		if($res)
		{
			$location_id = mysql_insert_id();
     		$query = "INSERT INTO `login` (`password`,`location_id`, `user_name`, `firstname`, `lastname`, `access_level`, `email`, `jurisdiction`, `allowance`, `allowance2`, `realm`, `role_id`, `crange`, `job_classification`, `status`, `isAUS`, `daysworked`, `start_date`) VALUES 
						('temporary password only until approved', $location_id, '$storeno', \"$firstname\", \"$lastname\", '2', \"$email\", 'CHEMMART', '0', '0', 'GPC', '1', '1', 'CHEMMART', 'INACTIVE', 'Y', '5', '2017-02-06')";
     		$res = db_query($query);
			
			if($res)
			{
				
				$htmlBody = "<html><body><b>TERRY WHITE CHEMMART REGISTRATION DETAILS</b><br/>";
				$htmlBody .="Store Number: $storeno<br/>";
				$htmlBody .="Store Name: $storename<br/>";
				$htmlBody .="Address: $address<br/>";		
				$htmlBody .="Suburb: $suburb<br/>";		
				$htmlBody .="State: $state<br/>";		
				$htmlBody .="Postcode: $postcode<br/>";		
				$htmlBody .="Contact Name: $fullname<br/>";		
				$htmlBody .="Work Phone: $phone<br/>";		
				$htmlBody .="Email: $email<br/>";		
								
				//email
			    $mail = new PHPMailer();
		
		      $mail->IsSMTP(); // telling the class to use SMTP
		      $mail->SMTPKeepAlive = true;
		
		      //$mail->Host = "mail.bigpond.com"; // SMTP server
		      $mail->Host = _MAILHOST; // SMTP server
		      $mail->FromName = "Designs To You";
		      $mail->From = "sales@designstoyou.com.au";
		
		      $mail->AddBCC("c.cao@designstoyou.com.au");
		      $mail->AddBCC("m.grossi@designstoyou.com.au");	
		      $mail->AddBCC("d.grossi@designstoyou.com.au");			      		      
				$mail->AddBCC("sales@designstoyou.com.au");				      		      
		
			   $mail->Subject = "DTY/Terry White Chemmart Registration - $storename $storeno";
			   
		      $mail->Body = $htmlBody;
		      $mail->isHTML(true);
		
		      if(!$mail->Send())
		      {
      			db_query(_ROLLBACK);
		         $mail->SmtpClose();
					$errMsg = "Registration failed, please try again.";
					echo '{"success":false,"msg":"'.$errMsg.'"}';         
		      }
		      else
		      {
		      	db_query(_COMMIT);
		         $mail->SmtpClose();
					$successMsg = "$company $firstname $lastname $contact $email";
					echo '{"success":true,"msg":"'.$successMsg.'"}';         
		      }
			}	
			else
			{
				db_query(_ROLLBACK);
				$errMsg = "Registration failed, please try again.";
				echo '{"success":false,"msg":"'.$errMsg.'"}';    				
			}		
		}
		else 
		{
			db_query(_ROLLBACK);
			$errMsg = "Registration failed, please try again.";
			echo '{"success":false,"msg":"'.$errMsg.'"}';    			
		}
	}

}

?>