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
   $cat_id = _checkIsSet("cat_id");
   $range = _checkIsSet("range");
   $product = new product();
   $product->LoadProductId($prod_id, "Y");

   $item_number = $product->item_number;
   $desc = $product->description;
   $category = $product->category;
   $measure = $product->measure;

   $chest = 0;
   $waist = 0;
   $hip = 0;
   $lowwaist = 0;
   $collar = 0;
   $measureArr = explode(";", $measure);
   
   //29,30,16,17
   $tableOnlyArr = array(29,30,16,17,85,86,82,71);
?>
   <!-- Category Section -->
      <div id="mainSectionCalc">
         <ul id="articles">
            <li>
               <div class="articleContentCalc">
                  <h2>Size Calculator - <?php echo $item_number;?></h2>
                  <h3><?php echo $desc;?></h3>
                  <h3>Use the garment measurements to determine the best size for you.</h3>
                  <?php 
                  if(!in_array($prod_id, $tableOnlyArr))
                  {
                  ?>
                  <p>Enter your measurements below and click on <b>Recommend</b></p>
                  <?php 
                  }
                  ?>
                  <p>
                     <form action="" method="post" id="calcform">
                        <?php
                           if($category == 1)
                              $imgsrc = "../_img/womensguide_sm.jpg";
                           else
                              $imgsrc = "../_img/mensguide_sm.jpg";
                        ?>
                     <div id="imgmeasure">
                        <img src="<?php echo $imgsrc;?>">
                     </div>

                     <?php
                  if(!in_array($prod_id, $tableOnlyArr))
                  { 
                        for($i = 0; $i < count($measureArr); $i++)
                        {
                           $section = $measureArr[$i];
                           $info = "";
                           $fontcolour = "";
                           if($section == "Chest")
                           {
                              $chest = 1;
                              if($category == 1)
                                 $fontcolour = "blue";
                              else
                                 $fontcolour = "red";
                              $info = "Stand straight with arms down. Ask a friend to measure under your arms around the fullest part of your chest and back, keeping the tape measure straight but not stretched.";
                           }
                           else if($section == "Waist")
                           {
                              $waist = 1;
                              if($category == 1)
                              {
                                 $info = "Measure the slimmest part of your natural waistline (above your navel and below your ribcage)";
                                 $fontcolour = "orange";
                              }
                              else
                              {
                                 $info = "Measure all the way around your waist where your pants/belt naturally sit. Keep the tape measure straight and a little loose.";
                                 $fontcolour = "orange";
                              }
                           }
                           else if($section == "Hip")
                           {
                              $hip = 1;
                              if($category == 1)
                                 $fontcolour = "green";
                              else
                                 $fontcolour = "";

                              $info = "Measure all the way around the fullest part of your hips and bottom, keeping the measuring tape straight but not stretched.";
                           }
                           else if($section == "Lowwaist")
                           {
                              $lowwaist = 1;
                              if($category == 1)
                                 $fontcolour = "blue";
                              else
                                 $fontcolour = "";

                              $info = "Measure between your natural waistline and hip (just below your navel)";
                           }
                           else if($section == "Collar")
                           {
                              $collar = 1;
                              $fontcolour = "blue";
                              $info = "Measure around your neck just below the Adam's apple, keeping  the tape measure a little loose for comfort.";
                           }
                     ?>

                   <div id="measure">
                     <div class="formrow">
                        <label><font color="<?php echo $fontcolour;?>"><?php echo $section;?> (cm):</font></label>
                        <span class="formwrap">
                           <input class="validate[required]" type="text" name="<?php echo $section;?>" id="<?php echo $section;?>"  value=""/>

                        </span>
                        <span class="formwrapinfo"><font color="<?php echo $fontcolour;?>"><?php echo $info;?></font></span>
                     </div>
                     <?php
                        }
                     ?>
                        <div class="formrow">
                           <label></label>
                           <span class="formwrap">
                             <input type="submit" class="confirm" id="calc" name="calc" value="Recommend"/>&nbsp;(Guide only) <br/>
                             <div id="loadercheckout"><img src="../_img/fbloader.gif" alt="loading..."/></div>
                           </span>
                        </div>
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
                           <tr><td colspan="<?php echo ($snum+1);?>"><b>GARMENT MEASUREMENTS IN CM</b></td></tr>
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
                        <b>MEASURING TIPS</b>
                                 <ul>
	         <li>1. For accurate results wear light clothing when measuring.</li>
	         <li>2. Remove items from pockets.</li>
				<li>3. Stand straight in a natural position with your arms by your side.</li>
				<li>4. Keep measuring tape flat and taut but not stretched.</li>
				<li>5. Measure in a straight line around your body.</li>
				<li>6. Ask a friend to help if needed.</li>
         </ul>
                        </div>
         <?php
         }//spec arr > 0
         ?>
         

                        </div>

                   </div>
                  <input type="hidden" name="prod_id" id="prod_id" value="<?php echo $prod_id;?>">
                  <input type="hidden" name="cat_id" id="cat_id" value="<?php echo $cat_id;?>">   
                  <input type="hidden" name="range" id="range" value="<?php echo $range;?>">                                    
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