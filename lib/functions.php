<?php
error_reporting(E_ALL ^ E_NOTICE);
$hidden_hash_var='ifonlytheyknewthiskey768';


$LOGGED_IN=false;
//clear it out in case someone sets it in the URL or something
unset($LOGGED_IN);

	function formatNumber($number)
	{
		$number = number_format($number, 2, ".", ",");
		return $number;
	}

	function unformatNumber($number)
	{
		//$number = number_format($number, 2, ".", "");
      $number= preg_replace('/[\$,]/', '', $number);
		return $number;
	}

   function decodeState($state)
   {
      if($state == _VIC_CODE)
         return _VIC;
      else if($state == _NSW_CODE)
         return _NSW;
      else if($state == _SA_CODE)
         return _SA;
      else if($state == _QLD_CODE)
         return _QLD;
      else if($state == _WA_CODE)
         return _WA;
      else if($state == _TAS_CODE)
         return _TAS;
      else if($state == _ACT_CODE)
         return _ACT;
      else if($state == _NT_CODE)
         return _NT;
      else if($state == _CA_CODE)
         return _CA;
      else
         return -1;
   }

   function encodeState($state)
   {
      $state = strtoupper($state);
      if($state == _VIC)
         return _VIC_CODE;
      else if($state == _NSW)
         return _NSW_CODE;
      else if($state == _SA)
         return _SA_CODE;
      else if($state == _QLD)
         return _QLD_CODE;
      else if($state == _WA)
         return _WA_CODE;
      else if($state == _TAS)
         return _TAS_CODE;
      else if($state == _ACT)
         return _ACT_CODE;
      else if($state == _NT)
         return _NT_CODE;
      else if($state == _CA)
         return _CA_CODE;
      else
         return -1;
   }

   function productInfo($prod_id, $field)
   {
      $query = "select $field from products where prod_id = $prod_id";
      $res = db_query($query);
      $num = db_numrows($res);
      if($num > 0)
         return db_result($res, 0, $field);
      else
         return "N/A";
   }

   function productInfoLineItem($lineitem_id, $field)
   {
      $query = "select $field from lineitems where lineitem_id = $lineitem_id";
      $res = db_query($query);
      $num = db_numrows($res);
      if($num > 0)
         return db_result($res, 0, $field);
      else
         return "N/A";
   }

   function buildQueryOrders()
   {
      $jurisdiction = _checkIsSet(_SEARCH_JURISDICTION);
      $status = _checkIsSet(_SEARCH_STATUS);
      $todate = _checkIsSet(_TODATE);
      $fromdate = _checkIsSet(_FROMDATE);
      $centre_id = _checkIsSet(_SEARCH_CENTRE_ID);
      $searchOrderId = _checkIsSet("searchOrderID");

      if(!$todate)//check for session
         if($_SESSION[_TODATE])
            $todate = $_SESSION[_TODATE];

      if(!$fromdate)//check for session
         if($_SESSION[_FROMDATE])
            $fromdate = $_SESSION[_FROMDATE];

      //set default dates
      if(!$todate)
         $todate = date('Y-m-t');
      if(!$fromdate)
         $fromdate = date('Y-m') . "-01";

      $query = "select * from orders where order_id != ''";
      //admin and s/t
      if(minAccessLevel(_ADMIN_LEVEL))
      {
         if($jurisdiction)
            $query .= " and jurisdiction = $jurisdiction";

         if($centre_id)
            $query .= " and centre_id = $centre_id";
      }
      else if(minAccessLevel(_S_T_LEVEL))
      {
         if($centre_id)
            $query .= " and centre_id = $centre_id";

         $query .= " and jurisdiction = " . $_SESSION[_JURISDICTION];
      }
      else
      {
         $query .= " and jurisdiction = " . $_SESSION[_JURISDICTION];
         $centre_id = $_SESSION[_ENTITY_ID];
         $query .= " and centre_id = $centre_id";
      }

      if($status)
         $query .= " and status = '$status'";

      if($todate && $fromdate)
         $query .= " and ordertime between '$fromdate' and '$todate'";

      if($searchOrderId)
         $query .= " and order_id = $searchOrderId";

      $query .= " order by order_id asc";
      return $query;
   }

   function buildQueryReports()
   {
      $jurisdiction = _checkIsSet(_SEARCH_JURISDICTION);
      $status = _checkIsSet(_SEARCH_STATUS);
      $todate = _checkIsSet(_TODATE);
      $fromdate = _checkIsSet(_FROMDATE);
      $centre_id = _checkIsSet(_CENTRE_ID);

      //set default dates
      if(!$todate)
         $todate = date('Y-m-t');
      if(!$fromdate)
         $fromdate = date('Y-m') . "-01";

      $query = "select * from orders where order_id != '' and (status = 'DESPATCHED' or status = 'PROCESSING')";
      //admin and s/t
      if(minAccessLevel(_ADMIN_LEVEL))
      {
         if($jurisdiction)
            $query .= " and jurisdiction = $jurisdiction";

         if($centre_id)
            $query .= " and centre_id = $centre_id";
      }
      else if(minAccessLevel(_S_T_LEVEL))
      {
         if($centre_id)
            $query .= " and centre_id = $centre_id";

         $query .= " and jurisdiction = " . $_SESSION[_JURISDICTION];
      }
      else
      {
//         $query .= " and jurisdiction = " . $_SESSION[_JURISDICTION];
         $query .= " and centre_id = " . $_SESSION[_ENTITY_ID];
      }

      if($status)
         $query .= " and status = '$status'";

      if($todate && $fromdate)
         $query .= " and ordertime between '$fromdate' and '$todate'";



      $query .= " order by centre_name asc";
//echo "$query<BR>";
      return $query;
   }

   function buildQueryCentres()
   {


      $query = "select * from centres where centre_id != ''";

      if(minAccessLevel(_ADMIN_LEVEL))
      {
         $jur = _checkIsSet(_SEARCH_JURISDICTION);
         if($jur)
            $query .= " and jurisdiction = $jur";
      }
      else
      {
         $jur = $_SESSION[_JURISDICTION];
         $query .= " and jurisdiction = $jur";
      }

      $query .= " order by jurisdiction asc, centre_name asc";

      return $query;
   }

   //packs
   function buildQueryPacks()
   {
      $jur = _checkIsSet(_SEARCH_JURISDICTION);
      $p_id = _checkIsSet(_SEARCH_PRODUCT_ID);

      $query = "select * from packs where pack_id != ''";

      if($jur)
         $query .= " and jurisdiction = $jur";

      if($p_id)
         $query .= " and prod_id = $p_id";

      $query .= " order by jurisdiction, prod_id, mod_value asc";

      return $query;
   }

   //products list
   function buildProductsQuery()
   {
      $query = "select * from products";
      return $query;
   }

	//create the query
