<!DOCTYPE html>
<html class="no-js" lang="en">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet"> 
	<style>
		*{
			font-family: 'Poppins', sans-serif;
		}
	</style>
	<?php $this->getThemeElement("page/html/head",$__forward); ?>
	<body>
		<!-- Page Wrapper -->
		<div id="page-wrapper" class="page-loading">
			<!-- Preloader -->
			<div class="preloader themed-background">
				<h1 class="push-top-bottom text-light text-center" ><strong>CALYSTA BILLING</strong><br><small>Loading...</small></h1>
				<div class="inner">
					<h3 class="text-light visible-lt-ie10"><strong>Loading..</strong></h3>
					<div class="preloader-spinner hidden-lt-ie10"></div>
				</div>
			</div>
			<!-- END Preloader -->

			<div id="page-container" class="sidebar-partial sidebar-visible-lg sidebar-no-animations">
				<!-- Alternative Sidebar -->
				<?php $this->getThemeElement("page/html/sidebar_alt",$__forward); ?>
				<!-- END Alternative Sidebar -->

				<!-- Main Sidebar -->
				<?php $this->getThemeElement("page/html/sidebar",$__forward); ?>
				<!-- END Main Sidebar -->

				<!-- Main Container -->
				<div id="main-container">
					<!-- Header -->
					<?php $this->getThemeElement("page/html/header",$__forward); ?>
					<!-- END Header -->

					<!-- Main Container -->

					<!-- Global Message -->
					<?php $this->getThemeElement("page/html/global_message",$__forward); ?>
					<!-- Global Message -->

					<?php $this->getThemeContent(); ?>
					<!-- Main Container End -->

					<!-- Footer -->
					<?php $this->getThemeElement("page/html/footer",$__forward); ?>
					<!-- End Footer -->
				</div>
				<!-- End Main Container -->

			</div>
			<!-- End Page Container -->

		</div>
		<!-- End Page Wrapper -->

		<!-- Foot -->
		<?php $this->getThemeElement("page/html/foot",$__forward); ?>
		<!-- End Foot -->

		<div id="modal-preloader" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog slideInDown animated">
				<div class="modal-content" style="background-color: #000;color: #fff;">
					<!-- Modal Header -->
					<div class="modal-header text-center" style="border: none;">
						<h2 class="modal-title"><i class="fa fa-spin fa-refresh"></i> Loading...</h2>
					</div>
					<!-- END Modal Header -->
				</div>
			</div>
		</div>

		<!-- jQuery, Bootstrap.js, jQuery plugins and Custom JS code -->
		<?php $this->getJsFooter(); ?>

		<!-- Load and execute javascript code used only in this page -->
		<script>
			var from_user_id = '';
			var from_user_nama = '';
			var to_user_id = '';
			var to_user_nama = '';
			var chat_active = 1;
			var last_pesan_id = 0;
			var iterator = 1;
			$(document).ready(function(e){
				<?php $this->getJsReady(); ?>
			});
			<?php $this->getJsContent(); ?>
		</script>
		<script src="<?php echo $this->skins->front; ?>js/pages/index2.js"></script>
		<script>$(function(){ Index2.init(); });</script>

	</body>
</html>
