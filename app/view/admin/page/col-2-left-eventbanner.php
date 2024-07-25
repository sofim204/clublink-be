<!DOCTYPE html>
<html class="no-js" lang="en">
	<!-- by Muhammad Sofi 25 January 2022 22:10 | change font to Poppins -->
	<head>
		<link rel="preconnect" href="https://fonts.gstatic.com">
		<link href='https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800&display=swap' rel='stylesheet'>
		<?php $this->getThemeElement("page/html/head",$__forward); ?>
		<link rel="stylesheet" media="(prefers-color-scheme: dark)" href="<?=base_url()?>skin/admin/css/dark.css">
	</head>
	<style>
		body {
			text-transform: none!important; 
			font-family: 'Poppins'; 
			color:#000000;
			background-color: #F2EEEB !important;
			background-color: rgb(248, 243, 242) !important;
			background-color: rgba(248, 243, 242, 0.9) !important;
			/* background-color: transparent !important; */
		}
	</style>
	<body>
		<!-- <div id="page-wrapper" class="page-loading"> -->
			<div id="main-container">
				<?php $this->getThemeContent(); ?>
			</div>
		<!-- </div> -->
		<?php $this->getJsFooter(); ?>
	</body>
</html>