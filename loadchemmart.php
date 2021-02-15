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

   if(($handle = fopen($filename,"r")) == NULL)
   {
      echo "FAILED TO OPEN FILE<BR>";
   }
   else
   {
      $contents = fgets($handle);
//      echo "$contents<BR>";
      $j = 1;
      
      //store name,address 1 - contact details,address 2 - contact details,suburb,state,postcode,contact name,phone number,women size 8,women size 10,women size 12,women size 14,women size 16,women size 18,men size 14 (s),men size 16 (m),men size 18 (l),men size 20 (xl),men size 22 (2xl),men size 24 (3xl),Total
      while(!feof($handle))
      {
         $contents = fgets($handle);
         //echo "$contents<BR><BR>";
         $lineArr = explode(",", $contents);

         $storename = trim($lineArr[0]);
         $address1 = trim($lineArr[1]);
         $address2 = trim($lineArr[2]);
         $suburb = trim($lineArr[3]);
         $state = trim($lineArr[4]);
         $postcode = trim($lineArr[5]);         
         $contactname = trim($lineArr[6]);
         $phone = trim($lineArr[7]);
         $s8 = trim($lineArr[8]);
         $s10 = trim($lineArr[9]);
         $s12 = trim($lineArr[10]);
         $s14 = trim($lineArr[11]);
         $s16 = trim($lineArr[12]);
         $s18 = trim($lineArr[13]);
         $small = trim($lineArr[14]);
         $medium = trim($lineArr[15]);
         $large = trim($lineArr[16]);
         $xl = trim($lineArr[17]);
         $twoXL = trim($lineArr[18]);                                    
         $threeXL = trim($lineArr[19]);
         $first3sub = strtoupper(substr($suburb, 0,3));
         $custPO = $first3sub . "20140407-$j";
         $price = 23.00;
         $priceinc = 23*1.1;
         
         
			if($s8)
			{
				$qty = $s8;				
				$totalPrice = $price * $qty;
				$totalPriceInc = $totalPrice * 1.1;
				$gst = $totalPriceInc - $totalPrice;

				echo "Symbion Health Head Office (Chemmart Pharmacies),,$storename,ATN: $contactname,\"$address1, $address2\",\"$suburb $state $postcode\",,,7/04/2014,$custPO,,A,PS64-Navy-Impromy-08,$qty,SELadies Truedry Pique Polo-08,$price,$priceinc,0%,$totalPrice,$totalPriceInc,,,Imported Symbion Health Head Office (Chemmart Pharmacies),,,,,GST,$0.00,$gst,$0.00,,,,$0.00,$0.00,$0.00,O,,,3,20,20,0,0,$0.00,,,,,,,,,,,,,*None,1678<br/>";
			} 
			
			if($s10)
			{
				$qty = $s10;
				$totalPrice = $price * $qty;
				$totalPriceInc = $totalPrice * 1.1;
				$gst = $totalPriceInc - $totalPrice;
				echo "Symbion Health Head Office (Chemmart Pharmacies),,$storename,ATN: $contactname,\"$address1, $address2\",\"$suburb $state $postcode\",,,7/04/2014,$custPO,,A,PS64-Navy-Impromy-10,$qty,SELadies Truedry Pique Polo-10,$price,$priceinc,0%,$totalPrice,$totalPriceInc,,,Imported Symbion Health Head Office (Chemmart Pharmacies),,,,,GST,$0.00,$gst,$0.00,,,,$0.00,$0.00,$0.00,O,,,3,20,20,0,0,$0.00,,,,,,,,,,,,,*None,1678<br/>";
			}		

			if($s12)
			{
				$qty = $s12;
				$totalPrice = $price * $qty;
				$totalPriceInc = $totalPrice * 1.1;
				$gst = $totalPriceInc - $totalPrice;
				echo "Symbion Health Head Office (Chemmart Pharmacies),,$storename,ATN: $contactname,\"$address1, $address2\",\"$suburb $state $postcode\",,,7/04/2014,$custPO,,A,PS64-Navy-Impromy-12,$qty,SELadies Truedry Pique Polo-12,$price,$priceinc,0%,$totalPrice,$totalPriceInc,,,Imported Symbion Health Head Office (Chemmart Pharmacies),,,,,GST,$0.00,$gst,$0.00,,,,$0.00,$0.00,$0.00,O,,,3,20,20,0,0,$0.00,,,,,,,,,,,,,*None,1678<br/>";
			}	

			if($s14)
			{
				$qty = $s14;
				$totalPrice = $price * $qty;
				$totalPriceInc = $totalPrice * 1.1;
				$gst = $totalPriceInc - $totalPrice;
				echo "Symbion Health Head Office (Chemmart Pharmacies),,$storename,ATN: $contactname,\"$address1, $address2\",\"$suburb $state $postcode\",,,7/04/2014,$custPO,,A,PS64-Navy-Impromy-14,$qty,SELadies Truedry Pique Polo-14,$price,$priceinc,0%,$totalPrice,$totalPriceInc,,,Imported Symbion Health Head Office (Chemmart Pharmacies),,,,,GST,$0.00,$gst,$0.00,,,,$0.00,$0.00,$0.00,O,,,3,20,20,0,0,$0.00,,,,,,,,,,,,,*None,1678<br/>";
			}		

			if($s16)
			{
				$qty = $s16;
				$totalPrice = $price * $qty;
				$totalPriceInc = $totalPrice * 1.1;
				$gst = $totalPriceInc - $totalPrice;
				echo "Symbion Health Head Office (Chemmart Pharmacies),,$storename,ATN: $contactname,\"$address1, $address2\",\"$suburb $state $postcode\",,,7/04/2014,$custPO,,A,PS64-Navy-Impromy-16,$qty,SELadies Truedry Pique Polo-16,$price,$priceinc,0%,$totalPrice,$totalPriceInc,,,Imported Symbion Health Head Office (Chemmart Pharmacies),,,,,GST,$0.00,$gst,$0.00,,,,$0.00,$0.00,$0.00,O,,,3,20,20,0,0,$0.00,,,,,,,,,,,,,*None,1678<br/>";
			}		

			if($s18)
			{
				$qty = $s18;
				$totalPrice = $price * $qty;
				$totalPriceInc = $totalPrice * 1.1;
				$gst = $totalPriceInc - $totalPrice;
				echo "Symbion Health Head Office (Chemmart Pharmacies),,$storename,ATN: $contactname,\"$address1, $address2\",\"$suburb $state $postcode\",,,7/04/2014,$custPO,,A,PS64-Navy-Impromy-18,$qty,SELadies Truedry Pique Polo-18,$price,$priceinc,0%,$totalPrice,$totalPriceInc,,,Imported Symbion Health Head Office (Chemmart Pharmacies),,,,,GST,$0.00,$gst,$0.00,,,,$0.00,$0.00,$0.00,O,,,3,20,20,0,0,$0.00,,,,,,,,,,,,,*None,1678<br/>";
			}			

			if($small)
			{
				$qty = $small;
				$totalPrice = $price * $qty;
				$totalPriceInc = $totalPrice * 1.1;
				$gst = $totalPriceInc - $totalPrice;
				echo "Symbion Health Head Office (Chemmart Pharmacies),,$storename,ATN: $contactname,\"$address1, $address2\",\"$suburb $state $postcode\",,,7/04/2014,$custPO,,A,PS63-Navy-Impromy-S,$qty,SE Chemmart Impromy Mens Truedry Pique Polo-S,$price,$priceinc,0%,$totalPrice,$totalPriceInc,,,Imported Symbion Health Head Office (Chemmart Pharmacies),,,,,GST,$0.00,$gst,$0.00,,,,$0.00,$0.00,$0.00,O,,,3,20,20,0,0,$0.00,,,,,,,,,,,,,*None,1678<br/>";
			}			
			
			if($medium)
			{
				$qty = $medium;
				$totalPrice = $price * $qty;
				$totalPriceInc = $totalPrice * 1.1;
				$gst = $totalPriceInc - $totalPrice;
				echo "Symbion Health Head Office (Chemmart Pharmacies),,$storename,ATN: $contactname,\"$address1, $address2\",\"$suburb $state $postcode\",,,7/04/2014,$custPO,,A,PS63-Navy-Impromy-M,$qty,SE Chemmart Impromy Mens Truedry Pique Polo-M,$price,$priceinc,0%,$totalPrice,$totalPriceInc,,,Imported Symbion Health Head Office (Chemmart Pharmacies),,,,,GST,$0.00,$gst,$0.00,,,,$0.00,$0.00,$0.00,O,,,3,20,20,0,0,$0.00,,,,,,,,,,,,,*None,1678<br/>";
			}			

			if($large)
			{
				$qty = $large;
				$totalPrice = $price * $qty;
				$totalPriceInc = $totalPrice * 1.1;
				$gst = $totalPriceInc - $totalPrice;
				echo "Symbion Health Head Office (Chemmart Pharmacies),,$storename,ATN: $contactname,\"$address1, $address2\",\"$suburb $state $postcode\",,,7/04/2014,$custPO,,A,PS63-Navy-Impromy-L,$qty,SE Chemmart Impromy Mens Truedry Pique Polo-L,$price,$priceinc,0%,$totalPrice,$totalPriceInc,,,Imported Symbion Health Head Office (Chemmart Pharmacies),,,,,GST,$0.00,$gst,$0.00,,,,$0.00,$0.00,$0.00,O,,,3,20,20,0,0,$0.00,,,,,,,,,,,,,*None,1678<br/>";
			}			
			
			if($xl)
			{
				$qty = $xl;
				$totalPrice = $price * $qty;
				$totalPriceInc = $totalPrice * 1.1;
				$gst = $totalPriceInc - $totalPrice;
				echo "Symbion Health Head Office (Chemmart Pharmacies),,$storename,ATN: $contactname,\"$address1, $address2\",\"$suburb $state $postcode\",,,7/04/2014,$custPO,,A,PS63-Navy-Impromy-XL,$qty,SE Chemmart Impromy Mens Truedry Pique Polo-XL,$price,$priceinc,0%,$totalPrice,$totalPriceInc,,,Imported Symbion Health Head Office (Chemmart Pharmacies),,,,,GST,$0.00,$gst,$0.00,,,,$0.00,$0.00,$0.00,O,,,3,20,20,0,0,$0.00,,,,,,,,,,,,,*None,1678<br/>";
			}			
			
			if($twoXL)
			{
				$qty = $twoXL;
				$totalPrice = $price * $qty;
				$totalPriceInc = $totalPrice * 1.1;
				$gst = $totalPriceInc - $totalPrice;
				echo "Symbion Health Head Office (Chemmart Pharmacies),,$storename,ATN: $contactname,\"$address1, $address2\",\"$suburb $state $postcode\",,,7/04/2014,$custPO,,A,PS63-Navy-Impromy-2XL,$qty,SE Chemmart Impromy Mens Truedry Pique Polo-2XL,$price,$priceinc,0%,$totalPrice,$totalPriceInc,,,Imported Symbion Health Head Office (Chemmart Pharmacies),,,,,GST,$0.00,$gst,$0.00,,,,$0.00,$0.00,$0.00,O,,,3,20,20,0,0,$0.00,,,,,,,,,,,,,*None,1678<br/>";
			}			
			
			if($threeXL)
			{
				$qty = $threeXL;
				$totalPrice = $price * $qty;
				$totalPriceInc = $totalPrice * 1.1;
				$gst = $totalPriceInc - $totalPrice;
				echo "Symbion Health Head Office (Chemmart Pharmacies),,$storename,ATN: $contactname,\"$address1, $address2\",\"$suburb $state $postcode\",,,7/04/2014,$custPO,,A,PS63-Navy-Impromy-3XL,$qty,SE Chemmart Impromy Mens Truedry Pique Polo-3XL,$price,$priceinc,0%,$totalPrice,$totalPriceInc,,,Imported Symbion Health Head Office (Chemmart Pharmacies),,,,,GST,$0.00,$gst,$0.00,,,,$0.00,$0.00,$0.00,O,,,3,20,20,0,0,$0.00,,,,,,,,,,,,,*None,1678<br/>";
			}
			echo "<br/>";
			$j++;
      }
   }
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
