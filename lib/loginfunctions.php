<?php
define('WP_MEMORY_LIMIT', '64M');

$home = dirname(__FILE__) . "/../";
$lib = $home ."lib/";
require_once($lib . 'database.php');
require_once($lib . 'functions.php');
require_once($home . '/globals.php');
require_once($lib . 'dbfunctions.php');
$hidden_hash_var='ifonlytheyknewthiskey768';

function minAccessLevel($level)
{
   if(strlen($_SESSION[_ACCESS_LEVEL]) == 0)
      return false;
      //include('../logout.php');
   //allowed
   if($_SESSION[_ACCESS_LEVEL] <= $level)
      return true;
   else
      return false;
}

function process_login()
{
   //if appID is from mycricket goto decrypt, else use db
   $appID = _checkIsSet(_APP_ID);

   if($appID == _MYCRICKET)
   {
      return decrypt();
   }
   else
   {
      //get username/password, if it doesnt exist use the signature/hash method
      $username = _checkIsSet(_USER_NAME);
      $password = _checkIsSet(_PASSWORD);
      return user_login($username, $password);
   }
}

function anotherDecrypt($data, $key)
{
   $data = urldecode($data);
   $input = base64_decode($data);
   $key2 = md5(utf8_encode($key), true);
   $key_add = 24-strlen($key2);
   $key2 .= substr($key2,0,$key_add);   // append the first 8 bytes onto the end
   $dd = mcrypt_decrypt(MCRYPT_TRIPLEDES, $key2, $input, MCRYPT_MODE_ECB);

   //clean up the text
   $block = mcrypt_get_block_size(MCRYPT_TRIPLEDES, MCRYPT_MODE_ECB);
   $packing = ord($dd{strlen($dd) - 1});
   if($packing and ($packing < $block))
   {
      for($P = strlen($dd) - 1; $P >= strlen($dd) - $packing; $P--)
      {
         if(ord($dd{$P}) != $packing)
         {
            $packing = 0;
         }
      }
   }

   $dd = substr($dd,0,strlen($dd) - $packing);
   $dd = rtrim($dd);
   return $dd;
   

//     //Generate a key from a hash
//     $key = md5(utf8_encode($key), true);

//     //Take first 8 bytes of $key and append them to the end of $key.
//     $key .= substr($key, 0, 8);

//     $data = base64_decode($data);

//     $data = mcrypt_decrypt('tripledes', $key, $data, 'ecb');

//     $block = mcrypt_get_block_size('tripledes', 'ecb');
//     $len = strlen($data);
//     $pad = ord($data[$len-1]);

//     return substr($data, 0, strlen($data) - $pad);	
	
}

function testEncrypt($data, $key)
{
  //Generate a key from a hash
  $key = md5(utf8_encode($key), true);

  //Take first 8 bytes of $key and append them to the end of $key.
  $key .= substr($key, 0, 8);

  //Pad for PKCS7
  $blockSize = mcrypt_get_block_size('tripledes', 'ecb');
  $len = strlen($data);
  $pad = $blockSize - ($len % $blockSize);
  $data .= str_repeat(chr($pad), $pad);

  //Encrypt data
  $encData = mcrypt_encrypt('tripledes', $key, $data, 'ecb');

  return base64_encode($encData);
  
/*
   $key2 = md5(utf8_encode($key), true);
   $key_add = 24-strlen($key2);
   $key2 .= substr($key2,0,$key_add);   // append the first 8 bytes onto the end
   $c_t = ((mcrypt_encrypt(MCRYPT_TRIPLEDES, $key2, utf8_encode($data), MCRYPT_MODE_ECB)));
   $c_t = base64_encode($c_t);
   $c_t = urlencode($c_t);
*/
//   return $c_t;
}

