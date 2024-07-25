<div id="modal_add_whitelist" class="modal fade " tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-header-title text-center">
				<h3 class="modal-title"><strong>Add Whitelist</strong></h3>
			</div>

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="form_add_whitelist" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-4">
								<!-- <label for="user_type">Type : </label>
								<input type="text" name="type" id="user_type" class="form-control" style="background-color: #DBD5D1; pointer-events: none;" value="fcm_token" /> -->
								<label for="itype">Type</label>
								<select id="itype" class="form-control">
									<!-- <option value="">-- View All --</option> -->
									<!-- <option value="fcm_token">FCM Token</option> -->
									<option value="ip_address">IP Address</option>
								</select>
							</div>
							<div class="col-md-8">
								<label for="ip_address">IP Address : </label>
								<input type="text" name="ip_address" id="ip_address" class="form-control" autocomplete="off" />
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
						<button id="btn_delete_data" href="#" type="button" class="btn btn-danger text-left"><i class="fa fa-info-circle"></i> Delete</button>
					</div>
				</div>
				<div class="row" style="margin-top: 1em;">
					<div class="col-md-12" style="border-top: 1px #afafaf dashed;">&nbsp;</div>
					<div class="col-xs-12 btn-group-vertical">
						<button type="button" class="btn btn-default btn-block text-left" data-dismiss="modal"><i class="fa fa-times-circle"></i> Close</button>
					</div>
				</div>

			</div><!-- END Modal Body -->

		</div>
	</div>
</div>