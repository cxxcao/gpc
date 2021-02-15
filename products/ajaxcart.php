<?php
session_start();
$home = dirname(__FILE__) . "/../";
$lib = $home ."/lib/";

require_once($home . '/globals.php');
require_once($lib . 'functions.php');
require_once($lib . 'loginfunctions.php');
require_once($lib . 'htmlGenerator.php');
require_once('ordersclass.php');
require_once('productsclass.php');

$prod_id = _checkIsSet('prod_id');
$action = _checkIsSet('action');
$qty = _checkIsSet('qty');
$size = _checkIsSet('size');
$emb =_checkIsSet('emb');

if(!$_SESSION['order'])
{
   $orders = new orders();
}
else
{
   $orders = unserialize($_SESSION['order']);
}
if($action == "add")
{
   if(user_isloggedin())
   {
//      $orders->addLineItem($qty, $prod_id, $size, 0);
      $orders->addLineItem($qty, $prod_id, $size,0, $orders->isGST, null, $orders->status);
      $key = $prod_id . "_$size";
      $key=str_replace(array(" ","/","(",")"),"-",$key);
      
      $embroideryLogo = _checkIsSet("embroideryLogo");
      
      if($embroideryLogo)
          $key .= "_$embroideryLogo";      
      
      $qty = $orders->lineitems[$key]->qty;
      $size = $orders->lineitems[$key]->size;
      $name = $orders->lineitems[$key]->product->item_number;
      $desc = $orders->lineitems[$key]->product->description;
      
      if($embroideryLogo)
         $itemDisp = $name . "-" . $size . " <br/>$desc <br/>Name: $embroideryLogo ($qty items) ";     
      else
         $itemDisp = $name . "-" . $size . " <br/>$desc <br/>($qty items) ";     
      
      $final_cost = formatNumber($orders->lineitems[$key]->lineCost());
      $cat_id = $orders->lineitems[$key]->cat_id;
      $_SESSION['order'] = serialize($orders);
      
      //change size here?
      $size =str_replace(array(" ","/","(",")"),"-",$size);
      
      echo ('<li id="productID_' . $key . '"><a id="productID_' . $key . '" class="basketitems" href="products/ajaxcart.php?action=delete&prod_id=' . $prod_id . '&size=' . $size . '" onClick="return false;"><img src="../_img/del.png" id="productID_' . $key . '"></a> ' . $itemDisp . '- $<span class="productPrice">' . $final_cost . '</span><span class="productCategory">'.$cat_id.'_'.$qty.'_'.$prod_id.'</span></li>');
   }
   else
    echo "Please login";
}
else if($action == "delete")
{
   echo $orders->removeAllItems($prod_id, $size, $emb);

   $_SESSION['order'] = serialize($orders);
}

?>