//	function buildUniqueQuery($type)
//	{
//		$cur_month = date("m");
//		$cur_year = date("Y");
//		$last_day = date("t");
//
//		$first_date = $cur_year . "-" . $cur_month . "-" . "01";
//		$last_date = $cur_year . "-" . $cur_month . "-" . $last_day;
//		$query = "";
//
//		$jurisdiction = _checkIsSet(_JURISDICTION);
////		$jurisdiction = decodeState($jurisdiction);
//		if($jurisdiction < 0)
//			$jurisdiction = false;
//
//		$todate = _checkIsSet('todate');
//		if(!$todate)
//			$todate = $last_date;
//		$fromdate = _checkIsSet('fromdate');
//		if(!$fromdate)
//			$fromdate = $first_date;
//
//		$centreId = _checkIsSet(_CENTRE_ID);
//		if($centreId == "n/a")
//			$centreId = false;
//
//		if($type == _BUILD_QUERY_ORDER)
//		{
//			//if admin find all
//			if(user_getlevel() == _ADMIN_LEVEL)
//			{
//            $query = "select distinct o.centre_id from orders o, centres c where o.order_date between '$fromdate' and '$todate' and o.centre_id = c.centre_id";
//
//            if($centreId)
//            {
//               $query .= ' and o.centre_id = '.$centreId.'';
//            }
//
//            if($jurisdiction)
//               $query .= " and c.jurisdiction = upper('$jurisdiction')";
//
//            $query .= " order by o.centre_id asc";
//			}
//		}
////      echo "$query<BR>";
//		return $query;
//	}

	//create the query
