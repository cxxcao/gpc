
<script type="text/javascript">
   $(document).ready(function(){

         function rotate1() {
            //Get the first image
            var current = ($('div#rotator ul li.show')?  $('div#rotator ul li.show') : $('div#rotator ul li:first'));

            //Get next image, when it reaches the end, rotate it back to the first image
            var next = ((current.next().length) ? ((current.next().hasClass('show')) ? $('div#rotator ul li:first') :current.next()) : $('div#rotator ul li:first'));

            //Set the fade in effect for the next image, the show class has higher z-index
            next.css({opacity: 0.0})
            .addClass('show')
            .animate({opacity: 1.0}, 1000);

            //Hide the current image
            current.animate({opacity: 0.0}, 1000)
            .removeClass('show');

         }

       function theRotator() {
         //Set the opacity of all images to 0
         $('div#rotator ul li').css({opacity: 0.0});

         //Get the first image and display it (gets set to full opacity)
         $('div#rotator ul li:first').css({opacity: 1.0});

         //Call the rotator function to run the slideshow, 6000 = change to next image after 6 seconds
         setInterval(function(){rotate1();},6000);

      }
      theRotator();

    $(function() {
        $('#rotator a').lightBox();
    });
   })
</script>
            <dl class="slidedeck">
                <dt>Designs To You</dt>
                <dd>
                  <div id="rotator">
                    <ul class="galleryul">
                      <li class="show">
                        <a href="_img/gallery/8-large.jpg" title="4104PWL Charcoal Dress">
                            <img src="_img/gallery/8.jpg" alt="4104PWL Charcoal Dress" />
                        </a>
                      </li>

                      <li>
                        <a href="_img/gallery/1-large.jpg" title="6301P Navy Blouse with 2108PWL Textured Grey Pant">
                            <img src="_img/gallery/1.jpg" alt="6301P Navy Blouse with 2108PWL Textured Grey Pant" />
                        </a>
                      </li>
                      <li>
                        <a href="_img/gallery/2-large.jpg" title="1105PWL Textured Grey Jacket 6220P Camisole and 3112PWL Grey Skirt">
                            <img src="_img/gallery/2.jpg" alt="1105PWL Textured Grey Jacket 6220P Camisole and 3112PWL Grey Skirt" />
                        </a>
                      </li>
                      <li>
                        <a href="_img/gallery/3-large.jpg" title="1101PWL French Navy Jacket with 6216UP Print Blouse and 3112PWL French Navy Skirt">
                            <img src="_img/gallery/3.jpg" alt="1101PWL French Navy Jacket with 6216UP Print Blouse and 3112PWL French Navy Skirt" />
                        </a>
                      </li>
                      <li>
                        <a href="_img/gallery/4-large.jpg" title="6221UP Print Blouse with 3108PWL French Navy Skirt">
                            <img src="_img/gallery/4.jpg" alt="6221UP Print Blouse with 3108PWL French Navy Skirt" />
                        </a>
                      </li>
                      <li>
                        <a href="_img/gallery/5-large.jpg" title="6308WFS Ruby Blouse with 2108PWL French Navy Pant">
                            <img src="_img/gallery/5.jpg" alt="6308WFS Ruby Blouse with 2108PWL French Navy Pant" />
                        </a>
                      </li>
                      <li>
                        <a href="_img/gallery/6-large.jpg" title="5WT White Slim Shirt with B106 Navy Trouser and DTY Belt">
                            <img src="_img/gallery/6.jpg" alt="5WT White Slim Shirt with B106 Navy Trouser and DTY Belt" />
                        </a>
                      </li>
                      <li>
                        <a href="_img/gallery/7-large.jpg" title="220 Ink Shirt with B106 Navy Jacket & B106 Navy Trouser">
                            <img src="_img/gallery/7.jpg" alt="220 Ink Shirt with B106 Navy Jacket & B106 Navy Trouser" />
                        </a>
                      </li>
                      <li>
                        <a href="_img/gallery/9-large.jpg" title="Emily 6302XLA White Shirt with 1101PWL Charcoal Jacket and 3108PWL Charcoal Skirt. Michael 5WT White Shirt with B106 Charcoal Jacket, B106 Charcoal Trouser and DTY Belt.">
                            <img src="_img/gallery/9.jpg" alt="Emily 6302XLA White Shirt with 1101PWL Charcoal Jacket and 3108PWL Charcoal Skirt. Michael 5WT White Shirt with B106 Charcoal Jacket, B106 Charcoal Trouser and DTY Belt." />
                        </a>
                      </li>
                      <li>
                        <a href="_img/gallery/10-large.jpg" title="4170XLA Sky Slim Shirt with B106 Navy Jacket & B106 Navy Trouser.">
                            <img src="_img/gallery/10.jpg" alt="4170XLA Sky Slim Shirt with B106 Navy Jacket & B106 Navy Trouser." />
                        </a>
                      </li>
                      <li>
                        <a href="_img/gallery/11-large.jpg" title="7101S Mens Pharmacy Jacket. 6312P Womens 3/4 Slv Navy Longline Pharmacy Jacket.">
                            <img src="_img/gallery/11.jpg" alt="7101S Mens Pharmacy Jacket. 6312P Womens 3/4 Slv Navy Longline Pharmacy Jacket." />
                        </a>
                      </li>
                      <li>
                        <a href="_img/gallery/12-large.jpg" title="6212P Womens S/S White Longline Pharmacy Jacket with 3108PWL French Navy Skirt.">
                            <img src="_img/gallery/12.jpg" alt="6212P Womens S/S White Longline Pharmacy Jacket with 3108PWL French Navy Skirt." />
                        </a>
                      </li>
                    </ul>
                  </div>
                  <div id="maindiv">
                  <h1>Corporate Wear with a Difference</h1>
                  <p>
                     Designs To You Pty Ltd are suppliers of high quality, affordable and fashion forward...'Corporate Wear With A Difference'.
                  </p>
                  <p>
                     DTY was established in 1999, after a gap in the existing corporate wear market was noted. DTY provides a solution to that gap in the form of a complete range of fashionable corporate apparel tailored to the needs of individual clients. Our products are as unique and diverse as the corporate clients we service.
                  </p>
                  <div class="blankSeparator"><!-- --></div>

                  <p><a href="aboutus.php" class="linkButton">read more about Designs To You&nbsp;&raquo;</a></p>
                  </div>
                </dd>

                <dt>Corporate Uniforms</dt>
                <dd>
                  <a href="#"><img src="_img/3.jpg" alt="Corporate Uniforms" /></a>

                  <h1>Corporate Range</h1>
                  <p>


                  </p>

                  <p>
                     <b>DTY essentials:</b>
                     <ul>
                     <li>Grade A customer service</li>
                     <li>Superior quality and finish</li>
                     <li>Contemporary styling</li>
                     <li>Stretch lining for added flexibility</li>
                     <li>Comfort stretch shirting</li>
                     <li>Concealed internal button (womens shirts)</li>
                     </ul>
                  </p>
                  <div class="blankSeparator"><!-- --></div>

                  <!--<p><a href="#" class="linkButton">read more about our corporate uniform&nbsp;&raquo;</a></p>-->
                </dd>

                <dt>Contact Us</dt>
                <dd>
                  <a href="#"><img src="_img/2.jpg" alt="Contact Us" /></a>

                  <h1>Customer Service</h1>
                  <p>For any enquiries, please contact us.</p>

                  <p><b>Designs To You Pty. Ltd.</b><br/>
                  31 Enterprise Drive, Rowville VIC 3178<br/>
                  Phone: (03) 9753 2555<br/>
                  Fax: (03) 9753 2559<br/>
                  Email: <a href="mailto:sales@designstoyou.com.au">sales@designstoyou.com.au</a>
                  </p>
                  <div class="blankSeparator"><!-- --></div>

                  <!--<p><a href="#" class="linkButton">read more about our corporate uniform&nbsp;&raquo;</a></p>-->
                </dd>

                <dt>Other news</dt>
                <dd>
                  <h1>Website &amp; New Catalogue Launched!</h1>

                  <img src="_img/website.jpg" alt="News" />

                  <p>
                  DTY is very pleased to announce the launch of Edition 3 of our corporate catalogue and fresh new look website.
                  </p>

                  <div class="blankSeparator"><!-- --></div>

                </dd>
            </dl>