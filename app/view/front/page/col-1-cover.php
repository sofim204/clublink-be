<!DOCTYPE html>
<html lang="en">
	<?php $this->getThemeElement("page/html/head",$__forward); ?>
	<?php $this->getBodyBefore(); ?>
	<body class="shopping-cart-page">
		<!-- offcanvas cart -->
		<?php $this->getThemeElement('page/html/top_cart_content',$__forward); ?>
		<!-- end offcanvas cart-->

		<!-- mobile menu -->
		<?php $this->getThemeElement("page/html/menu_mobile",$__forward); ?>
		<!-- mobile menu -->

		<!-- page -->
		<div id="page">
			<!--[if lt IE 8]>
				<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
			<![endif]-->

			<!-- top info -->
			<?php $this->getThemeElement("page/html/top_info",$__forward); ?>
			<!-- end top info -->

			<!-- Header -->
			<?php $this->getThemeElement("page/html/menu",$__forward); ?>
			<!-- end header -->

      <?php if(!isset($page_image)) $page_image = ''; if(strlen($page_image)>4){ ?>
      <div class="produk_hero" style="background-image: url('<?=base_url($page_image)?>');">
        <h1 ><?=$page_name?></h1>
      </div>
      <?php } ?>
      
			<!-- Breadcrumbs -->
			<?php //$this->getThemeElement('page/html/breadcrumbs',$__forward); ?>
			<!-- Breadcrumbs End -->

			<!-- Main Container -->
			<?php $this->getThemeContent(); ?>
			<!-- Main Container End -->

			<!-- satelite button -->
			<?php //$this->getThemeElement('page/html/satelite_button',$__forward); ?>

			<!-- Footer -->
			<?php $this->getThemeElement('page/html/foot',$__forward); ?>
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
