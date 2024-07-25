<!DOCTYPE html>
<html class="no-js" lang="en">
	<!-- by Muhammad Sofi 25 January 2022 22:10 | change font to Poppins -->
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href='https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800&display=swap' rel='stylesheet'>
	<?php $this->getThemeElement("page/html/head",$__forward); ?>
	<body style="text-transform: none!important; font-family: 'Poppins'; color:#000000;">
		<div id="page-wrapper" class="page-loading">
			<!-- <div class="preloader themed-background">
				<h1 class="push-top-bottom text-light text-center" ><strong><?=$this->site_name?></strong><br><small>Loading...</small>
				</h1>
				<div class="inner">
					<h3 class="text-light visible-lt-ie10"><strong>Loading..</strong></h3>
					<div class="preloader-spinner hidden-lt-ie10"></div>
				</div>
			</div> -->
			<div id="page-container">
				<div id="main-container">
					<?php $this->getThemeContent(); ?>
				</div>
			</div>
		</div>
		<?php $this->getJsFooter(); ?>
	</body>
</html>
