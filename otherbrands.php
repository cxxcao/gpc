<!DOCTYPE html>

<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Designs To You - Corporate Wear with a Difference</title>

<?php include('_inc/js_css.php');?>
</head>
<body>

	<div id="topHeader" class="cAlign">

		<!-- Logo -->
		<a href="index.php" id="logo"><img src="_img/dty_logo_sm.jpg" alt="Designs To You" /></a>
      <?php
         include('_inc/mainnav.php');
      ?>

		<div class="cBoth"><!-- --></div>
	</div> <!-- end topheader -->

	<!-- Slider -->
	<div id="sliderSection">
		<div class="cAlign">
			<div id="slidedeckFrame" class="skin-slidedeck-basics">
            <?php
               include('_inc/sidedeck.php');
            ?>

				<!-- Fire up the slider -->
				<script type="text/javascript">
				    // The most basic implementation using the default options
				    $('.slidedeck').slidedeck();
				</script>
			</div> <!-- end slidedeckFrame -->
		</div>
	</div> <!-- end sliderSection -->

	<!-- Category Section -->
	<div id="categorySection">
		<div class="cAlign">
			<!-- Categories -->
         <?php
            include('_inc/middlenav.php');
         ?>

			<!-- Toggle Button -->
			<img src="_img/collapseButton.png" alt="Click here to collapse the panel" class="toggleButton" />
			<img src="_img/expandButton.png" alt="Click here to expand the panel" class="toggleButton" id="expandButton" />

			<div style="clear: both"><!-- --></div>
		</div>
	</div> <!-- end categorySection -->

	<!-- Breadcrumbs -->
	<div id="breadcrumbsSection">
		<div class="cAlign cFloat">
			<p>
				You are here:
				<a href="index.php">Home</a>
				&nbsp;&raquo;&nbsp;<strong>Other Brands</strong>
			</p>

			<!-- Search Bar
			<form action="#" method="post" id="searchForm">
				<fieldset>
					<input type="text" id="searchBar" name="searchBar" value="search here..."  onfocus="if(this.value=='search here...')this.value='';" onblur="if(this.value=='')this.value='search here...';"/>
				</fieldset>
			</form>
        -->
		</div>
	</div> <!-- end breacrumbsSection -->

	<div class="cAlign cFloat">
		<!-- Blog Post List -->
		<div id="mainSection">
			<ul id="articles">
				<li>
					<div class="articleContent">
						<h2>Other Brands</h2>

                  <p>
In addition to DTY's corporate collection, we are also authorised resellers of the following brands:
<br/>
Contact us at sales@designstoyou.com.au for a quote.
                  </p>

                  <p>
                  <h3>Corporate Shirts</h3>
                  <table width="100%">
                     <tr>
                        <td><a target="_blank" href="http://www.citycollection.com.au/"><img src="_img/otherbrands/citycollection2.jpg"></a></td>
                        <td><a target="_blank" href="http://www.johnkevin.com/"><img src="_img/otherbrands/johnkevin2.gif"></a></td>
                     </tr>
                  </table>
                  <h3>Imagewear/Sportswear</h3>
                  <table width="100%">
                     <tr>
                        <td><a target="_blank" href="http://www.jbswear.com.au/"><img src="_img/otherbrands/jbs-wear-bw2.jpg"></a></td>
                        <td><a target="_blank" href="http://www.bizcollection.com.au/"><img src="_img/otherbrands/Biz-Collection2.jpg"></a></td>
                     </tr>
                     <tr>
                        <td><a target="_blank" href="http://www.gearforlife.net/"><img src="_img/otherbrands/gearForLifeLogo2.jpg"></a></td>
                        <td><a target="_blank" href="http://www.auspirit.com/"><img src="_img/otherbrands/AuSpiritLogo2.jpg"></a></td>
                     </tr>
                     <tr>
                        <td><a target="_blank" href="http://www.winningspirit.biz/"><img src="_img/otherbrands/winningspirit2.jpg"></a></td>
                        <td><a target="_blank" href="http://www.jamesharvest.com.au/"><img src="_img/otherbrands/jamesharvest2.jpg"></a></td>
                     </tr>
                  </table>
                  <h3>Headwear</h3>
                  <table width="100%">
                     <tr>
                        <td><a target="_blank" href="http://www.headwear.com.au/"><img src="_img/otherbrands/headwear-logo2.jpg"></td>
                        <td><a target="_blank" href="http://www.epiclegend.com/"><img src="_img/otherbrands/epic_legend2.jpg"></a></td>
                     </tr>
                  </table>
                  <h3>Bags, Accessories &amp; Promotional Products</h3>
                  <table width="100%">
                     <tr>
                        <td><a target="_blank" href="http://www.epiclegend.com/"><img src="_img/otherbrands/epic_legend2.jpg"></a></td>
                        <td><a target="_blank" href="http://www.imagecollection.com.au/"><img src="_img/otherbrands/imagecollection2.jpg"></a></td>
                     </tr>
                  </table>

                  <h3>Workwear</h3>
                  <table width="100%">
                     <tr>
                        <td><a target="_blank" href="http://www.dncworkwear.com.au/"><img src="_img/otherbrands/dnc2.jpg"></a></td>
                        <td><a target="_blank" href="http://www.bisleyworkwear.com.au/"><img src="_img/otherbrands/bisley2.jpg"></a></td>
                     </tr>
                  </table>
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
      include('_inc/footer.php');
   ?>

</body>
</html>