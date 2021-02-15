<?php
date_default_timezone_set('Australia/Melbourne');
//dont forget to uncomment the email notification for new orders.
global $environment;
$environment = "DEV";

global $client;
$client = "gpc";

if($environment == "DEV")
{
   error_reporting(E_ALL ^ E_DEPRECATED);
   ini_set("display_errors", 1);
   define("_MAILHOST", "smtp.dsl.net.au");

   define("_CUR_HOST", "http://localhost");
   define("_DIR", "/gpc/");
   define("_MERCHANTID", "TESTANZDESIGNSTO");
   define("_ANZ_ACCESS_CODE", "8CB4712F");
   define("_CLIENT_LOGO", "gpc-logo.jpg");
}
else if($environment == "USERTEST")
{
//    error_reporting(E_ALL ^ E_DEPRECATED);
//    ini_set("display_errors", 1);

   define("_MAILHOST", "localhost");
   define("_CUR_HOST", "http://uat.designstoyou.com.au");
   define("_DIR", "/gpc/");
   define("_MERCHANTID", "TESTANZDESIGNSTO");
   define("_ANZ_ACCESS_CODE", "8CB4712F");
   define("_CLIENT_LOGO", "gpc-logo.jpg");
}
else
{
//    error_reporting(0);
//    error_reporting(E_ALL);

//    ini_set("display_errors", 1);
   define("_MAILHOST", "localhost");
   define("_CUR_HOST", "https://www.designstoyou.com.au");
   define("_DIR", "/gpc/");
   define("_MERCHANTID", "ANZDESIGNSTO");
   define("_ANZ_ACCESS_CODE", "37F8A711");
   define("_CLIENT_LOGO", "gpc-logo.jpg");
}

define("_NC", date('Ymdhms'));
define("_MAX_DEDUCTION_AMT", 300);
define("_SHOW_PRICE_WITH_GST", "Y");
define("_FREE_RETURNS", false);
define("_ENV", $environment);
define("_DEFAULT_PASSWORD", "gpc123");
define("_REALM", "GPC");

define("_CLIENT_ALT", "GPC");
define("_IMGLOC", "_img/gpc/");
define("_ACCOUNTS_EMAIL", "c.cao@designstoyou.com.au");
define("_ROLE_ID", "role_id");
//access levels
define("_ADMIN_LEVEL", "0");
define("_BRANCH_LEVEL", "1");
define("_USER_LEVEL", "2");

define("_ASBA_USER_ID", "2");
define("_USER_NAME", "user_name");
define("_PASSWORD", "password");
define("_EMAIL", "email");
define("_USER_ID", "user_id");
define("_LOCATION_ID", "location_id");
define("_ACCESS_LEVEL", "access_level");
define("_JURISDICTION", "jurisdiction");
define("_FIRST_NAME", "firstname");
define("_LAST_NAME", "lastname");
define("_ALLOWANCE", "allowance");

define("_PENDING", "PENDING");
define("_APPROVED", "APPROVED");
define("_DESPATCHED", "DESPATCHED");
define("_PROCESSING", "PROCESSING");
define("_RECEIVED", "RECEIVED");
define("_COMPLETED", "COMPLETED");

define("_NEW", "NEW");
define("_SAVE", "SAVE");
define("_UPDATE", "UPDATE");
define("_EDIT", "EDIT");
define("_DELETE", "DELETE");

//location
define("_SNAME","sname");
define("_PHONE","phone");
define("_FAX","fax");

//products
define("_DESCRIPTION", "description");
define("_PROD_ID", "prod_id");
define("_CATEGORY", "category");
define("_CAT_ID", "cat_id");
define("_ITEM_NUMBER", "item_number");
define("_MYOB_CODE", "myob_code");
define("_FABRIC", "fabric");
define("_COLOUR", "colour");
define("_MEASURE", "measure");
define("_QTY", "qty");
define("_PRICE", "price");
define("_BELT_PROD_ID", "219");

//category cat_id & description from products
define("_NAME", "name");

//orders
define("_ORDER_ID", "order_id");
define("_EMP_ID", "emp_id");
define("_ADDRESS", "address");
define("_SUBURB", "suburb");
define("_STATE", "state");
define("_POSTCODE", "postcode");
define("_CONTACT", "contact");
define("_ORDER_TIME", "order_time");
define("_STATUS", "status");
define("_COURIER", "courier");
define("_CONNOTE", "connote");
define("_NUMBOXES", "numboxes");
define("_LASTUPDATED", "lastupdated");
define("_COMMENTS", "comments");
define("_APPROVAL_TIME", "approval_time");
define("_SAMPLE", "SAMPLE");
define("_CARDNAME", "cardname");
define("_CARDNUMBER", "cardnumber");
define("_EXPIRY", "expiry");
define("_CARDTYPE", "cardtype");
define("_PAYABLE", "payable");
define("_PAID", "paid");
define("_ISWAGES", "iswages");

