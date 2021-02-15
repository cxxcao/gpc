<?php
session_start();
   error_reporting(E_ALL);
   ini_set("display_errors", 1);
$home = dirname(__FILE__) . "/../";
$lib = $home ."/lib/";

require_once($home . '/globals.php');
require_once($lib . 'functions.php');
require_once($lib . 'loginfunctions.php');
require_once($lib . 'htmlGenerator.php');
require_once('ordersclass.php');
require_once('productsclass.php');
require_once('../account/locationclass.php');

$action = _checkIsSet("action");

if(!user_isloggedin())
{
   header("Location: " . _CUR_HOST. _DIR);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="utf-8">
   <title>Designs To You - DTYLink Online Ordering</title>
   <?php include('../_inc/js_css.php');?>
</head>

<script type="text/javascript">
$(document).ready(function()
{
   $("#loadercheckout").hide();
   $("#calcform").validationEngine();

   $(".confirm").click(function(e)
   {
      if(!$("#calcform").validationEngine('validate'))
         return false;

      $("#loadercheckout").show();
      var vals = $("#calcform").serialize();
      var prod_id = $("#prod_id").val();
      $.ajax(
      {
         type: "POST",
         url: "performcalc.php",
         data: vals,
         //dataType: 'json', // expecting json
         success: function(msg)
         {
            alert('Recommended Size: ' + msg);
            $("#loadercheckout").hide();
            return false;
         },
         failure: function(msg)
         {
            alert('Error!');
            return false;
         }
      });
        e.preventDefault();
   });

});


</script>

<body>
<?php
   $prod_id = _checkIsSet("prod_id");
   $product = new product();
   $product->LoadProductId($prod_id, "Y");

   $item_number = $product->item_number;
   $category = $product->category;
   $measure = $product->measure;
   $description = $product->description;
   $colour = $product->colour;

   $chest = 0;
   $waist = 0;
   $hip = 0;
   $lowwaist = 0;
   $collar = 0;
   $measureArr = explode(";", $measure);
?>
   <!-- Category Section -->
      <div id="mainSectionCalc">
         <ul id="articles">
            <li>
               <div class="articleContentCalc">
                  <h2>Garment Information - <?php echo $item_number;?></h2>
                  <p>
                     <form action="" method="post" id="calcform">
                        <?php
                           $imgsrc = $item_number . ".jpg";
                           $imageloc = "../". _IMGLOC;
                        ?>
                     <div id="imgmeasure">
                        <img alt="<?php echo $item_number;?>" src="<?php echo $imageloc . $imgsrc;?>" width="30%" />
                        <p>
                        <?php echo $description;?>
                        </p>
                        <p>
                        <?php echo $colour;?>
                        </p>
                     </div>

                     <?php
//                        for($i = 0; $i < count($measureArr); $i++)
                        {

                     ?>

                   <div id="measure">
                     <?php
                        }
                     ?>

                        <div class="formrow">
      <?php
         //get what to measure
         $query = "select measure from products where prod_id = $prod_id";
         $res = db_query($query);
         $num = db_numrows($res);
         $measure = "";
         if($num > 0)
            $measure = db_result($res, 0, "measure");
         $measureArr = explode(";", $measure);
         $measure = "";
         $numMeasure = count($measureArr);

         for($i = 0; $i < $numMeasure; $i++)
         {
            $measure .= $measureArr[$i];
            if($i+1 < $numMeasure)
               $measure .= ",";
         }

         //now get the sizes
         if(strlen($measure) > 1)
            $query = "select size,$measure from sizes where prod_id = $prod_id and size != 'special'";
         else
            $query = "select size from sizes where prod_id = $prod_id and size != 'special'";
//         echo "$query<BR>";
         $sres = db_query($query);
         $snum = db_numrows($sres);
         $specArr = array();
         $measure = $measureArr[0];
         $m = db_result($sres, 0, $measureArr[0]);

         if(strlen($m) > 0)
         {
      ?>

                        <div class="formrow">
                        <table id="sizemeasure">
                           <tr><td colspan="<?php echo ($snum+1);?>"><b>GARMENT SPECIFICATIONS</b></td></tr>
                           <tr>
                              <td><b>Size</b></td>
                              <?php
                                 for($i = 0, $z=0; $i < ($snum); $i++) //dont want special
                                 {
                                    $s = db_result($sres, $i, "size");
                                    echo "<td><b>$s</b></td>";
                                    for($j = 0; $j < $numMeasure; $j++)
                                    {
                                       $measure = $measureArr[$j];
                                       $m = db_result($sres, $i, $measure);
                                       array_push($specArr, $m);
                                    }
                                 }
                              ?>
                           </tr>
                           <?php
                              for($j = 0; $j < $numMeasure; $j++)
                              {
                                 $measure = $measureArr[$j];
                                 echo"<tr><td><b>$measure (cm)<b></td>";

                                 for($z = 0+$j; $z < count($specArr); $z+=$numMeasure)
                                 {
                                    $spec = $specArr[$z];
                                    echo "<td>$spec</td>";

                                 }
                                 echo"</tr>";
                              }
                              $z++;
                              echo "<tr><td colspan='$z'>Disclaimer: This is intended to be a guide only, and while we do our best to ensure all our sizing is consistent, you may find that some styles will vary in size.</td></tr>";
                           ?>

                        </table>
                        </div>
         <?php
         }//spec arr > 0
         ?>
                        </div>

                   </div>
                  <input type="hidden" name="prod_id" id="prod_id" value="<?php echo $prod_id;?>">
                     </form>
                  </p>
               </div>
            </li>
            <li>

            </li>
         </ul>
      </div> <!-- end mainSection -->
</body>
</html>