//	function buildQuery($type)
//	{
//		$cur_month = date("m");
//		$cur_year = date("Y");
//		$last_day = date("t");
//
//		$first_date = $cur_year . "-" . $cur_month . "-" . "01";
//		$last_date = $cur_year . "-" . $cur_month . "-" . $last_day;
//		$query = "";
//
//		$status = _checkIsSet('status');
//
//		$todate = _checkIsSet('todate');
//		if(!$todate)
//			$todate = $last_date;
//		$fromdate = _checkIsSet('fromdate');
//		if(!$fromdate)
//			$fromdate = $first_date;
//
//		$centre = _checkIsSet(_CENTRE_ID);
//		if($centre == "n/a")
//			$centre = false;
//
//		$jurisdiction = _checkIsSet(_JURISDICTION);
//
//		if($type == _BUILD_QUERY_ORDER)
//		{
//			//if admin find all
//			if(user_getlevel() == _ADMIN_LEVEL)
//			{
//
//            $query = "select * from orders o, centres c where o.order_date between '$fromdate' and '$todate' and o.centre_id = c.centre_id";
//
//            if($centre)
//               $query .= " and o.centre_id = '$centre'";
//
//            if($status)
//               $query .= " and o.status = '$status'";
//
//            if($jurisdiction)
//               $query .= " and c.jurisdiction = '$jurisdiction'";
//
//            $query .= ' order by order_date desc';
//			}
//			else //normal user
//			{
//            $staterep = user_getStateRep();
//            $query = "select * from orders o, centres c where o.order_date between '$fromdate' and '$todate' and o.centre_id = c.centre_id and c.jurisdiction = '$staterep'";
//            if($centre)
//               $query .= " and o.centre_id = '$centre'";
//
//            if($status)
//               $query .= " and o.status = '$status'";
//			}
//		}
//		else if($type == _BULID_QUERY_CENTRE)
//		{
//			$min = _checkIsSet("min");
//			$max = _checkIsSet("max");
//			$goto = _checkIsSet(_GOTO);
//			$incr = 10;
//			if(!$min)
//				$min = 0;
//			if($goto)
//			{
//				//since page 1 starts at 0 in limit for mysql, need to change it to 0 so the first 10 items can be displayed
//				$tmp = $goto;
//				if($goto == 1)
//				{
//					$min = 0;
//					$tmp = 0;
//				}
//				else
//					$tmp -= 1;
//				//multiply the page by the increment to get start of min for limit
//				$min = $tmp * $incr;
//			}
//
//			$jumpto = _checkIsSet("jumpto");
//			if($jumpto == _NEXT)
//			{
//				$goto = "";
//				$min += $incr;
//				$curPage += 1;
//			}
//			else if($jumpto == _PREVIOUS)
//			{
//				$goto = "";
//				$min -= $incr;
//				$curPage -= 1;
//			}
//			if($min < 0)
//				$min = 0;
//			//if admin find all
//			if(user_getlevel() == _ADMIN_LEVEL)
//			{
//            $jurisdiction = _checkIsSet(_JURISDICTION);
//            if($jurisdiction != $_SESSION[_DEFAULT_JUR])
//               $min = 0;
//
//            $query = "select * from centres where centre_id != ''";
//            if($centre)
//               $query .= " and centre_id = $centre";
//            if($jurisdiction)
//               $query .= " and jurisdiction = '$jurisdiction'";
//			}
//			else //normal user
//			{
//				$stateRep = user_getStateRep();
//            $query = "select * from centres where jurisdiction = '$stateRep'";
//				if($centre)
//               $query .= " and centre_id = $centre";
//			}
//         $query .= "  order by centre_name asc limit $min, $incr";
//		}
////		echo "buildquery: $query<br>";
//		return $query;
//	}

