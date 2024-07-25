<style>
	.text-left {
		text-align: left !important;
	}
</style>

<!-- modal option -->
<div id="modal_option" class="modal fade " tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header modal-header-title text-center">
				<h2 class="modal-title"><strong>Options</strong></h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12 btn-group-vertical" style="text-align: left;">
						<?php if($user_role == "marketing" || $user_role == "customer_service") { ?>
							<a id="adetail" href="#" class="btn btn-info btn-alt text-left"><i class="fa fa-info-circle"></i> Detail</a>
						<?php } else { ?>
							<a id="adetail" href="#" class="btn btn-info btn-alt text-left"><i class="fa fa-info-circle"></i> Detail</a>
							<a id="aset_publish" href="#" class="btn btn-success btn-alt text-left"><i class="fa fa-check"></i> Set Publish</a>
							<a id="aset_draft" href="#" class="btn btn-danger btn-alt text-left"><i class="fa fa-minus"></i> Set Draft</a>
							<a id="aset_active" href="#" class="btn btn-success btn-alt text-left"><i class="fa fa-play"></i> Set Active</a>
							<a id="aset_inactive" href="#" class="btn btn-danger btn-alt text-left"><i class="fa fa-stop"></i> Set Inactive</a>
						<?php } ?>
					</div>
				</div>
				<div class="row" style="margin-top: 1em;">
					<div class="col-md-12" style="border-top: 1px #afafaf dashed;">&nbsp;</div>
					<div class="col-xs-12 btn-group-vertical">
						<button type="button" class="btn btn-default btn-block text-left" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
					</div>
				</div>
				<!-- END Modal Body -->
			</div>
		</div>
	</div>
</div>
