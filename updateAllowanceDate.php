<?php
   error_reporting(E_ALL);
   ini_set("display_errors", 1);
session_start();
$home = dirname(__FILE__);
$lib = $home ."/lib/";

echo "home: $home<BR>";
echo "lib: $lib<BR>";


require_once($lib . 'database.php');
require_once($lib . 'functions.php');
require_once($lib . 'dbfunctions.php');
require_once($home . '/globals.php');


$action = _checkIsSet("action");

function determineAccessLevel($jobclass)
{
//   echo "$jobclass<BR>";
//   echo stristr($jobclass, "Manager") . "<BR>";
   $pos =  preg_match("/Manager|mgr/i", $jobclass);
   if($pos == 1)
   {
      return 1;
   }
   else
   {
      return 2;
   }
}

if($action == "submit")
{
   if ($_FILES["file"]["error"] > 0)
   {
      $_SESSION['msg'] = '<font color="'._FAILED_COLOR.'">ERROR UPLOADING FILE, PLEASE TRY AGAIN</font>';
   }
   else
   {
      $target_path = "$home/uploads/";
      $target_path = $target_path . basename($_FILES['file']['name']);
     $filename = $_FILES['file']['tmp_name'];

      echo "FILENAME: $filename [$target_path]<BR>";
   }

   echo "<table><tr><td>STAFF ID</td><td>Username</td><td>Surname</td><td>Firstname</td><td>ROLE</td><td>Allowance</td><td>LocationID</td><td>Hire Date</td><td>addy</td><tr>";
   if(($handle = fopen($filename,"r")) == NULL)
   {
      echo "FAILED TO OPEN FILE<BR>";
   }
   else
   {
      $contents = fgets($handle);
//      echo "$contents<BR>";
      $j = 1;
         //delete from update table;
//         $query = "delete from login_update";
//
//         $res = db_query($query);
//         if(!$res)
//            echo "ERROR!!<BR>";
      while(!feof($handle))
      {

         $contents = fgets($handle);
         //echo "$contents<BR><BR>";
         $lineArr = explode(",", $contents);

         $staffid = trim($lineArr[1]);
         $startDate = trim($lineArr[2]);

               //update allowance;
               if($startDate < "2011-11-26")
               {
                  $allowance = $secondallowance;
                  $strTime = "2012-11-25";
                  $endTime = "2013-11-25";
               }
               else
               {
                  $allowance = 250;//$firstallowance - $minusAmt;
                  $plus6months = strtotime(date("Y-m-d", strtotime($startDate)) . "+6 month");
                  $plus1year = strtotime(date("Y-m-d", strtotime($startDate)) . "+1 year");

                  $strTime = date('Y-m-d', $plus6months);
                  $endTime = date('Y-m-d', $plus1year);

                  $query = "select * from allowance where user_id = $staffid";
                  $res = db_query($query);
                  $num = db_numrows($res);

                  if($num == 1)
                  {
                     $aid = db_result($res, 0, "allowance_Id");
                     $query = "update allowance set start='$strTime', end='$endTime' where allowance_id = $aid";
                     echo "$query<BR>";
                     db_query($query);
                  }
                  else
                  {
                     echo "MORE THAN 1 ALLOWANCE: $staffid<BR>";
                  }
               }

         echo "ID: $staffid HIRE: $startDate START: $strTime END: $endTime<BR>";

         //$query = "select * from ";



         //echo "<tr><td>$staffid</td><td>$username</td><td>$surname</td><td>$firstname</td><td>$role_id</td><td>$firstAllowance | $secondAllowance</td><td>$branchNo</td><td>$startDate</td><td>$branchNo $street $suburb $postcode </td><tr>";
      } //end insert update table
      echo "</table>";
   }
}

?>

<html>
<body>

<form enctype="multipart/form-data" name="list" method="post">
<br>
File:<input type="file" size="50" name="file"><br>

<input type="submit" name="action" value="submit">

</form>


</body>
</html>
