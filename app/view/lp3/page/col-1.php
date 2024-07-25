<!DOCTYPE html>
<html lang="en">
	<?php $this->getThemeElement("page/html/head",$__forward); ?>
	<?php $this->getBodyBefore(); ?>
	<body class="landing-page-3">
		<?php $this->getThemeElement("page/html/navbar",$__forward); ?>
		<div class="container">
			<!-- Main Container -->
			<?php $this->getThemeContent(); ?>
			<!-- Main Container End -->
		</div>

		<!-- Footer -->
		<?php $this->getThemeElement('page/html/foot',$__forward); ?>
		<!-- End Footer -->

		<?php $this->getJsFooter(); ?>

		<script>
			$(document).ready(function(e){
				<?php $this->getJsReady(); ?>
				<?php $this->getJsContent(); ?>
			});
		</script>
	</body>
</html>
