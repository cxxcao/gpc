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

$action = _checkIsSet("action");

if(!user_isloggedin())
{
   header("Location: " . _CUR_HOST. _DIR);
}

if($action == "new")
{
   unset($_SESSION["order"]);

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
//   $('.slidedeck').slidedeck().options.scroll = false;
//   $(".iframe").fancybox({
//      'width'        : '50%',
//      'height'       : '75%',
//      'autoScale'    : false,
//      'transitionIn' : 'none',
//      'transitionOut': 'none',
//      'type'         : 'iframe'
//   });

   $(".prodimg").fancybox({
      'titlePosition'   : 'inside'
   });

});
</script>

            <!-- Fire up the slider -->
            <script type="text/javascript">
                // The most basic implementation using the default options

            </script>
<body>

	<div id="topHeader" class="cAlign">

		<!-- Logo -->
		<a href="<?php  echo _CUR_HOST . _DIR ; ?>index.php" id="logo"><img width="50%" src="<?php  echo _CUR_HOST . _DIR ; ?>_img/dtylink_logo.png" alt="DTY Link - Online Ordering System" /></a>
      <div style="float:right">
      <img width="50%" src="<?php  echo _CUR_HOST . _DIR ; ?>_img/<?php  echo _CLIENT_LOGO; ?>" alt="<?php  echo _CLIENT_ALT; ?>" />
      </div>
      <?php
      //   include('_inc/mainnav.php');
      ?>

		<div class="cBoth"><!-- --></div>
	</div> <!-- end topheader -->

	<!-- Category Section -->
	<div id="categorySection">
		<div class="cAlign">
		</div>
	</div> <!-- end categorySection -->

	<!-- Breadcrumbs -->

	<div class="cAlign cFloat">
		<!-- Blog Post List -->
		<div id="mainSection">

			<ul id="articles">
				<li>
					<div class="articleContent">
                  <form name="rangeform" method="post">
                  <?php
                     $range = _checkIsSet("range");
                     $role = _checkIsSet("role");
                  ?>
                     <p>Range:
                     <select name="range" onchange="submit()">
                     <?php
                        if($range == "Ladies")
                        {
                     ?>
                           <option value="Ladies" selected>Womenswear</option>
                     <?php
                        }
                        else
                        {
                     ?>
                           <option value="Ladies">Womenswear</option>
                      <?php
                        }
                     ?>
                     <?php
                        if($range == "Mens")
                        {
                     ?>
                           <option value="Mens" selected>Menswear</option>
                     <?php
                        }
                        else
                        {
                     ?>
                           <option value="Mens">Menswear</option>
                      <?php
                        }
                     ?>
                     </select>
                     </p>
                  </form>
                  <?php

                  if(!$range)
                     $range = "Ladies";
                     if(minAccessLevel(_ADMIN_LEVEL))
                     {
                        $query = "select * from employee_role order by name asc";
                     }
                     else
                     {
                        $role_id = $_SESSION["ROLE_ID"];
                        $query = "select * from employee_role where employeerole_id = $role_id order by name asc";
                     }
                     $res = db_query($query);
                     $num = db_numrows($res);
                     if($num > 0)
                     {
                        for($i = 0; $i < $num; $i++)
                        {
                           $role_id = db_result($res, $i, "employeerole_id");
                           $name = db_result($res, $i, "name");

                           if($role == $role_id)
                           {
                              echo "<h3>";
                  ?>
                           <a href="<?php echo _CUR_HOST ._DIR;?>products/tmporder.php?role=<?php echo $role_id;?>&range=<?php echo $range;?>"><?php echo $name;?></a> |
                  <?php
                              echo "</h3>";
                           }
                        }
                     }
                  ?>
                  <p>
   <div id="sliderSection">
      <div class="cAlign">
         <div id="slidedeckFrame" class="skin-slidedeck-basics">
           <dl class="slidedeck">

           <?php
              $cquery = "select * from category";
              $cres = db_query($cquery);
              $cnum = db_numrows($cres);
              if($cnum > 0)
              {
                 //for($c = 0; $c < $cnum; $c++)
                 $c = 0;
                 $c = _checkIsSet("c");
                 if(!$c)
                  $c = 0;
                 {
                    $name = db_result($cres, $c, _NAME);
                    $cat_id = db_result($cres, $c, _CAT_ID);

                     if(minAccessLevel(_ADMIN_LEVEL))
                     {
                        $role_id = _checkIsSet("role");
                        if(!$role_id)
                           $role_id = 3; //default to admin
                     }
                     else
                     {
                        $role_id = $_SESSION["ROLE_ID"];
                     }

                     //$query = "select * from products where cat_id = $cat_id order by category, myob_code";
                     $query = "select * from products p, employee_role er, productcategory pc where p.prod_id != 255 and p.description like '$range%' and p.cat_id = $cat_id and er.employeerole_id = $role_id and er.employeerole_id = pc.employeerole_id and pc.prod_id = p.prod_id order by category, myob_code";

                     $res = db_query($query);
                     $num = db_numrows($res);

                     if($num > 0)
                     {
           ?>
              <dt><?php echo $name;?></dt>
              <dd>
               <div class="container_12">
                  <?php
                        for($i = 0; $i < $num; $i++)
                        {
                           $imageloc = "../". _IMGLOC;
                           $prod_id = db_result($res, $i, _PROD_ID);
                           $item_number = db_result($res, $i, _ITEM_NUMBER);
                           $myobcode = db_result($res, $i, _MYOB_CODE);
                           $description = db_result($res, $i, _DESCRIPTION);
                           $fabric = db_result($res, $i, _FABRIC);
                           $price = db_result($res, $i, _PRICE);
                           $sm_img = $item_number . "_tmb.jpg";
                           $img = $item_number . ".jpg";
                           $price *=1.1;
                           //$price = formatNumber($price);

                           if($i == 0)
                              $tmpImg = $sm_img;

                           if(!file_exists($imageloc. $sm_img))
                              $sm_img = "noimage.jpg";

                           $size_text = "size_$prod_id";
                           $qty_text = "qty_$prod_id";

                  ?>

                  <div class="grid_3">
                           <a class="prodimg" href="<?php echo $imageloc . $img;?>" title="<?php echo $item_number ." - ". $description; ?>"><img alt="<?php echo $item_number;?>" src="<?php echo $imageloc . $sm_img;?>" /></a>
                     <div class="box">

                        <div class="blocktmp">
                           <p><?php echo $item_number;?></p>
                           <p><?php echo $description;?></p>
                           <p><?php echo $fabric;?></p>
                        </div>
                     </div>
                  </div>
                  <?php
                        }

//                     }
//                     else
//                     {
//                        echo "NO ITEMS ARE AVAILABLE UNDER THIS CATEGORY";
//                     }
                  ?>
               </div>
              </dd>
           <?php
                     }
                 }//category (garment type) for loop
              }
           ?>
           </dl>

         </div> <!-- end slidedeckFrame -->
      </div>
   </div> <!-- end sliderSection -->
                  </p>


					</div>
				</li>

			</ul>

			<!-- Pagination --
			<ul id="pagination">
				<li><a href="#" class="active">1</a></li>
            <!--
				<li><a href="#">2</a></li>
				<li><a href="#">3</a></li>

			</ul>-->

		</div> <!-- end mainSection -->

		<!-- Sidebar -->
		<div id="sidebar">

		</div> <!-- end sidebar -->

	</div>

   <?php
      include('../_inc/footer.php');
   ?>



	<!-- Copyright info -->
	<div id="copyrightSection">

		<!--<img src="_img/basicsSmallLogo.png" alt="Designs To You" />-->

		<p>&copy; Copyright 2011 Designs To You Pty Ltd. All rights reserved.</p>

	</div> <!-- end copyrightSection -->

</body>
</html>