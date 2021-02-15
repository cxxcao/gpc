<?php
   error_reporting(E_ALL);
   ini_set("display_errors", 1);
session_start();
$home = dirname(__FILE__);
$lib = $home ."/lib/";

// echo "home: $home<BR>";
// echo "lib: $lib<BR>";


require_once($lib . 'database.php');
require_once($lib . 'functions.php');
require_once($lib . 'dbfunctions.php');
require_once($home . '/globals.php');


$action = _checkIsSet("action");
$invType = _checkIsSet("invtype");
$invNumber = _checkIsSet("invnum");
$businessGroup = _checkIsSet("bg");
$purchaserName = _checkIsSet("purchasername");
$invTotal = _checkIsSet("invtotal");

if($action == "submit")
{
	
	if(!$invType && !$invNumber && !$businessGroup && !$purchaserName && !$invTotal)
	{
		echo "Invoice Type, Invoice #, Business Group (IMED or JHCJV or WESLEY), Purchaser Name, Invoice Total are all required!<BR>";
		exit(0);
	}
	
   if ($_FILES["file"]["error"] > 0)
   {
      $_SESSION['msg'] = '<font color="'._FAILED_COLOR.'">ERROR UPLOADING FILE, PLEASE TRY AGAIN</font>';
   }
   else
   {
      $target_path = "$home/uploads/";
      $target_path = $target_path . basename($_FILES['file']['name']);
     $filename = $_FILES['file']['tmp_name'];

     // echo "FILENAME: $filename [$target_path]<BR>";
   }

   //echo "<table><tr><td>prodid</td><td>measure</td><td>myob_code</td><td>size</td><td>onhand</td><tr>";
   if(($handle = fopen($filename,"r")) == NULL)
   {
      echo "FAILED TO OPEN FILE<BR>";
   }
   else
   {
   	
   	$prodDescArr = array(
"2113M-Sorbtek-FrenchNavy"=>"Mat. pant",
"I-MED Tie"=>"Woven tie",
"DRJ Tie"=>"Woven tie",
"DTY Mens Belt 9124-Blk"=>"Belt-Black",
"3115S-FrenchNavy"=>"Pencil skirts",
"I-MED Scarf"=>"Chiffon scarf",
"WMI Scarf"=>"Chiffon scarf",
"3113S-FrenchNavy"=>"Box pleat skirt",
"PS63-Navy"=>"TrueDry polo SS",
"9003-Navy"=>"V-neck pullover",
"2108S-FrenchNavy"=>"Relaxed leg pant",
"PM800-Pant-Navy"=>"Flat front pants",
"PM800-Pant"=>"Pants SM",   			
"9004-Navy"=>"Mens V-neck vest",
"6245P-Orange"=>"Keyhole Blouse SS",
"2108E-Sorbtek-FrenchNavy"=>"Pant elastic back",
"1115S-FrenchNavy"=>"Zip front tech JK",
"6244-Navy"=>"Tuck front tee SS",
"DTY Ladies Belt-Orange"=>"Slim belt",
"DRJ Scarf"=>"Lightweight scarf",
"9004W-Navy"=>"Womens V-neck vest",
"6214XLA-White/Midnight"=>"Pinstripe SS",
"4214XLA-White/Midnight"=>"Pinstripe LS",
"4214XLA-SS-White/Midnight"=>"Pinstripe SS",
"4117S-FrenchNavy"=>"Open K/H dress",
"9004W-Navy"=>"Womens V-neck vest",
"4117S-FrenchNavy"=>"Open Keyhole dress",
"6210-Blue Check"=>"Mini check SS",
"6338P-Orange"=>"Keyhole Blouse 3/4S",
"6246M-Orange"=>"Keyhole Mat. blouse",
"6246M"=>"Keyhole Mat. blouse",   			
"5WT-Blue Check-LS"=>"Mini check LS",
"5WT-Blue Check-SS"=>"Mini check SS",
"6344-Navy"=>"Tuck front tee 3/4S",
"8005-Navy"=>"Cardigan",
"1116S-FrenchNavy"=>"Classic 2-Btn JK",
"PM702-Jkt-Navy"=>"Classic 2-Btn JK",
"8005-Navy"=>"Cardigans Loose fit",
"1116S-FrenchNavy"=>"Classic 2-Btn JK",
"5WR-SS-Tailored-White"=>"Self stripe SS",
"5WR-Tailored-White"=>"Self stripe LS",
"6210CP-White"=>"Self stripe SS",
"6301XLA-White/Midnight"=>"Pinstripe 3/4S",
"1110S"=>"Intgrtd lapel JK",
"1110S-FrenchNavy"=>"Intgrtd lapel JK",
"6301-Blue Check"=>"Mini check 3/4S",
"6243-Navy"=>"Boat neck tee SS",
"2112S-FrenchNavy"=>"Cropped leg pant",
"6301CP-White"=>"Self stripe 3/4S",
"6301CP"=>"Self stripe 3/4S",   			
"6334-Navy"=>"Boat neck tee 3/4S",
"6303M-White/Midnight"=>"P.stripe Mat. shirt 3/4S",
"WMIOD9"=>"IM WMI scarf",
"DRJOE1"=>"IM DRJ scarf",   			
"IMDAH8"=>"IM Mens Tie", 
"DRJAHA"=>"DRJ Mens Tie",
"IMDOE7"=>"IM BELT",   			
"Misc"=>"Misc", "Plain"=>"Plain",
"CreditCard"=>"Credit Card",
"6338P"=>"Keyhole Blouse 3/4S"
   	);
   	
   	$entityArr = array(
'00ADMI'=>'2100',
'00IRAD'=>'2100',
'00ONRA'=>'2100',
'00ACCR'=>'002000',
'02ARMD'=>'21100',
'02AUBN'=>'21100',
'02BALG'=>'21100',
'02BRWA'=>'21100',
'02BWNM'=>'21100',
'02CAHL'=>'21100',
'02CAMP'=>'21100',
'02CANM'=>'21100',
'02CCAD'=>'21100',
'02CCTP'=>'21100',
'02CMPH'=>'21100',
'02COIT'=>'21100',
'02COMK'=>'21100',
'02DEWY'=>'21100',
'02DNMM'=>'21100',
'02DYGD'=>'21100',
'02ERIN'=>'21100',
'02FRFO'=>'21100',
'02GONM'=>'21100',
'02KANM'=>'21100',
'02LDFD'=>'21100',
'02LIVE'=>'21100',
'02LJTY'=>'21100',
'02NRLN'=>'21100',
'02NWAD'=>'21100',
'02PCTP'=>'21100',
'02PMRD'=>'21100',
'06ACTP'=>'21100',
'06CWAD'=>'21100',
'06DEAK'=>'21100',
'06TUGG'=>'21100',
'06WODN'=>'21100',
'03BELE'=>'31200',
'03BERW'=>'31200',
'03BHPH'=>'31200',
'03BLAC'=>'31200',
'03BOBS'=>'31200',
'03BORO'=>'31200',
'03BOXH'=>'31200',
'03BROA'=>'31200',
'03BUND'=>'31200',
'03CACU'=>'31200',
'03CASE'=>'31200',
'03CAUL'=>'31200',
'03COLL'=>'31200',
'03COMO'=>'31200',
'03CRAN'=>'31200',
'03CROY'=>'31200',
'03DARA'=>'31200',
'03DONT'=>'31200',
'03ELTH'=>'31200',
'03EPEC'=>'31200',
'03EPPI'=>'31200',
'03FRAN'=>'31200',
'03FRPH'=>'31200',
'03FRPP'=>'31200',
'03GLWA'=>'31200',
'03GREE'=>'31200',
'03HALL'=>'31200',
'03HOCR'=>'31200',
'03JFAW'=>'31200',
'03LALO'=>'31200',
'03LILY'=>'31200',
'03LINA'=>'31200',
'03MADI'=>'31200',
'03MALB'=>'31200',
'03MALS'=>'31200',
'03MENT'=>'31200',
'03MERC'=>'31200',
'03MONA'=>'31200',
'03MRAD'=>'31200',
'03MTWA'=>'31200',
'03NEAD'=>'31200',
'03NORT'=>'31200',
'03RESE'=>'31200',
'03ROSE'=>'31200',
'03SJOG'=>'31200',
'03STHE'=>'31200',
'03THBA'=>'31200',
'03THVA'=>'31200',
'03VICO'=>'31200',
'03VIHO'=>'31200',
'03VIMK'=>'31200',
'03VIMY'=>'31200',
'03WANM'=>'31200',
'03WARR'=>'31200',
'03WERR'=>'31200',
'03WYND'=>'31200',
'02CARV'=>'32100',
'02GRIF'=>'32100',
'02RRMV'=>'32100',
'02RVAD'=>'32100',
'02WAGG'=>'32100',
'03ALBE'=>'32100',
'03ALBU'=>'32100',
'03BOAD'=>'32100',
'03BRMV'=>'32100',
'03GIAD'=>'32100',
'03GPCO'=>'32100',
'03GPMV'=>'32100',
'03HOYL'=>'32100',
'03KAYS'=>'32100',
'03KIRK'=>'32100',
'03LRHO'=>'32100',
'03RAMS'=>'32100',
'03RITP'=>'32100',
'03WGHO'=>'32100',
'03WGNM'=>'32100',
'04BAYV'=>'32100',
'04CALV'=>'32100',
'04CHAR'=>'32100',
'04DEVO'=>'32100',
'04HOPR'=>'32100',
'04KING'=>'32100',
'04MERS'=>'32100',
'04NTMV'=>'32100',
'04NUCM'=>'32100',
'04NWMC'=>'32100',
'04QUTN'=>'32100',
'04RIVO'=>'32100',
'04ROSN'=>'32100',
'04STHE'=>'32100',
'04STJN'=>'32100',
'04STLU'=>'32100',
'04STMV'=>'32100',
'04SVIN'=>'32100',
'04TAAD'=>'32100',
'04TASA'=>'32100',
'08DWPH'=>'81500',
'08NOMV'=>'81500',
'08NTAD'=>'81500',
'08PALM'=>'81500',
'08RDWH'=>'81500',
'07EMER'=>'71600',
'07FRIE'=>'71600',
'07GLMA'=>'71600',
'07MISA'=>'72215',
'07NROC'=>'71600',
'07QLTY'=>'72215',
'07QUAD'=>'71600',
'07QUMK'=>'72215',
'07ROBA'=>'71600',
'07ROMA'=>'71600',
'07YEPP'=>'71600',
'07BNAD'=>'72215',
'07BURA'=>'72215',
'07CAHO'=>'72215',
'07CASP'=>'72215',
'07CHER'=>'72215',
'07CLAY'=>'72215',
'07GYMP'=>'72215',
'07HERS'=>'72215',
'07IPSW'=>'72215',
'07QLDE'=>'72215',
'07QUIT'=>'72215',
'07SCPH'=>'72215',
'07SIGA'=>'72215',
'07STRA'=>'72215',
'07WESL'=>'72215',
'56HERV'=>'560000',
'56MARY'=>'560000',
'56SCPH'=>'560000',
'56WESL'=>'560000',
'68ADWI'=>'680000',
'68ALIC'=>'680000',
'68BURN'=>'680000',
'68JOAD'=>'680000',
'68KURP'=>'680000',
'68MBDA'=>'680000',
'68MODB'=>'680000',
'68MPAR'=>'680000',
'68MTBA'=>'680000',
'68NOAR'=>'680000',
'68PAUG'=>'680000',
'68PLIN'=>'680000',
'68PROP'=>'680000',
'68SADE'=>'680000',
'68SAMA'=>'680000',
'68SATP'=>'680000',
'68SPOR'=>'680000',
'68SSCO'=>'680000',
'68STAN'=>'680000',
'68VICT'=>'680000',
'68WAKE'=>'680000',
'68WALL'=>'680000',
'68WHYA'=>'680000',
'00IPRC'=>'2000',
'03VIAD'=>'31200',
'03VITP'=>'31200',
'02WMPH'=>'21100',
'68SEAF'=>'680000',
'08CASU'=>'81500',
'02COAR'=>'21100',
'07QUCA'=>'71600',
'03YARW'=>'32100',
'03YARW'=>'32100',
'03YARW'=>'32100',
'02COAD'=>'21100',
'03RIDE'=>'32100',
'04NTDR'=>'32100',
'00ACCR'=>'002000',
'00ACCR'=>'002000',
'02NWTP'=>'21100',
'03NEWS'=>'32100',
'02KANW'=>'21100',
'07NRAD'=>'71600',
'02CHBR'=>'21100',
'02CHRH'=>'21100',
'02BRGA'=>'21100',
'02NAMB'=>'21100',
'68ADMI'=>'680000',
'07COLA'=>'072215',
'68GLWA'=>'680000'   			
   			
   	);
   	
   	$ignoreItemsList = array("Plain"=>"Plain","I-MED Scarf"=>"I-MED Scarf","IMDAH8"=>"IMDAH8","Misc","Misc","I-MED Tie"=>"I-MED Tie","DRJ Tie"=>"DRJ Tie", "WMI Scarf"=>"WMI Scarf", 'DRJ Scarf'=>'DRJ Scarf', 'WMIOD9'=>'WMIOD9','DRJOE1'=>'DRJOE1','DRJAHA'=>'DRJAHA');
      $contents = fgets($handle);
//      echo "$contents<BR>";
      $j = 1;
      
      if($invType == "credit")
      	$invType = "Adjustment Note";
      else 
      	$invType = "Tax Invoice";
      
      $invType = str_pad($invType, 20);//default pad spaces to right
      $invNumber = str_pad($invNumber,16);
      $vendorCode = str_pad("DES01",12);
      $vendorName = str_pad("Designs To You",30);
      $vendorABN = str_pad("39 160 358 308",20);
      $invoiceDate = str_pad("25/04/2016",10);
      
      $invTotal = bcadd($invTotal, 0,2);
      $invTotal = str_pad($invTotal, 10, "0", STR_PAD_LEFT);
      $gstVal = bcdiv($invTotal, 11,2);
      $invGST = str_pad($gstVal, 10, "0", STR_PAD_LEFT);
      
      $purchaserName = str_pad($purchaserName,15);
      $accountsReceivable = str_pad("Corneila Rice", 15);
      $accountsContact = str_pad("03 9753 2555", 15);
      $paymentTerms = str_pad("30DAYS", 8);
      $filler1 = str_pad(" ",5);
      $businessGroup = str_pad($businessGroup,6);
      $filler2 = str_pad(" ",15);
      
      $out = "$invType$invNumber$vendorCode$vendorName$vendorABN$invoiceDate$invTotal$invGST$purchaserName$accountsReceivable$accountsContact$paymentTerms$filler1$businessGroup$filler2\n";
      //echo "$out";
      
      $glCode = str_pad("007540",6);
            
      while(!feof($handle))
      {
         $contents = fgets($handle);
         //echo "$contents<BR><BR>";
         $lineArr = explode(",", $contents);

         $CardRecordID = trim($lineArr[0]);	
         $InvoiceNumber = str_pad(trim($lineArr[1]),32); //delivery docket
         $InvoiceStatusID = trim($lineArr[2]);
         $CustomerPONumber = str_pad(trim($lineArr[3]),16);
         
$date = DateTime::createFromFormat('d/m/y', trim($lineArr[4]));
			$Date = date_format($date, 'd/m/Y');         
         
         $Date = str_pad($Date,10);
         $ShipToAddress = trim($lineArr[5]);
         $ShipToAddressLine1 = trim($lineArr[6]); //name
         if(strlen($ShipToAddressLine1) > 20)
         	$ShipToAddressLine1 = explode(" ", $ShipToAddressLine1)[1];
         
         $ShipToAddressLine2 = trim($lineArr[7]);
         $ItemID = trim($lineArr[8]);
         
        
         $ItemNumber = trim($lineArr[9]);
         
         if(!in_array($ItemNumber,$ignoreItemsList))
         {
	         $ItemStr = substr($ItemNumber, 0, strrpos($ItemNumber, '-'));//remove everything after last -
	         
	         $ItemNumber = str_replace("Orange", "ORA", $ItemNumber);
	         $ItemNumber = str_replace("FrenchNavy", "FN", $ItemNumber);
	         $ItemNumber = str_replace("Sorbtek", "SORBTK", $ItemNumber);
	         $ItemNumber = str_replace("White", "WH", $ItemNumber);
	         $ItemNumber = str_replace("Tailored", "TLRD", $ItemNumber);
	         $ItemNumber = str_replace("Charcoal", "CHR", $ItemNumber);
	         $ItemNumber = str_replace("Midnight", "MD", $ItemNumber);
	         $ItemNumber = str_replace("Navy", "NV", $ItemNumber);	         
	         $ItemNumber = str_replace("DTY Ladies Belt", "Belt", $ItemNumber);         
	         $ItemNumber = str_replace("Special Make", "SP", $ItemNumber);
				$ItemNumber = str_replace("Special Mak", "SP", $ItemNumber);	         
	         $ItemNumber = str_replace("Special make", "SP", $ItemNumber);	         
	         $ItemNumber = str_replace("SpecialMake", "SP", $ItemNumber);	         
	         $ItemNumber = str_replace("Blue Check", "B-CHK", $ItemNumber);                           
         }
         
         $ItemNumber = str_pad($ItemNumber,20);
         $ItemDescription = str_pad($ShipToAddressLine1.",".$prodDescArr[$ItemStr],40);
         
         if(strlen($ItemStr)< 3)
         	echo "[$ItemStr] not found " . " [" . $lineArr[9] . "]<BR>";
//          if(!in_array($ItemStr, $prodDescArr))
//          {
//          	echo "[$ItemStr] not found " . " [" . $lineArr[9] . "]<BR>";
//          }
         //$Description = trim($lineArr[11]);
         
         $Quantity = str_pad(bcadd(trim($lineArr[12]),0,2),17, "0", STR_PAD_LEFT);
         $UnitMeasure = str_pad("EA",8);
         
         
         $TaxExclusiveUnitPrice = trim($lineArr[13]);
         //$LineNumber = trim($lineArr[14]);
         //$QuantityOnHand = trim($lineArr[15]);
         $SubtotalNoPad = bcmul(trim($lineArr[16]),1.1,2);
         $SubtotalNoPad = number_format($SubtotalNoPad, 2, '.', '');
         $Subtotal = str_pad($SubtotalNoPad,10, "0", STR_PAD_LEFT);

        	$GstSubtotal = bcdiv($Subtotal, 11,2);
         $GstSubtotal = number_format($GstSubtotal, 2, '.', '');
         $GstSubtotal = str_pad($GstSubtotal,9, "0", STR_PAD_LEFT);
         
         
			$GstCode = "PI";
			$CostCentre = substr($ShipToAddressLine2, 0,6);
         $entityCode = str_pad($entityArr[$CostCentre],6,"0",STR_PAD_LEFT);
         $filler2 = str_pad(" ",7);
         
         $tmpOut = "$glCode$ItemNumber$ItemDescription$Quantity$Quantity$UnitMeasure$InvoiceNumber$Date$CustomerPONumber$Subtotal$GstSubtotal$GstCode$CostCentre$entityCode$filler2\n";
         
         if(strlen($tmpOut) > 207)
         {
         	echo "DATA TOO LONG! " .strlen($tmpOut) ."<BR>";
         	echo "$tmpOut<BR>";
         	exit(1);
         }
         
         $out .= $tmpOut;
         //echo "$out<BR>";
      }
      
      
      $fileName = $invNumber."-".$businessGroup.".txt";
      
            $f = fopen ($fileName,'w');
         if(!$f)
             exit("ERROR");
   //       Put all values from $out to export.csv.
         fputs($f, $out);
   //      echo $out;
         fclose($f);
         header("Pragma: ");
         header("Cache-Control: ");
         header('Content-type: application/csv');
         header('Content-Disposition: attachment; filename="'.$fileName.'"');
         readfile($fileName);
   }
  // echo "</table>";
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
Invoice or Adjustment Note:<select name="invtype">
<option value="Tax Invoice">Tax Invoice</option>
<option value="credit">Adjustment Note</option>
</select></br>

Invoice#: <input type="text" name="invnum"/><br/>

Business Group:<select name="bg">
<option value="IMED">IMED</option>
<option value="JHCJV">JHCJV</option>
<option value="WESLEY">WESLEY</option>
</select></br>

Purchaser Name:<select name="purchasername">
<option value="I-MED NETWORK">IMED NETWORK</option>
<option value="DR JONES">DR JONES</option>
<option value="WESLEY MEDICAL">WESLEY MEDICAL</option>
</select></br>

Invoice Total: <input type="text" name="invtotal"/>&nbsp; no commas, inc GST<br/>

<input type="submit" name="action" value="submit">



</form>


</body>
</html>
