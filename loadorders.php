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

   echo "<table><tr><td>userid</td><td>Name</td><td>storename</td><td>address</td><td>suburb</td><td>state</td><td>postcode</td><tr>";
   if(($handle = fopen($filename,"r")) == NULL)
   {
      echo "FAILED TO OPEN FILE<BR>";
   }
   else
   {
      $contents = fgets($handle);
//      echo "$contents<BR>";
      $j = 1;
      while(!feof($handle))
      {
         $contents = fgets($handle);
         //echo "$contents<BR><BR>";
         $lineArr = explode(",", $contents);

         $user_id = trim($lineArr[0]);
         $surname = trim($lineArr[1]);
         $firstname = trim($lineArr[2]);
         $fullname = "$firstname $surname";

         $lid = trim($lineArr[4]); //lookup add
         $query = "select * from location where location_id = $lid";
         $res = db_query($query);
         $num = db_numrows($res);
         if($num > 0)
         {
            $lname = db_result($res, 0, "sname");
            $laddress = db_result($res, 0, "address");
            $lsuburb = db_result($res, 0, "suburb");
            $lpostcode = db_result($res, 0, "postcode");
            $lstate = db_result($res, 0, "state");
         }


         $payable = trim($lineArr[7]);

         $iswages = "0";
         if($payable > 0)
         {
            $iswages = true;
//            $payable /=1.1;
         }

         if(!$payable)
         {
            $payable = 0;
         }
         echo "PAY: $payable<BR>";

//         $qty1 = trim($lineArr[8]);
//         $item1 = trim($lineArr[9]);
//         $size1 = trim($lineArr[10]);
//         $unitcost1 = trim($lineArr[11]);
//
//         $qty2 = trim($lineArr[13]);
//         $item2 = trim($lineArr[14]);
//         $size2 = trim($lineArr[15]);
//         $unitcost2 = trim($lineArr[16]);
//
//         $qty3 = trim($lineArr[18]);
//         $item3 = trim($lineArr[19]);
//         $size3 = trim($lineArr[20]);
//         $unitcost3 = trim($lineArr[21]);
//
//         $qty4 = trim($lineArr[23]);
//         $item4 = trim($lineArr[24]);
//         $size4 = trim($lineArr[25]);
//         $unitcost4 = trim($lineArr[26]);
//
//         $qty5 = trim($lineArr[28]);
//         $item5 = trim($lineArr[29]);
//         $size5 = trim($lineArr[30]);
//         $unitcost5 = trim($lineArr[31]);
//
//         $qty6 = trim($lineArr[33]);
//         $item6 = trim($lineArr[34]);
//         $size6 = trim($lineArr[35]);
//         $unitcost6 = trim($lineArr[36]);
//
//         $qty7 = trim($lineArr[38]);
//         $item7 = trim($lineArr[39]);
//         $size7 = trim($lineArr[40]);
//         $unitcost7 = trim($lineArr[41]);

         $ordertime = date("Y-m-d H:i:s");

         db_query("begin");

         $query = "INSERT INTO orders (user_id, name, address, suburb, state, postcode, order_time, status, lastupdated, sname, payable, iswages ) VALUES ($user_id, '$fullname', '$laddress', '$lsuburb', '$lstate', '$lpostcode', '$ordertime', 'APPROVED', '$ordertime', '$lname', $payable,  $iswages)";
//echo "$query<BR>";
         if(db_query($query))
         {
            echo "<tr><td><b>$user_id</b></td><td>$fullname</td><td>$lname</td><td>$laddress</td><td>$lsuburb</td><td>$lstate</td><td>$lpostcode</td><tr>";
            $order_id = mysql_insert_id();
            for($i = 8; $i<42;$i++)
            {

               $qty = trim($lineArr[$i]);
               $i++;
               $item = trim($lineArr[$i]);
               $i++;
               $size = trim($lineArr[$i]);
               $i++;
               $unitcost = trim($lineArr[$i])/1.1;
               if($qty > 0)
               {
                  echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>$qty</td><td>$item</td><td>$size</td><td>$unitcost</td><tr>";

                  $query = "INSERT INTO lineitems (order_id, myob_code, qty, `size`, price ) VALUES  ( $order_id, '$item', $qty, '$size',  $unitcost)";
                  db_query($query);
               }
               $i++;
            }
            db_query("commit");
         }
         else
         {
            db_query("rollback");
            echo "ERROR!!$query<BR>";
         }





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
