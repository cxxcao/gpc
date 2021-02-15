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

   echo "<table><tr><td>prodid</td><td>measure</td><td>myob_code</td><td>size</td><td>onhand</td><tr>";
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

         $prodid = trim($lineArr[0]);
         $item_number = trim($lineArr[1]);
         $myob_code = trim($lineArr[2]);
         $desc = trim($lineArr[3]);
         $fabric = trim($lineArr[4]);
         $colour = trim($lineArr[5]);
         $measure = trim($lineArr[6]);
         $price = trim($lineArr[7]);
         $size = trim($lineArr[8]);
         $chest = ceil(trim($lineArr[9]));
         $waist = ceil(trim($lineArr[10]));
         $hip = ceil(trim($lineArr[11]));
         $lowwaist = ceil(trim($lineArr[12]));
         $collar = ceil(trim($lineArr[13]));
         $onhand = 0;
//         $onhand = trim($lineArr[14]);
//
         if(!$chest)
            $chest = 0;
         if(!$waist)
            $waist = 0;
         if(!$hip)
            $hip = 0;
         if(!$collar)
            $collar = 0;

        $query = "update products set price = $price, fabric = '$fabric', colour='$colour', myob_code='$myob_code' where prod_id = $prodid";
        db_query($query);
        echo "$query<BR>";

         $query = "INSERT INTO sizes (prod_id, `size`, chest, waist,hip,collar, lowwaist, onhand) VALUES  ($prodid, '$size', '$chest', '$waist', '$hip', '$collar', '$lowwaist', $onhand)";
         db_query($query);
        echo "$query<BR>";
         echo "<tr><td>$prodid</td><td>$measure</td><td>$myob_code</td><td>$size</td><td>$onhand</td><tr>";
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