//   function getCurrentDate()
//   {
//		$date2_year = date("Y"); //Gets Current Year
//		$date2_month = date("m"); //Gets Current Month
//		$date2_day = date("d"); //Gets Current Day
//
//		$dateval = "$date2_year-$date2_month-$date2_day";
//		return $dateval;
//   }
//
//   function generateCombo4($name, $query, $default, $textInputName, $rowId)
//   {
//      $result = db_query($query);
//
//    	$onchange = 'this.form.'.$textInputName.'.value=this.form.'.$name.'[this.selectedIndex].value;this.form.submit()';
//
//      print("<select name=\"$name\" onChange=\"$onchange\">\n");
//      print("<option value=\"n/a\"></option>\n");
//      while($row = mysql_fetch_array($result))
//      {
//         $row_val = $row[$rowId];
//         $row_val = strtoupper($row_val);
//         $default = strtoupper($default);
//         if($default != "" && $default == $row_val)
//         {
//            print("<option value=\"$row_val\" selected>$row_val\n");
//         }
//         else
//         {
//            print("<option value=\"$row_val\">$row_val\n");
//         }
//      }
//      print("</select>");
//   }

//   function generateCombo3($name, $query, $default, $textInputName)
//   {
//      $result = db_query($query);
//     	$onchange = 'this.form.'.$textInputName.'.value=this.form.'.$name.'[this.selectedIndex].value;this.form.submit()';
//
//      print("<select name=\"$name\" onChange=\"$onchange\">\n");
//      print("<option value=\"n/a\"></option>\n");
//      while($row = mysql_fetch_array($result))
//      {
//         $row_val = $row[0];
//         $id = $row[1];
//         $row_val = strtoupper($row_val);
//         $default = strtoupper($default);
//         if($default != "" && $default == $id)
//         {
//            print("<option value=\"$id\" selected>$row_val\n");
//         }
//         else
//         {
//            print("<option value=\"$id\">$row_val\n");
//         }
//      }
//      print("</select>");
//   }
//
//   function _unset($name1)
//   {
//   	unset ( $_REQUEST[$name1]);
//   }

   function _checkIsSet($name1)
   {
      $setname = "";
		if (!empty($_REQUEST))
		{
			if (!empty($_REQUEST[$name1]))
         {
            $setname=$_REQUEST[$name1];

         }
         else if( $_REQUEST[$name1] === '0')
         {
            $setname=$_REQUEST[$name1];
         }
		}
      return $setname;
   }

//   function createCombo($name, $default, $arr, $textInputName)
//   {
//       	$onchange = 'this.form.'.$textInputName.'.value=this.form.'.$name.'[this.selectedIndex].value;this.form.submit()';
////      	$onchange = 'this.form.'.$textInputName.'.value=this.form.'.$name.'[this.selectedIndex].value;';
//
//         print("<select name=\"$name\" onChange=\"$onchange\">\n");
//
////         $stateArr =  array();
////         $stateArr[0] = "ACT";
////         $stateArr[1] = "NSW";
////         $stateArr[2] = "NT";
////         $stateArr[3] = "QLD";
////         $stateArr[4] = "SA";
////         $stateArr[5] = "TAS";
////         $stateArr[6] = "VIC";
////         $stateArr[7] = "WA";
////
////         if($default == "")
////         	print('<option value="'.$stateArr[6].'" selected>'.$stateArr[6].'');
////         else
//         {
//         	$size = sizeof($arr);
//         	$i = 0;
//         	for($i = 0; $i < $size; $i++)
//         	{
//         		if($default == $arr[$i])
//         			print('<option value="'.$arr[$i].'" selected>'.$arr[$i].'');
//         		else
//         			print('<option value="'.$arr[$i].'">'.$arr[$i].'');
//         	}
//         }
//         print("</select>");
//
//   }