define("_METHOD", "post");
define("_BEGIN", "BEGIN");
define("_COMMIT", "COMMIT");
define("_ROLLBACK", "ROLLBACK");

define("_VIC", "VIC");
define("_NSW", "NSW");
define("_QLD", "QLD");
define("_WA", "WA");
define("_SA", "SA");
define("_TAS", "TAS");
define("_ACT", "ACT");
define("_NT", "NT");
define("_NZ", "NZ");
define("_AU", "AU");

define("_VISA", "VISA");
define("_MASTERCARD", "MASTERCARD");

/* returns */
define("_WARRANTY_ID", "warranty_id");
define("_CLAIM_DATE", "claim_date");
define("_REASON", "reason");
define("_RETURN_TYPE", "return_type");
define("_WARRANTY_SESSION", "warrantySession");
define("_FAULTY", "FAULTY");
define("_INCORRECTLY_SUPPLIED", "INCORRECTLY SUPPLIED");
define("_CHANGE_OF_STYLE", "CHANGE OF STYLE");
define("_CHANGE_OF_SIZE", "CHANGE OF SIZE");
//define("_WRONG_GOODS", "WRONG GOODS");
define("_STAFF_HAS_LEFT", "STAFF HAS LEFT");

$ccArr = array(
_VISA => _VISA,
_MASTERCARD => _MASTERCARD
);

//$statusArr = array(
//_PENDING => _PENDING,
//_APPROVED => _APPROVED,
//_PROCESSING => _PROCESSING,
//_DESPATCHED => _DESPATCHED);
//

$accessArr = array(
"0"=>"ADMINISTRATOR",
"1"=>"BRANCH MANAGER",
"2"=>"STAFF"
);

$empStatusArr = array(
"Full-time Var Hrs" => "Full-time Var Hrs",
"Full-time Set Hrs" => "Full-time Set Hrs",
"Part-time Var Hrs" => "Part-time Var Hrs",
"Part-time Set Hrs" => "Part-time Set Hrs",
"Casual" => "Casual",
"Fixed Term Contract" => "Fixed Term Contract"
);

$daysWorkedArr = array(
"Full-Time" => "Full-Time",
"Part-Time" => "Part-Time"
);

define("_JACKET_TYPE", "0");
define("_UPPER_TYPE", "1");//resever
define("_LOWER_TYPE", "2"); //;lower
define("_KNIT_TYPE", "4"); //outer
define("_TECHJK_TYPE", "5"); //flame retardant
define("_POLO_TYPE", "3"); //upper
define("_ACC_TYPE", "7"); //cap, beanie
define("_BELT_TYPE", "8");//boots
define("_OPTIONAL", "9");//boots


global $portalURL;
$portalURL = "https://www.designstoyou.com.au/" . $client;

global $categoryArr;
$categoryArr = array(
      _POLO_TYPE=> "POLO/T-SHIRT",
		_LOWER_TYPE => "CARGO/PANT",
      _KNIT_TYPE=>"OUTER",
      _TECHJK_TYPE=>"FLAME RETARDANT",      
      _ACC_TYPE=>"HEADWEAR",
      _BELT_TYPE =>"FOOTWEAR",
     _OPTIONAL =>"OPTIONAL");

global $garmentTypes;
$garmentTypes = array(_LOWER_TYPE, _POLO_TYPE, _TECHJK_TYPE, _KNIT_TYPE, _ACC_TYPE, _BELT_TYPE, _OPTIONAL);

$yesNoArr = array("Y"=>"Yes", "N"=>"No");


$staffStatusArr = array("ACTIVE" => "ACTIVE", "INACTIVE" => "INACTIVE");


$warrantyStatusArr = array(
_PENDING => _PENDING,
_APPROVED => _APPROVED,
_PROCESSING => _PROCESSING,
_RECEIVED => _RECEIVED,
_DESPATCHED => _DESPATCHED,
_COMPLETED => _COMPLETED);

$statesArr = array(
_ACT => _ACT,
_NSW => _NSW,
_NT => _NT,
_QLD => _QLD,
_SA => _SA,
_TAS => _TAS,
_VIC => _VIC,
_WA => _WA,
_NZ => _NZ);

$countryArr = array(
_AU => _AU, _NZ => _NZ
);

$returnTypeArr = array(
_FAULTY => _FAULTY,
_INCORRECTLY_SUPPLIED => _INCORRECTLY_SUPPLIED,
_CHANGE_OF_STYLE => _CHANGE_OF_STYLE,
_CHANGE_OF_SIZE => _CHANGE_OF_SIZE,
);

$employeeRoleIDArr = array(
"2"=>"GPC Property Group",
"4"=>"GPC DC",   
"850"=>"Team850");

?>
