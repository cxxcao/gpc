<?php
session_start();
error_reporting(0);
//error_reporting(E_ALL);
ini_set("display_errors", 0);

$home = dirname(__FILE__) . "/../";
$pdfhome = $home ."/fpdf16/";
$lib = $home ."/lib/";

define('FPDF_FONTPATH',$pdfhome . '/font/');
require_once($pdfhome . 'fpdf.php');
require_once($lib . 'functions.php');
require_once('ordersclass.php');


$order_id = _checkIsSet(_ORDER_ID);


class PDF extends FPDF
{
//Page header
function Header()
{
    global $order_id;
    //Logo
    //$this->Image('../_img/dty_bupa.jpg');
    //Arial bold 15
    $this->SetFont('Arial','B',12);
    //Move to the right
    $this->Cell(80);
    //Title


    $orders = new orders();
    $orders->LoadOrderId($order_id);

    $this->Cell(30,10,'Your Order Summary | Order No: ' . $order_id,0,0,'C' );

    $orderDate = new DateTime($orders->ordertime);

    $this->Ln(10);
    $this->SetFont('');
    $this->Cell(0,10,'Ship To:',0,0,'L');
    $this->Cell(0,10,'Order Date: ' .  $orderDate->format('d/m/Y'),0,0,'R');
    $this->Ln();
    $this->Cell(0,6,'' . $orders->fullname,0,0,'L');
    $this->Ln();
    $this->Cell(0,6, $orders->sname,0,0,'L');
    $this->Ln();
    $this->Cell(0,6, $orders->address,0,0,'L');
    $this->Ln();
    $this->Cell(0,6, $orders->suburb,0,0,'L');
    $this->Ln();
    $this->Cell(0,6, $orders->state . " " . $orders->postcode,0,0,'L');

    //Line break
    $this->Ln(20);
}

//Load data
function LoadData($file)
{
    //Read file lines
    $lines=file($file);
    $data=array();
    foreach($lines as $line)
        $data[]=explode(';',chop($line));
    return $data;
}

//Colored table
   function FancyTable($header,$order_id)
   {
       //Colors, line width and bold font
       $this->SetFillColor(0,0,255);
       $this->SetTextColor(255);
       $this->SetDrawColor(0,0,128);
       $this->SetLineWidth(.3);
       $this->SetFont('','B');
       //Header
       $w=array(10,50,70,20,20,20);
       for($i=0;$i<count($header);$i++)
           $this->Cell($w[$i],7,$header[$i],1,0,'C',true);
       $this->Ln();
       //Color and font restoration
       $this->SetFillColor(224,235,255);
       $this->SetTextColor(0);
       $this->SetFont('');
       //Data
       $fill=false;

    $orders = new orders();
    $orders->LoadOrderId($order_id);
    $lineitems = $orders->lineitems;
    $numlines = count($lineitems);

    $arrKeys = array_keys($orders->lineitems);
    $total = 0;
    for($i = 0; $i < $numlines; $i++)
    {
      $key = $arrKeys[$i];
      $li = $orders->lineitems[$key];
      $tmpCost = $li->lineCost();
      $unitCost = formatNumber($li->unitcost);
      $isGST = $li->gst;
      $final_cost = formatNumber($tmpCost);
      $item_number = $li->product->item_number;
      $desc = $li->product->description;
      $prod_id = $li->product->prod_id;
      $qty = $li->qty;
      $size = $li->size;
      $total += $tmpCost;

      $this->Cell($w[0],6,$qty,'LR',0,'R');
      $this->Cell($w[1],6,$item_number,'LR');
      $this->Cell($w[2],6,$desc,'LR');
      $this->Cell($w[3],6,$size,'LR',0,'R');
      $this->Cell($w[4],6,formatNumber($unitCost),'LR',0,'R');
      $this->Cell($w[5],6,formatNumber($tmpCost),'LR',0,'R');
      $this->Ln();
    }

    if($isGST == "N")
    {
       $totalEx = $total;
       $gst = "N/A";
    }
    else
    {
       $totalEx = $total/1.1;
       $gst = $total - $totalEx;
       $gst = formatNumber($gst);
    }



    $this->SetFont('','B');
    $this->Cell(190,6,'Totals',1,1,'R');
    $this->Cell(190,6,'Subtotal (ex GST): $' . formatNumber($totalEx),1,1,'R');
    $this->Cell(190,6,'GST: $' . $gst ,1,1,'R');
    $this->Cell(190,6,'Balance: $' . formatNumber($total),1,1,'R');
    $payable = formatNumber($orders->payable);
    $this->Cell(190,6,'Payable: $' . $payable,1,1,'R');

    if($payable > 0)
    {
//       if($orders->paymentopt == "W")
//       {
//          $this->Cell(190,6,'Wage deduction selected.',1,1,'R');
//       }
//       else 
      //if($orders->paymentopt == "C")
      {
         //$cardnum = substr ( $orders->cardnumber , 0, 6);
         $cardtype = $orders->cardtype;
         $ccdetails = $cardtype . " - " . $orders->cardnumber;
         $this->Cell(190,6,'Credit Card Details: ' . $ccdetails,1,1,'R');
      }
//       else if($orders->paymentopt == "P")
//       {
//          $this->Cell(190,6,'Payment was made via Paypal',1,1,'R');
//       }
    }

    //$this->Ln();
    /*
    $this->Cell(270,6,'COMMENTS',0,1,'L');
    $commentArr = explode("\n\r", $orders->comments);
    for($i = 0; $i < count($commentArr); $i++)
    {
       $this->Cell(270,6,$commentArr[$i],0,0,'L');
       $this->Ln();
    }
    */
//$this->Cell(155);
//Centered text in a framed 20*10 mm cell and line break

//      $this->Ln();

//       foreach($data as $row)
//       {
//           $this->Cell($w[0],6,$row[0],'LR',0,'L',$fill);
//           $this->Cell($w[1],6,$row[1],'LR',0,'L',$fill);
//           $this->Cell($w[2],6,number_format($row[2]),'LR',0,'R',$fill);
//           $this->Cell($w[3],6,number_format($row[3]),'LR',0,'R',$fill);
//           $this->Cell($w[3],6,number_format($row[3]),'LR',0,'R',$fill);
//           $this->Ln();
//           $fill=!$fill;
//       }
       $this->Cell(array_sum($w),0,'','T');
   }
}

$order_id = _checkIsSet(_ORDER_ID);
$pdf=new PDF('L');
$pdf->SetTitle($order_id);
//Column titles
$header=array('QTY','Item','Description','Size','Unit Price','Total Price');
//Data loading


$pdf->SetFont('Arial','',10);
$pdf->AddPage("P", "A4");
$pdf->FancyTable($header, $order_id);
$pdf->Output('Order'.$order_id.'.pdf', 'D');


?>