function decryptIgnoreTime() // NEED DATETIME TO BE SENT AS WELL!!!
{
   //$entityID = _checkIsSet("userID");
   $entityName = _checkIsSet("entityName");
   $userID = _checkIsSet("userID"); //(int)
   $userName = _checkIsSet(_USER_NAME);
   $state = _checkIsSet(_STATE);
   $encrypted = urlencode(_checkIsSet("id"));
   $appID = _checkIsSet("appID");
   $ts = _checkIsSet("timestamp");

  	$data = "$ts|$appID|$entityName|$userID";
  	/*
   //$data = "2016-09-15T00:18:43.611Z|DTYLINK|Chemmart|1166369";
   echo "data to enc: [$data]<BR>";
   echo "ENC: " . urldecode(testEncrypt($data, "TBC")) . "<BR>";
   echo "CHM: " . urldecode($encrypted) . "<BR>";
	*/
   
  	$enc = urldecode(testEncrypt($data, "TBC"));
  	
   if($enc == urldecode($encrypted))
   {
      //$data = "$timeSent|$appID|$entityID|$userID";
      $dd = anotherDecrypt($encrypted, "TBC");
      $ddArr = explode("|", $dd);
      //print_r($ddArr);
      //successfully decrypted
      //if($ddArr[1] == "DTYLINK" && $ddArr[2] == $userID)
      {
         //check time
         //get current unix time;
         $timeSent = $ddArr[0];
         $u_time = time() - date('Z');

         $t_valid_sec = _TVALID_FOR * 60; //_TVALID_FOR = 60 mins
         list($dateStr, $timeStr) = preg_split("/[TZ]/", $timeSent);
         list($year, $month, $day) = preg_split("/-/", $dateStr);
         list($hr, $min, $sec) = preg_split("/:/", $timeStr);
//         echo"<BR>TIME: ". date('Y-m-d H:i:s', $u_time) . "<BR>";
//         $u_timesent = mktime($hr, $min, $sec, $month, $day, $year);
//         $u_timesent += $t_valid_sec; // add 60 mins, if < then now, expired.
//         echo"<BR>TIMESENT: ". date('Y-m-d H:i:s', $u_timesent) . "<BR>";
//         if($u_timesent < $u_time)
//         {
//            $_SESSION['msg'] = "Access Denied. Your session has expired.";
//            return false;
//         }
//         else 
         {
            $_SESSION['msg']  = 'SUCCESS - Logging in now.... ';
            //if ok check access levels and set tokens
            if($entityID == "1")
               $accessLevel = _ADMIN_LEVEL;
            else
               $accessLevel = _USER_LEVEL;

            $jurisdiction = "GPC";
            $realm = "GPC";
            user_set_tokens($userName, $userID, $entityName, $jurisdiction, $accessLevel,$realm,"");
            return true;
         }
      }

   }
      else
      {
         $_SESSION['msg'] = "Access Denied.";
         return false;
      }
}

function setEmail($jur)
{
   $query = "select * from login where jurisdiction = $jur";
   $res = db_query($query);
   $num = db_numrows($res);

   if($num > 0)
   {
      $email = db_result($res, 0, _EMAIL);
      if(!$email)
         $email = "c.cao@designstoyou.com.au";
//echo "EMAIL: $email<BR>";
      $_SESSION[_EMAIL] = $email;
   }
   else //set it with my default email
      $_SESSION[_EMAIL] = "c.cao@designstoyou.com.au";


}

function decrypt() // NEED DATETIME TO BE SENT AS WELL!!!
{
   $entityID = _checkIsSet(_ENTITY_ID);
   $entityName = _checkIsSet(_ENTITY_NAME);
   $userID = _checkIsSet(_USER_ID); //(int)
   $userName = _checkIsSet(_USER_NAME);
   $state = _checkIsSet(_STATE);
   $encrypted = urlencode(_checkIsSet(_ENCRYPTED_DIGEST));
   $appID = _checkIsSet(_APP_ID);

   $data = "2009-08-08T23:31:38|$appID|$entityID|$userID";
   //echo urlencode(testEncrypt($data, _KEY)) . "<BR>";
//
//
//return false;
   if($appID == _MYCRICKET)
   {
      //$data = "$timeSent|$appID|$entityID|$userID";
      $dd = anotherDecrypt($encrypted, _KEY);
      //echo "$dd<BR>[$encrypted]<BR>";
      $ddArr = explode("|", $dd);
      //print_r($ddArr);
      //successfully decrypted
      if($ddArr[1] == _MYCRICKET && $ddArr[2] == $entityID && $ddArr[3] == $userID)
      {
         //check time
         //get current unix time;
         $timeSent = $ddArr[0];
         $u_time = time() - date('Z');

         $t_valid_sec = _TVALID_FOR * 60; //_TVALID_FOR = 60 mins
         list($dateStr, $timeStr) = split("[TZ]", $timeSent);
         list($year, $month, $day) = split("-", $dateStr);
         list($hr, $min, $sec) = split(":", $timeStr);
//         echo"<BR>TIME: ". date('Y-m-d H:i:s', $u_time) . "<BR>";
         $u_timesent = mktime($hr, $min, $sec, $month, $day, $year);
         $u_timesent += $t_valid_sec; // add 60 mins, if < then now, expired.
//         echo"<BR>TIMESENT: ". date('Y-m-d H:i:s', $u_timesent) . "<BR>";
         if($u_timesent < $u_time)
         {
            $_SESSION['msg'] = "Access Denied. Your session has expired.";
            return false;
         }
         else
         {
            $_SESSION['msg']  = 'SUCCESS - Logging in now.... ';
            //if ok check access levels and set tokens
//            if($entityID == "99999" || $entityID == "1")
//               $accessLevel = _ADMIN_LEVEL;
//            else
               $accessLevel = _USER_LEVEL;

            //find the last order id and use the jurisdiction set there
            //if no order, just use the state as jurisdiciton
            $query = "select * from orders where centre_id = $entityID order by order_id desc";
            $res = db_query($query);
            $num = db_numrows($res);
            $jurisdiction = "";
            if($num > 0)
            {
               $jurisdiction = db_result($res, 0, "jurisdiction");
            }
            else
               $jurisdiction = encodeState($state);

            if($jurisdiction == -1)
            {
               $_SESSION['msg'] = "The link used does not contain the correct information to log you in.";
               return false;
            }

            //store the email address based on jurisdiction
            setEmail($jurisdiction);

//            if($state == _VIC)
//               $jurisdiction = _VIC_CODE;

            user_set_tokens($userName, $entityID, $entityName, $userID, $jurisdiction, $accessLevel);
            return true;
         }
      }
      else
      {
         $_SESSION['msg'] = "Access Denied.";
         return false;
      }
   }
   else
      return false;
}