//   function _createFormTable($classType, $textName, $inputType, $inputName, $inputSize, $alignType, $value)
//   {
//      $string = "<tr>\n<td class=\"$classType\" align=\"$alignType\"><b>$textName</b></td>\n
//      <td class=\"$classType\"><input type=\"$inputType\" name=\"$inputName\" value=\"$value\" size=\"$inputSize\"/></td>\n
//      </tr>\n";
//
//      return $string;
//   }
//
//   function _createFormTable2($classType1, $classType2, $textName, $inputType, $inputName, $inputSize, $alignType, $update)
//   {
//      if($update)
//      {
//         $value = _checkIsSet($inputName);
//       }
//      else
//      {
//         $user_id = user_getid();
//         $value = get_info($inputName, $user_id);
//      }
//      //value is empty
//      if($value == "")
//      {
//        $string = "<tr>\n<td class=\"$classType2\" align=\"$alignType\"><b>$textName</b></td>\n
//         <td class=\"$classType\"><input type=\"$inputType\" name=\"$inputName\" value=\"$value\" size=\"$inputSize\"/></td>\n
//         </tr>\n";
//         $_SESSION['allowupdate'] = 0;
//      }
//      else
//      {
//        $string = "<tr>\n<td class=\"$classType1\" align=\"$alignType\"><b>$textName</b></td>\n
//         <td class=\"$classType\"><input type=\"$inputType\" name=\"$inputName\" value=\"$value\" size=\"$inputSize\"/></td>\n
//         </tr>\n";
//         $_SESSION['allowupdate'] = 1;
//      }
//
//      return $string;
//   }
//   function executeQuery($db_name, $query, $hostname, $user, $pass)
//   {
//      $db = mysql_connect($hostname, $user, $pass) or die(mysql_error());
//      mysql_select_db($db_name, $db) or die(mysql_error());
//
//      $result = mysql_query($query, $db) or die(mysql_error());
//
//      //mysql_close($db);
//      return $result;
//   }

function user_getlevel()
{
   $user_id = user_getid();

   $query1 = "select user_level from login where user_id='".$user_id."'";

   $res = db_query($query1);

   if ($res && db_numrows($res) > 0)
   {
      return db_result($res,0,'user_level');
   }
   else
   {
      return 0;
   }
}

//function user_isloggedin() {
//	global $user_name,$id_hash,$hidden_hash_var,$LOGGED_IN;
//
//  $user_name = $_SESSION['user_name'];
//  $id_hash = $_SESSION['id_hash'];
//
//	//have we already run the hash checks?
//	//If so, return the pre-set var
//	if (isset($LOGGED_IN))
//   {
//		return $LOGGED_IN;
//	}
//	if ($user_name && $id_hash)
//   {
//		$hash=md5($user_name.$hidden_hash_var);
//		if ($hash == $id_hash)
//      {
//			$LOGGED_IN=true;
//			return true;
//		}
//      else
//      {
//			$LOGGED_IN=false;
//			return false;
//		}
//	}
//   else
//   {
//      //echo "<h4>4</h4><br>";
//		$LOGGED_IN=false;
//		return false;
//	}
//}

