<?php
session_start();
$home = dirname(__FILE__) . "/../";
$lib = $home ."/lib/";

require_once($lib . 'database.php');
require_once($home . '/globals.php');
require_once($lib . 'functions.php');
require_once($lib . 'loginfunctions.php');
require_once($lib . 'htmlGenerator.php');

$chest = _checkIsSet("Chest");
$waist = _checkIsSet("Waist");
$hip = _checkIsSet("Hip");
$lowwaist = _checkIsSet("Lowwaist");
$collar = _checkIsSet("Collar");
$prod_id = _checkIsSet("prod_id");
$cat_id = _checkIsSet("cat_id");
$range = _checkIsSet("range");

$chestMax = 0;
$waistMax = 0;
$hipMax = 0;
$lowwaistMax = 0;
$collarMax = 0;

$chestMin = 0;
$waistMin = 0;
$hipMin = 0;
$lowwaistMin = 0;
$collarMin = 0;

$numToAdd = 3;
$numToMinus = 2;
/*
if($prod_id == 42 || $prod_id == 43)
{
	$numToAdd = 2;
	$numToMinus = 3;
}
else if($range == 2 && $cat_id == 2)
{
	$numToAdd = 0;
	$numToMinus = 0;	
	$collar = ceil($collar);
}
*/
//if($prod_id == 86 || $prod_id == 87 || $prod_id == 88)
//{
//   $numToAdd = 4;
//   $numToMinus = 2;
//}
//else
//shirts?
// if($prod_id == 10 || $prod_id == 11)
// {
//    $numToAdd = 0;
//    $numToMinus = 0;
// }

	$numToAdd = 3;
	$numToMinus = 4;

$printAnd = true;
//echo "Chest: $chest, Waist; $waist, Hip; $hip, Lowwaist; $lowwaist, collar; $collar";

$query = "select * from sizes where prod_id = $prod_id";
// and (chest between 92 and 97) or (waist between 96 and 100) or (hip between 94 and 99) order by size_id desc";
//query is orders to have smallest size first so we just take the smallest measurement and if measurement
//entered is smaller than whats in the db, set smallest to what is in the db.
$resSizes = db_query($query);
if($chest)
{
   $tmpChest = db_result($resSizes, 0, "chest");
   if($tmpChest > $chest)
   {
      $numToAdd = 0;
      $chest = $tmpChest;
   }

   $chestMax = $chest + $numToAdd;
   $chestMin = $chest - $numToMinus;
}
if($waist)
{
   $tmpWaist = db_result($resSizes, 0, "waist");
   if($tmpWaist > $waist)
   {
      $waist = $tmpWaist;
      $numToAdd = 0;
   }
   $waistMax = $waist + $numToAdd;
   $waistMin = $waist - $numToMinus;
}
if($hip)
{
   $tmpHip = db_result($resSizes, 0, "hip");
   if($tmpHip > $hip)
   {
      $hip = $tmpHip;
      $numToAdd = 0;
   }
   $hipMax = $hip + $numToAdd;
   $hipMin = $hip - $numToMinus;
}
if($lowwaist)
{
   $tmpLowwaist = db_result($resSizes, 0, "lowwaist");
   if($tmpLowwaist > $lowwaist)
   {
      $lowwaist = $tmpWaist;
      $numToAdd = 0;
   }
   $lowwaistMax = $lowwaist + $numToAdd;
   $lowwaistMin = $lowwaist - $numToMinus;
}
if($collar)
{
   $tmpCollar = db_result($resSizes, 0, "collar");
   if($tmpCollar > $collar)
   {
      $collar = $tmpCollar;
      $numToAdd = 0;
   }
$numToAdd=0;
$numToMinus=0;

   $collarMax = $collar + $numToAdd;
   $collarMin = $collar - $numToMinus;
}


if($chest)
{
   if($printAnd)
   {
      $query .= " and (";
      $printAnd = false;
   }
   else
      $query .= " or";
   $query .= " (chest between $chestMin and $chestMax)";
}
if($waist)
{
   if($printAnd)
   {
      $query .= " and (";
      $printAnd = false;
   }
   else
      $query .= " or";
   $query .= " (waist between $waistMin and $waistMax)";
}
if($hip)
{
   if($printAnd)
   {
      $query .= " and (";
      $printAnd = false;
   }
   else
      $query .= " or";
   $query .= " (hip between $hipMin and $hipMax)";
}
if($lowwaist)
{
   if($printAnd)
   {
      $query .= " and (";
      $printAnd = false;
   }
   else
      $query .= " or";
   $query .= " (lowwaist between $lowwaistMin and $lowwaistMax)";
}
if($collar)
{
   if($printAnd)
   {
      $query .= " and (";
      $printAnd = false;
   }
   else
      $query .= " or";
   $query .= " (collar between $collarMin and $collarMax)";
}

$query .= ") order by size_id desc";

$res = db_query($query);
$num = db_numrows($res);
// echo "$query<BR>";
//echo "$num<BR>";
if($num > 0)
{
//    if($prod_id == 9)
//    {
//       if($num > 1)
//       {
//          $s1 = db_result($res, 0, "size");
//          $s2 = db_result($res, 1, "size");
//          $sizerec = "";
//          $test = substr($s1, -1);
//          if(substr($s1, -1) == "S")
//             $sizerec .= "$s1 short fit ";

//          if(substr($s1, -1) == "R")
//             $sizerec .= "$s1 regular fit ";

//          if(substr($s2, -1) == "S")
//             $sizerec .= "$s2 short fit ";

//          if(substr($s2, -1) == "R")
//             $sizerec .= "$s2 regular fit ";

//          echo $sizerec;
//       }
//       else
//       {
//          db_result($res, 0, "size");
//       }
 //  }
  // else
  if($num > 1 && !$collar)
  {
  		echo "\n".db_result($res, 0, "size") . " Comfortable Fit\n";
  		echo db_result($res, 1, "size") . " Tight Fit";  		
  }
  else if($collar > 0)
  {
  		echo "\n".db_result($res, 0, "size");  	
  }
  	else
  	{
  		$tempSize = db_result($res, 0, "size");
  		//$tempSize = substr($tempSize, 0, strlen($tempSize)-1);
      echo $tempSize;
  	}
}
else
   echo "special";

// echo " $query";

?>
