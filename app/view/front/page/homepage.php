<?php
if(!isset($config_db)) $config_db = new stdClass();
if(!isset($config_db->homepage_youtube_id)) $config_db->homepage_youtube_id = 'HW2WAqcJFiU';

function base_cdn($url){
	return 'https://customgrosir.b-cdn.net/'.$url;
}
?>
<!DOCTYPE html>
<html lang="en" prefix="og: http://ogp.me/ns#" xmlns:customgrosir="<?=base_url(); ?>">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<meta name="description" content="<?php echo $this->getDescription(); ?>" />
	<meta name="author" content="<?php echo $this->getDescription(); ?>" />
	<meta name="keyword" content="<?php echo $this->getKeyword(); ?>" />
	<meta name="robots" content="INDEX,FOLLOW" />

	<title><?php echo $this->getTitle(); ?></title>
	<meta name="language" content="id" />
	<link rel="icon" href="<?php echo $this->getIcon(); ?>" type="image/x-icon" />
	<link rel="shortcut icon" href="<?php echo $this->getShortcutIcon(); ?>" type="image/x-icon" />

	<!-- prefetch -->
	<meta http-equiv="x-dns-prefetch-control" content="on">
	<link rel="dns-prefetch" href="//www.googletagmanager.com" />
	<link rel="dns-prefetch" href="//connect.facebook.net" />
	<link rel="dns-prefetch" href="//www.facebook.com" />
	<link rel="dns-prefetch" href="//api2.encyclo.co.id" />
	<link rel="dns-prefetch" href="//fonts.googleapis.com" />

	<!-- geo -->
	<meta name="geo.region" content="ID-JB" />
	<meta name="geo.placename" content="Kab. Bandung" />
	<meta name="geo.position" content="-7.012524;107.525479" />
	<meta name="ICBM" content="-7.012524, 107.525479" />

	<!-- fontawesome css -->
	<link href="<?=base_url('skin/frontcg/'); ?>plugins/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
	<link href="<?=base_url('skin/frontcg/'); ?>plugins/font-awesome-4.7.0/css/font-awesome-animated.css" rel="stylesheet" type="text/css">
	<!-- Bootstrap core CSS -->
	<link href="<?=base_url('skin/front/'); ?>css/bootstrap.min.css" rel="stylesheet">

	<!-- Custom styles for this template -->
	<link href="<?=base_url('skin/frontcg/'); ?>styles/main_styles.css" rel="stylesheet">
	<link href="<?=base_url('skin/frontcg/'); ?>styles/responsive.css" rel="stylesheet">
	<link href="<?=base_url('skin/frontcg/'); ?>styles/normalize.min.css" rel="stylesheet">

	<link href="<?=base_url('skin/front/css/jquery.gritter'); ?>.css" rel="stylesheet">
	<link href="<?=base_url('skin/front/css/'); ?>owl.carousel.css" rel="stylesheet">
	<!-- style css -->
	<link href="<?=base_url('skin/front/'); ?>style.css" rel="stylesheet">
	<link href="<?=base_url('manifest'); ?>.json" rel="manifest">

	<!-- other meta -->
	<meta property="fb:app_id" content="130766654278919" />
	<meta property="og:url" content="<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>" />
	<meta property="og:type" content="website" />
	<meta property="og:title" content="<?php echo $this->getTitle(); ?>" />
	<meta property="og:image" content="<?=base_url('assets/img/fb-share.png'); ?>" />
	<meta property="og:description" content="<?php echo $this->getDescription(); ?>" />
	<meta property="og:image:alt" content="Shafira.com Logo or products" />

	<meta name="msapplication-TileColor" content="#000000" />
	<meta name="msapplication-TileImage" content="/favicon.png" />
	<meta name="theme-color" content="#000000" />

	<!--Google TAG manager goes here-->

	<!--Bing Verification goes here-->


	<!--Google Analytics goes here-->
	<style>.callout-image:nth-child(1) {
		-webkit-transform: translate3d(0,0,0) scale(1,1);
		transform: translate3d(0,0,0) scale(1,1);
		box-shadow: 0 20px 30px rgba(0,0,0,0.3);
	}

	.callout-image:nth-child(2) {
		-webkit-transform: translate3d(-193px,-107px,0);
		transform: translate3d(-193px,-107px,0);
	}
	.callout-image:nth-child(3) {
		-webkit-transform: translate3d(187px,111px,0);
		transform: translate3d(187px,111px,0);
	}
	.callout-image:nth-child(4) {
		-webkit-transform: translate3d(-157px,145px,0);
		transform: translate3d(-157px,145px,0);
	}
	.callout-image:nth-child(5) {
		-webkit-transform: translate3d(178px,-82px,0);
		transform: translate3d(178px,-82px,0);
	}
	</style>
