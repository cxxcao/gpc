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

   echo "<table><tr><td>userid</td><td>amt</td><td>start</td><td>end</td><tr>";
   if(($handle = fopen($filename,"r")) == NULL)
   {
      echo "FAILED TO OPEN FILE<BR>";
   }
   else
   {
      $contents = fgets($handle);
//      echo "$contents<BR>";
      $j = 1;
      $amtTotal = 0;
      while(!feof($handle))
      {
         $contents = fgets($handle);
         //echo "$contents<BR><BR>";
         $lineArr = explode(",", $contents);

         $userid = trim($lineArr[0]);
         $amt = trim($lineArr[1]);
         $start = trim($lineArr[2]);
         $end = trim($lineArr[3]);
         $name = trim($lineArr[5]). " " . trim($lineArr[6]);

         //check if exists
         $query = "select * from allowance where user_id = $userid";
         $res = db_query($query);
         $num = db_numrows($res);


         if($num > 0) //do nothing
         {
            $user_id = db_result($res, 0, "user_id");
            $start1 = db_result($res, 0, "start");
            $end1 = db_result($res, 0, "end");
            $amt1 = db_result($res, 0, "allowance");
            if($amt != "no new allowance required")
            {
               echo "<tr><td>$user_id</td><td>$amt | $amt1</td><td>$start</td><td>$end</td><tr>";
               $amtTotal += $amt;
               $query = "insert into allowance(user_id, allowance, start, end) values($userid, $amt, '$start','$end')";
               db_query($query);
            }
         }
         else
         {
            $query = "select * from login where user_id = $userid";
            $res = db_query($query);
            $num = db_numrows($res);
            if($num > 0)
            {

            }
            else
            {
               echo "NOT FOUND: $userid, $name, $amt<BR>";

            }
$amtTotal += $amt;
//            $query = "INSERT INTO allowance (user_id, allowance, start, `end` ) VALUES  ( $userid, $amt, '$start', '$end' )";
//            db_query($query);
//             echo "<tr><td>$userid</td><td>$amt</td><td>$start</td><td>$end</td><tr>";
         }

      }
   }
   echo "</table>";
   echo "AMT TOTAL: $amtTotal<BR>";
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
