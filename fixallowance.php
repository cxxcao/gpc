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
   echo "<table><tr><td>userid</td><td>order_id</td><td>num rules</td><tr>";


   //$query = "select * from allowance where end = '2013-03-20'";
   $query = "select * from allowance where start = '2012-11-25 00:00:00' and end >= '2014' order by user_id ";
   $res = db_query($query);
   $num = db_numrows($res);

   if($num > 0)
   {
      for($i = 0; $i < $num; $i++)
      {
         $allowance_id = db_result($res, $i, "allowance_id");
         $user_id = db_result($res, $i, "user_id");
         $allowance = db_result($res, $i, "allowance");
         $start = db_result($res, $i, "start");
         $end = db_result($res, $i, "end");
         $minus12months = strtotime(date("Y-m-d", strtotime($end)) . "-12 months");
         $endPlus1Day =  strtotime(date("Y-m-d", $minus12months) . "+1 day");

         $newEnd = date('Y-m-d', $minus12months);
         $newStart = date('Y-m-d', $endPlus1Day);


         db_query("update allowance set end = '$newEnd' where allowance_id = $allowance_id");
echo "END: $end MINUS12: $newEnd<BR>";

         $curD = date('Y-m-d');
         if($newEnd < $curD)
         {
            $query = "insert into allowance (user_id, allowance, start, end) values ($user_id, '$allowance', '$newStart', '$end')";
            db_query($query);
            echo "[$user_id]add new allowance! START: $newStart END: $end<BR>";
         }

//         $query2 = "select * from allowance where user_id = $user_id";
//         $res2 = db_query($query2);
//         $num2 = db_numrows($res2);

/*
         if($num2 == 4)
         {
            $allowance_id2 = db_result($res2, 1, "allowance_id");
            $user_id = db_result($res2, 1, "user_id");
            $allowance = db_result($res2, 1, "allowace");
            $start = db_result($res2, 1, "start");
            $end = db_result($res2, 1, "end");

            //get allowance @ position 2;  update expiry (end) of allowance @ position 1 with the end date of allowance @ position 2
            $allowance_id1 = db_result($res2, 0, "allowance_id");
            db_query("update allowance set end='$end' where allowance_id = $allowance_id1");
            db_query("delete from allowance where allowance_id = $allowance_id2");

            echo "NUM: $num2 - USER ID: [$user_id] END: $end ALID1: $allowance_id1 ALID2: $allowance_id2<br/>";
         }
         */
//         else if($num2 > 2)
//            echo "NUM: $num2 - USER ID: [$user_id]<br/>";
      }
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
