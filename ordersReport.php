<?php
   error_reporting(E_ALL);
   date_default_timezone_set('Australia/Melbourne');
   ini_set("display_errors", 1);
session_start();
$home = dirname(__FILE__);
$lib = $home ."/lib/";

echo "home: $home<BR>";
echo "lib: $lib<BR>";

$host = "designstoyou.com.au";
$dbuser="desi2010";
$dbpasswd="pura+upa";
$curDB = "reece_live";
$dbArr = array("reece_live");

//$dbArr = array("reece_live", "bupa2", "national", "ufs", "lhpa", "doclothing", "m10", "tvh2");
$custArr = array("REECE", "BUPA", "NATIONAL", "UFS", "LHPA", "DOCLOTHING", "MITRE10", "TVH");
$dbUserArr = array($dbuser,"bupa",$dbuser,$dbuser,$dbuser,$dbuser,$dbuser,$dbuser);
$dbPassArr = array($dbpasswd,$dbpasswd,$dbpasswd,$dbpasswd,$dbpasswd,$dbpasswd,$dbpasswd,$dbpasswd);


echo "<html><body>";
echo "<table><tr><td>CUSTOMER</td><td>ORDER ID</td><td>ORDER DATE</td><td>INVOICE DATE</td><td>STATUS</td><td>DESPATCH STATUS</td><td>DESPATCH DATE</td><td>DELIVERED DATE</td><td>DAYS TO SHIP</td><td>BRACKET</td></tr>";
for($z = 0; $z < count($dbArr); $z++)
{
   $curDB = $dbArr[$z];
   $dbuser = $dbUserArr[$z];
   $dbpasswd = $dbPassArr[$z];
   $cust = $custArr[$z];

   $mysqli = new mysqli($host, $dbuser, $dbpasswd, $curDB);
   if ($mysqli->connect_errno)
   {
      echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
      exit;
   }

   $myobsqli = new mysqli("10.0.0.2", "root", "#1nt3rrupt#", "myob");
   if ($mysqli->connect_errno)
   {
      echo "Failed to connect to MYOB DB: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
      exit;
   }


   $query = "select * from orders where order_time between '2014-09-01 00:00:00' and '2014-09-30 23:23:59'";
   $res = $mysqli->query($query);
   $num = $res->num_rows;
   $numBackOrdered = 0;
   $backorders = array();

   for($i = 0; $i < $num; $i++)
   {
      $row = $res->fetch_assoc();
      $order_id = $row["order_id"];
      $order_time = $row["order_time"];
      $status = $row["status"];
      $despatchdate = $row["despatchdate"];
      $despatchstatus = $row["despatchstatus"];
      $delivereddate = $row["delivereddate"];
      if(!$despatchstatus)
         $despatchstatus = $status;

      //get the myob invoice date!
      $query2 = "select * from sales where customerponumber like '$order_id' and InvoiceStatusID != 'OR' and cardrecordid in (3348,3349,3350,3351,3352,3353,3387,4775,60,4569,4679,3977,4727,4728,4729,4730,4499,4582,4579,4578,4580,4581,4583,4584,4585,4586,4587,4588,4577,4576,4575,4723,255,257,259,263,329,356,827,257)";
      $res2 = $myobsqli->query($query2);
      $num2 = $res2->num_rows;
      $row2 = $res2->fetch_assoc();
      $invDate = $row2["InvoiceDate"];
      $boDate = $invDate;

      $dStart  = new DateTime(date($order_time));
      $dEnd = new DateTime(date($invDate));
      $dDiff = $dStart->diff($dEnd);
      $daysDiff = $dDiff->days;
      $bracket = 0;

//      if($daysDiff < 6)
//         $bracket = "0-5";
//      else if($daysDiff > 5 and $daysDiff < 11)
//         $bracket = "6-10";
//      else if($daysDiff > 10 and $daysDiff < 16)
//         $bracket = "11-15";
//      else if($daysDiff > 15 and $daysDiff < 21)
//         $bracket = "16-20";
//      else if($daysDiff > 20 and $daysDiff < 26)
//         $bracket = "21-26";
//      else if($daysDiff > 25)
//         $bracket = "26+";

      if($daysDiff < 8)
         $bracket = "0-5";
      else if($daysDiff > 7 and $daysDiff < 15)
         $bracket = "6-10";
      else if($daysDiff > 14 and $daysDiff < 21)
         $bracket = "11-15";
      else if($daysDiff > 20 and $daysDiff < 29)
         $bracket = "16-20";
      else if($daysDiff > 30 and $daysDiff < 36)
         $bracket = "21-26";
      else if($daysDiff > 37)
         $bracket = "26+";

      //echo "$query2<BR>";
      //echo "ORDERID: $order_id inv date: $invDate NUM: $num2<BR>";
   echo "<tr><td>$cust</td><td>$order_id</td><td>$order_time</td><td>$invDate</td><td>$status</td><td>$despatchstatus</td><td>$despatchdate</td><td>$delivereddate</td><td>$daysDiff</td><td>$bracket</td></tr>";

      if($num2 > 1)
      {
         //get the next myob rec as these will be the backordered items and insert into array or backorders;
         for($j = 1; $j < $num2; $j++)
         {
            $row2 = $res2->fetch_assoc();
            $invNo = $row2["InvoiceNumber"];
            $invDate = $row2["InvoiceDate"];
            $invStatus = $row2["InvoiceStatusID"];

            if($invStatus != "OR")
            {
               if(array_key_exists($order_id, $backorders))
               {
                  array_push($backorders[$order_id]->invoiceNos, $invNo);
                  array_push($backorders[$order_id]->invoiceDates, $invDate);
                  array_push($backorders[$order_id]->deliveredDates, $delivereddate);

                //  echo "ORDERID: $order_id inv date: $invDate inv: $invNo<BR>";
               }
               else
               {
                  $bo = new backorder();
                  $bo->order_id = $order_id;
                  array_push($bo->invoiceNos, $invNo);
                  array_push($bo->invoiceDates, $invDate);
                  array_push($bo->deliveredDates, $delivereddate);
                  $backorders[$order_id] = $bo;
                 // echo "ORDERID: $order_id inv date: $invDate inv: $invNo<BR>";
               }
               $dStart  = new DateTime(date($boDate));
               $dEnd = new DateTime(date($invDate));
               $dDiff = $dStart->diff($dEnd);
               $daysDiff = $dDiff->days;

      if($daysDiff < 6)
         $bracket = "0-5";
      else if($daysDiff > 5 and $daysDiff < 11)
         $bracket = "6-10";
      else if($daysDiff > 10 and $daysDiff < 16)
         $bracket = "11-15";
      else if($daysDiff > 15 and $daysDiff < 21)
         $bracket = "16-20";
      else if($daysDiff > 20 and $daysDiff < 26)
         $bracket = "21-26";
      else if($daysDiff > 25)
         $bracket = "26+";
               echo "<tr><td>$cust</td><td>$order_id</td><td>$boDate</td><td>$invDate</td><td>$status</td><td>BACKORDER</td><td>$despatchdate</td><td>$delivereddate</td><td>$daysDiff</td><td>$bracket</td></tr>";
            }
         }
      }
   }
}

echo "</table></body></html>";

//
//echo "<tr><td colspan='7'>BACKORDERS</td></tr>";
//   $arrKeys = array_keys($backorders);
//
//   for($i = 0; $i < count($arrKeys); $i++)
//   {
//      $bo = $backorders[$arrKeys[$i]];
//
//      for($j = 0; $j < count($bo->invoiceNos); $j++)
//      {
//         $order_id = $bo->order_id;
//         $invDate = $bo->invoiceDates[$j];
//         $delivereddate = $bo->deliveredDates[$j];
//
//         echo "<tr><td>$order_id</td><td>$order_time</td><td>$invDate</td><td>$status</td><td>$despatchstatus</td><td>$despatchdate</td><td>$delivereddate</td></tr>";
//      }
//   }


class backorder
{
   var $order_id;
   var $invoiceNos;
   var $invoiceDates;
   var $deliveredDates;


   function backorder()
   {
      $this->order_id = "";
      $this->invoiceNos = array();
      $this->invoiceDates = array();
      $this->deliveredDates = array();
   }

}

?>

