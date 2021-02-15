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
				&nbsp;&raquo;&nbsp;<strong>Services</strong>
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
						<h2>Services</h2>
                  <p>
<h3>Garment Management &amp; Distribution</h3>
</p>
<p>
<b>Designs To You</b> will cater to suit the dynamics of all businesses, large and small. Our services not only entail that of providing quality products but also servicing and delivering them with precision and attention to detail. Consequently, our delivery of goods accomodates to the preferences and needs of our customers. This includes delivering bulk and personalised orders which are all packed in the appropriate way to ensure maximum protection and care.
</p>
<p>
At <b>Designs To You</b>, customer service is regarded with the utmost importance. Hence, it is through our centralised warehouse system that we are continually able to ensure the safe and prompt delivery of goods. We also insist on employing transport systems to locate customer orders to which our staff have full access to if the need arises. Furthermore, our transport carrier will monitor and report as required the time taken to deliver all goods on a per order basis from pick up from our Distribution Centre into delivery at the address nominated by the customer.
</p>
<p>
<h3>Exclusive Design &amp; Quality</h3>
</p>
<p>
<b>What are We?</b>
</p>
<p>
Designers and manufacturers who listen and understand the needs and expectations of our clients and service them to the best of our ability.
</p>
<p>
<b>Designs To You</b> believes that, as a group of committed people, we can supply Corporate Wear with a Difference. Our aim is to incorporate Fashion and Style into the daily work lives of our customers. Our garments are not only sophisticated and functional, but we also take pride in ensuring the fit is correct and we carry a quality guarantee.
</p>
<p>
At <b>Designs To You</b>, we do not simply offer a range of stock service garments, we create a complete corporate image designed and tailored to cater for the criteria of our clients.
</p>
<p>
Our ultimate vision is to provide a collection that not only evokes a professional image, but is also comfortable to wear, affordable and easy-care; <b>a corporate wardrobe that your staff would be proud to wear.</b>
</p>
<p>
Our ready to wear collection is available as a stock service with great emphasis placed upon the use of quality fabrics such as Polyester Wool Lycra, Wool Blend Lycra, Washable Pure Wool Knitwear, Stretch Lycra suiting, Stretch shirting and shirting treated with Natural Safe Skin Care properties, all proudly manufactured in Australia.
</p>
<p>
We also specialise in made to order garments and welcome the opportunity to design and develop new products to meet your requirements.
</p>
<p>
<h3>Customer Service</h3>
</p>
<p>
We, at <b>Designs To You</b>, are dedicated to ensuring exceptional customer service at all times. Likewise, we believe in maintaining a good communication level with our customers. Our friendly staff are on hand and willing to answer any queries and facilitate any directions of customers. Meanwhile, our call centre is operative from 9.00am to 5.00pm every working day of the year, excluding public holidays.
</p>
<p>
Our staff are familiar with the products and demands of all customers as we specially cater to their diverse businesses.
Our outstanding customer service does not simply conclude once our clients have received their uniforms. On the contrary we believe in servicing our customers, ensuring that they are completely content and pleased with the wear and quality of our products. Hence, the staff at <b>Designs To You</b> will constantly remain committed and helpful.
</p>
<p>
<h3>Monogramming</h3>
</p>
<p>
<b>Designs To You</b> offers quality embroidery, sublimated printing and screen printing services to add that extra touch to your uniforms. Embroidering or printing garments both promotes your company while depicting a sophisticated and stylish image towards the public.
</p>
<p>
Consequently, <b>Designs To You</b> are able to stylise your uniforms with our quality and impecable embroidery no matter how intricate or straightforward the design.
</p>
<p>
<h3>Tax Deductibility</h3>
</p>
<p>
Design To You's range of garments are eligible to be registered in accordance with Aus Industry taxation guidelines. Hence, any personal contribution an employee makes to the cost of their uniform, which has a company logo monogram, may be claimed as a tax deduction. Alterations and any costs involved in cleaning the garments are also regarded as adequate to make tax claims. Please ask your tax adviser or accountant for further information.
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