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
		<a href="index.html" id="logo"><img src="_img/dty_logo_sm.jpg" alt="Designs To You" /></a>
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
				&nbsp;&raquo;&nbsp;<strong>About Us</strong>
			</p>

			<!-- Search Bar
			<form action="#" method="post" id="searchForm">
				<fieldset>
					<input type="text" id="searchBar" name="searchBar" value="search here..."  onfocus="if(this.value=='search here...')this.value='';" onblur="if(this.value=='')this.value='search here...';"/>
				</fieldset>
         -->
			</form>
		</div>
	</div> <!-- end breacrumbsSection -->

	<div class="cAlign cFloat">
		<!-- Blog Post List -->
		<div id="mainSection">
			<ul id="articles">
				<li>
					<div class="articleContent">
						<h2>About Us</h2>
                  <p>
                  <b>Designs To You Pty Ltd</b> are suppliers of high quality, affordable and fashion forward...<br/>
                  <b>'Corporate Wear With A Difference'.</b>
                  </p>
                  <p>
                  DTY was established in 1999, after a gap in the existing corporate wear market was noted.  DTY provides a solution to that gap in the form of a complete range of fashionable corporate apparel tailored to the needs of individual clients. Our products are as unique and diverse as the corporate clients we service.
                  </p>
                  <p>
                  DTY's head office is based in Melbourne Australia. The principle of "customer first" underpins
                  our business strategy as we aim to provide our clients with corporate apparel they can wear
                  with confidence to proudly promote their own corporate identity.
                  </p>
                  <p>
                  DTY has supplied corporate apparel to companies Australia wide, as well as to high profile
                  internationally recognised companies. We are proactive in the development of our client's
                  uniform ranges, taking into consideration suitable fabrics, styles, working environment and
                  climatic conditions.
                  </p>
                  <p>
                  DTY's aim is to put Fashion and Style into your daily work life. Our garments are not only
                  stylish and functional, we also pride ourselves on ensuring the fit is correct and the quality
                  long lasting.
                  </p>
                  <p>
                  DTY's ready to wear collection is available as a stock service with emphasis placed on the
                  use of quality fabrics such as Polyester Wool Lycra, Washable Pure Wool Knitwear, Stretch
                  shirting and comfort Polyester shirting.
                  </p>
                  <p>
                  DTY offers more than just a range of stock service garments, we create a complete corporate
                  image, designed to meet your criteria. We welcome the opportunity to tailor a corporate
                  wear solution to your specific needs.
                  </p>
                  <p>
                  The DTY range is a unique mix of contemporary styling and classic appeal. If you are looking
                  for a way to stand out from the rest DTY is the answer.
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