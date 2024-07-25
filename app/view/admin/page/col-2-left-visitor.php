<!DOCTYPE html>
<html class="no-js" lang="en">
	<head>
		<!-- <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> -->
		<meta charset="UTF-8">
		<style>
			.modal-header-title {
				background-color: #383838; 
				color:#F2F2F2; 
				margin-top:-5px;
			}
			.modal-dismiss {
				font-size: 24px; 
				color: #FFFFFF;
			}
			.gap-per-row {
				margin-bottom: 5px;
			}
		</style>
	</head>
	<?php $this->getThemeElement("page/html/head",$__forward); ?>
	<body>
		<!-- Page Wrapper -->
		<div id="page-wrapper" class="page-loading">
			<!-- Preloader -->
			<div class="preloader themed-background">
				<h1 class="push-top-bottom text-light text-center" ><strong><?=$this->site_name?></strong><br><small>Loading...</small>
				</h1>
				<div class="inner">
					<h3 class="text-light visible-lt-ie10"><strong>Loading..</strong></h3>
					<div class="preloader-spinner hidden-lt-ie10"></div>
				</div>
			</div>
			<!-- END Preloader -->

			<div id="page-container" >
				<!-- Alternative Sidebar -->
				<!-- END Alternative Sidebar -->

				<!-- Main Sidebar -->
				<!-- END Main Sidebar -->

				<!-- Main Container -->
				<div id="main-container">
					<!-- Header -->

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

		<!-- by Donny Dennison - 3 january 2021 12:07 -->
		<!-- change chat to open chatting -->
		<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


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
		<!-- <script>
			$(document).ready(function(){
			    $('[data-toggle="tooltip"]').tooltip();
			});
		</script> -->
	</body>
</html>
