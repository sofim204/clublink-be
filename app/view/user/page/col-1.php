<!DOCTYPE html>
<html class="no-js" lang="en">
	<head>
		<meta charset="utf-8" />
		<meta name="author" content="<?=$this->site_name?>" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title><?=$this->getTitle();?></title>
		<link rel="stylesheet" href="<?=base_url()?>skin/user/css/animate.min.css" />
		<link rel="stylesheet" href="<?=base_url()?>skin/user/css/normalize.css" />
		<link rel="stylesheet" href="<?=base_url()?>skin/user/css/foundation.min.css" />
		<link rel="stylesheet" href="<?=base_url()?>skin/user/css/font-awesome.min.css" />
		<link rel="stylesheet" href="<?=base_url()?>skin/user/css/jquery.growl.css" />
		<link rel="stylesheet" href="<?=base_url()?>skin/user/css/app.css" />
	</head>
	<body>
		<div class="container">
			<?php $this->getThemeContent(); ?>
		</div>
		<script src="<?=base_url()?>skin/user/js/vendor/jquery.min.js"></script>
		<script src="<?=base_url()?>skin/user/js/vendor/jquery.growl.js"></script>
		<script src="<?=base_url()?>skin/user/js/foundation.min.js"></script>
		<script>
			$(document).ready(function(e){
				  $(document).foundation();
				<?php $this->getJsReady(); ?>
			});
			<?php $this->getJsContent(); ?>
		</script>
	</body>
</html>
