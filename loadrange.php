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

   echo "<table><tr><td>ROLEID</td><td>Cat ID</td><td>myob_code</td><td>desc</td><td>fabric</td><td>colour</td><td>cost</td><td>cost nz</td><td>ITEM</td><tr>";
   if(($handle = fopen($filename,"r")) == NULL)
   {
      echo "FAILED TO OPEN FILE<BR>";
   }
   else
   {
      $contents = fgets($handle);
//      echo "$contents<BR>";
      $j = 1;
      $oldItem = "";
      $prod_id = "";      
      while(!feof($handle))
      {
         $contents = fgets($handle);
         //echo "$contents<BR><BR>";
         $lineArr = explode(",", $contents);

         $prod_id = trim($lineArr[0]);
         $crange = trim($lineArr[1]);
         $cat_id = trim($lineArr[2]);
         $item_code = trim($lineArr[3]);
         $myob_code = trim($lineArr[4]);
         $desc = trim($lineArr[5]);
         $fabric = trim($lineArr[6]);
         $colour = trim($lineArr[7]);
         $measure = trim($lineArr[8]);
         $cost = trim($lineArr[9]);
         $cost_nz=0;
         
         $size = trim($lineArr[10]);
         $chest = trim($lineArr[11]);
         $waist = trim($lineArr[12]);
         $hip = trim($lineArr[13]);
         $lowwaist = trim($lineArr[14]);
         $collar = trim($lineArr[15]);

         //insert the new product
         if(($oldItem != $item_code) || !$oldItem)
         {
            $oldItem = $item_code;         
	         $query = "INSERT INTO products (prod_id, category, cat_id, item_number, myob_code, description, fabric, colour, price, price_nz, measure) VALUES  ($prod_id, '$crange', $cat_id, '$item_code', '$myob_code', '$desc', '$fabric', '$colour',$cost, $cost_nz, '$measure')";
	         db_query($query);

            $query = "INSERT INTO productcategory (`prod_id`, `employeerole_id`, `emb` ) VALUES  ($prod_id, 1, 'Emb-Chemmart Pharmacy-Upper')";
            db_query($query);
	            //echo"$query<BR>";
	            //echo "$item_code $role $role_id<BR>";
         }
         $query = "insert into sizes(`prod_id`, `size`, `chest`, `waist`, `hip`, `lowwaist`, `collar`) values($prod_id, '$size', '$chest', '$waist', '$hip', '$lowwaist', '$collar')";
         db_query($query);         


         //search item first
//         $query = "select * from products where item_number = '$item_code'";
//         $res = db_query($query);
//         $num = db_numrows($res);
//
//         if($num > 0)
//         {
//            $prod_id = db_result($res, 0, 'prod_id');
//            //get the prod_id and just instert the product into productcategory table;
//            $query = "INSERT INTO productcategory (prod_id, employeerole_id ) VALUES  ($prod_id, $role_id)";
//            db_query($query);
//         }
//         else


         echo "<tr><td>$prod_id</td><td>$cat_id</td><td>$myob_code</td><td>$desc</td><td>$fabric</td><td>$colour</td><td>$cost</td><td>$cost_nz</td><td>$item_code</td><tr>";
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