//function user_login($user_name,$password) {
//	global $feedback;
//	if (!$user_name || !$password) {
//		$feedback .=  ' ERROR - Missing user name or password ';
//		return false;
//	} else {
//		$user_name=strtolower($user_name);
////		$password=strtolower($password);
//		$sql="SELECT * FROM login WHERE user_name='$user_name' AND password='". md5($password) ."'";
//
//		$result=db_query($sql);
//		if (!$result || db_numrows($result) < 1){
//			$feedback .=  ' ERROR - User not found or password incorrect ';
//			return false;
//		} else {
//			if (db_result($result,0,'is_confirmed') == '1') {
//				user_set_tokens($user_name);
//				$feedback .=  ' SUCCESS - Logging in now.... ';
//				return true;
//			} else {
//				$feedback .=  ' ERROR - You haven\'t Confirmed Your Account Yet ';
//				return false;
//			}
//		}
//	}
//}

//function user_logout() {
//	//setcookie('user_name','',(time()-2592000),'/','',0);
//	//setcookie('id_hash','',(time()-2592000),'/','',0);
//
//   unset($_SESSION['user_name']);
//   unset($_SESSION['id_hash']);
//
//   //destroy the session
//   $_SESSION = array();
//   setcookie (session_name(), '', time()-300, '/', '', 0);
//   @session_destroy();
//}

//function user_set_tokens($user_name_in) {
//	global $hidden_hash_var,$user_name,$id_hash;
//	if (!$user_name_in) {
//		$feedback .=  ' ERROR - User Name Missing When Setting Tokens ';
//		return false;
//	}
//	$user_name=strtolower($user_name_in);
//	$id_hash= md5($user_name.$hidden_hash_var);
//
//	//setcookie('user_name',$user_name,(time()+3600),'/','',0);
//	//setcookie('id_hash',$id_hash,(time()+3600),'/','',0);
//
//   $_SESSION['user_name'] = $user_name;
//   $_SESSION['id_hash'] = $id_hash;
//}

function user_confirm($hash,$email) {
	/*
		Call this function on the user confirmation page,
		which they arrive at when the click the link in the
		account confirmation email
	*/

	global $feedback,$hidden_hash_var;

	//verify that they didn't tamper with the email address
	$new_hash=md5($email.$hidden_hash_var);
	if ($new_hash && ($new_hash==$hash)) {
		//find this record in the db
		$sql="SELECT * FROM login WHERE confirm_hash='$hash'";
		$result=db_query($sql);
		if (!$result || db_numrows($result) < 1) {
			$feedback .= ' ERROR - Hash Not Found ';
			return false;
		} else {
			//confirm the email and set account to active
			$feedback .= ' User Account Updated! ';

			//user_set_tokens(db_result($result,0,'user_name'));
			$sql="UPDATE login SET dealer_email='$email',is_confirmed='1' WHERE confirm_hash='$hash'";
			$result=db_query($sql);
			return true;
		}
	} else {
		$feedback .= ' HASH INVALID - UPDATE FAILED ';
		return false;
	}
}

function user_change_password ($new_password1,$new_password2,$change_user_name,$old_password) {
	global $feedback;
	//new passwords present and match?
	if ($new_password1 && ($new_password1==$new_password2)) {
		//is this password long enough?
		if (account_pwvalid($new_password1)) {
			//all vars are present?
			if ($change_user_name && $old_password) {
				//lower case everything
				$change_user_name=strtolower($change_user_name);
				$old_password=strtolower($old_password);
				$new_password1=strtolower($new_password1);
				$sql="SELECT * FROM login WHERE user_name='$change_user_name' AND password='". md5($old_password) ."'";
				$result=db_query($sql);
				if (!$result || db_numrows($result) < 1) {
					$feedback .= ' User not found or bad password '.db_error();
					return false;
				} else {
					$sql="UPDATE login SET password='". md5($new_password1). "' ".
						"WHERE user_name='$change_user_name' AND password='". md5($old_password). "'";
					$result=db_query($sql);
					if (!$result || db_affected_rows($result) < 1) {
						$feedback .= ' NOTHING Changed '.db_error();
						return false;
					} else {
						$feedback .= ' Password Changed ';
						return true;
					}
				}
			} else {
				$feedback .= ' Must Provide User Name And Old Password ';
				return false;
			}
		} else {
			$feedback .= ' New Passwords Doesn\'t Meet Criteria ';
			return false;
		}
	} else {
		return false;
		$feedback .= ' New Passwords Must Match ';
	}
}