function user_isloggedin()
{
   global $user_id,$id_hash,$hidden_hash_var,$LOGGED_IN;
   if($_SESSION["REALM"])
   {
      if($_SESSION["REALM"] != _REALM)
      {
         user_logout();
      }
   }

//   if(strlen($_SESSION[_ACCESS_LEVEL]) == 0)
//      include(_CUR_HOST. _DIR . 'logout.php');


  $user_id = strtolower($_SESSION[_USER_ID]);
  $id_hash = $_SESSION['id_hash'];

   //have we already run the hash checks?
   //If so, return the pre-set var
//   if (isset($LOGGED_IN))
//   {
//      echo "here2<BR>";
//      return true;
//   }
   if ($user_id && $id_hash)
   {
      $hash=md5($user_id.$hidden_hash_var);
      if ($hash == $id_hash)
      {
         $LOGGED_IN=true;
         return true;
      }
      else
      {
         $LOGGED_IN=false;
         return false;
      }
   }
   else
   {
      $LOGGED_IN=false;
      return false;
   }
}

function user_getid()
{
   return $_SESSION[_USER_ID];
}

// function user_set_tokens($user_name_in, $firstname, $lastname, $user_id, $location_id, $email, $jurisdiction, $accessLevel, $allowance, $realm, $crange, $daysworked, $roleName, $branch_id)
function user_set_tokens($user_name_in, $userID, $entityName, $jurisdiction, $accessLevel, $realm, $location_id)
{
   global $hidden_hash_var,$userName,$id_hash;
   if(strlen($userID) ==0) 
   {
      $feedback .=  'Invalid User name.';
      return false;
   }
   //$user_name=strtolower($user_name_in);
   $id_hash= md5($userID.$hidden_hash_var);

   $_SESSION[_USER_ID] = $userID;
   $_SESSION[_LOCATION_ID] = $location_id;
   $_SESSION[_USER_NAME] = $user_name_in;
   $_SESSION[_JURISDICTION] = $jurisdiction;
   $_SESSION[_ACCESS_LEVEL] = $accessLevel;
   $_SESSION["REALM"] = $realm;
   $_SESSION['id_hash'] = $id_hash;
//   $_SESSION["ROLE_ID"] = 1;
//   $_SESSION["crange"] = 1;

}

