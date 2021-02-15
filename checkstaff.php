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

   echo "<table><tr>
               <td>firstNum</td>
               <td>dtyID</td>
               <td>reeceAccNo</td>
               <td>branchNo</td>
               <td>dtyInv</td>
               <td>invDate</td>
               <td>amtEx</td>
               <td>gst</td>
               <td>invNo</td>
               <td>connote</td>
               <td>empNoName</td>
               <td>empNo</td>
               <td>payFreq</td>
               <td>wageDeduction</td>
               <td>noWeeks</td>
               <td>backorderVal</td>
<tr>";
   if(($handle = fopen($filename,"r")) == NULL)
   {
      echo "FAILED TO OPEN FILE<BR>";
   }
   else
   {
      $contents = fgets($handle);
      while(!feof($handle))
      {

         $contents = fgets($handle);
         //echo "$contents<BR><BR>";
         $lineArr = explode(",", $contents);

         $firstNum = trim($lineArr[0]);
         $dtyID = trim($lineArr[1]);
         $reeceAccNo = trim($lineArr[2]);
         $branchNo = trim($lineArr[3]);
         $dtyInv = trim($lineArr[4]);
         $invDate = trim($lineArr[5]);
         $amtEx = trim($lineArr[6]);
         $gst = trim($lineArr[7]);
         $invNo = trim($lineArr[8]);
         $connote = trim($lineArr[9]);
         $empNoName = trim($lineArr[10]);
         $empNo = trim($lineArr[11]);
         $payFreq = trim($lineArr[12]);
         $wageDeduction = trim($lineArr[13]);
         $noWeeks = trim($lineArr[14]);
         $backorderVal = trim($lineArr[15]);

         $empNoNameArr = explode(" ", $empNoName);
         $empNoOnly = trim($empNoNameArr[0]);
         $empLastName = trim($empNoNameArr[count($empNoNameArr)-1]);

         $query = "select * from login where user_id = $empNoOnly";
         $res = db_query($query);
         $num = db_numrows($res);
         if($num > 0)
         {
            $dbLastName = strtolower(db_result($res, 0, "lastname"));
            $empLastName = strtolower($empLastName);
            if($empLastName != $dbLastName)
            {
               echo "NO MATCH! USERID: $empNoOnly LN: $empLastName DB: $dbLastName <BR>";
            }

         }


        // echo "no: $empNoOnly last: $empLastName<BR>";


         echo "<tr>
               <td>$firstNum</td>
               <td>$dtyID</td>
               <td>$reeceAccNo</td>
               <td>$branchNo</td>
               <td>$dtyInv</td>
               <td>$invDate</td>
               <td>$amtEx</td>
               <td>$gst</td>
               <td>$invNo</td>
               <td>$connote</td>
               <td>$empNoName</td>
               <td>$empNo</td>
               <td>$payFreq</td>
               <td>$wageDeduction</td>
               <td>$noWeeks</td>
               <td>$backorderVal</td>
         <tr>";
      } //end insert update table

   }
   echo "</table>";
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
