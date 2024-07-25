	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

		<title><?=$this->getTitle()?></title>

		<meta name="description" content="<?=$this->getDescription()?>">
		<meta name="keyword" content="<?=$this->getKeyword()?>"/>
		<meta name="author" content="<?=$this->getAuthor()?>">
		<meta name="robots" content="<?=$this->getRobots()?>" />

		<!-- Icons -->
		<!-- The following icons can be replaced with your own, they are used by desktop and mobile browsers -->
		<link rel="shortcut icon" href="<?=$this->skins->admin?>img/favicon.png">
		<link rel="apple-touch-icon" href="<?=$this->skins->admin?>img/icon57.png" sizes="57x57">
		<link rel="apple-touch-icon" href="<?=$this->skins->admin?>img/icon72.png" sizes="72x72">
		<link rel="apple-touch-icon" href="<?=$this->skins->admin?>img/icon76.png" sizes="76x76">
		<link rel="apple-touch-icon" href="<?=$this->skins->admin?>img/icon114.png" sizes="114x114">
		<link rel="apple-touch-icon" href="<?=$this->skins->admin?>img/icon120.png" sizes="120x120">
		<link rel="apple-touch-icon" href="<?=$this->skins->admin?>img/icon144.png" sizes="144x144">
		<link rel="apple-touch-icon" href="<?=$this->skins->admin?>img/icon152.png" sizes="152x152">
		<link rel="apple-touch-icon" href="<?=$this->skins->admin?>img/icon180.png" sizes="180x180">
		<!-- END Icons -->

		<!-- Stylesheets -->
		<!-- END Stylesheets -->

		<?php $this->getAdditionalBefore()?>
		<?php $this->getAdditional()?>
		<?php $this->getAdditionalAfter()?>

		<!-- Modernizr (browser feature detection library) -->
		<script src="<?=$this->skins->admin?>js/vendor/modernizr.min.js"></script>

	</head>