function user_lost_password ($email,$user_name) {
	global $feedback,$hidden_hash_var;
	if ($email && $user_name) {
		$user_name=strtolower($user_name);
		$sql="SELECT * FROM login WHERE user_name='$user_name' AND dealer_email='$email'";
		$result=db_query($sql);
		if (!$result || db_numrows($result) < 1) {
			//no matching user found
			$feedback .= ' ERROR - Incorrect User Name Or Email Address ';
			return false;
		} else {
			//create a secure, new password
			$new_pass=strtolower(substr(md5(time().$user_name.$hidden_hash_var),1,14));

			//update the database to include the new password
			$sql="UPDATE login SET password='". md5($new_pass) ."' WHERE user_name='$user_name'";
			$result=db_query($sql);

			//send a simple email with the new password
			mail ($email,'Password Reset','Your Password '.
				'has been reset to: '.$new_pass,'From: noreply@company.com');
			$feedback .= ' Your new password has been emailed to you. ';
			return true;
		}
	} else {
		$feedback .= ' ERROR - User Name and Email Address Are Required ';
		return false;
	}
}

function user_change_email ($password1,$new_email,$user_name) {
	global $feedback,$hidden_hash_var;
	if (validate_email($new_email)) {
		$hash=md5($new_email.$hidden_hash_var);
		//change the confirm hash in the db but not the email -
		//send out a new confirm email with a new hash
		$user_name=strtolower($user_name);
		$password1=strtolower($password1);
		$sql="UPDATE login SET confirm_hash='$hash' WHERE user_name='$user_name' AND password='". md5($password1) ."'";
		$result=db_query($sql);
		if (!$result || db_affected_rows($result) < 1) {
			$feedback .= ' ERROR - Incorrect User Name Or Password ';
			return false;
		} else {
			$feedback .= ' Confirmation Sent ';
			user_send_change_email($new_email,$hash);
			return true;
		}
	} else {
		$feedback .= ' New Email Address Appears Invalid ';
		return false;
	}
}

//function user_send_confirm_email($email,$hash) {
//	/*
//		Used in the initial registration function
//		as well as the change email address function
//	*/
//
//   $adminmsg = "$email registered: -
//\nPlease click on the following link to activate your account.\n
//\n\nhttp://www.qditech.com.au/login/confirm.php?hash=$hash&email=". urlencode($email);
//
//	$message = "Thank You For Registering at www.qditech.com.au".
//		"\nYou should also receive a confirmation email once your application is approved: ".
//
//		"\n\nOnce you approved, you can use the services available on the QDItech web site.";
//	mail ($email,'QDItech Registration Successful',$message,'From: noreply@qditech.com.au');
//   mail ('ccao@qditech.com.au','QDItech Account Activation'.$email,$adminmsg,'From: noreply@qditech.com.au');
//}
//
//function user_send_change_email($email,$hash) {
//   /*
//      Used in the initial registration function
//      as well as the change email address function
//   */
//
//   $adminmsg = "\n\nhttp://www.qditech.com.au/login/confirm.php?hash=$hash&email=". urlencode($email);
//
//   $message = "You have changed your email address and will need to reactivate this account. Please click on the link below to reactivate your account.".
//
//      "\n\n$adminmsg\n";
//   mail ($email,'QDItech Online Change of Email Info - '.$email,$message,'From: noreply@qditech.com.au');
//   //mail ('ccao@qditech.com.au','QDItech Change of Email Info - '.$email,$message,'From: noreply@qditech.com.au');
//}

