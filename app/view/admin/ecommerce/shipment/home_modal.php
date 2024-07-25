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
			<div class="modal-header text-center">
				<h2 class="modal-title">Option</h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12 btn-group-vertical " style="text-align: left;">
						<a id="bchange_tracking" href="#" class="btn btn-primary text-left"> <i class="fa"></i> Change Tracking ID</a>
						<a id="bchange_status" href="#" class="btn btn-success text-left"> <i class="fa"></i> Change Shipment Status</a>
						<a id="bdownload_waybill" href="#" class="btn btn-info text-left"> <i class="fa"></i> Download WayBill</a>
					</div>
				</div>
				<div class="row" style="margin-top: 1em; ">
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

<!-- modal change status -->
<div id="modal_change_status" class="modal fade " tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header text-center">
				<h2 class="modal-title">Change Shipment Status</h2>
			</div>
			<!-- END Modal Header -->
			<!-- Modal Body -->
			<form id="form_change_status" method="post" class="form-horizontal">
				<div class="modal-body">
					<div id="form_change_status_alert" class="alert alert-success" style="display: none;">
						Test
					</div>
					<div class="form-group">
						<div class="col-md-3">
							<label for="imcs_change_status" class="control-label">Change Status *</label>
						</div>
						<div class="col-md-9">
							<select id="imcs_change_status" name="change_status" class="form-control">
								<option value="process">Not yet sent</option>
								<option value="delivered">Delivery In Progress</option>
								<option value="succeed">Received</option>
							</select>
						</div>
					</div>

					<div class="row" style="margin-top: 1em; ">
						<div class="col-md-12" style="border-top: 1px #afafaf dashed;">&nbsp;</div>
						<div class="col-md-6">
							<div class="btn-group">
								<button type="button" class="btn btn-default  text-left" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
							</div>
						</div>
						<div class="col-md-6">
							<div class="btn-group pull-right">
								<button type="submit" class="btn btn-success text-left" ><i class="fa fa-save"></i> Save Changes</button>
							</div>
						</div>
					</div>
					<!-- END Modal Body -->
				</div>
			</form>

		</div>
	</div>
</div>
<!-- end modal change status -->


<!-- modal change tracking -->
<div id="modal_change_tracking" class="modal fade " tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header text-center">
				<h2 class="modal-title">Change Shipment Tracking</h2>
			</div>
			<!-- END Modal Header -->
			<!-- Modal Body -->
			<form id="form_change_tracking" method="post" class="form-horizontal">
				<div class="modal-body">
					<div id="form_change_tracking_alert" class="alert alert-success" style="display: none;">
						Test
					</div>
					<div class="form-group">
						<div class="col-md-3">
							<label for="imct_tracking_id" class="control-label">Tracking ID *</label>
						</div>
						<div class="col-md-9">
							<input id="imct_tracking_id" name="shipment_tranid" type="text" class="form-control" value="" />
						</div>
					</div>

					<div class="row" style="margin-top: 1em; ">
						<div class="col-md-12" style="border-top: 1px #afafaf dashed;">&nbsp;</div>
						<div class="col-md-6">
							<div class="btn-group">
								<button type="button" class="btn btn-default text-left" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
							</div>
						</div>
						<div class="col-md-6">
							<div class="btn-group pull-right">
								<button type="submit" class="btn btn-success text-left" ><i class="fa fa-save"></i> Save Changes</button>
							</div>
						</div>
					</div>
					<!-- END Modal Body -->
				</div>
			</form>

		</div>
	</div>
</div>
<!-- end modal change tracking -->