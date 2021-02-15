<?php
define('INCLUDE_CHECK',true);

$home = dirname(__FILE__) . "/../";
$lib = $home . "/lib";

require_once($home . '/globals.php');
require_once($lib . '/functions.php');
require_once($lib . '/htmlGenerator.php');
require_once($lib . '/database.php');
require_once('warrantyclass.php');

$prod_id = $_REQUEST['prod_id'];
//echo "pay: $payable voucher: $voucher_no<BR>";

$warranty = new warranty();
echo json_encode($warranty->getSize($prod_id));



//$payable = 0;
//$remaining = 30.21;
//send back array of lineitems
//{lineitems: [{"prod_id" : abc, "myob_code" : def}, {etc}]}
//echo '{payable:'.(float)$payable.',remaining:'.(float)$remaining.'}';
//echo "{payable:$payable,remaining:$remaining}";



//$img=mysql_real_escape_string(end(explode('/',$_POST['img'])));
//
//$query = "SELECT * FROM in2cricket_items WHERE img='".$img."'";
//$res = db_query($query);
//
//$id = db_result($res, 0, 'id');
//$name = db_result($res, 0, 'name');
//$desc = db_result($res, 0, 'description');
//$price = db_result($res, 0, 'price');
//
////echo '{'.$query.'}';
////echo '{status:1}';
//echo '{status:1,id:'.$id.',price:'.$price.',txt:\'<table width="100%" id="table_'.$id.'"><tr><td width="60%">'.$name.'</td><td width="10%">$'.$price.'</td><td width="15%"><input name="'.$id.'_cnt" id="'.$id.'_cnt" onchange="change('.$id.');" size="4"/></td><td width="15%"><a href="#" onclick="remove('.$id.');return false;" class="remove">remove</a></td></tr></table>\'}';

?>