</head>

<body class="shopping-cart-page">
	<!-- offcanvas cart -->
	<?php $this->getThemeElement('page/html/top_cart_content',$__forward); ?>
	<!-- end offcanvas cart-->

	<?php $this->getThemeElement("page/html/menu_mobile",$__forward); ?>
	<div id="page">
		<!-- top info -->
		<?php $this->getThemeElement("page/html/top_info",$__forward); ?>
		<!-- end top info -->

		<?php $this->getThemeElement("page/html/menu",$__forward); ?>
		<!-- Slider -->



		<?php if(isset($slider_enable)) if(!empty($slider_enable)) $this->getThemeElement('page/html/sliders',$__forward); ?>

		<!-- flashsale SLIDER -->
		<style>
		/*preloader*/
		@keyframes pulse {
			0% {
				background-color: rgba(165,165,165,0.1);
			}
			50% {
				background-color: rgba(165,165,165,0.3);
			}
			100% {
				background-color: rgba(165,165,165,0.1);
			}
		}
		@-webkit-keyframes pulse {
			0% {
				background-color: rgba(165,165,165,0.1);
			}
			50% {
				background-color: rgba(165,165,165,0.3);
			}
			100% {
				background-color: rgba(165,165,165,0.1);
			}
		}


		/*flash preloader*/
		.preloader-box-img {
			min-width: 272px;
			min-height: 290px;
			animation: pulse 1s infinite ease-in-out;
			animation: pulse 1s infinite ease-in-out;
		}
		/*.preloader-box-title {
		margin-top: 0px;
		min-width: 117px;
		min-height: 16px;
		animation: pulse 1s infinite ease-in-out;
		animation: pulse 1s infinite ease-in-out;
		}*/
		.preloader-box-price {
			margin-top: 10px;
			min-width: 272px;
			min-height: 33px;
			animation: pulse 1s infinite ease-in-out;
			animation: pulse 1s infinite ease-in-out;
		}
		#countdown-container {
			margin-bottom: 1em;
		}
		.timer {
			margin-top: 0;
			top: 0;
			padding: 0 0;
			padding-top: 0;
		  color: #000000;
		}
		.timer-content * {
			display: inline-block;
			font-size: 1.2em;
			font-family: "Lato","Times New Roman","Helvetica";
		}

		.text-judul {
			font-weight: bolder;
			font-size: 1.75em;
		}
		.text-timer {
			background-color: black;
			color: gold;
			border-radius: 5px;
			min-width: 2em;
			font-size: 0.9em;
			padding: 0.1em;
			text-align: center;
		}
		.text-timer.text-red {
			background-color: red;
			color: white;
		}
		.text-small {
			font-size: smaller;
			font-weight: 400;
		}
		.flashsale-item {
			cursor: pointer;
		}
		.flashsale-last-list {
			text-align: center;
		}
		.fa-slider-last {
			font-size: 22em;
			color: #ececec;
		}
		.sale-label.harga {
			background-color: red;
			text-transform: none;
		}
		.sale-label.persen {
			background-color: green;
			text-transform: none;
		}
		.flashsale-image-wrapper {
			height:290px;
			background-size:cover;
			background-position:center center;
		}
		.flashsale-price-wrapper {
	    text-align: center;
	    width: 254px;
			margin-left: 0.5em;
			margin-right: 0.5em;
		}
		.flashsale-price, .flashsale-price-asal {
	    color: #fe4c50;
			font-size: 1.4em;
			margin:0;
			line-height: 1;
		}
		.flashsale-price-asal {
			margin: 0.25em 0;
			font-size: 1em;
			font-weight: 100;
			color: #909090;
			text-decoration: line-through;
		}
		.item.flashsale-item a {
			background-color: transparent;
			border: none;
		}

		@media only screen and (max-width: 425px){
			.text-judul {
				display: block;
				font-size: 1.2em;
			}
			.hari_info * {

			}
			.text-timer {
				font-size: 0.7em;
			}
			#timer_jam {
				margin-left: 0;
			}
			.preloader-box-img {
				min-height: 180px;
			}
			.flashsale-price-wrapper {
				width: 359px;
			}
		}

		@media only screen and (max-width: 320px){
			.hari_info * {
				display: inline-block;
			}
			.timer {
				margin-top: 0;
			}
			.fa-slider-last {
				font-size: 20em;
			}
			.flashsale-image-wrapper {
				height: 254px;
				background-size: cover;
				background-position: center center;
			}
			.flashsale-price-wrapper {
				width: 254px;
			}
		}
		/*end preloader*/
		</style>
		<!-- Product tittle -->
		<section id="flashsale" itemscope itemtype="http://schema.org/SaleEvent">
			<meta itemprop="startDate" content="<?=$flashsale_data->sdate?>" />
			<meta itemprop="endDate" content="<?=$flashsale_data->edate?>" />
			<meta itemprop="eventStatus" content="http://schema.org/EventScheduled" />
			<div class="container">
				<!-- Countdown Timer -->
				<div id="countdown-container" class="container">
					<div class="row">
						<div class="col-xs-8">
							<div class="timer">
								<div class="timer-content">
									<div>
										<div class="text-judul" itemprop="name">Flash Sale</div>
										<div class="hari_info">
											<div id="timer_hari" class="text-timer text-red" style="display:none;">00</div>
											<div id="text_hari" class="text-small" style="display:none;">Hari </div>
										</div>
										<div id="timer_jam" class="text-timer" style="display:none;">00</div>
										<div id="text_jam" class="text-small" style="display:none;">:</div>
										<div id="timer_menit" class="text-timer" style="display:none;">00</div>
										<div id="text_menit" class="text-small" style="display:none;">:</div>
										<div id="timer_detik" class="text-timer" style="display:none;">00</div>
										<div id="text_detik" class="text-small" style="display:none;"></div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-xs-4 hidden-xs">
							<div class="customNavigation btn-group pull-right">
								<a class="btn btn-default prev btn-sm">
									<i class="fa fa-angle-left fa-2x" aria-hidden="true"></i>
								</a>
								<a class="btn btn-default next btn-sm">
									<i class="fa fa-angle-right fa-2x" aria-hidden="true"></i>
								</a>
							</div>
						</div>
					</div>
				</div>
				<!-- End Countdown Timer -->
			</div>

			<div class="container fs-style">
				<div class="row">
					<div class="col-md-12">
						<div id="owl-flashsale" class="owl-carousel owl-theme">

							<!-- Wrapper for slides -->
							<div class="item">
								<div class="preloader-box-img"></div>
								<div class="preloader-box-price"></div>
							</div>
							<div class="item">
								<div class="preloader-box-img"></div>
								<div class="preloader-box-price"></div>
							</div>
							<div class="item">
								<div class="preloader-box-img"></div>
								<div class="preloader-box-price"></div>
							</div>
							<div class="item">
								<div class="preloader-box-img"></div>
								<div class="preloader-box-price"></div>
							</div>

						</div>
					</div>
				</div>
			</section>

			<!-- END BRAND SLIDER -->


			<!-- BLOCK1 -->
			<?php if(!empty($block1_enable)) $this->getThemeElement('home/block1',$__forward); ?>
			<!-- end BLOCK1 -->

			<!-- BLOCK2 -->
			<?php if(!empty($block2_enable)) $this->getThemeElement('home/block2',$__forward); ?>
			<!-- end BLOCK2 -->

			<hr class="section--divider" />

			<!-- BLOCK3 -->
			<?php if(!empty($block3_enable)) $this->getThemeElement('home/block3',$__forward); ?>
			<!-- end BLOCK3 -->

			<!-- BLOCK4 -->
			<?php if(!empty($block4_enable)) $this->getThemeElement('home/block4',$__forward); ?>
			<!-- end BLOCK4 -->

			<!-- BLOCK5 -->
			<?php if(!empty($block5_enable)) $this->getThemeElement('home/block5',$__forward); ?>
			<!-- end BLOCK5 -->

			<!-- BLOCK6 -->
			<?php if(!empty($block6_enable)) $this->getThemeElement('home/block6',$__forward); ?>
			<!-- end BLOCK6 -->

			<?php if(isset($ig_list)) $this->getThemeElement('page/html/instagram_10x2',$__forward); ?>
			<?php $this->getThemeElement('page/html/foot',$__forward); ?>
		</div>

			<?php $this->getThemeElement("page/html/whatsapp_button"); ?>

			<!-- Bootstrap core JavaScript -->
			<script src="<?=base_url('skin/frontcg/'); ?>js/jquery-3.2.1.min.js"></script>
			<script src="<?=base_url('skin/front/'); ?>js/scrollreveal.min.js"></script>
			<!--<script async src="<?=base_url('skin/frontcg/'); ?>styles/bootstrap4/popper.js"></script>-->
			<script async src="<?=base_url('skin/front/'); ?>js/bootstrap.min.js"></script>
			<!--<script async src="<?=base_url('skin/frontcg/'); ?>plugins/Isotope/isotope.pkgd.min.js"></script>-->
			<!--<script async src="<?=base_url('skin/frontcg/'); ?>plugins/easing/easing.js"></script>-->
			<!--<script async src="<?=base_url('skin/frontcg/'); ?>js/jquery.touchSwipe.min.js"></script>-->
			<!--<script async src="<?=base_url('skin/frontcg/'); ?>js/custom.js"></script>-->
			<script async src="<?=base_url('skin/front/'); ?>js/owl.carousel.min.js"></script>
			<script async src="<?=base_url('assets/js/'); ?>jquery-ui.min.js"></script>
			<script async src="<?=base_url('skin/front/'); ?>js/jtv-mobile-menu.js"></script>
			<script async src="<?=base_url('skin/front/'); ?>js/countdown.js"></script>
			<script async src="//www.youtube.com/iframe_api"></script>
			<script async src="<?=base_url('skin/front/'); ?>js/theme.js"></script>
			<script async src="<?=base_url('skin/front/'); ?>js/main.js"></script>

			<?php //$this->getJsFooter(); ?>

			<script>
			function number_format(x){
				var parts = x.toString().split(".");
			  parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
			  return parts.join(".");
			}
			$('#buttonsearch').click(function(){
				$('#formsearch').slideToggle( "fast",function(){
					$( '#content' ).toggleClass( "moremargin" );
				} );
				$('#searchbox').focus()
				$('.openclosesearch').toggle();
			});
			window.sr = ScrollReveal();
			sr.reveal('#callout-images-1', {
				duration: 2000,
				origin:'top',
				distance:'200px',
				viewFactor: 0.2
			});
			sr.reveal('#callout-images-2', {
				duration: 2000,
				origin:'top',
				distance:'200px',
				viewFactor: 0.3
			});
			$(document).ready(function(e){
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

			// flashsale.js
			$.get('<?php echo base_url("api_web/flashsale/");?>').done(function(dt){
				if(dt.status == '100' || dt.status == 100){
					$("#owl-flashsale").empty();
					var h = '<div class="customNavigation">\
					<div class="small-navigation-left">\
					<a class="btn prev custom-icon">\
					<i class="fa fa-angle-left fa-2x" aria-hidden="true"></i>\
					</a>\
					</div>\
					<div class="small-navigation-right">\
					<a class="btn next custom-icon">\
					<i class="fa fa-angle-right fa-2x" aria-hidden="true"></i>\
					</a>\
					</div>\
					</div>';

					$.each(dt.result.produk,function(index,value){
						var h='';
						h=h+'<div class="item flashsale-item">';
						h=h+'<a href="<?=base_url("produk/") ?>'+value.slug+'" class="thumbnail" title="'+value.nama+'">';
						h=h+'<div class="flashsale-image-wrapper" style="background-image:url(\''+value.thumb+'\');">';
						if(value.promo_jenis == 'harga'){
							h=h+'<label class="sale-label sale-left harga">Disc. <br />Rp'+number_format(value.promo_nilai)+'</label>';
						}else{
							h=h+'<label class="sale-label sale-left persen">-'+number_format(value.promo_nilai)+'%<br />OFF</label>';
						}
						h=h+'</div>';
						h=h+'<div class="flashsale-price-wrapper">';
						h=h+'<p class="flashsale-price-asal">'+value.harga_jual+'</p>';
						h=h+'<p class="flashsale-price">'+value.harga_jadi+'</p>';
						h=h+'</div>';
						h=h+'</a>';
						h=h+'</div>';
						$("#owl-flashsale").append(h);
					});
					var h='';
					h=h+'<div class="item flashsale-item flashsale-last-list">';
					h=h+'<a href="<?=base_url("flashsale") ?>" class="thumbnail" title="Lihat flashsale selengkapnya">';
					h=h+'<div class="" style="height:290px;background-image:url(\'\');background-size:cover;">';
					h=h+'<i class="fa fa-chevron-circle-right fa-slider-last"></i>';
					h=h+'<p> Lihat Selengkapnya </p>';
					h=h+'</div>';
					h=h+'</a>';
					h=h+'</div>';
					$("#owl-flashsale").append(h);

					var x = setInterval(function(){
						var akhirflashsale = new Date(dt.result.edate).getTime();
						var now = new Date().getTime();
						var distance = akhirflashsale - now;
						var days = Math.floor(distance / (1000 * 60 * 60 * 24));
						var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
						var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
						var seconds = Math.floor((distance % (1000 * 60)) / 1000);
						$("#timer_hari").html(days);
						$("#timer_jam").html(hours);
						$("#timer_menit").html(minutes);
						$("#timer_detik").html(seconds);
					},1000);

					$("#timer_hari").fadeIn('slow');
					$("#timer_jam").fadeIn('slow');
					$("#timer_menit").fadeIn('slow');
					$("#timer_detik").fadeIn('slow');
					$("#text_hari").fadeIn('slow');

					setTimeout(function(){
						var owl = $("#owl-flashsale");
						owl.owlCarousel({
							items : 4, //10 items above 1000px browser width
							itemsDesktop : [1000,5], //5 items between 1000px and 901px
							itemsDesktopSmall : [900,2], // betweem 900px and 601px
							itemsTablet: [600,1], //2 items between 600 and 0
							itemsMobile : false, // itemsMobile disabled - inherit from itemsTablet option
							rewindNav : false,
							autoHeight: false,
						});
						// Custom Navigation Events
						$(".next").click(function(){
							owl.trigger('owl.next');
						});
						$(".prev").click(function(){
							owl.trigger('owl.prev');
						});
					},1000);
				}else{
					$("#flashsale").empty();
				}
			}).fail(function(){
				$("#flashsale").empty();
			});
			</script>
		</body>
		</html>
