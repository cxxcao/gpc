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
         $query = "delete from login_update";

         $res = db_query($query);
         if(!$res)
            echo "ERROR!!<BR>";
      while(!feof($handle))
      {

         $contents = fgets($handle);
         //echo "$contents<BR><BR>";
         $lineArr = explode(",", $contents);

         $staffid = trim($lineArr[0]);
         $surname = trim($lineArr[1]);
         $firstname = trim($lineArr[2]);
         $jobClass = trim($lineArr[4]);
         $gender =  trim($lineArr[5]);
         $payFreq = trim($lineArr[6]);
         $branchNo = trim($lineArr[9]);
         $username = trim($lineArr[10]);
         $email = trim($lineArr[11]);
         $firstAllowance = trim($lineArr[14]);
         $secondAllowance = trim($lineArr[15]);
         $role = trim($lineArr[16]);
         $state = trim($lineArr[19]);
         $startDate = trim($lineArr[7]);

            $street = trim($lineArr[17]);
            $suburb = trim($lineArr[18]);
            $state = trim($lineArr[19]);
            $postcode = trim($lineArr[20]);

          $location_id = md5(strtoupper($branchNo) . strtoupper($street) . strtoupper($suburb). strtoupper($postcode));
//echo "1[$firstAllowance] 2[$secondAllowance]<BR>";

         $status = "ACTIVE";
         $isAUS = "Y";

         if($firstAllowance == "#N/A")
            $firstAllowance = 0;

         if($secondAllowance == "#N/A")
            $secondAllowance = 0;

          if($role == "#N/A")
            $role = "Corporate";

          if($branchNo == "#N/A")
            $branchNo = 3980;

          if($username == "#N/A")
            $username = $staffid;


         if($state == "New Zealand")
            $isAUS = "N";

         if($gender == "Female")
            $gender = "1";
         else
            $gender = "2";

         $password = md5("Reece");
         $accesslevel = determineAccessLevel($jobClass);
         $realm = "REECE";

            $role_id = "";
            switch($role)
            {
               case "Corporate":
                  $role_id = 1;
                  break;
               case "Retail":
                  $role_id = 2;
                  break;
               case "Trade":
                  $role_id = 3;
                  break;
            }

         $query = "select * from allowance where user_id = $staffid  order by allowance_id desc";
         $res = db_query($query);
         $numrows = db_numrows($res);

         if($numrows > 1)
         {
            $allowance_id = db_result($res, 0, "allowance_id");
            $org_allowance = db_result($res, 1, "allowance");

            //get total already spent
            $query = "select sum(price*qty*1.1) as total from lineitems li, orders o where li.order_id = o.order_id and o.user_id = $staffid ";
            $spentRes = db_query($query);
            $spentNum = db_numrows($spentRes);
            if($spentNum > 0)
            {
               $total = db_result($spentRes, 0, "total");
               $remaining = bcsub($org_allowance, $total, 2);
            }
            else
               $remaining = $org_allowance;
echo "r: $remaining t: $total o: $org_allowance u: $staffid<BR>";
            //update
            if($remaining > 0)
            {
               $dateNow = date('Y-m-d');
               $yearStart = date('Y', strtotime($startDate));
               $yearNow = date('Y', strtotime($dateNow));
               $pattern = '/^(\d+){4}\-/';
               $yearNow = $yearNow;
               $replacement = "$yearNow-";

               $endTime = preg_replace($pattern, $replacement, $startDate);;

               $query = "update allowance set allowance = $remaining, end = '$endTime' where allowance_id = $allowance_id";
//               echo "$query<BR>";
               db_query($query);
            }
            else
            {
               //just leave things the way they are!

            }
          //  db_query($query);
         }
         else if($numrows == 1)
         {
            $allowance_id = db_result($res, 0, "allowance_id");
            $user_id = db_result($res, 0, "user_id");

         }
         echo "<tr><td>$staffid</td><td>$username</td><td>$surname</td><td>$firstname</td><td>$role_id</td><td>$firstAllowance | $secondAllowance</td><td>$branchNo</td><td>$startDate</td><td>$branchNo $street $suburb $postcode </td><tr>";

      }
   }
   echo "</table>";
//   echo "J: $j<BR>";
//      else
//      {
//         $i = 0;
//         $_SESSION['msg'] = "";
//         while (!feof($handle))
//         {
//            $contents = fgets($handle, 1024);

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
