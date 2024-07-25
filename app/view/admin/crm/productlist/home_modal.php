<style>
	.text-left {
		text-align: left !important;
	}
	.mb-8px {
		margin-bottom: 8px !important;
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
						<a id="adetail" href="#" class="btn btn-info text-left mb-8px"><i class="fa fa-info-circle"></i> Detail</a>
						<button id="b_delete_product" type="button" class="btn btn-danger text-left mb-8px"><i class="fa fa-info-circle"></i> Delete Product</button>
						<button id="b_restore_product" type="button" class="btn btn-success text-left mb-8px"><i class="fa fa-times-circle"></i> Restore Product</button>
						<button id="b_change_status_permanent_inactive" type="button" class="btn btn-info text-left mb-8px"><i class="fa fa-check-circle"></i> Permanently Account Stop</button>
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

<!-- permanent inactive -->
<div id="modal_change_status_permanent_inactive" class="modal fade " tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-header-title text-center">
				<h3 class="modal-title"><strong>Permanently Account Stop</strong></h3>
			</div>

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="form_change_status_permanent_inactive" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-4">
								<label for="user_id">User ID : </label>
								<input type="text" name="" id="user_id" class="form-control" style="background-color: #DBD5D1;">
							</div>
							<div class="col-md-8">
								<label for="user_email">Email : </label>
								<input type="text" name="" id="user_email" class="form-control" style="background-color: #DBD5D1;">
							</div>
							<div class="col-md-8">
								<label for="user_name">Name : </label>
								<input type="text" name="" id="user_name" class="form-control" style="background-color: #DBD5D1;">
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-12">
								<label for="status_permanent_inactive">Status : </label>
								<select id="status_permanent_inactive" name="is_permanent_inactive" class="form-control">
									<option value="1">No</option>
									<option value="0">Yes</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-12">
								<label for="iinactive_text">Reason : </label>
								<input type="text" name="inactive_text" id="iinactive_text" class="form-control" autocomplete="off">
							</div>
						</div>
					</fieldset>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
							<button type="submit" class="btn btn-sm btn-primary">Save</button>
						</div>
					</div>
				</form>
			</div>
			<!-- END Modal Body -->
		</div>
	</div>
</div>