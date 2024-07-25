<!-- modal input price -->
<div id="modal_input_price" class="modal fade " tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header text-center">
				<h2 class="modal-title">Input Price</h2>
			</div>
			<!-- END Modal Header -->
			<!-- Modal Body -->
			<form id="form_input_price" method="post" class="form-horizontal">
				<div class="modal-body">
					<div id="form_input_price_alert" class="alert alert-success" style="display: none;">
						Test
					</div>
					<div class="form-group">
						<div class="col-md-3">
							<label for="imip_input_price" class="control-label">Input Price *</label>
						</div>
						<div class="col-md-9">
							<input type="hidden" name="c_bulksale_id" value="<?=$produk->id?>" />
							<input id="imip_input_price" type="text" name="input_price" value="<?=$produk->price?>" class="form-control" />
						</div>
					</div>

					<div class="row" style="margin-top: 1em; ">
						<div class="col-md-12" style="border-top: 1px #afafaf dashed;">&nbsp;</div>
						<div class="col-xs-12 ">
							<div class="btn-group pull-right">
								<button type="submit" class="btn btn-success text-left" ><i class="fa fa-save"></i> Save</button>
								<button type="button" class="btn btn-default  text-left" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
							</div>
						</div>
					</div>
					<!-- END Modal Body -->
				</div>
			</form>

		</div>
	</div>
</div>
<!-- end modal input price -->

<!-- modal change status -->
<div id="modal_change_status" class="modal fade " tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header text-center">
				<h2 class="modal-title">Change Status</h2>
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
							<input type="hidden" name="c_bulksale_id" value="<?=$produk->id?>" />
							<select id="imcs_change_status" name="change_status" class="form-control">
								<option value="pending">Pending</option>
								<option value="visited">Visited</option>
								<option value="completed">Completed</option>
								<option value="rejected">Rejected</option>
							</select>
						</div>
					</div>
					<div id="div_reason" class="form-group" style="display:none;">
						<div class="col-md-3">
							<label for="imcs_reason" class="control-label">Reason</label>
						</div>
						<div class="col-md-9">
							<textarea id="imcs_reason" name="reason" class="form-control"></textarea>
						</div>
					</div>

					<div class="row" style="margin-top: 1em; ">
						<div class="col-md-12" style="border-top: 1px #afafaf dashed;">&nbsp;</div>
						<div class="col-xs-12 ">
							<div class="btn-group pull-right">
								<button type="submit" class="btn btn-success text-left" ><i class="fa fa-save"></i> Save</button>
								<button type="button" class="btn btn-default  text-left" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
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

<!-- modal visit date -->
<div id="modal_visit_date" class="modal fade " tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header text-center">
				<h2 class="modal-title">Visit Date</h2>
			</div>
			<!-- END Modal Header -->
			<!-- Modal Body -->
			<form id="form_visit_date" method="post" class="form-horizontal">
				<div class="modal-body">
					<div id="form_visit_date_alert" class="alert alert-success" style="display: none;">
						Test
					</div>
					<div class="form-group">
						<div class="col-md-3">
							<label for="imvd_vdate" class="control-label">Visit Date *</label>
						</div>
						<div class="col-md-9">
							<input type="hidden" name="c_bulksale_id" value="<?=$produk->id?>" />
							<input id="imvd_vdate" name="visit_date" type="text" value="" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" autocomplete="off" required />
						</div>
					</div>

					<div class="row" style="margin-top: 1em; ">
						<div class="col-md-12" style="border-top: 1px #afafaf dashed;">&nbsp;</div>
						<div class="col-xs-12 ">
							<div class="btn-group pull-right">
								<button type="submit" class="btn btn-success text-left" ><i class="fa fa-save"></i> Save</button>
								<button type="button" class="btn btn-default  text-left" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
							</div>
						</div>
					</div>
					<!-- END Modal Body -->
				</div>
			</form>

		</div>
	</div>
</div>
<!-- end modal visit date -->
