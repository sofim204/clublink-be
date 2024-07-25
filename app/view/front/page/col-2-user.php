<!DOCTYPE html>
<html lang="en">
	<?php $this->getThemeElement("page/html/head",$__forward); ?>
	<?php $this->getBodyBefore(); ?>
	<body class="category-page">
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

			<!-- Breadcrumbs -->
			<?php //$this->getThemeElement('page/html/breadcrumbs',$__forward); ?>
			<!-- Breadcrumbs End -->

			<section class="main-container">
				<div class="container">
					<div class="row">
            <!-- main -->
            <div class="col-sm-8 col-sm-push-4 col-md-9 col-md-push-3">
              <?php $this->getThemeContent(); ?>
            </div>
            <!-- end of smain -->

            <!-- sidebar -->
            <div class="sidebar col-sm-4 col-md-3 col-xs-12 col-md-pull-9 col-sm-pull-8">
              <aside class="sidebar">
                <?php $this->getThemeLeftContent(); ?>
              </aside>
            </div>
            <!-- end of sidebar -->
					</div>
				</div>
			</section>
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
		</script>
	</body>
</html>