function user_login($user_name,$password, $field)
{
   global $feedback;
   if (!$user_name || !$password)
   {
      $_SESSION['msg'] = "The username or password you entered is incorrect.";
      return false;
   }
   else
   {
      $user_name=strtolower($user_name);
//       if($user_name != "1")
//          $password = strtolower($password);
      
//          echo "PASS: $password<BR>";
      $sql="SELECT *,l.email as lemail FROM login l, location l1 WHERE $field='$user_name' AND password='". md5($password) ."' and l.location_id = l1.location_id";
//       echo "$sql<BR>";
      $result=db_query($sql);
      if (!$result || db_numrows($result) < 1)
      {
         $_SESSION['msg']  = 'The username or password you entered is incorrect.';
         return false;
      }
      else
      {
         $user_id = db_result($result, 0, _USER_ID);
         $user_name = db_result($result, 0, "user_name");
         $location_id = db_result($result, 0, _LOCATION_ID);
         $jurisdiction = db_result($result, 0, _JURISDICTION);
         $accessLevel = db_result($result, 0, _ACCESS_LEVEL);
         $firstname = db_result($result, 0, _FIRST_NAME);
         $lastname = db_result($result, 0, _LAST_NAME);
         $email = db_result($result, 0, "lemail");
         $allowance2 = db_result($result, 0, "allowance2");
         $branch_id = db_result($result,0, "branch_id");
         $realm = db_result($result, 0, "realm");
         $role_id = db_result($result, 0, "role_id");
         $crange = db_result($result, 0, "crange");
         $daysworked = db_result($result, 0, "daysworked");
         $status = db_result($result, 0, "status");
         $isAUS = db_result($result, 0, "isAUS");

         if($status == "INACTIVE")
         {
            $_SESSION['msg']  = 'The username or password you entered is incorrect.';
            return false;
         }

         $_SESSION["ROLE_ID"] = $role_id;
         $_SESSION[_EMAIL] = $email;
         $_SESSION[_FIRST_NAME]  = $firstname;
         $_SESSION[_LAST_NAME]  = $lastname;
        $roleName = $role_id;
//         $email = db_result($result, 0, _EMAIL);
//         $_SESSION[_EMAIL] = $email;
//         user_set_tokens($user_name, $firstname, $lastname, $user_id, $location_id, $email, $jurisdiction, $accessLevel, $allowance, $realm, $crange, $daysworked, $roleName, $branch_id);
         user_set_tokens($user_name, $user_id, $firstname, $jurisdiction, $accessLevel, $realm, $location_id);
         check_allowance($user_id, $role_id, $daysworked);
         //$_SESSION['msg']  = 'SUCCESS - Logging in now.... ';
         return true;
      }
   }
}

function check_allowance($user_id, $role_id, $daysworked)
{
   $query = "select * from allowance where user_id = $user_id order by allowance_id desc";
   $res = db_query($query);
   $num = db_numrows($res);

   if($num > 0)
   {
      $curDate = date('Y-m-d');
      $expiry = db_result($res, 0, "end");

      //echo "cur: $curDate ex: $expiry<BR>";

      //expiry + 1 day
      $start = strtotime(date("Y-m-d", strtotime($expiry)) . "+1 day");
      $start = date('Y-m-d', $start);
      //expiry + 1 year;
      $end = strtotime(date("Y-m-d", strtotime($expiry)) . "+1 year");
      $end = date('Y-m-d', $end);
      //if expired
      if($expiry < $curDate)
      {
         $allowanceAmtQuery = "select * from role_rules where role_id = $role_id and status= '$daysworked' ";
         $allowanceRes = db_query($allowanceAmtQuery);
         $allowanceNum = db_numrows($allowanceRes);
         $allowance2 = 0;
         if($allowanceNum > 0)
         {
            $allowance2 = db_result($allowanceRes,0, "allowance");
            
         }
         //add allowance!!
         $query = "insert into allowance(`user_id`, `allowance`, `start`, `end`) values ($user_id, $allowance2, '$start', '$end')";
            //echo "$query<BR>";
         db_query($query);
      }

   }
}

function user_logout()
{
   unset($_SESSION[_USER_ID]);
   unset($_SESSION[_USER_NAME]);
   unset($_SESSION[_LOCATION_ID]);
   unset($_SESSION[_JURISDICTION]);
   unset($_SESSION[_ACCESS_LEVEL]);
   unset($_SESSION[_FIRST_NAME]);
   unset($_SESSION[_LAST_NAME]);
   unset($_SESSION[_EMAIL]);
   unset($_SESSION[_ALLOWANCE]);
   unset($_SESSION["REALM"]);
   unset($_SESSION["isAUS"]);
   unset($_SESSION['id_hash']);

   //destroy the session
   $_SESSION = array();
   //setcookie (session_name(), '', time()-300, '/', '', 0);
   setcookie(session_name(), '', time()+(7*24*3600), "/; SameSite=None; Secure");
   @session_destroy();
}
?>
