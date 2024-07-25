<!DOCTYPE html>
<html lang="en">
	<?php $this->getThemeElement("page/html/head",$__forward); ?>
	<?php $this->getBodyBefore(); ?>
	<body class="shopping-cart-page">
		<!-- mobile menu -->
		<?php $this->getThemeElement("page/html/menu_mobile",$__forward); ?>
		<!-- mobile menu -->

		<!-- page -->
    <style>
      .checkout-banner {
        padding: 1.5em 0;
        padding-top: 6em;
        padding-bottom: 2em;
        height: 30vh;
        background-image: url('<?=base_url('skin/front/images/checkout.jpg')?>');
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center center;
      }
      .checkout-banner-wrap {
        padding: 0 5%;
        width: 90%;
        display: block;
        margin: 0 auto;
      }
      .checkout-banner-link {
        display: block;
        vertical-align: middle;
        text-align: right;
      }
      .checkout-banner-logo {
        display: inline-block;
        max-height: 4.28571em;
      }
      footer {
        margin-top: 0;
      }
      .radio > input[type=radio]:checked + label:before {
        background-color: #6d6d6d;
      }
    </style>
    <div class="checkout-banner">
      <div class="checkout-banner-wrap">
        <a href="<?=base_url('')?>" class="checkout-banner-link">
          <img src="<?=base_url('skin/front/images/logo.png')?>" class="checkout-banner-logo" />
        </a>
      </div>
    </div>
		<div id="page">
			<!--[if lt IE 8]>
				<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
			<![endif]-->
			<!-- Breadcrumbs -->
			<?php //$this->getThemeElement('page/html/breadcrumbs',$__forward); ?>
			<!-- Breadcrumbs End -->

			<!-- Main Container -->
			<?php $this->getThemeContent(); ?>
			<!-- Main Container End -->

			<!-- satelite button -->
			<?php //$this->getThemeElement('page/html/satelite_button',$__forward); ?>

			<!-- Footer -->
			<?php //$this->getThemeElement('page/html/foot',$__forward); ?>
			<!-- End Footer -->

		</div>
		<!-- end page -->

		<?php $this->getThemeElement("page/html/whatsapp_button"); ?>
		<?php $this->getJsFooter(); ?>

		<script>
			$(document).ready(function(e){
				$('#buttonsearch').click(function(){
					$('#formsearch').slideToggle( "fast",function(){
						 $( '#content' ).toggleClass( "moremargin" );
					} );
					$('#searchbox').focus()
					$('.openclosesearch').toggle();
				});
				$('.close-top-info').on('click',function(){
					$('#top-info').slideUp('slow');
				});
				setTimeout(function(){
					$('#top-info').slideDown('slow');
				},4000);
				<?php $this->getThemeElement('page/html/foot_visitor_js',$__forward); ?>
				<?php $this->getJsReady(); ?>
				<?php $this->getJsContent(); ?>
			});

			//baguetteBox.run('.tz-gallery');

		</script>
	</body>
</html>