function get_info($field, $user_id)
{
   //echo "<br>field is: $field user_id is: $user_id<br>";
   //$query = "select ".$field." from user where id='".$user_id."'";
   //echo "<BR>Q: $query<br>";
   $info_result = db_query("select $field from login where user_id='".$user_id."'");
   if ($info_result && db_numrows($info_result) > 0) {
      return db_result($info_result,0,$field);
   } else {
      return false;
   }
}

function user_getStateRep() {
	global $G_USER_RESULT;
 	//see if we have already fetched this user from the db, if not, fetch it
	if (!$G_USER_RESULT) {
		$G_USER_RESULT=db_query("SELECT * FROM login WHERE user_name='" . user_getname() . "'");
	}
	if ($G_USER_RESULT && db_numrows($G_USER_RESULT) > 0) {
		return db_result($G_USER_RESULT,0,'state');
	} else {
		return false;
	}
}


function user_getrealname() {
	global $G_USER_RESULT;
	//see if we have already fetched this user from the db, if not, fetch it
	//if (!$G_USER_RESULT)
   {
		$G_USER_RESULT=db_query("SELECT * FROM login WHERE user_name='" . user_getname() . "'");
		$fullname = db_result($G_USER_RESULT,0,'first_name') . " " . db_result($G_USER_RESULT,0,'last_name');
		return $fullname;
	}

}



function user_getemail() {
	global $G_USER_RESULT;
	//see if we have already fetched this user from the db, if not, fetch it
	if (!$G_USER_RESULT) {
		$G_USER_RESULT=db_query("SELECT * FROM login WHERE user_name='" . user_getname() . "'");
	}
	if ($G_USER_RESULT && db_numrows($G_USER_RESULT) > 0) {
		return db_result($G_USER_RESULT,0,'email');
	} else {
		return false;
	}
}

function user_getname() {
	if (user_isloggedin()) {
		return $GLOBALS['user_name'];
	} else {
		//look up the user some day when we need it
		return ' ERROR - Not Logged In ';
	}
}

function account_pwvalid($pw) {
	global $feedback;
	if (strlen($pw) < 6) {
		$feedback .= " Password must be at least 6 characters. ";
		return false;
	}
	return true;
}

//function account_namevalid($name) {
//	global $feedback;
//	// no spaces
//	if (strrpos($name,' ') > 0) {
//		$feedback .= " There cannot be any spaces in the login name. ";
//		return false;
//	}
//
//	// must have at least one character
//	if (strspn($name,"abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ") == 0) {
//		$feedback .= "There must be at least one character.";
//		return false;
//	}
//
//	// must contain all legal characters
//	if (strspn($name,"abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_")
//		!= strlen($name)) {
//		$feedback .= " Illegal character in name. ";
//		return false;
//	}
//
//	// min and max length
//	if (strlen($name) < 5) {
//		$feedback .= " Name is too short. It must be at least 5 characters. ";
//		return false;
//	}
//	if (strlen($name) > 15) {
//		$feedback .= "Name is too long. It must be less than 15 characters.";
//		return false;
//	}
//
//	// illegal names
//	if (eregi("^((root)|(bin)|(daemon)|(adm)|(lp)|(sync)|(shutdown)|(halt)|(mail)|(news)"
//		. "|(uucp)|(operator)|(games)|(mysql)|(httpd)|(nobody)|(dummy)"
//		. "|(www)|(cvs)|(shell)|(ftp)|(irc)|(debian)|(ns)|(download))$",$name)) {
//		$feedback .= "Name is reserved.";
//		return 0;
//	}
//	if (eregi("^(anoncvs_)",$name)) {
//		$feedback .= "Name is reserved for CVS.";
//		return false;
//	}
//
//	return true;
//}
//
//function validate_email ($address) {
//	return (ereg('^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+'. '@'. '[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.' . '[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$', $address));
//}

?>
